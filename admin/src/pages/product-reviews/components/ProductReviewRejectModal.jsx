import {
  useState,
} from "react";

import {
  XMarkIcon,
  ExclamationTriangleIcon,
} from "@heroicons/react/24/outline";

export default function ProductReviewRejectModal({
  open = false,
  review = null,
  loading = false,
  onClose,
  onSubmit,
}) {

  /*
  |--------------------------------------------------------------------------
  | State
  |--------------------------------------------------------------------------
  */

  const [
    notes,
    setNotes,
  ] = useState("");

  const [
    submitting,
    setSubmitting,
  ] = useState(false);

  /*
  |--------------------------------------------------------------------------
  | Submit
  |--------------------------------------------------------------------------
  */

  const handleSubmit =
    async (e) => {

      e.preventDefault();

      const value =
        notes.trim();

      /*
      |--------------------------------------------------------------------------
      | Backend Validation Sync
      |--------------------------------------------------------------------------
      |
      | moderation_notes:
      | required|string
      |
      */

      if (!value) {
        return;
      }

      if (
        value.length < 5
      ) {
        return;
      }

      try {

        setSubmitting(
          true
        );

        await onSubmit?.(
          value
        );

        setNotes("");

      } finally {

        setSubmitting(
          false
        );

      }

    };

  /*
  |--------------------------------------------------------------------------
  | Review Information
  |--------------------------------------------------------------------------
  */

  const customerName =

    review?.customer_name ||

    review?.customer?.full_name ||

    "-";

  const productName =

    review?.product?.name ||

    "-";

  const reviewTitle =

    review?.title ||

    "Tanpa Judul";

  const reviewContent =

    review?.review ||

    "Tidak ada isi review.";

  const disabled =

    loading ||

    submitting;
    if (!open) {
  return null;
}
  return (

    <div
      className="
        fixed
        inset-0
        z-[60]
        flex
        items-center
        justify-center
        bg-black/60
        backdrop-blur-sm
        p-4
      "
    >

      <div
        className="
          w-full
          max-w-xl
          rounded-3xl
          bg-white
          shadow-2xl
          overflow-hidden
        "
      >

        {/* Header */}

        <div
          className="
            px-8
            py-6
            border-b
            border-slate-100
            flex
            items-center
            justify-between
          "
        >

          <div
            className="
              flex
              items-center
              gap-4
            "
          >

            <div
              className="
                w-14
                h-14
                rounded-2xl
                bg-red-100
                flex
                items-center
                justify-center
              "
            >

              <ExclamationTriangleIcon
                className="
                  w-7
                  h-7
                  text-red-600
                "
              />

            </div>

            <div>

              <h2
                className="
                  text-2xl
                  font-bold
                  text-slate-900
                "
              >
                Reject Product Review
              </h2>

              <p
                className="
                  text-slate-500
                  mt-1
                "
              >
                Berikan alasan moderasi
                untuk review yang akan ditolak.
              </p>

            </div>

          </div>

          <button
            type="button"
            onClick={onClose}
            disabled={disabled}
            className="
              w-11
              h-11
              rounded-2xl
              bg-slate-100
              hover:bg-slate-200
              transition
              flex
              items-center
              justify-center
              disabled:opacity-50
              disabled:cursor-not-allowed
            "
          >

            <XMarkIcon
              className="
                w-6
                h-6
                text-slate-700
              "
            />

          </button>

        </div>

        <form
          onSubmit={handleSubmit}
        >
                    <div
            className="
              px-8
              py-8
              space-y-6
            "
          >

            {/* Review Information */}

            <div
              className="
                rounded-2xl
                bg-slate-50
                border
                border-slate-100
                p-5
              "
            >

              <p
                className="
                  text-sm
                  text-slate-500
                "
              >
                Review yang akan ditolak:
              </p>

              <p
                className="
                  mt-2
                  font-semibold
                  text-slate-900
                "
              >
                {reviewTitle}
              </p>

              <p
                className="
                  mt-1
                  text-xs
                  text-slate-500
                "
              >
                {customerName}
                {" • "}
                {productName}
              </p>

              <p
                className="
                  mt-3
                  text-sm
                  text-slate-500
                  line-clamp-3
                "
              >
                {reviewContent}
              </p>

            </div>

            {/* Moderation Notes */}

            <div>

              <label
                className="
                  block
                  text-sm
                  font-semibold
                  text-slate-700
                  mb-2
                "
              >
                Moderation Notes

                <span
                  className="
                    text-red-500
                  "
                >
                  {" "}*
                </span>

              </label>

              <textarea
                rows={6}
                value={notes}
                onChange={(e) =>
                  setNotes(
                    e.target.value
                  )
                }
                disabled={disabled}
                placeholder="
Review mengandung spam,
bahasa tidak pantas,
konten promosi,
atau tidak relevan dengan produk.
                "
                className="
                  w-full
                  rounded-2xl
                  border
                  border-slate-200
                  px-4
                  py-3
                  resize-none
                  focus:outline-none
                  focus:ring-2
                  focus:ring-red-500
                  focus:border-red-500
                  disabled:bg-slate-100
                "
              />

              <div
                className="
                  mt-2
                  flex
                  justify-between
                  text-xs
                  text-slate-400
                "
              >

                <span>

                  Minimal 5 karakter.

                </span>

                <span>

                  {notes.length}
                  {" "}
                  karakter

                </span>

              </div>

            </div>
                      </div>

          {/* Footer */}

          <div
            className="
              px-8
              py-6
              border-t
              border-slate-100
              bg-slate-50
              flex
              flex-col-reverse
              sm:flex-row
              sm:justify-end
              gap-3
            "
          >

            <button
              type="button"
              onClick={onClose}
              disabled={disabled}
              className="
                px-6
                py-3
                rounded-2xl
                border
                border-slate-300
                bg-white
                hover:bg-slate-100
                text-slate-700
                font-semibold
                transition
                disabled:opacity-50
                disabled:cursor-not-allowed
              "
            >
              Batal
            </button>

            <button
              type="submit"
              disabled={
                disabled
                ||
                notes.trim().length < 5
              }
              className="
                px-6
                py-3
                rounded-2xl
                bg-red-600
                hover:bg-red-700
                text-white
                font-semibold
                transition
                disabled:opacity-50
                disabled:cursor-not-allowed
              "
            >

              {submitting
                ? "Memproses..."
                : "Reject Review"}

            </button>

          </div>

        </form>

      </div>

    </div>

  );

}