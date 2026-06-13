# 🚀 DATABASE_V2_FIELDS.md

# KELLOGGS APP DATABASE FIELD SPECIFICATION V2

Last Update: 09 Juni 2026

Version: 2.1 Enterprise SKU Architecture

Status: Final

---

# OVERVIEW

Dokumen ini mendefinisikan:

* Nama tabel
* Nama kolom
* Tipe data
* Nullable
* Default Value
* Unique Constraint
* Index Strategy
* Foreign Key Reference

Dokumen ini menjadi referensi utama untuk:

* Migration
* Model
* Factory
* Seeder
* API Resource
* Validation Rules
* Service Layer
* Repository Layer

---

# STANDARD CONVENTIONS

## Primary Key

Semua tabel menggunakan:

```php
$table->id();
```

Tipe:

```text
BIGINT UNSIGNED
```

---

## Timestamp

Seluruh tabel bisnis menggunakan:

```php
$table->timestamps();
```

---

## Soft Delete

Seluruh tabel bisnis menggunakan:

```php
$table->softDeletes();
```

Kecuali:

* pivot tables
* logs
* cache
* jobs
* tracking tables

---

## Monetary Value

Seluruh nilai uang menggunakan:

```php
decimal(15,2)
```

Tujuan:

* aman untuk transaksi besar
* future proof

---

## Foreign Key Naming

Format:

```text
user_id
product_id
product_sku_id
category_id
order_id
payment_id
shipment_id
```

---

# CORE SYSTEM

# USERS

| Field             | Type         | Nullable | Notes        |
| ----------------- | ------------ | -------- | ------------ |
| id                | bigint       | No       | PK           |
| name              | string(255)  | No       |              |
| email             | string(255)  | No       | Unique       |
| avatar            | string(2048) | Yes      |              |
| is_active         | boolean      | No       | Default true |
| email_verified_at | timestamp    | Yes      |              |
| last_login_at     | timestamp    | Yes      |              |
| password          | string(255)  | No       |              |
| remember_token    | string(100)  | Yes      |              |
| created_at        | timestamp    | No       |              |
| updated_at        | timestamp    | No       |              |

---

# CUSTOMER MODULE

# CUSTOMER_PROFILES

| Field      | Type         | Nullable | Notes        |
| ---------- | ------------ | -------- | ------------ |
| id         | bigint       | No       | PK           |
| user_id    | bigint       | No       | FK users     |
| first_name | string(100)  | No       |              |
| last_name  | string(100)  | Yes      |              |
| phone      | string(30)   | No       | Unique       |
| gender     | enum         | Yes      | male,female  |
| birth_date | date         | Yes      |              |
| avatar     | string(2048) | Yes      |              |
| is_active  | boolean      | No       | Default true |
| created_at | timestamp    | No       |              |
| updated_at | timestamp    | No       |              |
| deleted_at | timestamp    | Yes      |              |

---

# CUSTOMER_ADDRESSES

| Field               | Type                   |
| ------------------- | ---------------------- |
| id                  | bigint                 |
| customer_profile_id | bigint                 |
| label               | string(100)            |
| recipient_name      | string(255)            |
| phone               | string(30)             |
| province            | string(100)            |
| city                | string(100)            |
| district            | string(100)            |
| postal_code         | string(20)             |
| address             | text                   |
| notes               | text nullable          |
| latitude            | decimal(10,7) nullable |
| longitude           | decimal(10,7) nullable |
| is_default          | boolean                |
| created_at          | timestamp              |
| updated_at          | timestamp              |
| deleted_at          | timestamp              |

---

# CUSTOMER_DEVICES

| Field               | Type               |
| ------------------- | ------------------ |
| id                  | bigint             |
| customer_profile_id | bigint             |
| device_type         | string(50)         |
| device_token        | string(500)        |
| last_active_at      | timestamp nullable |
| created_at          | timestamp          |
| updated_at          | timestamp          |

---

# CUSTOMER_NOTIFICATIONS

| Field               | Type               |
| ------------------- | ------------------ |
| id                  | bigint             |
| customer_profile_id | bigint             |
| title               | string(255)        |
| message             | text               |
| type                | string(100)        |
| is_read             | boolean            |
| read_at             | timestamp nullable |
| created_at          | timestamp          |
| updated_at          | timestamp          |

