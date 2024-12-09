<?php

namespace Tebex;

use GuzzleHttp\Client;
use Tebex\Checkout\BasketBuilder;
use TebexCheckout\ApiException;
use TebexCheckout\Configuration;
use TebexCheckout\Model\AddPackageRequest;
use TebexCheckout\Model\Basket;
use TebexCheckout\Model\CheckoutRequest;
use TebexCheckout\Model\CreateBasketRequest;
use TebexCheckout\Model\Package;
use TebexCheckout\Model\Payment;
use TebexCheckout\Model\RecurringPayment;
use TebexCheckout\Model\Sale;
use TebexCheckout\Model\UpdateRecurringPaymentRequest;
use TebexCheckout\Model\UpdateSubscriptionRequest;
use TebexCheckout\Model\UpdateSubscriptionRequestItemsInner;
use TebexCheckout\TebexCheckout\BasketsApi;
use TebexCheckout\TebexCheckout\CheckoutApi;
use TebexCheckout\TebexCheckout\PaymentsApi;
use TebexCheckout\TebexCheckout\RecurringPaymentsApi;

/**
 * TebexCheckout allows creating and transacting with ad-hoc packages not pre-defined in a webstore. This API requires
 * prior approval. Please contact Tebex to enable on your account.
 */
class Checkout extends TebexAPI {
    // Underlying OpenAPI objects
    protected static PaymentsApi $paymentsApi;
    protected static RecurringPaymentsApi $recurringPaymentsApi;
    protected static BasketsApi $basketsApi;
    protected static CheckoutApi $checkoutApi;

    /**
     * Creates a basket from a CreateBasketRequest
     *
     * @param CreateBasketRequest $request
     * @return Basket
     * @throws ApiException
     */
    static function createBasket(CreateBasketRequest $request) : Basket {
        return self::$basketsApi->createBasket($request);
    }

    /**
     * Sets the Checkout API keys.
     * @param string $projectId
     * @param string $privateKey
     * @return void
     */
    public static function setApiKeys(string $projectId, string $privateKey) {
        self::$_projectId = $projectId;
        self::$_privateKey = $privateKey;
        self::$_areApiKeysSet = true;

        self::$basketsApi = new BasketsApi(new Client(),
            Configuration::getDefaultConfiguration()->setUsername(self::$_projectId)->setPassword(self::$_privateKey));
        self::$checkoutApi = new CheckoutApi(new Client(),
            Configuration::getDefaultConfiguration()->setUsername(self::$_projectId)->setPassword(self::$_privateKey));
        self::$paymentsApi = new PaymentsApi(new Client(),
            Configuration::getDefaultConfiguration()->setUsername(self::$_projectId)->setPassword(self::$_privateKey));
        self::$recurringPaymentsApi = new RecurringPaymentsApi(new Client(),
            Configuration::getDefaultConfiguration()->setUsername(self::$_projectId)->setPassword(self::$_privateKey));
    }

    public static function areApiKeysSet() : bool
    {
        return !empty(Checkout::$_privateKey)
            //&& !empty(Checkout::$_publicToken)
            && !empty(Checkout::$_projectId)
            && is_string(Checkout::$_privateKey)
            //&& is_string(Checkout::$_publicToken)
            && is_string(Checkout::$_projectId);
    }

    /**
     * Adds a package to the given basket.
     *
     * @param Basket $basket
     * @param Package $package
     * @return Basket
     * @throws ApiException
     */
    public static function addPackage(Basket $basket, Package $package) : Basket
    {
        $addPackageRequest = new AddPackageRequest([
            "package" => $package,
            "qty" => $package->getQty(),
            "type" => $package->getType(),
        ]);
        return self::$basketsApi->addPackage($basket->getIdent(), $addPackageRequest);
    }

    /**
     * Performs an all-in-one checkout request, sending the basket information and desired items in a single request.
     * @param BasketBuilder $basket     BasketBuilder containing basket create information
     * @param array $items              Array of CheckoutItem
     * @param Sale|null $sale
     * @return Basket
     * @throws ApiException
     */
    public static function checkoutRequest(BasketBuilder $basket, array $items, Sale $sale = null) : Basket {
        return self::$checkoutApi->checkout(new CheckoutRequest([
            "basket" => $basket->build(),
            "items" => $items,
            "sale" => $sale,
        ]));
    }

