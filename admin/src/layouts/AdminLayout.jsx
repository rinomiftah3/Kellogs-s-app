import {
  Outlet,
  NavLink,
  useNavigate,
} from "react-router-dom";

import Swal from "sweetalert2";

import {
  ArrowLeftOnRectangleIcon,
} from "@heroicons/react/24/outline";

import { useAuth } from "../context/AuthContext";
import { sidebarMenu } from "../config/sidebarMenu";

export default function AdminLayout() {
  const {
    user,
    logout,
  } = useAuth();

  const navigate =
    useNavigate();

  const permissions =
    user?.permissions ?? [];

  const visibleMenus =
    sidebarMenu.filter(
      (menu) =>
        !menu.permission ||
        permissions.includes(
          menu.permission
        )
    );

  const handleLogout =
    async () => {
      const result =
        await Swal.fire({
          title: "Logout?",
          text: "Anda akan keluar dari sistem",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor:
            "#dc2626",
          cancelButtonColor:
            "#64748b",
          confirmButtonText:
            "Logout",
          cancelButtonText:
            "Batal",
        });

      if (!result.isConfirmed)
        return;

      await logout();

      navigate("/");
    };

  return (
    <div className="flex min-h-screen bg-slate-100">

      {/* Sidebar */}
      <aside className="w-64 bg-slate-900 text-white flex flex-col shadow-xl">

        {/* Brand */}
        <div className="p-6 border-b border-slate-700">

          <h1 className="text-2xl font-bold tracking-wide">
            Kellogg's
          </h1>

          <p className="text-sm text-slate-400">
            Admin Dashboard
          </p>

          {/* User */}
          <div className="mt-5">

            {user?.avatar_url ? (
              <img
                src={
                  user.avatar_url
                }
                alt={
                  user?.name
                }
                className="w-14 h-14 rounded-full object-cover border-2 border-slate-600"
              />
            ) : (
              <div className="w-14 h-14 rounded-full bg-slate-700 flex items-center justify-center text-lg font-bold">
                {(user?.name ||
                  "U")
                  .charAt(0)
                  .toUpperCase()}
              </div>
            )}

            <div className="mt-3">
              <p className="font-semibold">
                {user?.name}
              </p>

              <p className="text-xs text-slate-400">
                {user?.roles?.join(
                  ", "
                )}
              </p>
            </div>

          </div>

        </div>

        {/* Menu */}
        <nav className="flex-1 p-4 space-y-1">

          {visibleMenus.map(
            (menu) => {
              const Icon =
                menu.icon;

              return (
                <NavLink
                  key={
                    menu.path
                  }
                  to={
                    menu.path
                  }
                  className={({
                    isActive,
                  }) =>
                    `flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 ${
                      isActive
                        ? "bg-slate-700 text-white shadow"
                        : "text-slate-300 hover:bg-slate-800 hover:text-white"
                    }`
                  }
                >
                  <Icon className="w-5 h-5" />

                  <span>
                    {
                      menu.label
                    }
                  </span>
                </NavLink>
              );
            }
          )}

        </nav>

        {/* Logout */}
        <div className="p-4 border-t border-slate-700">

          <button
            onClick={
              handleLogout
            }
            className="w-full flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 transition px-4 py-3 rounded-lg"
          >
            <ArrowLeftOnRectangleIcon className="w-5 h-5" />

            <span>
              Logout
            </span>
          </button>

        </div>

      </aside>

      {/* Content */}
      <div className="flex flex-col flex-1">

        <main className="flex-1 p-6">
          <Outlet />
        </main>

        <footer className="bg-white border-t px-6 py-4 text-sm text-slate-500">
          Kellogg's Admin Dashboard ©{" "}
          {new Date().getFullYear()}
        </footer>

      </div>

    </div>
  );
}