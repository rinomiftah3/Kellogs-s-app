import {
  BrowserRouter,
  Routes,
  Route,
  Navigate,
} from "react-router-dom";

import Login from "../pages/auth/Login";

import Dashboard from "../pages/dashboard/Dashboard";
import Categories from "../pages/categories/Categories";
import Products from "../pages/products/Products";

import ProductReviews from "../pages/product-reviews/ProductReviews";
import ProductImage from "../pages/product-image/ProductImage";
import ProductOption from "../pages/product-option/ProductOption";
import ProductOptionValue from "../pages/product-option-value/ProductOptionValue";
import ProductSku from "../pages/product-sku/ProductSku";

import Orders from "../pages/orders/Orders";
import OrderDetail from "../pages/orders/OrderDetail";

import Payments from "../pages/payments/Payments";
import PaymentDetail from "../pages/payments/PaymentDetail";


import Users from "../pages/users/Users";
import Roles from "../pages/roles/Roles";
import ActivityLogs from "../pages/activity/ActivityLogs";
import Profile from "../pages/profile/Profile";

import ProtectedRoute from "./ProtectedRoute";
import PermissionRoute from "./PermissionRoute";

import AdminLayout from "../layouts/AdminLayout";

/*
|--------------------------------------------------------------------------
| Coming Soon Placeholder
|--------------------------------------------------------------------------
*/

function ComingSoon({
  title,
}) {

  return (

    <div
      className="
        bg-white
        rounded-3xl
        p-10
        shadow-sm
        border
        border-slate-100
        text-center
      "
    >

      <h1
        className="
          text-3xl
          font-bold
          text-slate-800
        "
      >
        {title}
      </h1>

      <p
        className="
          text-slate-500
          mt-3
        "
      >
        Halaman ini sedang dalam proses pengembangan.
      </p>

    </div>

  );

}

/*
|--------------------------------------------------------------------------
| Not Found
|--------------------------------------------------------------------------
*/

function NotFound() {

  return (

    <div
      className="
        flex
        items-center
        justify-center
        min-h-screen
      "
    >

      <div className="text-center">

        <h1
          className="
            text-6xl
            font-bold
            text-slate-900
          "
        >
          404
        </h1>

        <p
          className="
            text-slate-600
            mt-4
          "
        >
          Halaman tidak ditemukan.
        </p>

      </div>

    </div>

  );

}

export default function AppRouter() {

  return (

    <BrowserRouter>

      <Routes>

        {/* Login */}
        <Route
          path="/"
          element={<Login />}
        />

        {/* Protected Area */}
        <Route
          element={

            <ProtectedRoute>

              <AdminLayout />

            </ProtectedRoute>

          }
        >

          {/* Dashboard */}
          <Route
            path="dashboard"
            element={

              <PermissionRoute
                permission="dashboard.view"
              >

                <Dashboard />

              </PermissionRoute>

            }
          />

          {/* Profile */}
          <Route
            path="profile"
            element={

              <Profile />

            }
          />

          {/* Categories */}
          <Route
            path="categories"
            element={

              <PermissionRoute
                permission="categories.view"
              >

                <Categories />

              </PermissionRoute>

            }
          />

          {/* Products */}
          <Route
            path="products"
            element={

              <PermissionRoute
                permission="products.view"
              >

                <Products />

              </PermissionRoute>

            }
          />

          {/* Product Images */}
          <Route
            path="product-images"
            element={

              <PermissionRoute
                permission="product_images.view"
              >

                <ProductImage />

              </PermissionRoute>

            }
          />

          {/* Product Options */}
          <Route
            path="product-options"
            element={

              <PermissionRoute
                permission="product_options.view"
              >

                <ProductOption />

              </PermissionRoute>

            }
          />

          {/* Product Option Values */}
          <Route
            path="product-option-values"
            element={

              <PermissionRoute
                permission="product_option_values.view"
              >

                <ProductOptionValue/>

              </PermissionRoute>

            }
          />

          {/* Product SKUs */}
          <Route
            path="product-skus"
            element={

              <PermissionRoute
                permission="product_skus.view"
              >

                <ProductSku/>

              </PermissionRoute>

            }
          />

          {/* Product Reviews */}
          <Route
            path="product-reviews"
            element={

              <PermissionRoute
                permission="product_reviews.view"
              >

                <ProductReviews />

              </PermissionRoute>

            }

            /* Orders */
          />
          <Route
            path="orders"
            element={
              <PermissionRoute
                permission="orders.view"
              >
                <Orders />
              </PermissionRoute>
            }
          />

          <Route
            path="orders/:orderNumber"
            element={
              <PermissionRoute
                permission="orders.view"
              >
                <OrderDetail />
              </PermissionRoute>
            }
          />

          {/* Payments */}
          <Route
            path="payments"
            element={
              <PermissionRoute
                permission="payments.view"
              >
                <Payments />
              </PermissionRoute>
            }
          />

          <Route
            path="payments/:paymentNumber"
            element={
              <PermissionRoute
                permission="payments.view"
              >
                <PaymentDetail />
              </PermissionRoute>
            }
          />

          {/* Payment Methods */}
          <Route
            path="payment-methods"
            element={

              <PermissionRoute
                permission="payment_methods.view"
              >

                <ComingSoon
                  title="Payment Methods"
                />

              </PermissionRoute>

            }
          />

          {/* Vouchers */}
          <Route
            path="vouchers"
            element={

              <PermissionRoute
                permission="vouchers.view"
              >

                <ComingSoon
                  title="Vouchers"
                />

              </PermissionRoute>

            }
          />

          {/* Users */}
          <Route
            path="users"
            element={

              <PermissionRoute
                permission="users.view"
              >

                <Users />

              </PermissionRoute>

            }
          />

          {/* Customer Addresses */}
          <Route
            path="customer-addresses"
            element={

              <PermissionRoute
                permission="customer_addresses.view"
              >

                <ComingSoon
                  title="Customer Addresses"
                />

              </PermissionRoute>

            }
          />

          {/* Loyalty Points */}
          <Route
            path="loyalty-points"
            element={

              <PermissionRoute
                permission="loyalty_points.view"
              >

                <ComingSoon
                  title="Loyalty Points"
                />

              </PermissionRoute>

            }
          />

          {/* Roles */}
          <Route
            path="roles"
            element={

              <PermissionRoute
                permission="roles.view"
              >

                <Roles />

              </PermissionRoute>

            }
          />

          {/* Activity Logs */}
          <Route
            path="activity-logs"
            element={

              <PermissionRoute
                permission="activity_logs.view"
              >

                <ActivityLogs />

              </PermissionRoute>

            }
          />

        </Route>

        {/* Redirect */}
        <Route
          path="/home"
          element={

            <Navigate
              to="/dashboard"
              replace
            />

          }
        />

        {/* 404 */}
        <Route
          path="*"
          element={<NotFound />}
        />

      </Routes>

    </BrowserRouter>

  );

}