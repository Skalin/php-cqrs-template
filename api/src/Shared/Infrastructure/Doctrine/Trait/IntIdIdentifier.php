<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Doctrine\Trait;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\DBAL\Types\Types;

trait IntIdIdentifier
{
    #[Id]
    #[Column(type: Types::STRING)]
    #[GeneratedValue(strategy: 'NONE')]
    private string $id;

    public function getId(): string
    {
        return $this->id;
    }
}
