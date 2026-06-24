import 'order_timeline_model.dart';

class OrderModel {
  final String id;

  final String orderNumber;

  /*
  |--------------------------------------------------------------------------
  | Status
  |--------------------------------------------------------------------------
  */

  final String status;

  final String statusLabel;

  /*
  |--------------------------------------------------------------------------
  | Product
  |--------------------------------------------------------------------------
  */

  final String productName;

  final String image;

  final int quantity;

  /*
  |--------------------------------------------------------------------------
  | Payment
  |--------------------------------------------------------------------------
  */

  final String paymentMethod;

  /*
  |--------------------------------------------------------------------------
  | Shipping
  |--------------------------------------------------------------------------
  */

  final String shippingAddress;

  /*
  |--------------------------------------------------------------------------
  | Timeline
  |--------------------------------------------------------------------------
  */

  final List<OrderTimelineModel> timelines;

  /*
  |--------------------------------------------------------------------------
  | Amount
  |--------------------------------------------------------------------------
  */

  final double total;

  final String totalFormatted;

  /*
  |--------------------------------------------------------------------------
  | Metadata
  |--------------------------------------------------------------------------
  */

  final String createdAt;

  const OrderModel({
    required this.id,
    required this.orderNumber,

    required this.status,
    required this.statusLabel,

    required this.productName,
    required this.image,
    required this.quantity,

    required this.paymentMethod,
    required this.shippingAddress,
    required this.timelines,

    required this.total,
    required this.totalFormatted,

    required this.createdAt,
  });

  /*
  |--------------------------------------------------------------------------
  | Status Helpers
  |--------------------------------------------------------------------------
  */

  bool get isPending =>
      status == 'pending';

  bool get isProcessing =>
      status == 'processing';

  bool get isShipped =>
      status == 'shipped';

  bool get isCompleted =>
      status == 'completed';

  bool get isCancelled =>
      status == 'cancelled';

  /*
  |--------------------------------------------------------------------------
  | UI Helpers
  |--------------------------------------------------------------------------
  */

  bool get hasImage =>
      image.isNotEmpty;

  String get displayTitle =>
      productName;

  factory OrderModel.fromJson(
    Map<String, dynamic> json,
  ) {
    return OrderModel(
      id: json['id'] ?? '',

      orderNumber:
          json['order_number'] ?? '',

      /*
      |--------------------------------------------------------------------------
      | Status
      |--------------------------------------------------------------------------
      */

      status:
          json['status'] ?? '',

      statusLabel:
          json['status_label'] ?? '',

      /*
      |--------------------------------------------------------------------------
      | Product
      |--------------------------------------------------------------------------
      */

      productName:
          json['product_name'] ?? '',

      image:
          json['image'] ?? '',

      quantity:
          json['quantity'] ?? 0,

      /*
      |--------------------------------------------------------------------------
      | Payment
      |--------------------------------------------------------------------------
      */

      paymentMethod:
          json['payment_method'] ??
              'Belum tersedia',

      /*
      |--------------------------------------------------------------------------
      | Shipping
      |--------------------------------------------------------------------------
      */

      shippingAddress:
          json['shipping_address'] ??
              'Alamat belum tersedia',

      /*
      |--------------------------------------------------------------------------
      | Timeline
      |--------------------------------------------------------------------------
      */

      timelines:
          (json['timelines'] as List?)
                  ?.map(
                    (e) =>
                        OrderTimelineModel
                            .fromJson(
                      Map<String, dynamic>.from(
                        e,
                      ),
                    ),
                  )
                  .toList() ??
              [
                const OrderTimelineModel(
                  title:
                      'Pesanan Dibuat',
                  description:
                      'Pesanan berhasil dibuat',
                  dateTime: '',
                  isCompleted:
                      true,
                ),
              ],

      /*
      |--------------------------------------------------------------------------
      | Amount
      |--------------------------------------------------------------------------
      */

      total:
          (json['total'] ?? 0)
              .toDouble(),

      totalFormatted:
          json['total_formatted'] ?? '',

      /*
      |--------------------------------------------------------------------------
      | Metadata
      |--------------------------------------------------------------------------
      */

      createdAt:
          json['created_at'] ?? '',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,

      'order_number':
          orderNumber,

      /*
      |--------------------------------------------------------------------------
      | Status
      |--------------------------------------------------------------------------
      */

      'status': status,

      'status_label':
          statusLabel,

      /*
      |--------------------------------------------------------------------------
      | Product
      |--------------------------------------------------------------------------
      */

      'product_name':
          productName,

      'image': image,

      'quantity': quantity,

      /*
      |--------------------------------------------------------------------------
      | Payment
      |--------------------------------------------------------------------------
      */

      'payment_method':
          paymentMethod,

      /*
      |--------------------------------------------------------------------------
      | Shipping
      |--------------------------------------------------------------------------
      */

      'shipping_address':
          shippingAddress,

      /*
      |--------------------------------------------------------------------------
      | Timeline
      |--------------------------------------------------------------------------
      */

      'timelines': timelines
          .map(
            (e) => e.toJson(),
          )
          .toList(),

      /*
      |--------------------------------------------------------------------------
      | Amount
      |--------------------------------------------------------------------------
      */

      'total': total,

      'total_formatted':
          totalFormatted,

      /*
      |--------------------------------------------------------------------------
      | Metadata
      |--------------------------------------------------------------------------
      */

      'created_at':
          createdAt,
    };
  }

  /*
  |--------------------------------------------------------------------------
  | Copy With
  |--------------------------------------------------------------------------
  */

  OrderModel copyWith({
    String? id,
    String? orderNumber,
    String? status,
    String? statusLabel,
    String? productName,
    String? image,
    int? quantity,
    String? paymentMethod,
    String? shippingAddress,
    List<OrderTimelineModel>?
        timelines,
    double? total,
    String? totalFormatted,
    String? createdAt,
  }) {
    return OrderModel(
      id: id ?? this.id,

      orderNumber:
          orderNumber ??
              this.orderNumber,

      status:
          status ?? this.status,

      statusLabel:
          statusLabel ??
              this.statusLabel,

      productName:
          productName ??
              this.productName,

      image:
          image ?? this.image,

      quantity:
          quantity ??
              this.quantity,

      paymentMethod:
          paymentMethod ??
              this.paymentMethod,

      shippingAddress:
          shippingAddress ??
              this.shippingAddress,

      timelines:
          timelines ??
              this.timelines,

      total:
          total ?? this.total,

      totalFormatted:
          totalFormatted ??
              this.totalFormatted,

      createdAt:
          createdAt ??
              this.createdAt,
    );
  }
}