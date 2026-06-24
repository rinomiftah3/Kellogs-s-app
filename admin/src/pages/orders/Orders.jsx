import {
  useEffect,
  useMemo,
  useState,
} from "react";

import {
  useNavigate,
} from "react-router-dom";

import Swal from "sweetalert2";

import {
  EyeIcon,
  MagnifyingGlassIcon,
  FunnelIcon,
  ArrowPathIcon,
  CheckCircleIcon,
  Cog6ToothIcon,
  TruckIcon,
  ArchiveBoxIcon,
  XCircleIcon,
} from "@heroicons/react/24/outline";

import {
  getOrders,
  confirmOrder,
  processOrder,
  shipOrder,
  completeOrder,
  cancelOrder,

  ORDER_STATUS,

  PAYMENT_STATUS,

  FULFILLMENT_STATUS,
} from "../../services/orderService";

import {
  successAlert,
  errorAlert,
} from "../../utils/alert";

export default function Orders() {

  const navigate =
    useNavigate();

  /*
  |--------------------------------------------------------------------------
  | State
  |--------------------------------------------------------------------------
  */

  const [orders, setOrders] =
    useState([]);

  const [loading, setLoading] =
    useState(true);

  const [search, setSearch] =
    useState("");

  const [status, setStatus] =
    useState("");

  const [
    paymentStatus,
    setPaymentStatus,
  ] = useState("");

  const [
    fulfillmentStatus,
    setFulfillmentStatus,
  ] = useState("");

  const [page, setPage] =
    useState(1);

  const [perPage, setPerPage] =
    useState(15);

  const [meta, setMeta] =
    useState({

      current_page: 1,

      last_page: 1,

      total: 0,

      per_page: 15,
    });

  /*
  |--------------------------------------------------------------------------
  | Fetch Orders
  |--------------------------------------------------------------------------
  */

  const fetchOrders =
    async () => {

      try {

        setLoading(true);

        const response =
          await getOrders({

            search,

            status,

            payment_status:
              paymentStatus,

            fulfillment_status:
              fulfillmentStatus,

            page,

            per_page:
              perPage,
          });

        setOrders(
          response.data ?? []
        );

        setMeta(
          response.meta ?? {}
        );

      } catch (error) {

        console.error(error);

        errorAlert(
          error?.response?.data
            ?.message ||

          "Gagal mengambil data pesanan."
        );

      } finally {

        setLoading(false);
      }
    };

  /*
  |--------------------------------------------------------------------------
  | Effects
  |--------------------------------------------------------------------------
  */

  useEffect(() => {

    fetchOrders();

  }, [
    page,
    perPage,
    status,
    paymentStatus,
    fulfillmentStatus,
  ]);

  /*
  |--------------------------------------------------------------------------
  | Statistics
  |--------------------------------------------------------------------------
  */

  const statistics =
    useMemo(() => {

      return {

        total:
          orders.length,

        pending:
          orders.filter(

            (item) =>
              item.status ===
              ORDER_STATUS.PENDING

          ).length,

        processing:
          orders.filter(

            (item) =>
              item.status ===
              ORDER_STATUS.PROCESSING

          ).length,

        completed:
          orders.filter(

            (item) =>
              item.status ===
              ORDER_STATUS.COMPLETED

          ).length,

        cancelled:
          orders.filter(

            (item) =>
              item.status ===
              ORDER_STATUS.CANCELLED

          ).length,
      };

    }, [orders]);

  /*
  |--------------------------------------------------------------------------
  | Search
  |--------------------------------------------------------------------------
  */

  const handleSearch =
    (event) => {

      event.preventDefault();

      setPage(1);

      fetchOrders();
    };

  /*
  |--------------------------------------------------------------------------
  | Reset Filter
  |--------------------------------------------------------------------------
  */

  const handleReset =
    () => {

      setSearch("");

      setStatus("");

      setPaymentStatus("");

      setFulfillmentStatus("");

      setPage(1);

      setPerPage(15);

      setTimeout(() => {

        fetchOrders();

      }, 0);
    };

  /*
  |--------------------------------------------------------------------------
  | Detail
  |--------------------------------------------------------------------------
  */

  const handleDetail =
    (orderNumber) => {

      navigate(
        `/orders/${orderNumber}`
      );
    };

  /*
  |--------------------------------------------------------------------------
  | Confirm
  |--------------------------------------------------------------------------
  */

  const handleConfirm =
    async (orderNumber) => {

      const result =
        await Swal.fire({

          title:
            "Konfirmasi Pesanan?",

          text:
            "Pesanan akan dikonfirmasi.",

          icon:
            "question",

          showCancelButton:
            true,

          confirmButtonText:
            "Ya",

          cancelButtonText:
            "Batal",
        });

      if (!result.isConfirmed) {
        return;
      }

      try {

        await confirmOrder(
          orderNumber
        );

        successAlert(
          "Pesanan berhasil dikonfirmasi."
        );

        fetchOrders();

      } catch (error) {

        errorAlert(

          error?.response?.data
            ?.message ||

          "Gagal mengubah status."
        );
      }
    };

  /*
  |--------------------------------------------------------------------------
  | Process
  |--------------------------------------------------------------------------
  */

  const handleProcess =
    async (orderNumber) => {

      try {

        await processOrder(
          orderNumber
        );

        successAlert(
          "Pesanan diproses."
        );

        fetchOrders();

      } catch (error) {

        errorAlert(

          error?.response?.data
            ?.message ||

          "Gagal memproses pesanan."
        );
      }
    };
  /*
  |--------------------------------------------------------------------------
  | Ship
  |--------------------------------------------------------------------------
  */

  const handleShip =
    async (orderNumber) => {

      const result =
        await Swal.fire({

          title:
            "Kirim Pesanan?",

          text:
            "Pesanan akan diubah menjadi dikirim.",

          icon:
            "question",

          showCancelButton:
            true,

          confirmButtonText:
            "Kirim",

          cancelButtonText:
            "Batal",
        });

      if (!result.isConfirmed) {
        return;
      }

      try {

        await shipOrder(
          orderNumber
        );

        successAlert(
          "Pesanan berhasil dikirim."
        );

        fetchOrders();

      } catch (error) {

        errorAlert(

          error?.response?.data
            ?.message ||

          "Gagal mengirim pesanan."
        );
      }
    };

  /*
  |--------------------------------------------------------------------------
  | Complete
  |--------------------------------------------------------------------------
  */

  const handleComplete =
    async (orderNumber) => {

      const result =
        await Swal.fire({

          title:
            "Selesaikan Pesanan?",

          text:
            "Pesanan akan ditandai selesai.",

          icon:
            "question",

          showCancelButton:
            true,

          confirmButtonText:
            "Selesaikan",

          cancelButtonText:
            "Batal",
        });

      if (!result.isConfirmed) {
        return;
      }

      try {

        await completeOrder(
          orderNumber
        );

        successAlert(
          "Pesanan berhasil diselesaikan."
        );

        fetchOrders();

      } catch (error) {

        errorAlert(

          error?.response?.data
            ?.message ||

          "Gagal menyelesaikan pesanan."
        );
      }
    };

  /*
  |--------------------------------------------------------------------------
  | Cancel
  |--------------------------------------------------------------------------
  */

  const handleCancel =
    async (orderNumber) => {

      const result =
        await Swal.fire({

          title:
            "Batalkan Pesanan",

          input:
            "textarea",

          inputLabel:
            "Alasan pembatalan",

          inputPlaceholder:
            "Masukkan alasan...",

          icon:
            "warning",

          showCancelButton:
            true,

          confirmButtonText:
            "Batalkan",

          cancelButtonText:
            "Tutup",
        });

      if (!result.isConfirmed) {
        return;
      }

      try {

        await cancelOrder(
          orderNumber,
          result.value || ""
        );

        successAlert(
          "Pesanan berhasil dibatalkan."
        );

        fetchOrders();

      } catch (error) {

        errorAlert(

          error?.response?.data
            ?.message ||

          "Gagal membatalkan pesanan."
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

            <h1
              className="
                text-3xl
                font-bold
                text-slate-900
              "
            >
              Orders
            </h1>

            <p
              className="
                mt-2
                text-slate-500
              "
            >
              Monitor dan kelola seluruh
              transaksi pelanggan.
            </p>

          </div>

          <button
            onClick={fetchOrders}
            className="
              inline-flex
              items-center
              gap-2
              rounded-2xl
              bg-red-600
              hover:bg-red-700
              text-white
              px-5
              py-3
              font-medium
              transition
            "
          >

            <ArrowPathIcon
              className="
                w-5
                h-5
              "
            />

            Refresh

          </button>

        </div>

      </div>

      {/* Statistics */}
      <div
        className="
          grid
          grid-cols-1
          sm:grid-cols-2
          xl:grid-cols-5
          gap-5
        "
      >

        <div
          className="
            bg-white
            rounded-3xl
            border
            border-slate-200
            p-5
          "
        >

          <p
            className="
              text-sm
              text-slate-500
            "
          >
            Total Orders
          </p>

          <h3
            className="
              mt-2
              text-3xl
              font-bold
            "
          >
            {meta.total ?? 0}
          </h3>

        </div>

        <div
          className="
            bg-yellow-50
            rounded-3xl
            border
            border-yellow-200
            p-5
          "
        >

          <p
            className="
              text-sm
              text-yellow-700
            "
          >
            Pending
          </p>

          <h3
            className="
              mt-2
              text-3xl
              font-bold
              text-yellow-900
            "
          >
            {statistics.pending}
          </h3>

        </div>

        <div
          className="
            bg-indigo-50
            rounded-3xl
            border
            border-indigo-200
            p-5
          "
        >

          <p
            className="
              text-sm
              text-indigo-700
            "
          >
            Processing
          </p>

          <h3
            className="
              mt-2
              text-3xl
              font-bold
              text-indigo-900
            "
          >
            {statistics.processing}
          </h3>

        </div>

        <div
          className="
            bg-green-50
            rounded-3xl
            border
            border-green-200
            p-5
          "
        >

          <p
            className="
              text-sm
              text-green-700
            "
          >
            Completed
          </p>

          <h3
            className="
              mt-2
              text-3xl
              font-bold
              text-green-900
            "
          >
            {statistics.completed}
          </h3>

        </div>

        <div
          className="
            bg-red-50
            rounded-3xl
            border
            border-red-200
            p-5
          "
        >

          <p
            className="
              text-sm
              text-red-700
            "
          >
            Cancelled
          </p>

          <h3
            className="
              mt-2
              text-3xl
              font-bold
              text-red-900
            "
          >
            {statistics.cancelled}
          </h3>

        </div>

      </div>

      {/* Filters */}
      <div
        className="
          bg-white
          rounded-3xl
          border
          border-slate-200
          shadow-sm
          p-6
        "
      >

        <form
          onSubmit={handleSearch}
          className="
            grid
            grid-cols-1
            md:grid-cols-2
            xl:grid-cols-6
            gap-4
          "
        >

          <div className="xl:col-span-2">

            <div
              className="
                relative
              "
            >

              <MagnifyingGlassIcon
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
                value={search}
                onChange={(e) =>
                  setSearch(
                    e.target.value
                  )
                }
                placeholder="
                  Cari order/customer...
                "
                className="
                  w-full
                  rounded-2xl
                  border
                  border-slate-300
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
          {/* Order Status */}
          <select
            value={status}
            onChange={(e) => {
              setStatus(
                e.target.value
              );

              setPage(1);
            }}
            className="
              rounded-2xl
              border
              border-slate-300
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

            <option
              value={
                ORDER_STATUS.PENDING
              }
            >
              Pending
            </option>

            <option
              value={
                ORDER_STATUS.CONFIRMED
              }
            >
              Confirmed
            </option>

            <option
              value={
                ORDER_STATUS.PROCESSING
              }
            >
              Processing
            </option>

            <option
              value={
                ORDER_STATUS.SHIPPED
              }
            >
              Shipped
            </option>

            <option
              value={
                ORDER_STATUS.COMPLETED
              }
            >
              Completed
            </option>

            <option
              value={
                ORDER_STATUS.CANCELLED
              }
            >
              Cancelled
            </option>

          </select>

          {/* Payment Status */}
          <select
            value={paymentStatus}
            onChange={(e) => {
              setPaymentStatus(
                e.target.value
              );

              setPage(1);
            }}
            className="
              rounded-2xl
              border
              border-slate-300
              px-4
              py-3
              focus:outline-none
              focus:ring-2
              focus:ring-red-500
            "
          >

            <option value="">
              Payment
            </option>

            <option
              value={
                PAYMENT_STATUS.PENDING
              }
            >
              Pending
            </option>

            <option
              value={
                PAYMENT_STATUS.PAID
              }
            >
              Paid
            </option>

            <option
              value={
                PAYMENT_STATUS.FAILED
              }
            >
              Failed
            </option>

            <option
              value={
                PAYMENT_STATUS.REFUNDED
              }
            >
              Refunded
            </option>

          </select>

          {/* Fulfillment */}
          <select
            value={fulfillmentStatus}
            onChange={(e) => {
              setFulfillmentStatus(
                e.target.value
              );

              setPage(1);
            }}
            className="
              rounded-2xl
              border
              border-slate-300
              px-4
              py-3
              focus:outline-none
              focus:ring-2
              focus:ring-red-500
            "
          >

            <option value="">
              Fulfillment
            </option>

            <option
              value={
                FULFILLMENT_STATUS.PENDING
              }
            >
              Pending
            </option>

            <option
              value={
                FULFILLMENT_STATUS.PACKED
              }
            >
              Packed
            </option>

            <option
              value={
                FULFILLMENT_STATUS.SHIPPED
              }
            >
              Shipped
            </option>

            <option
              value={
                FULFILLMENT_STATUS.DELIVERED
              }
            >
              Delivered
            </option>

          </select>

          {/* Actions */}
          <div
            className="
              flex
              gap-3
            "
          >

            <button
              type="submit"
              className="
                flex-1
                inline-flex
                items-center
                justify-center
                gap-2
                rounded-2xl
                bg-red-600
                hover:bg-red-700
                text-white
                px-4
                py-3
                font-medium
                transition
              "
            >

              <FunnelIcon
                className="
                  w-5
                  h-5
                "
              />

              Cari

            </button>

            <button
              type="button"
              onClick={
                handleReset
              }
              className="
                inline-flex
                items-center
                justify-center
                rounded-2xl
                border
                border-slate-300
                px-4
                py-3
                hover:bg-slate-100
                transition
              "
            >

              <ArrowPathIcon
                className="
                  w-5
                  h-5
                "
              />

            </button>

          </div>

        </form>

      </div>

      {/* Table */}
      <div
        className="
          bg-white
          rounded-3xl
          border
          border-slate-200
          shadow-sm
          overflow-hidden
        "
      >

        <div
          className="
            overflow-x-auto
          "
        >

          <table
            className="
              w-full
              min-w-[1100px]
            "
          >

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
                  "
                >
                  Order
                </th>

                <th
                  className="
                    px-6
                    py-4
                    text-left
                    text-sm
                    font-semibold
                  "
                >
                  Customer
                </th>

                <th
                  className="
                    px-6
                    py-4
                    text-left
                    text-sm
                    font-semibold
                  "
                >
                  Total
                </th>

                <th
                  className="
                    px-6
                    py-4
                    text-left
                    text-sm
                    font-semibold
                  "
                >
                  Status
                </th>

                <th
                  className="
                    px-6
                    py-4
                    text-left
                    text-sm
                    font-semibold
                  "
                >
                  Payment
                </th>

                <th
                  className="
                    px-6
                    py-4
                    text-left
                    text-sm
                    font-semibold
                  "
                >
                  Ordered At
                </th>

                <th
                  className="
                    px-6
                    py-4
                    text-center
                    text-sm
                    font-semibold
                  "
                >
                  Actions
                </th>

              </tr>

            </thead>

            <tbody>

              {loading ? (

                <tr>

                  <td
                    colSpan="7"
                    className="
                      px-6
                      py-16
                      text-center
                      text-slate-500
                    "
                  >
                    Memuat data...
                  </td>

                </tr>

              ) : orders.length === 0 ? (

                <tr>

                  <td
                    colSpan="7"
                    className="
                      px-6
                      py-16
                      text-center
                      text-slate-500
                    "
                  >
                    Tidak ada data pesanan.
                  </td>

                </tr>

              ) : (

                orders.map(
                  (order) => (
                                      <tr
                    key={order.id}
                    className="
                      border-t
                      border-slate-100
                      hover:bg-slate-50
                      transition
                    "
                  >

                    {/* Order */}
                    <td className="px-6 py-5">

                      <div>

                        <p
                          className="
                            font-semibold
                            text-slate-900
                          "
                        >
                          {order.order_number}
                        </p>

                        <p
                          className="
                            text-sm
                            text-slate-500
                            mt-1
                          "
                        >
                          {order.items_count ??
                            order.item_count ??
                            0}
                          {" "}item(s)
                        </p>

                      </div>

                    </td>

                    {/* Customer */}
                    <td className="px-6 py-5">

                      <div>

                        <p className="font-medium">
                          {order.customer_name}
                        </p>

                        <p
                          className="
                            text-sm
                            text-slate-500
                          "
                        >
                          {order.customer_email}
                        </p>

                      </div>

                    </td>

                    {/* Total */}
                    <td className="px-6 py-5">

                      <span
                        className="
                          font-semibold
                          text-slate-900
                        "
                      >
                        {order.formatted_total}
                      </span>

                    </td>

                    {/* Status */}
                    <td className="px-6 py-5">

                      <span
                        className={`
                          inline-flex
                          px-3
                          py-1
                          rounded-full
                          text-xs
                          font-semibold

                          ${
                            order.status === "pending"
                              ? "bg-yellow-100 text-yellow-800"

                              : order.status === "confirmed"
                              ? "bg-blue-100 text-blue-800"

                              : order.status === "processing"
                              ? "bg-indigo-100 text-indigo-800"

                              : order.status === "shipped"
                              ? "bg-cyan-100 text-cyan-800"

                              : order.status === "completed"
                              ? "bg-green-100 text-green-800"

                              : "bg-red-100 text-red-800"
                          }
                        `}
                      >
                        {order.status_label}
                      </span>

                    </td>

                    {/* Payment */}
                    <td className="px-6 py-5">

                      <span
                        className={`
                          inline-flex
                          px-3
                          py-1
                          rounded-full
                          text-xs
                          font-semibold

                          ${
                            order.payment_status === "paid"
                              ? "bg-green-100 text-green-800"

                              : order.payment_status === "pending"
                              ? "bg-yellow-100 text-yellow-800"

                              : order.payment_status === "failed"
                              ? "bg-red-100 text-red-800"

                              : "bg-purple-100 text-purple-800"
                          }
                        `}
                      >
                        {order.payment_status_label}
                      </span>

                    </td>

                    {/* Ordered At */}
                    <td className="px-6 py-5">

                      <div>

                        <p>
                          {order.ordered_at_human}
                        </p>

                        <p
                          className="
                            text-sm
                            text-slate-500
                          "
                        >
                          {order.created_at
                            ? new Date(
                                order.created_at
                              ).toLocaleDateString(
                                "id-ID"
                              )
                            : "-"}
                        </p>

                      </div>

                    </td>

                    {/* Actions */}
                    <td
                      className="
                        px-6
                        py-5
                      "
                    >

                      <div
                        className="
                          flex
                          flex-wrap
                          justify-center
                          gap-2
                        "
                      >

                        {/* Detail */}
                        <button
                          onClick={() =>
                            handleDetail(
                              order.order_number
                            )
                          }
                          className="
                            p-2
                            rounded-xl
                            bg-slate-100
                            hover:bg-slate-200
                            transition
                          "
                          title="Detail"
                        >

                          <EyeIcon
                            className="
                              w-5
                              h-5
                            "
                          />

                        </button>

                        {/* Confirm */}
                        {order.status ===
                          ORDER_STATUS.PENDING && (

                          <button
                            onClick={() =>
                              handleConfirm(
                                order.order_number
                              )
                            }
                            className="
                              p-2
                              rounded-xl
                              bg-blue-100
                              text-blue-700
                              hover:bg-blue-200
                              transition
                            "
                            title="Confirm"
                          >

                            <CheckCircleIcon
                              className="
                                w-5
                                h-5
                              "
                            />

                          </button>

                        )}

                        {/* Process */}
                        {order.status ===
                          ORDER_STATUS.CONFIRMED && (

                          <button
                            onClick={() =>
                              handleProcess(
                                order.order_number
                              )
                            }
                            className="
                              p-2
                              rounded-xl
                              bg-indigo-100
                              text-indigo-700
                              hover:bg-indigo-200
                              transition
                            "
                            title="Process"
                          >

                            <Cog6ToothIcon
                              className="
                                w-5
                                h-5
                              "
                            />

                          </button>

                        )}

                        {/* Ship */}
                        {order.status ===
                          ORDER_STATUS.PROCESSING && (

                          <button
                            onClick={() =>
                              handleShip(
                                order.order_number
                              )
                            }
                            className="
                              p-2
                              rounded-xl
                              bg-cyan-100
                              text-cyan-700
                              hover:bg-cyan-200
                              transition
                            "
                            title="Ship"
                          >

                            <TruckIcon
                              className="
                                w-5
                                h-5
                              "
                            />

                          </button>

                        )}

                        {/* Complete */}
                        {order.status ===
                          ORDER_STATUS.SHIPPED && (

                          <button
                            onClick={() =>
                              handleComplete(
                                order.order_number
                              )
                            }
                            className="
                              p-2
                              rounded-xl
                              bg-green-100
                              text-green-700
                              hover:bg-green-200
                              transition
                            "
                            title="Complete"
                          >

                            <ArchiveBoxIcon
                              className="
                                w-5
                                h-5
                              "
                            />

                          </button>

                        )}

                        {/* Cancel */}
                        {[
                          ORDER_STATUS.PENDING,
                          ORDER_STATUS.CONFIRMED,
                          ORDER_STATUS.PROCESSING,
                        ].includes(
                          order.status
                        ) && (

                          <button
                            onClick={() =>
                              handleCancel(
                                order.order_number
                              )
                            }
                            className="
                              p-2
                              rounded-xl
                              bg-red-100
                              text-red-700
                              hover:bg-red-200
                              transition
                            "
                            title="Cancel"
                          >

                            <XCircleIcon
                              className="
                                w-5
                                h-5
                              "
                            />

                          </button>

                        )}

                      </div>

                    </td>

                  </tr>
                )
              )
            )
            }

            </tbody>

          </table>

        </div>

      </div>

      {/* Pagination */}
      <div
        className="
          flex
          flex-col
          md:flex-row
          md:items-center
          md:justify-between
          gap-4
        "
      >

        <p
          className="
            text-sm
            text-slate-500
          "
        >
          Menampilkan halaman{" "}
          <span className="font-semibold">
            {meta.current_page ?? 1}
          </span>
          {" "}dari{" "}
          <span className="font-semibold">
            {meta.last_page ?? 1}
          </span>
        </p>

        <div className="flex gap-3">

          <button
            disabled={
              meta.current_page <= 1
            }
            onClick={() =>
              setPage((prev) =>
                prev - 1
              )
            }
            className="
              px-4
              py-2
              rounded-xl
              border
              disabled:opacity-50
              hover:bg-slate-100
            "
          >
            Previous
          </button>

          <button
            disabled={
              meta.current_page >=
              meta.last_page
            }
            onClick={() =>
              setPage((prev) =>
                prev + 1
              )
            }
            className="
              px-4
              py-2
              rounded-xl
              border
              disabled:opacity-50
              hover:bg-slate-100
            "
          >
            Next
          </button>

        </div>

      </div>

    </div>

  );

}