import api from "../api/axios";

/*
|--------------------------------------------------------------------------
| Get Product Images
|--------------------------------------------------------------------------
*/

export const getProductImages = async (
  productId,
  params = {}
) => {
  const response = await api.get(
    `/products/${productId}/images`,
    {
      params,
    }
  );

  return response.data.data;
};

/*
|--------------------------------------------------------------------------
| Get Product Image Detail
|--------------------------------------------------------------------------
*/

export const getProductImage = async (
  imageId
) => {
  const response = await api.get(
    `/products/images/${imageId}`
  );

  return response.data.data;
};

/*
|--------------------------------------------------------------------------
| Create Product Image
|--------------------------------------------------------------------------
*/

export const createProductImage = async (
  productId,
  data
) => {
  const response = await api.post(
    `/products/${productId}/images`,
    data,
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
| Update Product Image
|--------------------------------------------------------------------------
*/

export const updateProductImage = async (
  imageId,
  data
) => {
  const response = await api.post(
    `/products/images/${imageId}?_method=PUT`,
    data,
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
| Delete Product Image
|--------------------------------------------------------------------------
*/

export const deleteProductImage = async (
  imageId
) => {
  const response = await api.delete(
    `/products/images/${imageId}`
  );

  return response.data;
};

/*
|--------------------------------------------------------------------------
| Set Primary Product Image
|--------------------------------------------------------------------------
*/

export const setPrimaryProductImage = async (
  imageId
) => {
  const response = await api.patch(
    `/products/images/${imageId}/primary`
  );

  return response.data.data;
};