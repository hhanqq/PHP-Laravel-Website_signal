

- **Маршруты (routes)** — с использованием Sanctum для аутентификации API.
- **Контроллеры (controllers)** — обрабатывают логику запросов, включая регистрацию, вход, верификацию email, проверку депозита клиента и т.д.
- **Middleware** — такие как `EnsureEmailIsVerified`, `EnsureClientDeposit`, `TrimStrings`, и другие.
- **Конфигурационные файлы**: `composer.json`, `package.json`, `phpunit.xml`, `config/ignition.php` и др.
- **Используемые библиотеки и зависимости**:
  - Laravel
  - Laravel Sanctum
  - Spatie Laravel Ignition
  - Faker
  - Ramsey UUID
  - Guzzle
  - Symfony Console / Mailer / HttpKernel
  - OpenAI PHP клиент
  - PestPHP тесты
  - Vite + Axios для фронтенда



# 🧠 [1]

> Бэкенд часть проекта реализована с использованием **Laravel**, с поддержкой современных практик разработки, REST API, JWT/Sanctum аутентификации, автоматической генерации тестовых данных и интеграции с внешними API.

---

## 📦 Технологии

| Категория       | Использованные технологии |
|------------------|----------------------------|
| Backend Framework | Laravel 10.x               |
| Аутентификация    | Laravel Sanctum            |
| ORM              | Eloquent ORM               |
| Маршрутизация     | Laravel Router             |
| Логирование      | Monolog / Laravel Log      |
| Генерация данных | Faker                      |
| Уникальные ID     | Ramsey UUID                |
| HTTP-клиент       | Guzzle                     |
| Отправка почты   | Symfony Mailer             |
| Фронтенд         | Vite + Axios               |
| Тестирование     | PestPHP, PHPUnit           |

---

## 🛠️ Основные функции

### 🔐 Аутентификация и регистрация
- Регистрация пользователей
- Вход/выход
- Верификация email
- Восстановление пароля

### 🔍 Проверка депозита
- Парсинг данных с внешнего ресурса через `Symfony DomCrawler`
- Проверка минимальной суммы депозита перед доступом к защищённым маршрутам

### 🔄 Middleware
- `EnsureEmailIsVerified` — блокирует доступ, если email не подтверждён
- `EnsureClientDeposit` — проверяет сумму депозита
- `TrimStrings` / `ConvertEmptyStringsToNull` — нормализация данных
- CORS middleware — управление политикой доступа

### 📡 API Endpoints
```php
// Гостевые маршруты
POST /register
POST /login
POST /forgot-password
POST /reset-password

// Защищённые маршруты
POST /logout
GET /secure-page (middleware: ensure.client.deposit)
```

---

## 🧪 Тестирование

- Написаны тесты с использованием **PestPHP**
- Поддержка **PHPUnit**
- Интеграционные тесты для всех ключевых модулей
- Mock-объекты, фабрики и фикстуры

---

## 🧰 Конфигурация

- `.env` — управление окружением через `vlucas/phpdotenv`
- `config/ignition.php` — настройки отладчика Laravel Ignition
- `phpunit.xml` — конфигурация тестирования
- `composer.json` — автозагрузка, зависимости и автолоадер PSR-4

---

## 📁 Структура проекта (основные директории)

```
app/
├── Http/
│   ├── Controllers/        — контроллеры
│   ├── Middleware/           — кастомные middleware
├── Providers/                — сервис-провайдеры
bootstrap/                    — загрузка приложения
config/                       — конфигурационные файлы
database/                     — миграции и фабрики
resources/
routes/                       — API и Web маршруты
tests/                        — тесты (Pest / PHPUnit)
vendor/                       — зависимости
```

---

## 📦 Зависимости (частично)

```json
"require": {
  "php": "^8.2",
  "laravel/framework": "^10.36",
  "laravel/sanctum": "^3.3",
  "spatie/laravel-ignition": "^2.1",
  "fzaninotto/faker": "^1.24",
  "ramsey/uuid": "^4.7",
  "guzzlehttp/guzzle": "^7.8"
}
```

---

## 🚀 Как запустить локально

1. Склонируйте репозиторий:
   ```bash
   git clone https://github.com/yourname/yourproject.git
   ```

2. Установите зависимости:
   ```bash
   composer install
   npm install
   ```

3. Настройте `.env`:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Запустите миграции:
   ```bash
   php artisan migrate --seed
   ```

5. Запустите сервер:
   ```bash
   php artisan serve
   npm run dev
   ```

---
