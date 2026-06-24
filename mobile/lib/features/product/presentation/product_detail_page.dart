import 'package:flutter/material.dart';

import '../data/models/product_model.dart';
import '../data/models/product_sku_model.dart';


import '../../cart/data/cart_repository.dart';
import '../../cart/data/models/cart_item_model.dart';

import '../data/product_repository.dart';
import '../data/product_sku_repository.dart';

import 'widgets/detail_bottom.dart';
import 'widgets/detail_description.dart';
import 'widgets/detail_empty.dart';
import 'widgets/detail_error.dart';
import 'widgets/detail_header.dart';
import 'widgets/detail_info.dart';
import 'widgets/detail_loading.dart';
import 'widgets/detail_sku_selector.dart';

class ProductDetailPage extends StatefulWidget {
  const ProductDetailPage({
    super.key,
    required this.slug,
  });

  final String slug;

  @override
  State<ProductDetailPage> createState() =>
      _ProductDetailPageState();
}

class _ProductDetailPageState
    extends State<ProductDetailPage> {
  /*
  |--------------------------------------------------------------------------
  | Repositories
  |--------------------------------------------------------------------------
  */

  final ProductRepository _repository =
      ProductRepository();

  final ProductSkuRepository _skuRepository =
      ProductSkuRepository();

  final CartRepository _cartRepository =
      CartRepository();

  /*
  |--------------------------------------------------------------------------
  | State
  |--------------------------------------------------------------------------
  */

  late Future<ProductModel> _future;

  List<ProductSkuModel> _skus = [];

  ProductSkuModel? _selectedSku;

  Map<String, String> _selectedOptions =
      {};

  @override
  void initState() {
    super.initState();

    _future = _loadProduct();
  }

  /*
  |--------------------------------------------------------------------------
  | Product
  |--------------------------------------------------------------------------
  */

  Future<ProductModel> _loadProduct() async {
    final product =
        await _repository.getProductDetail(
      widget.slug,
    );

    await _loadSkus(
      product.id,
    );

    return product;
  }

  /*
  |--------------------------------------------------------------------------
  | SKU
  |--------------------------------------------------------------------------
  */

  Future<void> _loadSkus(
    int productId,
  ) async {
    try {
      final skus =
          await _skuRepository.getByProduct(
        productId,
      );

      if (!mounted) return;

      ProductSkuModel? selected;

      if (skus.isNotEmpty) {
        selected = skus.firstWhere(
          (e) => e.isDefault,
          orElse: () => skus.first,
        );
      }

      final selectedOptions =
          <String, String>{};

      if (selected != null) {
        for (final option
            in selected.optionValues) {
          if (option.option != null) {
            selectedOptions[
                    option.option!] =
                option.name;
          }
        }
      }

      setState(() {
        _skus = skus;
        _selectedSku = selected;
        _selectedOptions =
            selectedOptions;
      });
    } catch (_) {
      // sementara diabaikan
    }
  }

  /*
  |--------------------------------------------------------------------------
  | SKU Matrix Engine
  |--------------------------------------------------------------------------
  */

  void _selectOption(
    String optionName,
    String value,
  ) {
    final updated =
        Map<String, String>.from(
      _selectedOptions,
    );

    updated[optionName] = value;

    ProductSkuModel? matchedSku;

    for (final sku in _skus) {
      bool matches = true;

      for (final entry
          in updated.entries) {
        final exists =
            sku.optionValues.any(
          (option) =>
              option.option ==
                  entry.key &&
              option.name ==
                  entry.value,
        );

        if (!exists) {
          matches = false;
          break;
        }
      }

      if (matches) {
        matchedSku = sku;
        break;
      }
    }

    setState(() {
      _selectedOptions = updated;

      if (matchedSku != null) {
        _selectedSku = matchedSku;
      }
    });
  }

  /*
  |--------------------------------------------------------------------------
  | Cart
  |--------------------------------------------------------------------------
  */

  Future<void> _addToCart(
    ProductModel product,
  ) async {
    if (_selectedSku == null) {
      ScaffoldMessenger.of(context)
          .showSnackBar(
        const SnackBar(
          content: Text(
            'Silakan pilih varian terlebih dahulu',
          ),
        ),
      );

      return;
    }

    await _cartRepository.addItem(
      CartItemModel(
        productId: product.id,
        skuId: _selectedSku!.id,
        productName: product.name,
        image: product.image,
        sku: _selectedSku!.sku,
        priceFormatted:
            _selectedSku!
                .priceFormatted,
        price: _selectedSku!.price,
        quantity: 1,
      ),
    );

    if (!mounted) return;

    ScaffoldMessenger.of(context)
        .showSnackBar(
      SnackBar(
        content: Text(
          '${product.name} berhasil ditambahkan ke keranjang',
        ),
      ),
    );
  }

  /*
  |--------------------------------------------------------------------------
  | Retry
  |--------------------------------------------------------------------------
  */

  void _retry() {
    setState(() {
      _future = _loadProduct();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor:
          const Color(0xFFF5F5F5),
      body: FutureBuilder<ProductModel>(
        future: _future,
        builder: (context, snapshot) {
          /*
          |--------------------------------------------------------------------------
          | Loading
          |--------------------------------------------------------------------------
          */

          if (snapshot.connectionState ==
              ConnectionState.waiting) {
            return const DetailLoading();
          }

          /*
          |--------------------------------------------------------------------------
          | Error
          |--------------------------------------------------------------------------
          */

          if (snapshot.hasError) {
            return DetailError(
              onRetry: _retry,
            );
          }

          /*
          |--------------------------------------------------------------------------
          | Empty
          |--------------------------------------------------------------------------
          */

          if (!snapshot.hasData) {
            return const DetailEmpty();
          }

          final product =
              snapshot.data!;

          return Column(
            children: [
              Expanded(
                child:
                    SingleChildScrollView(
                  child: Column(
                    children: [
                      DetailHeader(
                        product: product,
                      ),

                      DetailInfo(
                        product: product,
                        selectedSku:
                            _selectedSku,
                      ),

                      DetailSkuSelector(
                        skus: _skus,
                        selectedSku:
                            _selectedSku,
                        selectedOptions:
                            _selectedOptions,
                        onSelected:
                            _selectOption,
                      ),

                      DetailDescription(
                        product: product,
                      ),

                      const SizedBox(
                        height: 100,
                      ),
                    ],
                  ),
                ),
              ),

              DetailBottomBar(
                product: product,
                selectedSku:
                    _selectedSku,
                onAddToCart: () {
                  _addToCart(
                    product,
                  );
                },
              ),
            ],
          );
        },
      ),
    );
  }
}