import api from "../api/axios";

export const getActivityLogs =
  async (
    params = {}
  ) => {

    const response =
      await api.get(
        "/activity-logs",
        {
          params,
        }
      );

    return response.data.data;
  };

export const getActivityLog =
  async (id) => {

    const response =
      await api.get(
        `/activity-logs/${id}`
      );

    return response.data.data;
  };