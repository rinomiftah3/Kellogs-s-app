import 'package:dio/dio.dart';

import '../../../core/network/api_endpoints.dart';
import '../../../core/network/dio_client.dart';

import 'models/product_sku_model.dart';

class ProductSkuService {
  Future<List<ProductSkuModel>> getByProduct(
    int productId,
  ) async {
    final response = await DioClient.dio.get(
      ApiEndpoints.productSkus,
      queryParameters: {
        'product_id': productId,
      },
    );

    final data = response.data['data'];

    /*
    |--------------------------------------------------------------------------
    | Laravel Resource Collection
    |--------------------------------------------------------------------------
    */

    List<dynamic> items;

    if (data is List) {
      items = data;
    } else if (data is Map &&
        data.containsKey('data')) {
      items = data['data'];
    } else {
      items = [];
    }

    return items
        .map(
          (e) => ProductSkuModel.fromJson(
            e,
          ),
        )
        .toList();
  }
}