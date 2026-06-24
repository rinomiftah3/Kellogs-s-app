import {
  Outlet,
  NavLink,
  useNavigate,
} from "react-router-dom";

import {
  useState,
} from "react";

import Swal from "sweetalert2";

import {
  ArrowLeftOnRectangleIcon,
  Bars3Icon,
  XMarkIcon,
  BellIcon,
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

  const [
    sidebarOpen,
    setSidebarOpen,
  ] = useState(false);

  /*
  |--------------------------------------------------------------------------
  | Permissions
  |--------------------------------------------------------------------------
  */

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

  /*
  |--------------------------------------------------------------------------
  | User Info
  |--------------------------------------------------------------------------
  */

  const userName =
    user?.name ||
    "Administrator";

  const userRole =
    user?.roles?.join(", ") ||
    "Administrator";

  const userInitial =
    userName
      .charAt(0)
      .toUpperCase();

  /*
  |--------------------------------------------------------------------------
  | Logout
  |--------------------------------------------------------------------------
  */

  const handleLogout =
    async () => {

      const result =
        await Swal.fire({

          title: "Logout?",

          text:
            "Anda akan keluar dari sistem.",

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

      if (!result.isConfirmed) {
        return;
      }

      await logout();

      navigate("/");
    };

  return (

    <div className="min-h-screen bg-slate-100">

      {/* Mobile Overlay */}
      {sidebarOpen && (

        <div
          onClick={() =>
            setSidebarOpen(false)
          }
          className="
            fixed
            inset-0
            bg-black/50
            backdrop-blur-sm
            z-40
            lg:hidden
          "
        />

      )}

      <div className="flex">

        {/* Sidebar */}
        <aside
          className={`
            fixed
            inset-y-0
            left-0
            z-50
            w-72
            bg-slate-950
            text-white
            flex
            flex-col
            shadow-2xl
            transform
            transition-transform
            duration-300

            ${sidebarOpen
              ? "translate-x-0"
              : "-translate-x-full lg:translate-x-0"
            }
          `}
        >

          {/* Brand */}
          <div
            className="
              px-6
              py-6
              border-b
              border-white/10
              shrink-0
            "
          >

            <div className="flex items-center gap-4">

              <div
                className="
                  w-14
                  h-14
                  rounded-2xl
                  bg-gradient-to-br
                  from-red-600
                  to-red-500
                  flex
                  items-center
                  justify-center
                  shadow-lg
                  shadow-red-500/30
                  text-xl
                  font-bold
                "
              >
                K
              </div>

              <div>

                <h1 className="text-2xl font-bold">
                  Kellogg's
                </h1>

                <p className="text-sm text-slate-400">
                  Admin Dashboard
                </p>

              </div>

            </div>

          </div>

          {/* User Card */}
          <div
            className="
              px-5
              py-5
              shrink-0
            "
          >

            <div
              className="
                rounded-3xl
                bg-white/5
                backdrop-blur
                border
                border-white/10
                p-5
              "
            >

              <div className="flex items-center gap-4">

                {user?.avatar_url ? (

                  <img
                    src={user.avatar_url}
                    alt={userName}
                    className="
                      w-14
                      h-14
                      rounded-2xl
                      object-cover
                    "
                  />

                ) : (

                  <div
                    className="
                      w-14
                      h-14
                      rounded-2xl
                      bg-red-600
                      flex
                      items-center
                      justify-center
                      text-lg
                      font-bold
                    "
                  >
                    {userInitial}
                  </div>

                )}

                <div className="min-w-0">

                  <p
                    className="
                      font-bold
                      truncate
                    "
                  >
                    {userName}
                  </p>

                  <p
                    className="
                      text-sm
                      text-slate-400
                      truncate
                    "
                  >
                    {userRole}
                  </p>

                </div>

              </div>

              <div className="mt-5">

                <span
                  className="
                    inline-flex
                    items-center
                    gap-2
                    px-3
                    py-1
                    rounded-full
                    bg-emerald-500/20
                    text-emerald-400
                    text-xs
                    font-semibold
                  "
                >

                  <span className="w-2 h-2 rounded-full bg-emerald-400" />

                  Online

                </span>

              </div>

            </div>

          </div>
{/* Navigation */}
<nav
  className="
    flex-1
    overflow-y-auto
    px-4
    pb-5
    scrollbar-thin
    scrollbar-thumb-slate-700
    scrollbar-track-transparent
  "
>

  <div className="space-y-2">

    {visibleMenus.map((menu, index) => {

      /*
      |--------------------------------------------------------------------------
      | Section Heading
      |--------------------------------------------------------------------------
      */

      if (menu.section) {

        return (

          <div
            key={`section-${menu.section}-${index}`}
            className="
              px-3
              pt-5
              pb-2
            "
          >

            <p
              className="
                text-[11px]
                uppercase
                tracking-[0.2em]
                text-slate-500
                font-semibold
              "
            >
              {menu.section}
            </p>

          </div>

        );

      }

      /*
      |--------------------------------------------------------------------------
      | Menu Item
      |--------------------------------------------------------------------------
      */

      const Icon =
        menu.icon;

      return (

        <NavLink
          key={menu.path}
          to={menu.path}
          onClick={() =>
            setSidebarOpen(false)
          }
          className={({
            isActive,
          }) =>
            `
              flex
              items-center
              gap-4
              px-4
              py-3.5
              rounded-2xl
              transition-all
              duration-200

              ${
                isActive
                  ? `
                    bg-gradient-to-r
                    from-red-600
                    to-red-500
                    text-white
                    shadow-lg
                    shadow-red-500/20
                  `
                  : `
                    text-slate-300
                    hover:bg-white/5
                    hover:text-white
                  `
              }
            `
          }
        >

          <Icon
            className="
              w-5
              h-5
              shrink-0
            "
          />

          <span className="font-medium">
            {menu.label}
          </span>

        </NavLink>

      );

    })}

  </div>

</nav>
          {/* Logout */}
          <div
            className="
              p-5
              border-t
              border-white/10
              shrink-0
            "
          >

            <button
              onClick={handleLogout}
              className="
                w-full
                flex
                items-center
                justify-center
                gap-3
                rounded-2xl
                border
                border-red-500/30
                bg-red-500/10
                hover:bg-red-500
                hover:text-white
                text-red-400
                px-4
                py-3.5
                transition-all
                duration-200
                font-medium
              "
            >

              <ArrowLeftOnRectangleIcon className="w-5 h-5" />

              Logout

            </button>

          </div>

        </aside>

        {/* Main Area */}
        <div
          className="
            flex
            flex-col
            flex-1
            lg:ml-72
            min-h-screen
          "
        >

          {/* Header */}
          <header
            className="
              sticky
              top-0
              z-30
              bg-white/90
              backdrop-blur-xl
              border-b
              border-slate-200
            "
          >

            <div
              className="
                px-6
                py-5
                flex
                items-center
                justify-between
              "
            >

              {/* Left */}
              <div className="flex items-center gap-4">

                {/* Mobile Toggle */}
                <button
                  onClick={() =>
                    setSidebarOpen(true)
                  }
                  className="
                    lg:hidden
                    w-11
                    h-11
                    rounded-2xl
                    bg-slate-100
                    hover:bg-slate-200
                    transition
                    flex
                    items-center
                    justify-center
                  "
                >

                  <Bars3Icon className="w-6 h-6 text-slate-700" />

                </button>

                <div>

                  <h2
                    className="
                      text-2xl
                      font-bold
                      text-slate-900
                    "
                  >
                    Welcome back,
                    {" "}
                    {userName}
                  </h2>

                  <p
                    className="
                      text-sm
                      text-slate-500
                      mt-1
                    "
                  >
                    Monitor and manage your business efficiently.
                  </p>

                </div>

              </div>

              {/* Right */}
              <div className="flex items-center gap-3">

                {/* Notification */}
                <button
                  className="
                    relative
                    w-11
                    h-11
                    rounded-2xl
                    bg-slate-100
                    hover:bg-slate-200
                    transition
                    flex
                    items-center
                    justify-center
                  "
                >

                  <BellIcon className="w-5 h-5 text-slate-700" />

                  <span
                    className="
                      absolute
                      top-2
                      right-2
                      w-2.5
                      h-2.5
                      rounded-full
                      bg-red-500
                    "
                  />

                </button>

                {/* Mobile Close */}
                <button
                  onClick={() =>
                    setSidebarOpen(false)
                  }
                  className="
                    lg:hidden
                    w-11
                    h-11
                    rounded-2xl
                    bg-slate-100
                    hover:bg-slate-200
                    transition
                    flex
                    items-center
                    justify-center
                  "
                >

                  <XMarkIcon className="w-6 h-6 text-slate-700" />

                </button>

              </div>

            </div>

          </header>

          {/* Content */}
          <main
            className="
              flex-1
              p-6
            "
          >

            <Outlet />

          </main>

          {/* Footer */}
          <footer
            className="
              bg-white
              border-t
              border-slate-200
              px-6
              py-5
            "
          >

            <div
              className="
                flex
                flex-col
                md:flex-row
                md:items-center
                md:justify-between
                gap-3
              "
            >

              <div>

                <p
                  className="
                    text-sm
                    font-semibold
                    text-slate-700
                  "
                >
                  Kellogg's Admin Dashboard
                </p>

                <p
                  className="
                    text-xs
                    text-slate-500
                    mt-1
                  "
                >
                  Built with React + Laravel
                </p>

              </div>

              <div
                className="
                  text-sm
                  text-slate-500
                "
              >
                © {new Date().getFullYear()} Kellogg's Indonesia
              </div>

            </div>

          </footer>

        </div>

      </div>

    </div>

  );

}