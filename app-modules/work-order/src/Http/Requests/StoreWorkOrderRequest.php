<?php

namespace Modules\WorkOrder\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'vehicle_id' => ['required', 'integer', 'exists:vehicles,id'],
            'mechanic_id' => ['nullable', 'integer', 'exists:users,id'],
            'odometer_in' => ['nullable', 'integer', 'min:0'],
            'customer_complaint' => ['required', 'string', 'max:1000'],
            'services' => ['nullable', 'array'],
            'services.*.service_name' => ['required_with:services', 'string', 'max:150'],
            'services.*.labor_cost' => ['required_with:services', 'integer', 'min:0'],
        ];
    }
}
