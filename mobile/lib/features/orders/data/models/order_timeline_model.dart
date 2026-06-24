class OrderTimelineModel {
  final String title;

  final String? description;

  final String? dateTime;

  final bool isCompleted;

  const OrderTimelineModel({
    required this.title,
    this.description,
    this.dateTime,
    required this.isCompleted,
  });

  factory OrderTimelineModel.fromJson(
    Map<String, dynamic> json,
  ) {
    return OrderTimelineModel(
      title: json['title'] ?? '',

      description:
          json['description'],

      dateTime:
          json['date_time'],

      isCompleted:
          json['is_completed'] ?? false,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'title': title,

      'description':
          description,

      'date_time':
          dateTime,

      'is_completed':
          isCompleted,
    };
  }

  OrderTimelineModel copyWith({
    String? title,
    String? description,
    String? dateTime,
    bool? isCompleted,
  }) {
    return OrderTimelineModel(
      title: title ?? this.title,

      description:
          description ??
              this.description,

      dateTime:
          dateTime ??
              this.dateTime,

      isCompleted:
          isCompleted ??
              this.isCompleted,
    );
  }

  /*
  |--------------------------------------------------------------------------
  | Helper
  |--------------------------------------------------------------------------
  */

  bool get hasDescription =>
      description != null &&
      description!.isNotEmpty;

  bool get hasDateTime =>
      dateTime != null &&
      dateTime!.isNotEmpty;
}