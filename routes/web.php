<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Foundation\Http\FormRequest;

// Верификация email
Route::get('/email/verify', function () {
    return response()->json([
        'message' => 'Please verify your email address',
        'verified' => auth()->check() ? auth()->user()->hasVerifiedEmail() : false,
        'resend_url' => route('verification.send')
    ]);
})->name('verification.notice');

// Проверка email через ссылку (без auth:sanctum)
Route::get('/email/verify/{id}/{hash}', function (FormRequest $request, $id, $hash) {
    // Получаем пользователя по ID
    $user = \App\Models\User::find($id);

    if (!$user) {
        return redirect()->to('https://aiboostusa.com/email-verified?status=unauthorized');
    }

    // Проверяем хэш
    if (!hash_equals((string)$hash, sha1($user->getEmailForVerification()))) {
        return response()->json([
            'success' => false,
            'error' => 'Invalid verification link.',
        ], 403);
    }

    if ($user->hasVerifiedEmail()) {
        return redirect()->to('https://aiboostusa.com/activated-email');
    }

    // Подтверждаем email
    $user->markEmailAsVerified();

    return redirect()->to('https://aiboostusa.com/activated-email');

})->name('verification.verify');


// Отправка повторного письма (только для авторизованных)
Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return response()->json([
        'success' => true,
        'message' => 'Verification link sent!',
    ]);
})->middleware(['auth:sanctum', 'throttle:3,1'])->name('verification.send');
