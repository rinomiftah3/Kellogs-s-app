import 'package:flutter/material.dart';

class CartSummary extends StatelessWidget {
  const CartSummary({
    super.key,
    required this.totalItems,
    required this.subtotal,
    required this.onCheckout,
  });

  final int totalItems;

  final double subtotal;

  final VoidCallback? onCheckout;

  String get subtotalFormatted {
    return 'Rp ${subtotal.toStringAsFixed(0).replaceAllMapped(
      RegExp(r'\B(?=(\d{3})+(?!\d))'),
      (match) => '.',
    )}';
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      color: Colors.white,
      padding: const EdgeInsets.all(16),
      child: SafeArea(
        top: false,
        child: Row(
          children: [
            Expanded(
              child: Column(
                crossAxisAlignment:
                    CrossAxisAlignment.start,
                mainAxisSize: MainAxisSize.min,
                children: [
                  Text(
                    '$totalItems item',
                    style: TextStyle(
                      color: Colors.grey.shade600,
                      fontSize: 13,
                    ),
                  ),

                  const SizedBox(height: 4),

                  Text(
                    subtotalFormatted,
                    style: const TextStyle(
                      fontSize: 22,
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

            SizedBox(
              width: 150,
              height: 52,
              child: ElevatedButton(
                onPressed: onCheckout,
                style:
                    ElevatedButton.styleFrom(
                  backgroundColor:
                      const Color(
                    0xFFD5001C,
                  ),
                  foregroundColor:
                      Colors.white,
                  elevation: 0,
                  shape:
                      RoundedRectangleBorder(
                    borderRadius:
                        BorderRadius.circular(
                      14,
                    ),
                  ),
                ),
                child: const Text(
                  'Checkout',
                  style: TextStyle(
                    fontSize: 16,
                    fontWeight:
                        FontWeight.w600,
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}