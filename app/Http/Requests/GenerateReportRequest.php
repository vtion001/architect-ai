<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\ReportTemplate;

class GenerateReportRequest extends FormRequest
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
            'recipientName' => ['nullable', 'string', 'max:255'],
            'recipientTitle' => ['nullable', 'string', 'max:255'],
            'analysisType' => ['nullable', 'string', 'max:255'],
            'prompt' => ['nullable', 'string'],
            'contentData' => ['nullable', 'string'],
            'researchTopic' => ['nullable', 'string'],
            'brand_id' => ['nullable', 'uuid'],
            'targetRole' => ['nullable', 'string', 'max:500'],
            'profilePhotoUrl' => ['nullable', 'string', 'max:2048'],
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
        ];
    }
}
