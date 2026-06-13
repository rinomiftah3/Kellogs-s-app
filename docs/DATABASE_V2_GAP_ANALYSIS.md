# 🚀 DATABASE_V2_GAP_ANALYSIS.md

# KELLOGGS APP DATABASE GAP ANALYSIS V2

Last Update: 09 Juni 2026

Version: 2.1 Enterprise SKU Architecture

Status: Final

---

# OVERVIEW

Dokumen ini membandingkan:

```text
DATABASE CURRENT STATE
VS
DATABASE V2 TARGET STATE
```

Tujuan:

* Mengidentifikasi gap database
* Menentukan tabel yang dipertahankan
* Menentukan tabel yang perlu upgrade
* Menentukan tabel baru
* Menentukan tabel yang obsolete
* Menjadi panduan migration implementation

Dokumen ini adalah jembatan antara:

```text
Current Database
↓
Database V2
```

---

# CURRENT DATABASE INVENTORY

## Laravel Core

```text
users
password_reset_tokens
sessions

cache
cache_locks

jobs
job_batches
failed_jobs
```

Status:

✅ Keep

---

## Authentication

```text
personal_access_tokens
```

Status:

✅ Keep

Reason:

```text
Laravel Sanctum
```

---

## Authorization

```text
permissions
roles
model_has_permissions
model_has_roles
role_has_permissions
```

Status:

✅ Keep

Reason:

```text
Spatie Permission
```

---

## Activity Log

```text
activity_log
```

Status:

✅ Keep

Reason:

```text
Spatie Activity Log
```

---

## Business Tables

```text
categories
products
```

Status:

⚠ Need Upgrade

---

# GAP ANALYSIS SUMMARY

| Category            | Count |
| ------------------- | ----- |
| Existing Keep       | 13    |
| Existing Upgrade    | 2     |
| New Tables Required | 39    |
| Deprecated Tables   | 0     |

---

# TABLE STATUS ANALYSIS

# KEEP WITHOUT CHANGES

## users

Status:

```text
KEEP
```

Reason:

```text
Sudah sesuai kebutuhan
```

Minor Upgrade:

```text
Tidak diperlukan saat ini
```

---

## personal_access_tokens

Status:

```text
KEEP
```

Reason:

```text
Sanctum Compatible
```

---

## permissions

Status:

```text
KEEP
```

---

## roles

Status:

```text
KEEP
```

---

## model_has_permissions

Status:

```text
KEEP
```

---

## model_has_roles

Status:

```text
KEEP
```

---

## role_has_permissions

Status:

```text
KEEP
```

---

## activity_log

Status:

```text
KEEP
```

Reason:

```text
Audit Architecture Sudah Benar
```

---

# UPGRADE EXISTING TABLES

# CATEGORIES

Current:

```text
id
name
slug
description
is_active
```

---

Target:

```text
id
parent_id
name
slug
description
image
sort_order
is_active
created_at
updated_at
deleted_at
```

---

Required Upgrade:

Add:

```text
parent_id
image
sort_order
deleted_at
```

---

Impact:

```text
LOW
```

---

Migration Type:

```text
ALTER TABLE
```

---

# PRODUCTS

Current:

```text
id
category_id
name
slug
description
price
stock
image
is_active
```

---

Target:

```text
id
category_id
name
slug
short_description
description
thumbnail
status
is_featured
is_active
published_at
created_at
updated_at
deleted_at
```

---

Remove:

```text
price
stock
```

Move To:

```text
product_skus
inventories
```

---

Rename:

```text
image
↓
thumbnail
```

---

Add:

```text
short_description
status
is_featured
published_at
```

---

Impact:

```text
HIGH
```

---

Migration Type:

```text
ALTER TABLE
```

---

# NEW TABLES REQUIRED

# CUSTOMER MODULE

Create:

```text
customer_profiles
customer_addresses
customer_devices
customer_notifications
wishlists
```

Count:

```text
5
```

Priority:

```text
HIGH
```

---

# PRODUCT MODULE

Create:

```text
product_images

product_options
product_option_values

product_skus
product_sku_values

product_reviews
product_review_images
```

Count:

```text
7
```

