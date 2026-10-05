<?php

namespace App\Exceptions;

use Exception;

/**
 * Thrown during checkout when a cart still contains a product that's no
 * longer purchasable — e.g. deactivated by an admin after it was added to
 * the cart (see CheckoutController::process() and Product::isPurchasable()).
 */
class ProductNotPurchasableException extends Exception
{
}
