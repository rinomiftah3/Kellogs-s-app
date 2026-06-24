import 'package:flutter/material.dart';

import 'core/router/app_router.dart';
import 'core/theme/app_theme.dart';

class KelloggsApp extends StatelessWidget {
  const KelloggsApp({
    super.key,
  });

  @override
  Widget build(BuildContext context) {
    return MaterialApp.router(
      title: 'Kellogg\'s App',

      debugShowCheckedModeBanner: false,

      theme: AppTheme.lightTheme,

      routerConfig: AppRouter.router,
    );
  }
}