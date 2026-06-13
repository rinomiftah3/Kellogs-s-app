import { useEffect, useState } from "react";
import usePermission from "../../hooks/usePermission";

import {
  getProducts,
  createProduct,
  updateProduct,
  deleteProduct,
} from "../../services/productService";

import {
  getCategories,
} from "../../services/categoryService";

import {
  successAlert,
  errorAlert,
  confirmDelete,
} from "../../utils/alert";

export default function Products() {
  const { can } = usePermission();

  const [editingId, setEditingId] =
    useState(null);

  const [products, setProducts] =
    useState([]);

  const [categories, setCategories] =
    useState([]);

  const [loading, setLoading] =
    useState(false);

  const [submitting, setSubmitting] =
    useState(false);

  const [categoryId, setCategoryId] =
    useState("");

  const [name, setName] =
    useState("");

  const [description, setDescription] =
    useState("");

  const [price, setPrice] =
    useState("");

  const [stock, setStock] =
    useState("");

  const [image, setImage] =
    useState(null);

  const [preview, setPreview] =
    useState("");

  const canCreate =
    can("products.create");

  const canUpdate =
    can("products.update");

  const canDelete =
    can("products.delete");

  const canSubmit =
    editingId ? canUpdate : canCreate;

  const loadProducts = async () => {
  try {

    setLoading(true);

    const products =
      await getProducts();

    setProducts(
  products || []
);

  } catch (error) {
    console.error(error);
  } finally {
    setLoading(false);
  }
};

  const loadCategories = async () => {
  try {

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
      "Gagal mengambil kategori"
    );

  }
};
useEffect(() => {
  loadProducts();
  loadCategories();
}, []);
  useEffect(() => {
  return () => {

    if (
      preview &&
      preview.startsWith("blob:")
    ) {
      URL.revokeObjectURL(
        preview
      );
    }

  };
}, [preview]);

  const handleImageChange = (e) => {
    const file =
      e.target.files[0];

    if (!file) return;

    setImage(file);

    setPreview(
      URL.createObjectURL(file)
    );
  };

  const resetForm = () => {

  if (
    preview &&
    preview.startsWith("blob:")
  ) {
    URL.revokeObjectURL(
      preview
    );
  }

  setEditingId(null);
  setCategoryId("");
  setName("");
  setDescription("");
  setPrice("");
  setStock("");
  setImage(null);
  setPreview("");
};

  const handleEdit = (product) => {
    if (!canUpdate) return;

    setEditingId(product.id);
    setCategoryId(
  product.category?.id || ""
);
    setName(product.name);
    setDescription(product.description || "");
    setPrice(product.price || "");
    setStock(product.stock || "");
    setImage(null);
    setPreview(product.image_url || "");
  };

  const handleDelete = async (id) => {
    if (!canDelete) return;

    const result =
      await confirmDelete();

    if (!result.isConfirmed) {
      return;
    }

    try {
      await deleteProduct(id);

      await successAlert(
        "Produk berhasil dihapus"
      );

      await loadProducts();
    } catch (error) {
      errorAlert(
        error?.response?.data?.message ||
        "Gagal menghapus produk"
      );
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();

    if (!canSubmit) {
      return errorAlert(
        "Anda tidak memiliki izin untuk aksi ini"
      );
    }

    if (!categoryId) {
      return errorAlert(
        "Kategori wajib dipilih"
      );
    }

    if (!name.trim()) {
      return errorAlert(
        "Nama produk wajib diisi"
      );
    }

    try {
      setSubmitting(true);

      const formData =
        new FormData();

      formData.append("category_id", categoryId);
      formData.append("name", name);
      formData.append("description", description);

      if (price !== "") {
        formData.append("price", price);
      }

      if (stock !== "") {
        formData.append("stock", stock);
      }

      if (image) {
        formData.append("image", image);
      }

      if (editingId) {
        await updateProduct(
          editingId,
          formData
        );

        await successAlert(
          "Produk berhasil diperbarui"
        );
      } else {
        await createProduct(formData);

        await successAlert(
          "Produk berhasil dibuat"
        );
      }

      resetForm();
      await loadProducts();
    } catch (error) {
      console.error(error);

      const errors =
        error?.response?.data?.errors;

      if (errors) {
        const firstError =
          Object.values(errors)[0][0];

        return errorAlert(firstError);
      }

      errorAlert(
        error?.response?.data?.message ||
        "Gagal menyimpan produk"
      );
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <div>
      <h1 className="text-3xl font-bold mb-6">
        Products
      </h1>

      {(canCreate || canUpdate) && (
        <form
          onSubmit={handleSubmit}
          className="bg-white p-4 rounded shadow mb-6"
        >
          <div className="grid grid-cols-2 gap-4">
            <select
              value={categoryId}
              disabled={!canSubmit || submitting}
              onChange={(e) =>
                setCategoryId(e.target.value)
              }
              className="border p-3 rounded disabled:bg-slate-100"
            >
              <option value="">
                Pilih Category
              </option>

              {categories.map((category) => (
                <option
                  key={category.id}
                  value={category.id}
                >
                  {category.name}
                </option>
              ))}
            </select>

            <input
              type="text"
              placeholder="Product Name"
              className="border p-3 rounded disabled:bg-slate-100"
              value={name}
              disabled={!canSubmit || submitting}
              onChange={(e) =>
                setName(e.target.value)
              }
            />

            <input
              type="number"
              placeholder="Price"
              className="border p-3 rounded disabled:bg-slate-100"
              value={price}
              disabled={!canSubmit || submitting}
              onChange={(e) =>
                setPrice(e.target.value)
              }
            />

            <input
              type="number"
              placeholder="Stock"
              className="border p-3 rounded disabled:bg-slate-100"
              value={stock}
              disabled={!canSubmit || submitting}
              onChange={(e) =>
                setStock(e.target.value)
              }
            />
          </div>

          <textarea
            placeholder="Description"
            className="border p-3 rounded w-full mt-4 disabled:bg-slate-100"
            value={description}
            disabled={!canSubmit || submitting}
            onChange={(e) =>
              setDescription(e.target.value)
            }
          />

          <input
            type="file"
            accept="image/*"
            className="mt-4"
            disabled={!canSubmit || submitting}
            onChange={handleImageChange}
          />

          {preview && (
            <img
              src={preview}
              alt="Preview"
              className="w-40 h-40 object-cover border rounded mt-4"
            />
          )}

          <div className="mt-4 flex gap-2">
            {canSubmit && (
              <button
                type="submit"
                disabled={submitting}
                className="bg-slate-900 text-white px-4 py-2 rounded disabled:opacity-50"
              >
                {submitting
                  ? "Saving..."
                  : editingId
                    ? "Update Product"
                    : "Create Product"}
              </button>
            )}

            {editingId && (
              <button
                type="button"
                onClick={resetForm}
                disabled={submitting}
                className="bg-gray-500 text-white px-4 py-2 rounded disabled:opacity-50"
              >
                Cancel
              </button>
            )}
          </div>
        </form>
      )}

      <div className="bg-white rounded shadow p-4">
        {loading ? (
          <p>Loading...</p>
        ) : (
          <table className="w-full">
            <thead>
              <tr className="bg-slate-100">
  <th className="p-3">ID</th>
  <th className="p-3">Image</th>
  <th className="p-3">Name</th>
  <th className="p-3">Category</th>
  <th className="p-3">Price</th>
  <th className="p-3">Stock</th>
  <th className="p-3">Description</th>
  <th className="p-3">Status</th>

  {(canUpdate || canDelete) && (
    <th className="p-3">Action</th>
  )}
</tr>
            </thead>

            <tbody>
              {products.length === 0 ? (
    <tr>
      <td
        colSpan={
          canUpdate || canDelete
            ? 9
            : 8
        }
        className="p-4 text-center"
      >
        Belum ada produk
      </td>
    </tr>
  ) : (
              products.map((product) => (
                <tr
                  key={product.id}
                  className="border-t"
                >
                  <td className="p-3">{product.id}</td>
                  <td className="p-3">
                    {product.image_url ? (
                      <img
                        src={product.image_url}
                        alt={product.name}
                        className="w-16 h-16 object-cover rounded border"
                      />
                    ) : "-"}
                  </td>
                  <td className="p-3">{product.name}</td>
                  <td className="p-3">
                    {product.category?.name || "-"}
                  </td>
                  <td className="p-3">
  Rp {Number(product.price || 0).toLocaleString("id-ID")}
</td>
                  <td className="p-3">{product.stock ?? 0}</td>
                  <td className="p-3">
  {product.description || "-"}
</td>
<td className="p-3">
  {product.is_active
    ? "Active"
    : "Inactive"}
</td>
                  {(canUpdate || canDelete) && (
                    <td className="p-3">
                      {canUpdate && (
                        <button
                          onClick={() =>
                            handleEdit(product)
                          }
                          className="bg-blue-600 text-white px-3 py-1 rounded mr-2"
                        >
                          Edit
                        </button>
                      )}

                      {canDelete && (
                        <button
                          onClick={() =>
                            handleDelete(product.id)
                          }
                          className="bg-red-600 text-white px-3 py-1 rounded"
                        >
                          Delete
                        </button>
                      )}
                    </td>
                  )}
                </tr>
              )))}
            </tbody>
          </table>
        )}
      </div>
    </div>
  );
}
