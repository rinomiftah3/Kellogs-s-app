import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

class DetailEmpty extends StatelessWidget {
  const DetailEmpty({super.key});

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Padding(
        padding:
            const EdgeInsets.symmetric(
          horizontal: 24,
        ),
        child: Column(
          mainAxisAlignment:
              MainAxisAlignment.center,
          children: [
            const Icon(
              Icons.inventory_2_outlined,
              size: 72,
              color: Color(0xFFD5001C),
            ),

            const SizedBox(height: 16),

            const Text(
              'Produk Tidak Ditemukan',
              style: TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.bold,
              ),
            ),

            const SizedBox(height: 8),

            Text(
              'Produk mungkin sudah dihapus atau tidak tersedia.',
              textAlign: TextAlign.center,
              style: TextStyle(
                color: Colors.grey.shade600,
              ),
            ),

            const SizedBox(height: 24),

            ElevatedButton(
              onPressed: () {
                context.go('/home');
              },
              child: const Text(
                'Kembali ke Beranda',
              ),
            ),
          ],
        ),
      ),
    );
  }
}