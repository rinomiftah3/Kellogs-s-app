class CheckoutItemModel {
  final int productId;

  final int skuId;

  final String productName;

  final String sku;

  final String image;

  final double price;

  final String priceFormatted;

  final int quantity;

  const CheckoutItemModel({
    required this.productId,
    required this.skuId,
    required this.productName,
    required this.sku,
    required this.image,
    required this.price,
    required this.priceFormatted,
    required this.quantity,
  });

  double get subtotal =>
      price * quantity;

  String get subtotalFormatted {
    return 'Rp ${subtotal.toStringAsFixed(0)}';
  }

  factory CheckoutItemModel.fromJson(
    Map<String, dynamic> json,
  ) {
    return CheckoutItemModel(
      productId: json['product_id'] ?? 0,

      skuId: json['sku_id'] ?? 0,

      productName:
          json['product_name'] ?? '',

      sku: json['sku'] ?? '',

      image: json['image'] ?? '',

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
      'sku': sku,
      'image': image,
      'price': price,
      'price_formatted':
          priceFormatted,
      'quantity': quantity,
    };
  }
}