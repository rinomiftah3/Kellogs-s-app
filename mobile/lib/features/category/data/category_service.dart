import '../../../core/network/api_endpoints.dart';
import '../../../core/network/dio_client.dart';

import 'models/category_model.dart';

class CategoryService {
  Future<List<CategoryModel>> getCategories() async {
    final response = await DioClient.dio.get(
      ApiEndpoints.categories,
    );

    /*
    Backend:
    {
      success,
      message,
      data: [...]
    }
    */

    final List<dynamic> categories =
        response.data['data'] ?? [];

    return categories
        .map(
          (json) => CategoryModel.fromJson(
            json,
          ),
        )
        .toList();
  }
}