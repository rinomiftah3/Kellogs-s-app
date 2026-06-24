import {
  useEffect,
  useMemo,
  useState,
} from "react";

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

import {
  Package,
  CheckCircle,
  Star,
  Globe,
  Search,
  Plus,
  ImagePlus,
  Pencil,
  Trash2,
} from "lucide-react";

export default function Products() {

  const { can } = usePermission();

  /*
  |--------------------------------------------------------------------------
  | States
  |--------------------------------------------------------------------------
  */

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

  const [search, setSearch] =
    useState("");

  const [filterCategory, setFilterCategory] =
    useState("");

  /*
  |--------------------------------------------------------------------------
  | Form States
  |--------------------------------------------------------------------------
  */

  const [categoryId, setCategoryId] =
    useState("");

  const [name, setName] =
    useState("");

  const [
    shortDescription,
    setShortDescription,
  ] = useState("");

  const [description, setDescription] =
    useState("");

  const [status, setStatus] =
    useState("draft");

  const [
    isFeatured,
    setIsFeatured,
  ] = useState(false);

  const [
    isActive,
    setIsActive,
  ] = useState(true);

  const [
    publishedAt,
    setPublishedAt,
  ] = useState("");

  const [
    thumbnail,
    setThumbnail,
  ] = useState(null);

  const [preview, setPreview] =
    useState("");

  /*
  |--------------------------------------------------------------------------
  | Permissions
  |--------------------------------------------------------------------------
  */

  const canCreate =
    can("products.create");

  const canUpdate =
    can("products.update");

  const canDelete =
    can("products.delete");

  const canSubmit =
    editingId
      ? canUpdate
      : canCreate;

  /*
  |--------------------------------------------------------------------------
  | Load Products
  |--------------------------------------------------------------------------
  */

  const loadProducts = async () => {

    try {

      setLoading(true);

      const data =
        await getProducts();

      setProducts(
        Array.isArray(data)
          ? data
          : data?.data || []
      );

    } catch (error) {

      console.error(error);

      setProducts([]);

      errorAlert(
        error?.response?.data?.message ||
        "Gagal mengambil produk"
      );

    } finally {

      setLoading(false);

    }

  };

  /*
  |--------------------------------------------------------------------------
  | Load Categories
  |--------------------------------------------------------------------------
  */

  const loadCategories = async () => {

    try {

      const data =
        await getCategories();

      setCategories(
        Array.isArray(data)
          ? data
          : data?.data || []
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

  /*
  |--------------------------------------------------------------------------
  | Initial Load
  |--------------------------------------------------------------------------
  */

  useEffect(() => {

    loadProducts();

    loadCategories();

  }, []);

  /*
  |--------------------------------------------------------------------------
  | Cleanup Preview
  |--------------------------------------------------------------------------
  */

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

  /*
|--------------------------------------------------------------------------
| Thumbnail Change
|--------------------------------------------------------------------------
*/

const handleImageChange = (
  e
) => {

  const file =
    e.target.files?.[0];

  if (!file) return;

  if (
    preview &&
    preview.startsWith("blob:")
  ) {

    URL.revokeObjectURL(
      preview
    );

  }

  setThumbnail(file);

  setPreview(
    URL.createObjectURL(file)
  );

};

/*
|--------------------------------------------------------------------------
| Reset Form
|--------------------------------------------------------------------------
*/

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

  setShortDescription("");

  setDescription("");

  setStatus("draft");

  setIsFeatured(false);

  setIsActive(true);

  setPublishedAt("");

  setThumbnail(null);

  setPreview("");

};

/*
|--------------------------------------------------------------------------
| Edit Product
|--------------------------------------------------------------------------
*/

const handleEdit = (
  product
) => {

  if (!canUpdate) return;

  setEditingId(
    product.slug
  );

  setCategoryId(
    product.category_id || ""
  );

  setName(
    product.name || ""
  );

  setShortDescription(
    product.short_description || ""
  );

  setDescription(
    product.description || ""
  );

  setStatus(
    product.status || "draft"
  );

  setIsFeatured(
    Boolean(
      product.is_featured
    )
  );

  setIsActive(
    Boolean(
      product.is_active
    )
  );

  setPublishedAt(

    product.published_at
      ? product
          .published_at
          .slice(0, 16)
      : ""

  );

  setThumbnail(null);

  setPreview(
    product.thumbnail_url || ""
  );

  window.scrollTo({

    top: 0,

    behavior: "smooth",

  });

};

/*
|--------------------------------------------------------------------------
| Delete Product
|--------------------------------------------------------------------------
*/

const handleDelete = async (
  slug
) => {

  if (!canDelete) return;

  const result =
    await confirmDelete();

  if (
    !result.isConfirmed
  ) {
    return;
  }

  try {

    await deleteProduct(
      slug
    );

    await successAlert(
      "Produk berhasil dihapus"
    );

    await loadProducts();

  } catch (error) {

    console.error(error);

    const errors =
      error?.response?.data
        ?.errors;

    if (errors) {

      const firstError =
        Object.values(
          errors
        )[0]?.[0];

      return errorAlert(
        firstError
      );

    }

    errorAlert(

      error?.response?.data
        ?.message ||

      "Gagal menghapus produk"

    );

  }

};

/*
|--------------------------------------------------------------------------
| Submit Product
|--------------------------------------------------------------------------
*/

const handleSubmit = async (
  e
) => {

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

    formData.append(
      "category_id",
      categoryId
    );

    formData.append(
      "name",
      name
    );

    formData.append(
      "short_description",
      shortDescription
    );

    formData.append(
      "description",
      description
    );

    formData.append(
      "status",
      status
    );

    formData.append(
      "is_featured",
      isFeatured
        ? "1"
        : "0"
    );

    formData.append(
      "is_active",
      isActive
        ? "1"
        : "0"
    );

    if (
      publishedAt
    ) {

      formData.append(
        "published_at",
        publishedAt
      );

    }

    if (
      thumbnail
    ) {

      formData.append(
        "thumbnail",
        thumbnail
      );

    }

    if (
      editingId
    ) {

      await updateProduct(

        editingId,

        formData

      );

      await successAlert(
        "Produk berhasil diperbarui"
      );

    } else {

      await createProduct(
        formData
      );

      await successAlert(
        "Produk berhasil dibuat"
      );

    }

    resetForm();

    await loadProducts();

  } catch (error) {

    console.error(error);

    const errors =
      error?.response?.data
        ?.errors;

    if (errors) {

      const firstError =
        Object.values(
          errors
        )[0]?.[0];

      return errorAlert(
        firstError
      );

    }

    errorAlert(

      error?.response?.data
        ?.message ||

      "Gagal menyimpan produk"

    );

  } finally {

    setSubmitting(false);

  }

};

/*
|--------------------------------------------------------------------------
| Search & Filter
|--------------------------------------------------------------------------
*/

const filteredProducts =
  useMemo(() => {

    return products.filter(
      (product) => {

        const keyword =
          search.toLowerCase();

        const matchSearch =

          product.name
            ?.toLowerCase()
            .includes(
              keyword
            ) ||

          product.slug
            ?.toLowerCase()
            .includes(
              keyword
            ) ||

          product.short_description
            ?.toLowerCase()
            .includes(
              keyword
            ) ||

          product.description
            ?.toLowerCase()
            .includes(
              keyword
            );

        const matchCategory =

          !filterCategory ||

          String(
            product.category_id
          ) ===
          String(
            filterCategory
          );

        return (
          matchSearch &&
          matchCategory
        );

      }
    );

  }, [
    products,
    search,
    filterCategory,
  ]);

/*
|--------------------------------------------------------------------------
| Statistics
|--------------------------------------------------------------------------
*/

const totalProducts =
  products.length;

const activeProducts =
  products.filter(
    (product) =>
      product.is_active
  ).length;

const featuredProducts =
  products.filter(
    (product) =>
      product.is_featured
  ).length;

const publishedProducts =
  products.filter(
    (product) =>
      product.is_published
  ).length;

return (
  <div className="space-y-6">

    {/* Hero */}
    <div
      className="
        rounded-3xl
        bg-gradient-to-r
        from-red-600
        via-red-500
        to-orange-500
        p-8
        text-white
        shadow-xl
        relative
        overflow-hidden
      "
    >

      <div
        className="
          absolute
          -top-16
          -right-16
          w-64
          h-64
          rounded-full
          bg-white/10
        "
      />

      <div
        className="
          absolute
          -bottom-20
          -left-20
          w-72
          h-72
          rounded-full
          bg-white/10
        "
      />

      <div
        className="
          relative
          z-10
          flex
          flex-col
          lg:flex-row
          lg:items-center
          lg:justify-between
          gap-6
        "
      >

        <div>

          <div className="flex items-center gap-4">

            <div
              className="
                w-16
                h-16
                rounded-2xl
                bg-white/10
                backdrop-blur
                flex
                items-center
                justify-center
              "
            >
              <Package className="w-8 h-8" />
            </div>

            <div>

              <h1 className="text-4xl font-bold">
                Products
              </h1>

              <p className="text-red-100 mt-2">
                Kelola master produk
                Kellogg's.
              </p>

            </div>

          </div>

        </div>

        <div className="flex flex-wrap gap-3">

          <div
            className="
              bg-white/10
              backdrop-blur
              rounded-2xl
              px-4
              py-2
              text-sm
            "
          >
            Total: {totalProducts}
          </div>

          <div
            className="
              bg-white/10
              backdrop-blur
              rounded-2xl
              px-4
              py-2
              text-sm
            "
          >
            Active: {activeProducts}
          </div>

        </div>

      </div>

    </div>

    {/* Statistics */}
    <div
      className="
        grid
        grid-cols-1
        sm:grid-cols-2
        xl:grid-cols-4
        gap-6
      "
    >

      {/* Total */}
      <div
        className="
          bg-white
          rounded-3xl
          p-6
          shadow-sm
          border
          border-slate-100
        "
      >

        <div className="flex justify-between items-center">

          <div>

            <p className="text-slate-500 text-sm">
              Total Products
            </p>

            <h3 className="text-4xl font-bold mt-2">
              {totalProducts}
            </h3>

          </div>

          <div
            className="
              w-14
              h-14
              rounded-2xl
              bg-blue-50
              flex
              items-center
              justify-center
            "
          >
            <Package
              className="
                w-7
                h-7
                text-blue-600
              "
            />
          </div>

        </div>

      </div>

      {/* Active */}
      <div
        className="
          bg-white
          rounded-3xl
          p-6
          shadow-sm
          border
          border-slate-100
        "
      >

        <div className="flex justify-between items-center">

          <div>

            <p className="text-slate-500 text-sm">
              Active
            </p>

            <h3 className="text-4xl font-bold mt-2">
              {activeProducts}
            </h3>

          </div>

          <div
            className="
              w-14
              h-14
              rounded-2xl
              bg-green-50
              flex
              items-center
              justify-center
            "
          >
            <CheckCircle
              className="
                w-7
                h-7
                text-green-600
              "
            />
          </div>

        </div>

      </div>

      {/* Featured */}
      <div
        className="
          bg-white
          rounded-3xl
          p-6
          shadow-sm
          border
          border-slate-100
        "
      >

        <div className="flex justify-between items-center">

          <div>

            <p className="text-slate-500 text-sm">
              Featured
            </p>

            <h3 className="text-4xl font-bold mt-2">
              {featuredProducts}
            </h3>

          </div>

          <div
            className="
              w-14
              h-14
              rounded-2xl
              bg-yellow-50
              flex
              items-center
              justify-center
            "
          >
            <Star
              className="
                w-7
                h-7
                text-yellow-600
              "
            />
          </div>

        </div>

      </div>

      {/* Published */}
      <div
        className="
          bg-white
          rounded-3xl
          p-6
          shadow-sm
          border
          border-slate-100
        "
      >

        <div className="flex justify-between items-center">

          <div>

            <p className="text-slate-500 text-sm">
              Published
            </p>

            <h3 className="text-4xl font-bold mt-2">
              {publishedProducts}
            </h3>

          </div>

          <div
            className="
              w-14
              h-14
              rounded-2xl
              bg-purple-50
              flex
              items-center
              justify-center
            "
          >
            <Globe
              className="
                w-7
                h-7
                text-purple-600
              "
            />
          </div>

        </div>

      </div>

    </div>

    {/* Search & Filter */}
    <div
      className="
        bg-white
        rounded-3xl
        shadow-sm
        border
        border-slate-100
        p-6
      "
    >

      <div
        className="
          flex
          flex-col
          lg:flex-row
          gap-4
        "
      >

        <div className="relative flex-1">

          <Search
            className="
              absolute
              left-4
              top-1/2
              -translate-y-1/2
              w-5
              h-5
              text-slate-400
            "
          />

          <input
            type="text"
            placeholder="Cari produk..."
            value={search}
            onChange={(e) =>
              setSearch(
                e.target.value
              )
            }
            className="
              w-full
              rounded-2xl
              border
              border-slate-200
              pl-12
              pr-4
              py-3
              focus:outline-none
              focus:ring-2
              focus:ring-red-500
            "
          />

        </div>

        <select
          value={filterCategory}
          onChange={(e) =>
            setFilterCategory(
              e.target.value
            )
          }
          className="
            rounded-2xl
            border
            border-slate-200
            px-4
            py-3
            focus:outline-none
            focus:ring-2
            focus:ring-red-500
          "
        >

          <option value="">
            Semua Kategori
          </option>

          {categories.map(
            (category) => (

              <option
                key={category.id}
                value={category.id}
              >
                {category.name}
              </option>

            )
          )}

        </select>

      </div>

    </div>

    
    {/* Product Form */}
{(canCreate || canUpdate) && (

  <div className="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">

    <div className="flex items-center gap-3 mb-8">

      <div className="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center">

        <Plus className="w-6 h-6 text-red-600" />

      </div>

      <div>

        <h2 className="text-2xl font-bold">

          {editingId
            ? "Edit Product"
            : "Create Product"}

        </h2>

        <p className="text-slate-500">
          Kelola master produk Kellogg's.
        </p>

      </div>

    </div>

    <form
      onSubmit={handleSubmit}
      className="grid xl:grid-cols-3 gap-8"
    >

      {/* Left */}
      <div className="xl:col-span-2 space-y-5">

        <div className="grid md:grid-cols-2 gap-5">

          {/* Category */}
          <select
            value={categoryId}
            disabled={
              !canSubmit ||
              submitting
            }
            onChange={(e) =>
              setCategoryId(
                e.target.value
              )
            }
            className="
              rounded-2xl
              border
              border-slate-200
              px-4
              py-3
            "
          >

            <option value="">
              Pilih Kategori
            </option>

            {categories.map(
              (category) => (

                <option
                  key={category.id}
                  value={category.id}
                >
                  {category.name}
                </option>

              )
            )}

          </select>

          {/* Product Name */}
          <input
            type="text"
            placeholder="Nama Produk"
            value={name}
            disabled={
              !canSubmit ||
              submitting
            }
            onChange={(e) =>
              setName(
                e.target.value
              )
            }
            className="
              rounded-2xl
              border
              border-slate-200
              px-4
              py-3
            "
          />

        </div>

        {/* Short Description */}
        <textarea
          rows={2}
          placeholder="Deskripsi Singkat"
          value={shortDescription}
          disabled={
            !canSubmit ||
            submitting
          }
          onChange={(e) =>
            setShortDescription(
              e.target.value
            )
          }
          className="
            w-full
            rounded-2xl
            border
            border-slate-200
            px-4
            py-3
            resize-none
          "
        />

        {/* Description */}
        <textarea
          rows={5}
          placeholder="Deskripsi Produk"
          value={description}
          disabled={
            !canSubmit ||
            submitting
          }
          onChange={(e) =>
            setDescription(
              e.target.value
            )
          }
          className="
            w-full
            rounded-2xl
            border
            border-slate-200
            px-4
            py-3
            resize-none
          "
        />

        {/* Status */}
        <div className="grid md:grid-cols-2 gap-5">

          <select
            value={status}
            disabled={
              !canSubmit ||
              submitting
            }
            onChange={(e) =>
              setStatus(
                e.target.value
              )
            }
            className="
              rounded-2xl
              border
              border-slate-200
              px-4
              py-3
            "
          >

            <option value="draft">
              Draft
            </option>

            <option value="active">
              Active
            </option>

            <option value="inactive">
              Inactive
            </option>

            <option value="archived">
              Archived
            </option>

          </select>

          <input
            type="datetime-local"
            value={publishedAt}
            disabled={
              !canSubmit ||
              submitting
            }
            onChange={(e) =>
              setPublishedAt(
                e.target.value
              )
            }
            className="
              rounded-2xl
              border
              border-slate-200
              px-4
              py-3
            "
          />

        </div>

        {/* Switches */}
        <div className="grid md:grid-cols-2 gap-5">

          <label
            className="
              flex
              items-center
              gap-3
              rounded-2xl
              border
              border-slate-200
              px-4
              py-4
              cursor-pointer
            "
          >

            <input
              type="checkbox"
              checked={isFeatured}
              disabled={
                !canSubmit ||
                submitting
              }
              onChange={(e) =>
                setIsFeatured(
                  e.target.checked
                )
              }
            />

            <span className="font-medium">
              Featured Product
            </span>

          </label>

          <label
            className="
              flex
              items-center
              gap-3
              rounded-2xl
              border
              border-slate-200
              px-4
              py-4
              cursor-pointer
            "
          >

            <input
              type="checkbox"
              checked={isActive}
              disabled={
                !canSubmit ||
                submitting
              }
              onChange={(e) =>
                setIsActive(
                  e.target.checked
                )
              }
            />

            <span className="font-medium">
              Active Product
            </span>

          </label>

        </div>

      </div>

      {/* Thumbnail */}
      <div>

        <div
          className="
            border-2
            border-dashed
            border-slate-200
            rounded-3xl
            p-6
            text-center
          "
        >

          <ImagePlus
            className="
              w-12
              h-12
              mx-auto
              text-slate-300
              mb-4
            "
          />

          <p className="font-medium text-slate-700">
            Upload Thumbnail
          </p>

          <p className="text-sm text-slate-500 mt-1">
            JPG, PNG, WEBP
          </p>

          <input
            type="file"
            accept="image/*"
            disabled={
              !canSubmit ||
              submitting
            }
            onChange={
              handleImageChange
            }
            className="mt-5 w-full"
          />

          {preview && (

            <img
              src={preview}
              alt="Preview"
              className="
                w-full
                h-56
                object-cover
                rounded-2xl
                border
                mt-5
              "
            />

          )}

        </div>

        <div className="flex gap-3 mt-6">

          {canSubmit && (

            <button
              type="submit"
              disabled={submitting}
              className="
                flex-1
                inline-flex
                items-center
                justify-center
                gap-2
                rounded-2xl
                bg-gradient-to-r
                from-red-600
                to-red-700
                px-6
                py-3
                font-semibold
                text-white
                shadow-lg
                hover:shadow-xl
                hover:scale-[1.01]
                transition-all
                disabled:opacity-70
                disabled:cursor-not-allowed
              "
            >

              <Plus className="w-5 h-5" />

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
              className="
                px-6
                py-3
                rounded-2xl
                border
                border-slate-200
                text-slate-700
                hover:bg-slate-50
                transition
              "
            >
              Cancel
            </button>

          )}

        </div>

      </div>

    </form>

  </div>

)}
{/* Products Table */}
<div className="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">

  <div className="px-6 py-5 border-b border-slate-100">

    <h2 className="text-xl font-bold text-slate-900">
      Products List
    </h2>

    <p className="text-sm text-slate-500 mt-1">
      Menampilkan {filteredProducts.length} produk.
    </p>

  </div>

  <div className="overflow-x-auto">

    <table className="w-full">

      <thead className="bg-slate-50">

        <tr>

          <th className="px-6 py-4 text-left text-sm font-semibold text-slate-600">
            Product
          </th>

          <th className="px-6 py-4 text-left text-sm font-semibold text-slate-600">
            Category
          </th>

          <th className="px-6 py-4 text-center text-sm font-semibold text-slate-600">
            Status
          </th>

          <th className="px-6 py-4 text-center text-sm font-semibold text-slate-600">
            Featured
          </th>

          <th className="px-6 py-4 text-center text-sm font-semibold text-slate-600">
            Published
          </th>

          <th className="px-6 py-4 text-center text-sm font-semibold text-slate-600">
            SKU
          </th>

          <th className="px-6 py-4 text-center text-sm font-semibold text-slate-600">
            Reviews
          </th>

          {(canUpdate || canDelete) && (

            <th className="px-6 py-4 text-right text-sm font-semibold text-slate-600">
              Actions
            </th>

          )}

        </tr>

      </thead>

      <tbody>

        {loading ? (

          <tr>

            <td
              colSpan={
                canUpdate || canDelete
                  ? 8
                  : 7
              }
              className="py-16 text-center text-slate-500"
            >
              Loading products...
            </td>

          </tr>

        ) : filteredProducts.length === 0 ? (

          <tr>

            <td
              colSpan={
                canUpdate || canDelete
                  ? 8
                  : 7
              }
              className="py-20 text-center"
            >

              <div className="flex flex-col items-center">

                <Package className="w-16 h-16 text-slate-300 mb-4" />

                <h3 className="text-lg font-semibold text-slate-700">
                  Belum ada produk
                </h3>

                <p className="text-slate-500 mt-2">
                  Mulai tambahkan produk pertama Anda.
                </p>

              </div>

            </td>

          </tr>

        ) : (

          filteredProducts.map(
            (product) => (

              <tr
                key={product.id}
                className="
                  border-t
                  border-slate-100
                  hover:bg-slate-50
                  transition
                "
              >

                {/* Product */}
                <td className="px-6 py-5">

                  <div className="flex items-center gap-4">

                    {product.thumbnail_url ? (

                      <img
                        src={product.thumbnail_url}
                        alt={product.name}
                        className="
                          w-16
                          h-16
                          rounded-2xl
                          object-cover
                          border
                          border-slate-200
                        "
                      />

                    ) : (

                      <div
                        className="
                          w-16
                          h-16
                          rounded-2xl
                          bg-slate-100
                          flex
                          items-center
                          justify-center
                        "
                      >

                        <Package className="w-7 h-7 text-slate-400" />

                      </div>

                    )}

                    <div>

                      <p className="font-semibold text-slate-900">
                        {product.name}
                      </p>

                      <p className="text-xs text-slate-400 mt-1">
                        {product.slug}
                      </p>

                      <p className="text-xs text-slate-500 mt-1">
                        {product.short_description || "-"}
                      </p>

                    </div>

                  </div>

                </td>

                {/* Category */}
                <td className="px-6 py-5 text-slate-700">

                  {product.category?.name || "-"}

                </td>

                {/* Status */}
                <td className="px-6 py-5 text-center">

                  <span
                    className={`
                      inline-flex
                      items-center
                      px-3
                      py-1
                      rounded-full
                      text-xs
                      font-semibold
                      ${
                        product.status === "active"
                          ? "bg-green-100 text-green-700"
                          : product.status === "draft"
                          ? "bg-yellow-100 text-yellow-700"
                          : product.status === "archived"
                          ? "bg-slate-200 text-slate-700"
                          : "bg-red-100 text-red-700"
                      }
                    `}
                  >

                    {product.status_label}

                  </span>

                </td>

                {/* Featured */}
                <td className="px-6 py-5 text-center">

                  <span
                    className={`
                      inline-flex
                      items-center
                      px-3
                      py-1
                      rounded-full
                      text-xs
                      font-semibold
                      ${
                        product.is_featured
                          ? "bg-blue-100 text-blue-700"
                          : "bg-slate-100 text-slate-500"
                      }
                    `}
                  >

                    {product.is_featured
                      ? "Yes"
                      : "No"}

                  </span>

                </td>

                {/* Published */}
                <td className="px-6 py-5 text-center">

                  <span
                    className={`
                      inline-flex
                      items-center
                      px-3
                      py-1
                      rounded-full
                      text-xs
                      font-semibold
                      ${
                        product.is_published
                          ? "bg-green-100 text-green-700"
                          : "bg-slate-100 text-slate-500"
                      }
                    `}
                  >

                    {product.is_published
                      ? "Published"
                      : "Unpublished"}

                  </span>

                </td>

                {/* SKU Count */}
                <td className="px-6 py-5 text-center font-semibold text-slate-700">

                  {product.sku_count ?? 0}

                </td>

                {/* Review Count */}
                <td className="px-6 py-5 text-center font-semibold text-slate-700">

                  {product.review_count ?? 0}

                </td>

                {/* Actions */}
                {(canUpdate || canDelete) && (

                  <td className="px-6 py-5">

                    <div className="flex justify-end gap-2">

                      {canUpdate && (

                        <button
                          onClick={() =>
                            handleEdit(
                              product
                            )
                          }
                          className="
                            inline-flex
                            items-center
                            gap-2
                            px-4
                            py-2
                            rounded-xl
                            bg-blue-50
                            text-blue-700
                            hover:bg-blue-100
                            transition
                          "
                        >

                          <Pencil className="w-4 h-4" />

                          Edit

                        </button>

                      )}

                      {canDelete && (

                        <button
                          onClick={() =>
                            handleDelete(
                              product.slug
                            )
                          }
                          className="
                            inline-flex
                            items-center
                            gap-2
                            px-4
                            py-2
                            rounded-xl
                            bg-red-50
                            text-red-700
                            hover:bg-red-100
                            transition
                          "
                        >

                          <Trash2 className="w-4 h-4" />

                          Delete

                        </button>

                      )}

                    </div>

                  </td>

                )}

              </tr>

            )
          )

        )}

      </tbody>

    </table>

  </div>

</div>

    </div>
  );
}