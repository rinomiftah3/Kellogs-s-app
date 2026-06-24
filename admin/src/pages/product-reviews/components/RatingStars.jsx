import {
  StarIcon,
} from "@heroicons/react/24/solid";

export default function RatingStars({
  rating = 0,
  size = "md",
  showValue = true,
  showLabel = false,
}) {

  /*
  |--------------------------------------------------------------------------
  | Normalize Rating
  |--------------------------------------------------------------------------
  */

  const normalizedRating =
    Math.min(
      5,
      Math.max(
        0,
        Number(rating) || 0
      )
    );

  /*
  |--------------------------------------------------------------------------
  | Config
  |--------------------------------------------------------------------------
  */

  const totalStars = 5;

  const starSize = {

    sm: "w-4 h-4",

    md: "w-5 h-5",

    lg: "w-6 h-6",

    xl: "w-7 h-7",

  };

  const iconSize =
    starSize[size] ||
    starSize.md;

  return (

    <div
      className="
        flex
        items-center
        gap-2
      "
    >

      {/* Stars */}

      <div
        className="
          flex
          items-center
        "
      >

        {Array.from({
          length: totalStars,
        }).map((_, index) => {

          const filled =
            index <
            normalizedRating;

          return (

            <StarIcon
              key={index}
              className={`
                ${iconSize}
                transition-all
                duration-200
                ${
                  filled
                    ? "text-amber-400"
                    : "text-slate-300"
                }
              `}
            />

          );

        })}

      </div>

      {/* Numeric Value */}

      {showValue && (

        <span
          className="
            text-sm
            font-semibold
            text-slate-700
          "
        >
          {normalizedRating}/5
        </span>

      )}

      {/* Label */}

      {showLabel && (

        <span
          className="
            text-sm
            text-slate-500
          "
        >
          {getRatingLabel(
            normalizedRating
          )}
        </span>

      )}

    </div>

  );

}

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

function getRatingLabel(
  rating
) {

  switch (rating) {

    case 5:
      return "Excellent";

    case 4:
      return "Very Good";

    case 3:
      return "Good";

    case 2:
      return "Fair";

    case 1:
      return "Poor";

    default:
      return "No Rating";

  }

}