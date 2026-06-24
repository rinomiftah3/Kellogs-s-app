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
  getProductOptions,
  createProductOption,
  updateProductOption,
  deleteProductOption,
  activateProductOption,
  deactivateProductOption,
  markProductOptionRequired,
  markProductOptionOptional,
} from "../../services/productOptionService";

import {
  successAlert,
  errorAlert,
  confirmDelete,
} from "../../utils/alert";

import {
  SlidersHorizontal,
  CheckCircle,
  Search,
  Plus,
  Pencil,
  Trash2,
  ShieldCheck,
  Circle,
  Save,
} from "lucide-react";

export default function ProductOption() {

  /*
  |--------------------------------------------------------------------------
  | Permissions
  |--------------------------------------------------------------------------
  */

  const { can } =
    usePermission();

  const [editingId, setEditingId] =
    useState(null);

  const canCreate =
    can("products.create");

  const canUpdate =
    can("products.update");

  const canDelete =
    can("products.delete");

  const canSubmit =
    editingId !== null
      ? canUpdate
      : canCreate;

  /*
  |--------------------------------------------------------------------------
  | Data States
  |--------------------------------------------------------------------------
  */

  const [options, setOptions] =
    useState([]);

  const [products, setProducts] =
    useState([]);

  const [loading, setLoading] =
    useState(false);

  const [submitting, setSubmitting] =
    useState(false);

  /*
  |--------------------------------------------------------------------------
  | Form States
  |--------------------------------------------------------------------------
  */

  const [productId, setProductId] =
    useState("");

  const [name, setName] =
    useState("");

  const [code, setCode] =
    useState("");

  const [sortOrder, setSortOrder] =
    useState("");

  const [isRequired, setIsRequired] =
    useState(true);

  const [isActive, setIsActive] =
    useState(true);

  /*
  |--------------------------------------------------------------------------
  | Filter States
  |--------------------------------------------------------------------------
  */

  const [search, setSearch] =
    useState("");

  const [
    filterProduct,
    setFilterProduct,
  ] = useState("");

  const [
    filterStatus,
    setFilterStatus,
  ] = useState("");

  const [
    filterRequirement,
    setFilterRequirement,
  ] = useState("");

  /*
  |--------------------------------------------------------------------------
  | Load Product Options
  |--------------------------------------------------------------------------
  */

  const loadOptions =
    async () => {

      try {

        setLoading(true);

        const params = {};

        if (filterProduct) {

          params.product_id =
            filterProduct;

        }

        if (search.trim()) {

          params.search =
            search.trim();

        }

        if (
          filterStatus !== ""
        ) {

          params.is_active =
            filterStatus;

        }

        if (
          filterRequirement !== ""
        ) {

          params.is_required =
            filterRequirement;

        }

        const data =
          await getProductOptions(
            params
          );

        setOptions(
          data || []
        );

      } catch (error) {

        console.error(error);

        setOptions([]);

        errorAlert(

          error?.response
            ?.data
            ?.message ||

          "Gagal mengambil opsi produk"

        );

      } finally {

        setLoading(false);

      }

    };
      /*
  |--------------------------------------------------------------------------
  | Load Products
  |--------------------------------------------------------------------------
  */

  const loadProducts =
    async () => {

      try {

        const data =
          await getProducts({
            per_page: 100,
          });

        /*
        |--------------------------------------------------------------------------
        | Support paginated/non-paginated response
        |--------------------------------------------------------------------------
        */

        if (
          Array.isArray(data)
        ) {

          setProducts(data);

        } else if (
          Array.isArray(
            data?.data
          )
        ) {

          setProducts(
            data.data
          );

        } else {

          setProducts([]);

        }

      } catch (error) {

        console.error(
          error
        );

        setProducts([]);

        errorAlert(

          error?.response
            ?.data
            ?.message ||

          "Gagal mengambil produk"

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

  }, []);

  useEffect(() => {

    loadOptions();

  }, [

    search,

    filterProduct,

    filterStatus,

    filterRequirement,
  ]);

  /*
  |--------------------------------------------------------------------------
  | Statistics
  |--------------------------------------------------------------------------
  */

  const totalOptions =
    options.length;

  const activeOptions =
    options.filter(

      (option) =>

        option.is_active

    ).length;

  const requiredOptions =
    options.filter(

      (option) =>

        option.is_required

    ).length;

  const optionalOptions =
    options.filter(

      (option) =>

        !option.is_required

    ).length;

  /*
  |--------------------------------------------------------------------------
  | Reset Form
  |--------------------------------------------------------------------------
  */

  const resetForm = () => {

    setEditingId(null);

    setProductId("");

    setName("");

    setCode("");

    setSortOrder("");

    setIsRequired(true);

    setIsActive(true);

  };

  /*
  |--------------------------------------------------------------------------
  | Edit Option
  |--------------------------------------------------------------------------
  */

  const handleEdit =
    (option) => {

      if (!canUpdate) {
        return;
      }

      setEditingId(
        option.id
      );

      setProductId(
        String(
          option.product_id || ""
        )
      );

      setName(
        option.name || ""
      );

      setCode(
        option.code || ""
      );

      setSortOrder(

        option.sort_order ??
        ""

      );

      setIsRequired(
        option.is_required
      );

      setIsActive(
        option.is_active
      );

      window.scrollTo({

        top: 0,

        behavior: "smooth",

      });

    };

  /*
  |--------------------------------------------------------------------------
  | Local Data
  |--------------------------------------------------------------------------
  |
  | Backend sudah melakukan filtering.
  | Tidak perlu filtering ulang di frontend.
  |
  */

  const filteredOptions =
    useMemo(() => {

      return options;

    }, [

      options,

    ]);
      /*
  |--------------------------------------------------------------------------
  | Submit
  |--------------------------------------------------------------------------
  */

  const handleSubmit =
    async (e) => {

      e.preventDefault();

      if (

        editingId
          ? !canUpdate
          : !canCreate

      ) {

        return errorAlert(

          "Anda tidak memiliki izin untuk aksi ini"

        );

      }

      /*
      |--------------------------------------------------------------------------
      | Validation
      |--------------------------------------------------------------------------
      */

      if (!productId) {

        return errorAlert(
          "Produk wajib dipilih"
        );

      }

      if (!name.trim()) {

        return errorAlert(
          "Nama opsi wajib diisi"
        );

      }

      /*
      |--------------------------------------------------------------------------
      | Required option must remain active
      |--------------------------------------------------------------------------
      */

      if (
        isRequired &&
        !isActive
      ) {

        return errorAlert(
          "Opsi wajib harus berstatus aktif."
        );

      }

      try {

        setSubmitting(true);

        const payload = {

          name:
            name.trim(),

          code:
            code.trim()
              ? code
                  .trim()
                  .toUpperCase()
              : null,

          is_required:
            isRequired,

          is_active:
            isActive,
        };

        /*
        |--------------------------------------------------------------------------
        | Product ID
        |--------------------------------------------------------------------------
        |
        | Backend tidak mengizinkan
        | perubahan product_id saat update.
        |
        */

        if (!editingId) {

          payload.product_id =
            Number(
              productId
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Sort Order
        |--------------------------------------------------------------------------
        */

        if (

          sortOrder !== ""

        ) {

          payload.sort_order =
            Number(
              sortOrder
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */

        if (editingId) {

          await updateProductOption(

            editingId,

            payload

          );

          await successAlert(

            "Opsi produk berhasil diperbarui"

          );

        }

        /*
        |--------------------------------------------------------------------------
        | Create
        |--------------------------------------------------------------------------
        */

        else {

          await createProductOption(
            payload
          );

          await successAlert(

            "Opsi produk berhasil dibuat"

          );

        }

        resetForm();

        await loadOptions();

      } catch (error) {

        console.error(
          error
        );

        const errors =

          error?.response
            ?.data
            ?.errors;

        if (errors) {

          const firstError =

            Object
              .values(errors)?.[0]?.[0];

          return errorAlert(
            firstError
          );

        }

        errorAlert(

          error?.response
            ?.data
            ?.message ||

          "Gagal menyimpan opsi produk"

        );

      } finally {

        setSubmitting(false);

      }

    };
      /*
  |--------------------------------------------------------------------------
  | Delete
  |--------------------------------------------------------------------------
  */

  const handleDelete =
    async (option) => {

      if (!canDelete) {
        return;
      }

      /*
      |--------------------------------------------------------------------------
      | Backend Protection
      |--------------------------------------------------------------------------
      |
      | Option yang masih memiliki value
      | tidak boleh dihapus.
      |
      */

      if (
        option.can_be_deleted === false
      ) {

        return errorAlert(

          "Opsi masih memiliki value dan tidak dapat dihapus."

        );

      }

      const result =
        await confirmDelete();

      if (
        !result.isConfirmed
      ) {

        return;

      }

      try {

        await deleteProductOption(
          option.id
        );

        await successAlert(

          "Opsi produk berhasil dihapus"

        );

        /*
        |--------------------------------------------------------------------------
        | Jika sedang edit option yang dihapus
        |--------------------------------------------------------------------------
        */

        if (
          editingId === option.id
        ) {

          resetForm();

        }

        await loadOptions();

      } catch (error) {

        console.error(error);

        errorAlert(

          error?.response
            ?.data
            ?.message ||

          "Gagal menghapus opsi produk"

        );

      }

    };

  /*
  |--------------------------------------------------------------------------
  | Toggle Status
  |--------------------------------------------------------------------------
  */

  const handleToggleStatus =
    async (option) => {

      if (!canUpdate) {
        return;
      }

      try {

        /*
        |--------------------------------------------------------------------------
        | Required option tidak boleh inactive
        |--------------------------------------------------------------------------
        */

        if (

          option.is_required &&

          option.is_active

        ) {

          return errorAlert(

            "Opsi wajib tidak dapat dinonaktifkan."

          );

        }

        if (option.is_active) {

          await deactivateProductOption(
            option.id
          );

        } else {

          await activateProductOption(
            option.id
          );

        }

        await successAlert(

          option.is_active

            ? "Opsi berhasil dinonaktifkan"

            : "Opsi berhasil diaktifkan"

        );

        await loadOptions();

      } catch (error) {

        console.error(error);

        errorAlert(

          error?.response
            ?.data
            ?.message ||

          "Gagal mengubah status opsi"

        );

      }

    };

  /*
  |--------------------------------------------------------------------------
  | Toggle Requirement
  |--------------------------------------------------------------------------
  */

  const handleToggleRequirement =
    async (option) => {

      if (!canUpdate) {
        return;
      }

      try {

        if (option.is_required) {

          await markProductOptionOptional(
            option.id
          );

        } else {

          await markProductOptionRequired(
            option.id
          );

        }

        await successAlert(

          option.is_required

            ? "Opsi berhasil dijadikan opsional"

            : "Opsi berhasil dijadikan wajib"

        );

        /*
        |--------------------------------------------------------------------------
        | Sinkronisasi form edit
        |--------------------------------------------------------------------------
        */

        if (
          editingId === option.id
        ) {

          setIsRequired(
            !option.is_required
          );

          /*
          |--------------------------------------------------------------------------
          | Required harus aktif
          |--------------------------------------------------------------------------
          */

          if (
            !option.is_required
          ) {

            setIsActive(true);

          }

        }

        await loadOptions();

      } catch (error) {

        console.error(error);

        errorAlert(

          error?.response
            ?.data
            ?.message ||

          "Gagal mengubah requirement opsi"

        );

      }

    };
      return (

    <div className="space-y-6">

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

        <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">

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
                <SlidersHorizontal className="w-8 h-8" />
              </div>

              <div>

                <h1 className="text-4xl font-bold">
                  Product Options
                </h1>

                <p className="text-red-100 mt-2">
                  Kelola opsi produk Kellogg's seperti ukuran, rasa, atau varian lainnya.
                </p>

              </div>

            </div>

          </div>

          <div className="flex flex-wrap gap-3">

            <div className="bg-white/10 backdrop-blur rounded-2xl px-4 py-2 text-sm">
              Total: {totalOptions}
            </div>

            <div className="bg-white/10 backdrop-blur rounded-2xl px-4 py-2 text-sm">
              Active: {activeOptions}
            </div>

          </div>

        </div>

      </div>

      <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">

        <div className="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">

          <div className="flex justify-between items-center">

            <div>

              <p className="text-slate-500 text-sm">
                Total Options
              </p>

              <h3 className="text-4xl font-bold mt-2">
                {totalOptions}
              </h3>

            </div>

            <div className="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center">

              <SlidersHorizontal className="w-7 h-7 text-blue-600" />

            </div>

          </div>

        </div>

        <div className="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">

          <div className="flex justify-between items-center">

            <div>

              <p className="text-slate-500 text-sm">
                Active
              </p>

              <h3 className="text-4xl font-bold mt-2">
                {activeOptions}
              </h3>

            </div>

            <div className="w-14 h-14 rounded-2xl bg-green-50 flex items-center justify-center">

              <CheckCircle className="w-7 h-7 text-green-600" />

            </div>

          </div>

        </div>

        <div className="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">

          <div className="flex justify-between items-center">

            <div>

              <p className="text-slate-500 text-sm">
                Required
              </p>

              <h3 className="text-4xl font-bold mt-2">
                {requiredOptions}
              </h3>

            </div>

            <div className="w-14 h-14 rounded-2xl bg-yellow-50 flex items-center justify-center">

              <ShieldCheck className="w-7 h-7 text-yellow-600" />

            </div>

          </div>

        </div>

        <div className="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">

          <div className="flex justify-between items-center">

            <div>

              <p className="text-slate-500 text-sm">
                Optional
              </p>

              <h3 className="text-4xl font-bold mt-2">
                {optionalOptions}
              </h3>

            </div>

            <div className="w-14 h-14 rounded-2xl bg-purple-50 flex items-center justify-center">

              <Circle className="w-7 h-7 text-purple-600" />

            </div>

          </div>

        </div>

      </div>

      <div className="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">

        <div className="flex flex-col xl:flex-row gap-4">

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
              placeholder="Cari opsi produk..."
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
            value={filterProduct}
            onChange={(e) =>
              setFilterProduct(
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
              Semua Produk
            </option>

            {products.map((product) => (

              <option
                key={product.id}
                value={product.id}
              >
                {product.name}
              </option>

            ))}

          </select>

          <select
            value={filterStatus}
            onChange={(e) =>
              setFilterStatus(
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
              Semua Status
            </option>

            <option value="true">
              Active
            </option>

            <option value="false">
              Inactive
            </option>

          </select>

          <select
            value={filterRequirement}
            onChange={(e) =>
              setFilterRequirement(
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
              Semua Requirement
            </option>

            <option value="true">
              Required
            </option>

            <option value="false">
              Optional
            </option>

          </select>

        </div>

      </div>
            {(canCreate || canUpdate) && (

        <div className="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">

          <div className="flex items-center gap-3 mb-8">

            <div className="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center">

              <Plus className="w-6 h-6 text-red-600" />

            </div>

            <div>

              <h2 className="text-2xl font-bold">

                {editingId
                  ? "Edit Product Option"
                  : "Create Product Option"}

              </h2>

              <p className="text-slate-500">

                Tambahkan atau ubah opsi produk.

              </p>

            </div>

          </div>

          <form
            onSubmit={handleSubmit}
            className="space-y-6"
          >

            <div className="grid md:grid-cols-2 gap-5">

              <div>

                <label className="block text-sm font-medium text-slate-700 mb-2">

                  Product

                </label>

                <select
                  value={productId}
                  disabled={
                    editingId !== null ||
                    !canSubmit ||
                    submitting
                  }
                  onChange={(e) =>
                    setProductId(
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
                    disabled:bg-slate-100
                    disabled:cursor-not-allowed
                  "
                >

                  <option value="">
                    Pilih Produk
                  </option>

                  {products.map((product) => (

                    <option
                      key={product.id}
                      value={product.id}
                    >
                      {product.name}
                    </option>

                  ))}

                </select>

              </div>

              <div>

                <label className="block text-sm font-medium text-slate-700 mb-2">

                  Option Name

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
                  placeholder="Contoh: Ukuran"
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

              <div>

                <label className="block text-sm font-medium text-slate-700 mb-2">

                  Code

                </label>

                <input
                  type="text"
                  value={code}
                  disabled={
                    !canSubmit ||
                    submitting
                  }
                  onChange={(e) =>
                    setCode(
                      e.target.value.toUpperCase()
                    )
                  }
                  placeholder="SIZE"
                  className="
                    w-full
                    rounded-2xl
                    border
                    border-slate-200
                    px-4
                    py-3
                    uppercase
                    focus:outline-none
                    focus:ring-2
                    focus:ring-red-500
                  "
                />

              </div>

              <div>

                <label className="block text-sm font-medium text-slate-700 mb-2">

                  Sort Order

                </label>

                <input
                  type="number"
                  min="0"
                  value={sortOrder}
                  disabled={
                    !canSubmit ||
                    submitting
                  }
                  onChange={(e) =>
                    setSortOrder(
                      e.target.value
                    )
                  }
                  placeholder="0"
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

            </div>

            <div className="grid md:grid-cols-2 gap-5">

              <div
                className="
                  flex
                  items-center
                  justify-between
                  rounded-2xl
                  border
                  border-slate-200
                  px-5
                  py-4
                "
              >

                <div>

                  <h4 className="font-semibold text-slate-800">

                    Required Option

                  </h4>

                  <p className="text-sm text-slate-500">

                    Pelanggan wajib memilih opsi ini.

                  </p>

                </div>

                <input
                  type="checkbox"
                  checked={isRequired}
                  disabled={
                    !canSubmit ||
                    submitting
                  }
                  onChange={(e) => {

                    const checked =
                      e.target.checked;

                    setIsRequired(
                      checked
                    );

                    if (checked) {

                      setIsActive(
                        true
                      );

                    }

                  }}
                  className="
                    w-5
                    h-5
                    accent-red-600
                  "
                />

              </div>

              <div
                className="
                  flex
                  items-center
                  justify-between
                  rounded-2xl
                  border
                  border-slate-200
                  px-5
                  py-4
                "
              >

                <div>

                  <h4 className="font-semibold text-slate-800">

                    Active Status

                  </h4>

                  <p className="text-sm text-slate-500">

                    Opsi akan tersedia untuk digunakan.

                  </p>

                </div>

                <input
                  type="checkbox"
                  checked={isActive}
                  disabled={
                    !canSubmit ||
                    submitting ||
                    isRequired
                  }
                  onChange={(e) =>
                    setIsActive(
                      e.target.checked
                    )
                  }
                  className="
                    w-5
                    h-5
                    accent-green-600
                  "
                />

              </div>

            </div>

            <div className="flex gap-3">

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

                  <Save className="w-5 h-5" />

                  {submitting
                    ? "Saving..."
                    : editingId
                    ? "Update Option"
                    : "Create Option"}

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

          </form>

        </div>

      )}
            <div className="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">

        <div className="px-6 py-5 border-b border-slate-100">

          <h2 className="text-xl font-bold text-slate-900">
            Product Options List
          </h2>

          <p className="text-sm text-slate-500 mt-1">
            Menampilkan {filteredOptions.length} opsi produk.
          </p>

        </div>

        <div className="overflow-x-auto">

          <table className="w-full">

            <thead className="bg-slate-50">

              <tr>

                <th className="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                  Option
                </th>

                <th className="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                  Product
                </th>

                <th className="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                  Values
                </th>

                <th className="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                  Requirement
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
                    Loading product options...
                  </td>

                </tr>

              ) : filteredOptions.length === 0 ? (

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

                      <SlidersHorizontal className="w-16 h-16 text-slate-300 mb-4" />

                      <h3 className="text-lg font-semibold text-slate-700">
                        Belum ada opsi produk
                      </h3>

                      <p className="text-slate-500 mt-2">
                        Tambahkan opsi produk pertama Anda.
                      </p>

                    </div>

                  </td>

                </tr>

              ) : (

                filteredOptions.map((option) => (

                  <tr
                    key={option.id}
                    className="
                      border-t
                      border-slate-100
                      hover:bg-slate-50
                      transition
                    "
                  >

                    <td className="px-6 py-5">

                      <div>

                        <p className="font-semibold text-slate-900">
                          {option.name}
                        </p>

                        <p className="text-xs text-slate-400 mt-1">
                          {option.code || "-"}
                        </p>

                      </div>

                    </td>

                    <td className="px-6 py-5 text-slate-700">

                      {option.product?.name || "-"}

                    </td>

                    <td className="px-6 py-5">

                      <span
                        className="
                          inline-flex
                          items-center
                          px-3
                          py-1
                          rounded-full
                          text-xs
                          font-semibold
                          bg-blue-100
                          text-blue-700
                        "
                      >

                        {option.values_count || 0} Values

                      </span>

                    </td>

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
                            option.is_required
                              ? "bg-yellow-100 text-yellow-700"
                              : "bg-slate-100 text-slate-700"
                          }
                        `}
                      >

                        {option.requirement_label}

                      </span>

                    </td>

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
                            option.is_active
                              ? "bg-green-100 text-green-700"
                              : "bg-red-100 text-red-700"
                          }
                        `}
                      >

                        {option.status_label}

                      </span>

                    </td>

                    {(canUpdate || canDelete) && (

                      <td className="px-6 py-5">

                        <div className="flex justify-end flex-wrap gap-2">

                          {canUpdate && (

                            <button
                              onClick={() =>
                                handleEdit(option)
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

                          {canUpdate && (

                            <button
                              onClick={() =>
                                handleToggleStatus(option)
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
                                  option.is_active
                                    ? "bg-orange-50 text-orange-700 hover:bg-orange-100"
                                    : "bg-green-50 text-green-700 hover:bg-green-100"
                                }
                              `}
                            >

                              {option.is_active
                                ? "Deactivate"
                                : "Activate"}

                            </button>

                          )}

                          {canUpdate && (

                            <button
                              onClick={() =>
                                handleToggleRequirement(option)
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
                                  option.is_required
                                    ? "bg-slate-100 text-slate-700 hover:bg-slate-200"
                                    : "bg-yellow-50 text-yellow-700 hover:bg-yellow-100"
                                }
                              `}
                            >

                              {option.is_required
                                ? "Optional"
                                : "Required"}

                            </button>

                          )}

                          {canDelete &&
                            option.can_be_deleted && (

                              <button
                                onClick={() =>
                                  handleDelete(option)
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

                ))

              )}

            </tbody>

          </table>

        </div>

      </div>

    </div>

  );

}