<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CorporateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'corporate_name' => 'required|string|max:255',
            'corporate_id' => 'required|string|max:50',
            'corporate_no' => 'required|string|max:50',
            'display_name' => 'required|string|max:50',
            'registration_no' => 'required|string|max:100',
            'industry_segment' => 'required|string|max:100',
            'prof_image' => 'nullable|string|max:255', // Modify if this needs to be a file upload
            'company_profile' => 'nullable|string|max:100',
            'industry' => 'required|string|max:100',
            'created_by' => 'required|string|max:50',
            'gstin' => 'required|string|max:15|regex:/^[0-9A-Za-z]+$/', // Custom regex for GSTIN
            'discount' => 'required|numeric|min:0|max:100',
            'created_on' => 'required|date',
            'valid_from' => 'required|date|before_or_equal:valid_upto',
            'valid_upto' => 'required|date|after_or_equal:valid_from',
            'corporate_color' => 'required|string|regex:/^#[a-fA-F0-9]{6}$/',
            'active_status' => 'required|in:0,1',
        ];
    }
}
