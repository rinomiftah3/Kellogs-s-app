import EventBadge from "../../../components/common/EventBadge.jsx";

import {
  Eye,
  UserCircle2,
  CalendarDays,
  Boxes,
} from "lucide-react";

export default function ActivityTable({
  logs = [],
  onDetail,
}) {

  return (

    <table className="w-full">

      <thead className="bg-slate-50">

        <tr>

          <th className="px-6 py-4 text-left text-sm font-semibold text-slate-600">
            Event
          </th>

          <th className="px-6 py-4 text-left text-sm font-semibold text-slate-600">
            User
          </th>

          <th className="px-6 py-4 text-left text-sm font-semibold text-slate-600">
            Module
          </th>

          <th className="px-6 py-4 text-left text-sm font-semibold text-slate-600">
            Date
          </th>

          <th className="px-6 py-4 text-right text-sm font-semibold text-slate-600">
            Action
          </th>

        </tr>

      </thead>

      <tbody>

        {logs.map((log) => {

          const userName =
            log.causer?.name ||
            "System";

          const initial =
            userName
              ?.charAt(0)
              ?.toUpperCase();

          return (

            <tr
              key={log.id}
              className="
                border-t
                border-slate-100
                hover:bg-slate-50
                transition
              "
            >

              {/* Event */}
              <td className="px-6 py-5">

                <EventBadge
                  event={log.event}
                />

              </td>

              {/* User */}
              <td className="px-6 py-5">

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

              </td>

              {/* Module */}
              <td className="px-6 py-5">

                <span
                  className="
                    inline-flex
                    items-center
                    gap-2
                    px-3
                    py-1
                    rounded-full
                    bg-blue-100
                    text-blue-700
                    text-xs
                    font-semibold
                  "
                >

                  <Boxes className="w-3 h-3" />

                  {log.log_name || "-"}

                </span>

              </td>

              {/* Date */}
              <td className="px-6 py-5">

                <div className="flex items-start gap-2">

                  <CalendarDays
                    className="
                      w-4
                      h-4
                      text-slate-400
                      mt-0.5
                    "
                  />

                  <div>

                    <p className="text-sm text-slate-700">

                      {new Date(
                        log.created_at
                      ).toLocaleDateString(
                        "id-ID",
                        {
                          day: "2-digit",
                          month: "short",
                          year: "numeric",
                        }
                      )}

                    </p>

                    <p className="text-xs text-slate-400">

                      {new Date(
                        log.created_at
                      ).toLocaleTimeString(
                        "id-ID",
                        {
                          hour: "2-digit",
                          minute: "2-digit",
                        }
                      )}

                    </p>

                  </div>

                </div>

              </td>

              {/* Action */}
              <td className="px-6 py-5">

                <div className="flex justify-end">

                  <button
                    onClick={() =>
                      onDetail(log.id)
                    }
                    className="
                      inline-flex
                      items-center
                      gap-2
                      px-4
                      py-2
                      rounded-xl
                      bg-blue-50
                      text-blue-700
                      hover:bg-blue-100
                      transition
                    "
                  >

                    <Eye className="w-4 h-4" />

                    Detail

                  </button>

                </div>

              </td>

            </tr>

          );

        })}

      </tbody>

    </table>

  );

}