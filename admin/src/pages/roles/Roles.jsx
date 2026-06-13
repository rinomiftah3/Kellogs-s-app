import { useEffect, useState } from "react";
import usePermission from "../../hooks/usePermission";

import {
  getRoles,
  getPermissions,
  createRole,
  updateRole,
  deleteRole,
} from "../../services/roleService";

import {
  successAlert,
  errorAlert,
  confirmDelete,
} from "../../utils/alert";

export default function Roles() {
  const { can } = usePermission();

  const [roles, setRoles] =
    useState([]);

  const [permissions, setPermissions] =
    useState([]);

  const [loading, setLoading] =
    useState(false);

  const [submitting, setSubmitting] =
    useState(false);

  const [editingId, setEditingId] =
    useState(null);

  const [name, setName] =
    useState("");

  const [
    selectedPermissions,
    setSelectedPermissions,
  ] = useState([]);

  const canCreate =
    can("roles.create");

  const canUpdate =
    can("roles.update");

  const canDelete =
    can("roles.delete");

  const canSubmit =
    editingId ? canUpdate : canCreate;

  const loadRoles = async () => {
  try {
    setLoading(true);

    const roles =
      await getRoles();

    setRoles(
      roles || []
    );
  } catch (error) {
    console.error(error);

    setRoles([]);

    errorAlert(
      error?.response?.data?.message ||
      "Gagal mengambil data role"
    );
  } finally {
    setLoading(false);
  }
};

  const loadPermissions = async () => {
  try {
    const permissions =
      await getPermissions();

    setPermissions(
      permissions || []
    );
  } catch (error) {
    console.error(error);

    setPermissions([]);

    errorAlert(
      error?.response?.data?.message ||
      "Gagal mengambil permissions"
    );
  }
};

  useEffect(() => {
  const init = async () => {
    await Promise.all([
      loadRoles(),
      loadPermissions(),
    ]);
  };

  init();
}, []);

  const resetForm = () => {
    setEditingId(null);
    setName("");
    setSelectedPermissions([]);
  };

  const handlePermissionChange = (
    permission
  ) => {
    if (!canSubmit) return;

    if (
      selectedPermissions.includes(
        permission
      )
    ) {
      setSelectedPermissions(
  (prev) =>
    prev.filter(
      (item) =>
        item !== permission
    )
);
    } else {
      setSelectedPermissions(
  (prev) => [
    ...prev,
    permission,
  ]
);
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();

    if (!canSubmit) {
      return errorAlert(
        "Anda tidak memiliki izin untuk aksi ini"
      );
    }

    if (!name.trim()) {
      return errorAlert(
        "Nama role wajib diisi"
      );
    }

    try {
      setSubmitting(true);

      const payload = {
        name,
        permissions:
          selectedPermissions,
      };

      if (editingId) {
        await updateRole(
          editingId,
          payload
        );

        await successAlert(
          "Role berhasil diperbarui"
        );
      } else {
        await createRole(
          payload
        );

        await successAlert(
          "Role berhasil dibuat"
        );
      }

      resetForm();

      await loadRoles();
    } catch (error) {
      const errors =
        error?.response?.data
          ?.errors;

      if (errors) {
        const firstError =
          Object.values(errors)[0][0];

        return errorAlert(
          firstError
        );
      }

      errorAlert(
        error?.response?.data
          ?.message ||
          "Gagal menyimpan role"
      );
    } finally {
      setSubmitting(false);
    }
  };

  const handleEdit = (role) => {
    if (!canUpdate) return;

    setEditingId(role.id);

    setName(role.name);

    setSelectedPermissions(
      role.permissions?.map(
        (permission) =>
          permission.name
      ) || []
    );
  };

  const handleDelete = async (
    id
  ) => {
    if (!canDelete) return;

    const result =
      await confirmDelete();

    if (!result.isConfirmed) {
      return;
    }

    try {
      await deleteRole(id);

      await successAlert(
        "Role berhasil dihapus"
      );

      await loadRoles();
    } catch (error) {
      errorAlert(
        error?.response?.data
          ?.message ||
          "Gagal menghapus role"
      );
    }
  };

  return (
    <div>
      <h1 className="text-3xl font-bold mb-6">
        Roles Management
      </h1>

      {(canCreate ||
        canUpdate) && (
        <form
          onSubmit={
            handleSubmit
          }
          className="bg-white p-5 rounded shadow mb-6"
        >
          <input
            type="text"
            placeholder="Role Name"
            className="border p-3 rounded w-full mb-4 disabled:bg-slate-100"
            value={name}
            disabled={
              !canSubmit ||
              submitting
            }
            onChange={(e) =>
              setName(
                e.target.value
              )
            }
          />

          <div className="grid grid-cols-3 gap-3">
            {(permissions || []).map((permission) => (
                <label
                  key={
                    permission.id
                  }
                  className="flex items-center gap-2"
                >
                  <input
                    type="checkbox"
                    disabled={
                      !canSubmit ||
                      submitting
                    }
                    checked={selectedPermissions.includes(
                      permission.name
                    )}
                    onChange={() =>
                      handlePermissionChange(
                        permission.name
                      )
                    }
                  />

                  {
                    permission.name
                  }
                </label>
              )
            )}
          </div>

          <div className="mt-4 flex gap-2">
            {canSubmit && (
              <button
                type="submit"
                disabled={
                  submitting
                }
                className="bg-slate-900 text-white px-4 py-2 rounded disabled:opacity-50"
              >
                {submitting
                  ? "Saving..."
                  : editingId
                  ? "Update Role"
                  : "Create Role"}
              </button>
            )}

            {editingId && (
              <button
                type="button"
                onClick={
                  resetForm
                }
                disabled={
                  submitting
                }
                className="bg-gray-500 text-white px-4 py-2 rounded disabled:opacity-50"
              >
                Cancel
              </button>
            )}
          </div>
        </form>
      )}

      <div className="bg-white rounded shadow overflow-hidden">
        <table className="w-full">
          <thead>
            <tr className="bg-slate-100">
              <th className="p-3">
                ID
              </th>
              <th className="p-3">
                Name
              </th>
              <th className="p-3">
                Permissions
              </th>

              {(canUpdate ||
                canDelete) && (
                <th className="p-3">
                  Action
                </th>
              )}
            </tr>
          </thead>

          <tbody>
            {loading ? (
              <tr>
                <td
                  colSpan={
                    canUpdate ||
                    canDelete
                      ? 4
                      : 3
                  }
                  className="p-4"
                >
                  Loading...
                </td>
              </tr>
            ) : roles.length ===
              0 ? (
              <tr>
                <td
                  colSpan={
                    canUpdate ||
                    canDelete
                      ? 4
                      : 3
                  }
                  className="p-4 text-center"
                >
                  Belum ada role
                </td>
              </tr>
            ) : (
              roles.map(
                (role) => (
                  <tr
                    key={
                      role.id
                    }
                    className="border-t"
                  >
                    <td className="p-3">
                      {
                        role.id
                      }
                    </td>

                    <td className="p-3">
                      {
                        role.name
                      }
                    </td>

                    <td className="p-3">
                      <div className="flex flex-wrap gap-1">
                        {(role.permissions || []).map(
                          (
                            permission,
                            index
                          ) => (
                            <span
                              key={`${permission.name}-${index}`}
                              className="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs"
                            >
                              {
                                permission.name
                              }
                            </span>
                          )
                        )}
                      </div>
                    </td>

                    {(canUpdate ||
                      canDelete) && (
                      <td className="p-3">
                        {canUpdate &&
                          role.name !==
                            "Super Admin" && (
                            <button
                              onClick={() =>
                                handleEdit(
                                  role
                                )
                              }
                              className="bg-blue-600 text-white px-3 py-1 rounded mr-2"
                            >
                              Edit
                            </button>
                          )}

                        {canDelete &&
                          role.name !==
                            "Super Admin" && (
                            <button
                              onClick={() =>
                                handleDelete(
                                  role.id
                                )
                              }
                              className="bg-red-600 text-white px-3 py-1 rounded"
                            >
                              Delete
                            </button>
                          )}
                      </td>
                    )}
                  </tr>
                )
              )
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}