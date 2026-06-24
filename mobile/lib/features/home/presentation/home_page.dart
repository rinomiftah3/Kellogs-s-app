import 'package:flutter/material.dart';

import '../../category/presentation/widgets/category_section.dart';
import '../../product/presentation/widgets/product_section.dart';

import 'widgets/banner_placeholder.dart';
import 'widgets/home_bottom_nav.dart';
import 'widgets/home_header.dart';
import 'widgets/search_bar_widget.dart';
import 'widgets/shortcut_menu.dart';

class HomePage extends StatefulWidget {
  const HomePage({super.key});

  @override
  State<HomePage> createState() =>
      _HomePageState();
}

class _HomePageState extends State<HomePage> {
  Key _categoryKey = UniqueKey();

  Key _productKey = UniqueKey();

  Future<void> _refresh() async {
    setState(() {
      _categoryKey = UniqueKey();

      _productKey = UniqueKey();
    });

    await Future.delayed(
      const Duration(
        milliseconds: 600,
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor:
          const Color(0xFFF5F5F5),

      body: RefreshIndicator(
        color: const Color(
          0xFFD5001C,
        ),
        onRefresh: _refresh,
        child: SingleChildScrollView(
          physics:
              const AlwaysScrollableScrollPhysics(),
          child: Column(
            children: [
              const HomeHeader(),

              const SizedBox(height: 12),

              const SearchBarWidget(),

              const SizedBox(height: 16),

              const BannerPlaceholder(),

              const SizedBox(height: 20),

              const ShortcutMenu(),

              const SizedBox(height: 24),

              CategorySection(
                key: _categoryKey,
              ),

              const SizedBox(height: 24),

              ProductSection(
                key: _productKey,
              ),

              const SizedBox(height: 24),
            ],
          ),
        ),
      ),

      bottomNavigationBar:
          const HomeBottomNav(),
    );
  }
}