---

# WISHLISTS

| Field               | Type      |
| ------------------- | --------- |
| id                  | bigint    |
| customer_profile_id | bigint    |
| product_id          | bigint    |
| created_at          | timestamp |

---

# PRODUCT MODULE

# CATEGORIES

| Field       | Type                  |
| ----------- | --------------------- |
| id          | bigint                |
| parent_id   | bigint nullable       |
| name        | string(255)           |
| slug        | string(255) unique    |
| description | text nullable         |
| image       | string(2048) nullable |
| sort_order  | integer default 0     |
| is_active   | boolean default true  |
| created_at  | timestamp             |
| updated_at  | timestamp             |
| deleted_at  | timestamp             |

---

# PRODUCTS

Produk induk.

Tidak menyimpan:

* harga
* stok
* barcode

Karena semuanya berada di Product SKU.

| Field             | Type                  |
| ----------------- | --------------------- |
| id                | bigint                |
| category_id       | bigint                |
| name              | string(255)           |
| slug              | string(255) unique    |
| short_description | text nullable         |
| description       | longText nullable     |
| thumbnail         | string(2048) nullable |
| status            | enum                  |
| is_featured       | boolean default false |
| is_active         | boolean default true  |
| published_at      | timestamp nullable    |
| created_at        | timestamp             |
| updated_at        | timestamp             |
| deleted_at        | timestamp             |

Status:

```text
draft
published
archived
```

---

# PRODUCT_IMAGES

| Field      | Type                 |
| ---------- | -------------------- |
| id         | bigint               |
| product_id | bigint               |
| image_url  | string(2048)         |
| alt_text   | string(255) nullable |
| sort_order | integer              |
| created_at | timestamp            |
| updated_at | timestamp            |

---

# PRODUCT_OPTIONS

Contoh:

```text
Size
Flavor
Package
```

| Field      | Type        |
| ---------- | ----------- |
| id         | bigint      |
| product_id | bigint      |
| name       | string(100) |
| sort_order | integer     |
| created_at | timestamp   |
| updated_at | timestamp   |

---

# PRODUCT_OPTION_VALUES

Contoh:

```text
250g
500g

Original
Chocolate
```

| Field             | Type        |
| ----------------- | ----------- |
| id                | bigint      |
| product_option_id | bigint      |
| value             | string(100) |
| sort_order        | integer     |
| created_at        | timestamp   |
| updated_at        | timestamp   |

---

# PRODUCT_SKUS

Tabel utama transaksi.

Semua:

* cart
* checkout
* inventory
* order
* promotion

menggunakan product_sku_id.

| Field      | Type                        |
| ---------- | --------------------------- |
| id         | bigint                      |
| product_id | bigint                      |
| sku        | string(100) unique          |
| barcode    | string(100) unique nullable |
| price      | decimal(15,2)               |
| cost_price | decimal(15,2) nullable      |
| weight     | integer                     |
| is_default | boolean                     |
| is_active  | boolean                     |
| created_at | timestamp                   |
| updated_at | timestamp                   |
| deleted_at | timestamp                   |

---

# PRODUCT_SKU_VALUES

Pivot SKU dan Option Value.

| Field                   | Type   |
| ----------------------- | ------ |
| id                      | bigint |
| product_sku_id          | bigint |
| product_option_value_id | bigint |

---
# PRODUCT REVIEW MODULE

# PRODUCT_REVIEWS

| Field                | Type                  |
| -------------------- | --------------------- |
| id                   | bigint                |
| product_id           | bigint                |
| customer_profile_id  | bigint                |
| rating               | tinyInteger           |
| title                | string(255) nullable  |
| review               | text                  |
| is_verified_purchase | boolean default false |
| created_at           | timestamp             |
| updated_at           | timestamp             |
| deleted_at           | timestamp             |

Rating:

```text
1 - 5
```

---

# PRODUCT_REVIEW_IMAGES

| Field             | Type         |
| ----------------- | ------------ |
| id                | bigint       |
| product_review_id | bigint       |
| image_url         | string(2048) |
| created_at        | timestamp    |
| updated_at        | timestamp    |

