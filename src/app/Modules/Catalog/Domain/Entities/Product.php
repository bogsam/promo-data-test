<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Domain\Entities;

use App\Modules\Shared\ValueObjects\Id;

final class Product
{
    private function __construct(
        private ?Id     $id,
        private string  $productName,
        private Id      $categoryId,
        private Id      $manufacturerId,
    ) {}

    public static function create(
        string  $productName,
        Id      $categoryId,
        Id      $manufacturerId,
    ): self {
        return new self(
            id:             null,
            productName:    $productName,
            categoryId:     $categoryId,
            manufacturerId: $manufacturerId,
        );
    }

    public static function restore(
        Id      $id,
        string  $productName,
        Id      $categoryId,
        Id      $manufacturerId,
    ): self {
        return new self(
            id:             $id,
            productName:    $productName,
            categoryId:     $categoryId,
            manufacturerId: $manufacturerId,
        );
    }

    public function id(): ?Id
    {
        return $this->id;
    }

    public function productName(): string
    {
        return $this->productName;
    }

    public function categoryId(): Id
    {
        return $this->categoryId;
    }

    public function manufacturerId(): Id
    {
        return $this->manufacturerId;
    }
}
