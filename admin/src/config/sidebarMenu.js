import {
  HomeIcon,
  Squares2X2Icon,
  CubeIcon,
  UsersIcon,
  ShieldCheckIcon,
  ClockIcon,
  UserCircleIcon,
} from "@heroicons/react/24/outline";

export const sidebarMenu = [
  {
    label: "Dashboard",
    path: "/dashboard",
    permission: "dashboard.view",
    icon: HomeIcon,
  },

  {
    label: "Categories",
    path: "/categories",
    permission: "categories.view",
    icon: Squares2X2Icon,
  },

  {
    label: "Products",
    path: "/products",
    permission: "products.view",
    icon: CubeIcon,
  },

  {
    label: "Users",
    path: "/users",
    permission: "users.view",
    icon: UsersIcon,
  },

  {
    label: "Roles",
    path: "/roles",
    permission: "roles.view",
    icon: ShieldCheckIcon,
  },

  {
    label: "Activity Logs",
    path: "/activity-logs",
    permission: "activity_logs.view",
    icon: ClockIcon,
  },

  {
    label: "Profile",
    path: "/profile",
    permission: null,
    icon: UserCircleIcon,
  },
];