---

# CART MODULE

# CARTS

| Field               | Type      |
| ------------------- | --------- |
| id                  | bigint    |
| customer_profile_id | bigint    |
| created_at          | timestamp |
| updated_at          | timestamp |

---

# CART_ITEMS

Seluruh cart menggunakan:

```text
product_sku_id
```

bukan:

```text
product_id
```

| Field          | Type          |
| -------------- | ------------- |
| id             | bigint        |
| cart_id        | bigint        |
| product_sku_id | bigint        |
| quantity       | integer       |
| unit_price     | decimal(15,2) |
| subtotal       | decimal(15,2) |
| created_at     | timestamp     |
| updated_at     | timestamp     |

---

# CHECKOUT MODULE

# CHECKOUT_SESSIONS

| Field               | Type          |
| ------------------- | ------------- |
| id                  | bigint        |
| customer_profile_id | bigint        |
| status              | enum          |
| subtotal            | decimal(15,2) |
| discount_amount     | decimal(15,2) |
| shipping_cost       | decimal(15,2) |
| grand_total         | decimal(15,2) |
| expired_at          | timestamp     |
| created_at          | timestamp     |
| updated_at          | timestamp     |

Status:

```text
active
expired
completed
cancelled
```

---

# CHECKOUT_ITEMS

| Field               | Type          |
| ------------------- | ------------- |
| id                  | bigint        |
| checkout_session_id | bigint        |
| product_sku_id      | bigint        |
| quantity            | integer       |
| unit_price          | decimal(15,2) |
| subtotal            | decimal(15,2) |
| created_at          | timestamp     |
| updated_at          | timestamp     |

---

# ORDER MODULE

# ORDERS

| Field               | Type              |
| ------------------- | ----------------- |
| id                  | bigint            |
| order_number        | string(50) unique |
| customer_profile_id | bigint            |
| shipping_address_id | bigint            |
| subtotal            | decimal(15,2)     |
| discount_amount     | decimal(15,2)     |
| shipping_cost       | decimal(15,2)     |
| grand_total         | decimal(15,2)     |
| payment_status      | enum              |
| status              | enum              |
| notes               | text nullable     |
| ordered_at          | timestamp         |
| created_at          | timestamp         |
| updated_at          | timestamp         |
| deleted_at          | timestamp         |

Order Status:

```text
pending
paid
processing
packed
shipped
completed
cancelled
refunded
```

Payment Status:

```text
unpaid
paid
partial_refund
refunded
```

---

# ORDER_ITEMS

Snapshot wajib disimpan.

Tujuan:

Riwayat transaksi tidak berubah walaupun produk diubah.

| Field          | Type          |
| -------------- | ------------- |
| id             | bigint        |
| order_id       | bigint        |
| product_sku_id | bigint        |
| product_name   | string(255)   |
| sku            | string(100)   |
| quantity       | integer       |
| unit_price     | decimal(15,2) |
| subtotal       | decimal(15,2) |
| created_at     | timestamp     |
| updated_at     | timestamp     |

---

# ORDER_HISTORIES

| Field      | Type            |
| ---------- | --------------- |
| id         | bigint          |
| order_id   | bigint          |
| status     | string(100)     |
| notes      | text nullable   |
| created_by | bigint nullable |
| created_at | timestamp       |

---

# ORDER_STATUS_LOGS

| Field      | Type                 |
| ---------- | -------------------- |
| id         | bigint               |
| order_id   | bigint               |
| old_status | string(100) nullable |
| new_status | string(100)          |
| notes      | text nullable        |
| created_by | bigint nullable      |
| created_at | timestamp            |

---

# PAYMENT MODULE

# PAYMENTS

Midtrans Ready Architecture

| Field                 | Type                 |
| --------------------- | -------------------- |
| id                    | bigint               |
| order_id              | bigint               |
| method                | string(100)          |
| gateway               | string(100)          |
| amount                | decimal(15,2)        |
| status                | enum                 |
| transaction_reference | string(255) nullable |
| paid_at               | timestamp nullable   |
| created_at            | timestamp            |
| updated_at            | timestamp            |

Status:

