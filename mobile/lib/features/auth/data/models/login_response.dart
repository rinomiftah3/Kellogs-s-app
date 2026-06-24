import 'user_model.dart';

class LoginResponse {
  final bool success;

  final String message;

  final UserModel user;

  final String token;

  final dynamic meta;

  final String timestamp;

  final String requestId;

  const LoginResponse({
    required this.success,
    required this.message,
    required this.user,
    required this.token,
    required this.meta,
    required this.timestamp,
    required this.requestId,
  });

  factory LoginResponse.fromJson(
    Map<String, dynamic> json,
  ) {
    final data =
        json['data'] as Map<String, dynamic>;

    return LoginResponse(
      success: json['success'] ?? false,

      message: json['message'] ?? '',

      user: UserModel.fromJson(
        data['user'],
      ),

      token: data['token'] ?? '',

      meta: json['meta'],

      timestamp:
          json['timestamp'] ?? '',

      requestId:
          json['request_id'] ?? '',
    );
  }
}