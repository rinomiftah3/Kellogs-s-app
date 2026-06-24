import {
  useState,
  useEffect,
} from "react";

import Swal from "sweetalert2";

import {
  XMarkIcon,
  UserCircleIcon,
  CalendarDaysIcon,
  HandThumbUpIcon,
  CheckBadgeIcon,
  PhotoIcon,
  CubeIcon,
  ShieldCheckIcon,
  TrashIcon,
} from "@heroicons/react/24/outline";

import {
  deleteProductReviewImage,
  getProductReview,
} from "../../../services/productReviewService";

import {
  successAlert,
  errorAlert,
} from "../../../utils/alert";

import RatingStars from "./RatingStars";
import ReviewStatusBadge from "./ReviewStatusBadge";

export default function ProductReviewDetail({
  review,
  loading = false,
  canDeleteImage = false,
  onClose,
  onApprove,
  onReject,
}) {

  /*
  |--------------------------------------------------------------------------
  | State
  |--------------------------------------------------------------------------
  */

  const [
    imageDeletingId,
    setImageDeletingId,
  ] = useState(null);

  const [
    localReview,
    setLocalReview,
  ] = useState(review);

  /*
  |--------------------------------------------------------------------------
  | Sync Local Review
  |--------------------------------------------------------------------------
  |
  | Fix:
  | Saat user membuka review lain,
  | localReview harus ikut berubah.
  |
  */

  useEffect(() => {

    setLocalReview(
      review || null
    );

  }, [review]);

  /*
  |--------------------------------------------------------------------------
  | Active Review
  |--------------------------------------------------------------------------
  */

  const activeReview =
    localReview || review;

  /*
  |--------------------------------------------------------------------------
  | Empty State
  |--------------------------------------------------------------------------
  */

  if (
    !activeReview &&
    !loading
  ) {
    return null;
  }

  /*
  |--------------------------------------------------------------------------
  | Delete Review Image
  |--------------------------------------------------------------------------
  */

  const handleDeleteImage =
    async (image) => {

      const confirmed =
        await Swal.fire({

          title:
            "Hapus gambar review?",

          text:
            "Gambar review akan dihapus permanen.",

          icon:
            "warning",

          showCancelButton:
            true,

          confirmButtonColor:
            "#dc2626",

          cancelButtonColor:
            "#64748b",

          confirmButtonText:
            "Hapus",

          cancelButtonText:
            "Batal",
        });

      if (
        !confirmed.isConfirmed
      ) {
        return;
      }

      try {

        setImageDeletingId(
          image.id
        );

        await deleteProductReviewImage(
          image.id
        );

        await successAlert(
          "Gambar review berhasil dihapus."
        );

        /*
        |--------------------------------------------------------------------------
        | Refresh Detail
        |--------------------------------------------------------------------------
        */

        const freshReview =
          await getProductReview(
            activeReview.id
          );

        setLocalReview(
          freshReview
        );

      } catch (error) {

        console.error(error);

        errorAlert(

          error?.response?.data?.message ||

          "Gagal menghapus gambar review."

        );

      } finally {

        setImageDeletingId(
          null
        );

      }

    };

  return (

    <div
      className="
        fixed
        inset-0
        z-50
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
          bg-white
          rounded-3xl
          shadow-2xl
          w-full
          max-w-7xl
          max-h-[95vh]
          overflow-hidden
          flex
          flex-col
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
            bg-white
          "
        >

          <div className="flex items-center gap-4">

            <div
              className="
                w-14
                h-14
                rounded-2xl
                bg-amber-100
                flex
                items-center
                justify-center
              "
            >

              <CheckBadgeIcon
                className="
                  w-7
                  h-7
                  text-amber-600
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
                Product Review Detail
              </h2>

              <p
                className="
                  text-slate-500
                  mt-1
                "
              >
                Review moderation and
                customer feedback
                information.
              </p>

            </div>

          </div>

          <button
            onClick={onClose}
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
                {/* Loading */}

        {loading ? (

          <div
            className="
              py-24
              flex
              flex-col
              items-center
              justify-center
            "
          >

            <div
              className="
                w-14
                h-14
                rounded-full
                border-4
                border-slate-200
                border-t-amber-500
                animate-spin
              "
            />

            <p
              className="
                mt-5
                text-slate-500
              "
            >
              Loading review detail...
            </p>

          </div>

        ) : (

          <div
            className="
              flex-1
              overflow-y-auto
              p-8
              space-y-8
            "
          >

            {/* Summary */}

            <div
              className="
                grid
                grid-cols-1
                md:grid-cols-2
                xl:grid-cols-4
                gap-6
              "
            >

              {/* Reviewer */}

              <div
                className="
                  bg-slate-50
                  rounded-3xl
                  p-6
                "
              >

                <div
                  className="
                    flex
                    items-center
                    gap-2
                    mb-4
                  "
                >

                  <UserCircleIcon
                    className="
                      w-5
                      h-5
                      text-slate-500
                    "
                  />

                  <span
                    className="
                      text-sm
                      text-slate-500
                    "
                  >
                    Reviewer
                  </span>

                </div>

                <p
                  className="
                    text-lg
                    font-bold
                    text-slate-900
                  "
                >
                  {
                    activeReview.customer_name ||

                    activeReview.customer?.full_name ||

                    "-"
                  }
                </p>

                <p
                  className="
                    text-sm
                    text-slate-500
                    mt-1
                  "
                >
                  Customer Review
                </p>

              </div>

              {/* Product */}

              <div
                className="
                  bg-slate-50
                  rounded-3xl
                  p-6
                "
              >

                <div
                  className="
                    flex
                    items-center
                    gap-2
                    mb-4
                  "
                >

                  <CubeIcon
                    className="
                      w-5
                      h-5
                      text-slate-500
                    "
                  />

                  <span
                    className="
                      text-sm
                      text-slate-500
                    "
                  >
                    Product
                  </span>

                </div>

                <p
                  className="
                    text-lg
                    font-bold
                    text-slate-900
                  "
                >
                  {
                    activeReview.product?.name ||

                    "-"
                  }
                </p>

                <p
                  className="
                    text-sm
                    text-slate-500
                    mt-1
                  "
                >
                  Product Review
                </p>

              </div>

              {/* Rating */}

              <div
                className="
                  bg-slate-50
                  rounded-3xl
                  p-6
                "
              >

                <div
                  className="
                    flex
                    items-center
                    gap-2
                    mb-4
                  "
                >

                  <ShieldCheckIcon
                    className="
                      w-5
                      h-5
                      text-slate-500
                    "
                  />

                  <span
                    className="
                      text-sm
                      text-slate-500
                    "
                  >
                    Rating
                  </span>

                </div>

                {/* FIX:
                    Gunakan rating integer
                    bukan rating_stars
                */}

                <RatingStars
                  rating={
                    activeReview.rating ?? 0
                  }
                  size="lg"
                  showLabel
                />

              </div>

              {/* Helpful */}

              <div
                className="
                  bg-slate-50
                  rounded-3xl
                  p-6
                "
              >

                <div
                  className="
                    flex
                    items-center
                    gap-2
                    mb-4
                  "
                >

                  <HandThumbUpIcon
                    className="
                      w-5
                      h-5
                      text-slate-500
                    "
                  />

                  <span
                    className="
                      text-sm
                      text-slate-500
                    "
                  >
                    Helpful
                  </span>

                </div>

                <p
                  className="
                    text-3xl
                    font-bold
                    text-slate-900
                  "
                >
                  {
                    activeReview.helpful_count ??
                    0
                  }
                </p>

                <p
                  className="
                    text-sm
                    text-slate-500
                    mt-1
                  "
                >
                  users found this useful
                </p>

              </div>

            </div>

            {/* Status & Purchase */}

            <div
              className="
                grid
                grid-cols-1
                md:grid-cols-2
                gap-6
              "
            >


                            {/* Review Status */}

              <div
                className="
                  bg-white
                  border
                  border-slate-100
                  rounded-3xl
                  p-6
                "
              >

                <h3
                  className="
                    text-lg
                    font-bold
                    text-slate-900
                    mb-4
                  "
                >
                  Review Status
                </h3>

                <ReviewStatusBadge
                  status={
                    activeReview.status
                  }
                />

                {activeReview.status_label && (

                  <p
                    className="
                      text-sm
                      text-slate-500
                      mt-3
                    "
                  >
                    {
                      activeReview.status_label
                    }
                  </p>

                )}

              </div>

              {/* Verified Purchase */}

              <div
                className="
                  bg-white
                  border
                  border-slate-100
                  rounded-3xl
                  p-6
                "
              >

                <h3
                  className="
                    text-lg
                    font-bold
                    text-slate-900
                    mb-4
                  "
                >
                  Purchase Verification
                </h3>

                {activeReview.is_verified_purchase ? (

                  <span
                    className="
                      inline-flex
                      items-center
                      gap-2
                      px-4
                      py-2
                      rounded-2xl
                      bg-emerald-100
                      text-emerald-700
                      font-semibold
                    "
                  >

                    <CheckBadgeIcon
                      className="
                        w-5
                        h-5
                      "
                    />

                    Verified Purchase

                  </span>

                ) : (

                  <span
                    className="
                      inline-flex
                      px-4
                      py-2
                      rounded-2xl
                      bg-slate-100
                      text-slate-600
                      font-semibold
                    "
                  >
                    Not Verified
                  </span>

                )}

              </div>

            </div>

            {/* Review Content */}

            <div
              className="
                bg-white
                border
                border-slate-100
                rounded-3xl
                p-6
              "
            >

              <h3
                className="
                  text-xl
                  font-bold
                  text-slate-900
                  mb-6
                "
              >
                Review Content
              </h3>

              <div className="mb-6">

                <p
                  className="
                    text-sm
                    font-semibold
                    text-slate-500
                    uppercase
                    tracking-wide
                    mb-2
                  "
                >
                  Title
                </p>

                <p
                  className="
                    text-lg
                    font-semibold
                    text-slate-900
                  "
                >
                  {
                    activeReview.title ||
                    "-"
                  }
                </p>

              </div>

              <div>

                <p
                  className="
                    text-sm
                    font-semibold
                    text-slate-500
                    uppercase
                    tracking-wide
                    mb-2
                  "
                >
                  Review
                </p>

                <div
                  className="
                    rounded-2xl
                    bg-slate-50
                    p-5
                    border
                    border-slate-100
                  "
                >

                  {/* FIX:
                      Backend hanya punya field review
                      Tidak ada field comment
                  */}

                  <p
                    className="
                      whitespace-pre-wrap
                      break-words
                      leading-relaxed
                      text-slate-700
                    "
                  >
                    {
                      activeReview.review ||
                      "-"
                    }
                  </p>

                </div>

              </div>

            </div>

            {/* Review Images */}

            <div
              className="
                bg-white
                border
                border-slate-100
                rounded-3xl
                p-6
              "
            >

              <div
                className="
                  flex
                  items-center
                  justify-between
                  mb-6
                "
              >

                <div
                  className="
                    flex
                    items-center
                    gap-2
                  "
                >

                  <PhotoIcon
                    className="
                      w-6
                      h-6
                      text-slate-500
                    "
                  />

                  <h3
                    className="
                      text-xl
                      font-bold
                      text-slate-900
                    "
                  >
                    Review Images
                  </h3>

                </div>

                <span
                  className="
                    px-3
                    py-1
                    rounded-full
                    bg-slate-100
                    text-sm
                    font-medium
                    text-slate-600
                  "
                >
                  {
                    activeReview.images_count ??
                    0
                  } Images
                </span>

              </div>

              {activeReview.images?.length > 0 ? (

                <div
                  className="
                    grid
                    grid-cols-2
                    md:grid-cols-3
                    lg:grid-cols-4
                    gap-4
                  "
                >
                  {activeReview.images.map(
                    (image) => (

                      <div
                        key={image.id}
                        className="
                          relative
                          overflow-hidden
                          rounded-2xl
                          border
                          border-slate-100
                          bg-slate-50
                          group
                        "
                      >

                        <img
                          src={
                            image.image_url
                          }
                          alt="Review Image"
                          className="
                            w-full
                            h-44
                            object-cover
                          "
                        />

                        {/* FIX:
                            Sinkron dengan
                            ProductReviewImagePolicy

                            Hanya tampil jika
                            user punya permission
                        */}

                        {canDeleteImage && (

                          <button
                            type="button"
                            onClick={() =>
                              handleDeleteImage(
                                image
                              )
                            }
                            disabled={
                              imageDeletingId ===
                              image.id
                            }
                            className="
                              absolute
                              top-2
                              right-2
                              w-9
                              h-9
                              rounded-xl
                              bg-red-600
                              text-white
                              flex
                              items-center
                              justify-center
                              opacity-0
                              group-hover:opacity-100
                              transition
                              disabled:opacity-50
                            "
                          >

                            <TrashIcon
                              className="
                                w-5
                                h-5
                              "
                            />

                          </button>

                        )}

                      </div>

                    )
                  )}

                </div>

              ) : (

                <div
                  className="
                    rounded-2xl
                    bg-slate-50
                    border
                    border-dashed
                    border-slate-200
                    py-14
                    text-center
                  "
                >

                  <PhotoIcon
                    className="
                      w-12
                      h-12
                      mx-auto
                      text-slate-300
                      mb-4
                    "
                  />

                  <p
                    className="
                      text-slate-500
                    "
                  >
                    No review images
                    available.
                  </p>

                </div>

              )}

            </div>

            {/* Moderation Notes */}

            {activeReview.moderation_notes && (

              <div
                className="
                  bg-amber-50
                  border
                  border-amber-200
                  rounded-3xl
                  p-6
                "
              >

                <h3
                  className="
                    text-lg
                    font-bold
                    text-amber-900
                    mb-3
                  "
                >
                  Moderation Notes
                </h3>

                <p
                  className="
                    whitespace-pre-wrap
                    leading-relaxed
                    text-amber-800
                  "
                >
                  {
                    activeReview
                      .moderation_notes
                  }
                </p>

              </div>

            )}

            {/* Timeline */}

            <div
              className="
                bg-white
                border
                border-slate-100
                rounded-3xl
                p-6
              "
            >

              <div
                className="
                  flex
                  items-center
                  gap-2
                  mb-6
                "
              >

                <CalendarDaysIcon
                  className="
                    w-6
                    h-6
                    text-slate-500
                  "
                />

                <h3
                  className="
                    text-xl
                    font-bold
                    text-slate-900
                  "
                >
                  Activity Timeline
                </h3>

              </div>

              <div
                className="
                  grid
                  grid-cols-1
                  md:grid-cols-2
                  gap-6
                "
              >

                <div
                  className="
                    rounded-2xl
                    bg-slate-50
                    p-5
                  "
                >

                  <p
                    className="
                      text-sm
                      font-semibold
                      text-slate-500
                      uppercase
                    "
                  >
                    Created At
                  </p>

                  <p
                    className="
                      mt-2
                      font-semibold
                      text-slate-900
                    "
                  >
                    {
                      activeReview
                        .created_at_human ||

                      activeReview
                        .created_at ||

                      "-"
                    }
                  </p>

                </div>

                <div
                  className="
                    rounded-2xl
                    bg-slate-50
                    p-5
                  "
                >

                  <p
                    className="
                      text-sm
                      font-semibold
                      text-slate-500
                      uppercase
                    "
                  >
                    Updated At
                  </p>

                  <p
                    className="
                      mt-2
                      font-semibold
                      text-slate-900
                    "
                  >
                    {
                      activeReview
                        .updated_at_human ||

                      activeReview
                        .updated_at ||

                      "-"
                    }
                  </p>

                </div>

              </div>

            </div>

          </div>

        )}
                {/* Footer */}

        {!loading && (

          <div
            className="
              border-t
              border-slate-100
              px-8
              py-6
              bg-slate-50
              flex
              flex-col
              sm:flex-row
              sm:justify-between
              gap-4
            "
          >

            <div className="flex gap-3">

              {activeReview?.status ===
                "pending" && (

                <>

                  <button
                    type="button"
                    onClick={() =>
                      onApprove?.(
                        activeReview
                      )
                    }
                    className="
                      px-6
                      py-3
                      rounded-2xl
                      bg-emerald-600
                      hover:bg-emerald-700
                      text-white
                      font-semibold
                      transition
                    "
                  >
                    Approve Review
                  </button>

                  <button
                    type="button"
                    onClick={() =>
                      onReject?.(
                        activeReview
                      )
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
                    "
                  >
                    Reject Review
                  </button>

                </>

              )}

            </div>

            <button
              type="button"
              onClick={onClose}
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
              "
            >
              Close
            </button>

          </div>

        )}

      </div>

    </div>

  );

}