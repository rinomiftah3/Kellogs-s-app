import 'dart:convert';

import 'package:shared_preferences/shared_preferences.dart';

import 'models/cart_item_model.dart';

class CartRepository {
  CartRepository._internal();

  static final CartRepository _instance =
      CartRepository._internal();

  factory CartRepository() => _instance;

  static const String _storageKey =
      'kelloggs_cart';

  /*
  |--------------------------------------------------------------------------
  | In Memory Cart
  |--------------------------------------------------------------------------
  */

  final List<CartItemModel> _items = [];

  /*
  |--------------------------------------------------------------------------
  | Load Cart From SharedPreferences
  |--------------------------------------------------------------------------
  */

  Future<void> _loadIfNeeded() async {
    if (_items.isNotEmpty) {
      return;
    }

    final prefs =
        await SharedPreferences.getInstance();

    final jsonString =
        prefs.getString(_storageKey);

    if (jsonString == null ||
        jsonString.isEmpty) {
      return;
    }

    try {
      final decoded =
          jsonDecode(jsonString) as List;

      _items
        ..clear()
        ..addAll(
          decoded.map(
            (e) => CartItemModel.fromJson(
              Map<String, dynamic>.from(e),
            ),
          ),
        );
    } catch (_) {
      _items.clear();
    }
  }

  /*
  |--------------------------------------------------------------------------
  | Save Cart
  |--------------------------------------------------------------------------
  */

  Future<void> _save() async {
    final prefs =
        await SharedPreferences.getInstance();

    final jsonString = jsonEncode(
      _items
          .map(
            (e) => e.toJson(),
          )
          .toList(),
    );

    await prefs.setString(
      _storageKey,
      jsonString,
    );
  }

  /*
  |--------------------------------------------------------------------------
  | Get Items
  |--------------------------------------------------------------------------
  */

  Future<List<CartItemModel>> getItems() async {
    await _loadIfNeeded();

    return List.unmodifiable(_items);
  }

  /*
  |--------------------------------------------------------------------------
  | Total Quantity
  |--------------------------------------------------------------------------
  */

  Future<int> getTotalQuantity() async {
  await _loadIfNeeded();

  return _items.fold<int>(
    0,
    (sum, item) {
      return sum + item.quantity;
    },
  );
}

  /*
  |--------------------------------------------------------------------------
  | Grand Total
  |--------------------------------------------------------------------------
  */

  Future<double> getGrandTotal() async {
  await _loadIfNeeded();

  return _items.fold<double>(
    0.0,
    (sum, item) {
      return sum +
          (item.price * item.quantity);
    },
  );
}

  /*
  |--------------------------------------------------------------------------
  | Add Item
  |--------------------------------------------------------------------------
  */

  Future<void> addItem(
    CartItemModel item,
  ) async {
    await _loadIfNeeded();

    final index = _items.indexWhere(
      (e) => e.skuId == item.skuId,
    );

    if (index != -1) {
      final existing = _items[index];

      _items[index] =
          existing.copyWith(
        quantity:
            existing.quantity +
            item.quantity,
      );
    } else {
      _items.add(item);
    }

    await _save();
  }

  /*
  |--------------------------------------------------------------------------
  | Increase Quantity
  |--------------------------------------------------------------------------
  */

  Future<void> increaseQty(
    int skuId,
  ) async {
    await _loadIfNeeded();

    final index = _items.indexWhere(
      (e) => e.skuId == skuId,
    );

    if (index == -1) {
      return;
    }

    final item = _items[index];

    _items[index] = item.copyWith(
      quantity: item.quantity + 1,
    );

    await _save();
  }

  /*
  |--------------------------------------------------------------------------
  | Decrease Quantity
  |--------------------------------------------------------------------------
  */

  Future<void> decreaseQty(
    int skuId,
  ) async {
    await _loadIfNeeded();

    final index = _items.indexWhere(
      (e) => e.skuId == skuId,
    );

    if (index == -1) {
      return;
    }

    final item = _items[index];

    if (item.quantity <= 1) {
      _items.removeAt(index);
    } else {
      _items[index] = item.copyWith(
        quantity: item.quantity - 1,
      );
    }

    await _save();
  }

  /*
  |--------------------------------------------------------------------------
  | Update Quantity
  |--------------------------------------------------------------------------
  */

  Future<void> updateQuantity(
    int skuId,
    int quantity,
  ) async {
    await _loadIfNeeded();

    final index = _items.indexWhere(
      (e) => e.skuId == skuId,
    );

    if (index == -1) {
      return;
    }

    if (quantity <= 0) {
      _items.removeAt(index);
    } else {
      _items[index] =
          _items[index].copyWith(
        quantity: quantity,
      );
    }

    await _save();
  }

  /*
  |--------------------------------------------------------------------------
  | Remove Item
  |--------------------------------------------------------------------------
  */

  Future<void> removeItem(
    int skuId,
  ) async {
    await _loadIfNeeded();

    _items.removeWhere(
      (e) => e.skuId == skuId,
    );

    await _save();
  }

  /*
  |--------------------------------------------------------------------------
  | Clear Cart
  |--------------------------------------------------------------------------
  */

  Future<void> clear() async {
    _items.clear();

    await _save();
  }

  /*
  |--------------------------------------------------------------------------
  | Exists
  |--------------------------------------------------------------------------
  */

  Future<bool> contains(
    int skuId,
  ) async {
    await _loadIfNeeded();

    return _items.any(
      (e) => e.skuId == skuId,
    );
  }
}