<?php

namespace App\Exceptions;

use Exception;

/**
 * Thrown during checkout when a product's remaining stock can't cover the
 * quantity in the cart (see CheckoutController::process()). Caught
 * separately from generic exceptions so the customer gets the specific
 * "not enough stock" message instead of a generic error page.
 */
class InsufficientStockException extends Exception
{
}
