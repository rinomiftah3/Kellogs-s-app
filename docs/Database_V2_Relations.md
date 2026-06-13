# 🚀 DATABASE_V2_RELATIONS.md

# KELLOGGS APP DATABASE RELATIONSHIP BLUEPRINT V2

Last Update: 09 Juni 2026

Version: 2.1 Enterprise SKU Architecture

Status: Final

---

# OVERVIEW

Dokumen ini mendefinisikan:

* Foreign Key
* Cardinality
* Cascade Rule
* Database Relationship
* ERD Text Diagram

untuk seluruh modul pada Kelloggs App Enterprise E-Commerce Platform.

Dokumen ini menjadi referensi utama untuk:

* Migration
* Eloquent Relationship
* Repository Layer
* Service Layer
* API Development
* React Dashboard
* Flutter Mobile App

---

# RELATIONSHIP RULES

## Cardinality Legend

| Symbol | Meaning      |
| ------ | ------------ |
| 1:1    | One To One   |
| 1:N    | One To Many  |
| N:N    | Many To Many |

---

# CASCADE STRATEGY

## Business Tables

Default:

```php
->restrictOnDelete()
```

Digunakan pada:

* products
* orders
* payments
* shipments
* inventories

Tujuan:

Mencegah kehilangan data transaksi.

---

## Child Tables

Default:

```php
->cascadeOnDelete()
```

Digunakan pada:

* product_images
* product_reviews
* cart_items
* checkout_items
* order_items

---

## Soft Delete Strategy

Seluruh tabel bisnis menggunakan:

```php
softDeletes()
```

Kecuali:

* pivot tables
* logs
* cache
* jobs
* tracking tables

---

# CUSTOMER MODULE

## users → customer_profiles

Cardinality

```text
1 : 1
```

Foreign Key

```text
customer_profiles.user_id
→ users.id
```

Delete Rule

```text
CASCADE
```

---

## customer_profiles → customer_addresses

Cardinality

```text
1 : N
```

Foreign Key

```text
customer_addresses.customer_profile_id
→ customer_profiles.id
```

Delete Rule

```text
CASCADE
```

---

## customer_profiles → customer_devices

Cardinality

```text
1 : N
```

Foreign Key

```text
customer_devices.customer_profile_id
→ customer_profiles.id
```

Delete Rule

```text
CASCADE
```

---

## customer_profiles → customer_notifications

Cardinality

```text
1 : N
```

Foreign Key

```text
customer_notifications.customer_profile_id
→ customer_profiles.id
```

Delete Rule

```text
CASCADE
```

---

## customer_profiles → wishlists

Cardinality

```text
1 : N
```

Foreign Key

```text
wishlists.customer_profile_id
→ customer_profiles.id
```

Delete Rule

```text
CASCADE
```

---

# PRODUCT MODULE

## categories → categories

Cardinality

```text
1 : N
```

Foreign Key

```text
categories.parent_id
→ categories.id
```

Delete Rule

```text
RESTRICT
```

---

## categories → products

Cardinality

```text
1 : N
```

Foreign Key

```text
products.category_id
→ categories.id
```

Delete Rule

```text
RESTRICT
```

---

## products → product_images

Cardinality

```text
1 : N
```

Foreign Key

```text
product_images.product_id
→ products.id
```

Delete Rule

```text
CASCADE
```

---

# PRODUCT OPTION ARCHITECTURE

## products → product_options

Cardinality

```text
1 : N
```

Foreign Key

```text
product_options.product_id
→ products.id
```

Delete Rule

```text
CASCADE
```

---

## product_options → product_option_values

Cardinality

```text
1 : N
```

Foreign Key

```text
product_option_values.product_option_id
→ product_options.id
```

Delete Rule

```text
CASCADE
```

---

# PRODUCT SKU ARCHITECTURE

## products → product_skus

Cardinality

```text
1 : N
```

Foreign Key

```text
product_skus.product_id
→ products.id
```

Delete Rule

```text
CASCADE
```

---

## product_skus → product_sku_values

Cardinality

```text
1 : N
```

Foreign Key

```text
product_sku_values.product_sku_id
→ product_skus.id
```

Delete Rule

```text
CASCADE
```

---

## product_option_values → product_sku_values

Cardinality

```text
1 : N
```

Foreign Key

```text
product_sku_values.product_option_value_id
→ product_option_values.id
```

Delete Rule

```text
CASCADE
```

---

# REVIEW SYSTEM

## products → product_reviews

Cardinality

