<?php
namespace App\Exceptions;
use Exception;

class InvalidCouponException extends Exception
{
    public function __construct(string $message = 'This coupon is not valid.')
    {
        parent::__construct($message, 422);
    }

    public function render($request)
    {
        return response()->json(['message' => $this->getMessage()], 422);
    }
}
