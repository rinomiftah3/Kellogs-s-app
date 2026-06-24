import api from "../api/axios";

/*
|--------------------------------------------------------------------------
| Get Product Options
|--------------------------------------------------------------------------
|
| Supported Query Params:
| - product_id
| - search
| - is_active
| - is_required
| - per_page
|
*/

export const getProductOptions = async (
  params = {}
) => {
  const response = await api.get(
    "/product-options",
    {
      params,
    }
  );

  return response.data.data;
};

/*
|--------------------------------------------------------------------------
| Get Product Option Detail
|--------------------------------------------------------------------------
*/

export const getProductOption = async (
  id
) => {
  const response = await api.get(
    `/product-options/${id}`
  );

  return response.data.data;
};

/*
|--------------------------------------------------------------------------
| Create Product Option
|--------------------------------------------------------------------------
|
| Payload:
| {
|   product_id,
|   name,
|   code,
|   sort_order,
|   is_required,
|   is_active,
| }
|
*/

export const createProductOption =
  async (data) => {

    const response =
      await api.post(
        "/product-options",
        data
      );

    return response.data.data;
  };

/*
|--------------------------------------------------------------------------
| Update Product Option
|--------------------------------------------------------------------------
*/

export const updateProductOption =
  async (
    id,
    data
  ) => {

    const response =
      await api.put(
        `/product-options/${id}`,
        data
      );

    return response.data.data;
  };

/*
|--------------------------------------------------------------------------
| Delete Product Option
|--------------------------------------------------------------------------
*/

export const deleteProductOption =
  async (id) => {

    const response =
      await api.delete(
        `/product-options/${id}`
      );

    return response.data;
  };

/*
|--------------------------------------------------------------------------
| Activate Product Option
|--------------------------------------------------------------------------
*/

export const activateProductOption =
  async (id) => {

    const response =
      await api.patch(
        `/product-options/${id}/activate`
      );

    return response.data.data;
  };

/*
|--------------------------------------------------------------------------
| Deactivate Product Option
|--------------------------------------------------------------------------
*/

export const deactivateProductOption =
  async (id) => {

    const response =
      await api.patch(
        `/product-options/${id}/deactivate`
      );

    return response.data.data;
  };

/*
|--------------------------------------------------------------------------
| Toggle Status Helper
|--------------------------------------------------------------------------
*/

export const toggleProductOptionStatus =
  async (
    id,
    isActive
  ) => {

    return isActive
      ? deactivateProductOption(id)
      : activateProductOption(id);
  };

/*
|--------------------------------------------------------------------------
| Mark As Required
|--------------------------------------------------------------------------
*/

export const markProductOptionRequired =
  async (id) => {

    const response =
      await api.patch(
        `/product-options/${id}/required`
      );

    return response.data.data;
  };

/*
|--------------------------------------------------------------------------
| Mark As Optional
|--------------------------------------------------------------------------
*/

export const markProductOptionOptional =
  async (id) => {

    const response =
      await api.patch(
        `/product-options/${id}/optional`
      );

    return response.data.data;
  };

/*
|--------------------------------------------------------------------------
| Toggle Requirement Helper
|--------------------------------------------------------------------------
*/

export const toggleProductOptionRequirement =
  async (
    id,
    isRequired
  ) => {

    return isRequired
      ? markProductOptionOptional(id)
      : markProductOptionRequired(id);
  };

/*
|--------------------------------------------------------------------------
| Default Export
|--------------------------------------------------------------------------
*/

export default {

  getProductOptions,

  getProductOption,

  createProductOption,

  updateProductOption,

  deleteProductOption,

  activateProductOption,

  deactivateProductOption,

  toggleProductOptionStatus,

  markProductOptionRequired,

  markProductOptionOptional,

  toggleProductOptionRequirement,
};