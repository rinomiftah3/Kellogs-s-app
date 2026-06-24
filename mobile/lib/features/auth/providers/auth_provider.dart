import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../data/auth_repository.dart';
import '../data/models/user_model.dart';

final authRepositoryProvider =
    Provider<AuthRepository>(
  (ref) => AuthRepository(),
);

final currentUserProvider =
    FutureProvider<UserModel?>(
  (ref) async {
    final repository =
        ref.read(authRepositoryProvider);

    try {
      return await repository.me();
    } catch (_) {
      return null;
    }
  },
);