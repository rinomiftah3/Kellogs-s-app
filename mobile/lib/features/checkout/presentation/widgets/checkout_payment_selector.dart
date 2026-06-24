import 'package:flutter/material.dart';

import '../../data/models/payment_method_model.dart';

class CheckoutPaymentSelector
    extends StatelessWidget {
  const CheckoutPaymentSelector({
    super.key,
    required this.methods,
    required this.selectedMethod,
    required this.onSelected,
  });

  final List<PaymentMethodModel> methods;

  final PaymentMethodModel?
      selectedMethod;

  final Function(
    PaymentMethodModel method,
  ) onSelected;

  @override
  Widget build(BuildContext context) {
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
          const Text(
            'Metode Pembayaran',
            style: TextStyle(
              fontSize: 16,
              fontWeight:
                  FontWeight.bold,
            ),
          ),

          const SizedBox(
            height: 16,
          ),

          ...methods.map(
            (method) {
              final isSelected =
                  selectedMethod?.id ==
                      method.id;

              return Padding(
                padding:
                    const EdgeInsets.only(
                  bottom: 12,
                ),
                child: InkWell(
                  borderRadius:
                      BorderRadius.circular(
                    14,
                  ),
                  onTap:
                      method.isEnabled
                          ? () {
                              onSelected(
                                method,
                              );
                            }
                          : null,
                  child: Container(
                    padding:
                        const EdgeInsets.all(
                      14,
                    ),
                    decoration:
                        BoxDecoration(
                      border: Border.all(
                        color: isSelected
                            ? const Color(
                                0xFFD5001C,
                              )
                            : Colors
                                .grey
                                .shade300,
                        width:
                            isSelected
                                ? 2
                                : 1,
                      ),
                      borderRadius:
                          BorderRadius
                              .circular(
                        14,
                      ),
                      color: method
                              .isEnabled
                          ? Colors.white
                          : Colors.grey
                              .shade100,
                    ),
                    child: Row(
                      children: [
                        _buildIcon(
                          method.icon,
                        ),

                        const SizedBox(
                          width: 14,
                        ),

                        Expanded(
                          child: Column(
                            crossAxisAlignment:
                                CrossAxisAlignment
                                    .start,
                            children: [
                              Text(
                                method.name,
                                style:
                                    TextStyle(
                                  fontSize:
                                      15,
                                  fontWeight:
                                      FontWeight
                                          .w600,
                                  color: method
                                          .isEnabled
                                      ? Colors
                                          .black
                                      : Colors
                                          .grey,
                                ),
                              ),

                              const SizedBox(
                                height: 4,
                              ),

                              Text(
                                method
                                    .description,
                                style:
                                    TextStyle(
                                  fontSize:
                                      13,
                                  color: Colors
                                      .grey
                                      .shade600,
                                ),
                              ),
                            ],
                          ),
                        ),

                        Radio<String>(
                          value: method.id,
                          groupValue:
                              selectedMethod
                                  ?.id,
                          activeColor:
                              const Color(
                            0xFFD5001C,
                          ),
                          onChanged:
                              method.isEnabled
                                  ? (_) {
                                      onSelected(
                                        method,
                                      );
                                    }
                                  : null,
                        ),
                      ],
                    ),
                  ),
                ),
              );
            },
          ),
        ],
      ),
    );
  }

  Widget _buildIcon(
    String icon,
  ) {
    IconData iconData;

    switch (icon) {
      case 'local_shipping':
        iconData =
            Icons.local_shipping;
        break;

      case 'account_balance':
        iconData =
            Icons.account_balance;
        break;

      case 'account_balance_wallet':
        iconData = Icons
            .account_balance_wallet;
        break;

      default:
        iconData =
            Icons.payments_outlined;
    }

    return Container(
      width: 48,
      height: 48,
      decoration: BoxDecoration(
        color: const Color(
          0xFFF8F8F8,
        ),
        borderRadius:
            BorderRadius.circular(
          12,
        ),
      ),
      child: Icon(
        iconData,
        color: const Color(
          0xFFD5001C,
        ),
      ),
    );
  }
}