```text
pending
paid
failed
expired
cancelled
refunded
```

---

# PAYMENT_TRANSACTIONS

Menyimpan transaksi gateway.

| Field            | Type               |
| ---------------- | ------------------ |
| id               | bigint             |
| payment_id       | bigint             |
| transaction_id   | string(255) unique |
| transaction_type | string(100)        |
| amount           | decimal(15,2)      |
| response_payload | json nullable      |
| created_at       | timestamp          |
| updated_at       | timestamp          |

---

# PAYMENT_CALLBACKS

Menyimpan seluruh callback Midtrans.

Tidak boleh dihapus.

| Field            | Type      |
| ---------------- | --------- |
| id               | bigint    |
| payment_id       | bigint    |
| callback_payload | json      |
| received_at      | timestamp |
| created_at       | timestamp |

---
# SHIPPING MODULE

# COURIERS

| Field      | Type              |
| ---------- | ----------------- |
| id         | bigint            |
| code       | string(50) unique |
| name       | string(100)       |
| is_active  | boolean           |
| created_at | timestamp         |
| updated_at | timestamp         |

Contoh:

```text
jne
jnt
sicepat
anteraja
```

---

# SHIPPING_METHODS

| Field          | Type                |
| -------------- | ------------------- |
| id             | bigint              |
| courier_id     | bigint              |
| service_code   | string(50)          |
| service_name   | string(100)         |
| estimated_days | string(50) nullable |
| is_active      | boolean             |
| created_at     | timestamp           |
| updated_at     | timestamp           |

---

# SHIPMENTS

| Field              | Type                        |
| ------------------ | --------------------------- |
| id                 | bigint                      |
| order_id           | bigint                      |
| shipping_method_id | bigint                      |
| courier_name       | string(100)                 |
| service_name       | string(100)                 |
| tracking_number    | string(100) unique nullable |
| status             | enum                        |
| shipped_at         | timestamp nullable          |
| delivered_at       | timestamp nullable          |
| created_at         | timestamp                   |
| updated_at         | timestamp                   |

Status:

```text
pending
shipped
in_transit
delivered
returned
cancelled
```

---

# SHIPMENT_TRACKINGS

| Field       | Type        |
| ----------- | ----------- |
| id          | bigint      |
| shipment_id | bigint      |
| status      | string(100) |
| description | text        |
| tracked_at  | timestamp   |
| created_at  | timestamp   |

---

# VOUCHER MODULE

# VOUCHERS

| Field              | Type                   |
| ------------------ | ---------------------- |
| id                 | bigint                 |
| code               | string(100) unique     |
| name               | string(255)            |
| description        | text nullable          |
| discount_type      | enum                   |
| discount_value     | decimal(15,2)          |
| minimum_order      | decimal(15,2)          |
| maximum_discount   | decimal(15,2) nullable |
| usage_limit        | integer                |
| usage_per_customer | integer                |
| start_date         | datetime               |
| end_date           | datetime               |
| is_active          | boolean                |
| created_at         | timestamp              |
| updated_at         | timestamp              |
| deleted_at         | timestamp              |

Discount Type:

```text
percentage
fixed
```

---

# VOUCHER_USAGES

| Field               | Type          |
| ------------------- | ------------- |
| id                  | bigint        |
| voucher_id          | bigint        |
| customer_profile_id | bigint        |
| order_id            | bigint        |
| discount_amount     | decimal(15,2) |
| used_at             | timestamp     |
| created_at          | timestamp     |

---

# PROMOTION MODULE

# PROMOTIONS

| Field          | Type              |
| -------------- | ----------------- |
| id             | bigint            |
| name           | string(255)       |
| description    | text nullable     |
| promotion_type | enum              |
| value          | decimal(15,2)     |
| priority       | integer default 0 |
| start_date     | datetime          |
| end_date       | datetime          |
| is_active      | boolean           |
| created_at     | timestamp         |
| updated_at     | timestamp         |
| deleted_at     | timestamp         |

Promotion Type:

```text
percentage
fixed
flash_sale
buy_x_get_y
```

---

# PROMO_PRODUCTS

| Field        | Type   |
| ------------ | ------ |
| id           | bigint |
| promotion_id | bigint |
| product_id   | bigint |

