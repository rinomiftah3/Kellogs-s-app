class CheckoutSummaryModel {
  final int totalItems;

  final int totalQuantity;

  final double subtotal;

  final double shippingCost;

  final double discount;

  final double total;

  const CheckoutSummaryModel({
    required this.totalItems,
    required this.totalQuantity,
    required this.subtotal,
    required this.shippingCost,
    required this.discount,
    required this.total,
  });

  /*
  |--------------------------------------------------------------------------
  | Formatter
  |--------------------------------------------------------------------------
  */

  String _format(
    double value,
  ) {
    return 'Rp ${value.toStringAsFixed(0)}';
  }

  String get subtotalFormatted =>
      _format(subtotal);

  String get shippingCostFormatted =>
      _format(shippingCost);

  String get discountFormatted =>
      _format(discount);

  String get totalFormatted =>
      _format(total);

  /*
  |--------------------------------------------------------------------------
  | JSON
  |--------------------------------------------------------------------------
  */

  factory CheckoutSummaryModel.fromJson(
    Map<String, dynamic> json,
  ) {
    return CheckoutSummaryModel(
      totalItems:
          json['total_items'] ?? 0,

      totalQuantity:
          json['total_quantity'] ?? 0,

      subtotal:
          (json['subtotal'] ?? 0)
              .toDouble(),

      shippingCost:
          (json['shipping_cost'] ?? 0)
              .toDouble(),

      discount:
          (json['discount'] ?? 0)
              .toDouble(),

      total:
          (json['total'] ?? 0)
              .toDouble(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'total_items': totalItems,
      'total_quantity':
          totalQuantity,
      'subtotal': subtotal,
      'shipping_cost':
          shippingCost,
      'discount': discount,
      'total': total,
    };
  }
}