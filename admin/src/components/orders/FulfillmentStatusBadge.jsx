export default function FulfillmentStatusBadge({
  status,
}) {

  const config = {

    pending: {
      label: "Pending",
      className: `
        bg-slate-100
        text-slate-700
        border-slate-200
      `,
    },

    packed: {
      label: "Packed",
      className: `
        bg-indigo-100
        text-indigo-700
        border-indigo-200
      `,
    },

    shipped: {
      label: "Shipped",
      className: `
        bg-cyan-100
        text-cyan-700
        border-cyan-200
      `,
    },

    delivered: {
      label: "Delivered",
      className: `
        bg-emerald-100
        text-emerald-700
        border-emerald-200
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
        gap-2
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

      <span
        className="
          w-2
          h-2
          rounded-full
          bg-current
          opacity-70
        "
      />

      {badge.label}

    </span>

  );

}