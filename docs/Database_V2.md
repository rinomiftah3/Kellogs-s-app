# 🚀 DATABASE_V2.md

# KELLOGGS APP DATABASE BLUEPRINT V2

Last Update: 09 Juni 2026

Version: 2.1 Enterprise SKU Architecture

Status: Final Blueprint

---

# OVERVIEW

Database V2 dirancang sebagai fondasi utama Kelloggs App Enterprise E-Commerce Platform.

Blueprint ini menjadi Single Source of Truth untuk:

* Migration
* Model
* Seeder
* Factory
* API Development
* Admin Dashboard
* Mobile Application
* Reporting
* Analytics
* Inventory Management
* Promotion Engine

Target utama:

* Scalable
* Maintainable
* Secure
* Inventory Ready
* Payment Ready
* Shipping Ready
* Promotion Ready
* Analytics Ready
* Portfolio Grade
* Production Ready

---

# BUSINESS DOMAIN

Jenis bisnis:

FMCG (Fast Moving Consumer Goods)

Contoh produk:

* Sereal
* Snack
* Minuman
* Produk Sarapan
* Produk Konsumsi Harian

Karakteristik bisnis:

* Multi kategori
* Multi SKU
* Multi ukuran
* Multi rasa
* High transaction volume
* Stock sensitive
* Promotion intensive
* Inventory intensive

Karena itu database menggunakan:

Enterprise SKU Based Product Architecture

---

# DATABASE PRINCIPLES

## 1. Single Responsibility Table

Setiap tabel hanya memiliki satu tanggung jawab.

Contoh:

products

Tidak menyimpan:

* review
* inventory
* images
* sku
* stock

Semua dipisahkan ke tabel masing-masing.

---

## 2. SKU First Architecture

Seluruh transaksi bisnis menggunakan:

product_sku_id

bukan:

product_id

Karena:

Setiap kombinasi produk memiliki:

* harga berbeda
* stok berbeda
* barcode berbeda
* promo berbeda

---

## 3. Soft Delete First

Seluruh tabel bisnis menggunakan:

softDeletes()

Kecuali:

* pivot tables
* logs
* cache
* jobs
* tracking tables

---

## 4. Audit Friendly

Seluruh aktivitas penting harus dapat ditelusuri melalui:

* Activity Log
* Stock Movement
* Order History
* Payment Callback
* Shipment Tracking

---

## 5. API Friendly

Database harus mendukung:

* REST API
* React Dashboard
* Flutter Mobile App
* Future Marketplace Integration

---

# DATABASE MODULES

## Core System

* users
* personal_access_tokens
* activity_log
* permissions
* roles

---

## Customer Module

* customer_profiles
* customer_addresses
* customer_devices
* customer_notifications
* wishlists

---

## Product Module

* categories
* products
* product_images

### Product Option Architecture

* product_options
* product_option_values

### Product SKU Architecture

* product_skus
* product_sku_values

### Review System

* product_reviews
* product_review_images

---

## Cart Module

* carts
* cart_items

---

## Checkout Module

* checkout_sessions
* checkout_items

---

## Order Module

* orders
* order_items
* order_histories
* order_status_logs

---

## Payment Module

* payments
* payment_transactions
* payment_callbacks

---

## Shipping Module

* couriers
* shipping_methods
* shipments
* shipment_trackings

---

## Promotion Module

* vouchers
* voucher_usages
* promotions
* promo_products
* promo_categories
* promo_skus

---

## Inventory Module

* inventories
* stock_movements
* stock_adjustments
* stock_opnames

---

## Loyalty Module

* loyalty_points
* point_transactions

---

# CATEGORY STRUCTURE

Hierarchical Category

Contoh:

Makanan
├── Sereal
├── Snack

Minuman
├── Susu
├── Kopi

Struktur:

categories

* id
* parent_id
* name
* slug
* description
* image
* sort_order
* is_active

Relasi:

Category

↓

hasMany

↓

Category

(parent-child)

---

# PRODUCT ARCHITECTURE

Menggunakan:

Enterprise SKU Based Architecture

---

## Product

Produk induk.

Contoh:

Kellogg's Corn Flakes

products hanya menyimpan:

* informasi umum produk
* kategori
* deskripsi
* thumbnail

Tidak menyimpan:

* harga
* stok
* barcode

---

## Product Options

Contoh:

Size

Flavor

Package

Tabel:

product_options

---

## Product Option Values

Contoh:

250g
500g

Original
Chocolate

Tabel:

product_option_values

---

## Product SKU

Produk yang benar-benar dijual.

Contoh:

CF-ORI-250

CF-ORI-500

CF-CHO-250

CF-CHO-500

Tabel:

product_skus

Setiap SKU memiliki:

