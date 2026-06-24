export default function OrderStatusBadge({
  status,
}) {

  const config = {

    pending: {
      label: "Pending",
      className:
        `
          bg-amber-100
          text-amber-700
          border-amber-200
        `,
    },

    confirmed: {
      label: "Confirmed",
      className:
        `
          bg-blue-100
          text-blue-700
          border-blue-200
        `,
    },

    processing: {
      label: "Processing",
      className:
        `
          bg-indigo-100
          text-indigo-700
          border-indigo-200
        `,
    },

    shipped: {
      label: "Shipped",
      className:
        `
          bg-cyan-100
          text-cyan-700
          border-cyan-200
        `,
    },

    completed: {
      label: "Completed",
      className:
        `
          bg-emerald-100
          text-emerald-700
          border-emerald-200
        `,
    },

    cancelled: {
      label: "Cancelled",
      className:
        `
          bg-red-100
          text-red-700
          border-red-200
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

      className:
        `
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