Priority:

```text
CRITICAL
```

Reason:

```text
Core SKU Architecture
```

---

# INVENTORY MODULE

Create:

```text
inventories
stock_movements
stock_adjustments
stock_opnames
```

Count:

```text
4
```

Priority:

```text
CRITICAL
```

Reason:

```text
Products tidak lagi menyimpan stock
```

---

# CART MODULE

Create:

```text
carts
cart_items
```

Count:

```text
2
```

Priority:

```text
HIGH
```

---

# CHECKOUT MODULE

Create:

```text
checkout_sessions
checkout_items
```

Count:

```text
2
```

Priority:

```text
HIGH
```

---

# ORDER MODULE

Create:

```text
orders
order_items
order_histories
order_status_logs
```

Count:

```text
4
```

Priority:

```text
CRITICAL
```

Reason:

```text
Core Business Transaction
```

---

# PAYMENT MODULE

Create:

```text
payments
payment_transactions
payment_callbacks
```

Count:

```text
3
```

Priority:

```text
CRITICAL
```

Reason:

```text
Midtrans Integration
```

---

# SHIPPING MODULE

Create:

```text
couriers
shipping_methods
shipments
shipment_trackings
```

Count:

```text
4
```

Priority:

```text
HIGH
```

---

# PROMOTION MODULE

Create:

```text
vouchers
voucher_usages

promotions
promo_products
promo_categories
promo_skus
```

Count:

```text
6
```

Priority:

```text
HIGH
```

---

# LOYALTY MODULE

Create:

```text
loyalty_points
point_transactions
```

Count:

```text
2
```

Priority:

```text
MEDIUM
```

---

# SKU ARCHITECTURE IMPACT

Current Architecture:

```text
products
├── price
└── stock
```

---

Target Architecture:

```text
products

└── product_skus
    ├── price
    ├── barcode
    ├── weight
    └── inventory
```

---

Impact:

```text
VERY HIGH
```

Reason:

```text
Seluruh transaksi berubah menjadi:

product_sku_id
```

---

Affected Modules

```text
inventory
cart
checkout
orders
promotions
analytics
```

---

# DATA MIGRATION STRATEGY

# Existing Categories

Strategy:

```text
ALTER TABLE
```

---

# Existing Products

Strategy:

```text
ALTER TABLE
```

Lalu:

```text
Generate Default SKU
```

Contoh:

```text
Kellogg Corn Flakes

↓

SKU:
CF-DEFAULT
```

---

# Existing Stock

Current:

```text
products.stock
```

Move To:

```text
inventories.current_stock
```

---

# Existing Price

Current:

```text
products.price
```

Move To:

```text
product_skus.price
```

---

# IMPLEMENTATION ORDER

Step 1

```text
Upgrade Categories
```

---

Step 2

```text
Upgrade Products
```

---

Step 3

```text
Create SKU Architecture
```

---

Step 4

```text
Migrate Existing Product Data
```

---

Step 5

```text
Create Inventory Module
```

---

Step 6

```text
Create Customer Module
```

---

Step 7

```text
Create Transaction Modules
```

---

Step 8

```text
Create Promotion Module
```

---

Step 9

```text
Create Loyalty Module
```

---

# RISK ANALYSIS

## Categories

Risk:

```text
LOW
```

---

## Products

Risk:

```text
HIGH
```

Reason:

```text
Perubahan menuju SKU Architecture
```

---

## Inventory

Risk:

```text
MEDIUM
```

---

## Orders

Risk:

```text
LOW
```

Karena:

```text
Belum ada data produksi
```

---

# FINAL CONCLUSION

Current Database Foundation:

```text
GOOD
```

Enterprise Readiness:

```text
40%
```

Database V2 Completion Target:

```text
100%
```

Required Work:

```text
Upgrade Existing Tables : 2

Create New Tables : 39

Deprecated Tables : 0
```

Next Step:

```text
1. Categories Upgrade Migration
2. Products Upgrade Migration
3. SKU Architecture Migration
4. Inventory Migration
```

Status:

✅ Analysis Complete

✅ Ready For Migration Generation

✅ Ready For Backend Modernization Phase
