import 'package:flutter/material.dart';

import '../../data/models/order_model.dart';

class OrderStatusCard extends StatelessWidget {
  const OrderStatusCard({
    super.key,
    required this.order,
  });

  final OrderModel order;

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.all(16),
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          colors: [
            Color(0xFFD5001C),
            Color(0xFFFF5A36),
          ],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius:
            BorderRadius.circular(20),
      ),
      child: Column(
        crossAxisAlignment:
            CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              CircleAvatar(
                radius: 24,
                backgroundColor:
                    Colors.white24,
                child: Icon(
                  _statusIcon(),
                  color: Colors.white,
                  size: 28,
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
                      order.statusLabel,
                      style:
                          const TextStyle(
                        color:
                            Colors.white,
                        fontSize: 20,
                        fontWeight:
                            FontWeight.bold,
                      ),
                    ),

                    const SizedBox(
                      height: 4,
                    ),

                    Text(
                      'Order #${order.orderNumber}',
                      style:
                          const TextStyle(
                        color:
                            Colors.white70,
                        fontSize: 14,
                      ),
                    ),

                    const SizedBox(
                      height: 2,
                    ),

                    Text(
                      order.createdAt,
                      style:
                          const TextStyle(
                        color:
                            Colors.white70,
                        fontSize: 12,
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),

          const SizedBox(height: 24),

          _buildProgress(),
        ],
      ),
    );
  }

  Widget _buildProgress() {
    final steps = [
      'Dibuat',
      'Bayar',
      'Proses',
      'Kirim',
      'Selesai',
    ];

    final current =
        _currentStepIndex();

    return Row(
      children: List.generate(
        steps.length,
        (index) {
          final completed =
              index <= current;

          return Expanded(
            child: Column(
              children: [
                Row(
                  children: [
                    Expanded(
                      child: Container(
                        height: 2,
                        color: index == 0
                            ? Colors
                                .transparent
                            : completed
                                ? Colors
                                    .white
                                : Colors
                                    .white24,
                      ),
                    ),

                    Container(
                      width: 24,
                      height: 24,
                      decoration:
                          BoxDecoration(
                        color: completed
                            ? Colors.white
                            : Colors.white24,
                        shape:
                            BoxShape.circle,
                      ),
                      child: Icon(
                        completed
                            ? Icons.check
                            : Icons.circle,
                        size: 14,
                        color: completed
                            ? const Color(
                                0xFFD5001C,
                              )
                            : Colors.white,
                      ),
                    ),

                    Expanded(
                      child: Container(
                        height: 2,
                        color: index ==
                                steps.length -
                                    1
                            ? Colors
                                .transparent
                            : index < current
                                ? Colors
                                    .white
                                : Colors
                                    .white24,
                      ),
                    ),
                  ],
                ),

                const SizedBox(height: 8),

                Text(
                  steps[index],
                  style:
                      const TextStyle(
                    color:
                        Colors.white,
                    fontSize: 11,
                  ),
                  textAlign:
                      TextAlign.center,
                ),
              ],
            ),
          );
        },
      ),
    );
  }

  int _currentStepIndex() {
    switch (order.status) {
      case 'pending':
        return 1;

      case 'processing':
        return 2;

      case 'shipped':
        return 3;

      case 'completed':
        return 4;

      case 'cancelled':
        return 0;

      default:
        return 0;
    }
  }

  IconData _statusIcon() {
    switch (order.status) {
      case 'pending':
        return Icons.schedule;

      case 'processing':
        return Icons.inventory_2;

      case 'shipped':
        return Icons.local_shipping;

      case 'completed':
        return Icons.check_circle;

      case 'cancelled':
        return Icons.cancel;

      default:
        return Icons.receipt_long;
    }
  }
}