import ActivityChanges from "./ActivityChanges";
import EventBadge from "../../../components/common/EventBadge.jsx";

import {
  X,
  User,
  CalendarDays,
  FileText,
  Hash,
  Activity,
} from "lucide-react";

export default function ActivityDetail({
  log,
  loading = false,
  onClose,
}) {

  if (!log && !loading) {
    return null;
  }

  const oldValues =
    log?.properties?.old || {};

  const newValues =
    log?.properties?.attributes || {};

  const userName =
    log?.causer?.name ||
    "System";

  const initial =
    userName
      ?.charAt(0)
      ?.toUpperCase();

  return (

    <div
      className="
        fixed
        inset-0
        z-50
        flex
        items-center
        justify-center
        bg-black/50
        backdrop-blur-sm
        p-4
      "
    >

      <div
        className="
          bg-white
          rounded-3xl
          shadow-2xl
          w-full
          max-w-6xl
          max-h-[90vh]
          overflow-hidden
          flex
          flex-col
        "
      >

        {/* Header */}
        <div
          className="
            px-8
            py-6
            border-b
            border-slate-100
            flex
            items-center
            justify-between
          "
        >

          <div className="flex items-center gap-4">

            <div
              className="
                w-14
                h-14
                rounded-2xl
                bg-red-50
                flex
                items-center
                justify-center
              "
            >
              <Activity className="w-7 h-7 text-red-600" />
            </div>

            <div>

              <h2 className="text-2xl font-bold text-slate-900">
                Activity Detail
              </h2>

              <p className="text-slate-500 mt-1">
                Detailed audit information.
              </p>

            </div>

          </div>

          <button
            onClick={onClose}
            className="
              w-11
              h-11
              rounded-2xl
              bg-slate-100
              hover:bg-slate-200
              transition
              flex
              items-center
              justify-center
            "
          >
            <X className="w-5 h-5 text-slate-600" />
          </button>

        </div>

        {/* Loading */}
        {loading ? (

          <div className="py-20 flex flex-col items-center">

            <div
              className="
                w-12
                h-12
                rounded-full
                border-4
                border-slate-200
                border-t-red-600
                animate-spin
              "
            />

            <p className="mt-4 text-slate-500">
              Loading activity detail...
            </p>

          </div>

        ) : (

          <div className="overflow-y-auto p-8 space-y-8">

            {/* Information */}
            <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

              {/* Event */}
              <div className="bg-slate-50 rounded-3xl p-5">

                <div className="flex items-center gap-2 mb-3">

                  <Activity className="w-5 h-5 text-slate-500" />

                  <span className="text-sm text-slate-500">
                    Event
                  </span>

                </div>

                <EventBadge
                  event={log.event}
                />

              </div>

              {/* Description */}
              <div className="bg-slate-50 rounded-3xl p-5">

                <div className="flex items-center gap-2 mb-3">

                  <FileText className="w-5 h-5 text-slate-500" />

                  <span className="text-sm text-slate-500">
                    Description
                  </span>

                </div>

                <p className="font-semibold text-slate-900 capitalize">
                  {log.description || "-"}
                </p>

              </div>

              {/* User */}
              <div className="bg-slate-50 rounded-3xl p-5">

                <div className="flex items-center gap-2 mb-3">

                  <User className="w-5 h-5 text-slate-500" />

                  <span className="text-sm text-slate-500">
                    User
                  </span>

                </div>

                <div className="flex items-center gap-3">

                  <div
                    className="
                      w-10
                      h-10
                      rounded-2xl
                      bg-red-100
                      text-red-700
                      font-bold
                      flex
                      items-center
                      justify-center
                    "
                  >
                    {initial}
                  </div>

                  <div>

                    <p className="font-semibold text-slate-900">
                      {userName}
                    </p>

                    <p className="text-xs text-slate-400">
                      ID #{log.causer_id || "-"}
                    </p>

                  </div>

                </div>

              </div>

              {/* Subject */}
              <div className="bg-slate-50 rounded-3xl p-5">

                <div className="flex items-center gap-2 mb-3">

                  <Hash className="w-5 h-5 text-slate-500" />

                  <span className="text-sm text-slate-500">
                    Subject ID
                  </span>

                </div>

                <p className="font-semibold text-slate-900">
                  {log.subject_id || "-"}
                </p>

              </div>

            </div>

            {/* Timestamp */}
            <div className="bg-white border border-slate-100 rounded-3xl p-6">

              <div className="flex items-center gap-2 mb-4">

                <CalendarDays className="w-5 h-5 text-slate-500" />

                <h3 className="font-semibold text-slate-900">
                  Activity Time
                </h3>

              </div>

              <p className="text-slate-700">

                {new Date(
                  log.created_at
                ).toLocaleString(
                  "id-ID",
                  {
                    day: "2-digit",
                    month: "long",
                    year: "numeric",
                    hour: "2-digit",
                    minute: "2-digit",
                    second: "2-digit",
                  }
                )}

              </p>

            </div>

            {/* Changes */}
            <div>

              <h3 className="text-xl font-bold text-slate-900 mb-5">
                Changes
              </h3>

              <ActivityChanges
                oldValues={oldValues}
                newValues={newValues}
              />

            </div>

          </div>

        )}

      </div>

    </div>

  );

}