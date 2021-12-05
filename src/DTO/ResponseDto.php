<?php

namespace B2Binpay\DTO;

class ResponseDto extends BaseDto
{
    /** @var array|DataItemDto[]  */
    protected ?array $data = null;
    protected ?MetaDto $meta = null;

    public function __construct(?array $data = null, ?MetaDto $meta = null)
    {
        parent::__construct(['data' => $data, 'meta' => $meta]);
    }

    /**
     * @return DataItemDto[]|null
     */
    public function getData(): ?array
    {
        return $this->data;
    }

    /**
     * @return MetaDto|null
     */
    public function getMeta(): ?MetaDto
    {
        return $this->meta;
    }
}