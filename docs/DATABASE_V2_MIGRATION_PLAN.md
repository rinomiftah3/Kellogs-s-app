# 🚀 DATABASE_V2_MIGRATION_PLAN.md

# KELLOGGS APP DATABASE MIGRATION PLAN V2

Last Update: 09 Juni 2026

Version: 2.1 Enterprise SKU Architecture

Status: Final

---

# OVERVIEW

Dokumen ini mendefinisikan urutan migration resmi untuk Database V2 Kelloggs App.

Tujuan:

* Menghindari foreign key error
* Menghindari circular dependency
* Mempermudah rollback
* Mempermudah testing
* Menjamin konsistensi struktur database

Dokumen ini menjadi referensi utama untuk:

* Laravel Migration
* Seeder
* Factory
* Testing
* CI/CD Deployment

---

# MIGRATION PRINCIPLES

## 1. Parent First

Selalu buat parent table terlebih dahulu.

Contoh:

```text
categories
↓
products
```

---

## 2. Child Later

Tabel child dibuat setelah parent tersedia.

Contoh:

```text
products
↓
product_images
```

---

## 3. SKU Architecture First

Seluruh modul transaksi bergantung pada:

```text
product_skus
```

Karena itu Product SKU wajib selesai terlebih dahulu.

---

## 4. Inventory Driven

Semua stok berada pada:

```text
inventories
```

bukan products.

---

## 5. Transaction Safe

Order, Payment, Shipping dibuat setelah seluruh product architecture selesai.

---

# PHASE 0

# EXISTING LARAVEL TABLES

Status:

✅ Existing

Migration:

```text
users
password_reset_tokens
sessions

cache
cache_locks

jobs
job_batches
failed_jobs

personal_access_tokens
```

---

# PHASE 1

# AUTHORIZATION & AUDIT

Priority:

Critical

---

## permissions

```text
permissions
```

---

## roles

```text
roles
```

---

## model_has_permissions

```text
model_has_permissions
```

---

## model_has_roles

```text
model_has_roles
```

---

## role_has_permissions

```text
role_has_permissions
```

---

## activity_log

```text
activity_log
```

---

Expected Result:

```text
Authentication Ready
Authorization Ready
Audit Ready
```

---

# PHASE 2

# CUSTOMER MODULE

Priority:

Critical

---

## customer_profiles

Dependencies:

```text
users
```

---

## customer_addresses

Dependencies:

```text
customer_profiles
```

---

## customer_devices

Dependencies:

```text
customer_profiles
```

---

## customer_notifications

Dependencies:

```text
customer_profiles
```

---

## wishlists

Dependencies:

```text
customer_profiles
products
```

---

Expected Result:

```text
Customer Management Ready
```

---

# PHASE 3

# CATEGORY MODULE

Priority:

Critical

---

## categories

Self Reference:

```text
parent_id
```

---

Expected Result:

```text
Category Tree Ready
```

---

# PHASE 4

# PRODUCT CORE MODULE

Priority:

Critical

---

## products

Dependencies:

```text
categories
```

---

## product_images

Dependencies:

```text
products
```

---

Expected Result:

```text
Product Catalog Ready
```

---

# PHASE 5

# SKU ARCHITECTURE

Priority:

Highest

Core Of Entire E-Commerce

---

## product_options

Dependencies:

```text
products
```

---

## product_option_values

Dependencies:

```text
product_options
```

---

## product_skus

Dependencies:

```text
products
```

---

## product_sku_values

Dependencies:

```text
product_skus

product_option_values
```

---

Expected Result:

```text
SKU Engine Ready
Variant System Ready
```

---

# PHASE 6

# PRODUCT REVIEW MODULE

---

## product_reviews

Dependencies:

```text
products

customer_profiles
```

---

## product_review_images

Dependencies:

```text
product_reviews
```

---

Expected Result:

```text
Review System Ready
```

---

# PHASE 7

# INVENTORY MODULE

Priority:

Critical

Semua inventory menggunakan:

```text
product_sku_id
```

---

## inventories

Dependencies:

```text
product_skus
```

---

## stock_movements

Dependencies:

```text
product_skus
```

---

## stock_adjustments

Dependencies:

```text
product_skus
```

---

## stock_opnames

Dependencies:

```text
product_skus
```

---

Expected Result:

