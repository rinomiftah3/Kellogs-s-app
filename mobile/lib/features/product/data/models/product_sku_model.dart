class ProductSkuModel {
  final int id;

  final String sku;

  final String? barcode;

  /*
  |--------------------------------------------------------------------------
  | Pricing
  |--------------------------------------------------------------------------
  */

  final double price;

  final String priceFormatted;

  final double? compareAtPrice;

  final String? compareAtPriceFormatted;

  final bool hasDiscount;

  final double discountAmount;

  final String? discountAmountFormatted;

  final double discountPercentage;

  /*
  |--------------------------------------------------------------------------
  | Inventory
  |--------------------------------------------------------------------------
  */

  final int stock;

  final bool isInStock;

  final bool isOutOfStock;

  final bool isLowStock;

  final String stockStatus;

  final String stockLabel;

  /*
  |--------------------------------------------------------------------------
  | Product
  |--------------------------------------------------------------------------
  */

  final int productId;

  /*
  |--------------------------------------------------------------------------
  | Status
  |--------------------------------------------------------------------------
  */

  final bool isDefault;

  final bool isActive;

  final bool isPublished;

  final bool canBePurchased;

  /*
  |--------------------------------------------------------------------------
  | Option Values
  |--------------------------------------------------------------------------
  */

  final List<SkuOptionValue> optionValues;

  const ProductSkuModel({
    required this.id,
    required this.sku,
    required this.barcode,
    required this.price,
    required this.priceFormatted,
    required this.compareAtPrice,
    required this.compareAtPriceFormatted,
    required this.hasDiscount,
    required this.discountAmount,
    required this.discountAmountFormatted,
    required this.discountPercentage,
    required this.stock,
    required this.isInStock,
    required this.isOutOfStock,
    required this.isLowStock,
    required this.stockStatus,
    required this.stockLabel,
    required this.productId,
    required this.isDefault,
    required this.isActive,
    required this.isPublished,
    required this.canBePurchased,
    required this.optionValues,
  });

  factory ProductSkuModel.fromJson(
    Map<String, dynamic> json,
  ) {
    double toDouble(dynamic value) {
      if (value == null) return 0;

      if (value is double) return value;

      if (value is int) return value.toDouble();

      return double.tryParse(
            value.toString(),
          ) ??
          0;
    }

    int toInt(dynamic value) {
      if (value == null) return 0;

      if (value is int) return value;

      return int.tryParse(
            value.toString(),
          ) ??
          0;
    }

    return ProductSkuModel(
      id: toInt(json['id']),

      sku: json['sku']?.toString() ?? '',

      barcode: json['barcode']?.toString(),

      /*
      |--------------------------------------------------------------------------
      | Pricing
      |--------------------------------------------------------------------------
      */

      price: toDouble(json['price']),

      priceFormatted:
          json['price_formatted']?.toString() ??
              '',

      compareAtPrice:
          json['compare_at_price'] != null
              ? toDouble(
                  json['compare_at_price'],
                )
              : null,

      compareAtPriceFormatted:
          json['compare_at_price_formatted']
              ?.toString(),

      hasDiscount:
          json['has_discount'] == true,

      discountAmount:
          toDouble(
        json['discount_amount'],
      ),

      discountAmountFormatted:
          json['discount_amount_formatted']
              ?.toString(),

      discountPercentage:
          toDouble(
        json['discount_percentage'],
      ),

      /*
      |--------------------------------------------------------------------------
      | Inventory
      |--------------------------------------------------------------------------
      */

      stock: toInt(
        json['stock'],
      ),

      isInStock:
          json['is_in_stock'] == true,

      isOutOfStock:
          json['is_out_of_stock'] == true,

      isLowStock:
          json['is_low_stock'] == true,

      stockStatus:
          json['stock_status']?.toString() ??
              '',

      stockLabel:
          json['stock_label']?.toString() ??
              '',

      /*
      |--------------------------------------------------------------------------
      | Product
      |--------------------------------------------------------------------------
      */

      productId: toInt(
        json['product_id'],
      ),

      /*
      |--------------------------------------------------------------------------
      | Status
      |--------------------------------------------------------------------------
      */

      isDefault:
          json['is_default'] == true,

      isActive:
          json['is_active'] == true,

      isPublished:
          json['is_published'] == true,

      canBePurchased:
          json['can_be_purchased'] == true,

      /*
      |--------------------------------------------------------------------------
      | Option Values
      |--------------------------------------------------------------------------
      */

      optionValues:
          (json['option_values'] as List?)
                  ?.map(
                    (e) =>
                        SkuOptionValue.fromJson(
                      Map<String, dynamic>.from(
                        e,
                      ),
                    ),
                  )
                  .toList() ??
              [],
    );
  }
}

class SkuOptionValue {
  final int id;

  final String name;

  final String? option;

  const SkuOptionValue({
    required this.id,
    required this.name,
    required this.option,
  });

  factory SkuOptionValue.fromJson(
    Map<String, dynamic> json,
  ) {
    return SkuOptionValue(
      id: json['id'] is int
          ? json['id']
          : int.tryParse(
                  json['id'].toString(),
                ) ??
              0,

      name:
          json['name']?.toString() ?? '',

      option:
          json['option']?.toString(),
    );
  }
}