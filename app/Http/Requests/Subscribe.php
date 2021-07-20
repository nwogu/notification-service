<?php

namespace App\Http\Requests;

use App\Models\Topic;
use App\Dtos\SubscribeDto;
use Illuminate\Foundation\Http\FormRequest;

class Subscribe extends FormRequest
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
            'url' => 'required|url'
        ];
    }

    /**
     * Return a data transfer object for this request
     * 
     * @return App\Dtos\SubscribeDto
     */
    public function dto()
    {
        return new SubscribeDto($this->url, Topic::whereName($this->topic)->firstOrFail());
    }
}
