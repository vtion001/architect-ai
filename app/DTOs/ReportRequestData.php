<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Enums\ReportTemplate;

readonly class ReportRequestData
{
    public function __construct(
        public ReportTemplate $template,
        public ?string $variant = null,
        public ?string $recipientName = null,
        public ?string $recipientTitle = null,
        public ?string $analysisType = null,
        public ?string $prompt = null,
        public ?string $contentData = null,
        public ?string $researchTopic = null,
        public ?string $brandId = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            template: ReportTemplate::from($data['template'] ?? ReportTemplate::EXECUTIVE_SUMMARY->value),
            variant: $data['variant'] ?? null,
            recipientName: $data['recipientName'] ?? null,
            recipientTitle: $data['recipientTitle'] ?? null,
            analysisType: $data['analysisType'] ?? null,
            prompt: $data['prompt'] ?? null,
            contentData: $data['contentData'] ?? null,
            researchTopic: $data['researchTopic'] ?? null,
            brandId: $data['brand_id'] ?? null,
        );
    }
}
