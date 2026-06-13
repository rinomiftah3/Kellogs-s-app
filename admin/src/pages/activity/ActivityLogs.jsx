import { useEffect, useState } from "react";

import {
  getActivityLogs,
  getActivityLog,
} from "../../services/activityService";
import ActivityTable from "./components/ActivityTable";
import ActivityDetail from "./components/ActivityDetail";
import Swal from "sweetalert2";

export default function ActivityLogs() {
  const [logs, setLogs] =
    useState([]);

  const [loading, setLoading] =
    useState(false);

  const [search, setSearch] =
    useState("");

  const [event, setEvent] =
    useState("");
const [selectedLog, setSelectedLog] =
  useState(null);
  const [currentPage, setCurrentPage] =
    useState(1);

  const [lastPage, setLastPage] =
    useState(1);

  const loadLogs = async (
  page = 1
) => {
  try {

    setLoading(true);

    const result =
      await getActivityLogs({
        page,
        search,
        event,
      });

    setLogs(
      result?.data || []
    );

    setCurrentPage(
      result?.current_page || 1
    );

    setLastPage(
      result?.last_page || 1
    );

  } catch (error) {

    console.error(error);

    setLogs([]);
    setCurrentPage(1);
    setLastPage(1);

  } finally {

    setLoading(false);

  }
};

  useEffect(() => {
    loadLogs(1);
  }, [
    search,
    event,
  ]);

  const handleDetail = async (
  id
) => {
  try {

    const log =
      await getActivityLog(id);

    setSelectedLog(log);

  } catch (error) {

    console.error(error);

  }
};
  return (
    <div>
      <h1 className="text-3xl font-bold mb-6">
        Activity Logs
      </h1>

      <div className="bg-white rounded shadow p-4">
        <div className="flex gap-4 mb-4">
          <input
            type="text"
            placeholder="Search"
            value={search}
            onChange={(e) =>
              setSearch(
                e.target.value
              )
            }
            className="border p-3 rounded"
          />

          <select
            value={event}
            onChange={(e) =>
              setEvent(
                e.target.value
              )
            }
            className="border p-3 rounded"
          >
            <option value="">
              Semua Event
            </option>

            <option value="created">
              Created
            </option>

            <option value="updated">
              Updated
            </option>

            <option value="deleted">
              Deleted
            </option>
          </select>
        </div>

        <table className="w-full">
          <thead>
            <tr className="bg-slate-100">
              <th className="p-3">
                ID
              </th>

              <th className="p-3">
                Event
              </th>

              <th className="p-3">
                Description
              </th>

              <th className="p-3">
                User
              </th>

              <th className="p-3">
                Date
              </th>

              <th className="p-3">
                Action
              </th>
            </tr>
          </thead>

          <tbody>
            {loading ? (
              <tr>
                <td
                  colSpan="6"
                  className="p-4 text-center"
                >
                  Loading...
                </td>
              </tr>
            ) : logs.length === 0 ? (
              <tr>
                <td
                  colSpan="6"
                  className="p-4 text-center"
                >
                  Tidak ada data
                </td>
              </tr>
            ) : (
              logs.map(
                (log) => (
                  <tr
                    key={log.id}
                    className="border-t"
                  >
                    <td className="p-3">
                      {log.id}
                    </td>

                    <td className="p-3">
                      {log.event}
                    </td>

                    <td className="p-3">
                      {log.description}
                    </td>

                    <td className="p-3">
                      {
                        log.causer?.name ||
                        "-"
                      }
                    </td>

                    <td className="p-3">
  {log.created_at
    ? new Date(
        log.created_at
      ).toLocaleString()
    : "-"}
</td>
<ActivityDetail
  log={selectedLog}
  onClose={() =>
    setSelectedLog(null)
  }
/>
                    <td className="p-3">
                      <button
                        onClick={() =>
                          handleDetail(
                            log.id
                          )
                        }
                        className="bg-blue-600 text-white px-3 py-1 rounded"
                      >
                        Detail
                      </button>
                    </td>
                  </tr>
                )
              )
            )}
          </tbody>
        </table>

        <div className="flex justify-end gap-2 mt-4">
          <button
            disabled={
              currentPage === 1
            }
            onClick={() =>
              loadLogs(
                currentPage - 1
              )
            }
            className="px-4 py-2 bg-gray-300 rounded disabled:opacity-50"
          >
            Prev
          </button>

          <span className="px-4 py-2">
            {currentPage}
            {" / "}
            {lastPage}
          </span>

          <button
            disabled={
              currentPage ===
              lastPage
            }
            onClick={() =>
              loadLogs(
                currentPage + 1
              )
            }
            className="px-4 py-2 bg-gray-300 rounded disabled:opacity-50"
          >
            Next
          </button>
        </div>
      </div>
    </div>
  );
}