class CartItemModel {
  final int productId;

  final int skuId;

  final String productName;

  final String image;

  final String sku;

  final double price;

  final String priceFormatted;

  final int quantity;

  const CartItemModel({
    required this.productId,
    required this.skuId,
    required this.productName,
    required this.image,
    required this.sku,
    required this.price,
    required this.priceFormatted,
    required this.quantity,
  });

  /*
  |--------------------------------------------------------------------------
  | JSON
  |--------------------------------------------------------------------------
  */

  factory CartItemModel.fromJson(
    Map<String, dynamic> json,
  ) {
    return CartItemModel(
      productId:
          json['product_id'] ?? 0,

      skuId:
          json['sku_id'] ?? 0,

      productName:
          json['product_name'] ?? '',

      image:
          json['image'] ?? '',

      sku:
          json['sku'] ?? '',

      price:
          (json['price'] ?? 0)
              .toDouble(),

      priceFormatted:
          json['price_formatted'] ?? '',

      quantity:
          json['quantity'] ?? 1,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'product_id': productId,
      'sku_id': skuId,
      'product_name': productName,
      'image': image,
      'sku': sku,
      'price': price,
      'price_formatted':
          priceFormatted,
      'quantity': quantity,
    };
  }

  /*
  |--------------------------------------------------------------------------
  | Copy With
  |--------------------------------------------------------------------------
  */

  CartItemModel copyWith({
    int? productId,
    int? skuId,
    String? productName,
    String? image,
    String? sku,
    double? price,
    String? priceFormatted,
    int? quantity,
  }) {
    return CartItemModel(
      productId:
          productId ?? this.productId,

      skuId:
          skuId ?? this.skuId,

      productName:
          productName ??
              this.productName,

      image:
          image ?? this.image,

      sku:
          sku ?? this.sku,

      price:
          price ?? this.price,

      priceFormatted:
          priceFormatted ??
              this.priceFormatted,

      quantity:
          quantity ?? this.quantity,
    );
  }

  /*
  |--------------------------------------------------------------------------
  | Helpers
  |--------------------------------------------------------------------------
  */

  double get subtotal =>
      price * quantity;

  String get subtotalFormatted {
    return 'Rp ${subtotal.toStringAsFixed(0)}';
  }

  bool get hasImage =>
      image.isNotEmpty;

  bool get canDecrease =>
      quantity > 1;

  bool get isSingleQuantity =>
      quantity == 1;
}