<?php
namespace App\Exceptions;
use Exception;

class InsufficientStockException extends Exception
{
    public function __construct(string $message = 'Insufficient stock for one or more items.')
    {
        parent::__construct($message, 422);
    }

    public function render($request)
    {
        return response()->json(['message' => $this->getMessage()], 422);
    }
}
