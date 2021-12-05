<?php

namespace B2Binpay\DTO;

class RelationshipItemDto extends BaseDto
{
    public array $data;

    public function __construct(int $id, string $type)
    {
        $this->data = ['id' => $id, 'type' => $type];
        parent::__construct();
    }
}