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
  getProductOptionValues,
} from "../../services/productOptionValueService";

import {
  getProductSkus,
  getProductSku,
  createProductSku,
  updateProductSku,
  deleteProductSku,
  activateProductSku,
  deactivateProductSku,
  publishProductSku,
  archiveProductSku,
  setDefaultProductSku,

  getVariationLabelFromSku,
  getStockFromSku,
  canDeleteSku,
  isSkuPublished,
  isSkuArchived,
} from "../../services/productSkuService";

import {
  successAlert,
  errorAlert,
  confirmDelete,
} from "../../utils/alert";

import {
  Search,
  Plus,
  Pencil,
  Trash2,
  Save,
  Package,
  CheckCircle,
  XCircle,
  Archive,
  Star,
  Boxes,
  Warehouse,
  Rocket,
} from "lucide-react";

export default function ProductSku() {

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

  const [skus, setSkus] =
    useState([]);

  const [products, setProducts] =
    useState([]);

  const [
    optionValues,
    setOptionValues,
  ] = useState([]);

  const [loading, setLoading] =
    useState(false);

  const [
    submitting,
    setSubmitting,
  ] = useState(false);

  const [
    editingId,
    setEditingId,
  ] = useState(null);

  /*
  |--------------------------------------------------------------------------
  | Pagination
  |--------------------------------------------------------------------------
  */

  const [meta, setMeta] =
    useState({});

  const [page, setPage] =
    useState(1);

  const [perPage, setPerPage] =
    useState(15);

  /*
  |--------------------------------------------------------------------------
  | Form States
  |--------------------------------------------------------------------------
  */

  const [productId, setProductId] =
    useState("");

  const [sku, setSku] =
    useState("");

  const [barcode, setBarcode] =
    useState("");

  const [price, setPrice] =
    useState("");

  const [
    compareAtPrice,
    setCompareAtPrice,
  ] = useState("");

  const [
    costPrice,
    setCostPrice,
  ] = useState("");

  const [weight, setWeight] =
    useState("");

  const [length, setLength] =
    useState("");

  const [width, setWidth] =
    useState("");

  const [height, setHeight] =
    useState("");

  const [
    minimumOrderQuantity,
    setMinimumOrderQuantity,
  ] = useState(1);

  const [
    maximumOrderQuantity,
    setMaximumOrderQuantity,
  ] = useState("");

  /*
  |--------------------------------------------------------------------------
  | Option Values
  |--------------------------------------------------------------------------
  */

  const [
    selectedOptionValues,
    setSelectedOptionValues,
  ] = useState([]);

  /*
  |--------------------------------------------------------------------------
  | Inventory
  |--------------------------------------------------------------------------
  */

  const [
    currentStock,
    setCurrentStock,
  ] = useState(0);

  const [
    minimumStock,
    setMinimumStock,
  ] = useState(0);

  const [
    maximumStock,
    setMaximumStock,
  ] = useState("");

  const [
    reorderPoint,
    setReorderPoint,
  ] = useState(0);

  const [
    allowBackorder,
    setAllowBackorder,
  ] = useState(false);

  /*
  |--------------------------------------------------------------------------
  | Status
  |--------------------------------------------------------------------------
  */

  const [status, setStatus] =
    useState("draft");

  const [isActive, setIsActive] =
    useState(true);

  const [isDefault, setIsDefault] =
    useState(false);

  /*
  |--------------------------------------------------------------------------
  | Filters
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
    filterActive,
    setFilterActive,
  ] = useState("");

  const [
    filterDefault,
    setFilterDefault,
  ] = useState("");

  /*
  |--------------------------------------------------------------------------
  | Submit Permission
  |--------------------------------------------------------------------------
  */

  const canSubmit =
    editingId
      ? canUpdate
      : canCreate;

  /*
  |--------------------------------------------------------------------------
  | Load Products
  |--------------------------------------------------------------------------
  */

  const loadProducts =
    async () => {

      try {

        const result =
          await getProducts({
            per_page: 999,
          });

        setProducts(
          result.data || []
        );

      } catch (error) {

        console.error(error);

        setProducts([]);

        errorAlert(
          error?.response
            ?.data
            ?.message ||
          "Gagal mengambil data produk."
        );

      }

    };

  /*
  |--------------------------------------------------------------------------
  | Load Option Values
  |--------------------------------------------------------------------------
  */

  const loadOptionValues =
    async () => {

      try {

        const result =
          await getProductOptionValues({
            is_active: true,
            per_page: 999,
          });

        setOptionValues(
          result.data || []
        );

      } catch (error) {

        console.error(error);

        setOptionValues([]);

        errorAlert(
          error?.response
            ?.data
            ?.message ||
          "Gagal mengambil option value."
        );

      }

    };

  /*
  |--------------------------------------------------------------------------
  | Load SKUs
  |--------------------------------------------------------------------------
  */

  const loadSkus =
    async () => {

      try {

        setLoading(true);

        const params = {
          page,
          per_page: perPage,
        };

        if (search.trim()) {
          params.search =
            search.trim();
        }

        if (filterProduct) {
          params.product_id =
            filterProduct;
        }

        if (filterStatus) {
          params.status =
            filterStatus;
        }

        if (filterActive !== "") {
          params.is_active =
            filterActive === "true";
        }

        if (filterDefault !== "") {
          params.is_default =
            filterDefault === "true";
        }

        const result =
          await getProductSkus(
            params
          );

        setSkus(
          result.data || []
        );

        setMeta(
          result.meta || {}
        );

      } catch (error) {

        console.error(error);

        setSkus([]);

        setMeta({});

        errorAlert(
          error?.response
            ?.data
            ?.message ||
          "Gagal mengambil data SKU."
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

    loadProducts();

    loadOptionValues();

  }, []);

  useEffect(() => {

    loadSkus();

  }, [
    page,
    perPage,
    search,
    filterProduct,
    filterStatus,
    filterActive,
    filterDefault,
  ]);

  /*
  |--------------------------------------------------------------------------
  | Statistics
  |--------------------------------------------------------------------------
  */

  const totalSkus =
    meta.total ??
    skus.length;

  const activeSkus =
    skus.filter(
      (item) => item.is_active
    ).length;

  const defaultSkus =
    skus.filter(
      (item) => item.is_default
    ).length;

  const lowStockSkus =
    skus.filter(
      (item) => item.is_low_stock
    ).length;

  const outOfStockSkus =
    skus.filter(
      (item) => item.is_out_of_stock
    ).length;

  /*
  |--------------------------------------------------------------------------
  | Reset Form
  |--------------------------------------------------------------------------
  */

  const resetForm = () => {

    setEditingId(null);

    setProductId("");

    setSku("");

    setBarcode("");

    setPrice("");

    setCompareAtPrice("");

    setCostPrice("");

    setWeight("");

    setLength("");

    setWidth("");

    setHeight("");

    setMinimumOrderQuantity(1);

    setMaximumOrderQuantity("");

    setSelectedOptionValues([]);

    /*
    |--------------------------------------------------------------------------
    | Inventory
    |--------------------------------------------------------------------------
    */

    setCurrentStock(0);

    setMinimumStock(0);

    setMaximumStock("");

    setReorderPoint(0);

    setAllowBackorder(false);

    /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

    setStatus("draft");

    setIsActive(true);

    setIsDefault(false);

  };

  /*
  |--------------------------------------------------------------------------
  | Edit SKU
  |--------------------------------------------------------------------------
  */

  const handleEdit =
    async (item) => {

      if (!canUpdate) {
        return;
      }

      try {

        setLoading(true);

        const skuData =
          await getProductSku(
            item.id
          );

        /*
        |--------------------------------------------------------------------------
        | Basic Information
        |--------------------------------------------------------------------------
        */

        setEditingId(
          skuData.id
        );

        setProductId(
          skuData.product_id
            ?.toString() || ""
        );

        setSku(
          skuData.sku || ""
        );

        setBarcode(
          skuData.barcode || ""
        );

        /*
        |--------------------------------------------------------------------------
        | Pricing
        |--------------------------------------------------------------------------
        */

        setPrice(
          skuData.price ?? ""
        );

        setCompareAtPrice(
          skuData.compare_at_price ?? ""
        );

        setCostPrice(
          skuData.cost_price ?? ""
        );

        /*
        |--------------------------------------------------------------------------
        | Dimensions
        |--------------------------------------------------------------------------
        */

        setWeight(
          skuData.weight ?? ""
        );

        setLength(
          skuData.length ?? ""
        );

        setWidth(
          skuData.width ?? ""
        );

        setHeight(
          skuData.height ?? ""
        );

        /*
        |--------------------------------------------------------------------------
        | Purchase Rules
        |--------------------------------------------------------------------------
        */

        setMinimumOrderQuantity(
          skuData.minimum_order_quantity ?? 1
        );

        setMaximumOrderQuantity(
          skuData.maximum_order_quantity ?? ""
        );

        /*
        |--------------------------------------------------------------------------
        | Option Values
        |--------------------------------------------------------------------------
        */

        setSelectedOptionValues(

          skuData.option_values
            ? skuData.option_values.map(
                (value) => value.id
              )
            : []

        );

        /*
        |--------------------------------------------------------------------------
        | Inventory
        |--------------------------------------------------------------------------
        */

        setCurrentStock(
          skuData.stock ?? 0
        );

        setMinimumStock(
          skuData.inventory?.minimum_stock ?? 0
        );

        setMaximumStock(
          skuData.inventory?.maximum_stock ?? ""
        );

        setReorderPoint(
          skuData.inventory?.reorder_point ?? 0
        );

        setAllowBackorder(
          skuData.inventory?.allow_backorder ?? false
        );

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        setStatus(
          skuData.status || "draft"
        );

        setIsActive(
          skuData.is_active ?? true
        );

        setIsDefault(
          skuData.is_default ?? false
        );

        /*
        |--------------------------------------------------------------------------
        | Scroll To Form
        |--------------------------------------------------------------------------
        */

        window.scrollTo({

          top: 0,

          behavior: "smooth",

        });

      } catch (error) {

        console.error(error);

        errorAlert(

          error?.response
            ?.data
            ?.message ||

          "Gagal mengambil detail SKU."

        );

      } finally {

        setLoading(false);

      }

    };

  /*
  |--------------------------------------------------------------------------
  | Option Value Selection
  |--------------------------------------------------------------------------
  */

  const handleOptionValueChange =
    (optionValueId) => {

      setSelectedOptionValues(

        (previous) => {

          const exists =
            previous.includes(
              optionValueId
            );

          if (exists) {

            return previous.filter(

              (id) =>
                id !== optionValueId

            );

          }

          return [

            ...previous,

            optionValueId,

          ];

        }

      );

    };

  /*
  |--------------------------------------------------------------------------
  | Product Option Grouping
  |--------------------------------------------------------------------------
  */

  const groupedOptionValues =
    useMemo(() => {

      return optionValues.reduce(

        (groups, value) => {

          const optionName =

            value.option?.name ||

            "Lainnya";

          if (!groups[optionName]) {

            groups[optionName] = [];

          }

          groups[optionName].push(
            value
          );

          return groups;

        },

        {}

      );

    }, [

      optionValues,

    ]);

  /*
  |--------------------------------------------------------------------------
  | Selected Option Labels
  |--------------------------------------------------------------------------
  */

  const selectedOptionLabels =
    useMemo(() => {

      return optionValues

        .filter(

          (value) =>

            selectedOptionValues.includes(
              value.id
            )

        )

        .map((value) => {

          const optionName =

            value.option?.name || "";

          return optionName

            ? `${optionName}: ${value.value}`

            : value.value;

        });

    }, [

      optionValues,

      selectedOptionValues,

    ]);

  /*
  |--------------------------------------------------------------------------
  | Submit SKU
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

          "Anda tidak memiliki izin untuk melakukan aksi ini."

        );

      }

      /*
      |--------------------------------------------------------------------------
      | Frontend Validation
      |--------------------------------------------------------------------------
      */

      if (!productId) {

        return errorAlert(
          "Produk wajib dipilih."
        );

      }

      if (!sku.trim()) {

        return errorAlert(
          "SKU wajib diisi."
        );

      }

      if (
        price === "" ||
        Number(price) < 0
      ) {

        return errorAlert(
          "Harga jual tidak valid."
        );

      }

      if (
        weight === "" ||
        Number(weight) < 0
      ) {

        return errorAlert(
          "Berat tidak valid."
        );

      }

      if (
        minimumOrderQuantity < 1
      ) {

        return errorAlert(
          "Minimum pembelian minimal 1."
        );

      }

      if (

        maximumOrderQuantity !== ""

        &&

        Number(
          maximumOrderQuantity
        ) < Number(
          minimumOrderQuantity
        )

      ) {

        return errorAlert(

          "Maksimum pembelian harus lebih besar atau sama dengan minimum pembelian."

        );

      }

      if (

        compareAtPrice !== ""

        &&

        Number(compareAtPrice)
          <= Number(price)

      ) {

        return errorAlert(

          "Harga coret harus lebih besar dari harga jual."

        );

      }

      if (

        costPrice !== ""

        &&

        Number(costPrice)
          > Number(price)

      ) {

        return errorAlert(

          "Harga modal tidak boleh melebihi harga jual."

        );

      }

      if (
        isDefault &&
        !isActive
      ) {

        return errorAlert(

          "SKU default harus aktif."

        );

      }

      /*
      |--------------------------------------------------------------------------
      | Payload
      |--------------------------------------------------------------------------
      */

      const payload = {

        product_id:
          Number(productId),

        sku:
          sku
            .trim()
            .toUpperCase(),

        barcode:
          barcode.trim() || null,

        /*
        |--------------------------------------------------------------------------
        | Pricing
        |--------------------------------------------------------------------------
        */

        price:
          Number(price),

        compare_at_price:

          compareAtPrice !== ""

            ? Number(compareAtPrice)

            : null,

        cost_price:

          costPrice !== ""

            ? Number(costPrice)

            : null,

        /*
        |--------------------------------------------------------------------------
        | Dimensions
        |--------------------------------------------------------------------------
        */

        weight:
          Number(weight),

        length:

          length !== ""

            ? Number(length)

            : null,

        width:

          width !== ""

            ? Number(width)

            : null,

        height:

          height !== ""

            ? Number(height)

            : null,

        /*
        |--------------------------------------------------------------------------
        | Purchase Rules
        |--------------------------------------------------------------------------
        */

        minimum_order_quantity:

          Number(
            minimumOrderQuantity
          ),

        maximum_order_quantity:

          maximumOrderQuantity !== ""

            ? Number(
                maximumOrderQuantity
              )

            : null,

        /*
        |--------------------------------------------------------------------------
        | Variations
        |--------------------------------------------------------------------------
        */

        option_value_ids:
          selectedOptionValues,

        /*
        |--------------------------------------------------------------------------
        | Inventory
        |--------------------------------------------------------------------------
        */

        stock:
          Number(currentStock),

        minimum_stock:
          Number(minimumStock),

        maximum_stock:

          maximumStock !== ""

            ? Number(maximumStock)

            : null,

        reorder_point:
          Number(reorderPoint),

        allow_backorder:
          allowBackorder,

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        status,

        is_active:
          isActive,

        is_default:
          isDefault,

      };

      try {

        setSubmitting(true);

        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */

        if (editingId) {

          await updateProductSku(

            editingId,

            payload

          );

          await successAlert(

            "SKU berhasil diperbarui."

          );

        }

        /*
        |--------------------------------------------------------------------------
        | Create
        |--------------------------------------------------------------------------
        */

        else {

          await createProductSku(
            payload
          );

          await successAlert(

            "SKU berhasil dibuat."

          );

        }

        resetForm();

        await loadSkus();

      } catch (error) {

        console.error(error);

        const errors =

          error?.response
            ?.data
            ?.errors;

        if (errors) {

          const firstError =

            Object
              .values(errors)
              .flat()?.[0];

          return errorAlert(
            firstError
          );

        }

        errorAlert(

          error?.response
            ?.data
            ?.message ||

          "Gagal menyimpan SKU."

        );

      } finally {

        setSubmitting(false);

      }

    };

      /*
  |--------------------------------------------------------------------------
  | Delete SKU
  |--------------------------------------------------------------------------
  */

  const handleDelete =
    async (item) => {

      if (!canDelete) {
        return;
      }

      if (!canDeleteSku(item)) {

        return errorAlert(
          "SKU ini tidak dapat dihapus."
        );

      }

      const result =
        await confirmDelete();

      if (!result.isConfirmed) {
        return;
      }

      try {

        await deleteProductSku(
          item.id
        );

        await successAlert(
          "SKU berhasil dihapus."
        );

        /*
        |--------------------------------------------------------------------------
        | Reset Form
        |--------------------------------------------------------------------------
        */

        if (
          editingId === item.id
        ) {

          resetForm();

        }

        await loadSkus();

      } catch (error) {

        console.error(error);

        errorAlert(

          error?.response
            ?.data
            ?.message ||

          "Gagal menghapus SKU."

        );

      }

    };

  /*
  |--------------------------------------------------------------------------
  | Activate SKU
  |--------------------------------------------------------------------------
  */

  const handleActivate =
    async (item) => {

      if (!canUpdate) {
        return;
      }

      try {

        await activateProductSku(
          item.id
        );

        await successAlert(
          "SKU berhasil diaktifkan."
        );

        /*
        |--------------------------------------------------------------------------
        | Refresh Edit Form
        |--------------------------------------------------------------------------
        */

        if (
          editingId === item.id
        ) {

          setIsActive(true);

        }

        await loadSkus();

      } catch (error) {

        console.error(error);

        errorAlert(

          error?.response
            ?.data
            ?.message ||

          "Gagal mengaktifkan SKU."

        );

      }

    };

  /*
  |--------------------------------------------------------------------------
  | Deactivate SKU
  |--------------------------------------------------------------------------
  */

  const handleDeactivate =
    async (item) => {

      if (!canUpdate) {
        return;
      }

      try {

        await deactivateProductSku(
          item.id
        );

        await successAlert(
          "SKU berhasil dinonaktifkan."
        );

        /*
        |--------------------------------------------------------------------------
        | Refresh Edit Form
        |--------------------------------------------------------------------------
        */

        if (
          editingId === item.id
        ) {

          setIsActive(false);

        }

        await loadSkus();

      } catch (error) {

        console.error(error);

        errorAlert(

          error?.response
            ?.data
            ?.message ||

          "Gagal menonaktifkan SKU."

        );

      }

    };

  /*
  |--------------------------------------------------------------------------
  | Toggle Active Status
  |--------------------------------------------------------------------------
  */

  const handleToggleStatus =
    async (item) => {

      if (!canUpdate) {
        return;
      }

      if (item.is_active) {

        return handleDeactivate(
          item
        );

      }

      return handleActivate(
        item
      );

    };

  /*
  |--------------------------------------------------------------------------
  | Publish SKU
  |--------------------------------------------------------------------------
  */

  const handlePublish =
    async (item) => {

      if (!canUpdate) {
        return;
      }

      try {

        await publishProductSku(
          item.id
        );

        await successAlert(
          "SKU berhasil dipublikasikan."
        );

        /*
        |--------------------------------------------------------------------------
        | Refresh Edit Form
        |--------------------------------------------------------------------------
        */

        if (
          editingId === item.id
        ) {

          setStatus("active");

          setIsActive(true);

        }

        await loadSkus();

      } catch (error) {

        console.error(error);

        errorAlert(

          error?.response
            ?.data
            ?.message ||

          "Gagal mempublikasikan SKU."

        );

      }

    };

  /*
  |--------------------------------------------------------------------------
  | Archive SKU
  |--------------------------------------------------------------------------
  */

  const handleArchive =
    async (item) => {

      if (!canUpdate) {
        return;
      }

      try {

        await archiveProductSku(
          item.id
        );

        await successAlert(
          "SKU berhasil diarsipkan."
        );

        /*
        |--------------------------------------------------------------------------
        | Reset Form
        |--------------------------------------------------------------------------
        */

        if (
          editingId === item.id
        ) {

          resetForm();

        }

        await loadSkus();

      } catch (error) {

        console.error(error);

        errorAlert(

          error?.response
            ?.data
            ?.message ||

          "Gagal mengarsipkan SKU."

        );

      }

    };

  /*
  |--------------------------------------------------------------------------
  | Set Default SKU
  |--------------------------------------------------------------------------
  */

  const handleSetDefault =
    async (item) => {

      if (!canUpdate) {
        return;
      }

      try {

        await setDefaultProductSku(
          item.id
        );

        await successAlert(
          "SKU default berhasil diperbarui."
        );

        /*
        |--------------------------------------------------------------------------
        | Refresh Edit Form
        |--------------------------------------------------------------------------
        */

        if (
          editingId === item.id
        ) {

          setIsDefault(true);

          setIsActive(true);

          setStatus("active");

        }

        await loadSkus();

      } catch (error) {

        console.error(error);

        errorAlert(

          error?.response
            ?.data
            ?.message ||

          "Gagal mengubah SKU default."

        );

      }

    };

  /*
  |--------------------------------------------------------------------------
  | Stock Badge Helper
  |--------------------------------------------------------------------------
  */

  const getStockBadge =
    (item) => {

      if (
        item.is_out_of_stock
      ) {

        return {

          label:
            "Out Of Stock",

          className:
            "bg-red-100 text-red-700",

        };

      }

      if (
        item.is_low_stock
      ) {

        return {

          label:
            "Low Stock",

          className:
            "bg-amber-100 text-amber-700",

        };

      }

      return {

        label:
          "Available",

        className:
          "bg-green-100 text-green-700",

      };

    };

      return (

    <div className="space-y-6">

      {/* Hero */}
      <div
        className="
          relative
          overflow-hidden
          rounded-3xl
          bg-gradient-to-r
          from-red-600
          via-red-500
          to-orange-500
          p-8
          text-white
          shadow-xl
        "
      >

        <div className="absolute -top-16 -right-16 h-64 w-64 rounded-full bg-white/10" />

        <div className="absolute -bottom-20 -left-20 h-72 w-72 rounded-full bg-white/10" />

        <div
          className="
            relative
            z-10
            flex
            flex-col
            gap-6
            lg:flex-row
            lg:items-center
            lg:justify-between
          "
        >

          <div>

            <div className="flex items-center gap-4">

              <div
                className="
                  flex
                  h-16
                  w-16
                  items-center
                  justify-center
                  rounded-2xl
                  bg-white/10
                  backdrop-blur
                "
              >

                <Package className="h-8 w-8" />

              </div>

              <div>

                <h1 className="text-4xl font-bold">

                  Product SKU

                </h1>

                <p className="mt-2 text-red-100">

                  Kelola seluruh SKU Kellogg's,
                  variasi produk, harga, dan stok
                  inventory dalam satu halaman.

                </p>

              </div>

            </div>

          </div>

          <div className="flex flex-wrap gap-3">

            <div
              className="
                rounded-2xl
                bg-white/10
                px-4
                py-2
                text-sm
                backdrop-blur
              "
            >

              Total: {totalSkus}

            </div>

            <div
              className="
                rounded-2xl
                bg-white/10
                px-4
                py-2
                text-sm
                backdrop-blur
              "
            >

              Active: {activeSkus}

            </div>

          </div>

        </div>

      </div>

      {/* Statistics */}
      <div
        className="
          grid
          grid-cols-1
          gap-6
          sm:grid-cols-2
          xl:grid-cols-5
        "
      >

        {/* Total */}
        <div className="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm">

          <div className="flex items-center justify-between">

            <div>

              <p className="text-sm text-slate-500">

                Total SKU

              </p>

              <h3 className="mt-2 text-4xl font-bold">

                {totalSkus}

              </h3>

            </div>

            <div
              className="
                flex
                h-14
                w-14
                items-center
                justify-center
                rounded-2xl
                bg-blue-50
              "
            >

              <Package className="h-7 w-7 text-blue-600" />

            </div>

          </div>

        </div>

        {/* Active */}
        <div className="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm">

          <div className="flex items-center justify-between">

            <div>

              <p className="text-sm text-slate-500">

                Active

              </p>

              <h3 className="mt-2 text-4xl font-bold">

                {activeSkus}

              </h3>

            </div>

            <div
              className="
                flex
                h-14
                w-14
                items-center
                justify-center
                rounded-2xl
                bg-green-50
              "
            >

              <CheckCircle className="h-7 w-7 text-green-600" />

            </div>

          </div>

        </div>

        {/* Default */}
        <div className="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm">

          <div className="flex items-center justify-between">

            <div>

              <p className="text-sm text-slate-500">

                Default SKU

              </p>

              <h3 className="mt-2 text-4xl font-bold">

                {defaultSkus}

              </h3>

            </div>

            <div
              className="
                flex
                h-14
                w-14
                items-center
                justify-center
                rounded-2xl
                bg-yellow-50
              "
            >

              <Star className="h-7 w-7 text-yellow-600" />

            </div>

          </div>

        </div>

        {/* Low Stock */}
        <div className="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm">

          <div className="flex items-center justify-between">

            <div>

              <p className="text-sm text-slate-500">

                Low Stock

              </p>

              <h3 className="mt-2 text-4xl font-bold">

                {lowStockSkus}

              </h3>

            </div>

            <div
              className="
                flex
                h-14
                w-14
                items-center
                justify-center
                rounded-2xl
                bg-orange-50
              "
            >

              <Warehouse className="h-7 w-7 text-orange-600" />

            </div>

          </div>

        </div>

        {/* Out Of Stock */}
        <div className="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm">

          <div className="flex items-center justify-between">

            <div>

              <p className="text-sm text-slate-500">

                Out Of Stock

              </p>

              <h3 className="mt-2 text-4xl font-bold">

                {outOfStockSkus}

              </h3>

            </div>

            <div
              className="
                flex
                h-14
                w-14
                items-center
                justify-center
                rounded-2xl
                bg-red-50
              "
            >

              <Boxes className="h-7 w-7 text-red-600" />

            </div>

          </div>

        </div>

      </div>

      {/* Search & Filters */}
      <div
        className="
          rounded-3xl
          border
          border-slate-100
          bg-white
          p-6
          shadow-sm
        "
      >

        <div
          className="
            flex
            flex-col
            gap-4
            xl:flex-row
          "
        >

          {/* Search */}
          <div className="relative flex-1">

            <Search
              className="
                absolute
                left-4
                top-1/2
                h-5
                w-5
                -translate-y-1/2
                text-slate-400
              "
            />

            <input
              type="text"
              value={search}
              onChange={(e) =>
                setSearch(
                  e.target.value
                )
              }
              placeholder="Cari SKU, barcode, atau produk..."
              className="
                w-full
                rounded-2xl
                border
                border-slate-200
                py-3
                pl-12
                pr-4
                focus:outline-none
                focus:ring-2
                focus:ring-red-500
              "
            />

          </div>

          {/* Product */}
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

          {/* Status */}
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

          {/* Active */}
          <select
            value={filterActive}
            onChange={(e) =>
              setFilterActive(
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

              Semua Active

            </option>

            <option value="true">

              Active

            </option>

            <option value="false">

              Inactive

            </option>

          </select>

          {/* Default */}
          <select
            value={filterDefault}
            onChange={(e) =>
              setFilterDefault(
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

              Semua Default

            </option>

            <option value="true">

              Default

            </option>

            <option value="false">

              Non Default

            </option>

          </select>

        </div>

      </div>

      {/* Form */}
      {(canCreate || canUpdate) && (

        <div
          className="
            rounded-3xl
            border
            border-slate-100
            bg-white
            p-8
            shadow-sm
          "
        >

          {/* Header */}
          <div className="mb-8 flex items-center gap-3">

            <div
              className="
                flex
                h-12
                w-12
                items-center
                justify-center
                rounded-2xl
                bg-red-50
              "
            >

              <Plus className="h-6 w-6 text-red-600" />

            </div>

            <div>

              <h2 className="text-2xl font-bold">

                {editingId
                  ? "Edit Product SKU"
                  : "Create Product SKU"}

              </h2>

              <p className="text-slate-500">

                Kelola informasi SKU,
                variasi produk, harga,
                dan inventory.

              </p>

            </div>

          </div>

          <form
            onSubmit={handleSubmit}
            className="space-y-8"
          >
          
                      {/* Product & SKU */}
            <div>

              <h3
                className="
                  mb-5
                  text-lg
                  font-semibold
                  text-slate-800
                "
              >

                Informasi SKU

              </h3>

              <div
                className="
                  grid
                  gap-5
                  md:grid-cols-3
                "
              >

                {/* Product */}
                <div>

                  <label
                    className="
                      mb-2
                      block
                      text-sm
                      font-medium
                      text-slate-700
                    "
                  >

                    Product

                  </label>

                  <select
                    value={productId}
                    disabled={
                      submitting ||
                      editingId !== null
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
                    "
                  >

                    <option value="">

                      Pilih Produk

                    </option>

                    {products.map((item) => (

                      <option
                        key={item.id}
                        value={item.id}
                      >

                        {item.name}

                      </option>

                    ))}

                  </select>

                </div>

                {/* SKU */}
                <div>

                  <label
                    className="
                      mb-2
                      block
                      text-sm
                      font-medium
                      text-slate-700
                    "
                  >

                    SKU

                  </label>

                  <input
                    type="text"
                    value={sku}
                    disabled={submitting}
                    onChange={(e) =>
                      setSku(
                        e.target.value.toUpperCase()
                      )
                    }
                    placeholder="SKU-001"
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

                {/* Barcode */}
                <div>

                  <label
                    className="
                      mb-2
                      block
                      text-sm
                      font-medium
                      text-slate-700
                    "
                  >

                    Barcode

                  </label>

                  <input
                    type="text"
                    value={barcode}
                    disabled={submitting}
                    onChange={(e) =>
                      setBarcode(
                        e.target.value
                      )
                    }
                    placeholder="Optional"
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

            </div>

            {/* Pricing */}
            <div>

              <h3
                className="
                  mb-5
                  text-lg
                  font-semibold
                  text-slate-800
                "
              >

                Pricing

              </h3>

              <div
                className="
                  grid
                  gap-5
                  md:grid-cols-3
                "
              >

                <div>

                  <label className="mb-2 block text-sm font-medium text-slate-700">

                    Harga Jual

                  </label>

                  <input
                    type="number"
                    min="0"
                    value={price}
                    disabled={submitting}
                    onChange={(e) =>
                      setPrice(
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

                <div>

                  <label className="mb-2 block text-sm font-medium text-slate-700">

                    Compare Price

                  </label>

                  <input
                    type="number"
                    min="0"
                    value={compareAtPrice}
                    disabled={submitting}
                    onChange={(e) =>
                      setCompareAtPrice(
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

                <div>

                  <label className="mb-2 block text-sm font-medium text-slate-700">

                    Cost Price

                  </label>

                  <input
                    type="number"
                    min="0"
                    value={costPrice}
                    disabled={submitting}
                    onChange={(e) =>
                      setCostPrice(
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

              </div>

            </div>

            {/* Dimensions */}
            <div>

              <h3
                className="
                  mb-5
                  text-lg
                  font-semibold
                  text-slate-800
                "
              >

                Dimensions

              </h3>

              <div
                className="
                  grid
                  gap-5
                  md:grid-cols-4
                "
              >

                {[
                  {
                    label: "Weight (kg)",
                    value: weight,
                    setter: setWeight,
                  },
                  {
                    label: "Length",
                    value: length,
                    setter: setLength,
                  },
                  {
                    label: "Width",
                    value: width,
                    setter: setWidth,
                  },
                  {
                    label: "Height",
                    value: height,
                    setter: setHeight,
                  },
                ].map((field) => (

                  <div key={field.label}>

                    <label className="mb-2 block text-sm font-medium text-slate-700">

                      {field.label}

                    </label>

                    <input
                      type="number"
                      min="0"
                      step="0.01"
                      value={field.value}
                      disabled={submitting}
                      onChange={(e) =>
                        field.setter(
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

                ))}

              </div>

            </div>

            {/* Purchase Rules */}
            <div>

              <h3
                className="
                  mb-5
                  text-lg
                  font-semibold
                  text-slate-800
                "
              >

                Purchase Rules

              </h3>

              <div
                className="
                  grid
                  gap-5
                  md:grid-cols-2
                "
              >

                <div>

                  <label className="mb-2 block text-sm font-medium text-slate-700">

                    Minimum Order Quantity

                  </label>

                  <input
                    type="number"
                    min="1"
                    value={minimumOrderQuantity}
                    disabled={submitting}
                    onChange={(e) =>
                      setMinimumOrderQuantity(
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

                <div>

                  <label className="mb-2 block text-sm font-medium text-slate-700">

                    Maximum Order Quantity

                  </label>

                  <input
                    type="number"
                    min="1"
                    value={maximumOrderQuantity}
                    disabled={submitting}
                    onChange={(e) =>
                      setMaximumOrderQuantity(
                        e.target.value
                      )
                    }
                    placeholder="Optional"
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

            </div>

            {/* Product Variations */}
            <div>

              <h3
                className="
                  mb-5
                  text-lg
                  font-semibold
                  text-slate-800
                "
              >

                Product Variations

              </h3>

              <div
                className="
                  rounded-2xl
                  border
                  border-slate-200
                  p-5
                "
              >

                {Object.entries(
                  groupedOptionValues
                ).map(([group, values]) => (

                  <div
                    key={group}
                    className="mb-6 last:mb-0"
                  >

                    <h4
                      className="
                        mb-3
                        font-semibold
                        text-slate-700
                      "
                    >

                      {group}

                    </h4>

                    <div
                      className="
                        grid
                        gap-3
                        md:grid-cols-2
                        lg:grid-cols-3
                      "
                    >

                      {values.map((item) => {

                        const checked =
                          selectedOptionValues.includes(
                            item.id
                          );

                        return (

                          <label
                            key={item.id}
                            className={`
                              flex
                              cursor-pointer
                              items-center
                              gap-3
                              rounded-2xl
                              border
                              px-4
                              py-3
                              transition
                              ${
                                checked
                                  ? "border-red-500 bg-red-50"
                                  : "border-slate-200 hover:bg-slate-50"
                              }
                            `}
                          >

                            <input
                              type="checkbox"
                              checked={checked}
                              disabled={submitting}
                              onChange={() =>
                                handleOptionValueChange(
                                  item.id
                                )
                              }
                              className="
                                h-5
                                w-5
                                accent-red-600
                              "
                            />

                            <span>

                              {item.value}

                            </span>

                          </label>

                        );

                      })}

                    </div>

                  </div>

                ))}

                {selectedOptionLabels.length > 0 && (

                  <div className="mt-5">

                    <p className="mb-2 text-sm text-slate-500">

                      Variasi terpilih:

                    </p>

                    <div className="flex flex-wrap gap-2">

                      {selectedOptionLabels.map(
                        (label) => (

                          <span
                            key={label}
                            className="
                              rounded-full
                              bg-red-50
                              px-3
                              py-1
                              text-sm
                              text-red-700
                            "
                          >

                            {label}

                          </span>

                        )
                      )}

                    </div>

                  </div>

                )}

              </div>

            </div>
                        {/* Inventory */}
            <div>

              <h3
                className="
                  mb-5
                  text-lg
                  font-semibold
                  text-slate-800
                "
              >

                Inventory

              </h3>

              <div
                className="
                  grid
                  gap-5
                  md:grid-cols-3
                "
              >

                <div>

                  <label
                    className="
                      mb-2
                      block
                      text-sm
                      font-medium
                      text-slate-700
                    "
                  >

                    Current Stock

                  </label>

                  <input
                    type="number"
                    min="0"
                    value={currentStock}
                    disabled={submitting}
                    onChange={(e) =>
                      setCurrentStock(
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

                <div>

                  <label
                    className="
                      mb-2
                      block
                      text-sm
                      font-medium
                      text-slate-700
                    "
                  >

                    Minimum Stock

                  </label>

                  <input
                    type="number"
                    min="0"
                    value={minimumStock}
                    disabled={submitting}
                    onChange={(e) =>
                      setMinimumStock(
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

                <div>

                  <label
                    className="
                      mb-2
                      block
                      text-sm
                      font-medium
                      text-slate-700
                    "
                  >

                    Reorder Point

                  </label>

                  <input
                    type="number"
                    min="0"
                    value={reorderPoint}
                    disabled={submitting}
                    onChange={(e) =>
                      setReorderPoint(
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

              <div
                className="
                  mt-5
                  rounded-2xl
                  border
                  border-slate-200
                  px-5
                  py-4
                "
              >

                <div
                  className="
                    flex
                    items-center
                    justify-between
                  "
                >

                  <div>

                    <h4
                      className="
                        font-semibold
                        text-slate-800
                      "
                    >

                      Allow Backorder

                    </h4>

                    <p
                      className="
                        text-sm
                        text-slate-500
                      "
                    >

                      Izinkan pembelian
                      meskipun stok habis.

                    </p>

                  </div>

                  <input
                    type="checkbox"
                    checked={allowBackorder}
                    disabled={submitting}
                    onChange={(e) =>
                      setAllowBackorder(
                        e.target.checked
                      )
                    }
                    className="
                      h-5
                      w-5
                      accent-red-600
                    "
                  />

                </div>

              </div>

            </div>

            {/* Status */}
            <div>

              <h3
                className="
                  mb-5
                  text-lg
                  font-semibold
                  text-slate-800
                "
              >

                Status SKU

              </h3>

              <div
                className="
                  grid
                  gap-5
                  md:grid-cols-3
                "
              >

                <div>

                  <label
                    className="
                      mb-2
                      block
                      text-sm
                      font-medium
                      text-slate-700
                    "
                  >

                    Status

                  </label>

                  <select
                    value={status}
                    disabled={submitting}
                    onChange={(e) =>
                      setStatus(
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

                </div>

                <div
                  className="
                    flex
                    items-center
                    gap-3
                    rounded-2xl
                    border
                    border-slate-200
                    px-5
                    py-4
                  "
                >

                  <input
                    type="checkbox"
                    checked={isActive}
                    disabled={submitting}
                    onChange={(e) =>
                      setIsActive(
                        e.target.checked
                      )
                    }
                    className="
                      h-5
                      w-5
                      accent-green-600
                    "
                  />

                  <div>

                    <p
                      className="
                        font-semibold
                        text-slate-800
                      "
                    >

                      Active

                    </p>

                    <p
                      className="
                        text-sm
                        text-slate-500
                      "
                    >

                      SKU dapat digunakan.

                    </p>

                  </div>

                </div>

                <div
                  className="
                    flex
                    items-center
                    gap-3
                    rounded-2xl
                    border
                    border-slate-200
                    px-5
                    py-4
                  "
                >

                  <input
                    type="checkbox"
                    checked={isDefault}
                    disabled={submitting}
                    onChange={(e) =>
                      setIsDefault(
                        e.target.checked
                      )
                    }
                    className="
                      h-5
                      w-5
                      accent-blue-600
                    "
                  />

                  <div>

                    <p
                      className="
                        font-semibold
                        text-slate-800
                      "
                    >

                      Default SKU

                    </p>

                    <p
                      className="
                        text-sm
                        text-slate-500
                      "
                    >

                      SKU utama produk.

                    </p>

                  </div>

                </div>

              </div>

            </div>

            {/* Actions */}
            <div
              className="
                flex
                flex-col
                gap-3
                pt-4
                sm:flex-row
              "
            >

              {canSubmit && (

                <button
                  type="submit"
                  disabled={submitting}
                  className="
                    inline-flex
                    flex-1
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
                    transition
                    hover:shadow-xl
                    disabled:opacity-70
                  "
                >

                  <Save className="h-5 w-5" />

                  {submitting
                    ? "Saving..."
                    : editingId
                    ? "Update SKU"
                    : "Create SKU"}

                </button>

              )}

              {editingId && (

                <button
                  type="button"
                  onClick={resetForm}
                  disabled={submitting}
                  className="
                    rounded-2xl
                    border
                    border-slate-200
                    px-6
                    py-3
                    font-medium
                    text-slate-700
                    transition
                    hover:bg-slate-50
                  "
                >

                  Cancel

                </button>

              )}

            </div>

          </form>

        </div>

      )}
            {/* SKU Table */}
      <div
        className="
          overflow-hidden
          rounded-3xl
          border
          border-slate-100
          bg-white
          shadow-sm
        "
      >

        <div
          className="
            border-b
            border-slate-100
            px-6
            py-5
          "
        >

          <h2
            className="
              text-xl
              font-bold
              text-slate-900
            "
          >

            Product SKU List

          </h2>

          <p
            className="
              mt-1
              text-sm
              text-slate-500
            "
          >

            Menampilkan {skus.length} SKU.

          </p>

        </div>

        <div className="overflow-x-auto">

          <table className="w-full">

            <thead className="bg-slate-50">

              <tr>

                <th className="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                  SKU
                </th>

                <th className="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                  Product
                </th>

                <th className="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                  Variations
                </th>

                <th className="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                  Price
                </th>

                <th className="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                  Stock
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
                        ? 7
                        : 6
                    }
                    className="
                      py-16
                      text-center
                      text-slate-500
                    "
                  >

                    Loading SKU...

                  </td>

                </tr>

              ) : skus.length === 0 ? (

                <tr>

                  <td
                    colSpan={
                      canUpdate || canDelete
                        ? 7
                        : 6
                    }
                    className="py-16 text-center"
                  >

                    Tidak ada data SKU.

                  </td>

                </tr>

              ) : (

                skus.map((item) => {

                  const stockBadge =
                    getStockBadge(item);

                  return (

                    <tr
                      key={item.id}
                      className="
                        border-t
                        border-slate-100
                        transition
                        hover:bg-slate-50
                      "
                    >

                      {/* SKU */}
                      <td className="px-6 py-5">

                        <div>

                          <p className="font-semibold">

                            {item.sku}

                          </p>

                          <p
                            className="
                              text-sm
                              text-slate-500
                            "
                          >

                            {item.barcode || "-"}

                          </p>

                        </div>

                      </td>

                      {/* Product */}
                      <td className="px-6 py-5">

                        {item.product?.name || "-"}

                      </td>

                      {/* Variations */}
                      <td className="px-6 py-5">

                        <div
                          className="
                            flex
                            flex-wrap
                            gap-2
                          "
                        >

                          {item.option_values?.length ? (

                            item.option_values.map(
                              (value) => (

                                <span
                                  key={value.id}
                                  className="
                                    rounded-full
                                    bg-slate-100
                                    px-3
                                    py-1
                                    text-xs
                                  "
                                >

                                  {value.option?.name}
                                  {" : "}
                                  {value.value}

                                </span>

                              )
                            )

                          ) : (

                            "-"

                          )}

                        </div>

                      </td>

                      {/* Price */}
                      <td className="px-6 py-5">

                        Rp{" "}

                        {Number(
                          item.price || 0
                        ).toLocaleString(
                          "id-ID"
                        )}

                      </td>

                      {/* Stock */}
                      <td className="px-6 py-5">

                        <div>

                          <p
                            className="
                              font-semibold
                            "
                          >

                            {item.current_stock ?? 0}

                          </p>

                          <span
                            className={`
                              inline-flex
                              rounded-full
                              px-3
                              py-1
                              text-xs
                              font-medium
                              ${stockBadge.className}
                            `}
                          >

                            {stockBadge.label}

                          </span>

                        </div>

                      </td>

                      {/* Status */}
                      <td className="px-6 py-5">

                        <div
                          className="
                            flex
                            flex-col
                            gap-2
                          "
                        >

                          <span
                            className="
                              inline-flex
                              w-fit
                              rounded-full
                              bg-slate-100
                              px-3
                              py-1
                              text-xs
                            "
                          >

                            {item.status}

                          </span>

                          {item.is_default && (

                            <span
                              className="
                                inline-flex
                                w-fit
                                rounded-full
                                bg-yellow-100
                                px-3
                                py-1
                                text-xs
                                text-yellow-700
                              "
                            >

                              Default

                            </span>

                          )}

                        </div>

                      </td>
                                            {/* Actions */}
                      {(canUpdate || canDelete) && (

                        <td className="px-6 py-5">

                          <div
                            className="
                              flex
                              flex-wrap
                              justify-end
                              gap-2
                            "
                          >

                            {/* Edit */}
                            {canUpdate && (

                              <button
                                type="button"
                                onClick={() =>
                                  handleEdit(item)
                                }
                                className="
                                  inline-flex
                                  items-center
                                  gap-2
                                  rounded-xl
                                  bg-blue-50
                                  px-4
                                  py-2
                                  text-blue-700
                                  transition
                                  hover:bg-blue-100
                                "
                              >

                                <Pencil className="h-4 w-4" />

                                Edit

                              </button>

                            )}

                            {/* Activate */}
                            {canUpdate &&
                              !item.is_active && (

                              <button
                                type="button"
                                onClick={() =>
                                  handleActivate(item)
                                }
                                className="
                                  inline-flex
                                  items-center
                                  gap-2
                                  rounded-xl
                                  bg-green-50
                                  px-4
                                  py-2
                                  text-green-700
                                  transition
                                  hover:bg-green-100
                                "
                              >

                                <CheckCircle className="h-4 w-4" />

                                Activate

                              </button>

                            )}

                            {/* Deactivate */}
                            {canUpdate &&
                              item.is_active &&
                              !item.is_default && (

                              <button
                                type="button"
                                onClick={() =>
                                  handleDeactivate(item)
                                }
                                className="
                                  inline-flex
                                  items-center
                                  gap-2
                                  rounded-xl
                                  bg-orange-50
                                  px-4
                                  py-2
                                  text-orange-700
                                  transition
                                  hover:bg-orange-100
                                "
                              >

                                <XCircle className="h-4 w-4" />

                                Deactivate

                              </button>

                            )}

                            {/* Publish */}
                            {canUpdate &&
                              item.status === "draft" && (

                              <button
                                type="button"
                                onClick={() =>
                                  handlePublish(item)
                                }
                                className="
                                  inline-flex
                                  items-center
                                  gap-2
                                  rounded-xl
                                  bg-emerald-50
                                  px-4
                                  py-2
                                  text-emerald-700
                                  transition
                                  hover:bg-emerald-100
                                "
                              >

                                <Upload className="h-4 w-4" />

                                Publish

                              </button>

                            )}

                            {/* Archive */}
                            {canUpdate &&
                              item.status !== "archived" &&
                              !item.is_default && (

                              <button
                                type="button"
                                onClick={() =>
                                  handleArchive(item)
                                }
                                className="
                                  inline-flex
                                  items-center
                                  gap-2
                                  rounded-xl
                                  bg-slate-100
                                  px-4
                                  py-2
                                  text-slate-700
                                  transition
                                  hover:bg-slate-200
                                "
                              >

                                <Archive className="h-4 w-4" />

                                Archive

                              </button>

                            )}

                            {/* Set Default */}
                            {canUpdate &&
                              !item.is_default && (

                              <button
                                type="button"
                                onClick={() =>
                                  handleSetDefault(item)
                                }
                                className="
                                  inline-flex
                                  items-center
                                  gap-2
                                  rounded-xl
                                  bg-indigo-50
                                  px-4
                                  py-2
                                  text-indigo-700
                                  transition
                                  hover:bg-indigo-100
                                "
                              >

                                <Star className="h-4 w-4" />

                                Default

                              </button>

                            )}

                            {/* Delete */}
                            {canDelete &&
                              !item.is_default && (

                              <button
                                type="button"
                                onClick={() =>
                                  handleDelete(item)
                                }
                                className="
                                  inline-flex
                                  items-center
                                  gap-2
                                  rounded-xl
                                  bg-red-50
                                  px-4
                                  py-2
                                  text-red-700
                                  transition
                                  hover:bg-red-100
                                "
                              >

                                <Trash2 className="h-4 w-4" />

                                Delete

                              </button>

                            )}

                          </div>

                        </td>

                      )}

                    </tr>

                  );

                })

              )}

            </tbody>

          </table>

        </div>

        {/* Pagination */}
        {meta?.last_page > 1 && (

          <div
            className="
              flex
              items-center
              justify-between
              border-t
              border-slate-100
              px-6
              py-5
            "
          >

            <div
              className="
                text-sm
                text-slate-500
              "
            >

              Menampilkan

              {" "}

              {meta.from || 0}

              -

              {meta.to || 0}

              {" "}

              dari

              {" "}

              {meta.total || 0}

              {" "}

              data

            </div>

            <div className="flex gap-2">

              <button
                type="button"
                disabled={
                  page <= 1
                }
                onClick={() =>
                  setPage(
                    page - 1
                  )
                }
                className="
                  rounded-xl
                  border
                  border-slate-200
                  px-4
                  py-2
                  disabled:opacity-50
                "
              >

                Previous

              </button>

              <button
                type="button"
                disabled={
                  page >= meta.last_page
                }
                onClick={() =>
                  setPage(
                    page + 1
                  )
                }
                className="
                  rounded-xl
                  border
                  border-slate-200
                  px-4
                  py-2
                  disabled:opacity-50
                "
              >

                Next

              </button>

            </div>

          </div>

        )}

      </div>

    </div>

  );

}