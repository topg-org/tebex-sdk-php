<?php

namespace Tebex\Headless;

use PHPUnit\Framework\TestCase;
use Tebex\Headless;
use Tebex\Headless\Projects\TebexProject;
use TebexHeadless\Model\Basket;
use TebexHeadless\Model\Package;
use function PHPUnit\Framework\assertTrue;

class HeadlessBasketFacadeTest extends TestCase
{
    public string $publicToken =  (string)getenv("TEBEX_HEADLESS_PUBLIC_TOKEN");

    public TebexProject $project;
    public BasketFacade $basket;
    public static Package $package1;

    protected function setUp() : void {
        $this->project = Headless::setProject($this->publicToken);
        $this->basket = new BasketFacade(Headless::createBasket([
            "complete_url" => "https://tebex.io/complete",
            "cancel_url" => "https://tebex.io/cancel",
        ]), $this->project);

        if (empty(HeadlessBasketFacadeTest::$package1)) {
            HeadlessBasketFacadeTest::$package1 = Headless::getAllPackages()[0];
        }
    }

    public function testAddPackageWithGameServerCommand()
    {
        $this->markTestSkipped("No testable parameters in response");
        return;

        $basket = $this->basket->addPackageWithGameServerCommand(self::$package1, "Tebex");
        $packages = $basket->getBasket()->getPackages();

        $this->assertTrue($basket->getBasePrice() > 0);
        var_dump($packages);
        $this->assertArrayHasKey("username", (array)$packages[0]); // FIXME username is not attached?
    }

    public function testAddPackageWithCustomData()
    {
        $basket = $this->basket->addPackageWithCustomData(self::$package1, [
            "username" => "Tebex"
        ], [
            "foo" => "bar"
        ]);
        $packages = $basket->getBasket()->getPackages();

        var_dump($packages);
        $this->assertArrayHasKey("custom", (array)$packages[0]);
    }

    public function testAddPackageWithDiscordDeliverable()
    {
        $this->markTestSkipped("No testable parameters in response");
        return;

        // package must be a discord type in order to fail if discordId is wrong, otherwise is accepted
        $basket = $this->basket->addPackageWithDiscordDeliverable(self::$package1, "0000000");
        $packages = $basket->getBasket()->getPackages();
        var_dump($packages);
        $this->assertArrayHasKey("discord_id", (array)$packages[0]);
    }

    public function testAddCoupon()
    {
        $this->basket->addPackage(self::$package1);
        $this->basket->addCoupon("test", true);
        self::assertNotEmpty($this->basket->getBasket()->getCoupons());
    }

    public function testGetLinks()
    {
        $this->assertNotEmpty($this->basket->getLinks());
    }

    public function testRefreshBasket()
    {
        $this->basket->getBasket()->setBasePrice(1.00);
        $this->assertEquals(1.00, $this->basket->getBasePrice());
        $this->basket->refreshBasket();
        $this->assertEquals(0.00, $this->basket->getBasePrice());
    }

    public function testAddGiftCardPackage()
    {
        $this->markTestSkipped("No testable parameters in response");
        return;

        //gift card data is not returned, but gifted data is contained in the returned $package
        $basket = $this->basket->addGiftCardPackage(self::$package1, "tebex-integrations@overwolf.com");
        $packages = $basket->getBasket()->getPackages();
        var_dump($packages);
        $this->assertArrayHasKey("giftcard_to", (array)$packages[0]);
    }

    public function testAddCreatorCode()
    {
        $this->basket->addPackage(self::$package1);
        self::assertEmpty($this->basket->getBasket()->getCreatorCode());
        $this->basket->addCreatorCode("TebexDev", true);
        self::assertNotEmpty($this->basket->getBasket()->getCreatorCode());
    }

    public function testGetBasket()
    {
        $this->assertInstanceOf(Basket::class, $this->basket->getBasket());
    }

    public function testAddPackage()
    {
        self::assertEmpty($this->basket->getBasket()->getPackages());
        $this->basket->addPackage(self::$package1);
        self::assertNotEmpty($this->basket->getBasket()->getPackages());
    }

    public function testAddPackageForTargetGameServer()
    {
        $this->markTestSkipped("No testable parameters in response");
        return;

        //should fail if server id is not correct
        $basket = $this->basket->addPackageForTargetGameServer(self::$package1, "TebexDev", 123456);
        $packages = $basket->getBasket()->getPackages();
        var_dump($packages);
        $this->assertArrayHasKey("username", (array)$packages[0]);
    }

    public function testAddGiftCard()
    {
        $this->basket->addPackage(self::$package1);
        self::assertEmpty($this->basket->getBasket()->getGiftcards());
        $this->basket->addGiftCard("8616880565801044", true);
        self::assertNotEmpty($this->basket->getBasket()->getGiftcards());
    }

    public function testRemoveGiftCard()
    {
        $this->basket->addPackage(self::$package1);
        self::assertEmpty($this->basket->getBasket()->getGiftcards());
        $this->basket->addGiftCard("8616880565801044", true);
        self::assertNotEmpty($this->basket->getBasket()->getGiftcards());
        $this->basket->removeGiftCard("8616880565801044", true);
        self::assertEmpty($this->basket->getBasket()->getGiftcards());
    }

    public function testGetBasePrice()
    {
        assertTrue($this->basket->getBasePrice() == 0);
        $this->basket->addPackage(self::$package1);
        assertTrue($this->basket->getBasePrice() > 0);
    }

    public function testRemoveCreatorCode()
    {
        $this->basket->addPackage(self::$package1);
        self::assertEmpty($this->basket->getBasket()->getCreatorCode());
        $this->basket->addCreatorCode("TebexDev", true);
        self::assertNotEmpty($this->basket->getBasket()->getCreatorCode());
        $this->basket->removeCreatorCode("TebexDev", true);
        self::assertEmpty($this->basket->getBasket()->getCreatorCode());
    }

    public function testAddGiftedPackage()
    {
        $this->markTestSkipped("No testable parameters in response");
        return;

        $this->basket->addGiftedPackage(self::$package1, "TebexDev", []);
        $package = $this->basket->getBasket()->getPackages()[0];
        var_dump($package);
    }

    public function testRemoveCoupon()
    {
        $this->basket->addPackage(self::$package1);
        $this->basket->addCoupon("test", true);
        self::assertNotEmpty($this->basket->getBasket()->getCoupons());
        $this->basket->removeCoupon("test", true);
        self::assertEmpty($this->basket->getBasket()->getCoupons());
    }
}
