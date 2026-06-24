import 'package:flutter/material.dart';

class Skeleton extends StatefulWidget {
  final double width;
  final double height;
  final BorderRadius? borderRadius;

  const Skeleton({
    super.key,
    required this.width,
    required this.height,
    this.borderRadius,
  });

  @override
  State<Skeleton> createState() =>
      _SkeletonState();
}

class _SkeletonState
    extends State<Skeleton>
    with SingleTickerProviderStateMixin {
  late AnimationController _controller;

  @override
  void initState() {
    super.initState();

    _controller = AnimationController(
      vsync: this,
      duration: const Duration(
        milliseconds: 1200,
      ),
    )..repeat(reverse: true);
  }

  @override
  void dispose() {
    _controller.dispose();

    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return AnimatedBuilder(
      animation: _controller,
      builder: (_, __) {
        return Container(
          width: widget.width,
          height: widget.height,
          decoration: BoxDecoration(
            borderRadius:
                widget.borderRadius ??
                    BorderRadius.circular(
                      12,
                    ),
            color: Color.lerp(
              Colors.grey.shade300,
              Colors.grey.shade100,
              _controller.value,
            ),
          ),
        );
      },
    );
  }
}