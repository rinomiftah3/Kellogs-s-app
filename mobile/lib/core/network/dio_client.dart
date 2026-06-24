import 'package:dio/dio.dart';

import '../storage/secure_storage.dart';
import 'api_endpoints.dart';

class DioClient {
  DioClient._();

  static final Dio dio = Dio(
    BaseOptions(
      baseUrl: ApiEndpoints.baseUrl,

      connectTimeout:
          const Duration(seconds: 30),

      receiveTimeout:
          const Duration(seconds: 30),

      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
    ),
  )
    ..interceptors.add(
      InterceptorsWrapper(
        onRequest: (
          options,
          handler,
        ) async {
          final token =
              await SecureStorage.getToken();

          if (token != null) {
            options.headers['Authorization'] =
                'Bearer $token';
          }

          handler.next(options);
        },
      ),
    )
    ..interceptors.add(
      LogInterceptor(
        requestBody: true,
        responseBody: true,
      ),
    );
}