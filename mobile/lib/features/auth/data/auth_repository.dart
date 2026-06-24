import '../../../core/storage/secure_storage.dart';

import 'auth_service.dart';

import 'models/login_request.dart';
import 'models/login_response.dart';
import 'models/user_model.dart';

class AuthRepository {
  final AuthService _service = AuthService();

  Future<LoginResponse> login(
    LoginRequest request,
  ) async {
    final response = await _service.login(
      request,
    );

    await SecureStorage.saveToken(
      response.token,
    );

    return response;
  }

  Future<UserModel> me() async {
    return await _service.me();
  }

  Future<void> logout() async {
    await SecureStorage.deleteToken();
  }
}