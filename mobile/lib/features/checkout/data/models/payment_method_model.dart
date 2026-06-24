class PaymentMethodModel {
  final String id;

  final String name;

  final String description;

  final String icon;

  final bool isEnabled;

  const PaymentMethodModel({
    required this.id,
    required this.name,
    required this.description,
    required this.icon,
    required this.isEnabled,
  });

  factory PaymentMethodModel.fromJson(
    Map<String, dynamic> json,
  ) {
    return PaymentMethodModel(
      id: json['id'] ?? '',

      name: json['name'] ?? '',

      description:
          json['description'] ?? '',

      icon: json['icon'] ?? '',

      isEnabled:
          json['is_enabled'] ?? true,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'description':
          description,
      'icon': icon,
      'is_enabled':
          isEnabled,
    };
  }

  /*
  |--------------------------------------------------------------------------
  | Default Payment Methods
  |--------------------------------------------------------------------------
  */

  static List<PaymentMethodModel>
      defaults() {
    return const [
      PaymentMethodModel(
        id: 'cod',
        name: 'Bayar di Tempat',
        description:
            'Bayar saat pesanan diterima',
        icon: 'local_shipping',
        isEnabled: true,
      ),
      PaymentMethodModel(
        id: 'bank_transfer',
        name: 'Transfer Bank',
        description:
            'BCA, BRI, BNI, Mandiri',
        icon: 'account_balance',
        isEnabled: true,
      ),
      PaymentMethodModel(
        id: 'ewallet',
        name: 'E-Wallet',
        description:
            'OVO, DANA, GoPay, ShopeePay',
        icon: 'account_balance_wallet',
        isEnabled: true,
      ),
    ];
  }
}