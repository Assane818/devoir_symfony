<?php
namespace App\DTO;

class ClientSearchDTO
{
    private ?string $data = null;

    public function getData(): ?string
    {
        return $this->data;
    }

    public function setData(?string $data): self
    {
        $this->data = $data;
        return $this;
    }
}
