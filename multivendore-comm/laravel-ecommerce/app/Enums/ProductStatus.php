<?php

namespace App\Enums;

enum ProductStatus: string
{
    case Draft    = 'draft';
    case Active   = 'active';
    case Inactive = 'inactive';
    case Archived = 'archived';

    public function isVisible(): bool
    {
        return $this === self::Active;
    }
}
