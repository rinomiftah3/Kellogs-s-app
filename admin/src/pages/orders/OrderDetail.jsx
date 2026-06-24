import {
  useEffect,
  useState,
} from "react";

import {
  useNavigate,
  useParams,
} from "react-router-dom";

import Swal from "sweetalert2";

import {
  ArrowLeftIcon,
} from "@heroicons/react/24/outline";

import {
  getOrder,
  updateOrderStatus,
  cancelOrder,
} from "../../services/orderService";

import OrderStatusBadge from "../../components/orders/OrderStatusBadge";
import PaymentStatusBadge from "../../components/orders/PaymentStatusBadge";
import FulfillmentStatusBadge from "../../components/orders/FulfillmentStatusBadge";

export default function OrderDetail() {

  const navigate =
    useNavigate();

  const { orderNumber } =
    useParams();

  /*
  |--------------------------------------------------------------------------
  | States
  |--------------------------------------------------------------------------
  */

  const [
    order,
    setOrder,
  ] = useState(null);

  const [
    loading,
    setLoading,
  ] = useState(true);

  const [
    processing,
    setProcessing,
  ] = useState(false);

  /*
  |--------------------------------------------------------------------------
  | Fetch Detail
  |--------------------------------------------------------------------------
  */

  useEffect(() => {

    fetchOrder();

    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [orderNumber]);

  const fetchOrder =
    async () => {

      try {

        setLoading(true);

        const response =
          await getOrder(orderNumber);

        setOrder(
          response.data
        );

      } catch (error) {

        console.error(error);

        Swal.fire({

          icon: "error",

          title: "Gagal",

          text:
            error?.response?.data?.message
            ||
            "Gagal memuat detail order.",
        });

        navigate("/orders");

      } finally {

        setLoading(false);
      }
    };

  /*
  |--------------------------------------------------------------------------
  | Update Status
  |--------------------------------------------------------------------------
  */

  const handleStatusUpdate =
    async (
      status
    ) => {

      const result =
        await Swal.fire({

          title:
            "Ubah Status?",

          text:
            `Yakin mengubah status menjadi "${status}"?`,

          icon:
            "question",

          showCancelButton:
            true,

          confirmButtonText:
            "Ya",

          cancelButtonText:
            "Batal",
        });

      if (
        !result.isConfirmed
      ) {
        return;
      }

      try {

        setProcessing(true);

        await updateOrderStatus(
          order.order_number,
          {
            status,
          }
        );

        Swal.fire({

          icon: "success",

          title: "Berhasil",

          text:
            "Status berhasil diperbarui.",
        });

        fetchOrder();

      } catch (error) {

        Swal.fire({

          icon: "error",

          title: "Gagal",

          text:
            error?.response?.data?.message
            ||
            "Gagal memperbarui status.",
        });

      } finally {

        setProcessing(false);
      }
    };

  /*
  |--------------------------------------------------------------------------
  | Cancel Order
  |--------------------------------------------------------------------------
  */

  const handleCancel =
    async () => {

      const {
        value: reason,
      } = await Swal.fire({

        title:
          "Batalkan Pesanan",

        input:
          "textarea",

        inputLabel:
          "Alasan pembatalan",

        inputPlaceholder:
          "Masukkan alasan...",

        showCancelButton:
          true,

        confirmButtonText:
          "Batalkan Pesanan",

        cancelButtonText:
          "Tutup",
      });

      if (!reason) {
        return;
      }

      try {

        setProcessing(true);

        await cancelOrder(
          order.order_number,
          {
            reason,
          }
        );

        Swal.fire({

          icon: "success",

          title: "Berhasil",

          text:
            "Pesanan berhasil dibatalkan.",
        });

        fetchOrder();

      } catch (error) {

        Swal.fire({

          icon: "error",

          title: "Gagal",

          text:
            error?.response?.data?.message
            ||
            "Gagal membatalkan pesanan.",
        });

      } finally {

        setProcessing(false);
      }
    };

  /*
  |--------------------------------------------------------------------------
  | Loading
  |--------------------------------------------------------------------------
  */

  if (loading) {

    return (

      <div className="space-y-6">

        <div className="animate-pulse">

          <div className="h-8 w-56 bg-slate-200 rounded mb-4" />

          <div className="h-48 bg-white rounded-3xl" />

        </div>

      </div>
    );
  }

  if (!order) {

    return (

      <div className="bg-white rounded-3xl p-10 text-center">

        <h2 className="text-xl font-bold text-slate-700">

          Order tidak ditemukan

        </h2>

      </div>
    );
  }
  return (

    <div className="space-y-6">

      {/* Header */}
      <div
        className="
          bg-white
          rounded-3xl
          shadow-sm
          border
          border-slate-200
          p-6
        "
      >

        <div
          className="
            flex
            flex-col
            lg:flex-row
            lg:items-center
            lg:justify-between
            gap-5
          "
        >

          <div>

            <button
              onClick={() =>
                navigate("/orders")
              }
              className="
                inline-flex
                items-center
                gap-2
                text-sm
                text-slate-500
                hover:text-slate-700
                mb-4
              "
            >

              <ArrowLeftIcon className="w-4 h-4" />

              Kembali

            </button>

            <h1
              className="
                text-3xl
                font-bold
                text-slate-900
              "
            >
              Order Detail
            </h1>

            <p
              className="
                text-slate-500
                mt-2
              "
            >
              {order.order_number}
            </p>

          </div>

          <div
            className="
              flex
              flex-wrap
              gap-3
            "
          >

            <OrderStatusBadge
              status={order.status}
            />

            <PaymentStatusBadge
              status={order.payment_status}
            />

            <FulfillmentStatusBadge
              status={order.fulfillment_status}
            />

          </div>

        </div>

      </div>

      {/* Customer & Shipping */}
      <div
        className="
          grid
          grid-cols-1
          xl:grid-cols-2
          gap-6
        "
      >

        {/* Customer */}
        <div
          className="
            bg-white
            rounded-3xl
            shadow-sm
            border
            border-slate-200
            p-6
          "
        >

          <h2
            className="
              text-lg
              font-bold
              text-slate-900
              mb-5
            "
          >
            Customer Information
          </h2>

          <div className="space-y-4">

            <div>

              <p
                className="
                  text-sm
                  text-slate-500
                "
              >
                Customer Name
              </p>

              <p
                className="
                  font-semibold
                  text-slate-800
                "
              >
                {order.customer_name}
              </p>

            </div>

            <div>

              <p
                className="
                  text-sm
                  text-slate-500
                "
              >
                Email
              </p>

              <p
                className="
                  font-semibold
                  text-slate-800
                "
              >
                {order.customer_email || "-"}
              </p>

            </div>

            <div>

              <p
                className="
                  text-sm
                  text-slate-500
                "
              >
                Phone
              </p>

              <p
                className="
                  font-semibold
                  text-slate-800
                "
              >
                {order.customer_phone || "-"}
              </p>

            </div>

            <div>

              <p
                className="
                  text-sm
                  text-slate-500
                "
              >
                Customer Code
              </p>

              <p
                className="
                  font-semibold
                  text-slate-800
                "
              >
                {order.customer_profile
                  ?.customer_code || "-"}
              </p>

            </div>

          </div>

        </div>

        {/* Shipping */}
        <div
          className="
            bg-white
            rounded-3xl
            shadow-sm
            border
            border-slate-200
            p-6
          "
        >

          <h2
            className="
              text-lg
              font-bold
              text-slate-900
              mb-5
            "
          >
            Shipping Information
          </h2>

          <div className="space-y-4">

            <div>

              <p
                className="
                  text-sm
                  text-slate-500
                "
              >
                Recipient
              </p>

              <p
                className="
                  font-semibold
                  text-slate-800
                "
              >
                {order.recipient_name}
              </p>

            </div>

            <div>

              <p
                className="
                  text-sm
                  text-slate-500
                "
              >
                Recipient Phone
              </p>

              <p
                className="
                  font-semibold
                  text-slate-800
                "
              >
                {order.recipient_phone || "-"}
              </p>

            </div>

            <div>

              <p
                className="
                  text-sm
                  text-slate-500
                "
              >
                Shipping Address
              </p>

              <p
                className="
                  font-semibold
                  text-slate-800
                  leading-relaxed
                "
              >
                {order.shipping_address}
              </p>

            </div>

            <div
              className="
                grid
                grid-cols-2
                gap-4
              "
            >

              <div>

                <p
                  className="
                    text-sm
                    text-slate-500
                  "
                >
                  Province
                </p>

                <p className="font-medium">
                  {order.province}
                </p>

              </div>

              <div>

                <p
                  className="
                    text-sm
                    text-slate-500
                  "
                >
                  City
                </p>

                <p className="font-medium">
                  {order.city}
                </p>

              </div>

              <div>

                <p
                  className="
                    text-sm
                    text-slate-500
                  "
                >
                  District
                </p>

                <p className="font-medium">
                  {order.district || "-"}
                </p>

              </div>

              <div>

                <p
                  className="
                    text-sm
                    text-slate-500
                  "
                >
                  Postal Code
                </p>

                <p className="font-medium">
                  {order.postal_code}
                </p>

              </div>

            </div>

          </div>

        </div>

      </div>
      {/* Timeline */}
      <div
        className="
          bg-white
          rounded-3xl
          shadow-sm
          border
          border-slate-200
          p-6
        "
      >

        <h2
          className="
            text-lg
            font-bold
            text-slate-900
            mb-6
          "
        >
          Order Timeline
        </h2>

        <div
          className="
            grid
            grid-cols-1
            md:grid-cols-2
            xl:grid-cols-5
            gap-5
          "
        >

          <div>
            <p className="text-sm text-slate-500">
              Ordered At
            </p>

            <p className="font-semibold text-slate-800">
              {order.ordered_at_human || "-"}
            </p>

            <p className="text-xs text-slate-400 mt-1">
              {order.ordered_at || "-"}
            </p>
          </div>

          <div>
            <p className="text-sm text-slate-500">
              Paid At
            </p>

            <p className="font-semibold text-slate-800">
              {order.paid_at_human || "-"}
            </p>

            <p className="text-xs text-slate-400 mt-1">
              {order.paid_at || "-"}
            </p>
          </div>

          <div>
            <p className="text-sm text-slate-500">
              Shipped At
            </p>

            <p className="font-semibold text-slate-800">
              {order.shipped_at_human || "-"}
            </p>

            <p className="text-xs text-slate-400 mt-1">
              {order.shipped_at || "-"}
            </p>
          </div>

          <div>
            <p className="text-sm text-slate-500">
              Completed At
            </p>

            <p className="font-semibold text-slate-800">
              {order.completed_at_human || "-"}
            </p>

            <p className="text-xs text-slate-400 mt-1">
              {order.completed_at || "-"}
            </p>
          </div>

          <div>
            <p className="text-sm text-slate-500">
              Cancelled At
            </p>

            <p className="font-semibold text-slate-800">
              {order.cancelled_at_human || "-"}
            </p>

            <p className="text-xs text-slate-400 mt-1">
              {order.cancelled_at || "-"}
            </p>
          </div>

        </div>

      </div>

      {/* Order Items */}
      <div
        className="
          bg-white
          rounded-3xl
          shadow-sm
          border
          border-slate-200
          overflow-hidden
        "
      >

        <div className="p-6 border-b border-slate-200">

          <h2
            className="
              text-lg
              font-bold
              text-slate-900
            "
          >
            Order Items
          </h2>

          <p className="text-sm text-slate-500 mt-1">
            {order.items?.length || 0} item(s)
          </p>

        </div>

        <div className="overflow-x-auto">

          <table className="min-w-full">

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
                    text-xs
                    font-semibold
                    uppercase
                    tracking-wider
                    text-slate-500
                  "
                >
                  Product
                </th>

                <th
                  className="
                    px-6
                    py-4
                    text-left
                    text-xs
                    font-semibold
                    uppercase
                    tracking-wider
                    text-slate-500
                  "
                >
                  SKU
                </th>

                <th
                  className="
                    px-6
                    py-4
                    text-center
                    text-xs
                    font-semibold
                    uppercase
                    tracking-wider
                    text-slate-500
                  "
                >
                  Qty
                </th>

                <th
                  className="
                    px-6
                    py-4
                    text-right
                    text-xs
                    font-semibold
                    uppercase
                    tracking-wider
                    text-slate-500
                  "
                >
                  Unit Price
                </th>

                <th
                  className="
                    px-6
                    py-4
                    text-right
                    text-xs
                    font-semibold
                    uppercase
                    tracking-wider
                    text-slate-500
                  "
                >
                  Discount
                </th>

                <th
                  className="
                    px-6
                    py-4
                    text-right
                    text-xs
                    font-semibold
                    uppercase
                    tracking-wider
                    text-slate-500
                  "
                >
                  Subtotal
                </th>

              </tr>

            </thead>

            <tbody className="divide-y divide-slate-100">

              {order.items?.map(
                (item) => (

                  <tr
                    key={item.id}
                    className="
                      hover:bg-slate-50
                    "
                  >

                    <td className="px-6 py-5">

                      <div className="flex items-center gap-4">

                        <img
                          src={
                            item.thumbnail_url ||
                            item.thumbnail ||
                            "https://placehold.co/80x80?text=No+Image"
                          }
                          alt={
                            item.product_name
                          }
                          className="
                            w-16
                            h-16
                            rounded-2xl
                            object-cover
                            border
                            border-slate-200
                          "
                        />

                        <div>

                          <p
                            className="
                              font-semibold
                              text-slate-800
                            "
                          >
                            {item.product_name}
                          </p>

                          <p
                            className="
                              text-sm
                              text-slate-500
                              mt-1
                            "
                          >
                            {item.variant_name || "-"}
                          </p>

                        </div>

                      </div>

                    </td>

                    <td className="px-6 py-5">

                      <span
                        className="
                          text-sm
                          font-medium
                          text-slate-700
                        "
                      >
                        {item.sku || "-"}
                      </span>

                    </td>

                    <td
                      className="
                        px-6
                        py-5
                        text-center
                        font-semibold
                      "
                    >
                      {item.quantity}
                    </td>

                    <td
                      className="
                        px-6
                        py-5
                        text-right
                        font-medium
                      "
                    >
                      {item.formatted_unit_price}
                    </td>

                    <td
                      className="
                        px-6
                        py-5
                        text-right
                        text-red-600
                        font-medium
                      "
                    >
                      {item.formatted_discount}
                    </td>

                    <td
                      className="
                        px-6
                        py-5
                        text-right
                        font-bold
                        text-slate-900
                      "
                    >
                      {item.formatted_subtotal}
                    </td>

                  </tr>

                )
              )}

              {(!order.items ||
                order.items.length === 0) && (

                <tr>

                  <td
                    colSpan={6}
                    className="
                      px-6
                      py-10
                      text-center
                      text-slate-500
                    "
                  >
                    Tidak ada item pesanan.
                  </td>

                </tr>

              )}

            </tbody>

          </table>

        </div>

      </div>
      {/* Bottom Section */}
      <div
        className="
          grid
          grid-cols-1
          xl:grid-cols-3
          gap-6
        "
      >

        {/* Payment & Shipment */}
        <div className="xl:col-span-2 space-y-6">

          {/* Payment */}
          <div
            className="
              bg-white
              rounded-3xl
              shadow-sm
              border
              border-slate-200
              p-6
            "
          >

            <h2
              className="
                text-lg
                font-bold
                text-slate-900
                mb-5
              "
            >
              Payment Information
            </h2>

            {order.payment ? (

              <div className="grid md:grid-cols-2 gap-5">

                <div>
                  <p className="text-sm text-slate-500">
                    Method
                  </p>

                  <p className="font-semibold">
                    {order.payment.method || "-"}
                  </p>
                </div>

                <div>
                  <p className="text-sm text-slate-500">
                    Gateway
                  </p>

                  <p className="font-semibold">
                    {order.payment.gateway || "-"}
                  </p>
                </div>

                <div>
                  <p className="text-sm text-slate-500">
                    Transaction ID
                  </p>

                  <p className="font-semibold">
                    {order.payment.transaction_id || "-"}
                  </p>
                </div>

                <div>
                  <p className="text-sm text-slate-500">
                    Status
                  </p>

                  <p className="font-semibold">
                    {order.payment.status || "-"}
                  </p>
                </div>

              </div>

            ) : (

              <p className="text-slate-500">
                Belum ada data pembayaran.
              </p>

            )}

          </div>

          {/* Shipment */}
          <div
            className="
              bg-white
              rounded-3xl
              shadow-sm
              border
              border-slate-200
              p-6
            "
          >

            <h2
              className="
                text-lg
                font-bold
                text-slate-900
                mb-5
              "
            >
              Shipment Information
            </h2>

            <div className="grid md:grid-cols-2 gap-5">

              <div>

                <p className="text-sm text-slate-500">
                  Courier
                </p>

                <p className="font-semibold">
                  {order.courier_code || "-"}
                </p>

              </div>

              <div>

                <p className="text-sm text-slate-500">
                  Service
                </p>

                <p className="font-semibold">
                  {order.courier_service || "-"}
                </p>

              </div>

              <div className="md:col-span-2">

                <p className="text-sm text-slate-500">
                  Tracking Number
                </p>

                <p className="font-semibold">
                  {order.tracking_number || "-"}
                </p>

              </div>

            </div>

          </div>

          {/* Notes */}
          <div
            className="
              bg-white
              rounded-3xl
              shadow-sm
              border
              border-slate-200
              p-6
            "
          >

            <h2
              className="
                text-lg
                font-bold
                text-slate-900
                mb-5
              "
            >
              Notes
            </h2>

            <div className="space-y-5">

              <div>

                <p className="text-sm text-slate-500 mb-2">
                  Customer Notes
                </p>

                <div
                  className="
                    bg-slate-50
                    rounded-2xl
                    p-4
                  "
                >
                  {order.customer_notes || "-"}
                </div>

              </div>

              <div>

                <p className="text-sm text-slate-500 mb-2">
                  Admin Notes
                </p>

                <div
                  className="
                    bg-slate-50
                    rounded-2xl
                    p-4
                  "
                >
                  {order.admin_notes || "-"}
                </div>

              </div>

            </div>

          </div>

        </div>

        {/* Summary */}
        <div>

          <div
            className="
              bg-white
              rounded-3xl
              shadow-sm
              border
              border-slate-200
              p-6
              sticky
              top-28
            "
          >

            <h2
              className="
                text-lg
                font-bold
                text-slate-900
                mb-6
              "
            >
              Order Summary
            </h2>

            <div className="space-y-4">

              <div className="flex justify-between">

                <span className="text-slate-500">
                  Subtotal
                </span>

                <span className="font-medium">
                  Rp {Number(order.subtotal).toLocaleString("id-ID")}
                </span>

              </div>

              <div className="flex justify-between">

                <span className="text-slate-500">
                  Shipping
                </span>

                <span className="font-medium">
                  Rp {Number(order.shipping_cost).toLocaleString("id-ID")}
                </span>

              </div>

              <div className="flex justify-between">

                <span className="text-slate-500">
                  Discount
                </span>

                <span className="font-medium text-red-600">
                  - Rp {Number(order.discount_amount).toLocaleString("id-ID")}
                </span>

              </div>

              <div className="flex justify-between">

                <span className="text-slate-500">
                  Tax
                </span>

                <span className="font-medium">
                  Rp {Number(order.tax_amount).toLocaleString("id-ID")}
                </span>

              </div>

              <hr />

              <div
                className="
                  flex
                  justify-between
                  text-lg
                  font-bold
                "
              >

                <span>Total</span>

                <span className="text-red-600">
                  Rp {Number(order.grand_total).toLocaleString("id-ID")}
                </span>

              </div>

            </div>

            {/* Actions */}
            <div className="mt-8 space-y-3">

              {order.status === "pending" && (

                <button
                  disabled={processing}
                  onClick={() =>
                    handleStatusUpdate(
                      "confirmed"
                    )
                  }
                  className="
                    w-full
                    py-3
                    rounded-2xl
                    bg-blue-600
                    text-white
                    hover:bg-blue-700
                    transition
                  "
                >
                  Confirm Order
                </button>

              )}

              {order.status === "confirmed" && (

                <button
                  disabled={processing}
                  onClick={() =>
                    handleStatusUpdate(
                      "processing"
                    )
                  }
                  className="
                    w-full
                    py-3
                    rounded-2xl
                    bg-indigo-600
                    text-white
                    hover:bg-indigo-700
                    transition
                  "
                >
                  Process Order
                </button>

              )}

              {order.status === "processing" && (

                <button
                  disabled={processing}
                  onClick={() =>
                    handleStatusUpdate(
                      "shipped"
                    )
                  }
                  className="
                    w-full
                    py-3
                    rounded-2xl
                    bg-cyan-600
                    text-white
                    hover:bg-cyan-700
                    transition
                  "
                >
                  Ship Order
                </button>

              )}

              {order.status === "shipped" && (

                <button
                  disabled={processing}
                  onClick={() =>
                    handleStatusUpdate(
                      "completed"
                    )
                  }
                  className="
                    w-full
                    py-3
                    rounded-2xl
                    bg-emerald-600
                    text-white
                    hover:bg-emerald-700
                    transition
                  "
                >
                  Complete Order
                </button>

              )}

              {![
                "completed",
                "cancelled",
              ].includes(order.status) && (

                <button
                  disabled={processing}
                  onClick={handleCancel}
                  className="
                    w-full
                    py-3
                    rounded-2xl
                    border
                    border-red-300
                    text-red-600
                    hover:bg-red-50
                    transition
                  "
                >
                  Cancel Order
                </button>

              )}

            </div>

          </div>

        </div>

      </div>

    </div>

  );
}