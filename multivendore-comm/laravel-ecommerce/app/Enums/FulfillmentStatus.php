<?php

namespace App\Enums;

enum FulfillmentStatus: string
{
    case Pending    = 'pending';
    case Processing = 'processing';
    case Shipped    = 'shipped';
    case Delivered  = 'delivered';
    case Returned   = 'returned';
    case Cancelled  = 'cancelled';
}
