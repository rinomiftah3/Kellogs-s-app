import 'package:flutter/material.dart';

import '../../data/models/order_model.dart';

class OrderCard extends StatelessWidget {
  const OrderCard({
    super.key,
    required this.order,
    this.onTap,
    this.onAction,
  });

  final OrderModel order;

  final VoidCallback? onTap;

  final VoidCallback? onAction;

  @override
  Widget build(BuildContext context) {
    return InkWell(
      borderRadius:
          BorderRadius.circular(16),
      onTap: onTap,
      child: Container(
        margin:
            const EdgeInsets.only(
          bottom: 12,
        ),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius:
              BorderRadius.circular(
            16,
          ),
          boxShadow: [
            BoxShadow(
              color: Colors.black
                  .withOpacity(0.04),
              blurRadius: 8,
              offset: const Offset(
                0,
                2,
              ),
            ),
          ],
        ),
        child: Column(
          children: [
            /*
            |--------------------------------------------------------------------------
            | Header
            |--------------------------------------------------------------------------
            */

            Padding(
              padding:
                  const EdgeInsets.all(
                16,
              ),
              child: Row(
                children: [
                  const Icon(
                    Icons.receipt_long,
                    size: 18,
                    color: Color(
                      0xFFD5001C,
                    ),
                  ),

                  const SizedBox(
                    width: 8,
                  ),

                  Expanded(
                    child: Text(
                      order.orderNumber,
                      style:
                          const TextStyle(
                        fontWeight:
                            FontWeight
                                .bold,
                      ),
                    ),
                  ),

                  _buildStatusChip(),
                ],
              ),
            ),

            const Divider(
              height: 1,
            ),

            /*
            |--------------------------------------------------------------------------
            | Product
            |--------------------------------------------------------------------------
            */

            Padding(
              padding:
                  const EdgeInsets.all(
                16,
              ),
              child: Row(
                crossAxisAlignment:
                    CrossAxisAlignment
                        .start,
                children: [
                  ClipRRect(
                    borderRadius:
                        BorderRadius
                            .circular(
                      12,
                    ),
                    child:
                        order.image
                                .isNotEmpty
                            ? Image.network(
                                order.image,
                                width: 80,
                                height: 80,
                                fit: BoxFit
                                    .cover,
                                errorBuilder:
                                    (
                                  context,
                                  error,
                                  stackTrace,
                                ) {
                                  return _placeholder();
                                },
                              )
                            : _placeholder(),
                  ),

                  const SizedBox(
                    width: 12,
                  ),

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
                            fontWeight:
                                FontWeight
                                    .w700,
                            fontSize:
                                15,
                          ),
                        ),

                        const SizedBox(
                          height: 8,
                        ),

                        Text(
                          '${order.quantity} item',
                          style:
                              TextStyle(
                            color: Colors
                                .grey
                                .shade600,
                          ),
                        ),

                        const SizedBox(
                          height: 8,
                        ),

                        Text(
                          order.createdAt,
                          style:
                              TextStyle(
                            fontSize: 12,
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
            ),

            const Divider(
              height: 1,
            ),

            /*
            |--------------------------------------------------------------------------
            | Footer
            |--------------------------------------------------------------------------
            */

            Padding(
              padding:
                  const EdgeInsets.all(
                16,
              ),
              child: Column(
                children: [
                  Row(
                    children: [
                      const Spacer(),

                      const Text(
                        'Total:',
                      ),

                      const SizedBox(
                        width: 8,
                      ),

                      Text(
                        order
                            .totalFormatted,
                        style:
                            const TextStyle(
                          color: Color(
                            0xFFD5001C,
                          ),
                          fontWeight:
                              FontWeight
                                  .bold,
                          fontSize:
                              16,
                        ),
                      ),
                    ],
                  ),

                  const SizedBox(
                    height: 12,
                  ),

                  Align(
                    alignment:
                        Alignment
                            .centerRight,
                    child:
                        OutlinedButton(
                      onPressed:
                          onAction,
                      style:
                          OutlinedButton.styleFrom(
                        foregroundColor:
                            const Color(
                          0xFFD5001C,
                        ),
                        side:
                            const BorderSide(
                          color: Color(
                            0xFFD5001C,
                          ),
                        ),
                        shape:
                            RoundedRectangleBorder(
                          borderRadius:
                              BorderRadius.circular(
                            12,
                          ),
                        ),
                      ),
                      child: Text(
                        _actionLabel(),
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  /*
  |--------------------------------------------------------------------------
  | Status Badge
  |--------------------------------------------------------------------------
  */

  Widget _buildStatusChip() {
    Color color;

    if (order.isPending) {
      color = Colors.orange;
    } else if (order.isProcessing) {
      color = Colors.blue;
    } else if (order.isShipped) {
      color = Colors.purple;
    } else if (order.isCompleted) {
      color = Colors.green;
    } else {
      color = Colors.red;
    }

    return Container(
      padding:
          const EdgeInsets.symmetric(
        horizontal: 10,
        vertical: 6,
      ),
      decoration: BoxDecoration(
        color:
            color.withOpacity(0.12),
        borderRadius:
            BorderRadius.circular(
          20,
        ),
      ),
      child: Text(
        order.statusLabel,
        style: TextStyle(
          color: color,
          fontWeight:
              FontWeight.bold,
          fontSize: 12,
        ),
      ),
    );
  }

  /*
  |--------------------------------------------------------------------------
  | Dynamic Action
  |--------------------------------------------------------------------------
  */

  String _actionLabel() {
    if (order.isPending) {
      return 'Bayar';
    }

    if (order.isProcessing) {
      return 'Lihat Detail';
    }

    if (order.isShipped) {
      return 'Lacak';
    }

    if (order.isCompleted) {
      return 'Beli Lagi';
    }

    return 'Lihat';
  }

  /*
  |--------------------------------------------------------------------------
  | Placeholder
  |--------------------------------------------------------------------------
  */

  Widget _placeholder() {
    return Container(
      width: 80,
      height: 80,
      color: const Color(
        0xFFF3F4F6,
      ),
      child: const Icon(
        Icons.breakfast_dining,
        color: Color(
          0xFFD5001C,
        ),
        size: 36,
      ),
    );
  }
}