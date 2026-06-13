import { useEffect, useState } from "react";
import usePermission from "../../hooks/usePermission";

import {
  getCategories,
  createCategory,
  updateCategory,
  deleteCategory,
} from "../../services/categoryService";

import {
  successAlert,
  errorAlert,
  confirmDelete,
} from "../../utils/alert";

export default function Categories() {
  const { can } = usePermission();

  const [categories, setCategories] =
    useState([]);

  const [name, setName] =
    useState("");

  const [description, setDescription] =
    useState("");

  const [editingId, setEditingId] =
    useState(null);

  const [loading, setLoading] =
    useState(false);

  const [submitting, setSubmitting] =
    useState(false);

  const canCreate =
    can("categories.create");

  const canUpdate =
    can("categories.update");

  const canDelete =
    can("categories.delete");

  const canSubmit =
    editingId ? canUpdate : canCreate;

  const loadCategories = async () => {
  try {
    setLoading(true);

    const categories =
      await getCategories();

    setCategories(
      categories || []
    );
  } catch (error) {
    console.error(error);

    setCategories([]);

    errorAlert(
      error?.response?.data?.message ||
      "Gagal mengambil data kategori"
    );
  } finally {
    setLoading(false);
  }
};

  useEffect(() => {
    loadCategories();
  }, []);

  const resetForm = () => {
    setEditingId(null);
    setName("");
    setDescription("");
  };

  const handleSubmit = async (e) => {
    e.preventDefault();

    if (!canSubmit) {
      return errorAlert(
        "Anda tidak memiliki izin untuk aksi ini"
      );
    }

    if (!name.trim()) {
      return errorAlert(
        "Nama kategori wajib diisi"
      );
    }

    try {
      setSubmitting(true);

      if (editingId) {
        await updateCategory(
          editingId,
          {
            name,
            description,
            is_active: true,
          }
        );

        await successAlert(
          "Kategori berhasil diperbarui"
        );
      } else {
        await createCategory({
          name,
          description,
          is_active: true,
        });

        await successAlert(
          "Kategori berhasil dibuat"
        );
      }

      resetForm();
      await loadCategories();
    } catch (error) {
      console.error(error);

      const message =
        error?.response?.data?.errors?.name?.[0] ||
        error?.response?.data?.message ||
        "Gagal menyimpan kategori";

      errorAlert(message);
    } finally {
      setSubmitting(false);
    }
  };

  const handleEdit = (
  category
) => {

  if (!canUpdate) return;

  setEditingId(
    category.id
  );

  setName(
    category.name || ""
  );

  setDescription(
    category.description || ""
  );
};

  const handleDelete = async (id) => {
    if (!canDelete) return;

    const result =
      await confirmDelete();

    if (!result.isConfirmed) {
      return;
    }

    try {
      await deleteCategory(id);

      await successAlert(
        "Kategori berhasil dihapus"
      );

      await loadCategories();
    } catch (error) {
      const message =
        error?.response?.data?.message ||
        "Gagal menghapus kategori";

      errorAlert(message);
    }
  };

  return (
    <div>
      <h1 className="text-3xl font-bold mb-6">
        Categories
      </h1>

      {(canCreate || canUpdate) && (
        <form
          onSubmit={handleSubmit}
          className="bg-white p-4 rounded shadow mb-6"
        >
          <div className="mb-3">
            <input
              type="text"
              placeholder="Category Name"
              className="w-full border p-3 rounded disabled:bg-slate-100"
              value={name}
              disabled={!canSubmit || submitting}
              onChange={(e) =>
                setName(e.target.value)
              }
            />
          </div>

          <div className="mb-3">
            <textarea
              placeholder="Description"
              className="w-full border p-3 rounded disabled:bg-slate-100"
              value={description}
              disabled={!canSubmit || submitting}
              onChange={(e) =>
                setDescription(e.target.value)
              }
            />
          </div>

          <div className="flex gap-2">
            {canSubmit && (
              <button
                type="submit"
                disabled={submitting}
                className="bg-slate-900 text-white px-4 py-2 rounded disabled:opacity-50"
              >
                {submitting
                  ? "Saving..."
                  : editingId
                    ? "Update"
                    : "Create"}
              </button>
            )}

            {editingId && (
              <button
                type="button"
                onClick={resetForm}
                disabled={submitting}
                className="bg-gray-400 text-white px-4 py-2 rounded disabled:opacity-50"
              >
                Cancel
              </button>
            )}
          </div>
        </form>
      )}

      <div className="bg-white rounded shadow overflow-hidden">
        <table className="w-full">
          <thead>
            <tr className="bg-slate-100">
              <th className="p-3 text-left">ID</th>
              <th className="p-3 text-left">Name</th>
              <th className="p-3 text-left">Description</th>
              <th className="p-3 text-left">Status</th>
              {(canUpdate || canDelete) && (
                <th className="p-3 text-left">Action</th>
              )}
            </tr>
          </thead>

          <tbody>
            {loading ? (
              <tr>
                <td
                  colSpan={canUpdate || canDelete ? 5 : 4}
                  className="p-4 text-center"
                >
                  Loading...
                </td>
              </tr>
            ) : categories.length === 0 ? (
              <tr>
                <td
                  colSpan={canUpdate || canDelete ? 5 : 4}
                  className="p-4 text-center"
                >
                  Belum ada kategori
                </td>
              </tr>
            ) : (
              categories.map((category) => (
                <tr
                  key={category.id}
                  className="border-t"
                >
                  <td className="p-3">{category.id}</td>
                  <td className="p-3">{category.name}</td>
                  <td className="p-3">
                    {category.description}
                  </td>
                  <td className="p-3">
                    {category.is_active === false
  ? "Inactive"
  : "Active"}
                  </td>

                  {(canUpdate || canDelete) && (
                    <td className="p-3">
                      {canUpdate && (
                        <button
                          onClick={() =>
                            handleEdit(category)
                          }
                          className="bg-blue-600 text-white px-3 py-1 rounded mr-2"
                        >
                          Edit
                        </button>
                      )}

                      {canDelete && (
                        <button
                          onClick={() =>
                            handleDelete(category.id)
                          }
                          className="bg-red-600 text-white px-3 py-1 rounded"
                        >
                          Delete
                        </button>
                      )}
                    </td>
                  )}
                </tr>
              ))
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}
