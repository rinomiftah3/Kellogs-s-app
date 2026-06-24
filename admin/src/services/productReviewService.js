import api from "../api/axios";

/*
|--------------------------------------------------------------------------
| Product Review Service
|--------------------------------------------------------------------------
|
| Backend Routes
|
| GET    /product-reviews
| POST   /product-reviews
| GET    /product-reviews/{id}
| PUT    /product-reviews/{id}
| DELETE /product-reviews/{id}
|
| PATCH  /product-reviews/{id}/approve
| PATCH  /product-reviews/{id}/reject
| POST   /product-reviews/{id}/helpful
|
| DELETE /product-review-images/{id}
|
*/

const BASE_URL =
  "/product-reviews";

/*
|--------------------------------------------------------------------------
| Get Product Reviews
|--------------------------------------------------------------------------
*/

export const getProductReviews =
  async (params = {}) => {

    const response =
      await api.get(
        BASE_URL,
        {
          params,
        }
      );

    return {

      success:
        response.data.success,

      message:
        response.data.message,

      data:
        response.data.data || [],

      meta:
        response.data.meta || {},

    };

  };

/*
|--------------------------------------------------------------------------
| Get Product Review Detail
|--------------------------------------------------------------------------
*/

export const getProductReview =
  async (id) => {

    const response =
      await api.get(
        `${BASE_URL}/${id}`
      );

    return (
      response.data.data
    );

  };

/*
|--------------------------------------------------------------------------
| Create Product Review
|--------------------------------------------------------------------------
*/

export const createProductReview =
  async (data) => {

    const config =

      data instanceof FormData

        ? {

            headers: {

              "Content-Type":
                "multipart/form-data",

            },

          }

        : {};

    const response =
      await api.post(
        BASE_URL,
        data,
        config
      );

    return response.data;

  };

/*
|--------------------------------------------------------------------------
| Update Product Review
|--------------------------------------------------------------------------
|
| Jika images dikirim,
| seluruh gambar lama akan diganti.
|
*/

export const updateProductReview =
  async (
    id,
    data
  ) => {

    const config =

      data instanceof FormData

        ? {

            headers: {

              "Content-Type":
                "multipart/form-data",

            },

          }

        : {};

    const response =
      await api.put(
        `${BASE_URL}/${id}`,
        data,
        config
      );

    return response.data;

  };

/*
|--------------------------------------------------------------------------
| Delete Product Review
|--------------------------------------------------------------------------
*/

export const deleteProductReview =
  async (id) => {

    const response =
      await api.delete(
        `${BASE_URL}/${id}`
      );

    return {

      success:
        response.data.success,

      message:
        response.data.message,

    };

  };

/*
|--------------------------------------------------------------------------
| Approve Review
|--------------------------------------------------------------------------
*/

export const approveProductReview =
  async (id) => {

    const response =
      await api.patch(
        `${BASE_URL}/${id}/approve`
      );

    return response.data;

  };

/*
|--------------------------------------------------------------------------
| Reject Review
|--------------------------------------------------------------------------
*/

export const rejectProductReview =
  async (
    id,
    moderationNotes
  ) => {

    const response =
      await api.patch(
        `${BASE_URL}/${id}/reject`,
        {
          moderation_notes:
            moderationNotes,
        }
      );

    return response.data;

  };

/*
|--------------------------------------------------------------------------
| Increase Helpful Count
|--------------------------------------------------------------------------
*/

export const increaseHelpfulCount =
  async (id) => {

    const response =
      await api.post(
        `${BASE_URL}/${id}/helpful`
      );

    return response.data;

  };

/*
|--------------------------------------------------------------------------
| Delete Review Image
|--------------------------------------------------------------------------
|
| Admin:
| - Hapus gambar tidak pantas
|
| Customer:
| - Hapus gambar miliknya
|   jika review pending/rejected
|
*/

export const deleteProductReviewImage =
  async (id) => {

    const response =
      await api.delete(
        `/product-review-images/${id}`
      );

    return {

      success:
        response.data.success,

      message:
        response.data.message,

    };

  };

/*
|--------------------------------------------------------------------------
| Build Filters Helper
|--------------------------------------------------------------------------
*/

export const buildProductReviewFilters =
  (filters = {}) => {

    const params = {};

    if (
      filters.page !==
      undefined
    ) {

      params.page =
        filters.page;

    }

    if (
      filters.perPage !==
      undefined
    ) {

      params.per_page =
        filters.perPage;

    }

    if (
      filters.productId !==
      undefined
    ) {

      params.product_id =
        filters.productId;

    }

    if (
      filters.customerProfileId !==
      undefined
    ) {

      params.customer_profile_id =
        filters.customerProfileId;

    }

    if (
      filters.rating !==
      undefined
    ) {

      params.rating =
        filters.rating;

    }

    if (
      filters.status !==
      undefined
    ) {

      params.status =
        filters.status;

    }

    if (
      filters.verifiedPurchase !==
      undefined
    ) {

      params.verified_purchase =
        filters.verifiedPurchase;

    }

    if (
      filters.hasImages !==
      undefined
    ) {

      params.has_images =
        filters.hasImages;

    }

    return params;

  };

/*
|--------------------------------------------------------------------------
| Review Helpers
|--------------------------------------------------------------------------
*/

export const isReviewApproved =
  (review) => {

    return Boolean(
      review?.is_approved
    );

  };

export const isReviewPending =
  (review) => {

    return Boolean(
      review?.is_pending
    );

  };

export const isReviewRejected =
  (review) => {

    return Boolean(
      review?.is_rejected
    );

  };

export const hasReviewImages =
  (review) => {

    return Boolean(
      review?.has_images
    );

  };

export const isVerifiedPurchase =
  (review) => {

    return Boolean(
      review?.is_verified_purchase
    );

  };

/*
|--------------------------------------------------------------------------
| Image Helpers
|--------------------------------------------------------------------------
*/

export const getReviewImages =
  (review) => {

    return (
      review?.images || []
    );

  };

export const getReviewImageCount =
  (review) => {

    return Number(
      review?.images_count ?? 0
    );

  };

  export const getReviewModerationNotes =
  (review) => {

    return (
      review?.moderation_notes
      || ""
    );

  };
/*
|--------------------------------------------------------------------------
| Status Options
|--------------------------------------------------------------------------
*/

export const REVIEW_STATUSES = [

  {
    label: "Pending",
    value: "pending",
  },

  {
    label: "Approved",
    value: "approved",
  },

  {
    label: "Rejected",
    value: "rejected",
  },

];

/*
|--------------------------------------------------------------------------
| Rating Options
|--------------------------------------------------------------------------
*/

export const REVIEW_RATINGS = [

  {
    label: "1 Star",
    value: 1,
  },

  {
    label: "2 Stars",
    value: 2,
  },

  {
    label: "3 Stars",
    value: 3,
  },

  {
    label: "4 Stars",
    value: 4,
  },

  {
    label: "5 Stars",
    value: 5,
  },

];