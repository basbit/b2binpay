<?php

namespace B2Binpay\DTO;

class DataItemDto extends BaseDto
{
    protected ?int $id;
    protected string $type;
    protected ?array $links;
    protected ?array $included;
    protected ?array $attributes;
    protected ?array $relationships;

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
}