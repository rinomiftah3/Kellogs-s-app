import 'package:flutter/material.dart';

import '../../../../core/widgets/skeleton.dart';

class DetailLoading extends StatelessWidget {
  const DetailLoading({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF5F5F5),

      body: SingleChildScrollView(
        child: Column(
          children: [
            const Skeleton(
              width: double.infinity,
              height: 320,
              borderRadius: BorderRadius.zero,
            ),

            Container(
              color: Colors.white,
              padding: const EdgeInsets.all(20),
              child: const Column(
                crossAxisAlignment:
                    CrossAxisAlignment.start,
                children: [
                  Skeleton(
                    width: 220,
                    height: 24,
                  ),

                  SizedBox(height: 16),

                  Skeleton(
                    width: 120,
                    height: 16,
                  ),

                  SizedBox(height: 16),

                  Skeleton(
                    width: 150,
                    height: 16,
                  ),

                  SizedBox(height: 12),

                  Skeleton(
                    width: 130,
                    height: 16,
                  ),

                  SizedBox(height: 24),

                  Row(
                    children: [
                      Skeleton(
                        width: 80,
                        height: 32,
                      ),

                      SizedBox(width: 8),

                      Skeleton(
                        width: 80,
                        height: 32,
                      ),

                      SizedBox(width: 8),

                      Skeleton(
                        width: 100,
                        height: 32,
                      ),
                    ],
                  ),
                ],
              ),
            ),

            const SizedBox(height: 12),

            Container(
              color: Colors.white,
              padding: const EdgeInsets.all(20),
              child: const Column(
                crossAxisAlignment:
                    CrossAxisAlignment.start,
                children: [
                  Skeleton(
                    width: 180,
                    height: 20,
                  ),

                  SizedBox(height: 16),

                  Skeleton(
                    width: double.infinity,
                    height: 14,
                  ),

                  SizedBox(height: 8),

                  Skeleton(
                    width: double.infinity,
                    height: 14,
                  ),

                  SizedBox(height: 8),

                  Skeleton(
                    width: 250,
                    height: 14,
                  ),
                ],
              ),
            ),
          ],
        ),
      ),

      bottomNavigationBar: Container(
        color: Colors.white,
        padding: const EdgeInsets.all(16),

        child: const SafeArea(
          top: false,
          child: Row(
            children: [
              Expanded(
                child: Skeleton(
                  width: double.infinity,
                  height: 48,
                ),
              ),

              SizedBox(width: 12),

              Expanded(
                child: Skeleton(
                  width: double.infinity,
                  height: 48,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}