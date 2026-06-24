import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/widgets/skeleton.dart';
import '../../data/category_repository.dart';
import '../../data/models/category_model.dart';
import '../../../../core/widgets/empty_state.dart';
import '../../../../core/widgets/error_state.dart';
class CategorySection extends StatefulWidget {
  const CategorySection({super.key});

  @override
  State<CategorySection> createState() =>
      _CategorySectionState();
}

class _CategorySectionState
    extends State<CategorySection> {
  final CategoryRepository _repository =
      CategoryRepository();

  late Future<List<CategoryModel>>
      _categoriesFuture;

  @override
  void initState() {
    super.initState();

    _categoriesFuture =
        _repository.getCategories();
  }

  @override
  Widget build(BuildContext context) {
    return FutureBuilder<List<CategoryModel>>(
      future: _categoriesFuture,
      builder: (context, snapshot) {
        /*
        |--------------------------------------------------------------------------
        | Loading
        |--------------------------------------------------------------------------
        */

        if (snapshot.connectionState ==
    ConnectionState.waiting) {
  return Column(
    children: [
      Padding(
        padding:
            const EdgeInsets.symmetric(
          horizontal: 16,
        ),
        child: Row(
          children: const [
            Skeleton(
              width: 120,
              height: 20,
            ),
          ],
        ),
      ),

      const SizedBox(height: 16),

      SizedBox(
        height: 110,
        child: ListView.separated(
          padding:
              const EdgeInsets.symmetric(
            horizontal: 16,
          ),
          scrollDirection:
              Axis.horizontal,
          itemCount: 5,
          separatorBuilder:
              (_, __) =>
                  const SizedBox(
            width: 16,
          ),
          itemBuilder: (_, __) {
            return const Column(
              children: [
                Skeleton(
                  width: 60,
                  height: 60,
                ),

                SizedBox(height: 8),

                Skeleton(
                  width: 50,
                  height: 12,
                ),
              ],
            );
          },
        ),
      ),
    ],
  );
}

        /*
        |--------------------------------------------------------------------------
        | Error
        |--------------------------------------------------------------------------
        */

        if (snapshot.hasError) {
  return ErrorState(
    title: 'Kategori Tidak Tersedia',
    subtitle:
        'Periksa koneksi internet lalu coba lagi.',
    onRetry: () {
      setState(() {
        _categoriesFuture =
            _repository.getCategories();
      });
    },
  );
}

        final categories =
            snapshot.data ?? [];

        /*
        |--------------------------------------------------------------------------
        | Empty State
        |--------------------------------------------------------------------------
        */

        if (categories.isEmpty) {
  return const EmptyState(
    icon: Icons.category_outlined,
    title: 'Belum Ada Kategori',
    subtitle:
        'Kategori akan muncul di sini.',
  );
}

        /*
        |--------------------------------------------------------------------------
        | Limit Home Categories
        |--------------------------------------------------------------------------
        */

        final homeCategories =
            categories.take(5).toList();

        return Column(
          children: [
            Padding(
              padding:
                  const EdgeInsets.symmetric(
                horizontal: 16,
              ),
              child: Row(
                children: [
                  const Text(
                    'Kategori',
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight:
                          FontWeight.bold,
                    ),
                  ),

                  const Spacer(),

                  TextButton(
                    onPressed: () {
                      context.go(
                        '/categories',
                      );
                    },
                    child: const Text(
                      'Lihat Semua',
                    ),
                  ),
                ],
              ),
            ),

            SizedBox(
              height: 110,
              child: ListView.separated(
                padding:
                    const EdgeInsets.symmetric(
                  horizontal: 16,
                ),
                scrollDirection:
                    Axis.horizontal,
                itemCount:
                    homeCategories.length,
                separatorBuilder:
                    (_, __) =>
                        const SizedBox(
                  width: 16,
                ),
                itemBuilder:
                    (context, index) {
                  final category =
                      homeCategories[
                          index];

                  return InkWell(
                    borderRadius:
                        BorderRadius.circular(
                      16,
                    ),
                    onTap: () {
                      context.go(
                        '/categories',
                      );
                    },
                    child: SizedBox(
                      width: 70,
                      child: Column(
                        children: [
                          Container(
                            width: 60,
                            height: 60,
                            decoration:
                                BoxDecoration(
                              color:
                                  const Color(
                                0xFFFFF3E0,
                              ),
                              borderRadius:
                                  BorderRadius
                                      .circular(
                                18,
                              ),
                            ),
                            child:
                                const Icon(
                              Icons.category,
                              color: Color(
                                0xFFD5001C,
                              ),
                              size: 30,
                            ),
                          ),

                          const SizedBox(
                            height: 8,
                          ),

                          Text(
                            category.name,
                            textAlign:
                                TextAlign
                                    .center,
                            maxLines: 2,
                            overflow:
                                TextOverflow
                                    .ellipsis,
                            style:
                                const TextStyle(
                              fontSize: 12,
                              fontWeight:
                                  FontWeight
                                      .w500,
                            ),
                          ),
                        ],
                      ),
                    ),
                  );
                },
              ),
            ),
          ],
        );
      },
    );
  }
}