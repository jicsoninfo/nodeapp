# Laravel Multi-Language Shop — Database & Architecture

## Overview

A clean, extensible product catalog with:
- **Multi-language** categories & products (translation tables, English fallback)
- **Separated price system** (currencies × price lists × products)
- **Easy to add new languages** — drop one row in `languages`, add translations

---

## Database Structure

```
languages               currencies          price_lists
───────────             ──────────          ───────────
id                      id                  id
code (en, ar, fr…)      code (USD, EUR…)    name
name                    name                code
native_name             symbol              is_default
is_rtl                  symbol_position
is_active               decimal_places
is_default              exchange_rate

categories              category_translations
──────────              ─────────────────────
id                      id
slug                    category_id  →  categories.id
parent_id               language_code  →  languages.code
is_active               name
sort_order              description
image                   meta_title
                        meta_description

products                product_translations
────────                ────────────────────
id                      id
sku                     product_id  →  products.id
slug                    language_code  →  languages.code
category_id             name
is_active               description
sort_order              short_description
stock_quantity          meta_title
main_image              meta_description

product_prices          product_images
──────────────          ──────────────
id                      id
product_id              product_id
price_list_id           path
currency_code           alt_text
amount                  sort_order
sale_amount
sale_starts_at
sale_ends_at
```

---

## Key Design Decisions

### 1. Translation tables (not JSON columns)
Each translatable entity has a `_translations` sibling table.
- ✅ Queryable, indexable, foreign-keyed
- ✅ Clean fallback logic in PHP
- ✅ No JSON marshaling

### 2. English fallback via `HasTranslations` trait
```php
$product->trans('name')        // current locale → fallback to 'en'
$product->trans('name', 'ar')  // explicit locale
$product->name                 // magic accessor → same as trans('name')
```

### 3. Price system is fully separated
Prices live in their own table, independent of translations.
```
product_prices: product_id × price_list_id × currency_code → amount
```
This allows:
- Different prices per market (retail vs wholesale)
- Sale prices with date windows
- Adding a new currency: just insert rows into `currencies` + `product_prices`

---

## Adding a New Language

```php
// 1. Add the language
Language::create([
    'code'        => 'es',
    'name'        => 'Spanish',
    'native_name' => 'Español',
]);

// 2. Add translations to existing content
$product->setTranslation('es', [
    'name'        => 'Producto en Español',
    'description' => 'Descripción del producto',
]);

// Missing translations automatically fall back to English.
```

---

## Adding a New Currency

```php
Currency::create([
    'code'            => 'JPY',
    'name'            => 'Japanese Yen',
    'symbol'          => '¥',
    'symbol_position' => 'before',
    'decimal_places'  => 0,
    'exchange_rate'   => 149.5,
]);

// Then add prices for that currency
ProductPrice::create([
    'product_id'    => $product->id,
    'price_list_id' => $retailList->id,
    'currency_code' => 'JPY',
    'amount'        => 149000,
]);
```

---

## Usage Examples

```php
// Get product with translations loaded
$product = Product::withActiveTranslations()
    ->with('prices.currency', 'prices.priceList')
    ->where('slug', 'smart-phone-x1')
    ->first();

// Get translated name (falls back to English)
echo $product->name;                    // uses app()->getLocale()
echo $product->trans('name', 'ar');     // force Arabic

// Get price
echo $product->formattedPrice('USD');            // "$899.00" (on sale)
echo $product->formattedPrice('EUR', 'wholesale'); // "735 €"

// Check sale
$price = $product->price('USD');
if ($price->isOnSale()) {
    echo "Was: " . $price->formattedOriginal();
    echo "Now: " . $price->formatted();
}

// Set a translation
$product->setTranslation('de', [
    'name'        => 'SmartPhone X1',
    'description' => 'Das neueste Flaggschiff-Smartphone.',
]);
```

---

## File Structure

```
app/
  Models/
    Language.php
    Category.php              ← uses HasTranslations
    CategoryTranslation.php
    Product.php               ← uses HasTranslations
    ProductTranslation.php
    PriceModels.php           ← Currency, PriceList, ProductPrice, ProductImage
  Traits/
    HasTranslations.php       ← shared fallback logic
  Http/
    Controllers/
      ProductController.php
    Middleware/
      SetLocale.php

database/
  migrations/
    ..._create_languages_table.php
    ..._create_categories_table.php
    ..._create_products_table.php
    ..._create_price_system_tables.php
  seeders/
    ShopSeeder.php

routes/
  web.php                     ← /{locale}/products
```

---

## Setup

```bash
php artisan migrate
php artisan db:seed --class=ShopSeeder

# Visit
# /en/products
# /ar/products
# /fr/products?currency=EUR&price_list=wholesale
```
