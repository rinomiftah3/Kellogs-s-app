import {
  useEffect,
  useMemo,
  useState,
} from "react";

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

import {
  FolderOpen,
  Plus,
  Search,
  Edit3,
  Trash2,
  Layers,
  CheckCircle2,
  FolderTree,
  Image as ImageIcon,
  Package,
} from "lucide-react";

export default function Categories() {

  const { can } =
    usePermission();

  /*
  |--------------------------------------------------------------------------
  | Permissions
  |--------------------------------------------------------------------------
  */

  const canCreate =
    can("categories.create");

  const canUpdate =
    can("categories.update");

  const canDelete =
    can("categories.delete");

  /*
  |--------------------------------------------------------------------------
  | States
  |--------------------------------------------------------------------------
  */

  const [categories, setCategories] =
    useState([]);

  const [loading, setLoading] =
    useState(false);

  const [submitting, setSubmitting] =
    useState(false);

  const [search, setSearch] =
    useState("");

  /*
  |--------------------------------------------------------------------------
  | Form States
  |--------------------------------------------------------------------------
  */

  const [editingId, setEditingId] =
    useState(null);

  const [name, setName] =
    useState("");

  const [description, setDescription] =
    useState("");

  const [parentId, setParentId] =
    useState("");

  const [sortOrder, setSortOrder] =
    useState(0);

  const [isActive, setIsActive] =
    useState(true);

  const [image, setImage] =
    useState(null);

  const [imagePreview, setImagePreview] =
    useState(null);

  /*
  |--------------------------------------------------------------------------
  | Derived
  |--------------------------------------------------------------------------
  */

  const canSubmit =
    editingId
      ? canUpdate
      : canCreate;

  /*
  |--------------------------------------------------------------------------
  | Load Categories
  |--------------------------------------------------------------------------
  */

  const loadCategories =
    async () => {

      try {

        setLoading(true);

        const data =
          await getCategories();

        setCategories(
          Array.isArray(data)
            ? data
            : []
        );

      } catch (error) {

        console.error(error);

        setCategories([]);

        errorAlert(
          error?.response?.data
            ?.message ||
            "Gagal mengambil data kategori."
        );

      } finally {

        setLoading(false);

      }
    };

  useEffect(() => {

    loadCategories();

  }, []);

  /*
  |--------------------------------------------------------------------------
  | Reset Form
  |--------------------------------------------------------------------------
  */

  const resetForm = () => {

    setEditingId(null);

    setName("");

    setDescription("");

    setParentId("");

    setSortOrder(0);

    setIsActive(true);

    setImage(null);

    setImagePreview(null);
  };

  /*
  |--------------------------------------------------------------------------
  | Submit
  |--------------------------------------------------------------------------
  */

  const handleSubmit =
    async (e) => {

      e.preventDefault();

      if (!canSubmit) {

        return errorAlert(
          "Anda tidak memiliki izin."
        );
      }

      if (!name.trim()) {

        return errorAlert(
          "Nama kategori wajib diisi."
        );
      }

      try {

        setSubmitting(true);

        const payload = {

          name,

          description,

          parent_id:
            parentId || null,

          sort_order:
            Number(sortOrder),

          is_active:
            isActive,
        };

        if (image) {

          payload.image =
            image;
        }

        if (editingId) {

          await updateCategory(
            editingId,
            payload
          );

          await successAlert(
            "Kategori berhasil diperbarui."
          );

        } else {

          await createCategory(
            payload
          );

          await successAlert(
            "Kategori berhasil dibuat."
          );
        }

        resetForm();

        await loadCategories();

      } catch (error) {

        console.error(error);

        const errors =
          error?.response?.data
            ?.errors;

        const message =

          errors?.name?.[0]

          ||

          errors?.parent_id?.[0]

          ||

          errors?.image?.[0]

          ||

          error?.response?.data
            ?.message

          ||

          "Gagal menyimpan kategori.";

        errorAlert(message);

      } finally {

        setSubmitting(false);

      }
    };

  /*
  |--------------------------------------------------------------------------
  | Edit
  |--------------------------------------------------------------------------
  */

  const handleEdit =
    (category) => {

      if (!canUpdate) {

        return;
      }

      setEditingId(
        category.slug
      );

      setName(
        category.name || ""
      );

      setDescription(
        category.description || ""
      );

      setParentId(
        category.parent_id || ""
      );

      setSortOrder(
        category.sort_order ?? 0
      );

      setIsActive(
        category.is_active !== false
      );

      setImage(null);

      setImagePreview(
        category.image_url || null
      );

      window.scrollTo({

        top: 0,

        behavior: "smooth",
      });
    };

  /*
  |--------------------------------------------------------------------------
  | Delete
  |--------------------------------------------------------------------------
  */

  const handleDelete =
    async (slug) => {

      if (!canDelete) {

        return;
      }

      const result =
        await confirmDelete();

      if (
        !result.isConfirmed
      ) {

        return;
      }

      try {

        await deleteCategory(
          slug
        );

        await successAlert(
          "Kategori berhasil dihapus."
        );

        await loadCategories();

      } catch (error) {

        console.error(error);

        errorAlert(

          error?.response?.data
            ?.message ||

          "Gagal menghapus kategori."
        );
      }
    };

  /*
  |--------------------------------------------------------------------------
  | Image
  |--------------------------------------------------------------------------
  */

  const handleImageChange =
    (e) => {

      const file =
        e.target.files?.[0];

      if (!file) {

        return;
      }

      setImage(file);

      setImagePreview(

        URL.createObjectURL(
          file
        )
      );
    };
  /*
  |--------------------------------------------------------------------------
  | Filtering
  |--------------------------------------------------------------------------
  */

  const filteredCategories =
    useMemo(() => {

      return categories.filter(
        (category) =>

          category.name
            ?.toLowerCase()
            .includes(
              search.toLowerCase()
            )

          ||

          category.description
            ?.toLowerCase()
            .includes(
              search.toLowerCase()
            )

          ||

          category.slug
            ?.toLowerCase()
            .includes(
              search.toLowerCase()
            )
      );

    }, [
      categories,
      search,
    ]);

  /*
  |--------------------------------------------------------------------------
  | Statistics
  |--------------------------------------------------------------------------
  */

  const totalCategories =
    categories.length;

  const activeCategories =
    categories.filter(
      (category) =>
        category.is_active
    ).length;

  const parentCategories =
    categories.filter(
      (category) =>
        category.parent_id === null
    ).length;

  const childCategories =
    categories.filter(
      (category) =>
        category.parent_id !== null
    ).length;

  return (

    <div className="space-y-6">

      {/* Header */}
      <div
        className="
          rounded-3xl
          bg-gradient-to-r
          from-slate-900
          via-slate-800
          to-slate-900
          p-8
          text-white
          shadow-xl
        "
      >

        <div
          className="
            flex
            flex-col
            lg:flex-row
            lg:items-center
            lg:justify-between
            gap-6
          "
        >

          <div>

            <div
              className="
                flex
                items-center
                gap-3
                mb-3
              "
            >

              <div
                className="
                  w-14
                  h-14
                  rounded-2xl
                  bg-white/10
                  backdrop-blur
                  flex
                  items-center
                  justify-center
                "
              >

                <FolderOpen
                  className="
                    w-7
                    h-7
                    text-red-400
                  "
                />

              </div>

              <div>

                <h1
                  className="
                    text-4xl
                    font-bold
                  "
                >
                  Categories
                </h1>

                <p
                  className="
                    text-slate-300
                    mt-1
                  "
                >
                  Kelola seluruh kategori produk Kellogg's.
                </p>

              </div>

            </div>

          </div>

          <div
            className="
              flex
              flex-wrap
              gap-3
            "
          >

            <div
              className="
                px-4
                py-2
                rounded-2xl
                bg-white/10
                text-sm
              "
            >
              Total: {totalCategories}
            </div>

            <div
              className="
                px-4
                py-2
                rounded-2xl
                bg-green-500/20
                text-green-300
                text-sm
              "
            >
              Active: {activeCategories}
            </div>

          </div>

        </div>

      </div>

      {/* Statistics */}
      <div
        className="
          grid
          grid-cols-1
          md:grid-cols-2
          xl:grid-cols-4
          gap-6
        "
      >

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

          <div className="flex items-center justify-between">

            <div>

              <p className="text-slate-500 text-sm">
                Total Categories
              </p>

              <h3 className="text-4xl font-bold mt-2">
                {totalCategories}
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

              <Layers
                className="
                  w-7
                  h-7
                  text-blue-600
                "
              />

            </div>

          </div>

        </div>

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

          <div className="flex items-center justify-between">

            <div>

              <p className="text-slate-500 text-sm">
                Active
              </p>

              <h3 className="text-4xl font-bold mt-2">
                {activeCategories}
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

              <CheckCircle2
                className="
                  w-7
                  h-7
                  text-green-600
                "
              />

            </div>

          </div>

        </div>

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

          <div className="flex items-center justify-between">

            <div>

              <p className="text-slate-500 text-sm">
                Parent Categories
              </p>

              <h3 className="text-4xl font-bold mt-2">
                {parentCategories}
              </h3>

            </div>

            <div
              className="
                w-14
                h-14
                rounded-2xl
                bg-amber-50
                flex
                items-center
                justify-center
              "
            >

              <FolderTree
                className="
                  w-7
                  h-7
                  text-amber-600
                "
              />

            </div>

          </div>

        </div>

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

          <div className="flex items-center justify-between">

            <div>

              <p className="text-slate-500 text-sm">
                Child Categories
              </p>

              <h3 className="text-4xl font-bold mt-2">
                {childCategories}
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

              <FolderOpen
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

      {/* Search */}
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

        <div className="relative max-w-md">

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
            placeholder="Cari kategori..."
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

      </div>

      {/* Form */}
      {(canCreate || canUpdate) && (

        <div
          className="
            bg-white
            rounded-3xl
            shadow-sm
            border
            border-slate-100
            p-8
          "
        >

          <div className="mb-6">

            <h2
              className="
                text-2xl
                font-bold
              "
            >

              {editingId
                ? "Edit Category"
                : "Create Category"}

            </h2>

            <p className="text-slate-500">

              Tambahkan atau perbarui kategori.

            </p>

          </div>

          <form
            onSubmit={handleSubmit}
            className="space-y-5"
          >

            {/* Name */}
            <div>

              <label className="block mb-2 text-sm font-medium">

                Category Name

              </label>

              <input
                type="text"
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
                  w-full
                  rounded-2xl
                  border
                  border-slate-200
                  px-4
                  py-3
                  focus:outline-none
                  focus:ring-2
                  focus:ring-red-500
                "
              />

            </div>

            {/* Parent + Sort */}
            <div
              className="
                grid
                grid-cols-1
                md:grid-cols-2
                gap-5
              "
            >

              <div>

                <label className="block mb-2 text-sm font-medium">

                  Parent Category

                </label>

                <select
                  value={parentId}
                  disabled={submitting}
                  onChange={(e) =>
                    setParentId(
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
                  "
                >

                  <option value="">
                    None (Parent Category)
                  </option>

                  {categories
                    .filter(
                      (item) =>
                        item.parent_id === null
                    )
                    .map((item) => (

                      <option
                        key={item.id}
                        value={item.id}
                      >
                        {item.name}
                      </option>

                    ))}

                </select>

              </div>

              <div>

                <label className="block mb-2 text-sm font-medium">

                  Sort Order

                </label>

                <input
                  type="number"
                  min="0"
                  value={sortOrder}
                  onChange={(e) =>
                    setSortOrder(
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
                  "
                />

              </div>
            </div>
                        {/* Description */}
            <div>

              <label className="block mb-2 text-sm font-medium">

                Description

              </label>

              <textarea
                rows={4}
                value={description}
                disabled={
                  submitting
                }
                onChange={(e) =>
                  setDescription(
                    e.target.value
                  )
                }
                placeholder="Masukkan deskripsi kategori"
                className="
                  w-full
                  rounded-2xl
                  border
                  border-slate-200
                  px-4
                  py-3
                  resize-none
                  focus:outline-none
                  focus:ring-2
                  focus:ring-red-500
                "
              />

            </div>

            {/* Image Upload */}
            <div>

              <label className="block mb-2 text-sm font-medium">

                Category Image

              </label>

              <input
                type="file"
                accept="image/*"
                disabled={
                  submitting
                }
                onChange={
                  handleImageChange
                }
                className="
                  block
                  w-full
                  rounded-2xl
                  border
                  border-slate-200
                  px-4
                  py-3
                  file:mr-4
                  file:rounded-xl
                  file:border-0
                  file:bg-red-50
                  file:px-4
                  file:py-2
                  file:text-red-700
                  file:font-medium
                "
              />

              {imagePreview && (

                <div className="mt-4">

                  <img
                    src={imagePreview}
                    alt="Preview"
                    className="
                      w-28
                      h-28
                      rounded-2xl
                      object-cover
                      border
                      border-slate-200
                    "
                  />

                </div>

              )}

            </div>

            {/* Status */}
            <div>

              <label className="block mb-2 text-sm font-medium">

                Status

              </label>

              <label
                className="
                  inline-flex
                  items-center
                  gap-3
                  cursor-pointer
                "
              >

                <input
                  type="checkbox"
                  checked={
                    isActive
                  }
                  disabled={
                    submitting
                  }
                  onChange={(e) =>
                    setIsActive(
                      e.target.checked
                    )
                  }
                  className="
                    w-5
                    h-5
                    rounded
                    text-red-600
                  "
                />

                <span
                  className="
                    text-slate-700
                    font-medium
                  "
                >

                  {isActive
                    ? "Active"
                    : "Inactive"}

                </span>

              </label>

            </div>

            {/* Buttons */}
            <div
              className="
                flex
                flex-wrap
                items-center
                gap-3
                pt-2
              "
            >

              {canSubmit && (

                <button
                  type="submit"
                  disabled={
                    submitting
                  }
                  className="
                    inline-flex
                    items-center
                    gap-2
                    px-6
                    py-3
                    rounded-2xl
                    bg-gradient-to-r
                    from-red-600
                    to-red-700
                    text-white
                    font-semibold
                    shadow-lg
                    hover:shadow-xl
                    transition
                    disabled:opacity-70
                  "
                >

                  <Plus className="w-5 h-5" />

                  {submitting

                    ? "Saving..."

                    : editingId

                    ? "Update Category"

                    : "Create Category"}

                </button>

              )}

              {editingId && (

                <button
                  type="button"
                  onClick={
                    resetForm
                  }
                  disabled={
                    submitting
                  }
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

          </form>

        </div>

      )}

      {/* Categories Table */}
      <div
        className="
          bg-white
          rounded-3xl
          shadow-sm
          border
          border-slate-100
          overflow-hidden
        "
      >

        <div
          className="
            px-6
            py-5
            border-b
            border-slate-100
          "
        >

          <h2
            className="
              text-xl
              font-bold
              text-slate-900
            "
          >

            Categories List

          </h2>

          <p
            className="
              text-sm
              text-slate-500
              mt-1
            "
          >

            Menampilkan
            {" "}
            {filteredCategories.length}
            {" "}
            kategori.

          </p>

        </div>

        <div className="overflow-x-auto">

          <table className="w-full">

            <thead
              className="
                bg-slate-50
              "
            >

              <tr>

                <th
                  className="
                    px-6
                    py-4
                    text-left
                    text-sm
                    font-semibold
                    text-slate-600
                  "
                >
                  ID
                </th>

                <th
                  className="
                    px-6
                    py-4
                    text-left
                    text-sm
                    font-semibold
                    text-slate-600
                  "
                >
                  Image
                </th>

                <th
                  className="
                    px-6
                    py-4
                    text-left
                    text-sm
                    font-semibold
                    text-slate-600
                  "
                >
                  Category
                </th>

                <th
                  className="
                    px-6
                    py-4
                    text-left
                    text-sm
                    font-semibold
                    text-slate-600
                  "
                >
                  Parent
                </th>

                <th
                  className="
                    px-6
                    py-4
                    text-left
                    text-sm
                    font-semibold
                    text-slate-600
                  "
                >
                  Products
                </th>

                <th
                  className="
                    px-6
                    py-4
                    text-left
                    text-sm
                    font-semibold
                    text-slate-600
                  "
                >
                  Children
                </th>

                <th
                  className="
                    px-6
                    py-4
                    text-left
                    text-sm
                    font-semibold
                    text-slate-600
                  "
                >
                  Status
                </th>

                {(canUpdate || canDelete) && (

                  <th
                    className="
                      px-6
                      py-4
                      text-right
                      text-sm
                      font-semibold
                      text-slate-600
                    "
                  >

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
                    className="
                      py-16
                      text-center
                      text-slate-500
                    "
                  >
                    Loading categories...
                  </td>

                </tr>

              ) : filteredCategories.length === 0 ? (

                <tr>

                  <td
                    colSpan={
                      canUpdate || canDelete
                        ? 8
                        : 7
                    }
                    className="
                      py-20
                      text-center
                    "
                  >

                    <div
                      className="
                        flex
                        flex-col
                        items-center
                      "
                    >

                      <FolderOpen
                        className="
                          w-14
                          h-14
                          text-slate-300
                          mb-4
                        "
                      />

                      <h3
                        className="
                          text-lg
                          font-semibold
                          text-slate-700
                        "
                      >
                        Belum ada kategori
                      </h3>

                      <p
                        className="
                          text-slate-500
                          mt-1
                        "
                      >
                        Data kategori akan muncul di sini.
                      </p>

                    </div>

                  </td>

                </tr>

              ) : (

                filteredCategories.map(
                  (category) => (

                    <tr
                      key={category.id}
                      className="
                        border-t
                        border-slate-100
                        hover:bg-slate-50/70
                        transition
                      "
                    >

                      {/* ID */}
                      <td
                        className="
                          px-6
                          py-5
                          font-medium
                          text-slate-700
                        "
                      >
                        #{category.id}
                      </td>

                      {/* Image */}
                      <td className="px-6 py-5">

                        {category.image_url ? (

                          <img
                            src={
                              category.image_url
                            }
                            alt={
                              category.name
                            }
                            className="
                              w-14
                              h-14
                              rounded-2xl
                              object-cover
                              border
                              border-slate-200
                            "
                          />

                        ) : (

                          <div
                            className="
                              w-14
                              h-14
                              rounded-2xl
                              bg-slate-100
                              flex
                              items-center
                              justify-center
                            "
                          >

                            <ImageIcon
                              className="
                                w-6
                                h-6
                                text-slate-400
                              "
                            />

                          </div>

                        )}

                      </td>

                      {/* Category */}
                      <td className="px-6 py-5">

                        <div>

                          <p
                            className="
                              font-semibold
                              text-slate-900
                            "
                          >
                            {category.name}
                          </p>

                          <p
                            className="
                              text-xs
                              text-slate-400
                              mt-1
                            "
                          >
                            {category.slug}
                          </p>

                          <div className="mt-2">

                            <span
                              className={`
                                inline-flex
                                items-center
                                px-2
                                py-1
                                rounded-full
                                text-xs
                                font-medium
                                ${
                                  category.parent_id === null
                                    ? "bg-blue-100 text-blue-700"
                                    : "bg-purple-100 text-purple-700"
                                }
                              `}
                            >
                              {category.parent_id === null
                                ? "Parent"
                                : "Child"}
                            </span>

                          </div>

                        </div>

                      </td>

                      {/* Parent */}
                      <td
                        className="
                          px-6
                          py-5
                          text-slate-600
                        "
                      >
                        {category.parent_name || "-"}
                      </td>

                      {/* Products */}
                      <td className="px-6 py-5">

                        <div
                          className="
                            inline-flex
                            items-center
                            gap-2
                            px-3
                            py-1
                            rounded-full
                            bg-emerald-100
                            text-emerald-700
                            text-sm
                            font-semibold
                          "
                        >

                          <Package className="w-4 h-4" />

                          {category.products_count ?? 0}

                        </div>

                      </td>

                      {/* Children */}
                      <td className="px-6 py-5">

                        <div
                          className="
                            inline-flex
                            items-center
                            gap-2
                            px-3
                            py-1
                            rounded-full
                            bg-amber-100
                            text-amber-700
                            text-sm
                            font-semibold
                          "
                        >
                          <FolderTree className="w-4 h-4" />

                          {category.children_count ?? 0}

                        </div>

                      </td>

                      {/* Status */}
                      <td className="px-6 py-5">

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
                              category.is_active
                                ? "bg-green-100 text-green-700"
                                : "bg-red-100 text-red-700"
                            }
                          `}
                        >
                          {category.is_active
                            ? "Active"
                            : "Inactive"}
                        </span>

                      </td>

                      {/* Actions */}
                      {(canUpdate || canDelete) && (

                        <td className="px-6 py-5">

                          <div
                            className="
                              flex
                              justify-end
                              gap-2
                            "
                          >

                            {canUpdate && (

                              <button
                                onClick={() =>
                                  handleEdit(
                                    category
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

                                <Edit3 className="w-4 h-4" />

                                Edit

                              </button>

                            )}

                            {canDelete && (

                              <button
                                disabled={
                                  !category.can_be_deleted
                                }
                                onClick={() =>
                                  handleDelete(
                                    category.slug
                                  )
                                }
                                className={`
                                  inline-flex
                                  items-center
                                  gap-2
                                  px-4
                                  py-2
                                  rounded-xl
                                  transition
                                  ${
                                    category.can_be_deleted
                                      ? "bg-red-50 text-red-700 hover:bg-red-100"
                                      : "bg-slate-100 text-slate-400 cursor-not-allowed"
                                  }
                                `}
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