```text
Inventory Ready
Stock Audit Ready
```

---

# PHASE 8

# CART MODULE

---

## carts

Dependencies:

```text
customer_profiles
```

---

## cart_items

Dependencies:

```text
carts

product_skus
```

---

Expected Result:

```text
Shopping Cart Ready
```

---

# PHASE 9

# CHECKOUT MODULE

---

## checkout_sessions

Dependencies:

```text
customer_profiles
```

---

## checkout_items

Dependencies:

```text
checkout_sessions

product_skus
```

---

Expected Result:

```text
Checkout Ready
```

---

# PHASE 10

# PROMOTION MODULE

Priority:

High

---

## vouchers

No Dependency

---

## promotions

No Dependency

---

## promo_products

Dependencies:

```text
promotions

products
```

---

## promo_categories

Dependencies:

```text
promotions

categories
```

---

## promo_skus

Dependencies:

```text
promotions

product_skus
```

---

Expected Result:

```text
Promotion Engine Ready
Voucher Engine Ready
```

---

# PHASE 11

# ORDER MODULE

Priority:

Highest

---

## orders

Dependencies:

```text
customer_profiles

customer_addresses
```

---

## order_items

Dependencies:

```text
orders

product_skus
```

---

## order_histories

Dependencies:

```text
orders
```

---

## order_status_logs

Dependencies:

```text
orders
```

---

Expected Result:

```text
Order Management Ready
```

---

# PHASE 12

# PAYMENT MODULE

Priority:

Highest

Midtrans Ready

---

## payments

Dependencies:

```text
orders
```

---

## payment_transactions

Dependencies:

```text
payments
```

---

## payment_callbacks

Dependencies:

```text
payments
```

---

Expected Result:

```text
Payment Ready
Midtrans Ready
```

---

# PHASE 13

# SHIPPING MODULE

Priority:

High

---

## couriers

No Dependency

---

## shipping_methods

Dependencies:

```text
couriers
```

---

## shipments

Dependencies:

```text
orders

shipping_methods
```

---

## shipment_trackings

Dependencies:

```text
shipments
```

---

Expected Result:

```text
Shipping Ready
Tracking Ready
```

---

# PHASE 14

# LOYALTY MODULE

Priority:

Medium

---

## loyalty_points

Dependencies:

```text
customer_profiles
```

---

## point_transactions

Dependencies:

```text
customer_profiles
```

---

Expected Result:

```text
Loyalty System Ready
```

---

# MIGRATION EXECUTION ORDER

Final Order:

```text
1. permissions
2. roles
3. model_has_permissions
4. model_has_roles
5. role_has_permissions
6. activity_log

7. customer_profiles
8. customer_addresses
9. customer_devices
10. customer_notifications

11. categories

12. products
13. product_images

14. product_options
15. product_option_values
16. product_skus
17. product_sku_values

18. product_reviews
19. product_review_images

20. inventories
21. stock_movements
22. stock_adjustments
23. stock_opnames

24. wishlists

25. carts
26. cart_items

27. checkout_sessions
28. checkout_items

29. vouchers
30. promotions
31. promo_products
32. promo_categories
33. promo_skus

34. orders
35. order_items
36. order_histories
37. order_status_logs

38. payments
39. payment_transactions
40. payment_callbacks

41. couriers
42. shipping_methods
43. shipments
44. shipment_trackings

45. loyalty_points
46. point_transactions
```

---

# IMPLEMENTATION STRATEGY

Current Database:

```text
users
roles
permissions
categories
products
activity_log
```

Status:

Need Upgrade

---

Implementation Method:

```text
Create New Migration

Do Not Edit Old Migration
```

Tujuan:

* Aman untuk rollback
* Aman untuk git history
* Aman untuk deployment

---

# FINAL STATUS

Migration Plan V2

✅ Completed

Enterprise SKU Architecture

✅ Completed

Foreign Key Dependency Mapping

✅ Completed

Inventory Architecture

✅ Completed

Payment Architecture

✅ Completed

Shipping Architecture

✅ Completed

Ready For:

* Laravel Migration Generation
* Model Generation
* Factory Generation
* Seeder Generation
* API Development
* Admin Dashboard Development
* Flutter Mobile Development

Target:

✅ Enterprise Grade

✅ Portfolio Grade

✅ Production Ready

✅ Zero Redesign Database
