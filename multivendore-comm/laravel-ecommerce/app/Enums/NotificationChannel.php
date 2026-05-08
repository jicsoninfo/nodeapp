<?php

namespace App\Enums;

enum NotificationChannel: string
{
    case Email  = 'email';
    case SMS    = 'sms';
    case Push   = 'push';
    case InApp  = 'in_app';
}
