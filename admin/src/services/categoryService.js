import api from "../api/axios";

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

const buildCategoryFormData = (data = {}) => {
  const formData = new FormData();

  Object.entries(data).forEach(
    ([key, value]) => {
      if (
        value !== undefined &&
        value !== null &&
        value !== ""
      ) {
        formData.append(
          key,
          value
        );
      }
    }
  );

  return formData;
};

/*
|--------------------------------------------------------------------------
| Get Categories
|--------------------------------------------------------------------------
*/

export const getCategories = async (
  params = {}
) => {
  const response =
    await api.get(
      "/categories",
      {
        params,
      }
    );

  return response.data.data;
};

/*
|--------------------------------------------------------------------------
| Create Category
|--------------------------------------------------------------------------
*/

export const createCategory =
  async (data) => {

    const formData =
      buildCategoryFormData(
        data
      );

    const response =
      await api.post(
        "/categories",
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
| Update Category
|--------------------------------------------------------------------------
*/

export const updateCategory =
  async (slug, data) => {

    const formData =
      buildCategoryFormData(
        data
      );

    /*
    |--------------------------------------------------------------------------
    | Laravel PUT + multipart workaround
    |--------------------------------------------------------------------------
    |
    | PHP/Laravel tidak selalu membaca file upload
    | dari request PUT multipart.
    |
    | Gunakan POST + _method=PUT.
    |
    */

    formData.append(
      "_method",
      "PUT"
    );

    const response =
      await api.post(
        `/categories/${slug}`,
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
| Delete Category
|--------------------------------------------------------------------------
*/

export const deleteCategory =
  async (slug) => {

    const response =
      await api.delete(
        `/categories/${slug}`
      );

    return response.data;
  };