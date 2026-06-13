import usePermission from "../hooks/usePermission";

export default function PermissionRoute({
  permission,
  children,
}) {
  const { can } =
    usePermission();

  if (!can(permission)) {
    return (
      <div className="flex items-center justify-center min-h-[70vh]">
        <div className="bg-white rounded-xl shadow-lg p-8 max-w-md text-center">
          <div className="text-6xl mb-4">
            🔒
          </div>

          <h1 className="text-3xl font-bold text-slate-800 mb-2">
            403 Forbidden
          </h1>

          <p className="text-slate-600">
            Anda tidak memiliki izin
            untuk mengakses halaman
            ini.
          </p>
        </div>
      </div>
    );
  }

  return children;
}