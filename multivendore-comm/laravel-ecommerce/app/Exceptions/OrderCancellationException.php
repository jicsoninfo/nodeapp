<?php
namespace App\Exceptions;
use Exception;

class OrderCancellationException extends Exception
{
    public function __construct(string $message = 'This order cannot be cancelled.')
    {
        parent::__construct($message, 422);
    }

    public function render($request)
    {
        return response()->json(['message' => $this->getMessage()], 422);
    }
}
