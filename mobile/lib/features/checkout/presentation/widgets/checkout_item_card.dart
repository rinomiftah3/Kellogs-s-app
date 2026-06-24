import 'package:flutter/material.dart';

import '../../data/models/checkout_item_model.dart';

class CheckoutItemCard extends StatelessWidget {
  const CheckoutItemCard({
    super.key,
    required this.item,
  });

  final CheckoutItemModel item;

  @override
  Widget build(BuildContext context) {
    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      child: ListTile(
        leading: item.image.isNotEmpty
            ? Image.network(
                item.image,
                width: 60,
                height: 60,
                fit: BoxFit.cover,
              )
            : const Icon(Icons.breakfast_dining),
        title: Text(item.productName),
        subtitle: Text(
          '${item.sku}\n${item.priceFormatted} x ${item.quantity}',
        ),
      ),
    );
  }
}