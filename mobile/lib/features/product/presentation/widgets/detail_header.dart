import 'package:flutter/material.dart';

import '../../data/models/product_model.dart';
import 'package:go_router/go_router.dart';
class DetailHeader extends StatelessWidget {
  const DetailHeader({
    super.key,
    required this.product,
  });

  final ProductModel product;

  @override
  Widget build(BuildContext context) {
    return Stack(
      children: [
        /*
        |--------------------------------------------------------------------------
        | Background
        |--------------------------------------------------------------------------
        */

        Container(
          height: 320,
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              colors: [
                Color(0xFFD5001C),
                Color(0xFFB30018),
              ],
              begin: Alignment.topCenter,
              end: Alignment.bottomCenter,
            ),
          ),
        ),

        SafeArea(
          child: Column(
            children: [
              /*
              |--------------------------------------------------------------------------
              | Top App Bar
              |--------------------------------------------------------------------------
              */

              Padding(
                padding: const EdgeInsets.symmetric(
                  horizontal: 12,
                  vertical: 8,
                ),
                child: Row(
                  children: [
                    CircleAvatar(
                      backgroundColor:
                          Colors.white,
                      child: IconButton(
                        icon: const Icon(
                          Icons.arrow_back,
                          color: Colors.black,
                        ),
                        onPressed: () {
                        if (context.canPop()) {
                            context.pop();
                        } else {
                            context.go('/home');
                        }
                        },
                      ),
                    ),

                    const Spacer(),

                    CircleAvatar(
                      backgroundColor:
                          Colors.white,
                      child: IconButton(
                        icon: const Icon(
                          Icons.share_outlined,
                          color: Colors.black,
                        ),
                        onPressed: () {
                          ScaffoldMessenger.of(
                            context,
                          ).showSnackBar(
                            const SnackBar(
                              content: Text(
                                'Fitur share akan dibuat pada step berikutnya',
                              ),
                            ),
                          );
                        },
                      ),
                    ),

                    const SizedBox(width: 12),

                    CircleAvatar(
                      backgroundColor:
                          Colors.white,
                      child: IconButton(
                        icon: const Icon(
                          Icons.favorite_border,
                          color: Colors.red,
                        ),
                        onPressed: () {
                          ScaffoldMessenger.of(
                            context,
                          ).showSnackBar(
                            const SnackBar(
                              content: Text(
                                'Wishlist akan dibuat pada step berikutnya',
                              ),
                            ),
                          );
                        },
                      ),
                    ),
                  ],
                ),
              ),

              const SizedBox(height: 20),

              /*
              |--------------------------------------------------------------------------
              | Product Image
              |--------------------------------------------------------------------------
              */

              Container(
                width: 220,
                height: 220,
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius:
                      BorderRadius.circular(
                    24,
                  ),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black12,
                      blurRadius: 20,
                      offset:
                          const Offset(0, 10),
                    ),
                  ],
                ),
                clipBehavior: Clip.antiAlias,
                child: product.image.isNotEmpty
                    ? Image.network(
                        product.image,
                        fit: BoxFit.cover,
                        errorBuilder: (
                          context,
                          error,
                          stackTrace,
                        ) {
                          return const _ImagePlaceholder();
                        },
                      )
                    : const _ImagePlaceholder(),
              ),

              const SizedBox(height: 20),
            ],
          ),
        ),
      ],
    );
  }
}

class _ImagePlaceholder
    extends StatelessWidget {
  const _ImagePlaceholder();

  @override
  Widget build(BuildContext context) {
    return Container(
      color: Colors.white,
      child: const Center(
        child: Icon(
          Icons.breakfast_dining,
          size: 80,
          color: Color(0xFFD5001C),
        ),
      ),
    );
  }
}