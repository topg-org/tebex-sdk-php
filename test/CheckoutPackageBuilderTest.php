<?php

namespace Tebex\Checkout;

use PHPUnit\Framework\TestCase;
use TebexCheckout\Model\CheckoutItem;
use TebexCheckout\Model\Package;
use function PHPUnit\Framework\assertTrue;

class CheckoutPackageBuilderTest extends TestCase
{
    private PackageBuilder $builder;

    protected function setUp(): void
    {
        $this->builder = PackageBuilder::new();
    }

    public function testName()
    {
        $this->builder->name("Test");
        $this->assertTrue($this->builder->getName() === "Test");
    }

    public function testOneTime()
    {
        $this->builder->oneTime();
        $this->assertEquals('single', $this->builder->getType(), 'One time packages should be of type single.');
    }

    public function testMonthly()
    {
        $this->builder->monthly();
        $this->assertEquals('month', $this->builder->getExpiryPeriod(), 'Monthly packages should have an expiry period of 1 month.');
        $this->assertEquals(1, $this->builder->getExpiryLength(), 'Monthly packages should have an expiry length of 1.');
        $this->assertEquals('subscription', $this->builder->getType(), 'Monthly packages should be of type subscription.');
    }

    public function testPrice()
    {
        $this->builder->price(10.99);
        assertTrue($this->builder->getPrice() === 10.99);
    }

    public function testBuildCheckoutItem()
    {
        $this->builder->name("Test");
        $this->builder->price(10.99);
        $this->builder->qty(1);
        $this->builder->oneTime();

        $item = $this->builder->buildCheckoutItem();
        $this->assertTrue($item instanceof CheckoutItem);
    }

    public function testCustom()
    {
        $this->builder->name("Test");
        $this->builder->price(10.99);
        $this->builder->qty(1);
        $this->builder->oneTime();
        $this->builder->custom((array)json_decode(json_encode(["foo" => "bar"])));
        assertTrue($this->builder->getCustom() != null);
    }

    public function testSemiAnnual()
    {
        $this->builder->semiAnnual();
        $this->assertEquals('month', $this->builder->getExpiryPeriod(), 'Semi annual packages should have an expiry period of 6 months.');
        $this->assertEquals(6, $this->builder->getExpiryLength(), 'Semi annual packages should have an expiry length of 6.');
        $this->assertEquals('subscription', $this->builder->getType(), 'Semi annual packages should be of type subscription.');
    }

    public function testNew()
    {
        $newBuilder = PackageBuilder::new();
        $this->assertTrue($newBuilder instanceof PackageBuilder);
    }

    public function testExpiryPeriod()
    {
        $this->builder->expiryPeriod('year');
        assertTrue($this->builder->getExpiryPeriod() === 'year');
    }

    public function testQuarterly()
    {
        $this->builder->quarterly();
        $this->assertEquals('month', $this->builder->getExpiryPeriod(), 'Quarterly packages should have an expiry period of 6 months.');
        $this->assertEquals(3, $this->builder->getExpiryLength(), 'Quarterly packages should have an expiry length of 6.');
        $this->assertEquals('subscription', $this->builder->getType(), 'Quarterly packages should be of type subscription.');
    }

    public function testType()
    {
        $this->builder->type("type-value");
        assertTrue($this->builder->getType() === "type-value");
    }

    public function testQty()
    {
        $this->builder->qty(5);
        assertTrue($this->builder->getQty() === 5);
    }

    public function testBuildPackage()
    {
        $this->builder->name("Test");
        $this->builder->price(10.99);
        $this->builder->qty(1);
        $this->builder->oneTime();
        $package = $this->builder->buildPackage();
        assertTrue($package instanceof Package);
    }

    public function testYearly()
    {
        $this->builder->yearly();
        $this->assertEquals('year', $this->builder->getExpiryPeriod(), 'Yearly packages should have an expiry period of 1 year.');
        $this->assertEquals(1, $this->builder->getExpiryLength(), 'Yearly packages should have an expiry length of 1.');
        $this->assertEquals('subscription', $this->builder->getType(), 'Yearly packages should be of type subscription.');
    }

    public function testSubscription()
    {
        $this->builder->subscription();
        $this->assertTrue($this->builder->getType() === 'subscription');
    }

    public function testExpiryLength()
    {
        $this->builder->expiryLength(365);
        $this->assertTrue($this->builder->getExpiryLength() === 365);
    }
}
