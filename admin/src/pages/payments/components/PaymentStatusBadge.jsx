import {
  PAYMENT_STATUS,
} from "../../services/paymentService";

export default function PaymentStatusBadge({
  status,
  className = "",
}) {

  const config = {

    [PAYMENT_STATUS.PENDING]: {

      label: "Pending",

      className:
        "bg-yellow-100 text-yellow-800",
    },

    [PAYMENT_STATUS.PAID]: {

      label: "Paid",

      className:
        "bg-green-100 text-green-800",
    },

    [PAYMENT_STATUS.FAILED]: {

      label: "Failed",

      className:
        "bg-red-100 text-red-800",
    },

    [PAYMENT_STATUS.EXPIRED]: {

      label: "Expired",

      className:
        "bg-orange-100 text-orange-800",
    },

    [PAYMENT_STATUS.CANCELLED]: {

      label: "Cancelled",

      className:
        "bg-slate-100 text-slate-800",
    },

    [PAYMENT_STATUS.REFUNDED]: {

      label: "Refunded",

      className:
        "bg-blue-100 text-blue-800",
    },

    [PAYMENT_STATUS.PARTIAL_REFUND]: {

      label: "Partial Refund",

      className:
        "bg-indigo-100 text-indigo-800",
    },
  };

  const badge =
    config[status] || {

      label:
        status
          ? status
              .replace(/_/g, " ")
              .replace(
                /\b\w/g,
                (char) =>
                  char.toUpperCase()
              )
          : "Unknown",

      className:
        "bg-slate-100 text-slate-700",
    };

  return (

    <span
      className={`
        inline-flex
        items-center
        px-3
        py-1
        rounded-full
        text-xs
        font-semibold
        ${badge.className}
        ${className}
      `}
    >

      {badge.label}

    </span>

  );

}