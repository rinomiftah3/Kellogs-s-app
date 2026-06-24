import {
  useEffect,
  useMemo,
  useState,
} from "react";

import usePermission from "../../hooks/usePermission";

import {
  getProducts,
} from "../../services/productService";

import {
  getProductImages,
  createProductImage,
  updateProductImage,
  deleteProductImage,
  setPrimaryProductImage,
} from "../../services/productImageService";

import {
  successAlert,
  errorAlert,
  confirmDelete,
} from "../../utils/alert";

import {
  Image,
  Star,
  Search,
  Plus,
  Pencil,
  Trash2,
  CheckCircle,
  XCircle,
  ImagePlus,
} from "lucide-react";

export default function ProductImage() {

  const { can } = usePermission();

  /*
  |--------------------------------------------------------------------------
  | Permissions
  |--------------------------------------------------------------------------
  */

  const canView =
    can("products.view");

  const canCreate =
    can("products.create");

  const canUpdate =
    can("products.update");

  const canDelete =
    can("products.delete");

  /*
  |--------------------------------------------------------------------------
  | States
  |--------------------------------------------------------------------------
  */

  const [products, setProducts] =
    useState([]);

  const [images, setImages] =
    useState([]);

  const [loading, setLoading] =
    useState(false);

  const [submitting, setSubmitting] =
    useState(false);

  const [selectedProductId,
    setSelectedProductId] =
    useState("");

  const [editingId,
    setEditingId] =
    useState(null);

  /*
  |--------------------------------------------------------------------------
  | Filters
  |--------------------------------------------------------------------------
  */

  const [search,
    setSearch] =
    useState("");

  /*
  |--------------------------------------------------------------------------
  | Form States
  |--------------------------------------------------------------------------
  */

  const [altText,
    setAltText] =
    useState("");

  const [sortOrder,
    setSortOrder] =
    useState("");

  const [isPrimary,
    setIsPrimary] =
    useState(false);

  const [isActive,
    setIsActive] =
    useState(true);

  const [image,
    setImage] =
    useState(null);

  const [preview,
    setPreview] =
    useState("");

  /*
  |--------------------------------------------------------------------------
  | Load Products
  |--------------------------------------------------------------------------
  */

  const loadProducts =
    async () => {

      try {

        const data =
          await getProducts();

        setProducts(
          data || []
        );

      } catch (error) {

        console.error(error);

        setProducts([]);

        errorAlert(
          error?.response?.data?.message ||
          "Gagal mengambil produk"
        );

      }

    };

  /*
  |--------------------------------------------------------------------------
  | Load Product Images
  |--------------------------------------------------------------------------
  */

  const loadImages =
    async (
      productId =
        selectedProductId
    ) => {

      if (!productId) {

        setImages([]);

        return;

      }

      try {

        setLoading(true);

        const data =
          await getProductImages(
            productId
          );

        /*
        |--------------------------------------------------------------------------
        | Support Pagination Resource
        |--------------------------------------------------------------------------
        */

        if (
          Array.isArray(data)
        ) {

          setImages(data);

        } else {

          setImages(
            data?.data || []
          );

        }

      } catch (error) {

        console.error(error);

        setImages([]);

        errorAlert(
          error?.response?.data?.message ||
          "Gagal mengambil gambar produk"
        );

      } finally {

        setLoading(false);

      }

    };

  /*
  |--------------------------------------------------------------------------
  | Initial Load
  |--------------------------------------------------------------------------
  */

  useEffect(() => {

    if (!canView) {

      return;

    }

    loadProducts();

  }, []);

  /*
  |--------------------------------------------------------------------------
  | Reload Images
  |--------------------------------------------------------------------------
  */

  useEffect(() => {

    if (
      selectedProductId
    ) {

      loadImages(
        selectedProductId
      );

    } else {

      setImages([]);

    }

  }, [
    selectedProductId,
  ]);
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
  | Image Change
  |--------------------------------------------------------------------------
  */

  const handleImageChange = (
    e
  ) => {

    const file =
      e.target.files?.[0];

    if (!file) {

      return;

    }

    if (
      preview &&
      preview.startsWith("blob:")
    ) {

      URL.revokeObjectURL(
        preview
      );

    }

    setImage(file);

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

    setAltText("");

    setSortOrder("");

    setIsPrimary(false);

    setIsActive(true);

    setImage(null);

    setPreview("");

  };

  /*
  |--------------------------------------------------------------------------
  | Edit Image
  |--------------------------------------------------------------------------
  */

  const handleEdit = (
    item
  ) => {

    if (!canUpdate) {

      return;

    }

    setEditingId(
      item.id
    );

    setAltText(
      item.alt_text || ""
    );

    setSortOrder(
      item.sort_order ?? ""
    );

    setIsPrimary(
      item.is_primary ?? false
    );

    setIsActive(
      item.is_active ?? true
    );

    setImage(null);

    setPreview(
      item.full_image_url ||
      item.image_url ||
      ""
    );

    window.scrollTo({

      top: 0,

      behavior: "smooth",

    });

  };

  /*
  |--------------------------------------------------------------------------
  | Delete Image
  |--------------------------------------------------------------------------
  */

  const handleDelete =
    async (
      imageId
    ) => {

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

        await deleteProductImage(
          imageId
        );

        await successAlert(
          "Gambar produk berhasil dihapus"
        );

        await loadImages();

        if (
          editingId === imageId
        ) {

          resetForm();

        }

      } catch (error) {

        console.error(error);

        errorAlert(

          error?.response?.data?.message ||

          "Gagal menghapus gambar produk"

        );

      }

    };

  /*
  |--------------------------------------------------------------------------
  | Set Primary Image
  |--------------------------------------------------------------------------
  */

  const handleSetPrimary =
    async (
      imageId
    ) => {

      if (!canUpdate) {

        return;

      }

      try {

        await setPrimaryProductImage(
          imageId
        );

        await successAlert(
          "Gambar utama berhasil diperbarui"
        );

        await loadImages();

      } catch (error) {

        console.error(error);

        errorAlert(

          error?.response?.data?.message ||

          "Gagal mengubah gambar utama"

        );

      }

    };
      /*
  |--------------------------------------------------------------------------
  | Submit Image
  |--------------------------------------------------------------------------
  */

  const handleSubmit = async (
    e
  ) => {

    e.preventDefault();

    const canSubmit =
      editingId
        ? canUpdate
        : canCreate;

    if (!canSubmit) {

      return errorAlert(
        "Anda tidak memiliki izin untuk aksi ini"
      );

    }

    if (!selectedProductId) {

      return errorAlert(
        "Produk wajib dipilih"
      );

    }

    if (
      !editingId &&
      !image
    ) {

      return errorAlert(
        "Gambar produk wajib diunggah"
      );

    }

    try {

      setSubmitting(true);

      const formData =
        new FormData();

      /*
      |--------------------------------------------------------------------------
      | Product
      |--------------------------------------------------------------------------
      */

      if (!editingId) {

        formData.append(
          "product_id",
          selectedProductId
        );

      }

      /*
      |--------------------------------------------------------------------------
      | Image
      |--------------------------------------------------------------------------
      */

      if (image) {

        formData.append(
          "image",
          image
        );

      }

      /*
      |--------------------------------------------------------------------------
      | Optional Fields
      |--------------------------------------------------------------------------
      */

      if (
        altText.trim()
      ) {

        formData.append(
          "alt_text",
          altText.trim()
        );

      }

      if (
        sortOrder !== ""
      ) {

        formData.append(
          "sort_order",
          sortOrder
        );

      }

      formData.append(
        "is_primary",
        isPrimary
      );

      formData.append(
        "is_active",
        isActive
      );

      /*
      |--------------------------------------------------------------------------
      | Create / Update
      |--------------------------------------------------------------------------
      */

      if (editingId) {

        await updateProductImage(
          editingId,
          formData
        );

        await successAlert(
          "Gambar produk berhasil diperbarui"
        );

      } else {

        await createProductImage(
          selectedProductId,
          formData
        );

        await successAlert(
          "Gambar produk berhasil ditambahkan"
        );

      }

      resetForm();

      await loadImages();

    } catch (error) {

      console.error(error);

      const errors =
        error?.response?.data
          ?.errors;

      if (errors) {

        const firstError =
          Object.values(errors)?.[0]?.[0];

        return errorAlert(
          firstError
        );

      }

      errorAlert(

        error?.response?.data
          ?.message ||

        "Gagal menyimpan gambar produk"

      );

    } finally {

      setSubmitting(false);

    }

  };

  /*
  |--------------------------------------------------------------------------
  | Search Filter
  |--------------------------------------------------------------------------
  */

  const filteredImages =
    useMemo(() => {

      return images.filter(
        (item) => {

          return (

            item.alt_text
              ?.toLowerCase()
              .includes(
                search.toLowerCase()
              )

            ||

            item.image_type
              ?.toLowerCase()
              .includes(
                search.toLowerCase()
              )

            ||

            item.status_label
              ?.toLowerCase()
              .includes(
                search.toLowerCase()
              )

          );

        }
      );

    }, [
      images,
      search,
    ]);

  /*
  |--------------------------------------------------------------------------
  | Statistics
  |--------------------------------------------------------------------------
  */

  const totalImages =
    images.length;

  const primaryImages =
    images.filter(
      (item) =>
        item.is_primary
    ).length;

  const activeImages =
    images.filter(
      (item) =>
        item.is_active
    ).length;

  const inactiveImages =
    images.filter(
      (item) =>
        !item.is_active
    ).length;

  /*
  |--------------------------------------------------------------------------
  | Hero Title
  |--------------------------------------------------------------------------
  */

  const selectedProduct =
    products.find(
      (product) =>

        String(
          product.id
        ) ===

        String(
          selectedProductId
        )
    );

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

        <div className="absolute -top-16 -right-16 w-64 h-64 rounded-full bg-white/10" />

        <div className="absolute -bottom-20 -left-20 w-72 h-72 rounded-full bg-white/10" />

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

                <Image className="w-8 h-8" />

              </div>

              <div>

                <h1 className="text-4xl font-bold">
                  Product Images
                </h1>

                <p className="text-red-100 mt-2">

                  {selectedProduct
                    ? `Kelola gallery untuk ${selectedProduct.name}`
                    : "Pilih produk untuk mengelola gallery."}

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
              Total: {totalImages}
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
              Primary: {primaryImages}
            </div>

          </div>

        </div>

      </div>
            {/* Statistics */}
      <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">

        {/* Total */}
        <div className="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">

          <div className="flex justify-between items-center">

            <div>

              <p className="text-slate-500 text-sm">
                Total Images
              </p>

              <h3 className="text-4xl font-bold mt-2">
                {totalImages}
              </h3>

            </div>

            <div className="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center">

              <Image className="w-7 h-7 text-blue-600" />

            </div>

          </div>

        </div>

        {/* Primary */}
        <div className="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">

          <div className="flex justify-between items-center">

            <div>

              <p className="text-slate-500 text-sm">
                Primary Images
              </p>

              <h3 className="text-4xl font-bold mt-2">
                {primaryImages}
              </h3>

            </div>

            <div className="w-14 h-14 rounded-2xl bg-yellow-50 flex items-center justify-center">

              <Star className="w-7 h-7 text-yellow-600" />

            </div>

          </div>

        </div>

        {/* Active */}
        <div className="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">

          <div className="flex justify-between items-center">

            <div>

              <p className="text-slate-500 text-sm">
                Active
              </p>

              <h3 className="text-4xl font-bold mt-2">
                {activeImages}
              </h3>

            </div>

            <div className="w-14 h-14 rounded-2xl bg-green-50 flex items-center justify-center">

              <CheckCircle className="w-7 h-7 text-green-600" />

            </div>

          </div>

        </div>

        {/* Inactive */}
        <div className="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">

          <div className="flex justify-between items-center">

            <div>

              <p className="text-slate-500 text-sm">
                Inactive
              </p>

              <h3 className="text-4xl font-bold mt-2">
                {inactiveImages}
              </h3>

            </div>

            <div className="w-14 h-14 rounded-2xl bg-red-50 flex items-center justify-center">

              <XCircle className="w-7 h-7 text-red-600" />

            </div>

          </div>

        </div>

      </div>

      {/* Product Selector & Search */}
      <div className="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">

        <div className="flex flex-col lg:flex-row gap-4">

          {/* Product Selector */}
          <select
            value={selectedProductId}
            onChange={(e) =>
              setSelectedProductId(
                e.target.value
              )
            }
            className="
              lg:w-80
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
              Pilih Produk
            </option>

            {products.map(
              (product) => (

                <option
                  key={product.id}
                  value={product.id}
                >
                  {product.name}
                </option>

              )
            )}

          </select>

          {/* Search */}
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
              placeholder="Cari gambar..."
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

      </div>

      {/* Upload Form */}
      {(canCreate || canUpdate) && (

        <div className="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">

          <div className="flex items-center gap-3 mb-8">

            <div className="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center">

              <Plus className="w-6 h-6 text-red-600" />

            </div>

            <div>

              <h2 className="text-2xl font-bold">

                {editingId
                  ? "Edit Product Image"
                  : "Upload Product Image"}

              </h2>

              <p className="text-slate-500">

                Tambahkan atau perbarui gallery produk.

              </p>

            </div>

          </div>

          <form
            onSubmit={handleSubmit}
            className="grid xl:grid-cols-3 gap-8"
          >

            {/* Left */}
            <div className="xl:col-span-2 space-y-5">

              <input
                type="text"
                placeholder="Alt Text"
                value={altText}
                disabled={
                  submitting
                }
                onChange={(e) =>
                  setAltText(
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

              <input
                type="number"
                placeholder="Sort Order"
                value={sortOrder}
                disabled={
                  submitting
                }
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

              <div className="flex flex-col sm:flex-row gap-6">

                <label className="flex items-center gap-3">

                  <input
                    type="checkbox"
                    checked={isPrimary}
                    disabled={
                      submitting
                    }
                    onChange={(e) =>
                      setIsPrimary(
                        e.target.checked
                      )
                    }
                  />

                  <span className="text-sm text-slate-700">
                    Set sebagai gambar utama
                  </span>

                </label>

                <label className="flex items-center gap-3">

                  <input
                    type="checkbox"
                    checked={isActive}
                    disabled={
                      submitting
                    }
                    onChange={(e) =>
                      setIsActive(
                        e.target.checked
                      )
                    }
                  />

                  <span className="text-sm text-slate-700">
                    Status aktif
                  </span>

                </label>

              </div>

            </div>

            {/* Upload */}
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

                <ImagePlus className="w-12 h-12 mx-auto text-slate-300 mb-4" />

                <p className="font-medium text-slate-700">
                  Upload Image
                </p>

                <p className="text-sm text-slate-500 mt-1">
                  JPG, PNG, WEBP (Max 2 MB)
                </p>

                <input
                  type="file"
                  accept="image/*"
                  disabled={
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

                <button
                  type="submit"
                  disabled={
                    submitting ||
                    !selectedProductId
                  }
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
                    ? "Update Image"
                    : "Upload Image"}

                </button>

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

      {/* Images Table */}
      <div className="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">

        <div className="px-6 py-5 border-b border-slate-100">

          <h2 className="text-xl font-bold text-slate-900">
            Product Images
          </h2>

          <p className="text-sm text-slate-500 mt-1">

            Menampilkan {filteredImages.length} gambar.

          </p>

        </div>

        <div className="overflow-x-auto">

          <table className="w-full">

            <thead className="bg-slate-50">

              <tr>

                <th className="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                  Image
                </th>

                <th className="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                  Alt Text
                </th>

                <th className="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                  Type
                </th>

                <th className="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                  Sort
                </th>

                <th className="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                  Status
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
                        ? 6
                        : 5
                    }
                    className="py-16 text-center text-slate-500"
                  >

                    Loading images...

                  </td>

                </tr>

              ) : filteredImages.length === 0 ? (

                <tr>

                  <td
                    colSpan={
                      canUpdate || canDelete
                        ? 6
                        : 5
                    }
                    className="py-20 text-center"
                  >

                    <div className="flex flex-col items-center">

                      <Image className="w-16 h-16 text-slate-300 mb-4" />

                      <h3 className="text-lg font-semibold text-slate-700">

                        Belum ada gambar

                      </h3>

                      <p className="text-slate-500 mt-2">

                        Upload gallery produk pertama Anda.

                      </p>

                    </div>

                  </td>

                </tr>

              ) : (

                filteredImages.map(
                  (item) => (

                    <tr
                      key={item.id}
                      className="
                        border-t
                        border-slate-100
                        hover:bg-slate-50
                        transition
                      "
                    >

                      {/* Image */}
                      <td className="px-6 py-5">

                        <div className="flex items-center gap-4">

                          {item.full_image_url ? (

                            <img
                              src={item.full_image_url}
                              alt={item.alt_text}
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

                              <Image className="w-7 h-7 text-slate-400" />

                            </div>

                          )}

                        </div>

                      </td>

                      {/* Alt Text */}
                      <td className="px-6 py-5 text-slate-700">

                        {item.alt_text || "-"}

                      </td>

                      {/* Type */}
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
                              item.is_primary
                                ? "bg-yellow-100 text-yellow-700"
                                : "bg-blue-100 text-blue-700"
                            }
                          `}
                        >

                          {item.image_type}

                        </span>

                      </td>

                      {/* Sort */}
                      <td className="px-6 py-5 text-slate-700">

                        {item.sort_order}

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
                              item.is_active
                                ? "bg-green-100 text-green-700"
                                : "bg-red-100 text-red-700"
                            }
                          `}
                        >

                          {item.status_label}

                        </span>

                      </td>

                      {/* Actions */}
                      {(canUpdate || canDelete) && (

                        <td className="px-6 py-5">

                          <div className="flex justify-end gap-2 flex-wrap">

                            {canUpdate &&
                              !item.is_primary && (

                              <button
                                onClick={() =>
                                  handleSetPrimary(
                                    item.id
                                  )
                                }
                                className="
                                  inline-flex
                                  items-center
                                  gap-2
                                  px-4
                                  py-2
                                  rounded-xl
                                  bg-yellow-50
                                  text-yellow-700
                                  hover:bg-yellow-100
                                  transition
                                "
                              >

                                <Star className="w-4 h-4" />

                                Primary

                              </button>

                            )}

                            {canUpdate && (

                              <button
                                onClick={() =>
                                  handleEdit(
                                    item
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
                                    item.id
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