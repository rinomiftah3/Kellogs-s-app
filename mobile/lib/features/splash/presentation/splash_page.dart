import 'dart:async';

import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

import '../../../core/storage/secure_storage.dart';
import '../../auth/data/auth_repository.dart';

class SplashPage extends StatefulWidget {
  const SplashPage({super.key});

  @override
  State<SplashPage> createState() => _SplashPageState();
}

class _SplashPageState extends State<SplashPage> {
  final AuthRepository _repository =
      AuthRepository();

  @override
  void initState() {
    super.initState();

    _navigate();
  }

  Future<void> _navigate() async {
    /*
    |--------------------------------------------------------------------------
    | Splash Delay
    |--------------------------------------------------------------------------
    */

    await Future.delayed(
      const Duration(seconds: 3),
    );

    /*
    |--------------------------------------------------------------------------
    | Check Token
    |--------------------------------------------------------------------------
    */

    final token =
        await SecureStorage.getToken();

    if (!mounted) return;

    /*
    |--------------------------------------------------------------------------
    | Belum Login
    |--------------------------------------------------------------------------
    */

    if (token == null || token.isEmpty) {
      context.go('/onboarding');
      return;
    }

    /*
    |--------------------------------------------------------------------------
    | Token Ada → Validasi ke Backend
    |--------------------------------------------------------------------------
    */

    try {
      await _repository.me();

      if (!mounted) return;

      context.go('/home');
    } catch (_) {
      /*
      |--------------------------------------------------------------------------
      | Token Invalid
      |--------------------------------------------------------------------------
      */

      await _repository.logout();

      if (!mounted) return;

      context.go('/login');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Container(
        width: double.infinity,
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
            colors: [
              Color(0xFFD5001C),
              Color(0xFFE30613),
            ],
          ),
        ),
        child: Stack(
          children: [
            /*
            |--------------------------------------------------------------------------
            | Background Ornament
            |--------------------------------------------------------------------------
            */

            Positioned(
              bottom: -50,
              left: -50,
              child: Opacity(
                opacity: 0.06,
                child: Icon(
                  Icons.breakfast_dining,
                  size: 220,
                  color: Colors.white,
                ),
              ),
            ),

            Positioned(
              bottom: 40,
              right: -30,
              child: Opacity(
                opacity: 0.05,
                child: Icon(
                  Icons.local_grocery_store,
                  size: 180,
                  color: Colors.white,
                ),
              ),
            ),

            /*
            |--------------------------------------------------------------------------
            | Main Content
            |--------------------------------------------------------------------------
            */

            SafeArea(
              child: Center(
                child: Padding(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 32,
                  ),
                  child: Column(
                    mainAxisAlignment:
                        MainAxisAlignment.center,
                    children: [
                      /*
                      |--------------------------------------------------------------------------
                      | Logo
                      |--------------------------------------------------------------------------
                      */

                      Image.asset(
                        'assets/images/logo_white.png',
                        width: 220,
                        fit: BoxFit.contain,
                      ),

                      const SizedBox(height: 48),

                      /*
                      |--------------------------------------------------------------------------
                      | Slogan
                      |--------------------------------------------------------------------------
                      */

                      const Text(
                        'Better Days\nStart with Kellogg\'s',
                        textAlign: TextAlign.center,
                        style: TextStyle(
                          color: Colors.white,
                          fontSize: 26,
                          fontWeight:
                              FontWeight.w700,
                          height: 1.4,
                        ),
                      ),

                      const SizedBox(height: 60),

                      /*
                      |--------------------------------------------------------------------------
                      | Loading Indicator
                      |--------------------------------------------------------------------------
                      */

                      const SizedBox(
                        width: 28,
                        height: 28,
                        child:
                            CircularProgressIndicator(
                          strokeWidth: 2.5,
                          valueColor:
                              AlwaysStoppedAnimation<
                                  Color>(
                            Colors.white,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}