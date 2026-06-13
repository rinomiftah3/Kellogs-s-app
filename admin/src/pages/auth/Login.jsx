import {
  useState,
  useEffect,
} from "react";

import { useNavigate } from "react-router-dom";

import { useAuth } from "../../context/AuthContext";

export default function Login() {
  const navigate =
    useNavigate();

  const {
    login,
    user,
  } = useAuth();

  const [email, setEmail] =
    useState("");

  const [
    password,
    setPassword,
  ] = useState("");

  const [error, setError] =
    useState("");

  const [loading, setLoading] =
    useState(false);

  useEffect(() => {
    if (user) {
      navigate("/dashboard");
    }
  }, [user, navigate]);

  const handleSubmit = async (
    e
  ) => {
    e.preventDefault();

    setError("");

    if (!email.trim()) {
      return setError(
        "Email wajib diisi"
      );
    }

    if (!password.trim()) {
      return setError(
        "Password wajib diisi"
      );
    }

    try {
      setLoading(true);

      await login(
        email,
        password
      );

      // Tidak perlu navigate di sini,
      // karena useEffect akan redirect
      // setelah user berhasil tersimpan.
    } catch (err) {
      setError(
        err?.response?.data
          ?.message ||
          "Login gagal"
      );
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen flex justify-center items-center bg-slate-100">
      <form
        onSubmit={handleSubmit}
        className="bg-white p-8 rounded-lg shadow-md w-full max-w-md"
      >
        <h1 className="text-2xl font-bold mb-6 text-center">
          Kellogg's Admin
        </h1>

        {error && (
          <div className="bg-red-100 text-red-600 p-3 rounded mb-4">
            {error}
          </div>
        )}

        <div className="mb-4">
          <label className="block mb-2">
            Email
          </label>

          <input
            type="email"
            className="w-full border rounded p-3"
            value={email}
            onChange={(e) =>
              setEmail(
                e.target.value
              )
            }
          />
        </div>

        <div className="mb-6">
          <label className="block mb-2">
            Password
          </label>

          <input
            type="password"
            className="w-full border rounded p-3"
            value={password}
            onChange={(e) =>
              setPassword(
                e.target.value
              )
            }
          />
        </div>

        <button
          type="submit"
          disabled={loading}
          className="w-full bg-slate-900 text-white p-3 rounded disabled:opacity-50"
        >
          {loading
            ? "Loading..."
            : "Login"}
        </button>
      </form>
    </div>
  );
}