import 'package:flutter/material.dart';

import '../data/address_repository.dart';
import '../data/models/address_model.dart';

class AddressFormPage extends StatefulWidget {
  const AddressFormPage({
    super.key,
    this.address,
  });

  final AddressModel? address;

  @override
  State<AddressFormPage> createState() =>
      _AddressFormPageState();
}

class _AddressFormPageState
    extends State<AddressFormPage> {
  final _formKey =
      GlobalKey<FormState>();

  final AddressRepository _repository =
      AddressRepository();

  late final TextEditingController
      _recipientController;

  late final TextEditingController
      _phoneController;

  late final TextEditingController
      _addressController;

  late final TextEditingController
      _districtController;

  late final TextEditingController
      _cityController;

  late final TextEditingController
      _provinceController;

  late final TextEditingController
      _postalCodeController;

  bool _isDefault = false;

  String _label = 'Rumah';

  bool _isSaving = false;

  bool get _isEdit =>
      widget.address != null;

  @override
  void initState() {
    super.initState();

    final address =
        widget.address;

    _recipientController =
        TextEditingController(
      text:
          address?.recipientName ??
              '',
    );

    _phoneController =
        TextEditingController(
      text:
          address?.phoneNumber ??
              '',
    );

    _addressController =
        TextEditingController(
      text:
          address?.address ?? '',
    );

    _districtController =
        TextEditingController(
      text:
          address?.district ?? '',
    );

    _cityController =
        TextEditingController(
      text:
          address?.city ?? '',
    );

    _provinceController =
        TextEditingController(
      text:
          address?.province ?? '',
    );

    _postalCodeController =
        TextEditingController(
      text:
          address?.postalCode ??
              '',
    );

    _isDefault =
        address?.isDefault ??
            false;

    _label =
        address?.label ??
            'Rumah';
  }

  @override
  void dispose() {
    _recipientController.dispose();

    _phoneController.dispose();

    _addressController.dispose();

    _districtController.dispose();

    _cityController.dispose();

    _provinceController.dispose();

    _postalCodeController.dispose();

    super.dispose();
  }

  Future<void> _save() async {
    if (!_formKey.currentState!
        .validate()) {
      return;
    }

    setState(() {
      _isSaving = true;
    });

    try {
      final model = AddressModel(
        id: widget.address?.id ??
            DateTime.now()
                .millisecondsSinceEpoch
                .toString(),

        recipientName:
            _recipientController
                .text
                .trim(),

        phoneNumber:
            _phoneController.text
                .trim(),

        address:
            _addressController.text
                .trim(),

        district:
            _districtController
                .text
                .trim(),

        city:
            _cityController.text
                .trim(),

        province:
            _provinceController
                .text
                .trim(),

        postalCode:
            _postalCodeController
                .text
                .trim(),

        label: _label,

        isDefault:
            _isDefault,

        createdAt:
            widget.address
                    ?.createdAt ??
                DateTime.now()
                    .toIso8601String(),
      );

      if (_isEdit) {
        await _repository
            .updateAddress(
          model,
        );
      } else {
        await _repository
            .addAddress(
          model,
        );
      }

      if (!mounted) return;

      Navigator.pop(
        context,
        true,
      );
    } finally {
      if (mounted) {
        setState(() {
          _isSaving = false;
        });
      }
    }
  }

  String? _required(
    String? value,
  ) {
    if (value == null ||
        value.trim().isEmpty) {
      return 'Wajib diisi';
    }

    return null;
  }

  @override
  Widget build(
    BuildContext context,
  ) {
    return Scaffold(
      appBar: AppBar(
        title: Text(
          _isEdit
              ? 'Ubah Alamat'
              : 'Tambah Alamat',
        ),
      ),
      body: Form(
        key: _formKey,
        child: ListView(
          padding:
              const EdgeInsets.all(
            16,
          ),
          children: [
            _buildField(
              controller:
                  _recipientController,
              label:
                  'Nama Penerima',
            ),

            _buildField(
              controller:
                  _phoneController,
              label:
                  'Nomor HP',
              keyboardType:
                  TextInputType.phone,
            ),

            _buildField(
              controller:
                  _addressController,
              label:
                  'Alamat Lengkap',
              maxLines: 3,
            ),

            _buildField(
              controller:
                  _districtController,
              label:
                  'Kecamatan',
            ),

            _buildField(
              controller:
                  _cityController,
              label:
                  'Kota/Kabupaten',
            ),

            _buildField(
              controller:
                  _provinceController,
              label:
                  'Provinsi',
            ),

            _buildField(
              controller:
                  _postalCodeController,
              label:
                  'Kode Pos',
              keyboardType:
                  TextInputType.number,
            ),

            const SizedBox(
              height: 24,
            ),

            const Text(
              'Label',
              style: TextStyle(
                fontWeight:
                    FontWeight.bold,
              ),
            ),

            const SizedBox(
              height: 12,
            ),

            Wrap(
              spacing: 8,
              children: [
                'Rumah',
                'Kantor',
                'Lainnya',
              ].map(
                (item) {
                  return ChoiceChip(
                    label:
                        Text(item),

                    selected:
                        _label ==
                            item,

                    onSelected:
                        (_) {
                      setState(() {
                        _label =
                            item;
                      });
                    },
                  );
                },
              ).toList(),
            ),

            const SizedBox(
              height: 16,
            ),

            SwitchListTile(
              contentPadding:
                  EdgeInsets.zero,

              title: const Text(
                'Jadikan alamat utama',
              ),

              value:
                  _isDefault,

              onChanged: (value) {
                setState(() {
                  _isDefault =
                      value;
                });
              },
            ),

            const SizedBox(
              height: 32,
            ),

            SizedBox(
              height: 52,
              child: ElevatedButton(
                onPressed:
                    _isSaving
                        ? null
                        : _save,

                child: _isSaving
                    ? const SizedBox(
                        width: 22,
                        height: 22,
                        child:
                            CircularProgressIndicator(
                          strokeWidth:
                              2,
                        ),
                      )
                    : Text(
                        _isEdit
                            ? 'Simpan Perubahan'
                            : 'Simpan Alamat',
                      ),
              ),
            ),

            const SizedBox(
              height: 24,
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildField({
    required TextEditingController
        controller,
    required String label,
    int maxLines = 1,
    TextInputType?
        keyboardType,
  }) {
    return Padding(
      padding:
          const EdgeInsets.only(
        bottom: 16,
      ),
      child: TextFormField(
        controller: controller,

        validator: _required,

        keyboardType:
            keyboardType,

        maxLines: maxLines,

        decoration: InputDecoration(
          labelText: label,

          border:
              const OutlineInputBorder(),
        ),
      ),
    );
  }
}