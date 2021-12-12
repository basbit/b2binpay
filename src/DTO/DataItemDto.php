<?php

namespace B2Binpay\DTO;

class DataItemDto extends BaseDto
{
    public ?int $id;
    public string $type;
    public ?array $links;
    public ?array $included;
    public ?array $attributes;
    public ?array $relationships;

    public static function setFromParams(
        string $type,
        ?array $attributes = null,
        ?array $relationships = null,
        ?array $included = null,
        ?array $links = null,
        ?int $id = null
    ): self {
        $dto = new self();
        $dto->id = $id;
        $dto->type = $type;
        $dto->links = $links;
        $dto->included = $included;
        $dto->attributes = $attributes;
        $dto->relationships = $relationships;

        return $dto;
    }

    /**
     * @return array|null
     */
    public function getAttributes(): ?array
    {
        return $this->attributes;
    }

    public function getRelationships(): ?array
    {
        return $this->relationships;
    }
}