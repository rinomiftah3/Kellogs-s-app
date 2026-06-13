# 🚀 DATABASE_V2_PRODUCT_VARIANT_ARCHITECTURE.md

# KELLOGGS APP PRODUCT VARIANT ARCHITECTURE

Last Update: 09 Juni 2026

Version: 2.0

Status: Final

---

# OVERVIEW

Dokumen ini mendefinisikan arsitektur variant produk untuk Kelloggs App.

Tujuan:

* Mendukung multi ukuran
* Mendukung multi rasa
* Mendukung multi kemasan
* Mendukung SKU per kombinasi
* Mendukung stok per kombinasi
* Mendukung harga per kombinasi
* Mendukung promo per kombinasi

Target:

Arsitektur setara:

* Shopify
* Shopee
* Tokopedia
* Alfagift

---

# PROBLEM

Struktur awal:

```text
products
product_variants
product_variant_values
```

Tidak cukup.

Karena tidak bisa membedakan:

```text
Original 250g
Original 500g
Chocolate 250g
Chocolate 500g
```

sebagai SKU yang berbeda.

---

# SOLUTION

Gunakan:

```text
products

product_options

product_option_values

product_skus

product_sku_values
```

---

# ARCHITECTURE

## Product

Produk utama.

Contoh:

```text
Kellogg's Corn Flakes
```

---

## Product Option

Contoh:

```text
Size
Flavor
Package
```

---

## Product Option Value

Contoh:

```text
250g
500g

Original
Chocolate
```

---

## Product SKU

Kombinasi final yang dijual.

Contoh:

```text
CF-ORI-250

CF-ORI-500

CF-CHO-250

CF-CHO-500
```

---

# TABLE STRUCTURE

## products

Produk induk.

Contoh:

```text
Corn Flakes
```

Field:

```text
id

category_id

name

slug

description

thumbnail

is_active
```

---

## product_options

Field:

```text
id

product_id

name
```

Contoh:

```text
Size

Flavor
```

---

## product_option_values

Field:

```text
id

product_option_id

value
```

Contoh:

```text
250g
500g

Original
Chocolate
```

---

## product_skus

Field:

```text
id

product_id

sku

barcode

price

weight

stock

is_default

is_active

created_at

updated_at
```

Contoh:

```text
CF-ORI-250
CF-ORI-500
CF-CHO-250
CF-CHO-500
```

---

## product_sku_values

Pivot table.

Field:

```text
id

product_sku_id

product_option_value_id
```

---

# REAL EXAMPLE

Product:

```text
Corn Flakes
```

Options:

```text
Size
Flavor
```

Values:

```text
250g
500g

Original
Chocolate
```

Generated SKU:

```text
CF-ORI-250

CF-ORI-500

CF-CHO-250

CF-CHO-500
```

---

# RELATIONSHIP

## Product

```text
Product

1:N

ProductOption
```

---

## ProductOption

```text
ProductOption

1:N

ProductOptionValue
```

---

## Product

```text
Product

1:N

ProductSku
```

---

## ProductSku

```text
ProductSku

N:N

ProductOptionValue
```

melalui:

```text
product_sku_values
```

---

# STOCK STRATEGY

Stock disimpan di:

```text
product_skus
```

bukan:

```text
products
```

Karena:

```text
250g
500g
```

memiliki stok berbeda.

---

# PRICE STRATEGY

Harga disimpan di:

```text
product_skus
```

Karena:

```text
250g = Rp 20.000

500g = Rp 35.000
```

---

# BARCODE STRATEGY

Barcode disimpan pada:

```text
product_skus
```

karena setiap SKU dapat memiliki barcode berbeda.

---

# INVENTORY STRATEGY

Inventory tidak lagi:

```text
product_id
```

tetapi:

```text
product_sku_id
```

---

## inventories

Field:

```text
id

product_sku_id

current_stock

reserved_stock

available_stock
```

---

## stock_movements

Field:

```text
id

product_sku_id

type

quantity

reference
```

---

## stock_adjustments

Field:

```text
id

product_sku_id

old_stock

new_stock
```

---

## stock_opnames

Field:

```text
id

product_sku_id

system_stock

physical_stock
```

---

# CART STRATEGY

Cart item tidak boleh menyimpan:

```text
product_id
```

tetapi:

```text
product_sku_id
```

---

## cart_items

Field:

```text
cart_id

product_sku_id

quantity

price

subtotal
```

---

# ORDER STRATEGY

Order item harus menyimpan:

```text
product_sku_id
```

agar histori transaksi tidak berubah walaupun produk diubah.

---

## order_items

Field:

```text
order_id

product_sku_id

product_name

sku

price

quantity

subtotal
```

---

# PROMOTION STRATEGY

Promo dapat diterapkan pada:

```text
Product
```

atau

```text
Product SKU
```

---

# FUTURE READY FEATURES

Mendukung:

✅ Multi Variant

✅ Multi Size

✅ Multi Flavor

✅ Multi Package

✅ Multi Barcode

✅ Inventory Tracking

✅ Warehouse Tracking

✅ Promotion Engine

✅ Marketplace Sync

✅ POS Integration

---

# COMPARISON

## Simple Variant

```text
products
variants
variant_values
```

Score:

6/10

---

## Enterprise Variant

```text
products

product_options

product_option_values

product_skus

product_sku_values
```

Score:

10/10

---

# FINAL DECISION

Kelloggs App menggunakan:

Enterprise SKU Based Product Architecture

dengan struktur:

```text
products

product_options

product_option_values

product_skus

product_sku_values
```

Status:

✅ Approved

✅ Future Proof

✅ Inventory Ready

✅ Promotion Ready

✅ Production Ready

✅ Portfolio Grade
