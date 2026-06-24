import {
  useState,
  useEffect,
} from "react";

import {
  useNavigate,
} from "react-router-dom";

import {
  useAuth,
} from "../../context/AuthContext";

import {
  EyeIcon,
  EyeSlashIcon,
  EnvelopeIcon,
  LockClosedIcon,
  ArrowRightIcon,
} from "@heroicons/react/24/outline";

import kelloggsLogo from "../../assets/images/kellogs-logo.png";

export default function Login() {
  const navigate = useNavigate();

  const {
    login,
    user,
  } = useAuth();

  const [email, setEmail] =
    useState("");

  const [password, setPassword] =
    useState("");

  const [error, setError] =
    useState("");

  const [loading, setLoading] =
    useState(false);

  const [showPassword, setShowPassword] =
    useState(false);

  const [rememberMe, setRememberMe] =
    useState(true);

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
        password,
        rememberMe
      );
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
    <div className="min-h-screen flex bg-slate-100">

      {/* Branding Section */}
      <div className="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-gradient-to-br from-red-700 via-red-600 to-orange-500">

        {/* Decorative Background */}
        <div className="absolute -top-24 -left-24 w-72 h-72 bg-white/10 rounded-full" />

        <div className="absolute bottom-0 right-0 w-96 h-96 bg-white/10 rounded-full translate-x-32 translate-y-32" />

        <div className="relative z-10 flex flex-col justify-center px-16 text-white">

          {/* Logo Branding */}
          <div className="mb-12 flex flex-col items-center text-center">

            <div
              className="
                bg-white
                rounded-[28px]
                px-10
                py-5
                shadow-2xl
                border
                border-white/40
                relative
                overflow-hidden
              "
            >

              {/* Accent */}
              <div className="absolute bottom-0 left-0 w-full h-1.5 bg-red-500" />

              <img
                src={kelloggsLogo}
                alt="Kellogg's"
                className="
                  h-12
                  w-auto
                  object-contain
                  relative
                  z-10
                "
              />

            </div>

            <h2 className="mt-8 text-4xl font-bold">
              Admin Portal
            </h2>

            <span
              className="
                inline-flex
                items-center
                mt-4
                px-5
                py-2
                rounded-full
                bg-white/20
                backdrop-blur-sm
                border
                border-white/20
                text-sm
                font-medium
              "
            >
              Enterprise Dashboard
            </span>

          </div>

          {/* Heading */}
          <div className="space-y-4 mb-12">

            <h2 className="text-5xl font-bold leading-tight">
              Manage Products.
              <br />
              Monitor Orders.
              <br />
              Grow Your Business.
            </h2>

            <p className="text-red-100 max-w-lg text-lg leading-relaxed">
              Kelola seluruh operasional
              bisnis Kellogg's melalui
              dashboard modern yang cepat,
              aman, dan mudah digunakan.
            </p>

          </div>

          {/* Feature Cards */}
          <div className="grid grid-cols-2 gap-4 max-w-xl">

            {[
              "Product Management",
              "Order Tracking",
              "Payment Monitoring",
              "Customer Insights",
            ].map((item) => (
              <div
                key={item}
                className="
                  bg-white/15
                  backdrop-blur-sm
                  rounded-2xl
                  p-4
                  border
                  border-white/20
                  hover:bg-white/20
                  transition
                "
              >
                ✓ {item}
              </div>
            ))}

          </div>

        </div>

      </div>

      {/* Login Section */}
      <div className="w-full lg:w-1/2 flex justify-center items-center px-6 py-10">

        <div className="w-full max-w-md">

          {/* Mobile Branding */}
          <div className="lg:hidden text-center mb-8">

            <div
              className="
                bg-white
                inline-flex
                rounded-2xl
                px-6
                py-4
                shadow-lg
                relative
                overflow-hidden
              "
            >

              <div className="absolute bottom-0 left-0 w-full h-1 bg-red-500" />

              <img
                src={kelloggsLogo}
                alt="Kellogg's"
                className="h-10 w-auto"
              />

            </div>

            <p className="text-slate-600 font-semibold mt-5">
              Admin Portal
            </p>

          </div>

          {/* Login Card */}
          <div
            className="
              bg-white
              rounded-[32px]
              shadow-[0_20px_60px_rgba(15,23,42,0.12)]
              p-8
              border
              border-slate-100
            "
          >

            <div className="mb-8">

              <h2 className="text-4xl font-bold text-slate-900">
                Welcome Back
              </h2>

              <p className="text-slate-500 mt-3 leading-relaxed">
                Sign in to access your
                Kellogg's Admin Dashboard.
              </p>

            </div>

            {error && (
              <div className="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
                {error}
              </div>
            )}

            <form
              onSubmit={handleSubmit}
              className="space-y-6"
            >

              {/* Email */}
              <div>

                <label className="block text-sm font-medium text-slate-700 mb-2">
                  Email Address
                </label>

                <div className="relative">

                  <EnvelopeIcon
                    className="
                      w-5 h-5 text-slate-400
                      absolute left-4 top-1/2
                      -translate-y-1/2
                    "
                  />

                  <input
                    type="email"
                    value={email}
                    placeholder="Masukkan email"
                    onChange={(e) =>
                      setEmail(
                        e.target.value
                      )
                    }
                    className="
                      w-full rounded-2xl
                      border border-slate-300
                      pl-12 pr-4 py-3
                      focus:outline-none
                      focus:ring-2
                      focus:ring-red-500
                      focus:border-red-500
                      transition
                    "
                  />

                </div>

              </div>

              {/* Password */}
              <div>

                <label className="block text-sm font-medium text-slate-700 mb-2">
                  Password
                </label>

                <div className="relative">

                  <LockClosedIcon
                    className="
                      w-5 h-5 text-slate-400
                      absolute left-4 top-1/2
                      -translate-y-1/2
                    "
                  />

                  <input
                    type={
                      showPassword
                        ? "text"
                        : "password"
                    }
                    value={password}
                    placeholder="Masukkan password"
                    onChange={(e) =>
                      setPassword(
                        e.target.value
                      )
                    }
                    className="
                      w-full rounded-2xl
                      border border-slate-300
                      pl-12 pr-14 py-3
                      focus:outline-none
                      focus:ring-2
                      focus:ring-red-500
                      focus:border-red-500
                      transition
                    "
                  />

                  <button
                    type="button"
                    onClick={() =>
                      setShowPassword(
                        !showPassword
                      )
                    }
                    className="
                      absolute right-4 top-1/2
                      -translate-y-1/2
                      text-slate-400
                      hover:text-slate-600
                    "
                  >
                    {showPassword ? (
                      <EyeSlashIcon className="w-5 h-5" />
                    ) : (
                      <EyeIcon className="w-5 h-5" />
                    )}
                  </button>

                </div>

              </div>

              {/* Remember Me */}
              <label className="flex items-center gap-2 text-sm text-slate-600">

                <input
                  type="checkbox"
                  checked={rememberMe}
                  onChange={() =>
                    setRememberMe(
                      !rememberMe
                    )
                  }
                  className="
                    rounded
                    border-slate-300
                    text-red-600
                    focus:ring-red-500
                  "
                />

                Remember Me

              </label>

              {/* Submit */}
              <button
                type="submit"
                disabled={loading}
                className="
                  w-full rounded-2xl
                  bg-gradient-to-r
                  from-red-600
                  to-red-700
                  text-white
                  py-3
                  font-semibold
                  shadow-lg
                  hover:shadow-xl
                  hover:scale-[1.01]
                  transition-all
                  duration-200
                  disabled:opacity-70
                  disabled:cursor-not-allowed
                "
              >

                {loading ? (

                  <div className="flex items-center justify-center gap-3">

                    <div className="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin" />

                    <span>
                      Signing In...
                    </span>

                  </div>

                ) : (

                  <div className="flex items-center justify-center gap-2">

                    <span>
                      Sign In
                    </span>

                    <ArrowRightIcon className="w-5 h-5" />

                  </div>

                )}

              </button>

            </form>

          </div>

          <p className="text-center text-sm text-slate-400 mt-6">
            Kellogg's Admin Portal
            <span className="mx-2">
              •
            </span>
            Version 1.0
          </p>

        </div>

      </div>

    </div>
  );
}