import 'package:flutter/material.dart';

class OrderEmpty extends StatelessWidget {
  const OrderEmpty({
    super.key,
    this.onShopNow,
  });

  final VoidCallback? onShopNow;

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(
          32,
        ),
        child: Column(
          mainAxisAlignment:
              MainAxisAlignment.center,
          children: [
            /*
            |--------------------------------------------------------------------------
            | Illustration
            |--------------------------------------------------------------------------
            */

            Container(
              width: 140,
              height: 140,
              decoration: BoxDecoration(
                color: const Color(
                  0xFFF8F9FA,
                ),
                shape: BoxShape.circle,
              ),
              child: const Icon(
                Icons.receipt_long_outlined,
                size: 72,
                color: Color(
                  0xFFD5001C,
                ),
              ),
            ),

            const SizedBox(
              height: 24,
            ),

            /*
            |--------------------------------------------------------------------------
            | Title
            |--------------------------------------------------------------------------
            */

            const Text(
              'Belum Ada Pesanan',
              textAlign: TextAlign.center,
              style: TextStyle(
                fontSize: 22,
                fontWeight:
                    FontWeight.bold,
              ),
            ),

            const SizedBox(
              height: 12,
            ),

            /*
            |--------------------------------------------------------------------------
            | Subtitle
            |--------------------------------------------------------------------------
            */

            Text(
              'Pesanan yang sudah kamu buat akan muncul di sini.',
              textAlign: TextAlign.center,
              style: TextStyle(
                fontSize: 15,
                color:
                    Colors.grey.shade600,
              ),
            ),

            const SizedBox(
              height: 32,
            ),

            /*
            |--------------------------------------------------------------------------
            | CTA Button
            |--------------------------------------------------------------------------
            */

            SizedBox(
              width: double.infinity,
              height: 52,
              child: ElevatedButton(
                onPressed: onShopNow,
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
                  'Belanja Sekarang',
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