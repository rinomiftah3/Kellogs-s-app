import {
  createContext,
  useContext,
  useEffect,
  useState,
} from "react";

import api from "../api/axios";

const AuthContext =
  createContext();

export function AuthProvider({
  children,
}) {
  const [user, setUser] =
    useState(null);

  const [loading, setLoading] =
    useState(true);

  const fetchUser =
  async () => {
    try {

      const response =
        await api.get("/me");

      setUser(
        response?.data?.data ||
        null
      );

    } catch (error) {

      localStorage.removeItem(
        "auth_token"
      );

      localStorage.removeItem(
        "user"
      );

      setUser(null);

    } finally {

      setLoading(false);

    }
  };

  useEffect(() => {
    const token =
      localStorage.getItem(
        "auth_token"
      );

    if (token) {
      fetchUser();
    } else {
      setLoading(false);
    }
  }, []);

  const login =
    async (
      email,
      password,
      remember = true
    ) => {
      const response =
        await api.post(
          "/login",
          {
            email,
            password,
            remember,
          }
        );

      const token =
        response?.data?.data
          ?.token;

      const userData =
        response?.data?.data
          ?.user;
      if (!token || !userData) {
        throw new Error(
          "Response login tidak valid"
        );
      }
      localStorage.setItem(
        "auth_token",
        token
      );

      localStorage.setItem(
        "user",
        JSON.stringify(
          userData
        )
      );

      setUser(userData);

      return response?.data;
    };

  const logout =
    async () => {
      try {
        await api.post(
          "/logout"
        );
      } catch (error) {
        console.error(error);
      }

      localStorage.removeItem(
        "auth_token"
      );

      localStorage.removeItem(
        "user"
      );

      setUser(null);
    };

  return (
    <AuthContext.Provider
      value={{
        user,
        loading,
        login,
        logout,
        refreshUser:
          fetchUser,
      }}
    >
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth() {
  return useContext(
    AuthContext
  );
}