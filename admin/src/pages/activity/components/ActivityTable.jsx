
import EventBadge from "../../../components/common/EventBadge.jsx";
export default function ActivityTable({
  logs,
  onDetail,
}) 
{
  return (
    <table className="w-full">
      <thead>
        <tr className="bg-slate-100">
          <th className="p-3">
            Event
          </th>

          <th className="p-3">
            User
          </th>

          <th className="p-3">
            Module
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
        {logs.map((log) => (
          <tr
            key={log.id}
            className="border-t"
          >
            <td className="p-3">
  <EventBadge
    event={log.event}
  />
</td>

            <td className="p-3">
              {log.causer?.name ||
                "-"}
            </td>

            <td className="p-3">
              {log.log_name}
            </td>

            <td className="p-3">
              {new Date(
                log.created_at
              ).toLocaleString()}
            </td>

            <td className="p-3">
              <button
                onClick={() =>
                  onDetail(log.id)
                }
                className="bg-blue-600 text-white px-3 py-2 rounded"
              >
                Detail
              </button>
            </td>
          </tr>
        ))}
      </tbody>
    </table>
  );
}