<?php

namespace Tebex\Checkout;

use PHPUnit\Framework\TestCase;
use Tebex\Checkout;
use TebexCheckout\Model\Basket;

class CheckoutBasketBuilderTest extends TestCase
{
    public int $projectId = 1361049;
    public string $privateKey = (string)getenv("TEBEX_CHECKOUT_PRIVATE_KEY");

    private BasketBuilder $builder;

    protected function setUp(): void
    {
        $this->builder = BasketBuilder::new();
    }

    public function testFirstname()
    {
        $this->builder->firstname('John');
        $this->assertEquals('John', $this->builder->getFirstname());
    }

    public function testCustom()
    {
        $this->builder->custom(['foo' => 'bar']);
        $this->assertEquals(['foo' => 'bar'], $this->builder->getCustom());
    }

    public function testCompleteAutoRedirect()
    {
        $this->builder->completeAutoRedirect(true);
        $this->assertTrue($this->builder->getCompleteAutoRedirect());
    }

    public function testBuild()
    {
        // properly configured builder with required params
        $this->builder->firstname('John');
        $this->builder->lastname('Doe');
        $this->builder->email('tebex-integrations@overwolf.com');
        $this->builder->returnUrl("https://tebex.io/");
        $this->builder->completeUrl("https://tebex.io/");

        Checkout::setApiKeys($this->projectId, $this->privateKey);
        $this->assertInstanceOf(Basket::class, $this->builder->build());
    }

    public function testInvalidBuild() {
        // unconfigured builder
        $this->expectException(\ValueError::class);
        $this->builder->build();
    }

    public function testEmail()
    {
        $this->builder->email('test@example.com');
        $this->assertEquals('test@example.com', $this->builder->getEmail());
    }

    public function testNew()
    {
        $this->builder = $this->builder->new();
        $this->assertInstanceOf(BasketBuilder::class, $this->builder);
    }

    public function testLastname()
    {
        $this->builder->lastname('Doe');
        $this->assertEquals('Doe', $this->builder->getLastname());
    }

    public function testCompleteUrl()
    {
        $this->builder->completeUrl('http://example.com/complete');
        $this->assertEquals('http://example.com/complete', $this->builder->getCompleteUrl());
    }

    public function testReturnUrl()
    {
        $this->builder->returnUrl('http://example.com/return');
        $this->assertEquals('http://example.com/return', $this->builder->getReturnUrl());
    }

    public function testIp()
    {
        $this->builder->ip('127.0.0.1');
        $this->assertEquals('127.0.0.1', $this->builder->getIp());
    }

    public function testCreatorCode()
    {
        $this->builder->creatorCode('creator_code');
        $this->assertEquals('creator_code', $this->builder->getCreatorCode());
    }
}
