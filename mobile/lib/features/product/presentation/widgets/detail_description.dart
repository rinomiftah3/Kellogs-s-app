import 'package:flutter/material.dart';

import '../../data/models/product_model.dart';

class DetailDescription
    extends StatefulWidget {
  const DetailDescription({
    super.key,
    required this.product,
  });

  final ProductModel product;

  @override
  State<DetailDescription>
      createState() =>
          _DetailDescriptionState();
}

class _DetailDescriptionState
    extends State<DetailDescription> {
  bool expanded = false;

  @override
  Widget build(BuildContext context) {
    return Container(
      margin:
          const EdgeInsets.only(top: 12),
      color: Colors.white,
      padding: const EdgeInsets.all(20),

      child: Column(
        crossAxisAlignment:
            CrossAxisAlignment.start,
        children: [
          const Text(
            'Deskripsi Produk',
            style: TextStyle(
              fontSize: 18,
              fontWeight:
                  FontWeight.bold,
            ),
          ),

          const SizedBox(height: 12),

          Text(
            widget.product.description ??
                '-',
            maxLines:
                expanded ? null : 3,
            overflow:
                expanded
                    ? null
                    : TextOverflow
                        .ellipsis,
          ),

          TextButton(
            onPressed: () {
              setState(() {
                expanded =
                    !expanded;
              });
            },
            child: Text(
              expanded
                  ? 'Lebih Sedikit'
                  : 'Lihat Selengkapnya',
            ),
          ),
        ],
      ),
    );
  }
}