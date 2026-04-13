<?php
declare(strict_types=1);
namespace App\Domain;
final class PolicyContext
{
    public function __construct(
        public readonly string $businessUnit,
        public readonly string $country,
        public readonly string $dataClassification,
        public readonly string $modelType,
        public readonly array $frameworks = [],
    ) {}
    public static function fromArray(array $data): self
    {
        return new self(
            businessUnit: (string)($data['business_unit'] ?? 'General'),
            country: strtoupper((string)($data['country'] ?? 'BE')),
            dataClassification: strtolower((string)($data['data_classification'] ?? 'public')),
            modelType: strtolower((string)($data['model_type'] ?? 'private_llm')),
            frameworks: array_values(array_map('strtoupper', $data['frameworks'] ?? [])),
        );
    }
    public function toArray(): array
    {
        return [
            'business_unit' => $this->businessUnit,
            'country' => $this->country,
            'data_classification' => $this->dataClassification,
            'model_type' => $this->modelType,
            'frameworks' => $this->frameworks,
        ];
    }
}
