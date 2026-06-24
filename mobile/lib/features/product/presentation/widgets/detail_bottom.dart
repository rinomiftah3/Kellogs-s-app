import 'package:flutter/material.dart';

import '../../data/models/product_model.dart';
import '../../data/models/product_sku_model.dart';

class DetailBottomBar extends StatelessWidget {
  const DetailBottomBar({
    super.key,
    required this.product,
    this.selectedSku,
    this.onAddToCart,
  });

  final ProductModel product;

  final ProductSkuModel? selectedSku;

  final VoidCallback? onAddToCart;

  @override
  Widget build(BuildContext context) {
    final canPurchase =
        selectedSku?.canBePurchased ?? false;

    return Container(
      color: Colors.white,
      padding: const EdgeInsets.all(16),
      child: SafeArea(
        top: false,
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            /*
            |--------------------------------------------------------------------------
            | Price & Stock
            |--------------------------------------------------------------------------
            */

            if (selectedSku != null)
              Padding(
                padding: const EdgeInsets.only(
                  bottom: 12,
                ),
                child: Row(
                  children: [
                    Expanded(
                      child: Column(
                        crossAxisAlignment:
                            CrossAxisAlignment.start,
                        children: [
                          Text(
                            selectedSku!
                                .priceFormatted,
                            style:
                                const TextStyle(
                              fontSize: 22,
                              fontWeight:
                                  FontWeight.bold,
                              color: Color(
                                0xFFD5001C,
                              ),
                            ),
                          ),

                          const SizedBox(
                            height: 4,
                          ),

                          Text(
                            selectedSku!.isInStock
                                ? 'Stok: ${selectedSku!.stock}'
                                : 'Stok habis',
                            style: TextStyle(
                              color: selectedSku!
                                      .isInStock
                                  ? Colors.green
                                  : Colors.red,
                              fontWeight:
                                  FontWeight.w600,
                            ),
                          ),
                        ],
                      ),
                    ),

                    if (selectedSku!
                        .hasDiscount)
                      Container(
                        padding:
                            const EdgeInsets.symmetric(
                          horizontal: 10,
                          vertical: 6,
                        ),
                        decoration:
                            BoxDecoration(
                          color: Colors.red,
                          borderRadius:
                              BorderRadius.circular(
                            8,
                          ),
                        ),
                        child: Text(
                          '-${selectedSku!.discountPercentage.toInt()}%',
                          style:
                              const TextStyle(
                            color:
                                Colors.white,
                            fontWeight:
                                FontWeight.bold,
                          ),
                        ),
                      ),
                  ],
                ),
              ),

            /*
            |--------------------------------------------------------------------------
            | Action Buttons
            |--------------------------------------------------------------------------
            */

            Row(
              children: [
                Expanded(
                  child: OutlinedButton(
                    onPressed: canPurchase
                        ? onAddToCart
                        : null,
                    style:
                        OutlinedButton.styleFrom(
                      minimumSize:
                          const Size(
                        double.infinity,
                        52,
                      ),
                    ),
                    child: const Text(
                      '+ Keranjang',
                    ),
                  ),
                ),

                const SizedBox(width: 12),

                Expanded(
                  child: ElevatedButton(
                    onPressed: canPurchase
                        ? () {
                            ScaffoldMessenger.of(
                              context,
                            ).showSnackBar(
                              const SnackBar(
                                content: Text(
                                  'Checkout akan dibuat pada Step berikutnya',
                                ),
                              ),
                            );
                          }
                        : null,
                    style:
                        ElevatedButton.styleFrom(
                      minimumSize:
                          const Size(
                        double.infinity,
                        52,
                      ),
                    ),
                    child: Text(
                      canPurchase
                          ? 'Beli Sekarang'
                          : 'Tidak Tersedia',
                    ),
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}