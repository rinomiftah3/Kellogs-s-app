import clsx from "clsx";

import {
  CheckCircle,
  Pencil,
  Trash2,
  LogIn,
  LogOut,
} from "lucide-react";

export default function EventBadge({
  event,
}) {
  let icon = null;
  let color = "";

  switch (event) {
    case "created":
    case "user_created":
    case "role_created":
      icon =
        <CheckCircle size={14} />;

      color =
        "bg-green-100 text-green-700";
      break;

    case "updated":
    case "user_updated":
    case "role_updated":
      icon =
        <Pencil size={14} />;

      color =
        "bg-yellow-100 text-yellow-700";
      break;

    case "deleted":
    case "user_deleted":
    case "role_deleted":
      icon =
        <Trash2 size={14} />;

      color =
        "bg-red-100 text-red-700";
      break;

    case "login":
      icon =
        <LogIn size={14} />;

      color =
        "bg-blue-100 text-blue-700";
      break;

    case "logout":
      icon =
        <LogOut size={14} />;

      color =
        "bg-slate-100 text-slate-700";
      break;

    default:
      color =
        "bg-gray-100 text-gray-700";
  }

  return (
    <span
      className={clsx(
        "inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold",
        color
      )}
    >
      {icon}
      {event}
    </span>
  );
}