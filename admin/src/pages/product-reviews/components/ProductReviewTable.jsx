import {
  Eye,
  Check,
  X,
  Trash2,
  BadgeCheck,
  Image as ImageIcon,
  ThumbsUp,
  Calendar,
} from "lucide-react";

import RatingStars from "./RatingStars";
import ReviewStatusBadge from "./ReviewStatusBadge";

export default function ProductReviewTable({
  reviews = [],
  canApprove = false,
  canReject = false,
  canDelete = false,
  onDetail,
  onApprove,
  onReject,
  onDelete,
}) {

  return (

    <table className="w-full">

      <thead className="bg-slate-50">

        <tr>

          <th
            className="
              px-6
              py-4
              text-left
              text-sm
              font-semibold
              text-slate-600
            "
          >
            Customer
          </th>

          <th
            className="
              px-6
              py-4
              text-left
              text-sm
              font-semibold
              text-slate-600
            "
          >
            Product & Review
          </th>

          <th
            className="
              px-6
              py-4
              text-left
              text-sm
              font-semibold
              text-slate-600
            "
          >
            Rating
          </th>

          <th
            className="
              px-6
              py-4
              text-left
              text-sm
              font-semibold
              text-slate-600
            "
          >
            Status
          </th>

          <th
            className="
              px-6
              py-4
              text-left
              text-sm
              font-semibold
              text-slate-600
            "
          >
            Info
          </th>

          <th
            className="
              px-6
              py-4
              text-right
              text-sm
              font-semibold
              text-slate-600
            "
          >
            Action
          </th>

        </tr>

      </thead>

      <tbody>

        {reviews.map((review) => (

          <tr
            key={review.id}
            className="
              border-t
              border-slate-100
              hover:bg-slate-50
              transition
            "
          >

            {/* Customer */}

            <td className="px-6 py-5 align-top">

              <div>

                <p
                  className="
                    font-semibold
                    text-slate-900
                  "
                >
                  {
                    review.customer_name ||

                    review.customer?.full_name ||

                    "-"
                  }
                </p>

                <p
                  className="
                    text-xs
                    text-slate-400
                  "
                >
                  Customer ID #
                  {review.customer_profile_id}
                </p>

                <div
                  className="
                    flex
                    items-center
                    gap-1
                    mt-2
                    text-xs
                    text-slate-500
                  "
                >

                  <Calendar
                    className="
                      w-3
                      h-3
                    "
                  />

                  {
                    review.created_at_human ||
                    "-"
                  }

                </div>

              </div>

            </td>
                        {/* Product & Review */}

            <td className="px-6 py-5 align-top">

              <div className="space-y-2">

                <div>

                  <p
                    className="
                      font-semibold
                      text-slate-900
                    "
                  >
                    {
                      review.product?.name ||
                      "-"
                    }
                  </p>

                  <p
                    className="
                      text-xs
                      text-slate-400
                    "
                  >
                    Product ID #
                    {review.product_id}
                  </p>

                </div>

                <div>

                  <p
                    className="
                      text-sm
                      font-medium
                      text-slate-700
                      line-clamp-1
                    "
                  >
                    {
                      review.title ||
                      "Tanpa Judul"
                    }
                  </p>

                  <p
                    className="
                      text-xs
                      text-slate-500
                      line-clamp-2
                      max-w-[280px]
                    "
                  >
                    {
                      review.review ||
                      "-"
                    }
                  </p>

                </div>

              </div>

            </td>

            {/* Rating */}

            <td className="px-6 py-5 align-top">

              <div className="space-y-2">

                <RatingStars
                  rating={
                    review.rating ?? 0
                  }
                />

                <p
                  className="
                    text-xs
                    text-slate-500
                  "
                >
                  {
                    review.rating_stars ||

                    `${review.rating}/5`
                  }
                </p>

                <p
                  className="
                    text-xs
                    text-slate-400
                  "
                >
                  {
                    review.rating_percentage
                  }%
                </p>

              </div>

            </td>

            {/* Status */}

            <td className="px-6 py-5 align-top">

              <div className="space-y-2">

                <ReviewStatusBadge
                  status={
                    review.status
                  }
                />

                {review.status_label && (

                  <p
                    className="
                      text-xs
                      text-slate-500
                    "
                  >
                    {
                      review.status_label
                    }
                  </p>

                )}

              </div>

            </td>
                        {/* Info */}

            <td className="px-6 py-5 align-top">

              <div
                className="
                  flex
                  flex-col
                  gap-3
                "
              >

                {review.is_verified_purchase && (

                  <span
                    className="
                      inline-flex
                      items-center
                      gap-1
                      text-xs
                      font-semibold
                      text-emerald-600
                    "
                  >

                    <BadgeCheck
                      className="
                        w-4
                        h-4
                      "
                    />

                    Verified Purchase

                  </span>

                )}

                <div
                  className="
                    flex
                    items-center
                    gap-4
                    text-xs
                    text-slate-500
                  "
                >

                  <span
                    className="
                      flex
                      items-center
                      gap-1
                    "
                  >

                    <ImageIcon
                      className="
                        w-4
                        h-4
                      "
                    />

                    {
                      review.images_count ?? 0
                    }

                  </span>

                  <span
                    className="
                      flex
                      items-center
                      gap-1
                    "
                  >

                    <ThumbsUp
                      className="
                        w-4
                        h-4
                      "
                    />

                    {
                      review.helpful_count ?? 0
                    }

                  </span>

                </div>

                {review.has_images && (

                  <span
                    className="
                      inline-flex
                      items-center
                      rounded-full
                      bg-blue-50
                      text-blue-700
                      text-xs
                      font-medium
                      px-2
                      py-1
                      w-fit
                    "
                  >
                    Memiliki Gambar
                  </span>

                )}

                {review.moderation_notes && (

                  <div
                    className="
                      text-xs
                      text-amber-700
                      bg-amber-50
                      border
                      border-amber-200
                      rounded-xl
                      p-2
                      max-w-[220px]
                      line-clamp-2
                    "
                  >

                    {
                      review.moderation_notes
                    }

                  </div>

                )}

              </div>

            </td>
                        {/* Actions */}

            <td className="px-6 py-5 align-top">

              <div
                className="
                  flex
                  justify-end
                  gap-2
                  flex-wrap
                "
              >

                {/* Detail */}

                <button
                  type="button"
                  onClick={() =>
                    onDetail?.(
                      review.id
                    )
                  }
                  className="
                    inline-flex
                    items-center
                    justify-center
                    w-10
                    h-10
                    rounded-xl
                    bg-blue-50
                    text-blue-700
                    hover:bg-blue-100
                    transition
                  "
                  title="Detail"
                >

                  <Eye
                    className="
                      w-4
                      h-4
                    "
                  />

                </button>

                {/* Approve */}

                {canApprove &&
                  review.is_pending && (

                  <button
                    type="button"
                    onClick={() =>
                      onApprove?.(
                        review
                      )
                    }
                    className="
                      inline-flex
                      items-center
                      justify-center
                      w-10
                      h-10
                      rounded-xl
                      bg-green-50
                      text-green-700
                      hover:bg-green-100
                      transition
                    "
                    title="Approve Review"
                  >

                    <Check
                      className="
                        w-4
                        h-4
                      "
                    />

                  </button>

                )}

                {/* Reject */}

                {canReject &&
                  review.is_pending && (

                  <button
                    type="button"
                    onClick={() =>
                      onReject?.(
                        review
                      )
                    }
                    className="
                      inline-flex
                      items-center
                      justify-center
                      w-10
                      h-10
                      rounded-xl
                      bg-amber-50
                      text-amber-700
                      hover:bg-amber-100
                      transition
                    "
                    title="Reject Review"
                  >

                    <X
                      className="
                        w-4
                        h-4
                      "
                    />

                  </button>

                )}

                {/* Delete */}

                {canDelete && (

                  <button
                    type="button"
                    onClick={() =>
                      onDelete?.(
                        review
                      )
                    }
                    className="
                      inline-flex
                      items-center
                      justify-center
                      w-10
                      h-10
                      rounded-xl
                      bg-red-50
                      text-red-700
                      hover:bg-red-100
                      transition
                    "
                    title="Delete Review"
                  >

                    <Trash2
                      className="
                        w-4
                        h-4
                      "
                    />

                  </button>

                )}

              </div>

            </td>

          </tr>

        ))}

      </tbody>

    </table>

  );

}