    /**
     * Gets a basket from Tebex by its ident
     *
     * @param String $basketIdent
     * @return Basket
     * @throws ApiException
     */
    public static function getBasket(String $basketIdent) : Basket {
        return self::$basketsApi->getBasketById($basketIdent);
    }

    /**
     * Removes the given row from the basket.
     *
     * @param Basket $basket
     * @param int $rowId
     * @throws ApiException
     */
    public static function removeBasketRow(Basket $basket, int $rowId) {
        self::$basketsApi->removeRowFromBasket($basket->getIdent(), $rowId);
    }

    /**
     * Adds a sale to the basket
     * @param Basket $basket
     * @param string $saleName
     * @param string $saleType
     * @param float $saleAmount
     * @return Basket
     * @throws ApiException
     */
    public static function addSaleToBasket(Basket $basket, string $saleName, string $saleType, float $saleAmount) : Basket {
        // Get the sale by name and add to the basket
        return self::$basketsApi->addSaleToBasket($basket->getIdent(),
            new Sale(['name' => $saleName, 'discount_type' => $saleType, 'amount' => $saleAmount])
        );
    }

    /**
     * Gets a payment by its identifier.
     *
     * @param string $identifier
     * @return Payment
     * @throws ApiException
     */
    public static function getPayment(string $identifier) : Payment {
        return self::$paymentsApi->getPaymentById($identifier);
    }

    /**
     * Refunds the given payment by its transaction id
     * @param string $transactionId
     * @return void
     * @throws ApiException
     */
    public static function refundPayment(string $transactionId) : Payment {
        return self::$paymentsApi->refundPaymentById($transactionId);
    }

    /**
     * Gets a recurring payment by its identifier
     * @param string $identifier
     * @return RecurringPayment
     * @throws ApiException
     */
    public static function getRecurringPayment(string $identifier) : RecurringPayment {
        return self::$recurringPaymentsApi->getRecurringPayment($identifier);
    }

    /**
     * Updates the set of packages associated with a subscription.
     *
     * @param string $recurringPaymentReference The recurring payment ID
     * @param array $packages   An array of Packages to associate with this subscription.
     * @return RecurringPayment
     * @throws ApiException
     */
    public static function updateSubscriptionProduct(string $recurringPaymentReference, array $packages) : RecurringPayment {
        $items = [];
        foreach($packages as $package) {
            $item = new UpdateSubscriptionRequestItemsInner([
                "type" => $package['type'],
                "qty" => $package['qty'],
                "package" => $package,
            ]);
            array_push($items, $item);
        }

        return self::$recurringPaymentsApi->updateSubscription($recurringPaymentReference, new UpdateSubscriptionRequest([
            "items" => $items
        ]));
    }

    /**
     * Cancels the given recurring payment.
     *
     * @param string $recurringPaymentReference
     * @return RecurringPayment
     * @throws ApiException
     */
    public static function cancelRecurringPayment(String $recurringPaymentReference) : RecurringPayment {
        return self::$recurringPaymentsApi->cancelRecurringPayment($recurringPaymentReference);
    }

    /**
     * Pauses the given recurring payment.
     *
     * @param string $recurringPaymentReference
     * @return RecurringPayment
     * @throws ApiException
     */
    public static function pauseRecurringPayment(string $recurringPaymentReference) : RecurringPayment {
        return self::$recurringPaymentsApi->updateRecurringPayment($recurringPaymentReference, new UpdateRecurringPaymentRequest([
            "status" => UpdateRecurringPaymentRequest::STATUS_PAUSED,
        ]));
    }

    /**
     * Reactivates the given recurring payment, returning the new payment
     *
     * @param string $recurringPaymentReference
     * @return RecurringPayment
     * @throws ApiException
     */
    public static function reactivateRecurringPayment(string $recurringPaymentReference) : ?RecurringPayment {
        return self::$recurringPaymentsApi->updateRecurringPayment($recurringPaymentReference, new UpdateRecurringPaymentRequest([
            "status" => UpdateRecurringPaymentRequest::STATUS_ACTIVE,
        ]));
    }
}