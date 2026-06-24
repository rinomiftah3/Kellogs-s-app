import '../../../core/network/api_endpoints.dart';
import '../../../core/network/dio_client.dart';

import 'models/product_model.dart';

class ProductService {
  Future<List<ProductModel>> getProducts() async {
    final response = await DioClient.dio.get(
      ApiEndpoints.products,
    );

    /*
    |--------------------------------------------------------------------------
    | Backend Response
    |--------------------------------------------------------------------------
    |
    | {
    |   success,
    |   message,
    |   data: [...]
    | }
    |
    */

    final List<dynamic> products =
        response.data['data'] ?? [];

    return products
        .map(
          (json) => ProductModel.fromJson(
            json,
          ),
        )
        .toList();
  }

  Future<ProductModel> getProductDetail(
    String slug,
  ) async {
    final response = await DioClient.dio.get(
      '${ApiEndpoints.products}/$slug',
    );

    /*
    |--------------------------------------------------------------------------
    | Backend Response
    |--------------------------------------------------------------------------
    |
    | {
    |   success,
    |   message,
    |   data: {...}
    | }
    |
    */

    return ProductModel.fromJson(
      response.data['data'],
    );
  }
}