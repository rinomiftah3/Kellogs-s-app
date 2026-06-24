import 'package:flutter/material.dart';

import '../../data/models/order_model.dart';

class OrderProductCard extends StatelessWidget {
  const OrderProductCard({
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
            'Produk Pesanan',
            style: TextStyle(
              fontSize: 16,
              fontWeight:
                  FontWeight.bold,
            ),
          ),

          const SizedBox(height: 16),

          Row(
            crossAxisAlignment:
                CrossAxisAlignment.start,
            children: [
              ClipRRect(
                borderRadius:
                    BorderRadius.circular(
                  12,
                ),
                child: order.hasImage
                    ? Image.network(
                        order.image,
                        width: 80,
                        height: 80,
                        fit: BoxFit.cover,
                        errorBuilder: (
                          context,
                          error,
                          stackTrace,
                        ) {
                          return _buildPlaceholder();
                        },
                      )
                    : _buildPlaceholder(),
              ),

              const SizedBox(width: 16),

              Expanded(
                child: Column(
                  crossAxisAlignment:
                      CrossAxisAlignment
                          .start,
                  children: [
                    Text(
                      order.productName,
                      maxLines: 2,
                      overflow:
                          TextOverflow
                              .ellipsis,
                      style:
                          const TextStyle(
                        fontSize: 15,
                        fontWeight:
                            FontWeight.w700,
                      ),
                    ),

                    const SizedBox(
                      height: 8,
                    ),

                    Text(
                      'Jumlah: ${order.quantity}',
                      style:
                          TextStyle(
                        fontSize: 13,
                        color: Colors
                            .grey
                            .shade700,
                      ),
                    ),

                    const SizedBox(
                      height: 8,
                    ),

                    Text(
                      order.totalFormatted,
                      style:
                          const TextStyle(
                        fontSize: 18,
                        fontWeight:
                            FontWeight.bold,
                        color: Color(
                          0xFFD5001C,
                        ),
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

  Widget _buildPlaceholder() {
    return Container(
      width: 80,
      height: 80,
      color: const Color(
        0xFFF3F4F6,
      ),
      child: const Icon(
        Icons.breakfast_dining,
        size: 40,
        color: Color(
          0xFFD5001C,
        ),
      ),
    );
  }
}