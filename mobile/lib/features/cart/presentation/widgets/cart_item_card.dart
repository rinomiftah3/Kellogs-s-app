import 'package:flutter/material.dart';

import '../../data/models/cart_item_model.dart';

class CartItemCard extends StatelessWidget {
  const CartItemCard({
    super.key,
    required this.item,
    required this.onIncrease,
    required this.onDecrease,
    required this.onDelete,
  });

  final CartItemModel item;

  final VoidCallback onIncrease;

  final VoidCallback onDecrease;

  final VoidCallback onDelete;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(
        12,
      ),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius:
            BorderRadius.circular(
          16,
        ),
        boxShadow: [
          BoxShadow(
            color: Colors.black
                .withOpacity(0.04),
            blurRadius: 8,
            offset: const Offset(
              0,
              2,
            ),
          ),
        ],
      ),
      child: Row(
        crossAxisAlignment:
            CrossAxisAlignment.start,
        children: [
          /*
          |--------------------------------------------------------------------------
          | Thumbnail
          |--------------------------------------------------------------------------
          */

          ClipRRect(
            borderRadius:
                BorderRadius.circular(
              12,
            ),
            child: item.hasImage
                ? Image.network(
                    item.image,
                    width: 80,
                    height: 80,
                    fit: BoxFit.cover,
                    errorBuilder: (
                      context,
                      error,
                      stackTrace,
                    ) {
                      return _buildPlaceholder();
                    },
                  )
                : _buildPlaceholder(),
          ),

          const SizedBox(width: 12),

          /*
          |--------------------------------------------------------------------------
          | Product Information
          |--------------------------------------------------------------------------
          */

          Expanded(
            child: Column(
              crossAxisAlignment:
                  CrossAxisAlignment
                      .start,
              children: [
                Text(
                  item.productName,
                  maxLines: 2,
                  overflow:
                      TextOverflow
                          .ellipsis,
                  style:
                      const TextStyle(
                    fontSize: 15,
                    fontWeight:
                        FontWeight.w700,
                  ),
                ),

                const SizedBox(height: 4),

                Text(
                  item.sku,
                  style: TextStyle(
                    fontSize: 12,
                    color: Colors
                        .grey.shade600,
                  ),
                ),

                const SizedBox(height: 10),

                Text(
                  item.priceFormatted,
                  style:
                      const TextStyle(
                    fontSize: 18,
                    fontWeight:
                        FontWeight.bold,
                    color: Color(
                      0xFFD5001C,
                    ),
                  ),
                ),

                const SizedBox(height: 12),

                /*
                |--------------------------------------------------------------------------
                | Qty Control
                |--------------------------------------------------------------------------
                */

                Row(
                  children: [
                    IconButton(
                      onPressed:
                          onDelete,
                      icon: const Icon(
                        Icons
                            .delete_outline,
                        color:
                            Colors.red,
                      ),
                    ),

                    const Spacer(),

                    _qtyButton(
                      icon:
                          Icons.remove,
                      onTap:
                          onDecrease,
                    ),

                    Container(
                      width: 40,
                      alignment:
                          Alignment
                              .center,
                      child: Text(
                        '${item.quantity}',
                        style:
                            const TextStyle(
                          fontSize: 16,
                          fontWeight:
                              FontWeight
                                  .bold,
                        ),
                      ),
                    ),

                    _qtyButton(
                      icon:
                          Icons.add,
                      onTap:
                          onIncrease,
                    ),
                  ],
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  /*
  |--------------------------------------------------------------------------
  | Qty Button
  |--------------------------------------------------------------------------
  */

  Widget _qtyButton({
    required IconData icon,
    required VoidCallback onTap,
  }) {
    return SizedBox(
      width: 36,
      height: 36,
      child: OutlinedButton(
        onPressed: onTap,
        style:
            OutlinedButton.styleFrom(
          padding:
              EdgeInsets.zero,
          shape:
              RoundedRectangleBorder(
            borderRadius:
                BorderRadius.circular(
              10,
            ),
          ),
        ),
        child: Icon(
          icon,
          size: 18,
        ),
      ),
    );
  }

  /*
  |--------------------------------------------------------------------------
  | Placeholder Image
  |--------------------------------------------------------------------------
  */

  Widget _buildPlaceholder() {
    return Container(
      width: 80,
      height: 80,
      color: const Color(
        0xFFF3F4F6,
      ),
      child: const Icon(
        Icons.breakfast_dining,
        size: 40,
        color: Color(
          0xFFD5001C,
        ),
      ),
    );
  }
}