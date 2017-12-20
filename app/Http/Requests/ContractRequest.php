<?php

namespace App\Http\Requests;

use App\Contract;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ContractRequest extends FormRequest
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
            'nest_id' => 'required',
			'pay_active' => 'required|numeric',
			'pay_limit' => 'required|numeric',
			'eggs' => 'required|numeric'
        ];
    }

    public function message()
	{

	}
}
