<?php

namespace Modules\CustomerVehicle\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInspectionChecklistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'current_odometer' => ['required', 'integer', 'min:0'],
            'fuel_level_percentage' => ['required', 'integer', 'min:0', 'max:100'],
            'checklist_groups' => ['required', 'array'],
            'body_defects' => ['nullable', 'array'],
            'body_defects.*.zone' => ['required_with:body_defects', 'string', 'max:100'],
            'body_defects.*.type' => ['required_with:body_defects', 'string', 'max:50'],
            'body_defects.*.note' => ['nullable', 'string', 'max:255'],
            'customer_belongings' => ['nullable', 'array'],
            'customer_belongings.*' => ['string', 'max:100'],
        ];
    }
}
