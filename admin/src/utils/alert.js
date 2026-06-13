import Swal from "sweetalert2";

export const successAlert = (message) => {
  return Swal.fire({
    icon: "success",
    title: "Berhasil",
    text: message,
    confirmButtonColor: "#0f172a",
    timer: 1500,
    showConfirmButton: false,
  });
};

export const errorAlert = (message) => {
  return Swal.fire({
    icon: "error",
    title: "Oops...",
    text: message,
    confirmButtonColor: "#dc2626",
  });
};

export const confirmDelete = () => {
  return Swal.fire({
    title: "Hapus data?",
    text: "Data yang dihapus tidak dapat dikembalikan.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#dc2626",
    cancelButtonColor: "#64748b",
    confirmButtonText: "Ya, Hapus",
    cancelButtonText: "Batal",
  });
};