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
  XCircleIcon,
  ArrowUturnLeftIcon,
  TrashIcon,
} from "@heroicons/react/24/outline";

import {
  getPayments,
  markAsPaid,
  markAsFailed,
  refundPayment,
  deletePayment,

  PAYMENT_STATUS,
} from "../../services/paymentService";

import {
  successAlert,
  errorAlert,
} from "../../utils/alert";

export default function Payments() {

  const navigate =
    useNavigate();

  /*
  |--------------------------------------------------------------------------
  | State
  |--------------------------------------------------------------------------
  */

  const [payments, setPayments] =
    useState([]);

  const [loading, setLoading] =
    useState(true);

  const [search, setSearch] =
    useState("");

  const [status, setStatus] =
    useState("");

  const [gateway, setGateway] =
    useState("");

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
  | Fetch Payments
  |--------------------------------------------------------------------------
  */

  const fetchPayments =
    async () => {

      try {

        setLoading(true);

        const response =
          await getPayments({

            search,

            status,

            gateway,

            page,

            per_page:
              perPage,
          });

        setPayments(
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

          "Gagal mengambil data pembayaran."
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

    fetchPayments();

  }, [
    page,
    perPage,
    status,
    gateway,
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
          meta.total ?? 0,

        pending:
          payments.filter(

            (item) =>
              item.status ===
              PAYMENT_STATUS.PENDING

          ).length,

        paid:
          payments.filter(

            (item) =>
              item.status ===
              PAYMENT_STATUS.PAID

          ).length,

        failed:
          payments.filter(

            (item) =>
              item.status ===
              PAYMENT_STATUS.FAILED

          ).length,

        refunded:
          payments.filter(

            (item) =>

              item.status ===
                PAYMENT_STATUS.REFUNDED ||

              item.status ===
                PAYMENT_STATUS.PARTIAL_REFUND

          ).length,
      };

    }, [
      payments,
      meta,
    ]);

  /*
  |--------------------------------------------------------------------------
  | Search
  |--------------------------------------------------------------------------
  */

  const handleSearch =
    (event) => {

      event.preventDefault();

      setPage(1);

      fetchPayments();
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

      setGateway("");

      setPage(1);

      setPerPage(15);

      setTimeout(() => {

        fetchPayments();

      }, 0);
    };

  /*
  |--------------------------------------------------------------------------
  | Detail
  |--------------------------------------------------------------------------
  */

  const handleDetail =
    (paymentNumber) => {

      navigate(
        `/payments/${paymentNumber}`
      );
    };

  /*
  |--------------------------------------------------------------------------
  | Mark Paid
  |--------------------------------------------------------------------------
  */

  const handlePaid =
    async (payment) => {

      const result =
        await Swal.fire({

          title:
            "Konfirmasi Pembayaran",

          html: `
            <input
              id="paid_amount"
              class="swal2-input"
              placeholder="Paid Amount"
              value="${payment.amount}"
            />

            <input
              id="gateway_transaction_id"
              class="swal2-input"
              placeholder="Gateway Transaction ID"
            />
          `,

          showCancelButton:
            true,

          confirmButtonText:
            "Simpan",

          cancelButtonText:
            "Batal",

          preConfirm: () => {

            return {

              paid_amount:
                document.getElementById(
                  "paid_amount"
                ).value,

              gateway_transaction_id:
                document.getElementById(
                  "gateway_transaction_id"
                ).value,
            };
          },
        });

      if (
        !result.isConfirmed
      ) {
        return;
      }

      try {

        await markAsPaid(

          payment.payment_number,

          {
            paid_amount:
              result.value
                .paid_amount,

            gateway_transaction_id:
              result.value
                .gateway_transaction_id,
          }
        );

        successAlert(
          "Pembayaran berhasil diperbarui."
        );

        fetchPayments();

      } catch (error) {

        errorAlert(

          error?.response?.data
            ?.message ||

          "Gagal memperbarui pembayaran."
        );
      }
    };

  /*
  |--------------------------------------------------------------------------
  | Mark Failed
  |--------------------------------------------------------------------------
  */

  const handleFailed =
    async (paymentNumber) => {

      const result =
        await Swal.fire({

          title:
            "Tandai Gagal?",

          text:
            "Pembayaran akan ditandai gagal.",

          icon:
            "warning",

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

        await markAsFailed(
          paymentNumber
        );

        successAlert(
          "Status pembayaran diperbarui."
        );

        fetchPayments();

      } catch (error) {

        errorAlert(

          error?.response?.data
            ?.message ||

          "Gagal memperbarui pembayaran."
        );
      }
    };

  /*
  |--------------------------------------------------------------------------
  | Refund
  |--------------------------------------------------------------------------
  */

  const handleRefund =
    async (paymentNumber) => {

      const result =
        await Swal.fire({

          title:
            "Refund",

          html: `
            <input
              id="refund_amount"
              class="swal2-input"
              placeholder="Refund Amount"
            />

            <textarea
              id="refund_notes"
              class="swal2-textarea"
              placeholder="Catatan"
            ></textarea>
          `,

          showCancelButton:
            true,

          confirmButtonText:
            "Refund",

          cancelButtonText:
            "Batal",

          preConfirm: () => {

            return {

              amount:
                document.getElementById(
                  "refund_amount"
                ).value,

              notes:
                document.getElementById(
                  "refund_notes"
                ).value,
            };
          },
        });

      if (!result.isConfirmed) {
        return;
      }

      try {

        await refundPayment(

          paymentNumber,

          result.value
        );

        successAlert(
          "Refund berhasil diproses."
        );

        fetchPayments();

      } catch (error) {

        errorAlert(

          error?.response?.data
            ?.message ||

          "Gagal melakukan refund."
        );
      }
    };

  /*
  |--------------------------------------------------------------------------
  | Delete
  |--------------------------------------------------------------------------
  */

  const handleDelete =
    async (paymentNumber) => {

      const result =
        await Swal.fire({

          title:
            "Hapus Pembayaran?",

          text:
            "Data yang dihapus tidak dapat dikembalikan.",

          icon:
            "warning",

          showCancelButton:
            true,

          confirmButtonText:
            "Hapus",

          cancelButtonText:
            "Batal",
        });

      if (!result.isConfirmed) {
        return;
      }

      try {

        await deletePayment(
          paymentNumber
        );

        successAlert(
          "Pembayaran berhasil dihapus."
        );

        fetchPayments();

      } catch (error) {

        errorAlert(

          error?.response?.data
            ?.message ||

          "Gagal menghapus pembayaran."
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
              Payments
            </h1>

            <p
              className="
                mt-2
                text-slate-500
              "
            >
              Monitor dan kelola seluruh
              pembayaran pelanggan.
            </p>

          </div>

          <button
            onClick={fetchPayments}
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

          <p className="text-sm text-slate-500">
            Total Payments
          </p>

          <h3
            className="
              mt-2
              text-3xl
              font-bold
            "
          >
            {statistics.total}
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

          <p className="text-sm text-yellow-700">
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
            bg-green-50
            rounded-3xl
            border
            border-green-200
            p-5
          "
        >

          <p className="text-sm text-green-700">
            Paid
          </p>

          <h3
            className="
              mt-2
              text-3xl
              font-bold
              text-green-900
            "
          >
            {statistics.paid}
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

          <p className="text-sm text-red-700">
            Failed
          </p>

          <h3
            className="
              mt-2
              text-3xl
              font-bold
              text-red-900
            "
          >
            {statistics.failed}
          </h3>

        </div>

        <div
          className="
            bg-blue-50
            rounded-3xl
            border
            border-blue-200
            p-5
          "
        >

          <p className="text-sm text-blue-700">
            Refunded
          </p>

          <h3
            className="
              mt-2
              text-3xl
              font-bold
              text-blue-900
            "
          >
            {statistics.refunded}
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
            xl:grid-cols-5
            gap-4
          "
        >

          {/* Search */}
          <div className="xl:col-span-2">

            <div className="relative">

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
                  Cari pembayaran...
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

          {/* Status */}
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
            "
          >

            <option value="">
              Semua Status
            </option>

            {Object.values(
              PAYMENT_STATUS
            ).map((item) => (

              <option
                key={item}
                value={item}
              >
                {item}
              </option>

            ))}

          </select>

          {/* Gateway */}
          <select
            value={gateway}
            onChange={(e) => {

              setGateway(
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
            "
          >

            <option value="">
              Semua Gateway
            </option>

            <option value="midtrans">
              Midtrans
            </option>

          </select>

          {/* Actions */}
          <div className="flex gap-3">

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
              "
            >

              <FunnelIcon className="w-5 h-5" />

              Cari

            </button>

            <button
              type="button"
              onClick={handleReset}
              className="
                rounded-2xl
                border
                border-slate-300
                px-4
                py-3
                hover:bg-slate-100
              "
            >

              <ArrowPathIcon
                className="w-5 h-5"
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

        <div className="overflow-x-auto">

          <table
            className="
              w-full
              min-w-[1200px]
            "
          >

            <thead className="bg-slate-50">

              <tr>

                <th className="px-6 py-4 text-left text-sm font-semibold">
                  Payment
                </th>

                <th className="px-6 py-4 text-left text-sm font-semibold">
                  Order
                </th>

                <th className="px-6 py-4 text-left text-sm font-semibold">
                  Gateway
                </th>

                <th className="px-6 py-4 text-left text-sm font-semibold">
                  Amount
                </th>

                <th className="px-6 py-4 text-left text-sm font-semibold">
                  Status
                </th>

                <th className="px-6 py-4 text-left text-sm font-semibold">
                  Created
                </th>

                <th className="px-6 py-4 text-center text-sm font-semibold">
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
                    Memuat data pembayaran...
                  </td>

                </tr>

              ) : payments.length === 0 ? (

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
                    Tidak ada data pembayaran.
                  </td>

                </tr>

              ) : (

                payments.map((payment) => (

                  <tr
                    key={payment.id}
                    className="
                      border-t
                      border-slate-100
                      hover:bg-slate-50
                      transition
                    "
                  >

                    {/* Payment */}
                    <td className="px-6 py-5">

                      <div>

                        <p
                          className="
                            font-semibold
                            text-slate-900
                          "
                        >
                          {payment.payment_number}
                        </p>

                        <p
                          className="
                            text-sm
                            text-slate-500
                          "
                        >
                          {payment.method}
                        </p>

                      </div>

                    </td>

                    {/* Order */}
                    <td className="px-6 py-5">

                      <span className="font-medium">

                        {payment.order?.order_number || "-"}

                      </span>

                    </td>

                    {/* Gateway */}
                    <td className="px-6 py-5">

                      <div>

                        <p className="font-medium">
                          {payment.gateway}
                        </p>

                        <p
                          className="
                            text-sm
                            text-slate-500
                          "
                        >
                          {payment.gateway_transaction_id || "-"}
                        </p>

                      </div>

                    </td>

                    {/* Amount */}
                    <td className="px-6 py-5">

                      <div>

                        <p
                          className="
                            font-semibold
                            text-slate-900
                          "
                        >
                          {payment.amount_formatted}
                        </p>

                        <p
                          className="
                            text-sm
                            text-slate-500
                          "
                        >
                          Paid: {payment.paid_amount_formatted}
                        </p>

                      </div>

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
                            payment.status_color === "green"
                              ? "bg-green-100 text-green-800"

                              : payment.status_color === "yellow"
                              ? "bg-yellow-100 text-yellow-800"

                              : payment.status_color === "red"
                              ? "bg-red-100 text-red-800"

                              : payment.status_color === "blue"
                              ? "bg-blue-100 text-blue-800"

                              : "bg-slate-100 text-slate-800"
                          }
                        `}
                      >
                        {payment.status_label}
                      </span>

                    </td>

                    {/* Created */}
                    <td className="px-6 py-5">

                      <div>

                        <p>
                          {payment.created_at_human}
                        </p>

                        <p
                          className="
                            text-sm
                            text-slate-500
                          "
                        >
                          {payment.created_at
                            ? new Date(
                                payment.created_at
                              ).toLocaleDateString(
                                "id-ID"
                              )
                            : "-"}
                        </p>

                      </div>

                    </td>

                    {/* Actions */}
                    <td className="px-6 py-5">

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
                              payment.payment_number
                            )
                          }
                          className="
                            p-2
                            rounded-xl
                            bg-slate-100
                            hover:bg-slate-200
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

                        {/* Paid */}
                        {payment.status ===
                          PAYMENT_STATUS.PENDING && (

                          <button
                            onClick={() =>
                              handlePaid(payment)
                            }
                            className="
                              p-2
                              rounded-xl
                              bg-green-100
                              text-green-700
                              hover:bg-green-200
                            "
                            title="Mark Paid"
                          >

                            <CheckCircleIcon
                              className="
                                w-5
                                h-5
                              "
                            />

                          </button>

                        )}

                        {/* Failed */}
                        {payment.status ===
                          PAYMENT_STATUS.PENDING && (

                          <button
                            onClick={() =>
                              handleFailed(
                                payment.payment_number
                              )
                            }
                            className="
                              p-2
                              rounded-xl
                              bg-red-100
                              text-red-700
                              hover:bg-red-200
                            "
                            title="Mark Failed"
                          >

                            <XCircleIcon
                              className="
                                w-5
                                h-5
                              "
                            />

                          </button>

                        )}

                        {/* Refund */}
                        {payment.status ===
                          PAYMENT_STATUS.PAID && (

                          <button
                            onClick={() =>
                              handleRefund(
                                payment.payment_number
                              )
                            }
                            className="
                              p-2
                              rounded-xl
                              bg-blue-100
                              text-blue-700
                              hover:bg-blue-200
                            "
                            title="Refund"
                          >

                            <ArrowUturnLeftIcon
                              className="
                                w-5
                                h-5
                              "
                            />

                          </button>

                        )}

                        {/* Delete */}
                        <button
                          onClick={() =>
                            handleDelete(
                              payment.payment_number
                            )
                          }
                          className="
                            p-2
                            rounded-xl
                            bg-red-100
                            text-red-700
                            hover:bg-red-200
                          "
                          title="Delete"
                        >

                          <TrashIcon
                            className="
                              w-5
                              h-5
                            "
                          />

                        </button>

                      </div>

                    </td>

                  </tr>

                ))

              )}

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