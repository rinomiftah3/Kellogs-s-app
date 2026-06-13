import api from "../api/axios";

export const getRoles =
  async () => {

    const response =
      await api.get("/roles");

    return response.data.data;
  };

export const getPermissions =
  async () => {

    const response =
      await api.get(
        "/permissions"
      );

    return response.data.data;
  };

export const createRole =
  async (data) => {

    const response =
      await api.post(
        "/roles",
        data
      );

    return response.data.data;
  };

export const updateRole =
  async (
    id,
    data
  ) => {

    const response =
      await api.put(
        `/roles/${id}`,
        data
      );

    return response.data.data;
  };

export const deleteRole =
  async (id) => {

    const response =
      await api.delete(
        `/roles/${id}`
      );

    return response.data;
  };