---

# PROMO_CATEGORIES

| Field        | Type   |
| ------------ | ------ |
| id           | bigint |
| promotion_id | bigint |
| category_id  | bigint |

---

# PROMO_SKUS

Enterprise SKU Promotion

| Field          | Type   |
| -------------- | ------ |
| id             | bigint |
| promotion_id   | bigint |
| product_sku_id | bigint |

---

# INVENTORY MODULE

# INVENTORIES

Seluruh inventory berbasis SKU.

Bukan Product.

| Field           | Type      |
| --------------- | --------- |
| id              | bigint    |
| product_sku_id  | bigint    |
| current_stock   | integer   |
| reserved_stock  | integer   |
| available_stock | integer   |
| created_at      | timestamp |
| updated_at      | timestamp |

---

# STOCK_MOVEMENTS

Audit stok utama.

| Field          | Type                 |
| -------------- | -------------------- |
| id             | bigint               |
| product_sku_id | bigint               |
| type           | enum                 |
| quantity       | integer              |
| reference_type | string(100) nullable |
| reference_id   | bigint nullable      |
| notes          | text nullable        |
| created_at     | timestamp            |

Movement Type:

```text
stock_in
stock_out
adjustment
return
```

---

# STOCK_ADJUSTMENTS

| Field          | Type            |
| -------------- | --------------- |
| id             | bigint          |
| product_sku_id | bigint          |
| old_stock      | integer         |
| new_stock      | integer         |
| reason         | text            |
| created_by     | bigint nullable |
| created_at     | timestamp       |

---

# STOCK_OPNAMES

| Field          | Type          |
| -------------- | ------------- |
| id             | bigint        |
| product_sku_id | bigint        |
| system_stock   | integer       |
| physical_stock | integer       |
| difference     | integer       |
| notes          | text nullable |
| opname_date    | date          |
| created_at     | timestamp     |

---

# LOYALTY MODULE

# LOYALTY_POINTS

| Field               | Type      |
| ------------------- | --------- |
| id                  | bigint    |
| customer_profile_id | bigint    |
| total_points        | integer   |
| created_at          | timestamp |
| updated_at          | timestamp |

---

# POINT_TRANSACTIONS

| Field               | Type        |
| ------------------- | ----------- |
| id                  | bigint      |
| customer_profile_id | bigint      |
| points              | integer     |
| type                | enum        |
| description         | string(255) |
| created_at          | timestamp   |

Type:

```text
earn
redeem
expire
adjustment
```

---

# FIELD NAMING RULES

## Foreign Key

```text
user_id
customer_profile_id
category_id
product_id
product_sku_id
order_id
payment_id
shipment_id
promotion_id
voucher_id
```

---

## Boolean

```text
is_active
is_default
is_featured
is_read
is_verified_purchase
```

---

## Timestamp

```text
created_at
updated_at
deleted_at

published_at

ordered_at

paid_at

shipped_at

delivered_at

tracked_at

used_at
```

---

# ENUM STANDARDS

## Product Status

```text
draft
published
archived
```

---

## Order Status

```text
pending
paid
processing
packed
shipped
completed
cancelled
refunded
```

---

## Payment Status

```text
pending
paid
failed
expired
cancelled
refunded
```

---

## Shipment Status

```text
pending
shipped
in_transit
delivered
returned
cancelled
```

---

## Promotion Type

```text
percentage
fixed
flash_sale
buy_x_get_y
```

---

## Stock Movement Type

```text
stock_in
stock_out
adjustment
return
```

---

# FINAL STATUS

Field Specification V2

✅ Completed

Enterprise SKU Architecture

✅ Completed

Inventory Architecture

✅ Completed

Promotion Architecture

✅ Completed

Order Architecture

✅ Completed

Payment Architecture

✅ Completed

Shipping Architecture

✅ Completed

Ready For:

* DATABASE_V2_MIGRATION_PLAN.md
* Laravel Migration Generation
* Model Generation
* Factory Generation
* Seeder Generation
* API Development
* React Admin Dashboard
* Flutter Mobile App

Target:

✅ Enterprise Grade

✅ Portfolio Grade

✅ Production Ready

✅ Future Proof
