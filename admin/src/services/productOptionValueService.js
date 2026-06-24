import api from "../api/axios";

/*
|--------------------------------------------------------------------------
| Get Product Option Values
|--------------------------------------------------------------------------
|
| Supported Query Params:
| - product_option_id
| - search
| - is_active
| - per_page
|
*/

export const getProductOptionValues = async (
  params = {}
) => {
  const response = await api.get(
    "/product-option-values",
    {
      params,
    }
  );

  return response.data.data;
};

/*
|--------------------------------------------------------------------------
| Get All Product Option Values
|--------------------------------------------------------------------------
*/

export const getAllProductOptionValues =
  async () => {
    return getProductOptionValues({
      per_page: 100,
    });
  };

/*
|--------------------------------------------------------------------------
| Get Product Option Value Detail
|--------------------------------------------------------------------------
*/

export const getProductOptionValue =
  async (id) => {
    const response = await api.get(
      `/product-option-values/${id}`
    );

    return response.data.data;
  };

/*
|--------------------------------------------------------------------------
| Create Product Option Value
|--------------------------------------------------------------------------
*/

export const createProductOptionValue =
  async (data) => {
    const response =
      await api.post(
        "/product-option-values",
        data
      );

    return response.data.data;
  };

/*
|--------------------------------------------------------------------------
| Update Product Option Value
|--------------------------------------------------------------------------
*/

export const updateProductOptionValue =
  async (
    id,
    data
  ) => {
    const response =
      await api.put(
        `/product-option-values/${id}`,
        data
      );

    return response.data.data;
  };

/*
|--------------------------------------------------------------------------
| Delete Product Option Value
|--------------------------------------------------------------------------
*/

export const deleteProductOptionValue =
  async (id) => {
    const response =
      await api.delete(
        `/product-option-values/${id}`
      );

    return response.data;
  };

/*
|--------------------------------------------------------------------------
| Activate Product Option Value
|--------------------------------------------------------------------------
*/

export const activateProductOptionValue =
  async (id) => {
    const response =
      await api.patch(
        `/product-option-values/${id}/activate`
      );

    return response.data.data;
  };

/*
|--------------------------------------------------------------------------
| Deactivate Product Option Value
|--------------------------------------------------------------------------
*/

export const deactivateProductOptionValue =
  async (id) => {
    const response =
      await api.patch(
        `/product-option-values/${id}/deactivate`
      );

    return response.data.data;
  };

/*
|--------------------------------------------------------------------------
| Get Used Product Option Values
|--------------------------------------------------------------------------
*/

export const getUsedProductOptionValues =
  async () => {
    const response =
      await api.get(
        "/product-option-values/used"
      );

    return response.data.data;
  };

/*
|--------------------------------------------------------------------------
| Get Unused Product Option Values
|--------------------------------------------------------------------------
*/

export const getUnusedProductOptionValues =
  async () => {
    const response =
      await api.get(
        "/product-option-values/unused"
      );

    return response.data.data;
  };