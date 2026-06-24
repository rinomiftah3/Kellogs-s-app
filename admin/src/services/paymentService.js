import api from "../api/axios";

/*
|--------------------------------------------------------------------------
| Payment Status
|--------------------------------------------------------------------------
*/

export const PAYMENT_STATUS = {
  PENDING: "pending",
  PAID: "paid",
  FAILED: "failed",
  EXPIRED: "expired",
  CANCELLED: "cancelled",
  REFUNDED: "refunded",
  PARTIAL_REFUND: "partial_refund",
};

/*
|--------------------------------------------------------------------------
| Get Payments
|--------------------------------------------------------------------------
|
| Filters:
| - search
| - status
| - gateway
| - page
| - per_page
|
*/

export const getPayments = async (params = {}) => {
  const response = await api.get("/payments", {
    params,
  });

  return response.data;
};

/*
|--------------------------------------------------------------------------
| Get Payment Detail
|--------------------------------------------------------------------------
|
| Route model binding menggunakan payment_number
|
| Contoh:
| getPayment("PAY-20260614123456-ABC123")
|
*/

export const getPayment = async (paymentNumber) => {
  const response = await api.get(
    `/payments/${paymentNumber}`
  );

  return response.data;
};

/*
|--------------------------------------------------------------------------
| Create Payment
|--------------------------------------------------------------------------
|
| Payload:
| {
|   order_id,
|   gateway,
|   method,
|   amount,
|   gateway_transaction_id?,
|   gateway_order_id?,
|   payment_url?,
|   expired_at?,
|   metadata?,
|   notes?,
| }
|
*/

export const createPayment = async (payload) => {
  const response = await api.post(
    "/payments",
    payload
  );

  return response.data;
};

/*
|--------------------------------------------------------------------------
| Update Payment Status
|--------------------------------------------------------------------------
|
| Payload:
| {
|   status,
|   paid_amount?,
|   refund_amount?,
|   gateway_transaction_id?,
|   notes?,
|   metadata?,
| }
|
*/

export const updatePaymentStatus = async (
  paymentNumber,
  payload
) => {
  const response = await api.patch(
    `/payments/${paymentNumber}/status`,
    payload
  );

  return response.data;
};

/*
|--------------------------------------------------------------------------
| Refund Payment
|--------------------------------------------------------------------------
|
| Payload:
| {
|   amount,
|   notes?,
| }
|
*/

export const refundPayment = async (
  paymentNumber,
  payload
) => {
  const response = await api.post(
    `/payments/${paymentNumber}/refund`,
    payload
  );

  return response.data;
};

/*
|--------------------------------------------------------------------------
| Delete Payment
|--------------------------------------------------------------------------
*/

export const deletePayment = async (
  paymentNumber
) => {
  const response = await api.delete(
    `/payments/${paymentNumber}`
  );

  return response.data;
};

/*
|--------------------------------------------------------------------------
| Helper Actions
|--------------------------------------------------------------------------
*/

export const markAsPaid = async (
  paymentNumber,
  {
    paid_amount,
    gateway_transaction_id,
    notes = "",
    metadata = null,
  }
) => {
  return updatePaymentStatus(
    paymentNumber,
    {
      status: PAYMENT_STATUS.PAID,
      paid_amount,
      gateway_transaction_id,
      notes,
      metadata,
    }
  );
};

export const markAsFailed = async (
  paymentNumber,
  notes = ""
) => {
  return updatePaymentStatus(
    paymentNumber,
    {
      status: PAYMENT_STATUS.FAILED,
      notes,
    }
  );
};

export const markAsExpired = async (
  paymentNumber,
  notes = ""
) => {
  return updatePaymentStatus(
    paymentNumber,
    {
      status: PAYMENT_STATUS.EXPIRED,
      notes,
    }
  );
};

export const markAsCancelled = async (
  paymentNumber,
  notes = ""
) => {
  return updatePaymentStatus(
    paymentNumber,
    {
      status: PAYMENT_STATUS.CANCELLED,
      notes,
    }
  );
};

export const markAsRefunded = async (
  paymentNumber,
  {
    refund_amount,
    notes = "",
  }
) => {
  return updatePaymentStatus(
    paymentNumber,
    {
      status: PAYMENT_STATUS.REFUNDED,
      refund_amount,
      notes,
    }
  );
};

export const markAsPartialRefund = async (
  paymentNumber,
  {
    refund_amount,
    notes = "",
  }
) => {
  return updatePaymentStatus(
    paymentNumber,
    {
      status:
        PAYMENT_STATUS.PARTIAL_REFUND,

      refund_amount,
      notes,
    }
  );
};

/*
|--------------------------------------------------------------------------
| Status Helpers
|--------------------------------------------------------------------------
*/

export const isPending = (payment) =>
  payment?.status ===
  PAYMENT_STATUS.PENDING;

export const isPaid = (payment) =>
  payment?.status ===
  PAYMENT_STATUS.PAID;

export const isFailed = (payment) =>
  payment?.status ===
  PAYMENT_STATUS.FAILED;

export const isExpired = (payment) =>
  payment?.status ===
  PAYMENT_STATUS.EXPIRED;

export const isCancelled = (payment) =>
  payment?.status ===
  PAYMENT_STATUS.CANCELLED;

export const isRefunded = (payment) =>
  payment?.status ===
  PAYMENT_STATUS.REFUNDED;

export const isPartialRefund = (
  payment
) =>
  payment?.status ===
  PAYMENT_STATUS.PARTIAL_REFUND;

/*
|--------------------------------------------------------------------------
| Badge Helpers
|--------------------------------------------------------------------------
*/

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

    case PAYMENT_STATUS.EXPIRED:
      return "orange";

    case PAYMENT_STATUS.CANCELLED:
      return "gray";

    case PAYMENT_STATUS.REFUNDED:
      return "blue";

    case PAYMENT_STATUS.PARTIAL_REFUND:
      return "indigo";

    default:
      return "slate";
  }
};

/*
|--------------------------------------------------------------------------
| Export Default
|--------------------------------------------------------------------------
*/

export default {
  getPayments,
  getPayment,
  createPayment,

  updatePaymentStatus,
  refundPayment,
  deletePayment,

  markAsPaid,
  markAsFailed,
  markAsExpired,
  markAsCancelled,
  markAsRefunded,
  markAsPartialRefund,

  PAYMENT_STATUS,

  isPending,
  isPaid,
  isFailed,
  isExpired,
  isCancelled,
  isRefunded,
  isPartialRefund,

  getPaymentStatusColor,
};