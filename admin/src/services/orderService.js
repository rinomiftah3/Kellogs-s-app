import api from "../api/axios";

/*
|--------------------------------------------------------------------------
| Order Status
|--------------------------------------------------------------------------
*/

export const ORDER_STATUS = {
  PENDING: "pending",
  CONFIRMED: "confirmed",
  PROCESSING: "processing",
  SHIPPED: "shipped",
  COMPLETED: "completed",
  CANCELLED: "cancelled",
};

/*
|--------------------------------------------------------------------------
| Payment Status
|--------------------------------------------------------------------------
*/

export const PAYMENT_STATUS = {
  PENDING: "pending",
  PAID: "paid",
  FAILED: "failed",
  REFUNDED: "refunded",
};

/*
|--------------------------------------------------------------------------
| Fulfillment Status
|--------------------------------------------------------------------------
*/

export const FULFILLMENT_STATUS = {
  PENDING: "pending",
  PACKED: "packed",
  SHIPPED: "shipped",
  DELIVERED: "delivered",
};

/*
|--------------------------------------------------------------------------
| Get Orders
|--------------------------------------------------------------------------
|
| Filters:
| - search
| - status
| - payment_status
| - fulfillment_status
| - customer_profile_id
| - page
| - per_page
|
*/

export const getOrders = async (params = {}) => {
  const response = await api.get("/orders", {
    params,
  });

  return response.data;
};

/*
|--------------------------------------------------------------------------
| Get Order Detail
|--------------------------------------------------------------------------
|
| Route model binding menggunakan order_number
|
| Contoh:
| getOrder("ORD-20260614-ABC123")
|
*/

export const getOrder = async (orderNumber) => {
  const response = await api.get(
    `/orders/${orderNumber}`
  );

  return response.data;
};

/*
|--------------------------------------------------------------------------
| Update Order Status
|--------------------------------------------------------------------------
|
| Payload:
| {
|   status,
|   fulfillment_status?,
|   admin_notes?,
| }
|
*/

export const updateOrderStatus = async (
  orderNumber,
  payload
) => {
  const response = await api.patch(
    `/orders/${orderNumber}/status`,
    payload
  );

  return response.data;
};

/*
|--------------------------------------------------------------------------
| Cancel Order
|--------------------------------------------------------------------------
|
| Payload:
| {
|   reason
| }
|
*/

export const cancelOrder = async (
  orderNumber,
  reason = ""
) => {
  const response = await api.patch(
    `/orders/${orderNumber}/cancel`,
    {
      reason,
    }
  );

  return response.data;
};

/*
|--------------------------------------------------------------------------
| Helper Actions
|--------------------------------------------------------------------------
*/

export const confirmOrder = async (
  orderNumber,
  adminNotes = ""
) => {
  return updateOrderStatus(
    orderNumber,
    {
      status:
        ORDER_STATUS.CONFIRMED,

      admin_notes:
        adminNotes,
    }
  );
};

export const processOrder = async (
  orderNumber,
  adminNotes = ""
) => {
  return updateOrderStatus(
    orderNumber,
    {
      status:
        ORDER_STATUS.PROCESSING,

      fulfillment_status:
        FULFILLMENT_STATUS.PACKED,

      admin_notes:
        adminNotes,
    }
  );
};

export const shipOrder = async (
  orderNumber,
  adminNotes = ""
) => {
  return updateOrderStatus(
    orderNumber,
    {
      status:
        ORDER_STATUS.SHIPPED,

      fulfillment_status:
        FULFILLMENT_STATUS.SHIPPED,

      admin_notes:
        adminNotes,
    }
  );
};

export const completeOrder = async (
  orderNumber,
  adminNotes = ""
) => {
  return updateOrderStatus(
    orderNumber,
    {
      status:
        ORDER_STATUS.COMPLETED,

      fulfillment_status:
        FULFILLMENT_STATUS.DELIVERED,

      admin_notes:
        adminNotes,
    }
  );
};

/*
|--------------------------------------------------------------------------
| Status Helpers
|--------------------------------------------------------------------------
*/

export const isPending = (
  order
) =>
  order?.status ===
  ORDER_STATUS.PENDING;

export const isConfirmed = (
  order
) =>
  order?.status ===
  ORDER_STATUS.CONFIRMED;

export const isProcessing = (
  order
) =>
  order?.status ===
  ORDER_STATUS.PROCESSING;

export const isShipped = (
  order
) =>
  order?.status ===
  ORDER_STATUS.SHIPPED;

export const isCompleted = (
  order
) =>
  order?.status ===
  ORDER_STATUS.COMPLETED;

export const isCancelled = (
  order
) =>
  order?.status ===
  ORDER_STATUS.CANCELLED;

export const isPaid = (
  order
) =>
  order?.payment_status ===
  PAYMENT_STATUS.PAID;

/*
|--------------------------------------------------------------------------
| Badge Helpers
|--------------------------------------------------------------------------
*/

export const getOrderStatusColor = (
  status
) => {
  switch (status) {
    case ORDER_STATUS.PENDING:
      return "yellow";

    case ORDER_STATUS.CONFIRMED:
      return "blue";

    case ORDER_STATUS.PROCESSING:
      return "indigo";

    case ORDER_STATUS.SHIPPED:
      return "cyan";

    case ORDER_STATUS.COMPLETED:
      return "green";

    case ORDER_STATUS.CANCELLED:
      return "red";

    default:
      return "gray";
  }
};

export const getPaymentStatusColor = (
  status
) => {
  switch (status) {
    case PAYMENT_STATUS.PAID:
      return "green";

    case PAYMENT_STATUS.PENDING:
      return "yellow";

    case PAYMENT_STATUS.FAILED:
      return "red";

    case PAYMENT_STATUS.REFUNDED:
      return "purple";

    default:
      return "gray";
  }
};

export const getFulfillmentStatusColor = (
  status
) => {
  switch (status) {
    case FULFILLMENT_STATUS.PENDING:
      return "yellow";

    case FULFILLMENT_STATUS.PACKED:
      return "blue";

    case FULFILLMENT_STATUS.SHIPPED:
      return "cyan";

    case FULFILLMENT_STATUS.DELIVERED:
      return "green";

    default:
      return "gray";
  }
};

/*
|--------------------------------------------------------------------------
| Export Default
|--------------------------------------------------------------------------
*/

export default {
  getOrders,
  getOrder,

  updateOrderStatus,
  cancelOrder,

  confirmOrder,
  processOrder,
  shipOrder,
  completeOrder,

  ORDER_STATUS,
  PAYMENT_STATUS,
  FULFILLMENT_STATUS,

  isPending,
  isConfirmed,
  isProcessing,
  isShipped,
  isCompleted,
  isCancelled,
  isPaid,

  getOrderStatusColor,
  getPaymentStatusColor,
  getFulfillmentStatusColor,
};