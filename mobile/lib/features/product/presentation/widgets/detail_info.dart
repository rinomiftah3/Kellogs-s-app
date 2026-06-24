import 'package:flutter/material.dart';

import '../../data/models/product_model.dart';
import '../../data/models/product_sku_model.dart';

class DetailInfo extends StatelessWidget {
  const DetailInfo({
    super.key,
    required this.product,
    this.selectedSku,
  });

  final ProductModel product;

  final ProductSkuModel? selectedSku;

  @override
  Widget build(BuildContext context) {
    return Container(
      color: Colors.white,
      padding: const EdgeInsets.all(
        20,
      ),
      child: Column(
        crossAxisAlignment:
            CrossAxisAlignment.start,
        children: [
          /*
          |--------------------------------------------------------------------------
          | Product Name
          |--------------------------------------------------------------------------
          */

          Text(
            product.name,
            style: const TextStyle(
              fontSize: 22,
              fontWeight:
                  FontWeight.bold,
            ),
          ),

          const SizedBox(height: 16),

          /*
          |--------------------------------------------------------------------------
          | Price
          |--------------------------------------------------------------------------
          */

          if (selectedSku != null)
            Column(
              crossAxisAlignment:
                  CrossAxisAlignment.start,
              children: [
                if (selectedSku!
                    .hasDiscount)
                  Row(
                    children: [
                      Text(
                        selectedSku!
                                .compareAtPriceFormatted ??
                            '',
                        style:
                            const TextStyle(
                          fontSize: 14,
                          color:
                              Colors.grey,
                          decoration:
                              TextDecoration
                                  .lineThrough,
                        ),
                      ),

                      const SizedBox(
                        width: 8,
                      ),

                      Container(
                        padding:
                            const EdgeInsets
                                .symmetric(
                          horizontal: 8,
                          vertical: 4,
                        ),
                        decoration:
                            BoxDecoration(
                          color:
                              Colors.red,
                          borderRadius:
                              BorderRadius
                                  .circular(
                            8,
                          ),
                        ),
                        child: Text(
                          '-${selectedSku!.discountPercentage.toInt()}%',
                          style:
                              const TextStyle(
                            color: Colors
                                .white,
                            fontSize: 12,
                            fontWeight:
                                FontWeight
                                    .bold,
                          ),
                        ),
                      ),
                    ],
                  ),

                if (selectedSku!
                    .hasDiscount)
                  const SizedBox(
                    height: 6,
                  ),

                Text(
                  selectedSku!
                      .priceFormatted,
                  style:
                      const TextStyle(
                    fontSize: 28,
                    fontWeight:
                        FontWeight.bold,
                    color: Color(
                      0xFFD5001C,
                    ),
                  ),
                ),
              ],
            )
          else
            const Text(
              'Harga belum tersedia',
              style: TextStyle(
                fontSize: 18,
                color: Colors.grey,
              ),
            ),

          const SizedBox(height: 16),

          /*
          |--------------------------------------------------------------------------
          | Reviews
          |--------------------------------------------------------------------------
          */

          Row(
            children: [
              const Icon(
                Icons.star,
                color: Colors.amber,
                size: 18,
              ),

              const SizedBox(
                width: 4,
              ),

              Text(
                '${product.reviewCount} ulasan',
              ),
            ],
          ),

          const SizedBox(height: 12),

          /*
          |--------------------------------------------------------------------------
          | Category
          |--------------------------------------------------------------------------
          */

          Text(
            'Kategori: ${product.categoryName}',
            style: const TextStyle(
              fontSize: 14,
            ),
          ),

          const SizedBox(height: 8),

          /*
          |--------------------------------------------------------------------------
          | Stock Status
          |--------------------------------------------------------------------------
          */

          if (selectedSku != null)
            Row(
              children: [
                Icon(
                  selectedSku!
                          .isInStock
                      ? Icons
                          .check_circle
                      : Icons
                          .cancel,
                  size: 18,
                  color: selectedSku!
                          .isInStock
                      ? Colors.green
                      : Colors.red,
                ),

                const SizedBox(
                  width: 6,
                ),

                Text(
                  selectedSku!
                          .isInStock
                      ? 'Stok tersedia : ${selectedSku!.stock}'
                      : 'Stok habis',
                  style:
                      TextStyle(
                    color: selectedSku!
                            .isInStock
                        ? Colors.green
                        : Colors.red,
                    fontWeight:
                        FontWeight
                            .w600,
                  ),
                ),
              ],
            ),

          const SizedBox(height: 8),

          Text(
            'Status: ${product.statusLabel}',
          ),

          const SizedBox(height: 20),

          /*
          |--------------------------------------------------------------------------
          | SKU Option Values
          |--------------------------------------------------------------------------
          */

          if (selectedSku != null &&
              selectedSku!
                  .optionValues
                  .isNotEmpty)
            Wrap(
              spacing: 8,
              runSpacing: 8,
              children:
                  selectedSku!
                      .optionValues
                      .map(
                        (option) =>
                            Chip(
                          label: Text(
                            option.option !=
                                    null
                                ? '${option.option}: ${option.name}'
                                : option
                                    .name,
                          ),
                        ),
                      )
                      .toList(),
            )
          else
            Wrap(
              spacing: 8,
              children: const [
                Chip(
                  label: Text(
                    'Original',
                  ),
                ),
                Chip(
                  label: Text(
                    'Halal',
                  ),
                ),
                Chip(
                  label: Text(
                    'Sarapan Sehat',
                  ),
                ),
              ],
            ),
        ],
      ),
    );
  }
}