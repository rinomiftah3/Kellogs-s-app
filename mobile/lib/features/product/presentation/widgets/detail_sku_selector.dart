import 'package:flutter/material.dart';

import '../../data/models/product_sku_model.dart';

class DetailSkuSelector extends StatelessWidget {
  const DetailSkuSelector({
    super.key,
    required this.skus,
    required this.selectedSku,
    required this.selectedOptions,
    required this.onSelected,
  });

  final List<ProductSkuModel> skus;

  final ProductSkuModel? selectedSku;

  final Map<String, String> selectedOptions;

  final Function(
    String optionName,
    String value,
  ) onSelected;

  @override
  Widget build(BuildContext context) {
    if (skus.isEmpty) {
      return const SizedBox();
    }

    /*
    |--------------------------------------------------------------------------
    | Group SKU Options
    |--------------------------------------------------------------------------
    */

    final Map<String, List<String>>
        groupedOptions = {};

    for (final sku in skus) {
      for (final option
          in sku.optionValues) {
        final key =
            option.option ?? 'Varian';

        groupedOptions.putIfAbsent(
          key,
          () => [],
        );

        if (!groupedOptions[key]!
            .contains(option.name)) {
          groupedOptions[key]!
              .add(option.name);
        }
      }
    }

    return Container(
      color: Colors.white,
      padding: const EdgeInsets.all(
        20,
      ),
      child: Column(
        crossAxisAlignment:
            CrossAxisAlignment.start,
        children: [
          /*
          |--------------------------------------------------------------------------
          | Title
          |--------------------------------------------------------------------------
          */

          const Text(
            'Pilih Varian',
            style: TextStyle(
              fontSize: 18,
              fontWeight:
                  FontWeight.bold,
            ),
          ),

          const SizedBox(height: 16),

          /*
          |--------------------------------------------------------------------------
          | Option Groups
          |--------------------------------------------------------------------------
          */

          ...groupedOptions.entries.map(
            (entry) {
              final optionName =
                  entry.key;

              final values =
                  entry.value;

              return Padding(
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
                      optionName,
                      style:
                          const TextStyle(
                        fontSize: 15,
                        fontWeight:
                            FontWeight.w600,
                      ),
                    ),

                    const SizedBox(
                      height: 10,
                    ),

                    Wrap(
                      spacing: 10,
                      runSpacing: 10,
                      children:
                          values.map(
                        (value) {
                          final isSelected =
                              selectedOptions[
                                      optionName] ==
                                  value;

                          return ChoiceChip(
                            label: Text(
                              value,
                            ),

                            selected:
                                isSelected,

                            selectedColor:
                                const Color(
                              0xFFD5001C,
                            ),

                            backgroundColor:
                                Colors.grey
                                    .shade100,

                            labelStyle:
                                TextStyle(
                              color: isSelected
                                  ? Colors
                                      .white
                                  : Colors
                                      .black,
                              fontWeight:
                                  FontWeight
                                      .w500,
                            ),

                            shape:
                                RoundedRectangleBorder(
                              borderRadius:
                                  BorderRadius
                                      .circular(
                                10,
                              ),
                            ),

                            onSelected:
                                (_) {
                              onSelected(
                                optionName,
                                value,
                              );
                            },
                          );
                        },
                      ).toList(),
                    ),
                  ],
                ),
              );
            },
          ),
        ],
      ),
    );
  }
}