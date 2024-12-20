<?php

require_once '../vendor/autoload.php';
use Tebex\Checkout;

Checkout::setApiKeys("123456789", "your-private-key");

// Use the BasketBuilder to create your basket for Checkout
$builder = Checkout\BasketBuilder::new()
    ->email("tebex-integrations@overwolf.com")
    ->firstname("Tebex")
    ->lastname("Integrations")
    ->ip("127.0.0.1")
    ->returnUrl("https://tebex.io/")
    ->completeUrl("https://tebex.io/");

// Use the PackageBuilder to create packages.
$package1 = Checkout\PackageBuilder::new()->name("100 Gold")->qty(1)->price(1.27)->oneTime();
$package2 = Checkout\PackageBuilder::new()->name("1 Month Sub")->qty(1)->price(2.44)
    ->subscription()->monthly()->expiryLength(1);

// Recommended: You can create a single checkout request containing the basket info, all packages, and any sales.
$checkoutItems = [$package1->buildCheckoutItem(), $package2->buildCheckoutItem()];
echo json_encode($checkoutItems);

$basket = Checkout::checkoutRequest($builder, $checkoutItems, null);

// Alternate: You can add, remove, or change packages as needed after initially building a basket
$basket = $builder->build();

// Each addPackage call provides the new, updated basket
$basket = Checkout::addPackage($basket, $package1->buildPackage());
$basket = Checkout::addPackage($basket, $package2->buildPackage());

// The $basket object contains all property getters
echo "Price: " . $basket->getPrice() . "\n";

// Go to checkout
$checkoutLink = $basket->getLinks()->getCheckout();
echo "Checkout at: " . $checkoutLink;