import api from "../api/axios";

/*
|--------------------------------------------------------------------------
| Get Products
|--------------------------------------------------------------------------
*/

export const getProducts = async (
  params = {}
) => {
  const response =
    await api.get(
      "/products",
      {
        params,
      }
    );

  return response.data.data;
};

/*
|--------------------------------------------------------------------------
| Get Product Detail
|--------------------------------------------------------------------------
*/

export const getProduct = async (
  slug
) => {
  const response =
    await api.get(
      `/products/${slug}`
    );

  return response.data.data;
};

/*
|--------------------------------------------------------------------------
| Create Product
|--------------------------------------------------------------------------
*/

export const createProduct =
  async (formData) => {

    const response =
      await api.post(
        "/products",
        formData,
        {
          headers: {
            "Content-Type":
              "multipart/form-data",
          },
        }
      );

    return response.data.data;
  };

/*
|--------------------------------------------------------------------------
| Update Product
|--------------------------------------------------------------------------
*/

export const updateProduct =
  async (
    slug,
    formData
  ) => {

    const response =
      await api.post(
        `/products/${slug}?_method=PUT`,
        formData,
        {
          headers: {
            "Content-Type":
              "multipart/form-data",
          },
        }
      );

    return response.data.data;
  };

/*
|--------------------------------------------------------------------------
| Delete Product
|--------------------------------------------------------------------------
*/

export const deleteProduct =
  async (slug) => {

    const response =
      await api.delete(
        `/products/${slug}`
      );

    return response.data;
  };

/*
|--------------------------------------------------------------------------
| Product Filters Helper
|--------------------------------------------------------------------------
*/

export const productFilters = {

  search: (
    keyword
  ) => ({
    search: keyword,
  }),

  category: (
    categoryId
  ) => ({
    category_id:
      categoryId,
  }),

  status: (
    status
  ) => ({
    status,
  }),

  active: (
    value
  ) => ({
    is_active: value,
  }),

  featured: (
    value
  ) => ({
    is_featured: value,
  }),

  published: (
    value
  ) => ({
    published: value,
  }),

  paginate: (
    page = 1,
    perPage = 15
  ) => ({
    page,
    per_page:
      perPage,
  }),

  sort: (
    sort = "latest",
    direction = "desc"
  ) => ({
    sort,
    direction,
  }),
};