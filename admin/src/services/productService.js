import api from "../api/axios";

export const getProducts =
  async (
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

export const getProduct =
  async (id) => {

    const response =
      await api.get(
        `/products/${id}`
      );

    return response.data.data;
  };

export const createProduct =
  async (data) => {

    const response =
      await api.post(
        "/products",
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

export const updateProduct =
  async (
    id,
    data
  ) => {

    const response =
      await api.post(
        `/products/${id}?_method=PUT`,
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

export const deleteProduct =
  async (id) => {

    const response =
      await api.delete(
        `/products/${id}`
      );

    return response.data;
  };