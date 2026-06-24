import 'package:flutter/material.dart';

import '../../data/models/order_model.dart';

class OrderPaymentCard extends StatelessWidget {
  const OrderPaymentCard({
    super.key,
    required this.order,
  });

  final OrderModel order;

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.symmetric(
        horizontal: 16,
      ),
      padding: const EdgeInsets.all(
        16,
      ),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius:
            BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(
              0.04,
            ),
            blurRadius: 8,
            offset: const Offset(
              0,
              2,
            ),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment:
            CrossAxisAlignment.start,
        children: [
          const Text(
            'Metode Pembayaran',
            style: TextStyle(
              fontSize: 16,
              fontWeight:
                  FontWeight.bold,
            ),
          ),

          const SizedBox(height: 16),

          Row(
            children: [
              Container(
                width: 48,
                height: 48,
                decoration: BoxDecoration(
                  color: const Color(
                    0xFFD5001C,
                  ).withOpacity(0.1),
                  borderRadius:
                      BorderRadius.circular(
                    12,
                  ),
                ),
                child: Icon(
                  _paymentIcon(),
                  color: const Color(
                    0xFFD5001C,
                  ),
                ),
              ),

              const SizedBox(width: 16),

              Expanded(
                child: Column(
                  crossAxisAlignment:
                      CrossAxisAlignment
                          .start,
                  children: [
                    Text(
                      order.paymentMethod,
                      style:
                          const TextStyle(
                        fontSize: 15,
                        fontWeight:
                            FontWeight.w700,
                      ),
                    ),

                    const SizedBox(
                      height: 4,
                    ),

                    Text(
                      _paymentDescription(),
                      style: TextStyle(
                        fontSize: 13,
                        color: Colors
                            .grey
                            .shade600,
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  IconData _paymentIcon() {
    final payment =
        order.paymentMethod.toLowerCase();

    if (payment.contains(
      'bca',
    )) {
      return Icons.account_balance;
    }

    if (payment.contains(
      'bni',
    )) {
      return Icons.account_balance;
    }

    if (payment.contains(
      'bri',
    )) {
      return Icons.account_balance;
    }

    if (payment.contains(
      'mandiri',
    )) {
      return Icons.account_balance;
    }

    if (payment.contains(
      'gopay',
    )) {
      return Icons.account_balance_wallet;
    }

    if (payment.contains(
      'ovo',
    )) {
      return Icons.account_balance_wallet;
    }

    if (payment.contains(
      'dana',
    )) {
      return Icons.account_balance_wallet;
    }

    if (payment.contains(
      'cod',
    )) {
      return Icons.local_shipping;
    }

    return Icons.credit_card;
  }

  String _paymentDescription() {
    final payment =
        order.paymentMethod.toLowerCase();

    if (payment.contains(
      'cod',
    )) {
      return 'Bayar saat pesanan diterima';
    }

    return 'Metode pembayaran yang dipilih pelanggan';
  }
}