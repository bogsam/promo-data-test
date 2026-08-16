<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Domain\Entities;

use App\Modules\Shared\Domain\ValueObjects\Id;

final class Manufacturer
{
    private function __construct(
        private ?Id $id,
        private string $manufacturerName,
    ) {}

    public static function create(string $manufacturerName): self
    {
        return new self(
            id: null,
            manufacturerName: $manufacturerName,
        );
    }

    public static function restore(Id $id, string $manufacturerName): self
    {
        return new self(
            id: $id,
            manufacturerName: $manufacturerName,
        );
    }

    public function id(): ?Id
    {
        return $this->id;
    }

    public function manufacturerName(): string
    {
        return $this->manufacturerName;
    }
}
