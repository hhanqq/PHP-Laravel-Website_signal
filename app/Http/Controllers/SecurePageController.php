<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

class SecurePageController extends Controller
{
    private $baseCookies = [
        'uuid' => '2e9785d0-f5f6-46db-bccd-adc94fec918f',
        'loggedIn' => '1',
        'no-login-captcha' => '1',
        'hide_earth2025_promo' => '1',
        'chat_opened' => '1',
        'lang' => 'ru',
        'brief-period' => 'currentWeek',
        'laravel_session' => 'eyJpdiI6IjhYYUdvanFkLzVkaG16RzNsb2hvbEE9PSIsInZhbHVlIjoiQ1VYdGQrb1JMekVEZjJYM3owMUVEelhoWEpVcmdDZXd0ZkV5bENwUm5NWWR2ekEvOFpDSE56ZklTcUU2RUZoQ3RaeURUbGpoSmlYMUFqa0lQWXM5RFY4S0wyRUNSWkV3UDluREord3NYT3hLSHBVZlUvbFh6by9HQ2RudVJtMGIiLCJtYWMiOiIyMmFhMGRiYjViNGMyZjAwYTI4YzU4ZDVjYjQ1MTFlZTJiNTEzYmUxMzliZTFhMzkyZjcxZDRhZTI1MTZlMTMyIiwidGFnIjoiIn0%3D'
    ];

    public function checkAccess(Request $request)
    {
        $user = auth()->user();

        if (!$user || !$user->pocket_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Authentication required or missing pocket_id',
                'access' => false
            ], 401);
        }

        try {
            // Получаем обновленные куки
            $refreshedCookies = $this->refreshLaravelSession($this->baseCookies);
            $allCookies = array_merge($this->baseCookies, $refreshedCookies);

            $baseUrl = 'https://affiliate.pocketoption.com/ru/statistics/detailed';
            $params = http_build_query([
                'dateFrom' => '2023-09-01',
                'dateTo' => '2026-03-02'
            ]);

            $matchedData = null;

            // Перебираем страницы от 1 до 10
            for ($page = 1; $page <= 10; $page++) {
                $url = "$baseUrl?$params&page=$page";

                Log::debug("Fetching page", ['page' => $page, 'url' => $url]);

                $response = Http::withOptions(['verify' => false, 'timeout' => 15])
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0',
                        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp;q=0.8',
                        'Referer' => 'https://affiliate.pocketoption.com/ru/statistics'
                    ])
                    ->withCookies($allCookies, 'affiliate.pocketoption.com')
                    ->get($url);

                if (!$response->successful()) {
                    Log::warning("Failed to fetch page", ['page' => $page, 'status' => $response->status()]);
                    continue;
                }

                $htmlContent = $response->body();
                $crawler = new Crawler($htmlContent);

                // Ищем все строки в таблице
                $dataRows = $crawler->filter('table.table tbody tr');

                if ($dataRows->count() === 0) {
                    Log::info("No rows found on page", ['page' => $page]);
                    continue;
                }

                // Парсим каждую строку
                $dataRows->each(function (Crawler $tr) use ($user, &$matchedData) {
                    $cells = $tr->filter('td');

                    // Ищем UID — может быть в любом теге с data-label="UID"
                    $uidNode = $cells->filter('[data-label="UID"]')->first();
                    if (!$uidNode->count()) {
                        foreach ($cells as $cell) {
                            if (is_numeric(trim($cell->textContent))) {
                                $uidNode = new Crawler($cell);
                                break;
                            }
                        }
                    }

                    if (!$uidNode->count()) return;

                    $uid = trim($uidNode->text());

                    // Берем сумму депозитов из последней ячейки DPST
                    $depoNode = $cells->filter('[data-label="DPST"]')->last();
                    $sumDepo = $depoNode->count()
                        ? floatval(preg_replace('/[^0-9.]/', '', $depoNode->text()))
                        : 0;

                    // Логируем найденные данные
                    Log::debug("Found user row", ['uid' => $uid, 'sum_depo' => $sumDepo]);

                    if ((string)$uid === (string)$user->pocket_id) {
                        $matchedData = [
                            'uid' => $uid,
                            'sum_depo' => $sumDepo,
                            'page' => $tr->getNode(0)->parentNode->parentNode->getAttribute('data-page') ?? $page
                        ];
                    }
                });

                // Если нашли совпадение — выходим
                if ($matchedData !== null) {
                    break;
                }
            }

            if ($matchedData !== null) {
                return response()->json([
                    'status' => 'success',
                    'access' => $matchedData['sum_depo'] > 10,
                    'sum_depo' => $matchedData['sum_depo'],
                    'page' => $matchedData['page'],
                    'session_refreshed' => !empty($refreshedCookies)
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Client data not found in HTML tables across all pages',
                'access' => false,
                'reason' => 'client_not_found'
            ], 404);

        } catch (\Exception $e) {
            Log::error('PocketOption HTML parsing error: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => $user->id ?? null,
                'pocket_id' => $user->pocket_id ?? null
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Internal server error: ' . $e->getMessage(),
                'access' => false,
                'reason' => 'server_error'
            ], 500);
        }
    }

    protected function refreshLaravelSession(array $currentCookies): array
    {
        try {
            $response = Http::withOptions(['verify' => false, 'timeout' => 10])
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0',
                    'Referer' => 'https://affiliate.pocketoption.com/ru/statistics'
                ])
                ->withCookies($currentCookies, 'affiliate.pocketoption.com')
                ->get('https://affiliate.pocketoption.com/ru/statistics');

            $newCookies = [];
            foreach ($response->cookies() as $cookie) {
                $newCookies[$cookie->getName()] = $cookie->getValue();
            }

            Log::debug('Session refreshed', [
                'old_cookies' => $currentCookies,
                'new_cookies' => $newCookies
            ]);

            return $newCookies;

        } catch (\Exception $e) {
            Log::error('Session refresh failed: ' . $e->getMessage());
            return [];
        }
    }
}
