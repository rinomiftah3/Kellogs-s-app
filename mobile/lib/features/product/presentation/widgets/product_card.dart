import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

import '../../data/models/product_model.dart';

class ProductCard extends StatelessWidget {
  final ProductModel product;

  const ProductCard({
    super.key,
    required this.product,
  });

  @override
  Widget build(BuildContext context) {
    return InkWell(
      borderRadius: BorderRadius.circular(
        16,
      ),
      onTap: () {
        context.go(
          '/products/${product.slug}',
        );
      },
      child: Container(
        width: 170,
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius:
              BorderRadius.circular(
            16,
          ),
          boxShadow: [
            BoxShadow(
              color: Colors.black
                  .withOpacity(0.05),
              blurRadius: 10,
              offset: const Offset(
                0,
                4,
              ),
            ),
          ],
        ),
        child: Column(
          crossAxisAlignment:
              CrossAxisAlignment.start,
          children: [
            /*
            |--------------------------------------------------------------------------
            | Product Image
            |--------------------------------------------------------------------------
            */

            Stack(
              children: [
                ClipRRect(
                  borderRadius:
                      const BorderRadius
                          .vertical(
                    top: Radius.circular(
                      16,
                    ),
                  ),
                  child: SizedBox(
                    height: 140,
                    width: double.infinity,
                    child:
                        product.hasImage &&
                                product
                                    .image
                                    .isNotEmpty
                            ? Image.network(
                                product.image,
                                fit: BoxFit
                                    .cover,
                                errorBuilder:
                                    (
                                  context,
                                  error,
                                  stackTrace,
                                ) {
                                  return _placeholderImage();
                                },
                              )
                            : _placeholderImage(),
                  ),
                ),

                if (product.isFeatured)
                  Positioned(
                    top: 10,
                    left: 10,
                    child: Container(
                      padding:
                          const EdgeInsets.symmetric(
                        horizontal: 10,
                        vertical: 4,
                      ),
                      decoration:
                          BoxDecoration(
                        color:
                            const Color(
                          0xFFD5001C,
                        ),
                        borderRadius:
                            BorderRadius.circular(
                          20,
                        ),
                      ),
                      child: const Text(
                        'FEATURED',
                        style: TextStyle(
                          color:
                              Colors.white,
                          fontSize: 10,
                          fontWeight:
                              FontWeight.bold,
                        ),
                      ),
                    ),
                  ),
              ],
            ),

            Expanded(
              child: Padding(
                padding:
                    const EdgeInsets.all(
                  12,
                ),
                child: Column(
                  crossAxisAlignment:
                      CrossAxisAlignment
                          .start,
                  children: [
                    Text(
                      product.name,
                      maxLines: 2,
                      overflow:
                          TextOverflow
                              .ellipsis,
                      style:
                          const TextStyle(
                        fontWeight:
                            FontWeight.bold,
                        fontSize: 14,
                      ),
                    ),

                    const SizedBox(
                      height: 6,
                    ),

                    Text(
                      product
                          .category.name,
                      style:
                          TextStyle(
                        color: Colors
                            .grey
                            .shade600,
                        fontSize: 12,
                      ),
                    ),

                    const Spacer(),

                    Row(
                      children: [
                        const Icon(
                          Icons.star,
                          color: Colors
                              .amber,
                          size: 16,
                        ),

                        const SizedBox(
                          width: 4,
                        ),

                        Text(
                          '${product.reviewCount} Reviews',
                          style:
                              const TextStyle(
                            fontSize:
                                12,
                          ),
                        ),
                      ],
                    ),

                    const SizedBox(
                      height: 10,
                    ),

                    SizedBox(
                      width:
                          double.infinity,
                      height: 38,
                      child:
                          ElevatedButton(
                        onPressed: () {
                          context.go(
                            '/products/${product.slug}',
                          );
                        },
                        child:
                            const Text(
                          'Lihat Detail',
                          style:
                              TextStyle(
                            fontSize:
                                12,
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _placeholderImage() {
    return Container(
      color: const Color(
        0xFFFFF3E0,
      ),
      child: const Center(
        child: Icon(
          Icons.breakfast_dining,
          color: Color(
            0xFFD5001C,
          ),
          size: 50,
        ),
      ),
    );
  }
}