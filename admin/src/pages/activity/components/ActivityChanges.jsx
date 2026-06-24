import {
  ArrowRight,
  Database,
} from "lucide-react";

export default function ActivityChanges({
  oldValues = {},
  newValues = {},
}) {

  /*
  |--------------------------------------------------------------------------
  | Changed Fields Only
  |--------------------------------------------------------------------------
  */

  const fields = [
    ...new Set([
      ...Object.keys(oldValues),
      ...Object.keys(newValues),
    ]),
  ].filter((field) => {

    return JSON.stringify(
      oldValues[field]
    ) !== JSON.stringify(
      newValues[field]
    );

  });

  /*
  |--------------------------------------------------------------------------
  | Format Value
  |--------------------------------------------------------------------------
  */

  const formatValue = (
    value
  ) => {

    if (
      value === null ||
      value === undefined ||
      value === ""
    ) {
      return "-";
    }

    if (
      typeof value === "object"
    ) {

      return JSON.stringify(
        value,
        null,
        2
      );

    }

    return String(value);

  };

  /*
  |--------------------------------------------------------------------------
  | Empty State
  |--------------------------------------------------------------------------
  */

  if (fields.length === 0) {

    return (

      <div
        className="
          bg-slate-50
          border
          border-slate-100
          rounded-3xl
          py-12
          px-6
          text-center
        "
      >

        <Database className="w-14 h-14 mx-auto text-slate-300 mb-4" />

        <h3 className="text-lg font-semibold text-slate-700">
          Tidak ada perubahan data
        </h3>

        <p className="text-slate-500 mt-2">
          Aktivitas ini tidak memiliki perubahan atribut.
        </p>

      </div>

    );

  }

  /*
  |--------------------------------------------------------------------------
  | Render
  |--------------------------------------------------------------------------
  */

  return (

    <div
      className="
        bg-white
        rounded-3xl
        border
        border-slate-100
        overflow-hidden
      "
    >

      {/* Header */}
      <div
        className="
          px-6
          py-5
          border-b
          border-slate-100
          bg-slate-50
        "
      >

        <h3 className="text-lg font-bold text-slate-900">
          Data Changes
        </h3>

        <p className="text-sm text-slate-500 mt-1">
          Perubahan atribut sebelum dan sesudah aktivitas dilakukan.
        </p>

      </div>

      {/* Desktop */}
      <div className="hidden lg:block overflow-x-auto">

        <table className="w-full">

          <thead className="bg-slate-50">

            <tr>

              <th
                className="
                  px-6
                  py-4
                  text-left
                  text-sm
                  font-semibold
                  text-slate-600
                "
              >
                Field
              </th>

              <th
                className="
                  px-6
                  py-4
                  text-left
                  text-sm
                  font-semibold
                  text-red-600
                "
              >
                Before
              </th>

              <th
                className="
                  px-6
                  py-4
                  text-center
                "
              >
              </th>

              <th
                className="
                  px-6
                  py-4
                  text-left
                  text-sm
                  font-semibold
                  text-green-600
                "
              >
                After
              </th>

            </tr>

          </thead>

          <tbody>

            {fields.map(
              (field) => (

                <tr
                  key={field}
                  className="
                    border-t
                    border-slate-100
                    hover:bg-slate-50
                    transition
                  "
                >

                  {/* Field */}
                  <td className="px-6 py-5">

                    <span
                      className="
                        font-semibold
                        text-slate-900
                      "
                    >
                      {field}
                    </span>

                  </td>

                  {/* Old */}
                  <td className="px-6 py-5">

                    <pre
                      className="
                        whitespace-pre-wrap
                        break-words
                        rounded-2xl
                        bg-red-50
                        px-4
                        py-3
                        text-sm
                        text-red-700
                        font-mono
                      "
                    >
                      {formatValue(
                        oldValues[field]
                      )}
                    </pre>

                  </td>

                  {/* Arrow */}
                  <td className="px-2 py-5 text-center">

                    <ArrowRight
                      className="
                        w-5
                        h-5
                        text-slate-400
                        mx-auto
                      "
                    />

                  </td>

                  {/* New */}
                  <td className="px-6 py-5">

                    <pre
                      className="
                        whitespace-pre-wrap
                        break-words
                        rounded-2xl
                        bg-green-50
                        px-4
                        py-3
                        text-sm
                        text-green-700
                        font-mono
                      "
                    >
                      {formatValue(
                        newValues[field]
                      )}
                    </pre>

                  </td>

                </tr>

              )
            )}

          </tbody>

        </table>

      </div>

      {/* Mobile */}
      <div className="lg:hidden p-5 space-y-4">

        {fields.map(
          (field) => (

            <div
              key={field}
              className="
                border
                border-slate-100
                rounded-2xl
                p-4
              "
            >

              <h4
                className="
                  font-semibold
                  text-slate-900
                  mb-4
                "
              >
                {field}
              </h4>

              <div className="space-y-3">

                <div>

                  <p
                    className="
                      text-xs
                      uppercase
                      font-semibold
                      text-red-600
                      mb-2
                    "
                  >
                    Before
                  </p>

                  <pre
                    className="
                      whitespace-pre-wrap
                      break-words
                      rounded-xl
                      bg-red-50
                      p-3
                      text-sm
                      text-red-700
                      font-mono
                    "
                  >
                    {formatValue(
                      oldValues[field]
                    )}
                  </pre>

                </div>

                <div>

                  <p
                    className="
                      text-xs
                      uppercase
                      font-semibold
                      text-green-600
                      mb-2
                    "
                  >
                    After
                  </p>

                  <pre
                    className="
                      whitespace-pre-wrap
                      break-words
                      rounded-xl
                      bg-green-50
                      p-3
                      text-sm
                      text-green-700
                      font-mono
                    "
                  >
                    {formatValue(
                      newValues[field]
                    )}
                  </pre>

                </div>

              </div>

            </div>

          )
        )}

      </div>

    </div>

  );

}