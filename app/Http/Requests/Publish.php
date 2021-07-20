<?php

namespace App\Http\Requests;

use App\Models\Topic;
use App\Dtos\PublishDto;
use Illuminate\Foundation\Http\FormRequest;

class Publish extends FormRequest
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
            //
        ];
    }

    /**
     * Return a data transfer object for this request
     * 
     * @return App\Dtos\PublishDto
     */
    public function dto()
    {
        return new PublishDto($this->getContent(), Topic::whereName($this->topic)->firstOrFail());
    }
}
