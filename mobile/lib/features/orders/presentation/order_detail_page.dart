import 'package:flutter/material.dart';

import '../data/models/order_model.dart';
import '../data/order_repository.dart';

import 'widgets/order_payment_card.dart';
import 'widgets/order_product_card.dart';
import 'widgets/order_shipping_card.dart';
import 'widgets/order_status_card.dart';
import 'widgets/order_timeline_card.dart';

class OrderDetailPage extends StatefulWidget {
  const OrderDetailPage({
    super.key,
    required this.orderNumber,
  });

  final String orderNumber;

  @override
  State<OrderDetailPage> createState() =>
      _OrderDetailPageState();
}

class _OrderDetailPageState
    extends State<OrderDetailPage> {
  final OrderRepository _repository =
      OrderRepository();

  OrderModel? _order;

  bool _isLoading = true;

  @override
  void initState() {
    super.initState();

    _loadOrder();
  }

  Future<void> _loadOrder() async {
    if (mounted) {
      setState(() {
        _isLoading = true;
      });
    }

    try {
      final order =
          await _repository.findByOrderNumber(
        widget.orderNumber,
      );

      if (!mounted) return;

      setState(() {
        _order = order;
        _isLoading = false;
      });
    } catch (_) {
      if (!mounted) return;

      setState(() {
        _order = null;
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoading) {
      return const Scaffold(
        body: Center(
          child:
              CircularProgressIndicator(),
        ),
      );
    }

    if (_order == null) {
      return Scaffold(
        appBar: AppBar(
          title: const Text(
            'Detail Pesanan',
          ),
        ),
        body: Center(
          child: Padding(
            padding:
                const EdgeInsets.all(
              24,
            ),
            child: Column(
              mainAxisAlignment:
                  MainAxisAlignment.center,
              children: [
                const Icon(
                  Icons.receipt_long,
                  size: 72,
                  color: Colors.grey,
                ),

                const SizedBox(
                  height: 16,
                ),

                const Text(
                  'Pesanan tidak ditemukan',
                  textAlign:
                      TextAlign.center,
                  style: TextStyle(
                    fontSize: 18,
                    fontWeight:
                        FontWeight.w600,
                  ),
                ),

                const SizedBox(
                  height: 8,
                ),

                const Text(
                  'Data pesanan mungkin telah dihapus.',
                  textAlign:
                      TextAlign.center,
                  style: TextStyle(
                    color: Colors.grey,
                  ),
                ),

                const SizedBox(
                  height: 24,
                ),

                ElevatedButton(
                  onPressed:
                      _loadOrder,
                  child: const Text(
                    'Coba Lagi',
                  ),
                ),
              ],
            ),
          ),
        ),
      );
    }

    final order = _order!;

    return Scaffold(
      backgroundColor:
          const Color(0xFFF5F5F5),

      appBar: AppBar(
        elevation: 0,
        backgroundColor:
            Colors.white,
        foregroundColor:
            Colors.black,
        centerTitle: true,
        title: const Text(
          'Detail Pesanan',
        ),
      ),

      body: RefreshIndicator(
        onRefresh: _loadOrder,
        child: ListView(
          physics:
              const AlwaysScrollableScrollPhysics(),
          padding:
              const EdgeInsets.symmetric(
            vertical: 16,
          ),
          children: [
            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            OrderStatusCard(
              order: order,
            ),

            const SizedBox(
              height: 12,
            ),

            /*
            |--------------------------------------------------------------------------
            | Product
            |--------------------------------------------------------------------------
            */

            OrderProductCard(
              order: order,
            ),

            const SizedBox(
              height: 12,
            ),

            /*
            |--------------------------------------------------------------------------
            | Payment
            |--------------------------------------------------------------------------
            */

            OrderPaymentCard(
              order: order,
            ),

            const SizedBox(
              height: 12,
            ),

            /*
            |--------------------------------------------------------------------------
            | Shipping
            |--------------------------------------------------------------------------
            */

            OrderShippingCard(
              order: order,
            ),

            const SizedBox(
              height: 12,
            ),

            /*
            |--------------------------------------------------------------------------
            | Timeline
            |--------------------------------------------------------------------------
            */

            OrderTimelineCard(
              order: order,
            ),

            const SizedBox(
              height: 24,
            ),
          ],
        ),
      ),
    );
  }
}