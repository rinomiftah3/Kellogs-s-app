import 'models/product_sku_model.dart';
import 'product_sku_service.dart';

class ProductSkuRepository {
  final ProductSkuService _service =
      ProductSkuService();

  Future<List<ProductSkuModel>> getByProduct(
    int productId,
  ) async {
    return await _service.getByProduct(
      productId,
    );
  }
}