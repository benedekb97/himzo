<?php

declare(strict_types=1);

namespace App\Traits;

interface EntityInterface
{
    public function getId(): int;

    public function setId(int $id): void;
}
