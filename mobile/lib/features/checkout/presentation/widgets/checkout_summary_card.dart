import 'package:flutter/material.dart';

import '../../data/models/checkout_summary_model.dart';

class CheckoutSummaryCard extends StatelessWidget {
  const CheckoutSummaryCard({
    super.key,
    required this.summary,
  });

  final CheckoutSummaryModel summary;

  @override
  Widget build(BuildContext context) {
    return Card(
      elevation: 0,
      shape: RoundedRectangleBorder(
        borderRadius:
            BorderRadius.circular(
          16,
        ),
      ),
      child: Padding(
        padding:
            const EdgeInsets.all(16),
        child: Column(
          children: [
            _row(
              'Subtotal',
              summary.subtotalFormatted,
            ),

            _row(
              'Ongkir',
              summary
                  .shippingCostFormatted,
            ),

            _row(
              'Diskon',
              summary.discountFormatted,
            ),

            const Divider(),

            _row(
              'Total',
              summary.totalFormatted,
              bold: true,
            ),
          ],
        ),
      ),
    );
  }

  Widget _row(
    String title,
    String value, {
    bool bold = false,
  }) {
    return Padding(
      padding:
          const EdgeInsets.symmetric(
        vertical: 6,
      ),
      child: Row(
        mainAxisAlignment:
            MainAxisAlignment
                .spaceBetween,
        children: [
          Text(title),

          Text(
            value,
            style: TextStyle(
              fontWeight: bold
                  ? FontWeight.bold
                  : FontWeight.normal,
            ),
          ),
        ],
      ),
    );
  }
}