export default function PaymentStatusBadge({
  status,
}) {

  const config = {

    pending: {
      label: "Pending",
      className: `
        bg-amber-100
        text-amber-700
        border-amber-200
      `,
    },

    paid: {
      label: "Paid",
      className: `
        bg-emerald-100
        text-emerald-700
        border-emerald-200
      `,
    },

    failed: {
      label: "Failed",
      className: `
        bg-red-100
        text-red-700
        border-red-200
      `,
    },

    refunded: {
      label: "Refunded",
      className: `
        bg-purple-100
        text-purple-700
        border-purple-200
      `,
    },

  };

  const badge =
    config[status] ?? {

      label:
        status
          ? status
              .replace(/_/g, " ")
              .replace(
                /\b\w/g,
                (char) =>
                  char.toUpperCase()
              )
          : "-",

      className: `
        bg-slate-100
        text-slate-700
        border-slate-200
      `,
    };

  return (

    <span
      className={`
        inline-flex
        items-center
        px-3
        py-1.5
        rounded-full
        border
        text-xs
        font-semibold
        whitespace-nowrap
        ${badge.className}
      `}
    >

      {badge.label}

    </span>

  );

}