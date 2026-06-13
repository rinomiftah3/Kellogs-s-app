import ActivityChanges from "./ActivityChanges";

export default function ActivityDetail({
  log,
  onClose,
}) {
  if (!log) return null;

  const oldValues =
    log.properties?.old || {};

  const newValues =
    log.properties?.attributes || {};

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
      <div className="bg-white rounded-xl shadow-xl w-full max-w-5xl p-6">

        <div className="flex justify-between mb-6">
          <h2 className="text-2xl font-bold">
            Activity Detail
          </h2>

          <button
            onClick={onClose}
            className="px-4 py-2 bg-red-600 text-white rounded"
          >
            Close
          </button>
        </div>

        <div className="grid grid-cols-2 gap-6 mb-6">

          <div>
            <h4 className="font-semibold">
              Event
            </h4>

            <p>{log.event}</p>
          </div>

          <div>
            <h4 className="font-semibold">
              Description
            </h4>

            <p>{log.description}</p>
          </div>

          <div>
            <h4 className="font-semibold">
              User
            </h4>

            <p>
              {log.causer?.name ??
                "-"}
            </p>
          </div>

          <div>
            <h4 className="font-semibold">
              Date
            </h4>

            <p>
              {new Date(
                log.created_at
              ).toLocaleString()}
            </p>
          </div>

        </div>

        <ActivityChanges
          oldValues={oldValues}
          newValues={newValues}
        />

      </div>
    </div>
  );
}