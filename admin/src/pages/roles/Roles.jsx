import {
  useEffect,
  useMemo,
  useState,
} from "react";

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

import {
  Shield,
  ShieldCheck,
  ShieldAlert,
  Users,
  Search,
  Plus,
  Pencil,
  Trash2,
  Lock,
} from "lucide-react";

export default function Roles() {

  const { can } = usePermission();

  /*
  |--------------------------------------------------------------------------
  | State
  |--------------------------------------------------------------------------
  */

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

  const [search, setSearch] =
    useState("");

  /*
  |--------------------------------------------------------------------------
  | Permissions
  |--------------------------------------------------------------------------
  */

  const canCreate =
    can("roles.create");

  const canUpdate =
    can("roles.update");

  const canDelete =
    can("roles.delete");

  const canSubmit =
    editingId
      ? canUpdate
      : canCreate;

  /*
  |--------------------------------------------------------------------------
  | Load Roles
  |--------------------------------------------------------------------------
  */

  const loadRoles = async () => {

    try {

      setLoading(true);

      const data =
        await getRoles();

      setRoles(
        data || []
      );

    } catch (error) {

      console.error(error);

      setRoles([]);

      errorAlert(
        error?.response?.data
          ?.message ||
        "Gagal mengambil data role"
      );

    } finally {

      setLoading(false);

    }

  };

  /*
  |--------------------------------------------------------------------------
  | Load Permissions
  |--------------------------------------------------------------------------
  */

  const loadPermissions =
    async () => {

      try {

        const data =
          await getPermissions();

        setPermissions(
          data || []
        );

      } catch (error) {

        console.error(error);

        setPermissions([]);

        errorAlert(
          error?.response?.data
            ?.message ||
          "Gagal mengambil permissions"
        );

      }

    };

  /*
  |--------------------------------------------------------------------------
  | Initial Load
  |--------------------------------------------------------------------------
  */

  useEffect(() => {

    const init =
      async () => {

        await Promise.all([
          loadRoles(),
          loadPermissions(),
        ]);

      };

    init();

  }, []);

  /*
  |--------------------------------------------------------------------------
  | Filter Roles
  |--------------------------------------------------------------------------
  */

  const filteredRoles =
    useMemo(() => {

      return roles.filter(
        (role) => {

          const keyword =
            search.toLowerCase();

          const matchRole =

            role.name
              ?.toLowerCase()
              .includes(
                keyword
              );

          const matchPermission =

            role.permissions?.some(
              (permission) =>
                permission.name
                  ?.toLowerCase()
                  .includes(
                    keyword
                  )
            );

          return (
            matchRole ||
            matchPermission
          );

        }
      );

    }, [
      roles,
      search,
    ]);
  /*
  |--------------------------------------------------------------------------
  | Reset Form
  |--------------------------------------------------------------------------
  */

  const resetForm = () => {

    setEditingId(null);

    setName("");

    setSelectedPermissions([]);

  };

  /*
  |--------------------------------------------------------------------------
  | Permission Grouping
  |--------------------------------------------------------------------------
  */

  const groupedPermissions =
    useMemo(() => {

      return permissions.reduce(
        (groups, permission) => {

          const permissionName =
            permission.name || "";

          const module =
            permissionName.includes(".")
              ? permissionName.split(".")[0]
              : "others";

          if (!groups[module]) {

            groups[module] = [];

          }

          groups[module].push(
            permission
          );

          return groups;

        },
        {}
      );

    }, [permissions]);

  /*
  |--------------------------------------------------------------------------
  | Toggle Permission
  |--------------------------------------------------------------------------
  */

  const handlePermissionChange =
    (permissionName) => {

      if (!canSubmit) return;

      if (
        selectedPermissions.includes(
          permissionName
        )
      ) {

        setSelectedPermissions(
          (prev) =>
            prev.filter(
              (item) =>
                item !== permissionName
            )
        );

      } else {

        setSelectedPermissions(
          (prev) => [
            ...prev,
            permissionName,
          ]
        );

      }

    };

  /*
  |--------------------------------------------------------------------------
  | Submit
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

        console.error(error);

        const errors =
          error?.response?.data
            ?.errors;

        if (errors) {

          const firstError =
            Object.values(errors)[0]?.[0];

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

  /*
  |--------------------------------------------------------------------------
  | Edit Role
  |--------------------------------------------------------------------------
  */

  const handleEdit =
    (role) => {

      if (!canUpdate) return;

      if (
        role.name ===
        "Super Admin"
      ) {
        return;
      }

      setEditingId(
        role.id
      );

      setName(
        role.name || ""
      );

      setSelectedPermissions(

        role.permissions?.map(
          (permission) =>
            permission.name
        ) || []

      );

      window.scrollTo({

        top: 0,

        behavior: "smooth",

      });

    };

  /*
  |--------------------------------------------------------------------------
  | Delete Role
  |--------------------------------------------------------------------------
  */

  const handleDelete =
    async (id) => {

      if (!canDelete) return;

      const result =
        await confirmDelete();

      if (
        !result.isConfirmed
      ) {

        return;

      }

      try {

        await deleteRole(
          id
        );

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

  /*
  |--------------------------------------------------------------------------
  | Statistics
  |--------------------------------------------------------------------------
  */

  const totalRoles =
    roles.length;

  const totalPermissions =
    permissions.length;

  const protectedRoles =
    roles.filter(
      (role) =>
        role.name ===
        "Super Admin"
    ).length;

  const editableRoles =
    totalRoles -
    protectedRoles;
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
                <Shield className="w-8 h-8 text-red-400" />
              </div>

              <div>

                <h1 className="text-4xl font-bold">
                  Roles & Permissions
                </h1>

                <p className="text-slate-300 mt-2">
                  Manage access control and permissions for your team.
                </p>

              </div>

            </div>

          </div>

          <div className="flex flex-wrap gap-3">

            <div className="bg-white/10 backdrop-blur rounded-2xl px-4 py-2 text-sm">
              Roles: {totalRoles}
            </div>

            <div className="bg-white/10 backdrop-blur rounded-2xl px-4 py-2 text-sm">
              Permissions: {totalPermissions}
            </div>

          </div>

        </div>

      </div>

      {/* Statistics */}
      <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">

        {/* Total Roles */}
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

            <div className="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center">

              <Users className="w-7 h-7 text-blue-600" />

            </div>

          </div>

        </div>

        {/* Permissions */}
        <div className="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">

          <div className="flex items-center justify-between">

            <div>

              <p className="text-sm text-slate-500">
                Permissions
              </p>

              <h3 className="text-4xl font-bold mt-2">
                {totalPermissions}
              </h3>

            </div>

            <div className="w-14 h-14 rounded-2xl bg-green-50 flex items-center justify-center">

              <ShieldCheck className="w-7 h-7 text-green-600" />

            </div>

          </div>

        </div>

        {/* Editable */}
        <div className="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">

          <div className="flex items-center justify-between">

            <div>

              <p className="text-sm text-slate-500">
                Editable Roles
              </p>

              <h3 className="text-4xl font-bold mt-2">
                {editableRoles}
              </h3>

            </div>

            <div className="w-14 h-14 rounded-2xl bg-amber-50 flex items-center justify-center">

              <ShieldAlert className="w-7 h-7 text-amber-600" />

            </div>

          </div>

        </div>

        {/* Protected */}
        <div className="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">

          <div className="flex items-center justify-between">

            <div>

              <p className="text-sm text-slate-500">
                Protected Roles
              </p>

              <h3 className="text-4xl font-bold mt-2">
                {protectedRoles}
              </h3>

            </div>

            <div className="w-14 h-14 rounded-2xl bg-red-50 flex items-center justify-center">

              <Lock className="w-7 h-7 text-red-600" />

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
            placeholder="Cari role atau permission..."
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

              <Plus className="w-6 h-6 text-red-600" />

            </div>

            <div>

              <h2 className="text-2xl font-bold">

                {editingId
                  ? "Edit Role"
                  : "Create Role"}

              </h2>

              <p className="text-slate-500">

                Configure roles and permissions.

              </p>

            </div>

          </div>

          <form
            onSubmit={handleSubmit}
            className="space-y-8"
          >

            {/* Role Name */}
            <div>

              <label className="block text-sm font-medium text-slate-700 mb-2">

                Role Name

              </label>

              <input
                type="text"
                placeholder="Masukkan nama role"
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

            {/* Permissions */}
            <div>

              <label className="block text-sm font-medium text-slate-700 mb-5">

                Permissions

              </label>

              <div className="space-y-6">

                {Object.entries(
                  groupedPermissions
                ).map(
                  ([module, modulePermissions]) => (

                    <div
                      key={module}
                      className="
                        border
                        border-slate-100
                        rounded-2xl
                        p-5
                        bg-slate-50/50
                      "
                    >

                      <h3 className="font-semibold text-slate-900 mb-4 capitalize">

                        {module}

                      </h3>

                      <div className="flex flex-wrap gap-3">                        {modulePermissions.map(
                          (permission) => {

                            const checked =
                              selectedPermissions.includes(
                                permission.name
                              );

                            return (

                              <button
                                key={permission.id}
                                type="button"
                                disabled={
                                  !canSubmit ||
                                  submitting
                                }
                                onClick={() =>
                                  handlePermissionChange(
                                    permission.name
                                  )
                                }
                                className={`
                                  px-4
                                  py-2
                                  rounded-2xl
                                  text-sm
                                  font-medium
                                  transition-all
                                  border
                                  disabled:opacity-60
                                  disabled:cursor-not-allowed
                                  ${
                                    checked
                                      ? "bg-red-600 text-white border-red-600 shadow"
                                      : "bg-white text-slate-700 border-slate-200 hover:border-red-300 hover:text-red-600"
                                  }
                                `}
                              >
                                {permission.name
                                  .split(".")
                                  .slice(1)
                                  .join(".")}
                              </button>

                            );

                          }
                        )}

                      </div>

                    </div>

                  )
                )}

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
                  <Plus className="w-5 h-5" />

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

      {/* Roles Table */}
      <div className="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">

        <div className="px-6 py-5 border-b border-slate-100">

          <h2 className="text-xl font-bold text-slate-900">
            Roles List
          </h2>

          <p className="text-sm text-slate-500 mt-1">
            Menampilkan {filteredRoles.length} role.
          </p>

        </div>

        <div className="overflow-x-auto">

          <table className="w-full">

            <thead className="bg-slate-50">

              <tr>

                <th className="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                  Role
                </th>

                <th className="px-6 py-4 text-left text-sm font-semibold text-slate-600">
                  Permissions
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
                        ? 4
                        : 3
                    }
                    className="py-16 text-center text-slate-500"
                  >
                    Loading roles...
                  </td>

                </tr>

              ) : filteredRoles.length === 0 ? (

                <tr>

                  <td
                    colSpan={
                      canUpdate || canDelete
                        ? 4
                        : 3
                    }
                    className="py-20 text-center"
                  >

                    <div className="flex flex-col items-center">

                      <Shield className="w-16 h-16 text-slate-300 mb-4" />

                      <h3 className="text-lg font-semibold text-slate-700">
                        Belum ada role
                      </h3>

                      <p className="text-slate-500 mt-2">
                        Tambahkan role untuk mengatur hak akses.
                      </p>

                    </div>

                  </td>

                </tr>

              ) : (

                filteredRoles.map(
                  (role) => {

                    const isProtected =
                      role.name ===
                      "Super Admin";

                    return (

                      <tr
                        key={role.id}
                        className="
                          border-t
                          border-slate-100
                          hover:bg-slate-50
                          transition
                        "
                      >

                        {/* Role */}
                        <td className="px-6 py-5">

                          <div>

                            <p className="font-semibold text-slate-900">
                              {role.name}
                            </p>

                            <p className="text-xs text-slate-400 mt-1">
                              ID #{role.id}
                            </p>

                          </div>

                        </td>

                        {/* Permissions */}
                        <td className="px-6 py-5">

                          <div className="flex flex-wrap gap-2">

                            {(role.permissions || [])
                              .slice(0, 5)
                              .map(
                                (permission) => (

                                  <span
                                    key={permission.id}
                                    className="
                                      px-3
                                      py-1
                                      rounded-full
                                      bg-blue-100
                                      text-blue-700
                                      text-xs
                                      font-medium
                                    "
                                  >
                                    {permission.name}
                                  </span>

                                )
                              )}

                            {(role.permissions || []).length > 5 && (

                              <span
                                className="
                                  px-3
                                  py-1
                                  rounded-full
                                  bg-slate-100
                                  text-slate-600
                                  text-xs
                                "
                              >
                                +
                                {(role.permissions || []).length - 5}
                              </span>

                            )}

                          </div>

                        </td>

                        {/* Status */}
                        <td className="px-6 py-5">

                          {isProtected ? (

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
                              Editable
                            </span>

                          )}

                        </td>

                        {/* Actions */}
                        {(canUpdate || canDelete) && (

                          <td className="px-6 py-5">

                            <div className="flex justify-end gap-2">

                              {canUpdate &&
                                !isProtected && (

                                  <button
                                    onClick={() =>
                                      handleEdit(
                                        role
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
                                !isProtected && (

                                  <button
                                    onClick={() =>
                                      handleDelete(
                                        role.id
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