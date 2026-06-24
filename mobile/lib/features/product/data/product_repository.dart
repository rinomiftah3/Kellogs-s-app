import 'product_service.dart';

import 'models/product_model.dart';

class ProductRepository {
  final ProductService _service =
      ProductService();

  /*
  |--------------------------------------------------------------------------
  | Home Products
  |--------------------------------------------------------------------------
  */

  Future<List<ProductModel>>
      getHomeProducts() async {
    final products =
        await _service.getProducts();

    final filteredProducts = products
        .where(
          (product) =>
              product.isActive &&
              product.isPublished &&
              product.isFeatured,
        )
        .toList();

    /*
    |--------------------------------------------------------------------------
    | Featured terlebih dahulu
    |--------------------------------------------------------------------------
    */

    filteredProducts.sort(
      (a, b) {
        if (a.isFeatured ==
            b.isFeatured) {
          return b.id.compareTo(a.id);
        }

        return a.isFeatured
            ? -1
            : 1;
      },
    );

    return filteredProducts;
  }

  /*
  |--------------------------------------------------------------------------
  | Semua Produk
  |--------------------------------------------------------------------------
  */

  Future<List<ProductModel>>
      getProducts() async {
    final products =
        await _service.getProducts();

    return products
        .where(
          (product) =>
              product.isActive &&
              product.isPublished,
        )
        .toList();
  }

  /*
  |--------------------------------------------------------------------------
  | Detail Produk
  |--------------------------------------------------------------------------
  */

  Future<ProductModel> getProductDetail(
    String slug,
  ) async {
    return await _service.getProductDetail(
      slug,
    );
  }
}