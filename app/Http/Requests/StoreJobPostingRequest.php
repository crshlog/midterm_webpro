<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobPostingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isEmployer();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'job_type' => ['required', 'in:Remote,On-site,Hybrid'],
            'budget' => ['required', 'numeric', 'min:5', 'max:999999'],
            'deadline' => ['required', 'date'],
            'requirements' => ['required', 'string', 'min:10'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
