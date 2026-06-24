import 'package:flutter/material.dart';

class CartEmpty extends StatelessWidget {
  const CartEmpty({
    super.key,
    this.onShopNow,
  });

  final VoidCallback? onShopNow;

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.symmetric(
          horizontal: 32,
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
                  0xFFFEECEC,
                ),
                shape: BoxShape.circle,
              ),
              child: const Icon(
                Icons.shopping_cart_outlined,
                size: 72,
                color: Color(
                  0xFFD5001C,
                ),
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
              'Keranjang Masih Kosong',
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
              'Yuk mulai belanja produk Kellogg\'s favoritmu dan nikmati pengalaman belanja yang lebih mudah.',
              textAlign: TextAlign.center,
              style: TextStyle(
                fontSize: 14,
                color:
                    Colors.grey.shade600,
                height: 1.5,
              ),
            ),

            const SizedBox(
              height: 32,
            ),

            /*
            |--------------------------------------------------------------------------
            | CTA
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
                  'Mulai Belanja',
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