import 'package:flutter/material.dart';

class OrderTrackingPage extends StatelessWidget {
  final String orderNumber;

  const OrderTrackingPage({
    super.key,
    required this.orderNumber,
  });

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Tracking'),
      ),
      body: Center(
        child: Text('Tracking: $orderNumber'),
      ),
    );
  }
}