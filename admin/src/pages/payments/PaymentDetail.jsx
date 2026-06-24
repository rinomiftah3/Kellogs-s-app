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
  getPayment,
  markAsPaid,
  markAsFailed,
  markAsExpired,
  markAsCancelled,
  refundPayment,
  deletePayment,

  PAYMENT_STATUS,
} from "../../services/paymentService";

export default function PaymentDetail() {

  const navigate =
    useNavigate();

  const {
    paymentNumber,
  } = useParams();

  /*
  |--------------------------------------------------------------------------
  | States
  |--------------------------------------------------------------------------
  */

  const [
    payment,
    setPayment,
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

    fetchPayment();

    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [paymentNumber]);

  const fetchPayment =
    async () => {

      try {

        setLoading(true);

        const response =
          await getPayment(
            paymentNumber
          );

        setPayment(
          response.data
        );

      } catch (error) {

        console.error(error);

        Swal.fire({

          icon: "error",

          title: "Gagal",

          text:
            error?.response?.data?.message ||

            "Gagal memuat detail pembayaran.",
        });

        navigate("/payments");

      } finally {

        setLoading(false);
      }
    };

  /*
  |--------------------------------------------------------------------------
  | Mark Paid
  |--------------------------------------------------------------------------
  */

  const handlePaid =
    async () => {

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

        setProcessing(true);

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

        Swal.fire({

          icon: "success",

          title: "Berhasil",

          text:
            "Pembayaran berhasil diperbarui.",
        });

        fetchPayment();

      } catch (error) {

        Swal.fire({

          icon: "error",

          title: "Gagal",

          text:
            error?.response?.data?.message ||

            "Gagal memperbarui pembayaran.",
        });

      } finally {

        setProcessing(false);
      }
    };

  /*
  |--------------------------------------------------------------------------
  | Mark Failed
  |--------------------------------------------------------------------------
  */

  const handleFailed =
    async () => {

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

        setProcessing(true);

        await markAsFailed(
          payment.payment_number
        );

        Swal.fire({

          icon: "success",

          title: "Berhasil",

          text:
            "Status pembayaran berhasil diperbarui.",
        });

        fetchPayment();

      } catch (error) {

        Swal.fire({

          icon: "error",

          title: "Gagal",

          text:
            error?.response?.data?.message ||

            "Gagal memperbarui pembayaran.",
        });

      } finally {

        setProcessing(false);
      }
    };

  /*
  |--------------------------------------------------------------------------
  | Mark Expired
  |--------------------------------------------------------------------------
  */

  const handleExpired =
    async () => {

      const result =
        await Swal.fire({

          title:
            "Tandai Expired?",

          text:
            "Pembayaran akan ditandai expired.",

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

        setProcessing(true);

        await markAsExpired(
          payment.payment_number
        );

        Swal.fire({

          icon: "success",

          title: "Berhasil",

          text:
            "Status pembayaran berhasil diperbarui.",
        });

        fetchPayment();

      } catch (error) {

        Swal.fire({

          icon: "error",

          title: "Gagal",

          text:
            error?.response?.data?.message ||

            "Gagal memperbarui pembayaran.",
        });

      } finally {

        setProcessing(false);
      }
    };

  /*
  |--------------------------------------------------------------------------
  | Mark Cancelled
  |--------------------------------------------------------------------------
  */

  const handleCancelled =
    async () => {

      const result =
        await Swal.fire({

          title:
            "Batalkan Pembayaran?",

          text:
            "Pembayaran akan dibatalkan.",

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

        setProcessing(true);

        await markAsCancelled(
          payment.payment_number
        );

        Swal.fire({

          icon: "success",

          title: "Berhasil",

          text:
            "Pembayaran berhasil dibatalkan.",
        });

        fetchPayment();

      } catch (error) {

        Swal.fire({

          icon: "error",

          title: "Gagal",

          text:
            error?.response?.data?.message ||

            "Gagal membatalkan pembayaran.",
        });

      } finally {

        setProcessing(false);
      }
    };
  /*
  |--------------------------------------------------------------------------
  | Refund
  |--------------------------------------------------------------------------
  */

  const handleRefund =
    async () => {

      const result =
        await Swal.fire({

          title:
            "Refund Payment",

          input:
            "number",

          inputLabel:
            "Nominal Refund",

          inputPlaceholder:
            "Masukkan nominal refund",

          showCancelButton:
            true,

          confirmButtonText:
            "Refund",

          cancelButtonText:
            "Batal",

          inputAttributes: {
            min: 1,
          },
        });

      if (
        !result.isConfirmed
      ) {
        return;
      }

      try {

        setProcessing(true);

        await refundPayment(

          payment.payment_number,

          {
            amount:
              Number(
                result.value
              ),
          }
        );

        Swal.fire({

          icon: "success",

          title: "Berhasil",

          text:
            "Refund berhasil diproses.",
        });

        fetchPayment();

      } catch (error) {

        Swal.fire({

          icon: "error",

          title: "Gagal",

          text:
            error?.response?.data?.message ||

            "Refund gagal diproses.",
        });

      } finally {

        setProcessing(false);
      }
    };

  /*
  |--------------------------------------------------------------------------
  | Delete
  |--------------------------------------------------------------------------
  */

  const handleDelete =
    async () => {

      const result =
        await Swal.fire({

          title:
            "Hapus Pembayaran?",

          text:
            "Data pembayaran akan dihapus permanen.",

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

        setProcessing(true);

        await deletePayment(
          payment.payment_number
        );

        Swal.fire({

          icon: "success",

          title: "Berhasil",

          text:
            "Pembayaran berhasil dihapus.",
        });

        navigate("/payments");

      } catch (error) {

        Swal.fire({

          icon: "error",

          title: "Gagal",

          text:
            error?.response?.data?.message ||

            "Gagal menghapus pembayaran.",
        });

      } finally {

        setProcessing(false);
      }
    };

  /*
  |--------------------------------------------------------------------------
  | Loading State
  |--------------------------------------------------------------------------
  */

  if (loading) {

    return (

      <div className="space-y-6">

        <div className="animate-pulse">

          <div className="h-8 w-64 bg-slate-200 rounded mb-4" />

          <div className="h-72 bg-white rounded-3xl" />

        </div>

      </div>
    );
  }

  /*
  |--------------------------------------------------------------------------
  | Empty State
  |--------------------------------------------------------------------------
  */

  if (!payment) {

    return (

      <div
        className="
          bg-white
          rounded-3xl
          p-10
          text-center
        "
      >

        <h2
          className="
            text-xl
            font-bold
            text-slate-700
          "
        >
          Payment tidak ditemukan
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
          border
          border-slate-200
          shadow-sm
          p-6
        "
      >

        <button
          onClick={() =>
            navigate("/payments")
          }
          className="
            inline-flex
            items-center
            gap-2
            text-sm
            text-slate-500
            hover:text-slate-700
            mb-5
          "
        >

          <ArrowLeftIcon
            className="
              w-4
              h-4
            "
          />

          Kembali

        </button>

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
              Payment Detail
            </h1>

            <p
              className="
                mt-2
                text-slate-500
              "
            >
              {payment.payment_number}
            </p>

          </div>

          <span
            className={`
              inline-flex
              items-center
              px-4
              py-2
              rounded-full
              text-sm
              font-semibold

              ${
                payment.status_color === "green"
                  ? "bg-green-100 text-green-700"

                : payment.status_color === "yellow"
                  ? "bg-yellow-100 text-yellow-700"

                : payment.status_color === "red"
                  ? "bg-red-100 text-red-700"

                : payment.status_color === "blue"
                  ? "bg-blue-100 text-blue-700"

                : "bg-slate-100 text-slate-700"
              }
            `}
          >

            {payment.status_label}

          </span>

        </div>

      </div>

      {/* Top Section */}
      <div
        className="
          grid
          grid-cols-1
          xl:grid-cols-2
          gap-6
        "
      >

        {/* Payment Information */}
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

          <h2
            className="
              text-lg
              font-bold
              mb-6
            "
          >
            Payment Information
          </h2>

          <div className="space-y-4">

            <div>

              <p className="text-sm text-slate-500">
                Gateway
              </p>

              <p className="font-semibold">
                {payment.gateway || "-"}
              </p>

            </div>

            <div>

              <p className="text-sm text-slate-500">
                Method
              </p>

              <p className="font-semibold">
                {payment.method || "-"}
              </p>

            </div>

            <div>

              <p className="text-sm text-slate-500">
                Gateway Transaction ID
              </p>

              <p className="font-semibold break-all">
                {payment.gateway_transaction_id || "-"}
              </p>

            </div>

            <div>

              <p className="text-sm text-slate-500">
                Gateway Order ID
              </p>

              <p className="font-semibold break-all">
                {payment.gateway_order_id || "-"}
              </p>

            </div>

            <div>

              <p className="text-sm text-slate-500">
                Payment URL
              </p>

              {
                payment.payment_url ? (

                  <a
                    href={payment.payment_url}
                    target="_blank"
                    rel="noreferrer"
                    className="
                      text-blue-600
                      hover:underline
                      break-all
                    "
                  >
                    {payment.payment_url}
                  </a>

                ) : (

                  <p className="font-semibold">
                    -
                  </p>

                )
              }

            </div>

          </div>

        </div>

        {/* Financial Information */}
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

          <h2
            className="
              text-lg
              font-bold
              mb-6
            "
          >
            Financial Information
          </h2>

          <div className="space-y-4">

            <div
              className="
                flex
                justify-between
              "
            >

              <span className="text-slate-500">
                Amount
              </span>

              <span className="font-semibold">
                {payment.amount_formatted}
              </span>

            </div>

            <div
              className="
                flex
                justify-between
              "
            >

              <span className="text-slate-500">
                Paid Amount
              </span>

              <span className="font-semibold">
                {payment.paid_amount_formatted}
              </span>

            </div>

            <div
              className="
                flex
                justify-between
              "
            >

              <span className="text-slate-500">
                Fee
              </span>

              <span className="font-semibold">
                {payment.fee_amount_formatted}
              </span>

            </div>

            <div
              className="
                flex
                justify-between
              "
            >

              <span className="text-slate-500">
                Refund
              </span>

              <span className="font-semibold text-red-600">
                {payment.refund_amount_formatted}
              </span>

            </div>

            <hr />

            <div
              className="
                flex
                justify-between
              "
            >

              <span className="font-semibold">
                Net Amount
              </span>

              <span className="font-bold">
                {payment.net_amount_formatted}
              </span>

            </div>

            <div
              className="
                flex
                justify-between
              "
            >

              <span className="font-semibold">
                Remaining
              </span>

              <span className="font-bold">
                {payment.remaining_amount_formatted}
              </span>

            </div>

          </div>

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

        {/* Left */}
        <div className="xl:col-span-2 space-y-6">

          {/* Order Information */}
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

            <h2
              className="
                text-lg
                font-bold
                mb-6
              "
            >
              Order Information
            </h2>

            <div className="space-y-4">

              <div>

                <p className="text-sm text-slate-500">
                  Order Number
                </p>

                <p className="font-semibold">
                  {payment.order?.order_number || "-"}
                </p>

              </div>

              <div>

                <button
                  onClick={() =>
                    payment.order?.order_number &&
                    navigate(
                      `/orders/${payment.order.order_number}`
                    )
                  }
                  disabled={!payment.order}
                  className="
                    px-4
                    py-2
                    rounded-xl
                    bg-slate-100
                    hover:bg-slate-200
                    disabled:opacity-50
                    transition
                  "
                >
                  Lihat Order
                </button>

              </div>

            </div>

          </div>

          {/* Notes */}
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

            <h2
              className="
                text-lg
                font-bold
                mb-6
              "
            >
              Notes
            </h2>

            <div
              className="
                bg-slate-50
                rounded-2xl
                p-5
                whitespace-pre-wrap
              "
            >
              {payment.notes || "-"}
            </div>

          </div>

          {/* Metadata */}
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

            <h2
              className="
                text-lg
                font-bold
                mb-6
              "
            >
              Metadata
            </h2>

            <pre
              className="
                bg-slate-50
                rounded-2xl
                p-5
                overflow-x-auto
                text-sm
              "
            >
              {
                payment.metadata
                  ? JSON.stringify(
                      payment.metadata,
                      null,
                      2
                    )
                  : "-"
              }
            </pre>

          </div>

          {/* Timeline */}
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

            <h2
              className="
                text-lg
                font-bold
                mb-6
              "
            >
              Timeline
            </h2>

            <div
              className="
                grid
                grid-cols-1
                md:grid-cols-2
                gap-6
              "
            >

              <div>

                <p className="text-sm text-slate-500">
                  Created
                </p>

                <p className="font-semibold">
                  {payment.created_at_human || "-"}
                </p>

                <p className="text-xs text-slate-400">
                  {payment.created_at || "-"}
                </p>

              </div>

              <div>

                <p className="text-sm text-slate-500">
                  Updated
                </p>

                <p className="font-semibold">
                  {payment.updated_at_human || "-"}
                </p>

                <p className="text-xs text-slate-400">
                  {payment.updated_at || "-"}
                </p>

              </div>

              <div>

                <p className="text-sm text-slate-500">
                  Paid At
                </p>

                <p className="font-semibold">
                  {payment.paid_at_human || "-"}
                </p>

                <p className="text-xs text-slate-400">
                  {payment.paid_at || "-"}
                </p>

              </div>

              <div>

                <p className="text-sm text-slate-500">
                  Expired At
                </p>

                <p className="font-semibold">
                  {payment.expired_at_human || "-"}
                </p>

                <p className="text-xs text-slate-400">
                  {payment.expired_at || "-"}
                </p>

              </div>

            </div>

          </div>

        </div>

        {/* Right */}
        <div>

          <div
            className="
              bg-white
              rounded-3xl
              border
              border-slate-200
              shadow-sm
              p-6
              sticky
              top-28
            "
          >

            <h2
              className="
                text-lg
                font-bold
                mb-6
              "
            >
              Actions
            </h2>

            <div className="space-y-3">

              {
                payment.status === "pending" && (
                  <>
                    <button
                      disabled={processing}
                      onClick={handleMarkPaid}
                      className="
                        w-full
                        py-3
                        rounded-2xl
                        bg-green-600
                        text-white
                        hover:bg-green-700
                        transition
                      "
                    >
                      Mark as Paid
                    </button>

                    <button
                      disabled={processing}
                      onClick={handleMarkFailed}
                      className="
                        w-full
                        py-3
                        rounded-2xl
                        bg-red-600
                        text-white
                        hover:bg-red-700
                        transition
                      "
                    >
                      Mark as Failed
                    </button>

                    <button
                      disabled={processing}
                      onClick={handleMarkExpired}
                      className="
                        w-full
                        py-3
                        rounded-2xl
                        bg-orange-600
                        text-white
                        hover:bg-orange-700
                        transition
                      "
                    >
                      Mark as Expired
                    </button>

                    <button
                      disabled={processing}
                      onClick={handleMarkCancelled}
                      className="
                        w-full
                        py-3
                        rounded-2xl
                        border
                        border-slate-300
                        hover:bg-slate-50
                        transition
                      "
                    >
                      Cancel Payment
                    </button>
                  </>
                )
              }

              {
                (
                  payment.status === "paid" ||
                  payment.status === "partial_refund"
                ) && (
                  <button
                    disabled={processing}
                    onClick={handleRefund}
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
                    Refund Payment
                  </button>
                )
              }

              <button
                disabled={processing}
                onClick={handleDelete}
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
                Delete Payment
              </button>

            </div>

          </div>

        </div>

      </div>

    </div>

  );

}