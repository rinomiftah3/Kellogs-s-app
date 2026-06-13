# 🚀 DATABASE_V2_INDEXES.md

# KELLOGGS APP DATABASE INDEX STRATEGY V2

Last Update: 09 Juni 2026

Version: 2.1 Enterprise SKU Architecture

Status: Final

---

# OVERVIEW

Dokumen ini mendefinisikan strategi indexing untuk seluruh database Kelloggs App.

Tujuan utama:

* Mempercepat query
* Mengurangi full table scan
* Mendukung filtering
* Mendukung sorting
* Mendukung analytics
* Mendukung dashboard
* Mendukung mobile API
* Mendukung scalability

Target:

* 10.000+ Active Users / Day
* 100.000+ Products
* 1.000.000+ Orders

---

# INDEXING PRINCIPLES

## 1. Index Only What Is Queried

Index dibuat hanya untuk:

* Search
* Filter
* Sort
* Join
* Foreign Key
* Analytics

---

## 2. Foreign Key Must Be Indexed

Seluruh foreign key wajib memiliki index.

---

## 3. Composite Index For Filtering

Contoh:

```sql
category_id,
is_active
```

lebih efektif dibanding index tunggal ketika digunakan bersamaan.

---

## 4. Unique Index For Business Identifier

Digunakan untuk:

```sql
email
slug
sku
barcode
order_number
voucher_code
tracking_number
transaction_id
```

---

# USERS

## Unique Index

```sql
email
```

---

## Normal Index

```sql
is_active

email_verified_at

last_login_at
```

---

## Composite Index

```sql
is_active,
email_verified_at
```

---

# CUSTOMER_PROFILES

## Unique Index

```sql
user_id
```

---

## Search Index

```sql
phone
```

---

## Composite Index

```sql
is_active,
created_at
```

---

# CUSTOMER_ADDRESSES

## Foreign Key Index

```sql
customer_profile_id
```

---

## Composite Index

```sql
customer_profile_id,
is_default
```

---

# CUSTOMER_DEVICES

## Foreign Key Index

```sql
customer_profile_id
```

---

## Composite Index

```sql
customer_profile_id,
last_active_at
```

---

# CUSTOMER_NOTIFICATIONS

## Foreign Key Index

```sql
customer_profile_id
```

---

## Composite Index

```sql
customer_profile_id,
is_read
```

---

# CATEGORIES

## Unique Index

```sql
slug
```

---

## Foreign Key Index

```sql
parent_id
```

---

## Search Index

```sql
name
```

---

## Composite Index

```sql
parent_id,
is_active
```

---

## Composite Index

```sql
is_active,
sort_order
```

---

# PRODUCTS

## Unique Index

```sql
slug
```

---

## Foreign Key Index

```sql
category_id
```

---

## Search Index

```sql
name
```

---

## Composite Index

```sql
category_id,
is_active
```

---

## Composite Index

```sql
is_featured,
is_active
```

---

## Composite Index

```sql
status,
is_active
```

---

## Composite Index

```sql
created_at,
is_active
```

---

# PRODUCT_IMAGES

## Foreign Key Index

```sql
product_id
```

---

## Composite Index

```sql
product_id,
sort_order
```

---

# PRODUCT_OPTIONS

## Foreign Key Index

```sql
product_id
```

---

## Composite Unique

```sql
product_id,
name
```

---

# PRODUCT_OPTION_VALUES

## Foreign Key Index

```sql
product_option_id
```

---

## Composite Unique

```sql
product_option_id,
value
```

---

# PRODUCT_SKUS

## Unique Index

```sql
sku

barcode
```

---

## Foreign Key Index

```sql
product_id
```

---

## Composite Index

```sql
product_id,
is_active
```

---

## Composite Index

```sql
is_default,
is_active
```

---

## Composite Index

```sql
price,
is_active
```

---

## Composite Index

```sql
created_at,
is_active
```

---

# PRODUCT_SKU_VALUES

## Foreign Key Index

```sql
product_sku_id

product_option_value_id
```

---

## Composite Unique

```sql
product_sku_id,
product_option_value_id
```

---

# PRODUCT_REVIEWS

## Foreign Key Index

```sql
product_id

customer_profile_id
```

---

## Composite Index

```sql
product_id,
rating
```

---

## Composite Index

```sql
product_id,
created_at
```

---

# PRODUCT_REVIEW_IMAGES

## Foreign Key Index

```sql
product_review_id
```

---

# WISHLISTS

## Composite Unique

```sql
customer_profile_id,
product_id
```

---

# CARTS

## Unique Index

```sql
customer_profile_id
```

---

# CART_ITEMS

## Foreign Key Index

```sql
cart_id

product_sku_id
```

---

## Composite Unique

```sql
cart_id,
product_sku_id
```

---

# CHECKOUT_SESSIONS

## Foreign Key Index

```sql
customer_profile_id
```

---

## Composite Index

```sql
customer_profile_id,
expired_at
```

---

## Composite Index

```sql
status,
expired_at
```

---

# CHECKOUT_ITEMS

## Foreign Key Index

```sql
checkout_session_id

product_sku_id
```

---

## Composite Unique

```sql
checkout_session_id,
product_sku_id
```

