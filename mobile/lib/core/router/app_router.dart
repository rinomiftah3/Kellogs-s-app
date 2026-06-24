import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

import '../../features/splash/presentation/splash_page.dart';
import '../../features/onboarding/presentation/onboarding_page.dart';

import '../../features/auth/presentation/login_page.dart';
import '../../features/auth/presentation/register_page.dart';
import '../../features/auth/presentation/forgot_password_page.dart';

import '../../features/home/presentation/home_page.dart';

import '../../features/category/presentation/category_page.dart';

import '../../features/product/presentation/product_page.dart';
import '../../features/product/presentation/product_detail_page.dart';

import '../../features/wishlist/presentation/wishlist_page.dart';

import '../../features/cart/presentation/cart_page.dart';

import '../../features/checkout/presentation/checkout_page.dart';
import '../../features/checkout/presentation/checkout_success_page.dart';
import '../../features/payment/presentation/payment_page.dart';
import '../../features/payment/presentation/payment_webview_page.dart';

import '../../features/orders/presentation/orders_page.dart';
import '../../features/orders/presentation/order_detail_page.dart';
import '../../features/orders/presentation/order_tracking_page.dart';

import '../../features/profile/presentation/profile_page.dart';
import '../../features/profile/presentation/address_page.dart';
import '../../features/profile/presentation/voucher_page.dart';
import '../../features/profile/presentation/settings_page.dart';

class AppRouter {
  static final router = GoRouter(
    initialLocation: '/',

    routes: [
      /*
      |--------------------------------------------------------------------------
      | Public
      |--------------------------------------------------------------------------
      */

      GoRoute(
        path: '/',
        builder: (_, __) => const SplashPage(),
      ),

      GoRoute(
        path: '/onboarding',
        builder: (_, __) => const OnboardingPage(),
      ),

      GoRoute(
        path: '/login',
        builder: (_, __) => const LoginPage(),
      ),

      GoRoute(
        path: '/register',
        builder: (_, __) => const RegisterPage(),
      ),

      GoRoute(
        path: '/forgot-password',
        builder: (_, __) => const ForgotPasswordPage(),
      ),

      /*
      |--------------------------------------------------------------------------
      | Home
      |--------------------------------------------------------------------------
      */

      GoRoute(
        path: '/home',
        builder: (_, __) => const HomePage(),
      ),

      /*
      |--------------------------------------------------------------------------
      | Categories
      |--------------------------------------------------------------------------
      */

      GoRoute(
        path: '/categories',
        builder: (_, __) => const CategoryPage(),
      ),

      /*
      |--------------------------------------------------------------------------
      | Products
      |--------------------------------------------------------------------------
      */

      GoRoute(
        path: '/products',
        builder: (_, __) => const ProductPage(),
      ),

      GoRoute(
        path: '/products/:slug',
        builder: (_, state) {
          final slug =
              state.pathParameters['slug']!;

          return ProductDetailPage(
            slug: slug,
          );
        },
      ),

      /*
      |--------------------------------------------------------------------------
      | Wishlist
      |--------------------------------------------------------------------------
      */

      GoRoute(
        path: '/wishlist',
        builder: (_, __) => const WishlistPage(),
      ),

      /*
      |--------------------------------------------------------------------------
      | Cart
      |--------------------------------------------------------------------------
      */

      GoRoute(
        path: '/cart',
        builder: (_, __) => const CartPage(),
      ),

      /*
      |--------------------------------------------------------------------------
      | Checkout
      |--------------------------------------------------------------------------
      */

      GoRoute(
        path: '/checkout',
        builder: (_, __) => const CheckoutPage(),
      ),
      GoRoute(
        path: '/checkout-success',
        builder: (_, __) =>
            const CheckoutSuccessPage(),
      ),
      /*
      |--------------------------------------------------------------------------
      | Payments
      |--------------------------------------------------------------------------
      */

      GoRoute(
        path: '/payment',
        builder: (_, __) => const PaymentPage(),
      ),

      GoRoute(
        path: '/payment/webview',
        builder: (_, state) {
          final url =
              state.uri.queryParameters['url'] ?? '';

          return PaymentWebviewPage(
            paymentUrl: url,
          );
        },
      ),

      /*
      |--------------------------------------------------------------------------
      | Orders
      |--------------------------------------------------------------------------
      */

      GoRoute(
        path: '/orders',
        builder: (_, __) => const OrdersPage(),
      ),

      GoRoute(
        path: '/orders/:orderNumber',
        builder: (_, state) {
          return OrderDetailPage(
            orderNumber:
                state.pathParameters['orderNumber']!,
          );
        },
      ),

      GoRoute(
        path: '/orders/:orderNumber/tracking',
        builder: (_, state) {
          return OrderTrackingPage(
            orderNumber:
                state.pathParameters['orderNumber']!,
          );
        },
      ),

      /*
      |--------------------------------------------------------------------------
      | Profile
      |--------------------------------------------------------------------------
      */

      GoRoute(
        path: '/profile',
        builder: (_, __) => const ProfilePage(),
      ),

      GoRoute(
        path: '/profile/address',
        builder: (_, __) => const AddressPage(),
      ),

      GoRoute(
        path: '/profile/vouchers',
        builder: (_, __) => const VoucherPage(),
      ),

      GoRoute(
        path: '/settings',
        builder: (_, __) => const SettingsPage(),
      ),
    ],

    errorBuilder: (_, __) {
      return const Scaffold(
        body: Center(
          child: Text(
            '404 - Page Not Found',
          ),
        ),
      );
    },
  );
}