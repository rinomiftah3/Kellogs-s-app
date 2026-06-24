import {
  Clock3,
  CheckCircle2,
  XCircle,
  HelpCircle,
} from "lucide-react";

export default function ReviewStatusBadge({
  status,
  className = "",
}) {

  /*
  |--------------------------------------------------------------------------
  | Normalize Status
  |--------------------------------------------------------------------------
  */

  const normalizedStatus =
    String(status || "")
      .toLowerCase()
      .trim();

  /*
  |--------------------------------------------------------------------------
  | Variants
  |--------------------------------------------------------------------------
  */

  const variants = {

    pending: {

      label: "Pending",

      icon: Clock3,

      className: `
        bg-amber-50
        text-amber-700
        border-amber-200
      `,
    },

    approved: {

      label: "Approved",

      icon: CheckCircle2,

      className: `
        bg-green-50
        text-green-700
        border-green-200
      `,
    },

    rejected: {

      label: "Rejected",

      icon: XCircle,

      className: `
        bg-red-50
        text-red-700
        border-red-200
      `,
    },

  };

  /*
  |--------------------------------------------------------------------------
  | Config
  |--------------------------------------------------------------------------
  */

  const config =
    variants[
      normalizedStatus
    ] || {

      label:
        normalizedStatus
          ? normalizedStatus
              .charAt(0)
              .toUpperCase()
              +
            normalizedStatus.slice(
              1
            )
          : "Unknown",

      icon: HelpCircle,

      className: `
        bg-slate-50
        text-slate-700
        border-slate-200
      `,
    };

  const Icon =
    config.icon;

  return (

    <span
      className={`
        inline-flex
        items-center
        gap-2
        px-3
        py-1.5
        rounded-full
        border
        text-xs
        font-semibold
        whitespace-nowrap
        ${config.className}
        ${className}
      `}
    >

      <Icon
        className="
          w-4
          h-4
          shrink-0
        "
      />

      <span>
        {config.label}
      </span>

    </span>

  );

}

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

export const isPendingReview =
  (review) =>
    String(
      review?.status || ""
    ).toLowerCase() ===
    "pending";

export const isApprovedReview =
  (review) =>
    String(
      review?.status || ""
    ).toLowerCase() ===
    "approved";

export const isRejectedReview =
  (review) =>
    String(
      review?.status || ""
    ).toLowerCase() ===
    "rejected";