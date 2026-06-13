import { useEffect, useState } from "react";

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

import { useAuth } from "../../context/AuthContext";

export default function Profile() {
  const { refreshUser } = useAuth();

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

  const loadProfile = async () => {
  try {
    setLoading(true);

    const data =
      await getProfile();

    setProfile(data);
    setName(data?.name || "");
    setEmail(data?.email || "");
  } catch (error) {
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

  const syncProfile = async (data) => {
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

  const handleProfileSubmit = async (e) => {
    e.preventDefault();

    if (!name.trim()) {
      return errorAlert("Nama wajib diisi");
    }

    if (!email.trim()) {
      return errorAlert("Email wajib diisi");
    }

    try {
      setSavingProfile(true);

      const profile =
  await updateProfile({
    name,
    email,
  });

      await syncProfile(
  profile
);

      await successAlert(
        "Profile berhasil diperbarui"
      );
    } catch (error) {
      const errors =
        error?.response?.data?.errors;

      if (errors) {
        return errorAlert(
          Object.values(errors)[0][0]
        );
      }

      errorAlert(
        error?.response?.data?.message ||
        "Gagal memperbarui profile"
      );
    } finally {
      setSavingProfile(false);
    }
  };

  const handlePasswordSubmit = async (e) => {
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

    try {
      setSavingPassword(true);

      await updatePassword({
        current_password: currentPassword,
        new_password: newPassword,
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
      const errors =
        error?.response?.data?.errors;

      if (errors) {
        return errorAlert(
          Object.values(errors)[0][0]
        );
      }

      errorAlert(
        error?.response?.data?.message ||
        "Gagal memperbarui password"
      );
    } finally {
      setSavingPassword(false);
    }
  };

  const handleAvatarChange = async (e) => {
    const file =
      e.target.files[0];

    if (!file) return;

    const formData =
      new FormData();

    formData.append("avatar", file);

    try {
      setSavingAvatar(true);

      const profile =
  await uploadAvatar(formData);

await syncProfile(profile);

      await successAlert(
        "Avatar berhasil diupload"
      );
    } catch (error) {
      const errors =
        error?.response?.data?.errors;

      if (errors) {
        return errorAlert(
          Object.values(errors)[0][0]
        );
      }

      errorAlert(
        error?.response?.data?.message ||
        "Gagal upload avatar"
      );
    } finally {
      setSavingAvatar(false);
      e.target.value = "";
    }
  };

  const handleDeleteAvatar = async () => {
    const result =
      await confirmDelete();

    if (!result.isConfirmed) {
      return;
    }

    try {
      setSavingAvatar(true);

      const profile =
  await deleteAvatar();

await syncProfile(profile);

      await successAlert(
        "Avatar berhasil dihapus"
      );
    } catch (error) {
      errorAlert(
        error?.response?.data?.message ||
        "Gagal menghapus avatar"
      );
    } finally {
      setSavingAvatar(false);
    }
  };

  if (loading) {
    return <p>Loading...</p>;
  }

  return (
    <div>
      <h1 className="text-3xl font-bold mb-6">
        Profile
      </h1>

      <div className="grid grid-cols-3 gap-6">
        <div className="bg-white rounded shadow p-5">
          <h2 className="text-lg font-semibold mb-4">
            Avatar
          </h2>

          <div className="flex flex-col items-center">
            {profile?.avatar_url ? (
              <img
                src={profile.avatar_url}
                alt={profile.name}
                className="w-32 h-32 rounded-full object-cover border"
              />
            ) : (
              <div className="w-32 h-32 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center text-4xl font-bold">
                {(profile?.name || "U")
                  .charAt(0)
                  .toUpperCase()}
              </div>
            )}

            <label className="mt-4 bg-slate-900 text-white px-4 py-2 rounded cursor-pointer">
              {savingAvatar
                ? "Processing..."
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
                className="mt-3 bg-red-600 text-white px-4 py-2 rounded disabled:opacity-50"
              >
                Delete Avatar
              </button>
            )}
          </div>
        </div>

        <form
          onSubmit={handleProfileSubmit}
          className="bg-white rounded shadow p-5 col-span-2"
        >
          <h2 className="text-lg font-semibold mb-4">
            Profile Information
          </h2>

          <div className="grid grid-cols-2 gap-4">
            <input
              type="text"
              placeholder="Name"
              className="border p-3 rounded disabled:bg-slate-100"
              value={name}
              disabled={savingProfile}
              onChange={(e) =>
                setName(e.target.value)
              }
            />

            <input
              type="email"
              placeholder="Email"
              className="border p-3 rounded disabled:bg-slate-100"
              value={email}
              disabled={savingProfile}
              onChange={(e) =>
                setEmail(e.target.value)
              }
            />
          </div>

          <button
            type="submit"
            disabled={savingProfile}
            className="mt-4 bg-slate-900 text-white px-4 py-2 rounded disabled:opacity-50"
          >
            {savingProfile
              ? "Saving..."
              : "Update Profile"}
          </button>
        </form>
      </div>

      <form
        onSubmit={handlePasswordSubmit}
        className="bg-white rounded shadow p-5 mt-6"
      >
        <h2 className="text-lg font-semibold mb-4">
          Change Password
        </h2>

        <div className="grid grid-cols-3 gap-4">
          <input
            type="password"
            placeholder="Current Password"
            className="border p-3 rounded disabled:bg-slate-100"
            value={currentPassword}
            disabled={savingPassword}
            onChange={(e) =>
              setCurrentPassword(e.target.value)
            }
          />

          <input
            type="password"
            placeholder="New Password"
            className="border p-3 rounded disabled:bg-slate-100"
            value={newPassword}
            disabled={savingPassword}
            onChange={(e) =>
              setNewPassword(e.target.value)
            }
          />

          <input
            type="password"
            placeholder="Confirm New Password"
            className="border p-3 rounded disabled:bg-slate-100"
            value={newPasswordConfirmation}
            disabled={savingPassword}
            onChange={(e) =>
              setNewPasswordConfirmation(
                e.target.value
              )
            }
          />
        </div>

        <button
          type="submit"
          disabled={savingPassword}
          className="mt-4 bg-slate-900 text-white px-4 py-2 rounded disabled:opacity-50"
        >
          {savingPassword
            ? "Saving..."
            : "Change Password"}
        </button>
      </form>
    </div>
  );
}
