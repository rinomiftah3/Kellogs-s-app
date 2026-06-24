import {
  useEffect,
  useState,
} from "react";

import usePermission from "../../hooks/usePermission";

import {
  getProductOptions,
} from "../../services/productOptionService";

import {
  getProductOptionValues,
  createProductOptionValue,
  updateProductOptionValue,
  deleteProductOptionValue,
  activateProductOptionValue,
  deactivateProductOptionValue,
} from "../../services/productOptionValueService";

import {
  successAlert,
  errorAlert,
  confirmDelete,
} from "../../utils/alert";

import {
  SlidersHorizontal,
  CheckCircle,
  XCircle,
  Search,
  Plus,
  Pencil,
  Trash2,
  Save,
  Tag,
  Boxes,
  Circle,
} from "lucide-react";

export default function ProductOptionValue() {

  /*
  |--------------------------------------------------------------------------
  | Permissions
  |--------------------------------------------------------------------------
  */

  const { can } = usePermission();

  const canCreate =
    can("products.create");

  const canUpdate =
    can("products.update");

  const canDelete =
    can("products.delete");

  /*
  |--------------------------------------------------------------------------
  | Data States
  |--------------------------------------------------------------------------
  */

  const [values, setValues] =
    useState([]);

  const [options, setOptions] =
    useState([]);

  const [loading, setLoading] =
    useState(false);

  const [submitting, setSubmitting] =
    useState(false);

  const [editingId, setEditingId] =
    useState(null);

  /*
  |--------------------------------------------------------------------------
  | Form States
  |--------------------------------------------------------------------------
  */

  const [
    productOptionId,
    setProductOptionId,
  ] = useState("");

  const [value, setValue] =
    useState("");

  const [code, setCode] =
    useState("");

  const [
    sortOrder,
    setSortOrder,
  ] = useState("");

  const [
    isActive,
    setIsActive,
  ] = useState(true);

  /*
  |--------------------------------------------------------------------------
  | Filter States
  |--------------------------------------------------------------------------
  */

  const [search, setSearch] =
    useState("");

  const [
    filterOption,
    setFilterOption,
  ] = useState("");

  const [
    filterStatus,
    setFilterStatus,
  ] = useState("");

  /*
  |--------------------------------------------------------------------------
  | Submit Permission
  |--------------------------------------------------------------------------
  */

  const canSubmit =
    editingId !== null
      ? canUpdate
      : canCreate;
        /*
  |--------------------------------------------------------------------------
  | Load Product Option Values
  |--------------------------------------------------------------------------
  */

  const loadValues = async () => {

    try {

      setLoading(true);

      const params = {};

      if (filterOption) {

        params.product_option_id =
          Number(filterOption);

      }

      if (search.trim()) {

        params.search =
          search.trim();

      }

      if (filterStatus !== "") {

        params.is_active =
          filterStatus === "true";

      }

      params.per_page = 100;

      const data =
        await getProductOptionValues(
          params
        );

      setValues(
        data || []
      );

    } catch (error) {

      console.error(error);

      setValues([]);

      errorAlert(

        error?.response
          ?.data
          ?.message ||

        "Gagal mengambil option value"

      );

    } finally {

      setLoading(false);

    }

  };

  /*
  |--------------------------------------------------------------------------
  | Load Product Options
  |--------------------------------------------------------------------------
  */

  const loadOptions = async () => {

    try {

      const data =
        await getProductOptions({
          per_page: 100,
        });

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

        "Gagal mengambil product option"

      );

    }

  };

  /*
  |--------------------------------------------------------------------------
  | Initial Load
  |--------------------------------------------------------------------------
  */

  useEffect(() => {

    loadOptions();

  }, []);

  useEffect(() => {

    loadValues();

  }, [

    search,
    filterOption,
    filterStatus,

  ]);

  /*
  |--------------------------------------------------------------------------
  | Statistics
  |--------------------------------------------------------------------------
  */

  const totalValues =
    values.length;

  const activeValues =
    values.filter(

      (item) =>
        item.is_active

    ).length;

  const usedValues =
    values.filter(

      (item) =>
        item.is_used

    ).length;

  const unusedValues =
    values.filter(

      (item) =>
        !item.is_used

    ).length;
      /*
  |--------------------------------------------------------------------------
  | Reset Form
  |--------------------------------------------------------------------------
  */

  const resetForm = () => {

    setEditingId(null);

    setProductOptionId("");

    setValue("");

    setCode("");

    setSortOrder("");

    setIsActive(true);

  };

  /*
  |--------------------------------------------------------------------------
  | Edit Value
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

    setProductOptionId(
      item.product_option_id || ""
    );

    setValue(
      item.value || ""
    );

    setCode(
      item.code || ""
    );

    setSortOrder(
      item.sort_order ?? ""
    );

    setIsActive(
      item.is_active
    );

    window.scrollTo({

      top: 0,

      behavior: "smooth",

    });

  };

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

      if (

        !editingId &&
        !productOptionId

      ) {

        return errorAlert(
          "Product option wajib dipilih"
        );

      }

      if (!value.trim()) {

        return errorAlert(
          "Nilai option wajib diisi"
        );

      }

      try {

        setSubmitting(true);

        /*
        |--------------------------------------------------------------------------
        | Create
        |--------------------------------------------------------------------------
        */

        if (!editingId) {

          const payload = {

            product_option_id:
              Number(
                productOptionId
              ),

            value:
              value.trim(),

            code:
              code.trim() || null,

            is_active:
              isActive,

          };

          if (

            sortOrder !== ""

          ) {

            payload.sort_order =
              Number(
                sortOrder
              );

          }

          await createProductOptionValue(
            payload
          );

          await successAlert(

            "Option value berhasil dibuat"

          );

        }

        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */

        else {

          const payload = {

            value:
              value.trim(),

            code:
              code.trim() || null,

            is_active:
              isActive,

          };

          if (

            sortOrder !== ""

          ) {

            payload.sort_order =
              Number(
                sortOrder
              );

          }

          await updateProductOptionValue(

            editingId,

            payload

          );

          await successAlert(

            "Option value berhasil diperbarui"

          );

        }

        resetForm();

        await loadValues();

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

          "Gagal menyimpan option value"

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

  const handleDelete = async (
    item
  ) => {

    if (!canDelete) {
      return;
    }

    if (
      item.can_be_deleted === false
    ) {

      return errorAlert(

        "Option value sudah digunakan oleh SKU dan tidak dapat dihapus."

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

      await deleteProductOptionValue(
        item.id
      );

      await successAlert(

        "Option value berhasil dihapus"

      );

      await loadValues();

    } catch (error) {

      errorAlert(

        error?.response
          ?.data
          ?.message ||

        "Gagal menghapus option value"

      );

    }

  };

  /*
  |--------------------------------------------------------------------------
  | Toggle Status
  |--------------------------------------------------------------------------
  */

  const handleToggleStatus =
    async (item) => {

      if (!canUpdate) {
        return;
      }

      try {

        if (item.is_active) {

          await deactivateProductOptionValue(
            item.id
          );

        } else {

          await activateProductOptionValue(
            item.id
          );

        }

        await successAlert(

          item.is_active

            ? "Option value berhasil dinonaktifkan"

            : "Option value berhasil diaktifkan"

        );

        await loadValues();

      } catch (error) {

        errorAlert(

          error?.response
            ?.data
            ?.message ||

          "Gagal mengubah status option value"

        );

      }

    };

  /*
  |--------------------------------------------------------------------------
  | Render
  |--------------------------------------------------------------------------
  */

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

                <Tag className="w-8 h-8" />

              </div>

              <div>

                <h1 className="text-4xl font-bold">
                  Product Option Values
                </h1>

                <p className="text-red-100 mt-2">

                  Kelola seluruh nilai opsi produk Kellogg's seperti Small,
                  Medium, Large, Chocolate, Strawberry, dan lainnya.

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

              Total: {totalValues}

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

              Active: {activeValues}

            </div>

          </div>

        </div>

      </div>
            {/* Statistics */}
      <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">

        <div className="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">

          <div className="flex justify-between items-center">

            <div>

              <p className="text-slate-500 text-sm">
                Total Values
              </p>

              <h3 className="text-4xl font-bold mt-2">
                {totalValues}
              </h3>

            </div>

            <div className="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center">

              <Tag className="w-7 h-7 text-blue-600" />

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
                {activeValues}
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
                Used by SKU
              </p>

              <h3 className="text-4xl font-bold mt-2">
                {usedValues}
              </h3>

            </div>

            <div className="w-14 h-14 rounded-2xl bg-purple-50 flex items-center justify-center">

              <Boxes className="w-7 h-7 text-purple-600" />

            </div>

          </div>

        </div>

        <div className="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">

          <div className="flex justify-between items-center">

            <div>

              <p className="text-slate-500 text-sm">
                Unused
              </p>

              <h3 className="text-4xl font-bold mt-2">
                {unusedValues}
              </h3>

            </div>

            <div className="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center">

              <Circle className="w-7 h-7 text-slate-600" />

            </div>

          </div>

        </div>

      </div>

      {/* Search & Filter */}
      <div className="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">

        <div className="flex flex-col lg:flex-row gap-4">

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
              placeholder="Cari option value..."
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
            value={filterOption}
            onChange={(e) =>
              setFilterOption(
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
              Semua Option
            </option>

            {options.map((option) => (

              <option
                key={option.id}
                value={option.id}
              >

                {option.name}

                {option.product?.name
                  ? ` (${option.product.name})`
                  : ""}

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

        </div>

      </div>
            {/* Form */}
      {(canCreate || canUpdate) && (

        <div className="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">

          <div className="flex items-center gap-3 mb-8">

            <div className="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center">

              <Plus className="w-6 h-6 text-red-600" />

            </div>

            <div>

              <h2 className="text-2xl font-bold">

                {editingId
                  ? "Edit Product Option Value"
                  : "Create Product Option Value"}

              </h2>

              <p className="text-slate-500">

                Tambahkan atau ubah nilai option produk.

              </p>

            </div>

          </div>

          <form
            onSubmit={handleSubmit}
            className="space-y-6"
          >

            <div className="grid md:grid-cols-2 gap-5">

              {/* Product Option */}
              <div>

                <label className="block text-sm font-medium text-slate-700 mb-2">

                  Product Option

                </label>

                <select
                  value={productOptionId}
                  disabled={
                    editingId !== null ||
                    !canSubmit ||
                    submitting
                  }
                  onChange={(e) =>
                    setProductOptionId(
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
                    Pilih Product Option
                  </option>

                  {options.map((option) => (

                    <option
                      key={option.id}
                      value={option.id}
                    >

                      {option.name}

                      {option.product?.name
                        ? ` (${option.product.name})`
                        : ""}

                    </option>

                  ))}

                </select>

                {editingId && (

                  <p className="text-xs text-slate-400 mt-2">

                    Product option tidak dapat diubah setelah value dibuat.

                  </p>

                )}

              </div>

              {/* Value */}
              <div>

                <label className="block text-sm font-medium text-slate-700 mb-2">

                  Value

                </label>

                <input
                  type="text"
                  value={value}
                  disabled={
                    !canSubmit ||
                    submitting
                  }
                  onChange={(e) =>
                    setValue(
                      e.target.value
                    )
                  }
                  placeholder="Contoh: Small"
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

              {/* Code */}
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
                  placeholder="S"
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

              {/* Sort Order */}
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

            {/* Active Status */}
            <div>

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

                    Nilai option akan tersedia untuk digunakan.

                  </p>

                </div>

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
                  className="
                    w-5
                    h-5
                    accent-green-600
                  "
                />

              </div>

            </div>

            {/* Actions */}
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
                    ? "Update Value"
                    : "Create Value"}

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
            {/* Values Table */}
      <div className="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">

        <div className="px-6 py-5 border-b border-slate-100">

          <h2 className="text-xl font-bold text-slate-900">
            Product Option Values List
          </h2>

          <p className="text-sm text-slate-500 mt-1">
            Menampilkan {values.length} option value.
          </p>

        </div>

        <div className="overflow-x-auto">

          <table className="w-full">

            <thead className="bg-slate-50">

              <tr>

                <th className="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                  Value
                </th>

                <th className="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                  Product
                </th>

                <th className="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                  Option
                </th>

                <th className="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                  SKU Usage
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

                    Loading option values...

                  </td>

                </tr>

              ) : values.length === 0 ? (

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

                      <Tag className="w-16 h-16 text-slate-300 mb-4" />

                      <h3 className="text-lg font-semibold text-slate-700">
                        Belum ada option value
                      </h3>

                      <p className="text-slate-500 mt-2">
                        Tambahkan option value pertama Anda.
                      </p>

                    </div>

                  </td>

                </tr>

              ) : (

                values.map((item) => (

                  <tr
                    key={item.id}
                    className="
                      border-t
                      border-slate-100
                      hover:bg-slate-50
                      transition
                    "
                  >

                    {/* Value */}
                    <td className="px-6 py-5">

                      <div>

                        <p className="font-semibold text-slate-900">

                          {item.value}

                        </p>

                        <p className="text-xs text-slate-400 mt-1">

                          {item.code || "-"}

                        </p>

                      </div>

                    </td>

                    {/* Product */}
                    <td className="px-6 py-5 text-slate-700">

                      {item.product?.name || "-"}

                    </td>

                    {/* Option */}
                    <td className="px-6 py-5">

                      <div>

                        <p className="font-medium text-slate-800">

                          {item.option?.name || "-"}

                        </p>

                        <p className="text-xs text-slate-400">

                          {item.option?.code || "-"}

                        </p>

                      </div>

                    </td>

                    {/* SKU Usage */}
                    <td className="px-6 py-5">

                      <div className="flex flex-col gap-2">

                        <span
                          className={`
                            inline-flex
                            items-center
                            px-3
                            py-1
                            rounded-full
                            text-xs
                            font-semibold
                            w-fit
                            ${
                              item.is_used
                                ? "bg-purple-100 text-purple-700"
                                : "bg-slate-100 text-slate-700"
                            }
                          `}
                        >

                          {item.sku_count || 0} SKU

                        </span>

                        <span
                          className={`
                            inline-flex
                            items-center
                            px-3
                            py-1
                            rounded-full
                            text-xs
                            font-semibold
                            w-fit
                            ${
                              item.is_used
                                ? "bg-blue-100 text-blue-700"
                                : "bg-green-100 text-green-700"
                            }
                          `}
                        >

                          {item.is_used
                            ? "Used"
                            : "Unused"}

                        </span>

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

                        <div className="flex justify-end flex-wrap gap-2">

                          {canUpdate && (

                            <button
                              onClick={() =>
                                handleEdit(item)
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
                                handleToggleStatus(item)
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
                                  item.is_active
                                    ? "bg-orange-50 text-orange-700 hover:bg-orange-100"
                                    : "bg-green-50 text-green-700 hover:bg-green-100"
                                }
                              `}
                            >

                              {item.is_active ? (

                                <>
                                  <XCircle className="w-4 h-4" />
                                  Deactivate
                                </>

                              ) : (

                                <>
                                  <CheckCircle className="w-4 h-4" />
                                  Activate
                                </>

                              )}

                            </button>

                          )}

                          {canDelete &&
                            item.can_be_deleted && (

                              <button
                                onClick={() =>
                                  handleDelete(item)
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