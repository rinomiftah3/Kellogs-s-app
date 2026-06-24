class AddressModel {
  final String id;

  /*
  |--------------------------------------------------------------------------
  | Recipient
  |--------------------------------------------------------------------------
  */

  final String recipientName;

  final String phoneNumber;

  /*
  |--------------------------------------------------------------------------
  | Address Information
  |--------------------------------------------------------------------------
  */

  final String address;

  final String district;

  final String city;

  final String province;

  final String postalCode;

  /*
  |--------------------------------------------------------------------------
  | Label
  |--------------------------------------------------------------------------
  */

  final String label;

  final bool isDefault;

  /*
  |--------------------------------------------------------------------------
  | Metadata
  |--------------------------------------------------------------------------
  */

  final String createdAt;

  const AddressModel({
    required this.id,
    required this.recipientName,
    required this.phoneNumber,
    required this.address,
    required this.district,
    required this.city,
    required this.province,
    required this.postalCode,
    required this.label,
    required this.isDefault,
    required this.createdAt,
  });

  /*
  |--------------------------------------------------------------------------
  | Helpers
  |--------------------------------------------------------------------------
  */

  bool get isHome =>
      label.toLowerCase() == 'rumah';

  bool get isOffice =>
      label.toLowerCase() == 'kantor';

  String get fullAddress {
    return '$address, '
        '$district, '
        '$city, '
        '$province '
        '$postalCode';
  }

  String get displayName {
    return '$recipientName • $phoneNumber';
  }

  /*
  |--------------------------------------------------------------------------
  | Factory
  |--------------------------------------------------------------------------
  */

  factory AddressModel.fromJson(
    Map<String, dynamic> json,
  ) {
    return AddressModel(
      id: json['id'] ?? '',

      recipientName:
          json['recipient_name'] ?? '',

      phoneNumber:
          json['phone_number'] ?? '',

      address:
          json['address'] ?? '',

      district:
          json['district'] ?? '',

      city:
          json['city'] ?? '',

      province:
          json['province'] ?? '',

      postalCode:
          json['postal_code'] ?? '',

      label:
          json['label'] ?? 'Rumah',

      isDefault:
          json['is_default'] ?? false,

      createdAt:
          json['created_at'] ?? '',
    );
  }

  /*
  |--------------------------------------------------------------------------
  | To JSON
  |--------------------------------------------------------------------------
  */

  Map<String, dynamic> toJson() {
    return {
      'id': id,

      'recipient_name':
          recipientName,

      'phone_number':
          phoneNumber,

      'address':
          address,

      'district':
          district,

      'city':
          city,

      'province':
          province,

      'postal_code':
          postalCode,

      'label':
          label,

      'is_default':
          isDefault,

      'created_at':
          createdAt,
    };
  }

  /*
  |--------------------------------------------------------------------------
  | Copy With
  |--------------------------------------------------------------------------
  */

  AddressModel copyWith({
    String? id,
    String? recipientName,
    String? phoneNumber,
    String? address,
    String? district,
    String? city,
    String? province,
    String? postalCode,
    String? label,
    bool? isDefault,
    String? createdAt,
  }) {
    return AddressModel(
      id: id ?? this.id,

      recipientName:
          recipientName ??
              this.recipientName,

      phoneNumber:
          phoneNumber ??
              this.phoneNumber,

      address:
          address ?? this.address,

      district:
          district ?? this.district,

      city:
          city ?? this.city,

      province:
          province ?? this.province,

      postalCode:
          postalCode ??
              this.postalCode,

      label:
          label ?? this.label,

      isDefault:
          isDefault ??
              this.isDefault,

      createdAt:
          createdAt ??
              this.createdAt,
    );
  }
}