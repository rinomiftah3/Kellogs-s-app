import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

import '../data/auth_repository.dart';
import '../data/models/login_request.dart';

class LoginPage extends StatefulWidget {
  const LoginPage({super.key});

  @override
  State<LoginPage> createState() => _LoginPageState();
}

class _LoginPageState extends State<LoginPage> {
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();

  final AuthRepository _repository = AuthRepository();

  bool _obscurePassword = true;
  bool _isLoading = false;

  Future<void> _login() async {
    FocusScope.of(context).unfocus();

    if (_emailController.text.trim().isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text(
            'Email wajib diisi',
          ),
        ),
      );

      return;
    }

    if (_passwordController.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text(
            'Password wajib diisi',
          ),
        ),
      );

      return;
    }

    try {
      setState(() {
        _isLoading = true;
      });

      final response = await _repository.login(
        LoginRequest(
          email: _emailController.text.trim(),
          password: _passwordController.text,
        ),
      );

      if (!mounted) return;

      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            response.message,
          ),
        ),
      );

      context.go('/home');
    } catch (e) {
      if (!mounted) return;

      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            'Login gagal.\n${e.toString()}',
          ),
        ),
      );
    } finally {
      if (mounted) {
        setState(() {
          _isLoading = false;
        });
      }
    }
  }

  @override
  void dispose() {
    _emailController.dispose();
    _passwordController.dispose();

    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,

      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.symmetric(
            horizontal: 24,
            vertical: 24,
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const SizedBox(height: 20),

              /*
              |--------------------------------------------------------------------------
              | Logo
              |--------------------------------------------------------------------------
              */

              Image.asset(
                'assets/images/logo_red.png',
                height: 45,
              ),

              const SizedBox(height: 50),

              /*
              |--------------------------------------------------------------------------
              | Welcome
              |--------------------------------------------------------------------------
              */

              const Text(
                'Selamat Datang 👋',
                style: TextStyle(
                  fontSize: 28,
                  fontWeight: FontWeight.bold,
                ),
              ),

              const SizedBox(height: 8),

              Text(
                'Masuk untuk melanjutkan',
                style: TextStyle(
                  fontSize: 15,
                  color: Colors.grey.shade600,
                ),
              ),

              const SizedBox(height: 40),

              /*
              |--------------------------------------------------------------------------
              | Email
              |--------------------------------------------------------------------------
              */

              TextField(
                controller: _emailController,
                keyboardType: TextInputType.emailAddress,
                enabled: !_isLoading,
                decoration: InputDecoration(
                  hintText: 'Email / Nomor HP',
                  prefixIcon: const Icon(
                    Icons.mail_outline,
                  ),
                  border: OutlineInputBorder(
                    borderRadius:
                        BorderRadius.circular(
                      12,
                    ),
                  ),
                ),
              ),

              const SizedBox(height: 16),

              /*
              |--------------------------------------------------------------------------
              | Password
              |--------------------------------------------------------------------------
              */

              TextField(
                controller: _passwordController,
                obscureText: _obscurePassword,
                enabled: !_isLoading,
                decoration: InputDecoration(
                  hintText: 'Password',
                  prefixIcon: const Icon(
                    Icons.lock_outline,
                  ),
                  suffixIcon: IconButton(
                    onPressed: () {
                      setState(() {
                        _obscurePassword =
                            !_obscurePassword;
                      });
                    },
                    icon: Icon(
                      _obscurePassword
                          ? Icons
                              .visibility_off_outlined
                          : Icons
                              .visibility_outlined,
                    ),
                  ),
                  border: OutlineInputBorder(
                    borderRadius:
                        BorderRadius.circular(
                      12,
                    ),
                  ),
                ),
              ),

              const SizedBox(height: 12),

              /*
              |--------------------------------------------------------------------------
              | Forgot Password
              |--------------------------------------------------------------------------
              */

              Align(
                alignment: Alignment.centerRight,
                child: TextButton(
                  onPressed: _isLoading
                      ? null
                      : () {
                          context.go(
                            '/forgot-password',
                          );
                        },
                  child: const Text(
                    'Lupa Password?',
                  ),
                ),
              ),

              const SizedBox(height: 20),

              /*
              |--------------------------------------------------------------------------
              | Login Button
              |--------------------------------------------------------------------------
              */

              SizedBox(
                width: double.infinity,
                height: 52,
                child: ElevatedButton(
                  onPressed: _isLoading
                      ? null
                      : _login,
                  child: _isLoading
                      ? const SizedBox(
                          height: 22,
                          width: 22,
                          child:
                              CircularProgressIndicator(
                            strokeWidth: 2.5,
                            color: Colors.white,
                          ),
                        )
                      : const Text(
                          'Masuk',
                        ),
                ),
              ),

              const SizedBox(height: 30),

              /*
              |--------------------------------------------------------------------------
              | Divider
              |--------------------------------------------------------------------------
              */

              Row(
                children: [
                  Expanded(
                    child: Divider(
                      color: Colors.grey.shade300,
                    ),
                  ),
                  const Padding(
                    padding: EdgeInsets.symmetric(
                      horizontal: 12,
                    ),
                    child: Text(
                      'atau masuk dengan',
                    ),
                  ),
                  Expanded(
                    child: Divider(
                      color: Colors.grey.shade300,
                    ),
                  ),
                ],
              ),

              const SizedBox(height: 24),

              /*
              |--------------------------------------------------------------------------
              | Social Login
              |--------------------------------------------------------------------------
              */

              Row(
                children: [
                  Expanded(
                    child: OutlinedButton.icon(
                      onPressed: _isLoading
                          ? null
                          : () {},
                      icon: const Icon(
                        Icons.g_mobiledata,
                      ),
                      label: const Text(
                        'Google',
                      ),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: OutlinedButton.icon(
                      onPressed: _isLoading
                          ? null
                          : () {},
                      icon: const Icon(
                        Icons.facebook,
                      ),
                      label: const Text(
                        'Facebook',
                      ),
                    ),
                  ),
                ],
              ),

              const SizedBox(height: 40),

              /*
              |--------------------------------------------------------------------------
              | Register
              |--------------------------------------------------------------------------
              */

              Row(
                mainAxisAlignment:
                    MainAxisAlignment.center,
                children: [
                  const Text(
                    'Belum punya akun?',
                  ),
                  TextButton(
                    onPressed: _isLoading
                        ? null
                        : () {
                            context.go(
                              '/register',
                            );
                          },
                    child: const Text(
                      'Daftar',
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }
}