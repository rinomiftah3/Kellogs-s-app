import {
  BrowserRouter,
  Routes,
  Route,
  Navigate,
} from "react-router-dom";

import Login from "../pages/auth/Login";

import Dashboard from "../pages/dashboard/Dashboard";
import Products from "../pages/products/Products";
import Categories from "../pages/categories/Categories";
import Users from "../pages/users/Users";
import ActivityLogs from "../pages/activity/ActivityLogs";
import Roles from "../pages/roles/Roles";
import Profile from "../pages/profile/Profile";

import ProtectedRoute from "./ProtectedRoute";
import PermissionRoute from "./PermissionRoute";

import AdminLayout from "../layouts/AdminLayout";

function NotFound() {
  return (
    <div className="flex items-center justify-center min-h-screen">
      <div className="text-center">
        <h1 className="text-5xl font-bold mb-4">
          404
        </h1>

        <p className="text-slate-600">
          Halaman tidak ditemukan
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

        {/* Admin Layout */}
        <Route
          element={
            <ProtectedRoute>
              <AdminLayout />
            </ProtectedRoute>
          }
        >

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

          <Route
            path="profile"
            element={<Profile />}
          />

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

        {/* Redirect dashboard lama */}
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