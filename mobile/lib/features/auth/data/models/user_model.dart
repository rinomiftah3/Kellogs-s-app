class UserModel {
  final int id;
  final String name;
  final String email;

  final String? avatar;
  final String? avatarUrl;

  final List<String> roles;
  final List<String> permissions;

  final bool isActive;
  final bool isVerified;

  final String? emailVerifiedAt;
  final String? lastLoginAt;

  const UserModel({
    required this.id,
    required this.name,
    required this.email,
    this.avatar,
    this.avatarUrl,
    required this.roles,
    required this.permissions,
    required this.isActive,
    required this.isVerified,
    this.emailVerifiedAt,
    this.lastLoginAt,
  });

  factory UserModel.fromJson(
    Map<String, dynamic> json,
  ) {
    return UserModel(
      id: json['id'] ?? 0,

      name: json['name'] ?? '',

      email: json['email'] ?? '',

      avatar: json['avatar'],

      avatarUrl: json['avatar_url'],

      roles: List<String>.from(
        json['roles'] ?? [],
      ),

      permissions: List<String>.from(
        json['permissions'] ?? [],
      ),

      isActive: json['is_active'] ?? false,

      isVerified: json['is_verified'] ?? false,

      emailVerifiedAt:
          json['email_verified_at'],

      lastLoginAt:
          json['last_login_at'],
    );
  }
}