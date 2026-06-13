import api from "../api/axios";

export const getProfile =
  async () => {

    const response =
      await api.get(
        "/profile"
      );

    return response.data.data;
  };

export const updateProfile =
  async (data) => {

    const response =
      await api.put(
        "/profile",
        data
      );

    return response.data.data;
  };

export const updatePassword =
  async (data) => {

    const response =
      await api.put(
        "/profile/password",
        data
      );

    return response.data;
  };

export const uploadAvatar =
  async (data) => {

    const response =
      await api.post(
        "/profile/avatar",
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

export const deleteAvatar =
  async () => {

    const response =
      await api.delete(
        "/profile/avatar"
      );

    return response.data.data;
  };