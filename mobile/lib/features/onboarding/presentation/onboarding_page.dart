import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

class OnboardingPage extends StatefulWidget {
  const OnboardingPage({super.key});

  @override
  State<OnboardingPage> createState() => _OnboardingPageState();
}

class _OnboardingPageState extends State<OnboardingPage> {
  final PageController _pageController = PageController();

  int _currentPage = 0;

  final List<Map<String, dynamic>> _pages = [
    {
      'icon': Icons.breakfast_dining,
      'title': 'Sereal Favorit\nLebih Mudah Didapat',
      'description':
          'Temukan berbagai produk Kellogg\'s original dengan harga terbaik.',
    },
    {
      'icon': Icons.delivery_dining,
      'title': 'Pengiriman Cepat\n& Aman',
      'description':
          'Pesanan kamu akan dikirim cepat langsung ke rumahmu.',
    },
    {
      'icon': Icons.account_balance_wallet,
      'title': 'Pembayaran Aman\n& Praktis',
      'description':
          'Berbagai metode pembayaran aman, mudah dan terpercaya.',
    },
  ];

  void _nextPage() {
    if (_currentPage < _pages.length - 1) {
      _pageController.nextPage(
        duration: const Duration(milliseconds: 300),
        curve: Curves.easeInOut,
      );
    } else {
      context.go('/login');
    }
  }

  void _skip() {
    context.go('/login');
  }

  @override
  void dispose() {
    _pageController.dispose();
    super.dispose();
  }

  Widget _buildDot(int index) {
    final isActive = index == _currentPage;

    return AnimatedContainer(
      duration: const Duration(milliseconds: 300),
      margin: const EdgeInsets.symmetric(horizontal: 4),
      width: isActive ? 20 : 8,
      height: 8,
      decoration: BoxDecoration(
        color: isActive
            ? const Color(0xFFE30613)
            : Colors.grey.shade300,
        borderRadius: BorderRadius.circular(20),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,

      body: SafeArea(
        child: Column(
          children: [
            Align(
              alignment: Alignment.centerRight,
              child: TextButton(
                onPressed: _skip,
                child: const Text(
                  'Lewati',
                  style: TextStyle(
                    color: Colors.black87,
                  ),
                ),
              ),
            ),

            Expanded(
              child: PageView.builder(
                controller: _pageController,
                itemCount: _pages.length,

                onPageChanged: (index) {
                  setState(() {
                    _currentPage = index;
                  });
                },

                itemBuilder: (context, index) {
                  final page = _pages[index];

                  return Padding(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 32,
                    ),
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        CircleAvatar(
                          radius: 90,
                          backgroundColor:
                              const Color(0xFFFDECEE),

                          child: Icon(
                            page['icon'],
                            size: 90,
                            color: const Color(0xFFE30613),
                          ),
                        ),

                        const SizedBox(height: 40),

                        Text(
                          page['title'],
                          textAlign: TextAlign.center,
                          style: const TextStyle(
                            fontSize: 28,
                            fontWeight: FontWeight.bold,
                            color: Colors.black87,
                            height: 1.3,
                          ),
                        ),

                        const SizedBox(height: 20),

                        Text(
                          page['description'],
                          textAlign: TextAlign.center,
                          style: TextStyle(
                            fontSize: 15,
                            color: Colors.grey.shade600,
                            height: 1.5,
                          ),
                        ),
                      ],
                    ),
                  );
                },
              ),
            ),

            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: List.generate(
                _pages.length,
                _buildDot,
              ),
            ),

            const SizedBox(height: 32),

            Padding(
              padding: const EdgeInsets.symmetric(
                horizontal: 24,
              ),
              child: SizedBox(
                width: double.infinity,
                height: 52,
                child: ElevatedButton(
                  onPressed: _nextPage,

                  style: ElevatedButton.styleFrom(
                    backgroundColor:
                        const Color(0xFFE30613),

                    shape: RoundedRectangleBorder(
                      borderRadius:
                          BorderRadius.circular(12),
                    ),
                  ),

                  child: Text(
                    _currentPage == _pages.length - 1
                        ? 'Mulai'
                        : 'Selanjutnya',
                    style: const TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ),
              ),
            ),

            const SizedBox(height: 32),
          ],
        ),
      ),
    );
  }
}