import {
  useEffect,
  useState,
} from "react";

import {
  User,
  Mail,
  Lock,
  Camera,
  Trash2,
  ShieldCheck,
  Eye,
  EyeOff,
  Save,
  KeyRound,
} from "lucide-react";

import {
  getProfile,
  updateProfile,
  updatePassword,
  uploadAvatar,
  deleteAvatar,
} from "../../services/profileService";

import {
  successAlert,
  errorAlert,
  confirmDelete,
} from "../../utils/alert";

import {
  useAuth,
} from "../../context/AuthContext";

export default function Profile() {

  const {
    refreshUser,
  } = useAuth();

  /*
  |--------------------------------------------------------------------------
  | State
  |--------------------------------------------------------------------------
  */

  const [loading, setLoading] =
    useState(true);

  const [savingProfile, setSavingProfile] =
    useState(false);

  const [savingPassword, setSavingPassword] =
    useState(false);

  const [savingAvatar, setSavingAvatar] =
    useState(false);

  const [profile, setProfile] =
    useState(null);

  const [name, setName] =
    useState("");

  const [email, setEmail] =
    useState("");

  const [currentPassword, setCurrentPassword] =
    useState("");

  const [newPassword, setNewPassword] =
    useState("");

  const [
    newPasswordConfirmation,
    setNewPasswordConfirmation,
  ] = useState("");

  /*
  |--------------------------------------------------------------------------
  | Password Visibility
  |--------------------------------------------------------------------------
  */

  const [
    showCurrentPassword,
    setShowCurrentPassword,
  ] = useState(false);

  const [
    showNewPassword,
    setShowNewPassword,
  ] = useState(false);

  const [
    showConfirmPassword,
    setShowConfirmPassword,
  ] = useState(false);

  /*
  |--------------------------------------------------------------------------
  | Load Profile
  |--------------------------------------------------------------------------
  */

  const loadProfile = async () => {

    try {

      setLoading(true);

      const data =
        await getProfile();

      setProfile(data);

      setName(
        data?.name || ""
      );

      setEmail(
        data?.email || ""
      );

    } catch (error) {

      console.error(error);

      errorAlert(
        error?.response?.data?.message ||
        "Gagal memuat profile"
      );

    } finally {

      setLoading(false);

    }

  };

  useEffect(() => {

    loadProfile();

  }, []);
  /*
  |--------------------------------------------------------------------------
  | Sync Profile
  |--------------------------------------------------------------------------
  */

  const syncProfile = async (
    data
  ) => {

    setProfile(data);

    setName(
      data?.name || ""
    );

    setEmail(
      data?.email || ""
    );

    if (refreshUser) {

      await refreshUser();

    }

  };

  /*
  |--------------------------------------------------------------------------
  | Password Strength
  |--------------------------------------------------------------------------
  */

  const passwordStrength = (() => {

    if (!newPassword) {

      return {
        label: "",
        width: "0%",
        color: "",
      };

    }

    let score = 0;

    if (newPassword.length >= 8) {
      score++;
    }

    if (/[A-Z]/.test(newPassword)) {
      score++;
    }

    if (/[0-9]/.test(newPassword)) {
      score++;
    }

    if (/[^A-Za-z0-9]/.test(newPassword)) {
      score++;
    }

    switch (score) {

      case 1:
        return {
          label: "Weak",
          width: "25%",
          color: "bg-red-500",
        };

      case 2:
        return {
          label: "Fair",
          width: "50%",
          color: "bg-amber-500",
        };

      case 3:
        return {
          label: "Good",
          width: "75%",
          color: "bg-blue-500",
        };

      case 4:
        return {
          label: "Strong",
          width: "100%",
          color: "bg-green-500",
        };

      default:
        return {
          label: "Very Weak",
          width: "10%",
          color: "bg-red-500",
        };

    }

  })();

  /*
  |--------------------------------------------------------------------------
  | Update Profile
  |--------------------------------------------------------------------------
  */

  const handleProfileSubmit =
    async (e) => {

      e.preventDefault();

      if (!name.trim()) {

        return errorAlert(
          "Nama wajib diisi"
        );

      }

      if (!email.trim()) {

        return errorAlert(
          "Email wajib diisi"
        );

      }

      try {

        setSavingProfile(true);

        const updatedProfile =
          await updateProfile({
            name,
            email,
          });

        await syncProfile(
          updatedProfile
        );

        await successAlert(
          "Profile berhasil diperbarui"
        );

      } catch (error) {

        console.error(error);

        const errors =
          error?.response?.data
            ?.errors;

        if (errors) {

          return errorAlert(
            Object.values(errors)[0][0]
          );

        }

        errorAlert(
          error?.response?.data
            ?.message ||
          "Gagal memperbarui profile"
        );

      } finally {

        setSavingProfile(false);

      }

    };

  /*
  |--------------------------------------------------------------------------
  | Update Password
  |--------------------------------------------------------------------------
  */

  const handlePasswordSubmit =
    async (e) => {

      e.preventDefault();

      if (
        !currentPassword ||
        !newPassword ||
        !newPasswordConfirmation
      ) {

        return errorAlert(
          "Semua field password wajib diisi"
        );

      }

      if (
        newPassword !==
        newPasswordConfirmation
      ) {

        return errorAlert(
          "Konfirmasi password tidak sesuai"
        );

      }

      try {

        setSavingPassword(true);

        await updatePassword({

          current_password:
            currentPassword,

          new_password:
            newPassword,

          new_password_confirmation:
            newPasswordConfirmation,

        });

        setCurrentPassword("");

        setNewPassword("");

        setNewPasswordConfirmation("");

        await successAlert(
          "Password berhasil diperbarui"
        );

      } catch (error) {

        console.error(error);

        const errors =
          error?.response?.data
            ?.errors;

        if (errors) {

          return errorAlert(
            Object.values(errors)[0][0]
          );

        }

        errorAlert(
          error?.response?.data
            ?.message ||
          "Gagal memperbarui password"
        );

      } finally {

        setSavingPassword(false);

      }

    };
  /*
  |--------------------------------------------------------------------------
  | Upload Avatar
  |--------------------------------------------------------------------------
  */

  const handleAvatarChange =
    async (e) => {

      const file =
        e.target.files?.[0];

      if (!file) return;

      const formData =
        new FormData();

      formData.append(
        "avatar",
        file
      );

      try {

        setSavingAvatar(true);

        const updatedProfile =
          await uploadAvatar(
            formData
          );

        await syncProfile(
          updatedProfile
        );

        await successAlert(
          "Avatar berhasil diupload"
        );

      } catch (error) {

        console.error(error);

        const errors =
          error?.response?.data
            ?.errors;

        if (errors) {

          return errorAlert(
            Object.values(errors)[0][0]
          );

        }

        errorAlert(
          error?.response?.data
            ?.message ||
          "Gagal upload avatar"
        );

      } finally {

        setSavingAvatar(false);

        e.target.value = "";

      }

    };

  /*
  |--------------------------------------------------------------------------
  | Delete Avatar
  |--------------------------------------------------------------------------
  */

  const handleDeleteAvatar =
    async () => {

      const result =
        await confirmDelete();

      if (
        !result.isConfirmed
      ) {

        return;

      }

      try {

        setSavingAvatar(true);

        const updatedProfile =
          await deleteAvatar();

        await syncProfile(
          updatedProfile
        );

        await successAlert(
          "Avatar berhasil dihapus"
        );

      } catch (error) {

        console.error(error);

        errorAlert(
          error?.response?.data
            ?.message ||
          "Gagal menghapus avatar"
        );

      } finally {

        setSavingAvatar(false);

      }

    };

  /*
  |--------------------------------------------------------------------------
  | Loading State
  |--------------------------------------------------------------------------
  */

  if (loading) {

    return (

      <div className="space-y-6 animate-pulse">

        <div className="h-48 rounded-3xl bg-slate-200" />

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">

          <div className="h-96 rounded-3xl bg-slate-200" />

          <div className="lg:col-span-2 h-96 rounded-3xl bg-slate-200" />

        </div>

        <div className="h-80 rounded-3xl bg-slate-200" />

      </div>

    );

  }

  return (

    <div className="space-y-6">

      {/* Hero */}
      <div
        className="
          rounded-3xl
          bg-gradient-to-r
          from-slate-900
          via-slate-800
          to-slate-900
          p-8
          text-white
          shadow-xl
          relative
          overflow-hidden
        "
      >

        <div className="absolute -top-20 -right-20 w-72 h-72 rounded-full bg-white/5" />

        <div className="absolute -bottom-24 -left-24 w-80 h-80 rounded-full bg-white/5" />

        <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">

          <div className="flex items-center gap-5">

            <div
              className="
                w-16
                h-16
                rounded-2xl
                bg-white/10
                backdrop-blur
                flex
                items-center
                justify-center
              "
            >
              <User className="w-8 h-8 text-red-400" />
            </div>

            <div>

              <h1 className="text-4xl font-bold">
                My Profile
              </h1>

              <p className="text-slate-300 mt-2">
                Manage your personal information,
                avatar, and account security.
              </p>

            </div>

          </div>

          <div className="flex flex-wrap gap-3">

            <div className="bg-white/10 backdrop-blur rounded-2xl px-4 py-2 text-sm">

              {profile?.roles?.[0]?.name ||
                "Administrator"}

            </div>

            <div className="bg-white/10 backdrop-blur rounded-2xl px-4 py-2 text-sm">

              Active Account

            </div>

          </div>

        </div>

      </div>
      {/* Main Content */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {/* Avatar Card */}
        <div className="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">

          <div className="flex flex-col items-center">

            {profile?.avatar_url ? (

              <img
                src={profile.avatar_url}
                alt={profile.name}
                className="
                  w-36
                  h-36
                  rounded-full
                  object-cover
                  ring-4
                  ring-red-100
                  shadow-lg
                "
              />

            ) : (

              <div
                className="
                  w-36
                  h-36
                  rounded-full
                  bg-gradient-to-br
                  from-red-500
                  to-red-600
                  text-white
                  flex
                  items-center
                  justify-center
                  text-5xl
                  font-bold
                  shadow-lg
                "
              >
                {(profile?.name || "U")
                  .charAt(0)
                  .toUpperCase()}
              </div>

            )}

            <h2 className="mt-6 text-2xl font-bold text-slate-900">
              {profile?.name}
            </h2>

            <p className="text-slate-500 mt-1">
              {profile?.email}
            </p>

            <span
              className="
                mt-4
                inline-flex
                items-center
                gap-2
                px-4
                py-2
                rounded-full
                bg-red-50
                text-red-700
                text-sm
                font-medium
              "
            >
              <ShieldCheck className="w-4 h-4" />
              {profile?.roles?.[0]?.name ||
                "Administrator"}
            </span>

            <label
              className="
                mt-8
                w-full
                inline-flex
                items-center
                justify-center
                gap-2
                px-6
                py-3
                rounded-2xl
                bg-gradient-to-r
                from-red-600
                to-red-700
                text-white
                font-semibold
                cursor-pointer
                shadow-lg
                hover:shadow-xl
                hover:scale-[1.01]
                transition-all
              "
            >
              <Camera className="w-5 h-5" />

              {savingAvatar
                ? "Uploading..."
                : "Upload Avatar"}

              <input
                type="file"
                accept=".jpg,.jpeg,.png,.webp"
                className="hidden"
                disabled={savingAvatar}
                onChange={handleAvatarChange}
              />
            </label>

            {profile?.avatar_url && (

              <button
                type="button"
                disabled={savingAvatar}
                onClick={handleDeleteAvatar}
                className="
                  mt-3
                  w-full
                  inline-flex
                  items-center
                  justify-center
                  gap-2
                  px-6
                  py-3
                  rounded-2xl
                  border
                  border-red-200
                  text-red-600
                  hover:bg-red-50
                  transition
                "
              >
                <Trash2 className="w-5 h-5" />
                Remove Avatar
              </button>

            )}

          </div>

        </div>

        {/* Profile Information */}
        <div className="lg:col-span-2 space-y-6">

          <form
            onSubmit={handleProfileSubmit}
            className="bg-white rounded-3xl shadow-sm border border-slate-100 p-8"
          >

            <h2 className="text-2xl font-bold text-slate-900">
              Profile Information
            </h2>

            <p className="text-slate-500 mt-2">
              Update your account information.
            </p>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">

              <div>

                <label className="block text-sm font-medium text-slate-700 mb-2">
                  Full Name
                </label>

                <input
                  type="text"
                  value={name}
                  disabled={savingProfile}
                  onChange={(e) =>
                    setName(e.target.value)
                  }
                  className="
                    w-full
                    rounded-2xl
                    border
                    border-slate-200
                    px-4
                    py-3
                    focus:outline-none
                    focus:ring-2
                    focus:ring-red-500
                  "
                />

              </div>

              <div>

                <label className="block text-sm font-medium text-slate-700 mb-2">
                  Email Address
                </label>

                <div className="relative">

                  <Mail
                    className="
                      absolute
                      left-4
                      top-1/2
                      -translate-y-1/2
                      w-5
                      h-5
                      text-slate-400
                    "
                  />

                  <input
                    type="email"
                    value={email}
                    disabled={savingProfile}
                    onChange={(e) =>
                      setEmail(e.target.value)
                    }
                    className="
                      w-full
                      rounded-2xl
                      border
                      border-slate-200
                      pl-12
                      pr-4
                      py-3
                      focus:outline-none
                      focus:ring-2
                      focus:ring-red-500
                    "
                  />

                </div>

              </div>

            </div>

            <button
              type="submit"
              disabled={savingProfile}
              className="
                mt-8
                inline-flex
                items-center
                gap-2
                px-6
                py-3
                rounded-2xl
                bg-gradient-to-r
                from-red-600
                to-red-700
                text-white
                font-semibold
                shadow-lg
                hover:shadow-xl
                hover:scale-[1.01]
                transition-all
              "
            >
              <Save className="w-5 h-5" />

              {savingProfile
                ? "Saving..."
                : "Save Changes"}

            </button>

          </form>

          {/* Change Password */}
          <form
            onSubmit={handlePasswordSubmit}
            className="bg-white rounded-3xl shadow-sm border border-slate-100 p-8"
          >

            <h2 className="text-2xl font-bold text-slate-900">
              Account Security
            </h2>

            <p className="text-slate-500 mt-2">
              Update your password regularly.
            </p>

            <div className="grid grid-cols-1 gap-6 mt-8">

              {/* Current Password */}
              <div>

                <label className="block text-sm font-medium text-slate-700 mb-2">
                  Current Password
                </label>

                {/* Input Current Password */}
                {/* (gunakan showCurrentPassword) */}

              </div>

              {/* New Password */}
              <div>

                <label className="block text-sm font-medium text-slate-700 mb-2">
                  New Password
                </label>

                {/* Input New Password */}
                {/* (gunakan showNewPassword) */}

                {newPassword && (

                  <div className="mt-3">

                    <div className="h-2 rounded-full bg-slate-100 overflow-hidden">

                      <div
                        className={`h-full ${passwordStrength.color}`}
                        style={{
                          width:
                            passwordStrength.width,
                        }}
                      />

                    </div>

                    <p className="text-xs text-slate-500 mt-2">
                      Strength:
                      {" "}
                      {passwordStrength.label}
                    </p>

                  </div>

                )}

              </div>

              {/* Confirm Password */}
              <div>

                <label className="block text-sm font-medium text-slate-700 mb-2">
                  Confirm Password
                </label>

                {/* Input Confirm Password */}
                {/* (gunakan showConfirmPassword) */}

              </div>

            </div>

            <button
              type="submit"
              disabled={savingPassword}
              className="
                mt-8
                inline-flex
                items-center
                gap-2
                px-6
                py-3
                rounded-2xl
                bg-gradient-to-r
                from-red-600
                to-red-700
                text-white
                font-semibold
                shadow-lg
                hover:shadow-xl
                hover:scale-[1.01]
                transition-all
              "
            >
              <KeyRound className="w-5 h-5" />

              {savingPassword
                ? "Updating..."
                : "Change Password"}

            </button>

          </form>

        </div>

      </div>

    </div>
  );
}