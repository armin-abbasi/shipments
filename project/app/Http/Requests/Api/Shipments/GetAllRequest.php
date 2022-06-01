<?php

namespace App\Http\Requests\Api\Shipments;

use Illuminate\Foundation\Http\FormRequest;

class GetAllRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'carrier'      => 'string|nullable',
            'company'      => 'string|nullable',
            'stop_address' => 'string|nullable',
        ];
    }
}