```text
1 : N
```

Foreign Key

```text
product_reviews.product_id
→ products.id
```

Delete Rule

```text
CASCADE
```

---

## customer_profiles → product_reviews

Cardinality

```text
1 : N
```

Foreign Key

```text
product_reviews.customer_profile_id
→ customer_profiles.id
```

Delete Rule

```text
CASCADE
```

---

## product_reviews → product_review_images

Cardinality

```text
1 : N
```

Foreign Key

```text
product_review_images.product_review_id
→ product_reviews.id
```

Delete Rule

```text
CASCADE
```

---

# CART MODULE

## customer_profiles → carts

Cardinality

```text
1 : 1
```

Foreign Key

```text
carts.customer_profile_id
→ customer_profiles.id
```

Delete Rule

```text
CASCADE
```

---

## carts → cart_items

Cardinality

```text
1 : N
```

Foreign Key

```text
cart_items.cart_id
→ carts.id
```

Delete Rule

```text
CASCADE
```

---

## product_skus → cart_items

Cardinality

```text
1 : N
```

Foreign Key

```text
cart_items.product_sku_id
→ product_skus.id
```

Delete Rule

```text
RESTRICT
```

---

# CHECKOUT MODULE

## customer_profiles → checkout_sessions

Cardinality

```text
1 : N
```

Foreign Key

```text
checkout_sessions.customer_profile_id
→ customer_profiles.id
```

Delete Rule

```text
RESTRICT
```

---

## checkout_sessions → checkout_items

Cardinality

```text
1 : N
```

Foreign Key

```text
checkout_items.checkout_session_id
→ checkout_sessions.id
```

Delete Rule

```text
CASCADE
```

---

## product_skus → checkout_items

Cardinality

```text
1 : N
```

Foreign Key

```text
checkout_items.product_sku_id
→ product_skus.id
```

Delete Rule

```text
RESTRICT
```

---

# ORDER MODULE

## customer_profiles → orders

Cardinality

```text
1 : N
```

Foreign Key

```text
orders.customer_profile_id
→ customer_profiles.id
```

Delete Rule

```text
RESTRICT
```

---

## customer_addresses → orders

Cardinality

```text
1 : N
```

Foreign Key

```text
orders.shipping_address_id
→ customer_addresses.id
```

Delete Rule

```text
RESTRICT
```

---

## orders → order_items

Cardinality

```text
1 : N
```

Foreign Key

```text
order_items.order_id
→ orders.id
```

Delete Rule

```text
CASCADE
```

---

## product_skus → order_items

Cardinality

```text
1 : N
```

Foreign Key

```text
order_items.product_sku_id
→ product_skus.id
```

Delete Rule

```text
RESTRICT
```

---

## orders → order_histories

Cardinality

```text
1 : N
```

Foreign Key

```text
order_histories.order_id
→ orders.id
```

Delete Rule

```text
CASCADE
```

---

## orders → order_status_logs

Cardinality

```text
1 : N
```

Foreign Key

```text
order_status_logs.order_id
→ orders.id
```

Delete Rule

```text
CASCADE
```

---

# PAYMENT MODULE

## orders → payments

Cardinality

```text
1 : 1
```

Foreign Key

```text
payments.order_id
→ orders.id
```

Delete Rule

```text
RESTRICT
```

---

## payments → payment_transactions

Cardinality

```text
1 : N
```

Foreign Key

```text
payment_transactions.payment_id
→ payments.id
```

Delete Rule

```text
CASCADE
```

---

## payments → payment_callbacks

Cardinality

```text
1 : N
```

Foreign Key

```text
payment_callbacks.payment_id
→ payments.id
```

Delete Rule

```text
CASCADE
```

---

# SHIPPING MODULE

## couriers → shipping_methods

Cardinality

```text
1 : N
```

Foreign Key

```text
shipping_methods.courier_id
→ couriers.id
```

Delete Rule

```text
RESTRICT
```

---

## shipping_methods → shipments

Cardinality

```text
1 : N
```

Foreign Key

```text
shipments.shipping_method_id
→ shipping_methods.id
```

Delete Rule

```text
RESTRICT
```

---

## orders → shipments

Cardinality

```text
1 : 1
```

Foreign Key

```text
shipments.order_id
→ orders.id
```

Delete Rule

```text
RESTRICT
```

---

## shipments → shipment_trackings

Cardinality

```text
1 : N
```

Foreign Key

```text
shipment_trackings.shipment_id
→ shipments.id
```

Delete Rule

