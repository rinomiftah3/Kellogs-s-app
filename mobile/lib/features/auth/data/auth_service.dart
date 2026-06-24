import '../../../core/network/api_endpoints.dart';
import '../../../core/network/dio_client.dart';

import 'models/login_request.dart';
import 'models/login_response.dart';
import 'models/user_model.dart';

class AuthService {
  Future<LoginResponse> login(
    LoginRequest request,
  ) async {
    final response = await DioClient.dio.post(
      ApiEndpoints.login,
      data: request.toJson(),
    );

    return LoginResponse.fromJson(
      response.data,
    );
  }

  Future<UserModel> me() async {
    final response = await DioClient.dio.get(
      ApiEndpoints.me,
    );

    /*
    Backend /me biasanya:
    {
      success,
      message,
      data: {
        ...
      }
    }
    */

    return UserModel.fromJson(
      response.data['data'],
    );
  }
}