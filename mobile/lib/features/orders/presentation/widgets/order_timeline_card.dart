import 'package:flutter/material.dart';

import '../../data/models/order_model.dart';
import '../../data/models/order_timeline_model.dart';

class OrderTimelineCard extends StatelessWidget {
  const OrderTimelineCard({
    super.key,
    required this.order,
  });

  final OrderModel order;

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.symmetric(
        horizontal: 16,
      ),
      padding: const EdgeInsets.all(
        16,
      ),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius:
            BorderRadius.circular(
          16,
        ),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(
              0.04,
            ),
            blurRadius: 8,
            offset: const Offset(
              0,
              2,
            ),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment:
            CrossAxisAlignment.start,
        children: [
          const Text(
            'Timeline Pesanan',
            style: TextStyle(
              fontSize: 16,
              fontWeight:
                  FontWeight.bold,
            ),
          ),

          const SizedBox(
            height: 20,
          ),

          ...List.generate(
            order.timelines.length,
            (index) {
              final timeline =
                  order.timelines[index];

              final isLast =
                  index ==
                  order.timelines.length -
                      1;

              return _TimelineItem(
                timeline: timeline,
                isLast: isLast,
              );
            },
          ),
        ],
      ),
    );
  }
}

class _TimelineItem extends StatelessWidget {
  const _TimelineItem({
    required this.timeline,
    required this.isLast,
  });

  final OrderTimelineModel timeline;

  final bool isLast;

  @override
  Widget build(BuildContext context) {
    final active =
        timeline.isCompleted;

    return IntrinsicHeight(
      child: Row(
        crossAxisAlignment:
            CrossAxisAlignment.start,
        children: [
          Column(
            children: [
              Container(
                width: 28,
                height: 28,
                decoration:
                    BoxDecoration(
                  color: active
                      ? const Color(
                          0xFFD5001C,
                        )
                      : Colors.grey
                          .shade300,
                  shape: BoxShape.circle,
                ),
                child: Icon(
                  active
                      ? Icons.check
                      : Icons.circle,
                  size: 14,
                  color: active
                      ? Colors.white
                      : Colors.grey,
                ),
              ),

              if (!isLast)
                Expanded(
                  child: Container(
                    width: 2,
                    color: active
                        ? const Color(
                            0xFFD5001C,
                          )
                        : Colors.grey
                            .shade300,
                  ),
                ),
            ],
          ),

          const SizedBox(
            width: 12,
          ),

          Expanded(
            child: Padding(
              padding:
                  const EdgeInsets.only(
                bottom: 20,
              ),
              child: Column(
                crossAxisAlignment:
                    CrossAxisAlignment
                        .start,
                children: [
                  Text(
                    timeline.title,
                    style:
                        TextStyle(
                      fontWeight:
                          FontWeight.w700,
                      color: active
                          ? Colors.black
                          : Colors.grey,
                    ),
                  ),

                  const SizedBox(
                    height: 4,
                  ),

                  Text(
                    timeline.description,
                    style:
                        TextStyle(
                      fontSize: 13,
                      color: Colors
                          .grey
                          .shade600,
                    ),
                  ),

                  if (timeline.dateTime
                      .isNotEmpty)
                    Padding(
                      padding:
                          const EdgeInsets.only(
                        top: 6,
                      ),
                      child: Text(
                        timeline.dateTime,
                        style:
                            TextStyle(
                          fontSize: 12,
                          color: Colors
                              .grey
                              .shade500,
                        ),
                      ),
                    ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}