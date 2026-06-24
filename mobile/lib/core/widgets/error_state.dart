import 'package:flutter/material.dart';

class ErrorState extends StatelessWidget {
  final String title;
  final String subtitle;
  final VoidCallback? onRetry;

  const ErrorState({
    super.key,
    required this.title,
    required this.subtitle,
    this.onRetry,
  });

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(
        horizontal: 24,
        vertical: 32,
      ),
      child: Center(
        child: Column(
          children: [
            Container(
              width: 72,
              height: 72,
              decoration: BoxDecoration(
                color: const Color(
                  0xFFFFF3E0,
                ),
                borderRadius:
                    BorderRadius.circular(
                  24,
                ),
              ),
              child: const Icon(
                Icons.wifi_off_rounded,
                size: 36,
                color: Color(
                  0xFFD5001C,
                ),
              ),
            ),

            const SizedBox(height: 16),

            Text(
              title,
              textAlign: TextAlign.center,
              style: const TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.bold,
              ),
            ),

            const SizedBox(height: 8),

            Text(
              subtitle,
              textAlign: TextAlign.center,
              style: TextStyle(
                fontSize: 13,
                color: Colors.grey.shade600,
                height: 1.4,
              ),
            ),

            if (onRetry != null) ...[
              const SizedBox(height: 20),

              SizedBox(
                height: 42,
                child: ElevatedButton.icon(
                  onPressed: onRetry,
                  icon: const Icon(
                    Icons.refresh,
                    size: 18,
                  ),
                  label: const Text(
                    'Coba Lagi',
                  ),
                ),
              ),
            ],
          ],
        ),
      ),
    );
  }
}