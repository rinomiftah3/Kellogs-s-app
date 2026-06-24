import 'package:flutter/material.dart';

import '../data/cart_repository.dart';
import '../data/models/cart_item_model.dart';

import 'widgets/cart_empty.dart';
import 'widgets/cart_item_card.dart';
import 'widgets/cart_summary.dart';

class CartPage extends StatefulWidget {
  const CartPage({
    super.key,
  });

  @override
  State<CartPage> createState() =>
      _CartPageState();
}

class _CartPageState
    extends State<CartPage> {
  final CartRepository _repository =
      CartRepository();

  List<CartItemModel> _items = [];

  bool _isLoading = true;

  @override
  void initState() {
    super.initState();

    _loadCart();
  }

  /*
  |--------------------------------------------------------------------------
  | Load Cart
  |--------------------------------------------------------------------------
  */

  Future<void> _loadCart() async {
    final items =
        await _repository.getItems();

    if (!mounted) return;

    setState(() {
      _items = items;
      _isLoading = false;
    });
  }

  /*
  |--------------------------------------------------------------------------
  | Increase Qty
  |--------------------------------------------------------------------------
  */

  Future<void> _increase(
    CartItemModel item,
  ) async {
    await _repository.increaseQty(
      item.skuId,
    );

    await _loadCart();
  }

  /*
  |--------------------------------------------------------------------------
  | Decrease Qty
  |--------------------------------------------------------------------------
  */

  Future<void> _decrease(
    CartItemModel item,
  ) async {
    await _repository.decreaseQty(
      item.skuId,
    );

    await _loadCart();
  }

  /*
  |--------------------------------------------------------------------------
  | Delete Item
  |--------------------------------------------------------------------------
  */

  Future<void> _delete(
    CartItemModel item,
  ) async {
    await _repository.removeItem(
      item.skuId,
    );

    await _loadCart();

    if (!mounted) return;

    ScaffoldMessenger.of(context)
        .showSnackBar(
      SnackBar(
        content: Text(
          '${item.productName} dihapus dari keranjang',
        ),
      ),
    );
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

    final totalItems = _items.fold(
      0,
      (sum, item) =>
          sum + item.quantity,
    );

    final subtotal =
        _items.fold<double>(
      0,
      (sum, item) =>
          sum +
          (item.price *
              item.quantity),
    );

    return Scaffold(
      backgroundColor:
          const Color(0xFFF5F5F5),

      appBar: AppBar(
        backgroundColor:
            Colors.white,
        foregroundColor:
            Colors.black,
        elevation: 0,
        centerTitle: true,
        title: Text(
          'Keranjang ($totalItems)',
        ),
      ),

      body: _items.isEmpty
          ? CartEmpty(
              onShopNow: () {
                Navigator.pop(
                  context,
                );
              },
            )
          : ListView.separated(
              padding:
                  const EdgeInsets.all(
                16,
              ),
              itemCount:
                  _items.length,
              separatorBuilder:
                  (_, __) =>
                      const SizedBox(
                height: 12,
              ),
              itemBuilder:
                  (context, index) {
                final item =
                    _items[index];

                return CartItemCard(
                  item: item,

                  onIncrease: () =>
                      _increase(
                    item,
                  ),

                  onDecrease: () =>
                      _decrease(
                    item,
                  ),

                  onDelete: () =>
                      _delete(
                    item,
                  ),
                );
              },
            ),

      bottomNavigationBar:
          _items.isEmpty
              ? null
              : CartSummary(
                  totalItems:
                      totalItems,

                  subtotal:
                      subtotal,

                  onCheckout: () {
                    ScaffoldMessenger.of(
                      context,
                    ).showSnackBar(
                      const SnackBar(
                        content: Text(
                          'Checkout akan dibuat pada Step berikutnya',
                        ),
                      ),
                    );
                  },
                ),
    );
  }
}