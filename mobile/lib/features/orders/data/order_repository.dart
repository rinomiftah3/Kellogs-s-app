import 'dart:math';

import '../../cart/data/cart_repository.dart';
import '../../cart/data/models/cart_item_model.dart';

import '../../orders/data/order_repository.dart';
import '../../orders/data/models/order_model.dart';
import '../../orders/data/models/order_timeline_model.dart';

import 'models/checkout_item_model.dart';
import 'models/checkout_summary_model.dart';
import 'models/payment_method_model.dart';

class CheckoutRepository {
  final CartRepository _cartRepository =
      CartRepository();

  final OrderRepository _orderRepository =
      OrderRepository();

  /*
  |--------------------------------------------------------------------------
  | Checkout Items
  |--------------------------------------------------------------------------
  */

  Future<List<CheckoutItemModel>>
      getItems() async {
    final cartItems =
        await _cartRepository.getItems();

    return cartItems
        .map(_mapCartItem)
        .toList();
  }

  /*
  |--------------------------------------------------------------------------
  | Checkout Summary
  |--------------------------------------------------------------------------
  */

  Future<CheckoutSummaryModel>
      getSummary() async {
    final cartItems =
        await _cartRepository.getItems();

    final totalItems =
        cartItems.length;

    final totalQuantity =
        cartItems.fold<int>(
      0,
      (sum, item) =>
          sum + item.quantity,
    );

    final subtotal =
        cartItems.fold<double>(
      0,
      (sum, item) =>
          sum +
          (item.price * item.quantity),
    );

    const shippingCost = 0.0;

    const discount = 0.0;

    final total =
        subtotal +
        shippingCost -
        discount;

    return CheckoutSummaryModel(
      totalItems: totalItems,
      totalQuantity:
          totalQuantity,
      subtotal: subtotal,
      shippingCost:
          shippingCost,
      discount: discount,
      total: total,
    );
  }

  /*
  |--------------------------------------------------------------------------
  | Payment Methods
  |--------------------------------------------------------------------------
  */

  Future<List<PaymentMethodModel>>
      getPaymentMethods() async {
    return PaymentMethodModel
        .defaults();
  }

  /*
  |--------------------------------------------------------------------------
  | Create Order
  |--------------------------------------------------------------------------
  */

  Future<void> createOrder({
    required String paymentMethodId,
  }) async {
    final cartItems =
        await _cartRepository.getItems();

    if (cartItems.isEmpty) {
      return;
    }

    final summary =
        await getSummary();

    final payments =
        await getPaymentMethods();

    final selectedPayment =
        payments.firstWhere(
      (e) => e.id == paymentMethodId,
      orElse: () => payments.first,
    );

    /*
    |--------------------------------------------------------------------------
    | Simulasi Request Backend
    |--------------------------------------------------------------------------
    */

    await Future.delayed(
      const Duration(
        seconds: 2,
      ),
    );

    final firstItem =
        cartItems.first;

    final now = DateTime.now();

    final random = Random();

    final orderNumber =
        'KLG-${random.nextInt(900000) + 100000}';

    final order = OrderModel(
      /*
      |--------------------------------------------------------------------------
      | Identity
      |--------------------------------------------------------------------------
      */

      id: now
          .millisecondsSinceEpoch
          .toString(),

      orderNumber: orderNumber,

      /*
      |--------------------------------------------------------------------------
      | Status
      |--------------------------------------------------------------------------
      */

      status: 'pending',

      statusLabel:
          'Menunggu Pembayaran',

      /*
      |--------------------------------------------------------------------------
      | Product
      |--------------------------------------------------------------------------
      */

      productName:
          firstItem.productName,

      image: firstItem.image,

      quantity:
          summary.totalQuantity,

      /*
      |--------------------------------------------------------------------------
      | Payment
      |--------------------------------------------------------------------------
      */

      paymentMethod:
          selectedPayment.name,

      /*
      |--------------------------------------------------------------------------
      | Shipping
      |--------------------------------------------------------------------------
      */

      /*
|--------------------------------------------------------------------------
| Shipping
|--------------------------------------------------------------------------
*/

final AddressModel? defaultAddress =
    await _addressRepository
        .getDefaultAddress();
      /*
      |--------------------------------------------------------------------------
      | Timeline
      |--------------------------------------------------------------------------
      */

      timelines: [
        OrderTimelineModel(
          title:
              'Pesanan Dibuat',
          description:
              'Pesanan berhasil dibuat',
          dateTime:
              now.toIso8601String(),
          isCompleted: true,
        ),

        const OrderTimelineModel(
          title: 'Pembayaran',
          description:
              'Menunggu pembayaran',
          dateTime: '',
          isCompleted: false,
        ),

        const OrderTimelineModel(
          title: 'Diproses',
          description:
              'Pesanan sedang diproses',
          dateTime: '',
          isCompleted: false,
        ),

        const OrderTimelineModel(
          title: 'Dikirim',
          description:
              'Pesanan sedang dikirim',
          dateTime: '',
          isCompleted: false,
        ),

        const OrderTimelineModel(
          title: 'Selesai',
          description:
              'Pesanan telah diterima',
          dateTime: '',
          isCompleted: false,
        ),
      ],

      /*
      |--------------------------------------------------------------------------
      | Amount
      |--------------------------------------------------------------------------
      */

      total: summary.total,

      totalFormatted:
          summary.totalFormatted,

      /*
      |--------------------------------------------------------------------------
      | Metadata
      |--------------------------------------------------------------------------
      */

      createdAt:
          now.toIso8601String(),
    );

    /*
    |--------------------------------------------------------------------------
    | Persist Order
    |--------------------------------------------------------------------------
    */

    await _orderRepository.addOrder(
      order,
    );
  }

  /*
  |--------------------------------------------------------------------------
  | Clear Cart
  |--------------------------------------------------------------------------
  */

  Future<void> clearCart() async {
    await _cartRepository.clear();
  }

  /*
  |--------------------------------------------------------------------------
  | Mapper
  |--------------------------------------------------------------------------
  */

  CheckoutItemModel _mapCartItem(
    CartItemModel item,
  ) {
    return CheckoutItemModel(
      productId: item.productId,
      skuId: item.skuId,
      productName:
          item.productName,
      sku: item.sku,
      image: item.image,
      price: item.price,
      priceFormatted:
          item.priceFormatted,
      quantity: item.quantity,
    );
  }
}