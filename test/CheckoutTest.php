<?php

namespace Tebex;

use PHPUnit\Framework\TestCase;
use Tebex\Checkout\BasketBuilder;
use Tebex\Checkout\PackageBuilder;
use TebexCheckout\ApiException;
use TebexCheckout\Model\Basket;
use TebexCheckout\Model\BasketRow;
use TebexCheckout\Model\Payment;
use TebexCheckout\Model\RecurringPayment;
use TebexCheckout\Model\Sale;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertTrue;

class CheckoutTest extends TestCase
{
    public int $projectId = 1361049;
    public string $privateKey = (string)getenv("TEBEX_CHECKOUT_PRIVATE_KEY");
    private string $testPaymentId = (string)getenv("TEBEX_CHECKOUT_TEST_PAYMENT_ID");
    private string $testRecurringPaymentId =  (string)getenv("TEBEX_CHECKOUT_TEST_RECURRING_PAYMENT_ID");

    private BasketBuilder $testBasketBuilder;

    protected function setUp() : void
    {
        parent::setUp();
        Checkout::setApiKeys("1361049", "pzpwOOO7Y7oUzQUVLeeH8smzfKYHy6gd");
    }

    private function createTestBasketBuilder() {
        $this->testBasketBuilder = BasketBuilder::new()
            ->firstname("John")
            ->lastname("Doe")
            ->email("tebex-integrations@overwolf.com")
            ->returnUrl("https://example.com/return")
            ->completeUrl("https://example.com/complete");
    }

    public function testSetApiKeys()
    {
        Checkout::setApiKeys("key", "secret");
        $this->assertTrue(Checkout::areApiKeysSet());
    }

    public function testGetBasket()
    {
        self::createTestBasketBuilder();
        $basket = $this->testBasketBuilder->build();

        $apiBasket = Checkout::getBasket($basket->getIdent());
        $this->assertEquals($basket->getIdent(), $apiBasket->getIdent());
        $this->assertInstanceOf(Basket::class, $apiBasket);
    }

    public function testGetPayment()
    {
        $payment = Checkout::getPayment($this->testPaymentId);
        self::assertInstanceOf(Payment::class, $payment);
        self::assertEquals($this->testPaymentId, $payment->getTransactionId());
    }

    public function testAddSaleToBasket()
    {
        self::createTestBasketBuilder();
        $basket = $this->testBasketBuilder->build();

        // 10% ad-hoc discount sale
        $newBasket = Checkout::addSaleToBasket($basket, "Test", "percentage", 10);
        $package = PackageBuilder::new()->name("Test")->price(1.00)->qty(1)->oneTime();
        $newBasket = Checkout::addPackage($basket, $package->buildPackage());
        assertTrue($newBasket->getPrice() == 0.90);
    }

    public function testGetRecurringPayment()
    {
        $recurringPayment = Checkout::getRecurringPayment($this->testRecurringPaymentId);
        assertInstanceOf(RecurringPayment::class, $recurringPayment);
    }

    public function testAreApiKeysSet()
    {
        Checkout::setApiKeys($this->projectId, $this->privateKey);
        assertTrue(Checkout::areApiKeysSet());
    }

    public function testRemoveBasketRow()
    {
        self::createTestBasketBuilder();
        $basket = $this->testBasketBuilder->build();

        // add a package then remove it
        $package = PackageBuilder::new()->name("Test")->price(1.00)->qty(1)->oneTime();
        $newBasket = Checkout::addPackage($basket, $package->buildPackage());
        $row = new BasketRow((array)json_decode(json_encode($newBasket->getRows()[0])));
        Checkout::removeBasketRow($newBasket, $row->getId());
        $newBasket = Checkout::getBasket($newBasket->getIdent());
        $this->assertTrue(count($newBasket->getRows()) == 0);
    }

    public function testCreateBasket()
    {
        self::createTestBasketBuilder();

        $basket = $this->testBasketBuilder->build();
        self::assertInstanceOf(Basket::class, $basket);
    }

    public function testCheckoutRequest()
    {
        self::createTestBasketBuilder();

        $package = PackageBuilder::new()->name("Test")->price(1.00)->qty(1)->oneTime();

        // checkout request with 10% sale on $1 item
        $basket = Checkout::checkoutRequest($this->testBasketBuilder, [$package->buildCheckoutItem()], new Sale(
            ["name" => "Test", "discount_type" => "percentage", "amount" => 10]
        ));
        assertTrue(sizeof($basket->getRows()) == 1);
        assertTrue($basket->getPrice() == 0.90);
    }

    public function testAddPackage()
    {
        self::createTestBasketBuilder();
        $basket = $this->testBasketBuilder->build();
        $package = PackageBuilder::new()->name("Test")->price(1.00)->qty(1)->oneTime();
        $newBasket = Checkout::addPackage($basket, $package->buildPackage());
        $this->assertTrue(count($newBasket->getRows()) == 1);
    }

    /**
     * @doesNotPerformAssertions
     * @throws ApiException
     */
    public function testUpdateSubscriptionProduct()
    {
        $newPackage = PackageBuilder::new()->name("Test")->price(1.28)->qty(1)->monthly()->buildPackage();
        $payment = Checkout::updateSubscriptionProduct($this->testRecurringPaymentId, [$newPackage]);
    }

    /**
     * @throws ApiException
     */
    public function testCancelRecurringPayment()
    {
        $currentPayment = Checkout::getRecurringPayment($this->testRecurringPaymentId);
        if ($currentPayment->getCancellationRequestedAt() != null) {
            $this->markTestSkipped("Improper setup conditions - test recurring payment is already cancelled");
        }
        $recurringPayment = Checkout::cancelRecurringPayment($this->testRecurringPaymentId);
        assertTrue($recurringPayment->getCancellationRequestedAt() != null);
    }

    public function testPauseRecurringPayment()
    {
        $recurringPayment = Checkout::pauseRecurringPayment($this->testRecurringPaymentId);
        assertTrue($recurringPayment->getPausedAt() != null);
    }

    /**
     * @throws ApiException
     */
    public function testReactivateRecurringPayment()
    {
        $recurringPayment = Checkout::reactivateRecurringPayment($this->testRecurringPaymentId);
        assertTrue($recurringPayment->getStatus()->getDescription() === "Active");
    }

    /**
     * @throws ApiException
     */
    public function testRefundPayment()
    {
        try {
            $refundedPayment = Checkout::refundPayment($this->testPaymentId);
            $this->assertTrue($refundedPayment->getStatus()->getDescription() === "Refund");
        } catch (ApiException $e) {
            if ($e->getCode() === 422) {
                $this->markTestSkipped("Test payment is already refunded");
            }
        }
    }
}