* harga
* stok
* barcode
* berat
* status

sendiri-sendiri.

---

## Product SKU Values

Menghubungkan SKU dengan Option Value.

Contoh:

CF-ORI-250

↓

Original

↓

250g

Tabel:

product_sku_values

---

# INVENTORY STRATEGY

Inventory berbasis SKU.

Bukan berbasis Product.

---

## inventories

Menyimpan:

* current_stock
* reserved_stock
* available_stock

berdasarkan:

product_sku_id

---

## stock_movements

Mencatat:

* stock_in
* stock_out
* adjustment
* return

berdasarkan:

product_sku_id

---

## stock_adjustments

Mencatat perubahan stok manual.

---

## stock_opnames

Mencatat hasil audit fisik gudang.

---

# CART STRATEGY

Cart menggunakan:

product_sku_id

Contoh:

Yang masuk ke keranjang:

Corn Flakes Chocolate 500g

bukan:

Corn Flakes

---

# CHECKOUT STRATEGY

Checkout menggunakan:

product_sku_id

untuk memastikan:

* harga benar
* stok benar
* promo benar

---

# ORDER STRATEGY

Order Item menyimpan snapshot:

* product_name
* sku
* quantity
* price

Tujuan:

Riwayat transaksi tidak berubah walaupun data produk berubah.

---

# ORDER FLOW

Customer

↓

Cart

↓

Checkout

↓

Order

↓

Payment

↓

Shipment

↓

Completed

---

# PAYMENT STRATEGY

Gateway Utama:

* Midtrans

Future Ready:

* Xendit
* Tripay

Tabel:

* payments
* payment_transactions
* payment_callbacks

Semua callback wajib disimpan.

Tujuan:

* audit
* troubleshooting
* dispute handling

---

# SHIPPING STRATEGY

Provider:

* RajaOngkir
* BinderByte

Future:

* JNE
* J&T
* SiCepat
* AnterAja

Tabel:

* couriers
* shipping_methods
* shipments
* shipment_trackings

---

# PROMOTION STRATEGY

Mendukung:

* Voucher
* Discount
* Flash Sale
* Product Promotion
* Category Promotion
* SKU Promotion

Promo dapat diterapkan ke:

* Product
* Category
* Product SKU

---

# LOYALTY STRATEGY

Customer memperoleh poin dari transaksi.

Poin dapat:

* Earn
* Redeem
* Expire
* Adjustment

---

# INDEX STRATEGY

Index wajib pada:

Users

* email
* is_active

Categories

* slug
* parent_id

Products

* slug
* category_id

Product SKUs

* sku
* barcode
* is_active

Orders

* order_number
* customer_profile_id
* status

Payments

* status
* method

Shipments

* tracking_number

---

# FOREIGN KEY STRATEGY

Default:

restrictOnDelete()

Digunakan untuk:

* products
* orders
* payments
* shipments
* inventories

---

Gunakan:

cascadeOnDelete()

hanya pada:

* child table
* pivot table
* temporary table

---

# AUDIT STRATEGY

Audit utama:

Spatie Activity Log

Audit tambahan:

* stock_movements
* order_histories
* payment_callbacks
* shipment_trackings

Target:

100% Transaction Traceability

---

# PERFORMANCE STRATEGY

Current:

* Database Cache
* Database Queue

Future:

* Redis Cache
* Redis Queue
* Horizon

Future Search:

* Laravel Scout
* Meilisearch

Target:

* 100.000+ Products
* 1.000.000+ Orders
* 10.000+ Active Users / Day

---

# IMPLEMENTATION ORDER

Phase 1

* Existing Table Upgrade
* Categories V2
* Products V2

Phase 2

* Product Option Architecture
* Product SKU Architecture

Phase 3

* Customer Module

Phase 4

* Inventory Module

Phase 5

* Cart Module
* Checkout Module

Phase 6

* Order Module

Phase 7

* Payment Module

Phase 8

* Shipping Module

Phase 9

* Promotion Module

Phase 10

* Loyalty Module

---

# FINAL GOAL

Database V2 menjadi fondasi utama Kelloggs App Enterprise E-Commerce Platform yang:

* Scalable
* Maintainable
* Secure
* Audit Friendly
* API Friendly
* Mobile Friendly
* Inventory Ready
* Promotion Ready
* Analytics Ready
* Production Ready

dan tidak memerlukan redesign besar ketika memasuki tahap:

* React Admin Dashboard
* Flutter Customer App
* Midtrans Integration
* Firebase Integration
* Marketplace Integration
* Analytics & Reporting
* Production Deployment

Status:

✅ Enterprise SKU Architecture Approved

✅ Future Proof

✅ Inventory Ready

✅ Promotion Ready

✅ Marketplace Ready

✅ Production Ready
