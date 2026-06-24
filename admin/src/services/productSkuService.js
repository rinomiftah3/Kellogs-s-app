import api from "../api/axios";

/*
|--------------------------------------------------------------------------
| Get Product SKUs
|--------------------------------------------------------------------------
|
| Supported Query Params:
| - product_id
| - search
| - status
| - is_active
| - is_default
| - per_page
|
*/

export const getProductSkus = async (
  params = {}
) => {
  const response = await api.get(
    "/product-skus",
    {
      params,
    }
  );

  return {
    success:
      response.data.success,

    message:
      response.data.message,

    data:
      response.data.data || [],

    meta:
      response.data.meta || {},
  };
};

/*
|--------------------------------------------------------------------------
| Get Product SKU Detail
|--------------------------------------------------------------------------
*/

export const getProductSku = async (
  id
) => {
  const response = await api.get(
    `/product-skus/${id}`
  );

  return response.data.data;
};

/*
|--------------------------------------------------------------------------
| Create Product SKU
|--------------------------------------------------------------------------
|
| Payload:
| {
|   product_id,
|   sku,
|   barcode,
|   price,
|   compare_at_price,
|   cost_price,
|   weight,
|   length,
|   width,
|   height,
|   minimum_order_quantity,
|   maximum_order_quantity,
|   option_value_ids,
|   is_default,
|   status,
|   is_active,
|   published_at,
|
|   stock,
|   minimum_stock,
|   maximum_stock,
|   reorder_point,
|   allow_backorder,
| }
|
*/

export const createProductSku =
  async (data) => {
    const response =
      await api.post(
        "/product-skus",
        data
      );

    return response.data.data;
  };

/*
|--------------------------------------------------------------------------
| Update Product SKU
|--------------------------------------------------------------------------
*/

export const updateProductSku =
  async (
    id,
    data
  ) => {
    const response =
      await api.put(
        `/product-skus/${id}`,
        data
      );

    return response.data.data;
  };

/*
|--------------------------------------------------------------------------
| Delete Product SKU
|--------------------------------------------------------------------------
*/

export const deleteProductSku =
  async (id) => {
    const response =
      await api.delete(
        `/product-skus/${id}`
      );

    return {
      success:
        response.data.success,

      message:
        response.data.message,
    };
  };

/*
|--------------------------------------------------------------------------
| Activate Product SKU
|--------------------------------------------------------------------------
*/

export const activateProductSku =
  async (id) => {
    const response =
      await api.patch(
        `/product-skus/${id}/activate`
      );

    return response.data.data;
  };

/*
|--------------------------------------------------------------------------
| Deactivate Product SKU
|--------------------------------------------------------------------------
*/

export const deactivateProductSku =
  async (id) => {
    const response =
      await api.patch(
        `/product-skus/${id}/deactivate`
      );

    return response.data.data;
  };

/*
|--------------------------------------------------------------------------
| Publish Product SKU
|--------------------------------------------------------------------------
*/

export const publishProductSku =
  async (id) => {
    const response =
      await api.patch(
        `/product-skus/${id}/publish`
      );

    return response.data.data;
  };

/*
|--------------------------------------------------------------------------
| Archive Product SKU
|--------------------------------------------------------------------------
*/

export const archiveProductSku =
  async (id) => {
    const response =
      await api.patch(
        `/product-skus/${id}/archive`
      );

    return response.data.data;
  };

/*
|--------------------------------------------------------------------------
| Set Default Product SKU
|--------------------------------------------------------------------------
*/

export const setDefaultProductSku =
  async (id) => {
    const response =
      await api.patch(
        `/product-skus/${id}/default`
      );

    return response.data.data;
  };

/*
|--------------------------------------------------------------------------
| SKU Inventory Helpers
|--------------------------------------------------------------------------
*/

export const getInventoryFromSku =
  (sku) => {
    return sku?.inventory ?? null;
  };

export const getInventoryIdFromSku =
  (sku) => {
    return sku?.inventory?.id ?? null;
  };

export const getStockFromSku =
  (sku) => {
    return Number(
      sku?.stock ?? 0
    );
  };

export const isSkuLowStock =
  (sku) => {
    return Boolean(
      sku?.is_low_stock
    );
  };

export const isSkuOutOfStock =
  (sku) => {
    return Boolean(
      sku?.is_out_of_stock
    );
  };

export const isSkuDefault =
  (sku) => {
    return Boolean(
      sku?.is_default
    );
  };

export const isSkuPublished =
  (sku) => {
    return Boolean(
      sku?.is_published
    );
  };

export const isSkuArchived =
  (sku) => {
    return (
      sku?.status ===
      "archived"
    );
  };

export const canPurchaseSku =
  (sku) => {
    return Boolean(
      sku?.can_be_purchased
    );
  };

export const canDeleteSku =
  (sku) => {
    return Boolean(
      sku?.can_be_deleted
    );
  };

/*
|--------------------------------------------------------------------------
| SKU Variation Helpers
|--------------------------------------------------------------------------
*/

export const getVariationLabelFromSku =
  (sku) => {
    if (
      sku?.variation_label
    ) {
      return sku.variation_label;
    }

    if (
      Array.isArray(
        sku?.option_values
      )
    ) {
      return sku.option_values
        .map(
          (value) =>
            value?.option?.name
              ? `${value.option.name}: ${value.value}`
              : value?.value
        )
        .filter(Boolean)
        .join(", ");
    }

    return "-";
  };