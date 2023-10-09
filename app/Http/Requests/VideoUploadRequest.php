<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VideoUploadRequest extends FormRequest
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
        return [
            'video' => 'required|file|mimetypes:video/*|max:10240',
        ];
    }

    public function messages()
    {
        return [
            'video.required' => 'Поле загрузки видео обязательно для заполнения.',
            'video.file' => 'Загруженный файл должен быть файлом.',
            'video.mimetypes' => 'Загруженный файл должен быть видео файлом.',
            'video.max' => 'Максимальный размер файла должен быть 10MB.',
        ];
    }
}
