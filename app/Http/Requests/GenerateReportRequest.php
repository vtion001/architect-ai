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
            'prompt' => ['nullable', 'string'],
            'contentData' => ['nullable', 'string'],
            'researchTopic' => ['nullable', 'string'],
        ];
    }
}
