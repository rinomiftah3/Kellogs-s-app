import 'package:flutter/material.dart';

import '../../data/models/checkout_summary_model.dart';

class CheckoutBottomBar extends StatelessWidget {
  const CheckoutBottomBar({
    super.key,
    required this.summary,
    required this.onCheckout,
    this.isLoading = false,
  });

  final CheckoutSummaryModel summary;

  final VoidCallback onCheckout;

  final bool isLoading;

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: const BoxDecoration(
        color: Colors.white,
        boxShadow: [
          BoxShadow(
            color: Color(0x14000000),
            blurRadius: 12,
            offset: Offset(
              0,
              -2,
            ),
          ),
        ],
      ),
      child: SafeArea(
        top: false,
        child: Padding(
          padding: const EdgeInsets.all(
            16,
          ),
          child: Row(
            children: [
              /*
              |--------------------------------------------------------------------------
              | Total Information
              |--------------------------------------------------------------------------
              */

              Expanded(
                child: Column(
                  mainAxisSize:
                      MainAxisSize.min,
                  crossAxisAlignment:
                      CrossAxisAlignment.start,
                  children: [
                    Text(
                      '${summary.totalQuantity} item',
                      style: TextStyle(
                        fontSize: 12,
                        color: Colors
                            .grey
                            .shade600,
                      ),
                    ),

                    const SizedBox(
                      height: 4,
                    ),

                    const Text(
                      'Total Pembayaran',
                      style: TextStyle(
                        fontSize: 13,
                        color: Colors.grey,
                      ),
                    ),

                    const SizedBox(
                      height: 2,
                    ),

                    Text(
                      summary
                          .totalFormatted,
                      style:
                          const TextStyle(
                        fontSize: 22,
                        fontWeight:
                            FontWeight
                                .bold,
                        color: Color(
                          0xFFD5001C,
                        ),
                      ),
                    ),
                  ],
                ),
              ),

              const SizedBox(
                width: 16,
              ),

              /*
              |--------------------------------------------------------------------------
              | Checkout Button
              |--------------------------------------------------------------------------
              */

              SizedBox(
                height: 52,
                child: ElevatedButton(
                  onPressed:
                      isLoading
                          ? null
                          : onCheckout,
                  style:
                      ElevatedButton
                          .styleFrom(
                    backgroundColor:
                        const Color(
                      0xFFD5001C,
                    ),
                    foregroundColor:
                        Colors.white,
                    elevation: 0,
                    padding:
                        const EdgeInsets
                            .symmetric(
                      horizontal: 28,
                    ),
                    shape:
                        RoundedRectangleBorder(
                      borderRadius:
                          BorderRadius
                              .circular(
                        14,
                      ),
                    ),
                  ),
                  child: isLoading
                      ? const SizedBox(
                          width: 22,
                          height: 22,
                          child:
                              CircularProgressIndicator(
                            strokeWidth:
                                2,
                            valueColor:
                                AlwaysStoppedAnimation(
                              Colors.white,
                            ),
                          ),
                        )
                      : const Text(
                          'Buat Pesanan',
                          style:
                              TextStyle(
                            fontSize: 15,
                            fontWeight:
                                FontWeight
                                    .bold,
                          ),
                        ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}