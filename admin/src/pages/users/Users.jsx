import { useEffect, useState } from "react";
import usePermission from "../../hooks/usePermission";

import {
  getUsers,
  createUser,
  updateUser,
  deleteUser,
} from "../../services/userService";

import {
  getRoles,
} from "../../services/roleService";

import {
  successAlert,
  errorAlert,
  confirmDelete,
} from "../../utils/alert";

export default function Users() {
  const { can } = usePermission();

  const [users, setUsers] =
    useState([]);

  const [roles, setRoles] =
    useState([]);

  const [loading, setLoading] =
    useState(false);

  const [submitting, setSubmitting] =
    useState(false);

  const [editingId, setEditingId] =
    useState(null);

  const [name, setName] =
    useState("");

  const [email, setEmail] =
    useState("");

  const [password, setPassword] =
    useState("");

  const [role, setRole] =
    useState("");

  const canCreate =
    can("users.create");

  const canUpdate =
    can("users.update");

  const canDelete =
    can("users.delete");

  const canSubmit =
    editingId ? canUpdate : canCreate;

  const loadUsers = async () => {
  try {

    setLoading(true);

    const users =
      await getUsers();

    setUsers(
      users || []
    );

  } catch (error) {

    console.error(error);

    setUsers([]);

    errorAlert(
      error?.response?.data?.message ||
      "Gagal mengambil data user"
    );

  } finally {

    setLoading(false);

  }
};

  const loadRoles = async () => {
  try {

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
      "Gagal mengambil role"
    );

  }
};
  useEffect(() => {
    loadUsers();
    loadRoles();
  }, []);

  const resetForm = () => {
    setEditingId(null);
    setName("");
    setEmail("");
    setPassword("");
    setRole("");
  };

  const handleEdit = (user) => {
    if (!canUpdate) return;

    setEditingId(user.id);
    setName(user.name);
    setEmail(user.email);
    setPassword("");
    setRole(user.roles?.[0]?.name || "");
  };

  const handleDelete = async (id) => {
    if (!canDelete) return;

    const result =
      await confirmDelete();

    if (!result.isConfirmed) {
      return;
    }

    try {
      await deleteUser(id);

      await successAlert(
        "User berhasil dihapus"
      );

      await loadUsers();
    } catch (error) {
      errorAlert(
        error?.response?.data?.message ||
        "Gagal menghapus user"
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
        "Nama wajib diisi"
      );
    }

    if (!email.trim()) {
      return errorAlert(
        "Email wajib diisi"
      );
    }

    if (!role) {
      return errorAlert(
        "Role wajib dipilih"
      );
    }
if (!editingId && !password) {

  return errorAlert(
    "Password wajib diisi"
  );
}
    try {
      setSubmitting(true);

      const payload = {
  name,
  email,
  role,
  ...(password && {
    password,
  }),
};

      if (editingId) {
        await updateUser(
          editingId,
          payload
        );

        await successAlert(
          "User berhasil diperbarui"
        );
      } else {
        

        await createUser(payload);

        await successAlert(
          "User berhasil dibuat"
        );
      }

      resetForm();
      await loadUsers();
    } catch (error) {
      console.error(error);

      const errors =
        error?.response?.data?.errors;

      if (errors) {
        const firstError =
          Object.values(errors)[0][0];

        return errorAlert(firstError);
      }

      errorAlert(
        error?.response?.data?.message ||
        "Gagal menyimpan user"
      );
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <div>
      <h1 className="text-3xl font-bold mb-6">
        Users
      </h1>

      {(canCreate || canUpdate) && (
        <form
          onSubmit={handleSubmit}
          className="bg-white p-4 rounded shadow mb-6"
        >
          <div className="grid grid-cols-2 gap-4">
            <input
              type="text"
              placeholder="Name"
              className="border p-3 rounded disabled:bg-slate-100"
              value={name}
              disabled={!canSubmit || submitting}
              onChange={(e) =>
                setName(e.target.value)
              }
            />

            <input
              type="email"
              placeholder="Email"
              className="border p-3 rounded disabled:bg-slate-100"
              value={email}
              disabled={!canSubmit || submitting}
              onChange={(e) =>
                setEmail(e.target.value)
              }
            />

            <input
              type="password"
              placeholder={
                editingId
                  ? "Kosongkan jika tidak diubah"
                  : "Password"
              }
              className="border p-3 rounded disabled:bg-slate-100"
              value={password}
              disabled={!canSubmit || submitting}
              onChange={(e) =>
                setPassword(e.target.value)
              }
            />

            <select
              value={role}
              disabled={!canSubmit || submitting}
              onChange={(e) =>
                setRole(e.target.value)
              }
              className="border p-3 rounded disabled:bg-slate-100"
            >
              <option value="">
                Pilih Role
              </option>

              {(roles || []).map((roleItem) => (
  <option
    key={roleItem.id}
    value={roleItem.name}
  >
    {roleItem.name}
  </option>
))}
            </select>
          </div>

          <div className="mt-4 flex gap-2">
            {canSubmit && (
              <button
                type="submit"
                disabled={submitting}
                className="bg-slate-900 text-white px-4 py-2 rounded disabled:opacity-50"
              >
                {submitting
                  ? "Saving..."
                  : editingId
                    ? "Update User"
                    : "Create User"}
              </button>
            )}

            {editingId && (
              <button
                type="button"
                onClick={resetForm}
                disabled={submitting}
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
              <th className="p-3">ID</th>
              <th className="p-3">Name</th>
              <th className="p-3">Email</th>
              <th className="p-3">Role</th>
              {(canUpdate || canDelete) && (
                <th className="p-3">Action</th>
              )}
            </tr>
          </thead>

          <tbody>

  {loading ? (

    <tr>
      <td
        colSpan={
          canUpdate || canDelete
            ? 5
            : 4
        }
        className="p-4 text-center"
      >
        Loading...
      </td>
    </tr>

  ) : users.length === 0 ? (

    <tr>
      <td
        colSpan={
          canUpdate || canDelete
            ? 5
            : 4
        }
        className="p-4 text-center"
      >
        Belum ada user
      </td>
    </tr>

  ) : (

    (users || []).map((user) => {

      const isSuperAdmin =
        user.roles?.some(
          (role) =>
            role.name ===
            "Super Admin"
        );

      return (
        <tr
          key={user.id}
          className="border-t"
        >
          <td className="p-3">
            {user.id}
          </td>

          <td className="p-3">
            {user.name}
          </td>

          <td className="p-3">
            {user.email}
          </td>

          <td className="p-3">
            <span className="bg-blue-100 text-blue-700 px-2 py-1 rounded text-sm">
              {user.roles?.length > 0
                ? user.roles[0].name
                : "-"}
            </span>
          </td>

          {(canUpdate || canDelete) && (
            <td className="p-3">

              {canUpdate &&
                !isSuperAdmin && (
                  <button
                    onClick={() =>
                      handleEdit(user)
                    }
                    className="bg-blue-600 text-white px-3 py-1 rounded mr-2"
                  >
                    Edit
                  </button>
              )}

              {canDelete &&
                !isSuperAdmin && (
                  <button
                    onClick={() =>
                      handleDelete(user.id)
                    }
                    className="bg-red-600 text-white px-3 py-1 rounded"
                  >
                    Delete
                  </button>
              )}

            </td>
          )}

        </tr>
      );
    })

  )}

</tbody>
        </table>
      </div>
    </div>
  );
}
