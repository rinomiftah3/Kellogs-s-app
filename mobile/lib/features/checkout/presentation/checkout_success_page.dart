import 'dart:math';

import 'package:flutter/material.dart';

class CheckoutSuccessPage extends StatelessWidget {
  const CheckoutSuccessPage({
    super.key,
  });

  String _generateOrderNumber() {
    final random = Random();

    final number =
        random.nextInt(900000) + 100000;

    return 'KLG-$number';
  }

  @override
  Widget build(BuildContext context) {
    final orderNumber =
        _generateOrderNumber();

    return WillPopScope(
      onWillPop: () async {
        Navigator.pushNamedAndRemoveUntil(
          context,
          '/',
          (route) => false,
        );

        return false;
      },
      child: Scaffold(
        backgroundColor: Colors.white,

        body: SafeArea(
          child: Padding(
            padding:
                const EdgeInsets.all(24),
            child: Column(
              children: [
                const Spacer(),

                /*
                |--------------------------------------------------------------------------
                | Success Icon
                |--------------------------------------------------------------------------
                */

                Container(
                  width: 120,
                  height: 120,
                  decoration: BoxDecoration(
                    color: const Color(
                      0xFFE8F5E9,
                    ),
                    shape: BoxShape.circle,
                  ),
                  child: const Icon(
                    Icons.check_circle,
                    size: 80,
                    color: Colors.green,
                  ),
                ),

                const SizedBox(
                  height: 32,
                ),

                /*
                |--------------------------------------------------------------------------
                | Title
                |--------------------------------------------------------------------------
                */

                const Text(
                  'Pesanan Berhasil Dibuat',
                  textAlign:
                      TextAlign.center,
                  style: TextStyle(
                    fontSize: 28,
                    fontWeight:
                        FontWeight.bold,
                  ),
                ),

                const SizedBox(
                  height: 12,
                ),

                const Text(
                  'Terima kasih telah berbelanja di Kellogg\'s.',
                  textAlign:
                      TextAlign.center,
                  style: TextStyle(
                    fontSize: 16,
                    color: Colors.grey,
                  ),
                ),

                const SizedBox(
                  height: 32,
                ),

                /*
                |--------------------------------------------------------------------------
                | Order Card
                |--------------------------------------------------------------------------
                */

                Container(
                  width: double.infinity,
                  padding:
                      const EdgeInsets.all(
                    20,
                  ),
                  decoration: BoxDecoration(
                    color: const Color(
                      0xFFF8F9FA,
                    ),
                    borderRadius:
                        BorderRadius.circular(
                      16,
                    ),
                  ),
                  child: Column(
                    children: [
                      const Text(
                        'Nomor Pesanan',
                        style: TextStyle(
                          color:
                              Colors.grey,
                        ),
                      ),

                      const SizedBox(
                        height: 8,
                      ),

                      Text(
                        orderNumber,
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

                      const Divider(
                        height: 32,
                      ),

                      const Row(
                        mainAxisAlignment:
                            MainAxisAlignment
                                .spaceBetween,
                        children: [
                          Text(
                            'Status',
                          ),
                          Text(
                            'Menunggu Pembayaran',
                            style:
                                TextStyle(
                              color:
                                  Colors.orange,
                              fontWeight:
                                  FontWeight
                                      .bold,
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),

                const Spacer(),

                /*
                |--------------------------------------------------------------------------
                | Buttons
                |--------------------------------------------------------------------------
                */

                SizedBox(
                  width:
                      double.infinity,
                  height: 54,
                  child: ElevatedButton(
                    onPressed: () {
                      ScaffoldMessenger.of(
                        context,
                      ).showSnackBar(
                        const SnackBar(
                          content: Text(
                            'Halaman pesanan akan dibuat pada Step berikutnya',
                          ),
                        ),
                      );
                    },
                    style:
                        ElevatedButton.styleFrom(
                      backgroundColor:
                          const Color(
                        0xFFD5001C,
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
                    child: const Text(
                      'Lihat Pesanan',
                      style: TextStyle(
                        color:
                            Colors.white,
                        fontSize: 16,
                        fontWeight:
                            FontWeight.bold,
                      ),
                    ),
                  ),
                ),

                const SizedBox(
                  height: 12,
                ),

                SizedBox(
                  width:
                      double.infinity,
                  height: 54,
                  child: OutlinedButton(
                    onPressed: () {
                      Navigator.pushNamedAndRemoveUntil(
                        context,
                        '/',
                        (
                          route,
                        ) =>
                            false,
                      );
                    },
                    style:
                        OutlinedButton.styleFrom(
                      side: const BorderSide(
                        color: Color(
                          0xFFD5001C,
                        ),
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
                    child: const Text(
                      'Belanja Lagi',
                      style: TextStyle(
                        color: Color(
                          0xFFD5001C,
                        ),
                        fontSize: 16,
                        fontWeight:
                            FontWeight.bold,
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}