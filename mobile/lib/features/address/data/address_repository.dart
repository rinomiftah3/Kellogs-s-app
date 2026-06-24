import 'dart:convert';

import 'package:shared_preferences/shared_preferences.dart';

import 'models/address_model.dart';

class AddressRepository {
  AddressRepository._internal();

  static final AddressRepository _instance =
      AddressRepository._internal();

  factory AddressRepository() => _instance;

  static const String _storageKey =
      'kelloggs_addresses';

  final List<AddressModel> _addresses =
      [];

  /*
  |--------------------------------------------------------------------------
  | Load Addresses
  |--------------------------------------------------------------------------
  */

  Future<void> _loadIfNeeded() async {
    if (_addresses.isNotEmpty) {
      return;
    }

    final prefs =
        await SharedPreferences.getInstance();

    final jsonString =
        prefs.getString(_storageKey);

    if (jsonString == null ||
        jsonString.isEmpty) {
      return;
    }

    try {
      final decoded =
          jsonDecode(jsonString) as List;

      _addresses
        ..clear()
        ..addAll(
          decoded.map(
            (e) => AddressModel.fromJson(
              Map<String, dynamic>.from(
                e,
              ),
            ),
          ),
        );
    } catch (_) {
      _addresses.clear();
    }
  }

  /*
  |--------------------------------------------------------------------------
  | Save Addresses
  |--------------------------------------------------------------------------
  */

  Future<void> _save() async {
    final prefs =
        await SharedPreferences.getInstance();

    final jsonString = jsonEncode(
      _addresses
          .map(
            (e) => e.toJson(),
          )
          .toList(),
    );

    await prefs.setString(
      _storageKey,
      jsonString,
    );
  }

  /*
  |--------------------------------------------------------------------------
  | Get Addresses
  |--------------------------------------------------------------------------
  */

  Future<List<AddressModel>>
      getAddresses() async {
    await _loadIfNeeded();

    return List.unmodifiable(
      _addresses,
    );
  }

  /*
  |--------------------------------------------------------------------------
  | Get Default Address
  |--------------------------------------------------------------------------
  */

  Future<AddressModel?>
      getDefaultAddress() async {
    await _loadIfNeeded();

    try {
      return _addresses.firstWhere(
        (e) => e.isDefault,
      );
    } catch (_) {
      return null;
    }
  }

  /*
  |--------------------------------------------------------------------------
  | Find By ID
  |--------------------------------------------------------------------------
  */

  Future<AddressModel?> findById(
    String id,
  ) async {
    await _loadIfNeeded();

    try {
      return _addresses.firstWhere(
        (e) => e.id == id,
      );
    } catch (_) {
      return null;
    }
  }

  /*
  |--------------------------------------------------------------------------
  | Add Address
  |--------------------------------------------------------------------------
  */

  Future<void> addAddress(
    AddressModel address,
  ) async {
    await _loadIfNeeded();

    /*
    |--------------------------------------------------------------------------
    | First Address Auto Default
    |--------------------------------------------------------------------------
    */

    if (_addresses.isEmpty) {
      _addresses.add(
        address.copyWith(
          isDefault: true,
        ),
      );
    } else {
      if (address.isDefault) {
        for (int i = 0;
            i < _addresses.length;
            i++) {
          _addresses[i] =
              _addresses[i].copyWith(
            isDefault: false,
          );
        }
      }

      _addresses.add(address);
    }

    await _save();
  }

  /*
  |--------------------------------------------------------------------------
  | Update Address
  |--------------------------------------------------------------------------
  */

  Future<void> updateAddress(
    AddressModel address,
  ) async {
    await _loadIfNeeded();

    final index =
        _addresses.indexWhere(
      (e) => e.id == address.id,
    );

    if (index == -1) {
      return;
    }

    if (address.isDefault) {
      for (int i = 0;
          i < _addresses.length;
          i++) {
        _addresses[i] =
            _addresses[i].copyWith(
          isDefault: false,
        );
      }
    }

    _addresses[index] = address;

    await _save();
  }

  /*
  |--------------------------------------------------------------------------
  | Set Default Address
  |--------------------------------------------------------------------------
  */

  Future<void> setDefaultAddress(
    String addressId,
  ) async {
    await _loadIfNeeded();

    for (int i = 0;
        i < _addresses.length;
        i++) {
      _addresses[i] =
          _addresses[i].copyWith(
        isDefault:
            _addresses[i].id ==
                addressId,
      );
    }

    await _save();
  }

  /*
  |--------------------------------------------------------------------------
  | Delete Address
  |--------------------------------------------------------------------------
  */

  Future<void> deleteAddress(
    String addressId,
  ) async {
    await _loadIfNeeded();

    final deleted =
        _addresses.firstWhere(
      (e) => e.id == addressId,
      orElse: () =>
          throw Exception(),
    );

    _addresses.removeWhere(
      (e) => e.id == addressId,
    );

    /*
    |--------------------------------------------------------------------------
    | Jika default dihapus,
    | jadikan alamat pertama default
    |--------------------------------------------------------------------------
    */

    if (deleted.isDefault &&
        _addresses.isNotEmpty) {
      _addresses[0] =
          _addresses[0].copyWith(
        isDefault: true,
      );
    }

    await _save();
  }

  /*
  |--------------------------------------------------------------------------
  | Exists
  |--------------------------------------------------------------------------
  */

  Future<bool> contains(
    String addressId,
  ) async {
    await _loadIfNeeded();

    return _addresses.any(
      (e) => e.id == addressId,
    );
  }

  /*
  |--------------------------------------------------------------------------
  | Total Addresses
  |--------------------------------------------------------------------------
  */

  Future<int> totalAddresses()
      async {
    await _loadIfNeeded();

    return _addresses.length;
  }

  /*
  |--------------------------------------------------------------------------
  | Clear
  |--------------------------------------------------------------------------
  */

  Future<void> clear() async {
    _addresses.clear();

    await _save();
  }
}