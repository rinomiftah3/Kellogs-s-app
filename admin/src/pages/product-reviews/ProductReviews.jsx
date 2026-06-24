import {
  useEffect,
  useMemo,
  useState,
} from "react";

import Swal from "sweetalert2";

import usePermission from "../../hooks/usePermission";

import {
  getProductReviews,
  getProductReview,
  approveProductReview,
  rejectProductReview,
  deleteProductReview,
} from "../../services/productReviewService";

import {
  successAlert,
  errorAlert,
  confirmDelete,
} from "../../utils/alert";

import ProductReviewTable from "./components/ProductReviewTable";
import ProductReviewDetail from "./components/ProductReviewDetail";
import ProductReviewRejectModal from "./components/ProductReviewRejectModal";

import {
  MessageSquare,
  Search,
  Clock3,
  CheckCircle2,
  XCircle,
} from "lucide-react";

export default function ProductReviews() {

  const { can } =
    usePermission();

  /*
  |--------------------------------------------------------------------------
  | State
  |--------------------------------------------------------------------------
  */

  const [reviews, setReviews] =
    useState([]);

  const [
    selectedReview,
    setSelectedReview,
  ] = useState(null);

  const [loading, setLoading] =
    useState(false);

  const [
    detailLoading,
    setDetailLoading,
  ] = useState(false);

  const [
    rejectLoading,
    setRejectLoading,
  ] = useState(false);

  const [search, setSearch] =
    useState("");

  const [
    statusFilter,
    setStatusFilter,
  ] = useState("");

  const [
    ratingFilter,
    setRatingFilter,
  ] = useState("");

  const [
    rejectModalOpen,
    setRejectModalOpen,
  ] = useState(false);

  const [
    rejectTarget,
    setRejectTarget,
  ] = useState(null);

  /*
  |--------------------------------------------------------------------------
  | Permissions
  |--------------------------------------------------------------------------
  */

  const canView =
    can("product_reviews.view");

  const canApprove =
    can("product_reviews.update");

  const canReject =
    can("product_reviews.update");

  const canDelete =
    can("product_reviews.delete");

  /*
  |--------------------------------------------------------------------------
  | Load Reviews
  |--------------------------------------------------------------------------
  */

  const loadReviews =
    async () => {

      try {

        setLoading(true);

        const response =
          await getProductReviews();

        setReviews(
          Array.isArray(
            response?.data
          )
            ? response.data
            : []
        );

      } catch (error) {

        console.error(error);

        setReviews([]);

        errorAlert(

          error?.response?.data
            ?.message ||

          "Gagal mengambil data review."

        );

      } finally {

        setLoading(false);

      }

    };

  /*
  |--------------------------------------------------------------------------
  | Initial Load
  |--------------------------------------------------------------------------
  */

  useEffect(() => {

    if (canView) {

      loadReviews();

    }

  }, [canView]);

  /*
  |--------------------------------------------------------------------------
  | Statistics
  |--------------------------------------------------------------------------
  */

  const totalReviews =
    reviews.length;

  const pendingReviews =
    reviews.filter(
      (review) =>
        review?.is_pending
    ).length;

  const approvedReviews =
    reviews.filter(
      (review) =>
        review?.is_approved
    ).length;

  const rejectedReviews =
    reviews.filter(
      (review) =>
        review?.is_rejected
    ).length;

  /*
  |--------------------------------------------------------------------------
  | Filtering
  |--------------------------------------------------------------------------
  */

  const filteredReviews =
    useMemo(() => {

      return reviews.filter(
        (review) => {

          const keyword =
            search
              .trim()
              .toLowerCase();

          const customerName =

            review
              ?.customer_name ||

            review
              ?.customer
              ?.full_name ||

            review
              ?.customer
              ?.name ||

            "";

          const productName =

            review
              ?.product_name ||

            review
              ?.product
              ?.name ||

            "";

          const title =

            review?.title ||
            "";

          const content =

            review?.review ||

            review?.comment ||

            "";

          const matchSearch =

            !keyword ||

            customerName
              .toLowerCase()
              .includes(
                keyword
              ) ||

            productName
              .toLowerCase()
              .includes(
                keyword
              ) ||

            title
              .toLowerCase()
              .includes(
                keyword
              ) ||

            content
              .toLowerCase()
              .includes(
                keyword
              );

          const matchStatus =

            !statusFilter ||

            (
              statusFilter ===
                "pending" &&
              review?.is_pending
            ) ||

            (
              statusFilter ===
                "approved" &&
              review?.is_approved
            ) ||

            (
              statusFilter ===
                "rejected" &&
              review?.is_rejected
            );

          const matchRating =

            !ratingFilter ||

            Number(
              review?.rating
            ) ===
              Number(
                ratingFilter
              );

          return (

            matchSearch &&
            matchStatus &&
            matchRating

          );

        }
      );

    }, [

      reviews,

      search,

      statusFilter,

      ratingFilter,

    ]);
      /*
  |--------------------------------------------------------------------------
  | Detail
  |--------------------------------------------------------------------------
  */

  const handleDetail =
    async (id) => {

      try {

        setDetailLoading(
          true
        );

        const review =
          await getProductReview(
            id
          );

        setSelectedReview(
          review || null
        );

      } catch (error) {

        console.error(
          error
        );

        errorAlert(

          error?.response?.data
            ?.message ||

          "Gagal mengambil detail review."

        );

      } finally {

        setDetailLoading(
          false
        );

      }

    };

  const closeDetail =
    () => {

      setSelectedReview(
        null
      );

    };

  /*
  |--------------------------------------------------------------------------
  | Approve Review
  |--------------------------------------------------------------------------
  */

  const handleApprove =
    async (review) => {

      const result =
        await Swal.fire({

          title:
            "Approve Review?",

          text:
            "Review akan ditampilkan kepada pelanggan.",

          icon:
            "question",

          showCancelButton:
            true,

          confirmButtonText:
            "Approve",

          cancelButtonText:
            "Batal",

          confirmButtonColor:
            "#16a34a",

        });

      if (
        !result.isConfirmed
      ) {

        return;

      }

      try {

        await approveProductReview(
          review.id
        );

        await successAlert(
          "Review berhasil disetujui."
        );

        await loadReviews();

        /*
        |--------------------------------------------------------------------------
        | Refresh Open Detail
        |--------------------------------------------------------------------------
        */

        if (

          selectedReview?.id ===
          review.id

        ) {

          const freshReview =
            await getProductReview(
              review.id
            );

          setSelectedReview(
            freshReview
          );

        }

      } catch (error) {

        console.error(
          error
        );

        errorAlert(

          error?.response?.data
            ?.message ||

          "Gagal approve review."

        );

      }

    };

  /*
  |--------------------------------------------------------------------------
  | Reject Review
  |--------------------------------------------------------------------------
  */

  const handleReject =
    (review) => {

      setRejectTarget(
        review
      );

      setRejectModalOpen(
        true
      );

    };

  const submitReject =
    async (notes) => {

      if (
        !rejectTarget
      ) {

        return;

      }

      try {

        setRejectLoading(
          true
        );

        await rejectProductReview(

          rejectTarget.id,

          notes

        );

        await successAlert(
          "Review berhasil ditolak."
        );

        setRejectModalOpen(
          false
        );

        await loadReviews();

        /*
        |--------------------------------------------------------------------------
        | Refresh Detail
        |--------------------------------------------------------------------------
        */

        if (

          selectedReview?.id ===
          rejectTarget.id

        ) {

          const freshReview =
            await getProductReview(
              rejectTarget.id
            );

          setSelectedReview(
            freshReview
          );

        }

        setRejectTarget(
          null
        );

      } catch (error) {

        console.error(
          error
        );

        errorAlert(

          error?.response?.data
            ?.message ||

          "Gagal menolak review."

        );

      } finally {

        setRejectLoading(
          false
        );

      }

    };
      /*
  |--------------------------------------------------------------------------
  | Delete Review
  |--------------------------------------------------------------------------
  */

  const handleDelete =
    async (review) => {

      const result =
            await confirmDelete(
              review?.title ||
              review?.product_name ||
              "review ini"
            );

          if (!result.isConfirmed) {
            return;
          }

      try {

        await deleteProductReview(
          review.id
        );

        await successAlert(
          "Review berhasil dihapus."
        );

        /*
        |--------------------------------------------------------------------------
        | Close Detail If Deleted
        |--------------------------------------------------------------------------
        */

        if (

          selectedReview?.id ===
          review.id

        ) {

          setSelectedReview(
            null
          );

        }

        await loadReviews();

      } catch (error) {

        console.error(
          error
        );

        errorAlert(

          error?.response?.data
            ?.message ||

          "Gagal menghapus review."

        );

      }

    };

  /*
  |--------------------------------------------------------------------------
  | Unauthorized
  |--------------------------------------------------------------------------
  */

  if (!canView) {

    return (

      <div
        className="
          bg-white
          rounded-3xl
          p-10
          shadow-sm
          border
          border-slate-100
          text-center
        "
      >

        <MessageSquare
          className="
            w-14
            h-14
            mx-auto
            text-slate-300
            mb-4
          "
        />

        <h2
          className="
            text-2xl
            font-bold
            text-slate-800
          "
        >
          Access Denied
        </h2>

        <p
          className="
            text-slate-500
            mt-2
          "
        >
          Anda tidak memiliki izin
          untuk melihat product reviews.
        </p>

      </div>

    );

  }

  /*
  |--------------------------------------------------------------------------
  | Render
  |--------------------------------------------------------------------------
  */

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

        <div
          className="
            absolute
            -top-20
            -right-20
            w-72
            h-72
            rounded-full
            bg-white/5
          "
        />

        <div
          className="
            absolute
            -bottom-24
            -left-24
            w-80
            h-80
            rounded-full
            bg-white/5
          "
        />

        <div
          className="
            relative
            z-10
            flex
            flex-col
            lg:flex-row
            lg:items-center
            lg:justify-between
            gap-6
          "
        >

          <div>

            <div
              className="
                flex
                items-center
                gap-4
              "
            >

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

                <MessageSquare
                  className="
                    w-8
                    h-8
                    text-yellow-400
                  "
                />

              </div>

              <div>

                <h1
                  className="
                    text-4xl
                    font-bold
                  "
                >
                  Product Reviews
                </h1>

                <p
                  className="
                    text-slate-300
                    mt-2
                  "
                >
                  Moderate customer reviews
                  and maintain review quality.
                </p>

              </div>

            </div>

          </div>

          <div
            className="
              flex
              flex-wrap
              gap-3
            "
          >

            <div
              className="
                bg-white/10
                backdrop-blur
                rounded-2xl
                px-4
                py-2
                text-sm
              "
            >
              Reviews: {totalReviews}
            </div>

            <div
              className="
                bg-white/10
                backdrop-blur
                rounded-2xl
                px-4
                py-2
                text-sm
              "
            >
              Pending: {pendingReviews}
            </div>

          </div>

        </div>

      </div>

      {/* Statistics */}

      <div
        className="
          grid
          grid-cols-1
          sm:grid-cols-2
          xl:grid-cols-4
          gap-6
        "
      >

        {/* Total */}

        <div
          className="
            bg-white
            rounded-3xl
            p-6
            shadow-sm
            border
            border-slate-100
          "
        >

          <div
            className="
              flex
              items-center
              justify-between
            "
          >

            <div>

              <p
                className="
                  text-sm
                  text-slate-500
                "
              >
                Total Reviews
              </p>

              <h3
                className="
                  text-4xl
                  font-bold
                  mt-2
                "
              >
                {totalReviews}
              </h3>

            </div>

            <div
              className="
                w-14
                h-14
                rounded-2xl
                bg-slate-100
                flex
                items-center
                justify-center
              "
            >

              <MessageSquare
                className="
                  w-7
                  h-7
                  text-slate-700
                "
              />

            </div>

          </div>

        </div>
                {/* Pending */}

        <div
          className="
            bg-white
            rounded-3xl
            p-6
            shadow-sm
            border
            border-slate-100
          "
        >
          <div
            className="
              flex
              items-center
              justify-between
            "
          >
            <div>
              <p
                className="
                  text-sm
                  text-slate-500
                "
              >
                Pending
              </p>

              <h3
                className="
                  text-4xl
                  font-bold
                  mt-2
                "
              >
                {pendingReviews}
              </h3>
            </div>

            <div
              className="
                w-14
                h-14
                rounded-2xl
                bg-amber-50
                flex
                items-center
                justify-center
              "
            >
              <Clock3
                className="
                  w-7
                  h-7
                  text-amber-600
                "
              />
            </div>
          </div>
        </div>

        {/* Approved */}

        <div
          className="
            bg-white
            rounded-3xl
            p-6
            shadow-sm
            border
            border-slate-100
          "
        >
          <div
            className="
              flex
              items-center
              justify-between
            "
          >
            <div>
              <p
                className="
                  text-sm
                  text-slate-500
                "
              >
                Approved
              </p>

              <h3
                className="
                  text-4xl
                  font-bold
                  mt-2
                "
              >
                {approvedReviews}
              </h3>
            </div>

            <div
              className="
                w-14
                h-14
                rounded-2xl
                bg-green-50
                flex
                items-center
                justify-center
              "
            >
              <CheckCircle2
                className="
                  w-7
                  h-7
                  text-green-600
                "
              />
            </div>
          </div>
        </div>

        {/* Rejected */}

        <div
          className="
            bg-white
            rounded-3xl
            p-6
            shadow-sm
            border
            border-slate-100
          "
        >
          <div
            className="
              flex
              items-center
              justify-between
            "
          >
            <div>
              <p
                className="
                  text-sm
                  text-slate-500
                "
              >
                Rejected
              </p>

              <h3
                className="
                  text-4xl
                  font-bold
                  mt-2
                "
              >
                {rejectedReviews}
              </h3>
            </div>

            <div
              className="
                w-14
                h-14
                rounded-2xl
                bg-red-50
                flex
                items-center
                justify-center
              "
            >
              <XCircle
                className="
                  w-7
                  h-7
                  text-red-600
                "
              />
            </div>
          </div>
        </div>

      </div>

      {/* Filters */}

      <div
        className="
          bg-white
          rounded-3xl
          shadow-sm
          border
          border-slate-100
          p-6
        "
      >
        <div
          className="
            grid
            grid-cols-1
            lg:grid-cols-3
            gap-4
          "
        >
          <div className="relative">

            <Search
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
              type="text"
              placeholder="Cari review..."
              value={search}
              onChange={(e) =>
                setSearch(
                  e.target.value
                )
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

          <select
            value={statusFilter}
            onChange={(e) =>
              setStatusFilter(
                e.target.value
              )
            }
            className="
              rounded-2xl
              border
              border-slate-200
              px-4
              py-3
            "
          >
            <option value="">
              Semua Status
            </option>

            <option value="pending">
              Pending
            </option>

            <option value="approved">
              Approved
            </option>

            <option value="rejected">
              Rejected
            </option>

          </select>

          <select
            value={ratingFilter}
            onChange={(e) =>
              setRatingFilter(
                e.target.value
              )
            }
            className="
              rounded-2xl
              border
              border-slate-200
              px-4
              py-3
            "
          >
            <option value="">
              Semua Rating
            </option>

            <option value="5">
              ★★★★★ (5)
            </option>

            <option value="4">
              ★★★★☆ (4)
            </option>

            <option value="3">
              ★★★☆☆ (3)
            </option>

            <option value="2">
              ★★☆☆☆ (2)
            </option>

            <option value="1">
              ★☆☆☆☆ (1)
            </option>
          </select>

        </div>
      </div>

      {/* Table */}

      <div
        className="
          bg-white
          rounded-3xl
          shadow-sm
          border
          border-slate-100
          overflow-hidden
        "
      >

        <div
          className="
            px-6
            py-5
            border-b
            border-slate-100
          "
        >

          <h2
            className="
              text-xl
              font-bold
              text-slate-900
            "
          >
            Product Reviews
          </h2>

          <p
            className="
              text-sm
              text-slate-500
              mt-1
            "
          >
            Menampilkan
            {" "}
            {filteredReviews.length}
            {" "}
            review pelanggan.
          </p>

        </div>

        <div className="overflow-x-auto">

          <ProductReviewTable
            reviews={
              filteredReviews
            }
            canApprove={
              canApprove
            }
            canReject={
              canReject
            }
            canDelete={
              canDelete
            }
            onDetail={
              handleDetail
            }
            onApprove={
              handleApprove
            }
            onReject={
              handleReject
            }
            onDelete={
              handleDelete
            }
          />

        </div>

      </div>

      {/* Detail */}

      {(

        selectedReview ||

        detailLoading

      ) && (

        <ProductReviewDetail
          review={
            selectedReview
          }
          loading={
            detailLoading
          }
          canApprove={
            canApprove
          }
          canReject={
            canReject
          }
          onClose={
            closeDetail
          }
          onApprove={
            handleApprove
          }
          onReject={
            handleReject
          }
        />

      )}

      {/* Reject Modal */}

      <ProductReviewRejectModal
        open={
          rejectModalOpen
        }
        review={
          rejectTarget
        }
        loading={
          rejectLoading
        }
        onClose={() => {

          setRejectModalOpen(
            false
          );

          setRejectTarget(
            null
          );

        }}
        onSubmit={
          submitReject
        }
      />

    </div>

  );

}