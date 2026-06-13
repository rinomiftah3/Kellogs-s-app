<?php

namespace App\Http\Requests;

use App\Models\Activity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * FilterActivityLogRequest
 *
 * Validates query parameters for filtering activity logs.
 * Maps to the Activity model scopes and repository filters.
 *
 * Laravel 13 | PHP 8.4
 */
class FilterActivityLogRequest extends FormRequest
{
    /**
     * Stop validation on first failure.
     */
    protected $stopOnFirstFailure = true;

    /*
    |--------------------------------------------------------------------------
    | Authorization
    |--------------------------------------------------------------------------
    */

    public function authorize(): bool
    {
        return $this->user()?->can('view activity logs')
            ?? false;
    }

    /*
    |--------------------------------------------------------------------------
    | Prepare Data
    |--------------------------------------------------------------------------
    */

    protected function prepareForValidation(): void
    {
        $this->merge([

            'log_name' => blank($this->log_name)
                ? null
                : $this->log_name,

            'event' => blank($this->event)
                ? null
                : $this->event,

            'subject_type' => blank($this->subject_type)
                ? null
                : $this->subject_type,

            'search' => blank($this->search)
                ? null
                : trim($this->search),

            'sort_order' => blank($this->sort_order)
                ? 'desc'
                : strtolower($this->sort_order),

            'per_page' => $this->integer(
                'per_page',
                25
            ),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Validation Rules
    |--------------------------------------------------------------------------
    */

    public function rules(): array
    {
        return [

            'log_name' => [
                'sometimes',
                'nullable',
                'string',
                'max:100',
            ],

            'event' => [
                'sometimes',
                'nullable',
                'string',
                Rule::in([
                    Activity::EVENT_CREATED,
                    Activity::EVENT_UPDATED,
                    Activity::EVENT_DELETED,
                    Activity::EVENT_RESTORED,
                    Activity::EVENT_LOGIN,
                    Activity::EVENT_LOGOUT,
                    Activity::EVENT_APPROVED,
                    Activity::EVENT_REJECTED,
                    Activity::EVENT_PUBLISHED,
                    Activity::EVENT_CANCELLED,
                ]),
            ],

            'causer_id' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:users,id',
            ],

            'subject_type' => [
                'sometimes',
                'nullable',
                'string',
                Rule::in([
                    'User',
                    'Role',
                    'Category',
                    'Product',
                    'Order',
                    'Voucher',
                    'Promotion',
                    'LoyaltyPoint',
                    'PointTransaction',
                ]),
            ],

            'subject_id' => [
                'sometimes',
                'nullable',
                'integer',
                'min:1',
            ],

            'date_from' => [
                'sometimes',
                'nullable',
                'date',
            ],

            'date_to' => [
                'sometimes',
                'nullable',
                'date',
                'after_or_equal:date_from',
            ],

            'search' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'per_page' => [
                'sometimes',
                'integer',
                'min:5',
                'max:200',
            ],

            'sort_by' => [
                'sometimes',
                'string',
                Rule::in([
                    'created_at',
                    'event',
                    'log_name',
                    'causer_id',
                ]),
            ],

            'sort_order' => [
                'sometimes',
                'string',
                Rule::in([
                    'asc',
                    'desc',
                ]),
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Custom Messages
    |--------------------------------------------------------------------------
    */

    public function messages(): array
    {
        return [

            'event.in' =>
                'The selected event type is invalid.',

            'subject_type.in' =>
                'The selected subject type is invalid.',

            'date_to.after_or_equal' =>
                'The end date must be on or after the start date.',

            'sort_by.in' =>
                'The selected sort field is invalid.',

            'sort_order.in' =>
                'Sort direction must be asc or desc.',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Custom Attribute Names
    |--------------------------------------------------------------------------
    */

    public function attributes(): array
    {
        return [

            'log_name' =>
                'log name',

            'event' =>
                'event type',

            'causer_id' =>
                'user',

            'subject_type' =>
                'subject type',

            'subject_id' =>
                'subject ID',

            'date_from' =>
                'start date',

            'date_to' =>
                'end date',

            'search' =>
                'search keyword',

            'per_page' =>
                'results per page',

            'sort_by' =>
                'sort field',

            'sort_order' =>
                'sort direction',
        ];
    }
}