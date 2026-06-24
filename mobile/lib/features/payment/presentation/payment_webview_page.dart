import 'package:flutter/material.dart';

class PaymentWebviewPage extends StatelessWidget {
  final String paymentUrl;

  const PaymentWebviewPage({
    super.key,
    required this.paymentUrl,
  });

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Payment'),
      ),
      body: Center(
        child: Text(paymentUrl),
      ),
    );
  }
}