```text
CASCADE
```

---

# PROMOTION MODULE

## vouchers → voucher_usages

Cardinality

```text
1 : N
```

Foreign Key

```text
voucher_usages.voucher_id
→ vouchers.id
```

---

## promotions → promo_products

Cardinality

```text
N : N
```

Foreign Key

```text
promo_products.promotion_id
→ promotions.id

promo_products.product_id
→ products.id
```

---

## promotions → promo_categories

Cardinality

```text
N : N
```

Foreign Key

```text
promo_categories.promotion_id
→ promotions.id

promo_categories.category_id
→ categories.id
```

---

## promotions → promo_skus

Cardinality

```text
N : N
```

Foreign Key

```text
promo_skus.promotion_id
→ promotions.id

promo_skus.product_sku_id
→ product_skus.id
```

---

# INVENTORY MODULE

## product_skus → inventories

Cardinality

```text
1 : 1
```

Foreign Key

```text
inventories.product_sku_id
→ product_skus.id
```

Delete Rule

```text
RESTRICT
```

---

## product_skus → stock_movements

Cardinality

```text
1 : N
```

Foreign Key

```text
stock_movements.product_sku_id
→ product_skus.id
```

Delete Rule

```text
RESTRICT
```

---

## product_skus → stock_adjustments

Cardinality

```text
1 : N
```

Foreign Key

```text
stock_adjustments.product_sku_id
→ product_skus.id
```

Delete Rule

```text
RESTRICT
```

---

## product_skus → stock_opnames

Cardinality

```text
1 : N
```

Foreign Key

```text
stock_opnames.product_sku_id
→ product_skus.id
```

Delete Rule

```text
RESTRICT
```

---

# LOYALTY MODULE

## customer_profiles → loyalty_points

Cardinality

```text
1 : 1
```

Foreign Key

```text
loyalty_points.customer_profile_id
→ customer_profiles.id
```

---

## customer_profiles → point_transactions

Cardinality

```text
1 : N
```

Foreign Key

```text
point_transactions.customer_profile_id
→ customer_profiles.id
```

---

# HIGH LEVEL ERD

```text
USERS
│
└── CUSTOMER_PROFILES
    │
    ├── CUSTOMER_ADDRESSES
    ├── CUSTOMER_DEVICES
    ├── CUSTOMER_NOTIFICATIONS
    ├── WISHLISTS
    │
    ├── CARTS
    │   └── CART_ITEMS
    │       └── PRODUCT_SKUS
    │
    ├── CHECKOUT_SESSIONS
    │   └── CHECKOUT_ITEMS
    │       └── PRODUCT_SKUS
    │
    ├── ORDERS
    │   ├── ORDER_ITEMS
    │   │   └── PRODUCT_SKUS
    │   │
    │   ├── ORDER_HISTORIES
    │   ├── ORDER_STATUS_LOGS
    │   │
    │   ├── PAYMENTS
    │   │   ├── PAYMENT_TRANSACTIONS
    │   │   └── PAYMENT_CALLBACKS
    │   │
    │   └── SHIPMENTS
    │       └── SHIPMENT_TRACKINGS
    │
    ├── LOYALTY_POINTS
    └── POINT_TRANSACTIONS

CATEGORIES
│
└── PRODUCTS
    │
    ├── PRODUCT_IMAGES
    │
    ├── PRODUCT_OPTIONS
    │   └── PRODUCT_OPTION_VALUES
    │
    ├── PRODUCT_SKUS
    │   └── PRODUCT_SKU_VALUES
    │
    ├── PRODUCT_REVIEWS
    │   └── PRODUCT_REVIEW_IMAGES
    │
    ├── INVENTORIES
    ├── STOCK_MOVEMENTS
    ├── STOCK_ADJUSTMENTS
    └── STOCK_OPNAMES

PROMOTIONS
├── PROMO_PRODUCTS
├── PROMO_CATEGORIES
└── PROMO_SKUS

VOUCHERS
└── VOUCHER_USAGES

COURIERS
└── SHIPPING_METHODS
```

---

# FINAL STATUS

Database Blueprint V2

✅ Completed

Database Relations V2

✅ Completed

Enterprise SKU Architecture

✅ Completed

Ready For:

* DATABASE_V2_INDEXES.md Revision
* DATABASE_V2_FIELDS.md Revision
* DATABASE_V2_MIGRATION_PLAN.md Revision
* Migration Generation
* Eloquent Model Generation
* Factory Generation
* Seeder Generation
* API Development
