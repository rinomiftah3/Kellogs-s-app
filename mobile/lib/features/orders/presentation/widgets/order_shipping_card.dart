import 'package:flutter/material.dart';

import '../../data/models/order_model.dart';

class OrderShippingCard extends StatelessWidget {
  const OrderShippingCard({
    super.key,
    required this.order,
  });

  final OrderModel order;

  @override
  Widget build(BuildContext context) {
    final addressLines =
        order.shippingAddress
            .split('\n')
            .where(
              (e) => e.trim().isNotEmpty,
            )
            .toList();

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
            'Alamat Pengiriman',
            style: TextStyle(
              fontSize: 16,
              fontWeight:
                  FontWeight.bold,
            ),
          ),

          const SizedBox(
            height: 16,
          ),

          Row(
            crossAxisAlignment:
                CrossAxisAlignment.start,
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
                child: const Icon(
                  Icons.location_on,
                  color: Color(
                    0xFFD5001C,
                  ),
                ),
              ),

              const SizedBox(
                width: 16,
              ),

              Expanded(
                child: addressLines.isEmpty
                    ? Text(
                        'Alamat belum tersedia',
                        style: TextStyle(
                          color: Colors
                              .grey
                              .shade600,
                        ),
                      )
                    : Column(
                        crossAxisAlignment:
                            CrossAxisAlignment
                                .start,
                        children: [
                          Text(
                            addressLines.first,
                            style:
                                const TextStyle(
                              fontSize: 15,
                              fontWeight:
                                  FontWeight
                                      .w700,
                            ),
                          ),

                          const SizedBox(
                            height: 8,
                          ),

                          ...addressLines
                              .skip(1)
                              .map(
                                (line) =>
                                    Padding(
                                  padding:
                                      const EdgeInsets.only(
                                    bottom:
                                        4,
                                  ),
                                  child: Text(
                                    line,
                                    style:
                                        TextStyle(
                                      fontSize:
                                          13,
                                      color: Colors
                                          .grey
                                          .shade700,
                                    ),
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
}