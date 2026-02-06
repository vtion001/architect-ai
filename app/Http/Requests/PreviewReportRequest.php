<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\ReportTemplate;

class PreviewReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'template' => ['required', new Enum(ReportTemplate::class)],
            'variant' => ['nullable', 'string', 'max:255'],
            'brand_id' => ['nullable', 'string', 'exists:brands,id'],
            'contractDetails' => ['nullable', 'array'],
            'recipientName' => ['nullable', 'string'],
            'recipientTitle' => ['nullable', 'string'],
            'senderName' => ['nullable', 'string'],
            'senderTitle' => ['nullable', 'string'],
            'companyAddress' => ['nullable', 'string'],
            'profilePhotoUrl' => ['nullable', 'string', 'max:4096'],
            'targetRole' => ['nullable', 'string', 'max:500'],
            'email' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'location' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'personalInfo' => ['nullable', 'array'],
            'personalInfo.age' => ['nullable', 'string', 'max:50'],
            'personalInfo.dob' => ['nullable', 'string', 'max:50'],
            'personalInfo.height' => ['nullable', 'string', 'max:50'],
            'personalInfo.weight' => ['nullable', 'string', 'max:50'],
            'personalInfo.gender' => ['nullable', 'string', 'max:50'],
            'personalInfo.civil_status' => ['nullable', 'string', 'max:50'],
            'personalInfo.nationality' => ['nullable', 'string', 'max:100'],
            'personalInfo.place_of_birth' => ['nullable', 'string', 'max:255'],
            'personalInfo.religion' => ['nullable', 'string', 'max:100'],
            'personalInfo.languages' => ['nullable', 'string', 'max:255'],
            'personalInfo.city' => ['nullable', 'string', 'max:255'],
            'personalInfo.alternate_phone' => ['nullable', 'string', 'max:50'],
        ];
    }
}
