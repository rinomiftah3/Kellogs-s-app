import 'package:flutter/material.dart';

import '../data/address_repository.dart';
import '../data/models/address_model.dart';

import 'address_form_page.dart';

class AddressListPage extends StatefulWidget {
  const AddressListPage({
    super.key,
  });

  @override
  State<AddressListPage> createState() =>
      _AddressListPageState();
}

class _AddressListPageState
    extends State<AddressListPage> {
  final AddressRepository _repository =
      AddressRepository();

  bool _isLoading = true;

  List<AddressModel> _addresses = [];

  @override
  void initState() {
    super.initState();

    _loadAddresses();
  }

  Future<void> _loadAddresses() async {
    if (mounted) {
      setState(() {
        _isLoading = true;
      });
    }

    try {
      final addresses =
          await _repository.getAddresses();

      if (!mounted) return;

      setState(() {
        _addresses = addresses;
        _isLoading = false;
      });
    } catch (_) {
      if (!mounted) return;

      setState(() {
        _addresses = [];
        _isLoading = false;
      });
    }
  }

  Future<void> _setDefault(
    AddressModel address,
  ) async {
    await _repository.setDefaultAddress(
      address.id,
    );

    await _loadAddresses();

    if (!mounted) return;

    ScaffoldMessenger.of(context)
        .showSnackBar(
      SnackBar(
        content: Text(
          '${address.label} menjadi alamat utama',
        ),
      ),
    );
  }

  Future<void> _delete(
    AddressModel address,
  ) async {
    final confirmed =
        await showDialog<bool>(
      context: context,
      builder: (context) {
        return AlertDialog(
          title: const Text(
            'Hapus Alamat',
          ),
          content: Text(
            'Hapus alamat ${address.label}?',
          ),
          actions: [
            TextButton(
              onPressed: () {
                Navigator.pop(
                  context,
                  false,
                );
              },
              child: const Text(
                'Batal',
              ),
            ),
            ElevatedButton(
              onPressed: () {
                Navigator.pop(
                  context,
                  true,
                );
              },
              child: const Text(
                'Hapus',
              ),
            ),
          ],
        );
      },
    );

    if (confirmed != true) {
      return;
    }

    await _repository.deleteAddress(
      address.id,
    );

    await _loadAddresses();

    if (!mounted) return;

    ScaffoldMessenger.of(context)
        .showSnackBar(
      const SnackBar(
        content: Text(
          'Alamat berhasil dihapus',
        ),
      ),
    );
  }

  Future<void> _openForm({
    AddressModel? address,
  }) async {
    await Navigator.push(
      context,
      MaterialPageRoute(
        builder: (_) =>
            AddressFormPage(
          address: address,
        ),
      ),
    );

    await _loadAddresses();
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoading) {
      return const Scaffold(
        body: Center(
          child:
              CircularProgressIndicator(),
        ),
      );
    }

    return Scaffold(
      backgroundColor:
          const Color(0xFFF5F5F5),

      appBar: AppBar(
        elevation: 0,
        centerTitle: true,
        backgroundColor:
            Colors.white,
        foregroundColor:
            Colors.black,
        title: Text(
          'Alamat Saya (${_addresses.length})',
        ),
      ),

      floatingActionButton:
          FloatingActionButton.extended(
        backgroundColor:
            const Color(0xFFD5001C),
        onPressed: () {
          _openForm();
        },
        icon: const Icon(
          Icons.add,
        ),
        label: const Text(
          'Tambah',
        ),
      ),

      body: _addresses.isEmpty
          ? _buildEmpty()
          : RefreshIndicator(
              onRefresh:
                  _loadAddresses,
              child: ListView.separated(
                padding:
                    const EdgeInsets.all(
                  16,
                ),
                itemCount:
                    _addresses.length,
                separatorBuilder:
                    (_, __) =>
                        const SizedBox(
                  height: 12,
                ),
                itemBuilder:
                    (context, index) {
                  final address =
                      _addresses[index];

                  return _buildCard(
                    address,
                  );
                },
              ),
            ),
    );
  }

  Widget _buildCard(
    AddressModel address,
  ) {
    return Container(
      padding: const EdgeInsets.all(
        16,
      ),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius:
            BorderRadius.circular(
          16,
        ),
      ),
      child: Column(
        crossAxisAlignment:
            CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                padding:
                    const EdgeInsets.symmetric(
                  horizontal: 10,
                  vertical: 4,
                ),
                decoration:
                    BoxDecoration(
                  color:
                      const Color(
                    0xFFD5001C,
                  ),
                  borderRadius:
                      BorderRadius.circular(
                    20,
                  ),
                ),
                child: Text(
                  address.label,
                  style:
                      const TextStyle(
                    color:
                        Colors.white,
                    fontWeight:
                        FontWeight.bold,
                  ),
                ),
              ),

              const SizedBox(
                width: 8,
              ),

              if (address.isDefault)
                Container(
                  padding:
                      const EdgeInsets.symmetric(
                    horizontal: 10,
                    vertical: 4,
                  ),
                  decoration:
                      BoxDecoration(
                    border: Border.all(
                      color:
                          const Color(
                        0xFFD5001C,
                      ),
                    ),
                    borderRadius:
                        BorderRadius.circular(
                      20,
                    ),
                  ),
                  child: const Text(
                    'UTAMA',
                    style: TextStyle(
                      color:
                          Color(
                        0xFFD5001C,
                      ),
                      fontWeight:
                          FontWeight.bold,
                    ),
                  ),
                ),
            ],
          ),

          const SizedBox(
            height: 12,
          ),

          Text(
            address.displayName,
            style:
                const TextStyle(
              fontSize: 16,
              fontWeight:
                  FontWeight.bold,
            ),
          ),

          const SizedBox(
            height: 8,
          ),

          Text(
            address.fullAddress,
          ),

          const Divider(
            height: 24,
          ),

          Row(
            children: [
              if (!address.isDefault)
                TextButton(
                  onPressed: () {
                    _setDefault(
                      address,
                    );
                  },
                  child: const Text(
                    'Jadikan Utama',
                  ),
                ),

              const Spacer(),

              TextButton(
                onPressed: () {
                  _openForm(
                    address: address,
                  );
                },
                child: const Text(
                  'Ubah',
                ),
              ),

              TextButton(
                onPressed: () {
                  _delete(
                    address,
                  );
                },
                child: const Text(
                  'Hapus',
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildEmpty() {
    return Center(
      child: Padding(
        padding:
            const EdgeInsets.all(24),
        child: Column(
          mainAxisAlignment:
              MainAxisAlignment.center,
          children: [
            const Icon(
              Icons.location_on_outlined,
              size: 80,
              color: Colors.grey,
            ),

            const SizedBox(
              height: 16,
            ),

            const Text(
              'Belum ada alamat',
              style: TextStyle(
                fontSize: 18,
                fontWeight:
                    FontWeight.bold,
              ),
            ),

            const SizedBox(
              height: 8,
            ),

            const Text(
              'Tambahkan alamat untuk mempermudah proses checkout.',
              textAlign:
                  TextAlign.center,
            ),

            const SizedBox(
              height: 24,
            ),

            ElevatedButton.icon(
              onPressed: () {
                _openForm();
              },
              icon: const Icon(
                Icons.add,
              ),
              label: const Text(
                'Tambah Alamat',
              ),
            ),
          ],
        ),
      ),
    );
  }
}