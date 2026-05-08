<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Card       = 'card';
    case UPI        = 'upi';
    case NetBanking = 'netbanking';
    case Wallet     = 'wallet';
    case COD        = 'cod';
    case BNPL       = 'bnpl';
    case Crypto     = 'crypto';
}
