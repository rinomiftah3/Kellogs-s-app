import 'category_service.dart';

import 'models/category_model.dart';

class CategoryRepository {
  final CategoryService _service =
      CategoryService();

  Future<List<CategoryModel>> getCategories() async {
    final categories =
        await _service.getCategories();

    /*
    |--------------------------------------------------------------------------
    | Home Categories
    |--------------------------------------------------------------------------
    | Hanya tampilkan kategori aktif dan child category
    | agar mirip Alfagift/Shopee.
    */

    final filteredCategories = categories
        .where(
          (category) =>
              category.isActive &&
              category.isChildCategory,
        )
        .toList();

    /*
    |--------------------------------------------------------------------------
    | Urutkan berdasarkan sort_order
    |--------------------------------------------------------------------------
    */

    filteredCategories.sort(
      (a, b) => a.sortOrder.compareTo(
        b.sortOrder,
      ),
    );

    return filteredCategories;
  }

  Future<List<CategoryModel>>
      getAllCategories() async {
    return await _service.getCategories();
  }

  Future<List<CategoryModel>>
      getParentCategories() async {
    final categories =
        await _service.getCategories();

    return categories
        .where(
          (category) =>
              category.isParentCategory &&
              category.isActive,
        )
        .toList();
  }

  Future<List<CategoryModel>>
      getChildCategories() async {
    final categories =
        await _service.getCategories();

    return categories
        .where(
          (category) =>
              category.isChildCategory &&
              category.isActive,
        )
        .toList();
  }
}