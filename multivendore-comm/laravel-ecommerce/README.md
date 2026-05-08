# Marketplace SaaS — Laravel 11

A production-ready, multi-vendor SaaS e-commerce platform built with Laravel 11.
Supports multiple languages, currencies, vendors, and payment providers.

---

## Stack

| Layer         | Technology                              |
|---------------|-----------------------------------------|
| Framework     | Laravel 11                              |
| Auth          | Laravel Sanctum (token-based)           |
| Roles & ACL   | Spatie Permission                       |
| Queue         | Redis + Laravel Horizon                 |
| Search        | Algolia (Laravel Scout)                 |
| Payments      | Stripe + Razorpay                       |
| Storage       | AWS S3                                  |
| Cache         | Redis                                   |
| Push Alerts   | Firebase Cloud Messaging (FCM)          |
| Activity Log  | Spatie Activity Log                     |
| Media         | Spatie Medialibrary + Intervention Image|
| Email         | Laravel Mail (Mailpit local / SES prod) |

---

## Quick Start

```bash
# 1. Clone & install
git clone <repo>
cd laravel-ecommerce
composer install

# 2. Environment
cp .env.example .env
php artisan key:generate

# 3. Database
php artisan migrate --seed

# 4. Create admin
php artisan admin:create

# 5. Start Horizon (queues)
php artisan horizon

# 6. Serve
php artisan serve
```

---

## Project Structure

```
app/
├── Console/Commands/        # Artisan commands (payouts, rates, cleanup)
├── Contracts/               # Repository & Service interfaces
├── Enums/                   # All typed enums (OrderStatus, UserStatus, …)
├── Events/                  # Domain events
├── Exceptions/              # Custom exceptions + global handler
├── Http/
│   ├── Controllers/Api/V1/  # Admin | Buyer | Vendor | Public | Auth | Webhook
│   ├── Middleware/          # ForceJson, SetLocale, EnsureVendorIsActive
│   ├── Requests/            # Form requests with validation
│   └── Resources/V1/        # API Resources (UserResource, OrderResource, …)
├── Jobs/                    # Queued jobs (RecordProductView, ProcessVendorPayout, …)
├── Listeners/               # Event listeners (email, push, inventory, analytics)
├── Models/                  # All Eloquent models
├── Notifications/           # Email + push notification classes
├── Observers/               # Model observers (Product, Order, Review, Vendor)
├── Policies/                # Authorization policies (Product, Order, Vendor, …)
├── Providers/               # App, Auth, Event, Repository service providers
├── Repositories/            # Concrete repository implementations
├── Services/                # Business logic services
└── Support/Traits/          # HasUuid and other shared traits

database/
├── factories/               # 20+ model factories
├── migrations/              # 43 migrations (all tables)
└── seeders/                 # Full seed data for all tables

resources/views/emails/      # Blade email templates
routes/
├── api.php                  # All versioned API routes (Public | Auth | Buyer | Vendor | Admin)
├── web.php                  # Health check + Horizon
└── console.php              # Scheduled commands
tests/
├── Feature/                 # Auth, Order, Product, Vendor feature tests
└── Unit/                    # Services and Repository unit tests
```

---

## API Overview

| Group    | Prefix          | Auth          |
|----------|-----------------|---------------|
| Public   | `/api/v1/public`| None          |
| Auth     | `/api/v1/auth`  | None          |
| Buyer    | `/api/v1/buyer` | Sanctum token |
| Vendor   | `/api/v1/vendor`| Sanctum + role|
| Admin    | `/api/v1/admin` | Sanctum + role|
| Webhooks | `/api/v1/webhooks` | Signed    |

---

## Queue Workers

```bash
# Start all queues
php artisan horizon

# Queue names: default | notifications | payouts | analytics | search
```

## Scheduled Jobs

| Command                     | Schedule          |
|-----------------------------|-------------------|
| `rates:sync`                | Hourly            |
| `carts:clean`               | Daily 03:00       |
| `payouts:process-monthly`   | 1st of month 09:00|
| `telescope:prune`           | Daily (local)     |
| `activitylog:clean`         | Weekly            |

---

## Testing

```bash
php artisan test
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature
php artisan test --filter ProductTest
```

---

## Key Commands

```bash
php artisan admin:create                    # Interactive admin creator
php artisan payouts:process-monthly --dry-run  # Preview payouts
php artisan rates:sync                     # Fetch exchange rates
php artisan products:reindex               # Re-sync Algolia index
php artisan carts:clean                    # Remove expired carts
```

---

## Multi-Language

All user-facing content has translation tables:
`product_translations`, `category_translations`, `vendor_translations`, `attribute_translations`

Set language via `Accept-Language` HTTP header. Supported: `en hi ar de fr zh ja es pt ru`

## Multi-Currency

Set currency via query param or user preference.
Exchange rates auto-sync hourly from external API.
Supported: `USD EUR INR GBP JPY AED CNY SAR BRL CAD`
