<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVideoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'recorded_video_data' => 'nullable|string',
        ];

        if ($this->has('recorded_video_data') && !empty($this->input('recorded_video_data'))) {
            $rules['title_record'] = 'required|string|max:255';
            $rules['description_record'] = 'nullable|string';
            $rules['video'] = 'nullable';
        } else {
            $rules['title_upload'] = 'required|string|max:255';
            $rules['description_upload'] = 'nullable|string';
            $rules['video'] = [
                'required',
                'file',
                'mimetypes:video/mp4,video/quicktime,video/webm',
                'max:51200', // 50MB
            ];
        }

        return $rules;
    }
}
