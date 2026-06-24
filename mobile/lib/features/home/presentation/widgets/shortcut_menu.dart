import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

class ShortcutMenu extends StatelessWidget {
  const ShortcutMenu({super.key});

  @override
  Widget build(BuildContext context) {
    final menus = [
      {
        'title': 'Voucher',
        'icon': Icons.confirmation_num_outlined,
        'route': '/profile/vouchers',
      },
      {
        'title': 'Flash Sale',
        'icon': Icons.flash_on,
        'route': '/products',
      },
      {
        'title': 'Reward',
        'icon': Icons.card_giftcard,
        'route': '/profile',
      },
      {
        'title': 'Gratis Ongkir',
        'icon': Icons.local_shipping_outlined,
        'route': '/checkout',
      },
    ];

    return Padding(
      padding: const EdgeInsets.symmetric(
        horizontal: 16,
      ),
      child: Row(
        mainAxisAlignment:
            MainAxisAlignment.spaceBetween,
        children: menus.map((menu) {
          return _ShortcutItem(
            title: menu['title'] as String,
            icon: menu['icon'] as IconData,
            onTap: () {
              context.go(
                menu['route'] as String,
              );
            },
          );
        }).toList(),
      ),
    );
  }
}

class _ShortcutItem extends StatelessWidget {
  final String title;
  final IconData icon;
  final VoidCallback onTap;

  const _ShortcutItem({
    required this.title,
    required this.icon,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return InkWell(
      borderRadius:
          BorderRadius.circular(16),
      onTap: onTap,
      child: SizedBox(
        width: 72,
        child: Column(
          children: [
            Container(
              width: 56,
              height: 56,
              decoration: BoxDecoration(
                color: const Color(
                  0xFFFFEEF1,
                ),
                borderRadius:
                    BorderRadius.circular(
                  16,
                ),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black
                        .withOpacity(0.04),
                    blurRadius: 8,
                    offset:
                        const Offset(0, 3),
                  ),
                ],
              ),
              child: Icon(
                icon,
                color: const Color(
                  0xFFD5001C,
                ),
                size: 28,
              ),
            ),

            const SizedBox(height: 8),

            Text(
              title,
              textAlign: TextAlign.center,
              maxLines: 2,
              overflow:
                  TextOverflow.ellipsis,
              style: const TextStyle(
                fontSize: 12,
                fontWeight:
                    FontWeight.w500,
                color: Color(
                  0xFF333333,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}