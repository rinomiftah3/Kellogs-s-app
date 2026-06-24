import 'dart:convert';

import 'package:shared_preferences/shared_preferences.dart';

import 'models/cart_item_model.dart';

class CartService {
  static const _key = 'cart_items';

  Future<List<CartItemModel>>
      getItems() async {
    final prefs =
        await SharedPreferences.getInstance();

    final raw =
        prefs.getStringList(_key) ?? [];

    return raw
        .map(
          (e) => CartItemModel.fromJson(
            jsonDecode(e),
          ),
        )
        .toList();
  }

  Future<void> addItem(
    CartItemModel item,
  ) async {
    final prefs =
        await SharedPreferences.getInstance();

    final items = await getItems();

    final index = items.indexWhere(
      (e) => e.skuId == item.skuId,
    );

    if (index >= 0) {
      items[index] =
          items[index].copyWith(
        quantity:
            items[index].quantity + 1,
      );
    } else {
      items.add(item);
    }

    await prefs.setStringList(
      _key,
      items
          .map(
            (e) =>
                jsonEncode(e.toJson()),
          )
          .toList(),
    );
  }
}