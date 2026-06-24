import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

import '../../data/models/product_model.dart';
import '../../data/product_repository.dart';
import 'product_card.dart';
import '../../../../core/widgets/skeleton.dart';
import '../../../../core/widgets/empty_state.dart';
import '../../../../core/widgets/error_state.dart';

class ProductSection extends StatefulWidget {
  const ProductSection({super.key});

  @override
  State<ProductSection> createState() =>
      _ProductSectionState();
}

class _ProductSectionState
    extends State<ProductSection> {
  final ProductRepository _repository =
      ProductRepository();

  late Future<List<ProductModel>>
      _productsFuture;

  @override
  void initState() {
    super.initState();

    _productsFuture =
        _repository.getHomeProducts();
  }

  @override
  Widget build(BuildContext context) {
    return FutureBuilder<List<ProductModel>>(
      future: _productsFuture,
      builder: (context, snapshot) {
        /*
        |--------------------------------------------------------------------------
        | Loading
        |--------------------------------------------------------------------------
        */

        if (snapshot.connectionState ==
    ConnectionState.waiting) {
  return Column(
    children: [
      Padding(
        padding:
            const EdgeInsets.symmetric(
          horizontal: 16,
        ),
        child: Row(
          children: const [
            Skeleton(
              width: 160,
              height: 20,
            ),
          ],
        ),
      ),

      const SizedBox(height: 16),

      SizedBox(
        height: 310,
        child: ListView.separated(
          padding:
              const EdgeInsets.symmetric(
            horizontal: 16,
          ),
          scrollDirection:
              Axis.horizontal,
          itemCount: 2,
          separatorBuilder:
              (_, __) =>
                  const SizedBox(
            width: 12,
          ),
          itemBuilder: (_, __) {
            return Container(
              width: 170,
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius:
                    BorderRadius.circular(
                  16,
                ),
              ),
              child: Padding(
                padding:
                    const EdgeInsets.all(
                  12,
                ),
                child: Column(
                  crossAxisAlignment:
                      CrossAxisAlignment
                          .start,
                  children: const [
                    Skeleton(
                      width: 146,
                      height: 140,
                    ),

                    SizedBox(height: 12),

                    Skeleton(
                      width: 120,
                      height: 14,
                    ),

                    SizedBox(height: 8),

                    Skeleton(
                      width: 80,
                      height: 12,
                    ),

                    Spacer(),

                    Skeleton(
                      width: 100,
                      height: 12,
                    ),

                    SizedBox(height: 12),

                    Skeleton(
                      width: 146,
                      height: 38,
                    ),
                  ],
                ),
              ),
            );
          },
        ),
      ),
    ],
  );
}

        /*
        |--------------------------------------------------------------------------
        | Error
        |--------------------------------------------------------------------------
        */

        if (snapshot.hasError) {
  return ErrorState(
    title: 'Produk Tidak Tersedia',
    subtitle:
        'Terjadi kesalahan saat memuat produk.',
    onRetry: () {
      setState(() {
        _productsFuture =
            _repository.getHomeProducts();
      });
    },
  );
}

        final products =
            snapshot.data ?? [];

        /*
        |--------------------------------------------------------------------------
        | Empty State
        |--------------------------------------------------------------------------
        */

        if (products.isEmpty) {
  return const EmptyState(
    icon: Icons.inventory_2_outlined,
    title: 'Belum Ada Produk',
    subtitle:
        'Silakan kembali lagi nanti.',
  );
}

        return Column(
          children: [
            Padding(
              padding:
                  const EdgeInsets.symmetric(
                horizontal: 16,
              ),
              child: Row(
                children: [
                  const Text(
                    'Produk Terlaris',
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight:
                          FontWeight.bold,
                    ),
                  ),

                  const Spacer(),

                  TextButton(
                    onPressed: () {
                      context.go(
                        '/products',
                      );
                    },
                    child: const Text(
                      'Lihat Semua',
                    ),
                  ),
                ],
              ),
            ),

            SizedBox(
              height: 310,
              child: ListView.separated(
                padding:
                    const EdgeInsets.symmetric(
                  horizontal: 16,
                ),
                scrollDirection:
                    Axis.horizontal,
                itemCount:
                    products.length,
                separatorBuilder:
                    (_, __) =>
                        const SizedBox(
                  width: 12,
                ),
                itemBuilder:
                    (context, index) {
                  return ProductCard(
                    product:
                        products[index],
                  );
                },
              ),
            ),
          ],
        );
      },
    );
  }
}