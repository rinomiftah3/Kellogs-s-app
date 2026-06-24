import 'package:carousel_slider/carousel_slider.dart';
import 'package:flutter/material.dart';

class BannerPlaceholder extends StatefulWidget {
  const BannerPlaceholder({super.key});

  @override
  State<BannerPlaceholder> createState() =>
      _BannerPlaceholderState();
}

class _BannerPlaceholderState
    extends State<BannerPlaceholder> {
  int _currentIndex = 0;

  final List<Map<String, dynamic>> banners = [
    {
      'title': 'Wake up to\nFUN mornings',
      'subtitle': 'DISC UP TO',
      'discount': '30%',
      'button': 'Belanja',
      'color': const Color(0xFFFFF3E0),
    },
    {
      'title': 'Sarapan\nLebih Hemat',
      'subtitle': 'PROMO HINGGA',
      'discount': '25%',
      'button': 'Promo',
      'color': const Color(0xFFFFF8E1),
    },
    {
      'title': 'Gratis\nOngkir',
      'subtitle': 'MIN. BELANJA',
      'discount': '50K',
      'button': 'Belanja',
      'color': const Color(0xFFFFEBEE),
    },
  ];

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        CarouselSlider.builder(
          itemCount: banners.length,
          itemBuilder:
              (context, index, realIndex) {
            final banner = banners[index];

            return Container(
              width: double.infinity,
              margin: const EdgeInsets.symmetric(
                horizontal: 4,
              ),
              padding: const EdgeInsets.all(18),
              decoration: BoxDecoration(
                color: banner['color'],
                borderRadius:
                    BorderRadius.circular(18),
              ),
              child: Row(
                children: [
                  Expanded(
                    child: Column(
                      mainAxisAlignment:
                          MainAxisAlignment.center,
                      crossAxisAlignment:
                          CrossAxisAlignment.start,
                      children: [
                        Text(
                          banner['title'],
                          style:
                              const TextStyle(
                            fontSize: 18,
                            fontWeight:
                                FontWeight.bold,
                            color: Color(
                              0xFFD5001C,
                            ),
                            height: 1.2,
                          ),
                        ),

                        const SizedBox(height: 8),

                        Text(
                          banner['subtitle'],
                          style:
                              TextStyle(
                            fontSize: 11,
                            color: Colors
                                .grey.shade700,
                          ),
                        ),

                        const SizedBox(height: 4),

                        Text(
                          banner['discount'],
                          style:
                              const TextStyle(
                            fontSize: 30,
                            fontWeight:
                                FontWeight.bold,
                            color: Colors.orange,
                          ),
                        ),

                        const SizedBox(height: 12),

                        SizedBox(
                          height: 36,
                          child: ElevatedButton(
                            onPressed: () {},
                            style:
                                ElevatedButton.styleFrom(
                              padding:
                                  const EdgeInsets.symmetric(
                                horizontal: 16,
                              ),
                              minimumSize:
                                  Size.zero,
                            ),
                            child: Text(
                              banner['button'],
                              style:
                                  const TextStyle(
                                fontSize: 12,
                              ),
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),

                  const SizedBox(width: 12),

                  Container(
                    width: 75,
                    height: 75,
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius:
                          BorderRadius.circular(
                        16,
                      ),
                    ),
                    child: const Icon(
                      Icons.breakfast_dining,
                      size: 40,
                      color: Color(
                        0xFFD5001C,
                      ),
                    ),
                  ),
                ],
              ),
            );
          },
          options: CarouselOptions(
            height: 190,
            autoPlay: true,
            autoPlayInterval:
                const Duration(
              seconds: 3,
            ),
            enlargeCenterPage: false,
            viewportFraction: 0.92,
            onPageChanged:
                (index, reason) {
              setState(() {
                _currentIndex = index;
              });
            },
          ),
        ),

        const SizedBox(height: 12),

        Row(
          mainAxisAlignment:
              MainAxisAlignment.center,
          children: List.generate(
            banners.length,
            (index) {
              return AnimatedContainer(
                duration:
                    const Duration(
                  milliseconds: 250,
                ),
                margin:
                    const EdgeInsets.symmetric(
                  horizontal: 4,
                ),
                width:
                    _currentIndex == index
                        ? 18
                        : 8,
                height: 8,
                decoration:
                    BoxDecoration(
                  color:
                      _currentIndex == index
                          ? const Color(
                              0xFFD5001C,
                            )
                          : Colors
                              .grey.shade300,
                  borderRadius:
                      BorderRadius.circular(
                    50,
                  ),
                ),
              );
            },
          ),
        ),
      ],
    );
  }
}