class ProductModel {
  final int id;

  final String name;

  final String slug;

  final String? shortDescription;

  final String? description;

  final String? thumbnail;

  final String? thumbnailUrl;

  final String? primaryImageUrl;

  final bool hasImage;

  final String status;

  final String statusLabel;

  final bool isActive;

  final bool isFeatured;

  final bool isPublished;

  final int categoryId;

  final ProductCategory category;

  final int reviewCount;

  final int skuCount;

  final bool hasReviews;

  final bool hasSku;

  final bool canBePurchased;

  final String? publishedAt;

  final String? publishedAtHuman;

  final String createdAt;

  final String createdAtHuman;

  final String updatedAt;

  final String updatedAtHuman;

  const ProductModel({
    required this.id,
    required this.name,
    required this.slug,
    this.shortDescription,
    this.description,
    this.thumbnail,
    this.thumbnailUrl,
    this.primaryImageUrl,
    required this.hasImage,
    required this.status,
    required this.statusLabel,
    required this.isActive,
    required this.isFeatured,
    required this.isPublished,
    required this.categoryId,
    required this.category,
    required this.reviewCount,
    required this.skuCount,
    required this.hasReviews,
    required this.hasSku,
    required this.canBePurchased,
    this.publishedAt,
    this.publishedAtHuman,
    required this.createdAt,
    required this.createdAtHuman,
    required this.updatedAt,
    required this.updatedAtHuman,
  });

  factory ProductModel.fromJson(
    Map<String, dynamic> json,
  ) {
    return ProductModel(
      id: json['id'] ?? 0,

      name: json['name'] ?? '',

      slug: json['slug'] ?? '',

      shortDescription:
          json['short_description'],

      description:
          json['description'],

      thumbnail:
          json['thumbnail'],

      thumbnailUrl:
          json['thumbnail_url'],

      primaryImageUrl:
          json['primary_image_url'],

      hasImage:
          json['has_image'] ?? false,

      status:
          json['status'] ?? '',

      statusLabel:
          json['status_label'] ?? '',

      isActive:
          json['is_active'] ?? false,

      isFeatured:
          json['is_featured'] ?? false,

      isPublished:
          json['is_published'] ?? false,

      categoryId:
          json['category_id'] ?? 0,

      category:
          ProductCategory.fromJson(
        json['category'] ?? {},
      ),

      reviewCount:
          json['review_count'] ?? 0,

      skuCount:
          json['sku_count'] ?? 0,

      hasReviews:
          json['has_reviews'] ?? false,

      hasSku:
          json['has_sku'] ?? false,

      canBePurchased:
          json['can_be_purchased'] ?? false,

      publishedAt:
          json['published_at'],

      publishedAtHuman:
          json['published_at_human'],

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
      'name': name,
      'slug': slug,
      'short_description':
          shortDescription,
      'description':
          description,
      'thumbnail':
          thumbnail,
      'thumbnail_url':
          thumbnailUrl,
      'primary_image_url':
          primaryImageUrl,
      'has_image':
          hasImage,
      'status':
          status,
      'status_label':
          statusLabel,
      'is_active':
          isActive,
      'is_featured':
          isFeatured,
      'is_published':
          isPublished,
      'category_id':
          categoryId,
      'category':
          category.toJson(),
      'review_count':
          reviewCount,
      'sku_count':
          skuCount,
      'has_reviews':
          hasReviews,
      'has_sku':
          hasSku,
      'can_be_purchased':
          canBePurchased,
      'published_at':
          publishedAt,
      'published_at_human':
          publishedAtHuman,
      'created_at':
          createdAt,
      'created_at_human':
          createdAtHuman,
      'updated_at':
          updatedAt,
      'updated_at_human':
          updatedAtHuman,
    };
  }

  /*
  |--------------------------------------------------------------------------
  | Helper
  |--------------------------------------------------------------------------
  */

  bool get canShowOnHome =>
      isActive &&
      isPublished &&
      isFeatured;

  String get image =>
      thumbnailUrl ?? '';

  bool get showDetailButton =>
      true;

  /// Tambahan agar DetailInfo tidak error
  String get categoryName =>
      category.name;

  /// Tambahan agar lebih mudah dipakai nanti
  bool get hasThumbnail =>
      image.isNotEmpty;

  /// Fallback gambar
  String get displayImage =>
      hasThumbnail
          ? image
          : '';

  /// Label publish
  String get publishLabel =>
      publishedAtHuman ?? '-';
}

class ProductCategory {
  final int id;

  final String name;

  final String slug;

  const ProductCategory({
    required this.id,
    required this.name,
    required this.slug,
  });

  factory ProductCategory.fromJson(
    Map<String, dynamic> json,
  ) {
    return ProductCategory(
      id: json['id'] ?? 0,

      name: json['name'] ?? '',

      slug: json['slug'] ?? '',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'slug': slug,
    };
  }
}