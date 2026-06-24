import {
  useEffect,
  useMemo,
  useState,
} from "react";

import usePermission from "../../hooks/usePermission";

import {
  getActivityLogs,
  getActivityLog,
} from "../../services/activityService";

import {
  errorAlert,
} from "../../utils/alert";

import ActivityTable from "./components/ActivityTable";
import ActivityDetail from "./components/ActivityDetail";

import {
  Activity,
  Search,
  PlusCircle,
  Pencil,
  Trash2,
  UserCircle,
} from "lucide-react";

export default function ActivityLog() {

  const { can } = usePermission();

  /*
  |--------------------------------------------------------------------------
  | State
  |--------------------------------------------------------------------------
  */

  const [logs, setLogs] =
    useState([]);

  const [selectedLog, setSelectedLog] =
    useState(null);

  const [loading, setLoading] =
    useState(false);

  const [detailLoading, setDetailLoading] =
    useState(false);

  const [search, setSearch] =
    useState("");

  const [eventFilter, setEventFilter] =
    useState("");

  const [moduleFilter, setModuleFilter] =
    useState("");

  /*
  |--------------------------------------------------------------------------
  | Permissions
  |--------------------------------------------------------------------------
  */

  const canView =
    can("activity-logs.view") ||
    can("activity_logs.view");

  /*
  |--------------------------------------------------------------------------
  | Load Logs
  |--------------------------------------------------------------------------
  */

  const loadLogs = async () => {

    try {

      setLoading(true);

      const data =
        await getActivityLogs();

      setLogs(
        data || []
      );

    } catch (error) {

      console.error(error);

      setLogs([]);

      errorAlert(
        error?.response?.data?.message ||
        "Gagal mengambil activity logs"
      );

    } finally {

      setLoading(false);

    }

  };

  /*
  |--------------------------------------------------------------------------
  | Initial Load
  |--------------------------------------------------------------------------
  */

  useEffect(() => {

    if (canView) {

      loadLogs();

    }

  }, [canView]);

  /*
  |--------------------------------------------------------------------------
  | Statistics
  |--------------------------------------------------------------------------
  */

  const totalLogs =
    logs.length;

  const createdCount =
    logs.filter(
      (log) =>
        log.event === "created"
    ).length;

  const updatedCount =
    logs.filter(
      (log) =>
        log.event === "updated"
    ).length;

  const deletedCount =
    logs.filter(
      (log) =>
        log.event === "deleted"
    ).length;

  /*
  |--------------------------------------------------------------------------
  | Modules
  |--------------------------------------------------------------------------
  */

  const modules =
    useMemo(() => {

      return [
        ...new Set(
          logs
            .map(
              (log) =>
                log.log_name
            )
            .filter(Boolean)
        ),
      ];

    }, [logs]);

  /*
  |--------------------------------------------------------------------------
  | Filtering
  |--------------------------------------------------------------------------
  */

  const filteredLogs =
    useMemo(() => {

      return logs.filter(
        (log) => {

          const keyword =
            search.toLowerCase();

          const matchSearch =

            log.event
              ?.toLowerCase()
              .includes(keyword)

            ||

            log.log_name
              ?.toLowerCase()
              .includes(keyword)

            ||

            log.description
              ?.toLowerCase()
              .includes(keyword)

            ||

            log.causer?.name
              ?.toLowerCase()
              .includes(keyword);

          const matchEvent =

            !eventFilter ||

            log.event ===
              eventFilter;

          const matchModule =

            !moduleFilter ||

            log.log_name ===
              moduleFilter;

          return (
            matchSearch &&
            matchEvent &&
            matchModule
          );

        }
      );

    }, [
      logs,
      search,
      eventFilter,
      moduleFilter,
    ]);

  /*
  |--------------------------------------------------------------------------
  | Detail
  |--------------------------------------------------------------------------
  */

  const handleDetail =
    async (id) => {

      try {

        setDetailLoading(true);

        const data =
          await getActivityLog(
            id
          );

        setSelectedLog(
          data
        );

      } catch (error) {

        console.error(error);

        errorAlert(
          error?.response?.data?.message ||
          "Gagal mengambil detail aktivitas"
        );

      } finally {

        setDetailLoading(false);

      }

    };

  const closeDetail =
    () => {

      setSelectedLog(
        null
      );

    };

  /*
  |--------------------------------------------------------------------------
  | Unauthorized
  |--------------------------------------------------------------------------
  */

  if (!canView) {

    return (

      <div className="bg-white rounded-3xl p-10 shadow-sm border border-slate-100 text-center">

        <Activity className="w-14 h-14 mx-auto text-slate-300 mb-4" />

        <h2 className="text-2xl font-bold text-slate-800">
          Access Denied
        </h2>

        <p className="text-slate-500 mt-2">
          Anda tidak memiliki izin untuk melihat activity logs.
        </p>

      </div>

    );

  }
  return (
    <div className="space-y-6">

      {/* Hero */}
      <div
        className="
          rounded-3xl
          bg-gradient-to-r
          from-slate-900
          via-slate-800
          to-slate-900
          p-8
          text-white
          shadow-xl
          relative
          overflow-hidden
        "
      >

        <div className="absolute -top-20 -right-20 w-72 h-72 rounded-full bg-white/5" />

        <div className="absolute -bottom-24 -left-24 w-80 h-80 rounded-full bg-white/5" />

        <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">

          <div>

            <div className="flex items-center gap-4">

              <div
                className="
                  w-16
                  h-16
                  rounded-2xl
                  bg-white/10
                  backdrop-blur
                  flex
                  items-center
                  justify-center
                "
              >
                <Activity className="w-8 h-8 text-red-400" />
              </div>

              <div>

                <h1 className="text-4xl font-bold">
                  Activity Logs
                </h1>

                <p className="text-slate-300 mt-2">
                  Monitor all user activities and system changes.
                </p>

              </div>

            </div>

          </div>

          <div className="flex flex-wrap gap-3">

            <div className="bg-white/10 backdrop-blur rounded-2xl px-4 py-2 text-sm">
              Logs: {totalLogs}
            </div>

            <div className="bg-white/10 backdrop-blur rounded-2xl px-4 py-2 text-sm">
              Modules: {modules.length}
            </div>

          </div>

        </div>

      </div>

      {/* Statistics */}
      <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">

        {/* Total Logs */}
        <div className="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">

          <div className="flex items-center justify-between">

            <div>

              <p className="text-sm text-slate-500">
                Total Logs
              </p>

              <h3 className="text-4xl font-bold mt-2">
                {totalLogs}
              </h3>

            </div>

            <div className="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center">

              <Activity className="w-7 h-7 text-slate-700" />

            </div>

          </div>

        </div>

        {/* Created */}
        <div className="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">

          <div className="flex items-center justify-between">

            <div>

              <p className="text-sm text-slate-500">
                Created
              </p>

              <h3 className="text-4xl font-bold mt-2">
                {createdCount}
              </h3>

            </div>

            <div className="w-14 h-14 rounded-2xl bg-green-50 flex items-center justify-center">

              <PlusCircle className="w-7 h-7 text-green-600" />

            </div>

          </div>

        </div>

        {/* Updated */}
        <div className="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">

          <div className="flex items-center justify-between">

            <div>

              <p className="text-sm text-slate-500">
                Updated
              </p>

              <h3 className="text-4xl font-bold mt-2">
                {updatedCount}
              </h3>

            </div>

            <div className="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center">

              <Pencil className="w-7 h-7 text-blue-600" />

            </div>

          </div>

        </div>

        {/* Deleted */}
        <div className="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">

          <div className="flex items-center justify-between">

            <div>

              <p className="text-sm text-slate-500">
                Deleted
              </p>

              <h3 className="text-4xl font-bold mt-2">
                {deletedCount}
              </h3>

            </div>

            <div className="w-14 h-14 rounded-2xl bg-red-50 flex items-center justify-center">

              <Trash2 className="w-7 h-7 text-red-600" />

            </div>

          </div>

        </div>

      </div>

      {/* Search & Filter */}
      <div className="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-4">

          {/* Search */}
          <div className="relative">

            <Search
              className="
                absolute
                left-4
                top-1/2
                -translate-y-1/2
                w-5
                h-5
                text-slate-400
              "
            />

            <input
              type="text"
              placeholder="Cari aktivitas..."
              value={search}
              onChange={(e) =>
                setSearch(
                  e.target.value
                )
              }
              className="
                w-full
                rounded-2xl
                border
                border-slate-200
                pl-12
                pr-4
                py-3
                focus:outline-none
                focus:ring-2
                focus:ring-red-500
                focus:border-red-500
              "
            />

          </div>

          {/* Event Filter */}
          <select
            value={eventFilter}
            onChange={(e) =>
              setEventFilter(
                e.target.value
              )
            }
            className="
              rounded-2xl
              border
              border-slate-200
              px-4
              py-3
              focus:outline-none
              focus:ring-2
              focus:ring-red-500
            "
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

          {/* Module Filter */}
          <select
            value={moduleFilter}
            onChange={(e) =>
              setModuleFilter(
                e.target.value
              )
            }
            className="
              rounded-2xl
              border
              border-slate-200
              px-4
              py-3
              focus:outline-none
              focus:ring-2
              focus:ring-red-500
            "
          >

            <option value="">
              Semua Module
            </option>

            {modules.map(
              (module) => (

                <option
                  key={module}
                  value={module}
                >
                  {module}
                </option>

              )
            )}

          </select>

        </div>

      </div>
      {/* Activity Logs Table */}
      <div className="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">

        <div className="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

          <div>

            <h2 className="text-xl font-bold text-slate-900">
              Activity Logs
            </h2>

            <p className="text-sm text-slate-500 mt-1">
              Menampilkan {filteredLogs.length} aktivitas sistem.
            </p>

          </div>

          <div className="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-slate-50 text-sm text-slate-600">

            <UserCircle className="w-4 h-4" />

            Audit Trail

          </div>

        </div>

        <div className="overflow-x-auto">

          {loading ? (

            <div className="py-20 flex flex-col items-center justify-center">

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
                Loading activity logs...
              </p>

            </div>

          ) : filteredLogs.length === 0 ? (

            <div className="py-20 flex flex-col items-center justify-center px-6 text-center">

              <Activity className="w-16 h-16 text-slate-300 mb-5" />

              <h3 className="text-xl font-semibold text-slate-700">
                Tidak ada aktivitas ditemukan
              </h3>

              <p className="text-slate-500 mt-2 max-w-md">
                Aktivitas sistem akan muncul di sini setelah pengguna
                melakukan perubahan data.
              </p>

            </div>

          ) : (

            <ActivityTable
              logs={filteredLogs}
              onDetail={handleDetail}
            />

          )}

        </div>

      </div>

      {/* Detail Modal */}
      {(selectedLog || detailLoading) && (

        <ActivityDetail
          log={selectedLog}
          loading={detailLoading}
          onClose={closeDetail}
        />

      )}
    </div>
  );
}