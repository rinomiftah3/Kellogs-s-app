import {
  useEffect,
  useMemo,
  useState,
} from "react";

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

import {
  Users as UsersIcon,
  UserPlus,
  Search,
  Pencil,
  Trash2,
  Shield,
  Mail,
  Lock,
  UserCog,
} from "lucide-react";

export default function Users() {

  const { can } = usePermission();

  /*
  |--------------------------------------------------------------------------
  | State
  |--------------------------------------------------------------------------
  */

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

  const [search, setSearch] =
    useState("");

  /*
  |--------------------------------------------------------------------------
  | Permissions
  |--------------------------------------------------------------------------
  */

  const canCreate =
    can("users.create");

  const canUpdate =
    can("users.update");

  const canDelete =
    can("users.delete");

  const canSubmit =
    editingId
      ? canUpdate
      : canCreate;

  /*
  |--------------------------------------------------------------------------
  | Load Users
  |--------------------------------------------------------------------------
  */

  const loadUsers = async () => {

    try {

      setLoading(true);

      const data =
        await getUsers();

      setUsers(
        data || []
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

  /*
  |--------------------------------------------------------------------------
  | Load Roles
  |--------------------------------------------------------------------------
  */

  const loadRoles = async () => {

    try {

      const data =
        await getRoles();

      setRoles(
        data || []
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

  /*
  |--------------------------------------------------------------------------
  | Initial Load
  |--------------------------------------------------------------------------
  */

  useEffect(() => {

    const init = async () => {

      await Promise.all([
        loadUsers(),
        loadRoles(),
      ]);

    };

    init();

  }, []);

  /*
  |--------------------------------------------------------------------------
  | Filter Users
  |--------------------------------------------------------------------------
  */

  const filteredUsers =
    useMemo(() => {

      return users.filter(
        (user) => {

          const keyword =
            search.toLowerCase();

          const matchName =
            user.name
              ?.toLowerCase()
              .includes(keyword);

          const matchEmail =
            user.email
              ?.toLowerCase()
              .includes(keyword);

          const matchRole =
            user.roles?.some(
              (role) =>
                role.name
                  ?.toLowerCase()
                  .includes(keyword)
            );

          return (
            matchName ||
            matchEmail ||
            matchRole
          );

        }
      );

    }, [
      users,
      search,
    ]);

  /*
  |--------------------------------------------------------------------------
  | Statistics
  |--------------------------------------------------------------------------
  */

  const totalUsers =
    users.length;

  const totalRoles =
    roles.length;

  const superAdmins =
    users.filter(
      (user) =>
        user.roles?.some(
          (role) =>
            role.name ===
            "Super Admin"
        )
    ).length;

  const staffAccounts =
    totalUsers -
    superAdmins;

  /*
  |--------------------------------------------------------------------------
  | Reset Form
  |--------------------------------------------------------------------------
  */

  const resetForm = () => {

    setEditingId(null);

    setName("");

    setEmail("");

    setPassword("");

    setRole("");

  };
  /*
  |--------------------------------------------------------------------------
  | Edit User
  |--------------------------------------------------------------------------
  */

  const handleEdit = (
    user
  ) => {

    if (!canUpdate) return;

    const isSuperAdmin =
      user.roles?.some(
        (role) =>
          role.name ===
          "Super Admin"
      );

    if (isSuperAdmin) {
      return;
    }

    setEditingId(
      user.id
    );

    setName(
      user.name || ""
    );

    setEmail(
      user.email || ""
    );

    setPassword("");

    setRole(
      user.roles?.[0]?.name || ""
    );

    window.scrollTo({
      top: 0,
      behavior: "smooth",
    });

  };

  /*
  |--------------------------------------------------------------------------
  | Delete User
  |--------------------------------------------------------------------------
  */

  const handleDelete = async (
    id
  ) => {

    if (!canDelete) return;

    const result =
      await confirmDelete();

    if (
      !result.isConfirmed
    ) {
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

  /*
  |--------------------------------------------------------------------------
  | Submit Form
  |--------------------------------------------------------------------------
  */

  const handleSubmit =
    async (e) => {

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

      if (
        !editingId &&
        !password
      ) {

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

          await createUser(
            payload
          );

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
            Object.values(errors)[0]?.[0];

          return errorAlert(
            firstError
          );

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
                <UsersIcon className="w-8 h-8 text-red-400" />
              </div>

              <div>

                <h1 className="text-4xl font-bold">
                  Users Management
                </h1>

                <p className="text-slate-300 mt-2">
                  Manage admin accounts and access across your organization.
                </p>

              </div>

            </div>

          </div>

          <div className="flex flex-wrap gap-3">

            <div className="bg-white/10 backdrop-blur rounded-2xl px-4 py-2 text-sm">
              Users: {totalUsers}
            </div>

            <div className="bg-white/10 backdrop-blur rounded-2xl px-4 py-2 text-sm">
              Roles: {totalRoles}
            </div>

          </div>

        </div>

      </div>

      {/* Statistics */}
      <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">

        <div className="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">

          <div className="flex items-center justify-between">

            <div>

              <p className="text-sm text-slate-500">
                Total Users
              </p>

              <h3 className="text-4xl font-bold mt-2">
                {totalUsers}
              </h3>

            </div>

            <div className="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center">

              <UsersIcon className="w-7 h-7 text-blue-600" />

            </div>

          </div>

        </div>

        <div className="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">

          <div className="flex items-center justify-between">

            <div>

              <p className="text-sm text-slate-500">
                Total Roles
              </p>

              <h3 className="text-4xl font-bold mt-2">
                {totalRoles}
              </h3>

            </div>

            <div className="w-14 h-14 rounded-2xl bg-green-50 flex items-center justify-center">

              <Shield className="w-7 h-7 text-green-600" />

            </div>

          </div>

        </div>

        <div className="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">

          <div className="flex items-center justify-between">

            <div>

              <p className="text-sm text-slate-500">
                Super Admin
              </p>

              <h3 className="text-4xl font-bold mt-2">
                {superAdmins}
              </h3>

            </div>

            <div className="w-14 h-14 rounded-2xl bg-red-50 flex items-center justify-center">

              <Lock className="w-7 h-7 text-red-600" />

            </div>

          </div>

        </div>

        <div className="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">

          <div className="flex items-center justify-between">

            <div>

              <p className="text-sm text-slate-500">
                Staff Accounts
              </p>

              <h3 className="text-4xl font-bold mt-2">
                {staffAccounts}
              </h3>

            </div>

            <div className="w-14 h-14 rounded-2xl bg-amber-50 flex items-center justify-center">

              <UserCog className="w-7 h-7 text-amber-600" />

            </div>

          </div>

        </div>

      </div>
      {/* Search */}
      <div className="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">

        <div className="relative max-w-md">

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
            placeholder="Cari nama, email, atau role..."
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

      </div>

      {/* Form */}
      {(canCreate || canUpdate) && (

        <div className="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">

          <div className="flex items-center gap-3 mb-8">

            <div className="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center">

              <UserPlus className="w-6 h-6 text-red-600" />

            </div>

            <div>

              <h2 className="text-2xl font-bold">

                {editingId
                  ? "Edit User"
                  : "Create User"}

              </h2>

              <p className="text-slate-500">

                Tambahkan atau kelola akun pengguna.

              </p>

            </div>

          </div>

          <form
            onSubmit={handleSubmit}
            className="space-y-6"
          >

            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">

              {/* Name */}
              <div>

                <label className="block text-sm font-medium text-slate-700 mb-2">

                  Full Name

                </label>

                <input
                  type="text"
                  placeholder="Masukkan nama lengkap"
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
                  className="
                    w-full
                    rounded-2xl
                    border
                    border-slate-200
                    px-4
                    py-3
                    focus:outline-none
                    focus:ring-2
                    focus:ring-red-500
                    disabled:bg-slate-100
                  "
                />

              </div>

              {/* Email */}
              <div>

                <label className="block text-sm font-medium text-slate-700 mb-2">

                  Email Address

                </label>

                <div className="relative">

                  <Mail
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
                    type="email"
                    placeholder="Masukkan email"
                    value={email}
                    disabled={
                      !canSubmit ||
                      submitting
                    }
                    onChange={(e) =>
                      setEmail(
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
                      disabled:bg-slate-100
                    "
                  />

                </div>

              </div>

              {/* Password */}
              <div>

                <label className="block text-sm font-medium text-slate-700 mb-2">

                  Password

                </label>

                <div className="relative">

                  <Lock
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
                    type="password"
                    placeholder={
                      editingId
                        ? "Kosongkan jika tidak diubah"
                        : "Masukkan password"
                    }
                    value={password}
                    disabled={
                      !canSubmit ||
                      submitting
                    }
                    onChange={(e) =>
                      setPassword(
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
                      disabled:bg-slate-100
                    "
                  />

                </div>

              </div>

              {/* Role */}
              <div>

                <label className="block text-sm font-medium text-slate-700 mb-2">

                  Role

                </label>

                <select
                  value={role}
                  disabled={
                    !canSubmit ||
                    submitting
                  }
                  onChange={(e) =>
                    setRole(
                      e.target.value
                    )
                  }
                  className="
                    w-full
                    rounded-2xl
                    border
                    border-slate-200
                    px-4
                    py-3
                    focus:outline-none
                    focus:ring-2
                    focus:ring-red-500
                    disabled:bg-slate-100
                  "
                >

                  <option value="">
                    Pilih Role
                  </option>

                  {(roles || []).map(
                    (roleItem) => (

                      <option
                        key={roleItem.id}
                        value={roleItem.name}
                      >
                        {roleItem.name}
                      </option>

                    )
                  )}

                </select>

              </div>

            </div>

            {/* Actions */}
            <div className="flex gap-3 pt-2">

              {canSubmit && (

                <button
                  type="submit"
                  disabled={submitting}
                  className="
                    inline-flex
                    items-center
                    gap-2
                    px-6
                    py-3
                    rounded-2xl
                    bg-gradient-to-r
                    from-red-600
                    to-red-700
                    text-white
                    font-semibold
                    shadow-lg
                    hover:shadow-xl
                    hover:scale-[1.01]
                    transition-all
                    disabled:opacity-70
                    disabled:cursor-not-allowed
                  "
                >

                  <UserPlus className="w-5 h-5" />

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
                  className="
                    px-6
                    py-3
                    rounded-2xl
                    border
                    border-slate-200
                    text-slate-700
                    hover:bg-slate-50
                    transition
                  "
                >
                  Cancel
                </button>

              )}

            </div>

          </form>

        </div>

      )}
      {/* Users Table */}
      <div className="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">

        <div className="px-6 py-5 border-b border-slate-100">

          <h2 className="text-xl font-bold text-slate-900">
            Users List
          </h2>

          <p className="text-sm text-slate-500 mt-1">
            Menampilkan {filteredUsers.length} user.
          </p>

        </div>

        <div className="overflow-x-auto">

          <table className="w-full">

            <thead className="bg-slate-50">

              <tr>

                <th className="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                  User
                </th>

                <th className="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                  Email
                </th>

                <th className="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                  Role
                </th>

                <th className="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                  Status
                </th>

                {(canUpdate || canDelete) && (

                  <th className="px-6 py-4 text-right text-sm font-semibold text-slate-600">
                    Actions
                  </th>

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
                    className="py-16 text-center text-slate-500"
                  >
                    Loading users...
                  </td>

                </tr>

              ) : filteredUsers.length === 0 ? (

                <tr>

                  <td
                    colSpan={
                      canUpdate || canDelete
                        ? 5
                        : 4
                    }
                    className="py-20 text-center"
                  >

                    <div className="flex flex-col items-center">

                      <UsersIcon className="w-16 h-16 text-slate-300 mb-4" />

                      <h3 className="text-lg font-semibold text-slate-700">
                        Belum ada user
                      </h3>

                      <p className="text-slate-500 mt-2">
                        Tambahkan user untuk mulai mengelola sistem.
                      </p>

                    </div>

                  </td>

                </tr>

              ) : (

                filteredUsers.map(
                  (user) => {

                    const isSuperAdmin =
                      user.roles?.some(
                        (role) =>
                          role.name ===
                          "Super Admin"
                      );

                    return (

                      <tr
                        key={user.id}
                        className="
                          border-t
                          border-slate-100
                          hover:bg-slate-50
                          transition
                        "
                      >

                        {/* User */}
                        <td className="px-6 py-5">

                          <div className="flex items-center gap-4">

                            <div
                              className="
                                w-12
                                h-12
                                rounded-2xl
                                bg-red-100
                                text-red-700
                                font-bold
                                flex
                                items-center
                                justify-center
                              "
                            >
                              {user.name
                                ?.charAt(0)
                                ?.toUpperCase()}
                            </div>

                            <div>

                              <p className="font-semibold text-slate-900">
                                {user.name}
                              </p>

                              <p className="text-xs text-slate-400 mt-1">
                                ID #{user.id}
                              </p>

                            </div>

                          </div>

                        </td>

                        {/* Email */}
                        <td className="px-6 py-5">

                          <span className="text-slate-700">
                            {user.email}
                          </span>

                        </td>

                        {/* Role */}
                        <td className="px-6 py-5">

                          {user.roles?.length > 0 ? (

                            <span
                              className="
                                inline-flex
                                items-center
                                px-3
                                py-1
                                rounded-full
                                bg-blue-100
                                text-blue-700
                                text-xs
                                font-semibold
                              "
                            >
                              {user.roles[0].name}
                            </span>

                          ) : (

                            <span
                              className="
                                inline-flex
                                items-center
                                px-3
                                py-1
                                rounded-full
                                bg-slate-100
                                text-slate-600
                                text-xs
                              "
                            >
                              No Role
                            </span>

                          )}

                        </td>

                        {/* Status */}
                        <td className="px-6 py-5">

                          {isSuperAdmin ? (

                            <span
                              className="
                                inline-flex
                                items-center
                                gap-2
                                px-3
                                py-1
                                rounded-full
                                bg-red-100
                                text-red-700
                                text-xs
                                font-semibold
                              "
                            >
                              <Lock className="w-3 h-3" />
                              Protected
                            </span>

                          ) : (

                            <span
                              className="
                                inline-flex
                                items-center
                                px-3
                                py-1
                                rounded-full
                                bg-green-100
                                text-green-700
                                text-xs
                                font-semibold
                              "
                            >
                              Active
                            </span>

                          )}

                        </td>

                        {/* Actions */}
                        {(canUpdate || canDelete) && (

                          <td className="px-6 py-5">

                            <div className="flex justify-end gap-2">

                              {canUpdate &&
                                !isSuperAdmin && (

                                  <button
                                    onClick={() =>
                                      handleEdit(
                                        user
                                      )
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
                                    <Pencil className="w-4 h-4" />
                                    Edit
                                  </button>

                              )}

                              {canDelete &&
                                !isSuperAdmin && (

                                  <button
                                    onClick={() =>
                                      handleDelete(
                                        user.id
                                      )
                                    }
                                    className="
                                      inline-flex
                                      items-center
                                      gap-2
                                      px-4
                                      py-2
                                      rounded-xl
                                      bg-red-50
                                      text-red-700
                                      hover:bg-red-100
                                      transition
                                    "
                                  >
                                    <Trash2 className="w-4 h-4" />
                                    Delete
                                  </button>

                              )}

                            </div>

                          </td>

                        )}

                      </tr>

                    );

                  }
                )

              )}

            </tbody>

          </table>

        </div>

      </div>

    </div>
  );
}