---

# ORDERS

## Unique Index

```sql
order_number
```

---

## Foreign Key Index

```sql
customer_profile_id

shipping_address_id
```

---

## Composite Index

```sql
customer_profile_id,
status
```

---

## Composite Index

```sql
status,
ordered_at
```

---

## Composite Index

```sql
created_at,
grand_total
```

---

## Composite Index

```sql
payment_status,
status
```

---

# ORDER_ITEMS

## Foreign Key Index

```sql
order_id

product_sku_id
```

---

## Composite Index

```sql
order_id,
product_sku_id
```

---

# ORDER_HISTORIES

## Foreign Key Index

```sql
order_id
```

---

## Composite Index

```sql
order_id,
status
```

---

# ORDER_STATUS_LOGS

## Foreign Key Index

```sql
order_id
```

---

## Composite Index

```sql
order_id,
created_at
```

---

# PAYMENTS

## Foreign Key Index

```sql
order_id
```

---

## Composite Index

```sql
status,
method
```

---

## Composite Index

```sql
status,
paid_at
```

---

# PAYMENT_TRANSACTIONS

## Foreign Key Index

```sql
payment_id
```

---

## Unique Index

```sql
transaction_id
```

---

# PAYMENT_CALLBACKS

## Foreign Key Index

```sql
payment_id
```

---

## Composite Index

```sql
payment_id,
created_at
```

---

# COURIERS

## Unique Index

```sql
code
```

---

# SHIPPING_METHODS

## Foreign Key Index

```sql
courier_id
```

---

## Composite Index

```sql
courier_id,
service_code
```

---

# SHIPMENTS

## Unique Index

```sql
tracking_number
```

---

## Foreign Key Index

```sql
order_id

shipping_method_id
```

---

## Composite Index

```sql
status,
shipped_at
```

---

# SHIPMENT_TRACKINGS

## Foreign Key Index

```sql
shipment_id
```

---

## Composite Index

```sql
shipment_id,
tracked_at
```

---

# VOUCHERS

## Unique Index

```sql
code
```

---

## Composite Index

```sql
start_date,
end_date
```

---

## Composite Index

```sql
is_active,
start_date,
end_date
```

---

# VOUCHER_USAGES

## Foreign Key Index

```sql
voucher_id

customer_profile_id

order_id
```

---

## Composite Unique

```sql
voucher_id,
customer_profile_id,
order_id
```

---

# PROMOTIONS

## Composite Index

```sql
is_active,
start_date,
end_date
```

---

# PROMO_PRODUCTS

## Composite Unique

```sql
promotion_id,
product_id
```

---

# PROMO_CATEGORIES

## Composite Unique

```sql
promotion_id,
category_id
```

---

# PROMO_SKUS

## Composite Unique

```sql
promotion_id,
product_sku_id
```

---

# INVENTORIES

## Unique Index

```sql
product_sku_id
```

---

## Composite Index

```sql
available_stock,
current_stock
```

---

## Composite Index

```sql
current_stock,
reserved_stock
```

---

# STOCK_MOVEMENTS

## Foreign Key Index

```sql
product_sku_id
```

---

## Composite Index

```sql
product_sku_id,
type
```

---

## Composite Index

```sql
product_sku_id,
created_at
```

---

# STOCK_ADJUSTMENTS

## Foreign Key Index

```sql
product_sku_id
```

---

## Composite Index

```sql
product_sku_id,
created_at
```

---

# STOCK_OPNAMES

## Foreign Key Index

```sql
product_sku_id
```

---

## Composite Index

```sql
product_sku_id,
opname_date
```

---

# LOYALTY_POINTS

## Unique Index

```sql
customer_profile_id
```

---

# POINT_TRANSACTIONS

## Foreign Key Index

```sql
customer_profile_id
```

---

## Composite Index

```sql
customer_profile_id,
created_at
```

---

## Composite Index

```sql
type,
created_at
```

---

# ANALYTICS INDEX STRATEGY

Dashboard Analytics akan sering menggunakan:

* Orders Per Day
* Orders Per Month
* Revenue Per Day
* Revenue Per Month
* Best Selling Products
* Active Customers
* Best Selling SKU
* Low Stock SKU

Karena itu wajib ada:

Orders

```sql
status,
ordered_at
```

Payments

```sql
status,
paid_at
```

Product SKUs

```sql
is_active,
created_at
```

Stock Movements

```sql
product_sku_id,
created_at
```

---

# SEARCH STRATEGY

Current

```text
LIKE Query
```

Tahap awal:

✅ Cukup

---

Future Upgrade

```text
Laravel Scout
+
Meilisearch
```

atau

```text
Elasticsearch
```

Status:

Planned

---

# FINAL CONCLUSION

Index Strategy V2 dirancang untuk:

✅ High Performance

✅ Enterprise SKU Architecture

✅ Inventory Ready

✅ Analytics Ready

✅ Dashboard Ready

✅ Mobile Ready

✅ Scalable

✅ Portfolio Grade

✅ Production Ready

Target:

* 100.000+ Products
* 1.000.000+ Orders
* 10.000+ Active Users / Day

tanpa perlu redesign indexing di masa depan.
