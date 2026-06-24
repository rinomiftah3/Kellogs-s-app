class CategoryModel {
  final int id;

  final int? parentId;

  final String? parentName;

  final String name;

  final String slug;

  final String? description;

  final String? image;

  final String? imageUrl;

  final bool hasImage;

  final int sortOrder;

  final bool isActive;

  final String statusLabel;

  final String statusColor;

  final int productsCount;

  final int childrenCount;

  final bool hasProducts;

  final bool hasChildren;

  final bool canBeDeleted;

  final String createdAt;

  final String createdAtHuman;

  final String updatedAt;

  final String updatedAtHuman;

  const CategoryModel({
    required this.id,
    this.parentId,
    this.parentName,
    required this.name,
    required this.slug,
    this.description,
    this.image,
    this.imageUrl,
    required this.hasImage,
    required this.sortOrder,
    required this.isActive,
    required this.statusLabel,
    required this.statusColor,
    required this.productsCount,
    required this.childrenCount,
    required this.hasProducts,
    required this.hasChildren,
    required this.canBeDeleted,
    required this.createdAt,
    required this.createdAtHuman,
    required this.updatedAt,
    required this.updatedAtHuman,
  });

  factory CategoryModel.fromJson(
    Map<String, dynamic> json,
  ) {
    return CategoryModel(
      id: json['id'] ?? 0,

      parentId: json['parent_id'],

      parentName: json['parent_name'],

      name: json['name'] ?? '',

      slug: json['slug'] ?? '',

      description: json['description'],

      image: json['image'],

      imageUrl: json['image_url'],

      hasImage: json['has_image'] ?? false,

      sortOrder: json['sort_order'] ?? 0,

      isActive: json['is_active'] ?? false,

      statusLabel:
          json['status_label'] ?? '',

      statusColor:
          json['status_color'] ?? '',

      productsCount:
          json['products_count'] ?? 0,

      childrenCount:
          json['children_count'] ?? 0,

      hasProducts:
          json['has_products'] ?? false,

      hasChildren:
          json['has_children'] ?? false,

      canBeDeleted:
          json['can_be_deleted'] ?? false,

      createdAt:
          json['created_at'] ?? '',

      createdAtHuman:
          json['created_at_human'] ?? '',

      updatedAt:
          json['updated_at'] ?? '',

      updatedAtHuman:
          json['updated_at_human'] ?? '',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'parent_id': parentId,
      'parent_name': parentName,
      'name': name,
      'slug': slug,
      'description': description,
      'image': image,
      'image_url': imageUrl,
      'has_image': hasImage,
      'sort_order': sortOrder,
      'is_active': isActive,
      'status_label': statusLabel,
      'status_color': statusColor,
      'products_count': productsCount,
      'children_count': childrenCount,
      'has_products': hasProducts,
      'has_children': hasChildren,
      'can_be_deleted': canBeDeleted,
      'created_at': createdAt,
      'created_at_human': createdAtHuman,
      'updated_at': updatedAt,
      'updated_at_human': updatedAtHuman,
    };
  }

  /*
  |--------------------------------------------------------------------------
  | Helper
  |--------------------------------------------------------------------------
  */

  bool get isParentCategory =>
      parentId == null;

  bool get isChildCategory =>
      parentId != null;
}