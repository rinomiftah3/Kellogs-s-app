import 'package:flutter/material.dart';

import '../data/checkout_repository.dart';

import '../data/models/checkout_item_model.dart';
import '../data/models/checkout_summary_model.dart';
import '../data/models/payment_method_model.dart';

import 'widgets/checkout_address_card.dart';
import 'widgets/checkout_bottom_bar.dart';
import 'widgets/checkout_item_card.dart';
import 'widgets/checkout_payment_selector.dart';
import 'widgets/checkout_summary_card.dart';

class CheckoutPage extends StatefulWidget {
  const CheckoutPage({
    super.key,
  });

  @override
  State<CheckoutPage> createState() =>
      _CheckoutPageState();
}

class _CheckoutPageState
    extends State<CheckoutPage> {
  final CheckoutRepository _repository =
      CheckoutRepository();

  bool _isLoading = true;

  bool _isCreatingOrder = false;

  List<CheckoutItemModel> _items = [];

  CheckoutSummaryModel? _summary;

  List<PaymentMethodModel>
      _paymentMethods = [];

  PaymentMethodModel?
      _selectedPayment;

  @override
  void initState() {
    super.initState();

    _loadCheckout();
  }

  Future<void> _loadCheckout() async {
    try {
      final items =
          await _repository.getItems();

      final summary =
          await _repository.getSummary();

      final payments =
          await _repository
              .getPaymentMethods();

      if (!mounted) return;

      setState(() {
        _items = items;

        _summary = summary;

        _paymentMethods =
            payments;

        if (payments.isNotEmpty) {
          _selectedPayment =
              payments.first;
        }

        _isLoading = false;
      });
    } catch (_) {
      if (!mounted) return;

      setState(() {
        _isLoading = false;
      });
    }
  }

  void _selectPayment(
    PaymentMethodModel method,
  ) {
    setState(() {
      _selectedPayment = method;
    });
  }

  Future<void> _checkout() async {
    if (_selectedPayment == null) {
      return;
    }

    setState(() {
      _isCreatingOrder = true;
    });

    try {
      await _repository.createOrder(
        paymentMethodId:
            _selectedPayment!.id,
      );

      await _repository.clearCart();

      if (!mounted) return;

      ScaffoldMessenger.of(context)
          .showSnackBar(
        const SnackBar(
          content: Text(
            'Pesanan berhasil dibuat',
          ),
        ),
      );

      Navigator.pushNamedAndRemoveUntil(
        context,
        '/orders',
        (route) => false,
      );
    } catch (_) {
      if (!mounted) return;

      ScaffoldMessenger.of(context)
          .showSnackBar(
        const SnackBar(
          content: Text(
            'Gagal membuat pesanan',
          ),
        ),
      );
    } finally {
      if (mounted) {
        setState(() {
          _isCreatingOrder =
              false;
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoading) {
      return const Scaffold(
        body: Center(
          child:
              CircularProgressIndicator(),
        ),
      );
    }

    if (_items.isEmpty ||
        _summary == null) {
      return Scaffold(
        appBar: AppBar(
          title: const Text(
            'Checkout',
          ),
        ),
        body: const Center(
          child: Text(
            'Tidak ada produk untuk checkout',
          ),
        ),
      );
    }

    return Scaffold(
      backgroundColor:
          const Color(0xFFF5F5F5),

      appBar: AppBar(
        elevation: 0,
        backgroundColor:
            Colors.white,
        foregroundColor:
            Colors.black,
        title: const Text(
          'Checkout',
        ),
      ),

      body: ListView(
        padding:
            const EdgeInsets.all(16),
        children: [
          const CheckoutAddressCard(),

          const SizedBox(
            height: 12,
          ),

          ..._items.map(
            (item) =>
                CheckoutItemCard(
              item: item,
            ),
          ),

          const SizedBox(
            height: 12,
          ),

          CheckoutPaymentSelector(
            methods:
                _paymentMethods,
            selectedMethod:
                _selectedPayment,
            onSelected:
                _selectPayment,
          ),

          const SizedBox(
            height: 12,
          ),

          CheckoutSummaryCard(
            summary: _summary!,
          ),

          const SizedBox(
            height: 100,
          ),
        ],
      ),

      bottomNavigationBar:
          CheckoutBottomBar(
        summary: _summary!,
        isLoading:
            _isCreatingOrder,
        onCheckout: _checkout,
      ),
    );
  }
}