import {
  HomeIcon,

  Squares2X2Icon,
  CubeIcon,
  PhotoIcon,
  AdjustmentsHorizontalIcon,
  ListBulletIcon,
  ArchiveBoxIcon,
  ChatBubbleBottomCenterTextIcon,

  ShoppingBagIcon,
  CreditCardIcon,
  BanknotesIcon,
  TicketIcon,

  UsersIcon,
  MapPinIcon,
  GiftIcon,

  ShieldCheckIcon,

  ClockIcon,

  UserCircleIcon,
} from "@heroicons/react/24/outline";

export const sidebarMenu = [

  /*
  |--------------------------------------------------------------------------
  | MAIN
  |--------------------------------------------------------------------------
  */

  {
    section: "MAIN",
  },

  {
    label: "Dashboard",
    path: "/dashboard",
    permission: "dashboard.view",
    icon: HomeIcon,
  },

  /*
  |--------------------------------------------------------------------------
  | CATALOG
  |--------------------------------------------------------------------------
  */

  {
    section: "CATALOG",
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
    label: "Product Images",
    path: "/product-images",
    permission: "product_images.view",
    icon: PhotoIcon,
  },

  {
    label: "Product Options",
    path: "/product-options",
    permission: "product_options.view",
    icon: AdjustmentsHorizontalIcon,
  },

  {
    label: "Option Values",
    path: "/product-option-values",
    permission: "product_option_values.view",
    icon: ListBulletIcon,
  },

  {
    label: "Product SKUs",
    path: "/product-skus",
    permission: "product_skus.view",
    icon: ArchiveBoxIcon,
  },

  {
    label: "Product Reviews",
    path: "/product-reviews",
    permission: "product_reviews.view",
    icon: ChatBubbleBottomCenterTextIcon,
  },

  /*
  |--------------------------------------------------------------------------
  | SALES
  |--------------------------------------------------------------------------
  */

  {
    section: "SALES",
  },

  {
    label: "Orders",
    path: "/orders",
    permission: "orders.view",
    icon: ShoppingBagIcon,
  },

  {
    label: "Payments",
    path: "/payments",
    permission: "payments.view",
    icon: CreditCardIcon,
  },

  {
    label: "Payment Methods",
    path: "/payment-methods",
    permission: "payment_methods.view",
    icon: BanknotesIcon,
  },

  {
    label: "Vouchers",
    path: "/vouchers",
    permission: "vouchers.view",
    icon: TicketIcon,
  },

  /*
  |--------------------------------------------------------------------------
  | CUSTOMER
  |--------------------------------------------------------------------------
  */

  {
    section: "CUSTOMER",
  },

  {
    label: "Users",
    path: "/users",
    permission: "users.view",
    icon: UsersIcon,
  },

  {
    label: "Customer Addresses",
    path: "/customer-addresses",
    permission: "customer_addresses.view",
    icon: MapPinIcon,
  },

  {
    label: "Loyalty Points",
    path: "/loyalty-points",
    permission: "loyalty_points.view",
    icon: GiftIcon,
  },

  /*
  |--------------------------------------------------------------------------
  | ACCESS CONTROL
  |--------------------------------------------------------------------------
  */

  {
    section: "ACCESS CONTROL",
  },

  {
    label: "Roles",
    path: "/roles",
    permission: "roles.view",
    icon: ShieldCheckIcon,
  },

  /*
  |--------------------------------------------------------------------------
  | SYSTEM
  |--------------------------------------------------------------------------
  */

  {
    section: "SYSTEM",
  },

  {
    label: "Activity Logs",
    path: "/activity-logs",
    permission: "activity_logs.view",
    icon: ClockIcon,
  },

  /*
  |--------------------------------------------------------------------------
  | ACCOUNT
  |--------------------------------------------------------------------------
  */

  {
    section: "ACCOUNT",
  },

  {
    label: "Profile",
    path: "/profile",
    permission: null,
    icon: UserCircleIcon,
  },
];