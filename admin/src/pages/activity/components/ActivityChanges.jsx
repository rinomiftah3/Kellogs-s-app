export default function ActivityChanges({
  oldValues = {},
  newValues = {},
}) {
  const fields = [
    ...new Set([
      ...Object.keys(oldValues),
      ...Object.keys(newValues),
    ]),
  ];

  if (fields.length === 0) {
    return (
      <div className="text-gray-500">
        Tidak ada perubahan data
      </div>
    );
  }

  return (
    <table className="w-full border">
      <thead>
        <tr className="bg-slate-100">
          <th className="p-3 border">
            Field
          </th>

          <th className="p-3 border">
            Old Value
          </th>

          <th className="p-3 border">
            New Value
          </th>
        </tr>
      </thead>

      <tbody>
        {fields.map((field) => (
          <tr key={field}>
            <td className="border p-3 font-medium">
              {field}
            </td>

            <td className="border p-3">
              {String(
                oldValues[field] ?? "-"
              )}
            </td>

            <td className="border p-3">
              {String(
                newValues[field] ?? "-"
              )}
            </td>
          </tr>
        ))}
      </tbody>
    </table>
  );
}