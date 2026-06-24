import 'package:flutter/material.dart';

import '../data/order_repository.dart';
import '../data/models/order_model.dart';

import 'order_detail_page.dart';

import 'widgets/order_card.dart';
import 'widgets/order_empty.dart';

class OrderHistoryPage extends StatefulWidget {
  const OrderHistoryPage({
    super.key,
  });

  @override
  State<OrderHistoryPage> createState() =>
      _OrderHistoryPageState();
}

class _OrderHistoryPageState
    extends State<OrderHistoryPage> {
  final OrderRepository _repository =
      OrderRepository();

  bool _isLoading = true;

  List<OrderModel> _orders = [];

  String _selectedStatus = 'all';

  final List<Map<String, String>>
      _statuses = const [
    {
      'value': 'all',
      'label': 'Semua',
    },
    {
      'value': 'pending',
      'label': 'Menunggu',
    },
    {
      'value': 'processing',
      'label': 'Diproses',
    },
    {
      'value': 'shipped',
      'label': 'Dikirim',
    },
    {
      'value': 'completed',
      'label': 'Selesai',
    },
    {
      'value': 'cancelled',
      'label': 'Dibatalkan',
    },
  ];

  @override
  void initState() {
    super.initState();

    _loadOrders();
  }

  Future<void> _loadOrders() async {
    if (mounted) {
      setState(() {
        _isLoading = true;
      });
    }

    try {
      final orders =
          await _repository.getOrders();

      if (!mounted) return;

      setState(() {
        _orders = orders;
        _isLoading = false;
      });
    } catch (_) {
      if (!mounted) return;

      setState(() {
        _orders = [];
        _isLoading = false;
      });
    }
  }

  void _changeStatus(
    String status,
  ) {
    setState(() {
      _selectedStatus = status;
    });
  }

  Future<void> _openDetail(
    OrderModel order,
  ) async {
    await Navigator.push(
      context,
      MaterialPageRoute(
        builder: (_) =>
            OrderDetailPage(
          orderNumber:
              order.orderNumber,
        ),
      ),
    );

    /*
    |--------------------------------------------------------------------------
    | Reload ketika kembali
    |--------------------------------------------------------------------------
    */

    await _loadOrders();
  }

  List<OrderModel> get _filteredOrders {
    if (_selectedStatus == 'all') {
      return _orders;
    }

    return _orders.where(
      (order) {
        return order.status ==
            _selectedStatus;
      },
    ).toList();
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

    final orders =
        _filteredOrders;

    return Scaffold(
      backgroundColor:
          const Color(0xFFF5F5F5),

      appBar: AppBar(
        elevation: 0,
        backgroundColor:
            Colors.white,
        foregroundColor:
            Colors.black,
        centerTitle: true,
        title: Text(
          'Pesanan Saya (${_orders.length})',
        ),
      ),

      body: Column(
        children: [
          /*
          |--------------------------------------------------------------------------
          | Status Filter
          |--------------------------------------------------------------------------
          */

          Container(
            color: Colors.white,
            height: 56,
            child: ListView.separated(
              padding:
                  const EdgeInsets.symmetric(
                horizontal: 16,
                vertical: 10,
              ),
              scrollDirection:
                  Axis.horizontal,
              itemCount:
                  _statuses.length,
              separatorBuilder:
                  (_, __) =>
                      const SizedBox(
                width: 8,
              ),
              itemBuilder:
                  (context, index) {
                final item =
                    _statuses[index];

                final selected =
                    item['value'] ==
                        _selectedStatus;

                return ChoiceChip(
                  label: Text(
                    item['label']!,
                  ),
                  selected:
                      selected,
                  selectedColor:
                      const Color(
                    0xFFD5001C,
                  ),
                  labelStyle:
                      TextStyle(
                    color: selected
                        ? Colors.white
                        : Colors.black,
                  ),
                  onSelected: (_) {
                    _changeStatus(
                      item['value']!,
                    );
                  },
                );
              },
            ),
          ),

          /*
          |--------------------------------------------------------------------------
          | Orders List
          |--------------------------------------------------------------------------
          */

          Expanded(
            child: orders.isEmpty
                ? OrderEmpty(
                    onShopNow: () {
                      Navigator.pushNamedAndRemoveUntil(
                        context,
                        '/',
                        (route) =>
                            false,
                      );
                    },
                  )
                : RefreshIndicator(
                    onRefresh:
                        _loadOrders,
                    child: ListView.builder(
                      physics:
                          const AlwaysScrollableScrollPhysics(),
                      padding:
                          const EdgeInsets.all(
                        16,
                      ),
                      itemCount:
                          orders.length,
                      itemBuilder:
                          (
                        context,
                        index,
                      ) {
                        final order =
                            orders[index];

                        return OrderCard(
                          order: order,

                          onTap: () {
                            _openDetail(
                              order,
                            );
                          },

                          onAction: () {
                            _openDetail(
                              order,
                            );
                          },
                        );
                      },
                    ),
                  ),
          ),
        ],
      ),
    );
  }
}