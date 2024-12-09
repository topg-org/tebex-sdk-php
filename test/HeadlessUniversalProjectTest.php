<?php

namespace Tebex;

use PHPUnit\Framework\TestCase;
use Tebex\Headless\BasketFacade;
use Tebex\Headless\Projects\TebexProject;
use Tebex\Headless\Projects\UniversalProject;
class HeadlessUniversalProjectTest extends TestCase
{
    public string $publicToken = (string)getenv("TEBEX_HEADLESS_TEST_UNIVERSAL_PUBLIC_TOKEN"); // universal
    public TebexProject $project;

    protected function setUp() : void {
        $this->project = Headless::setProject($this->publicToken);
    }

    public function testGetUserIdentifierParameter()
    {
        if ($this->project instanceof UniversalProject) {
            $this->markTestSkipped("Universal projects don't have a user identifier.");
        } else {
            $this->assertNotEmpty($this->project->getUserIdentifierParameter());
        }
    }

    public function testListCategories()
    {
        $this->assertNotEmpty($this->project->listCategories());
    }

    public function testRequiresUserAuth()
    {
        if ($this->project instanceof UniversalProject) {
            $this->markTestSkipped("Universal projects don't require user auth.");
        } else {
            $this->assertTrue($this->project->requiresUserAuth());
        }
    }

    public function testCreateBasket()
    {
        $basketFacade = $this->project->createBasket("https://tebex.io/completed", "https://tebex.io/cancel");
        self::assertInstanceOf(BasketFacade::class, $basketFacade);
    }

    public function testGetUserAuthUrl()
    {
        if ($this->project instanceof UniversalProject) {
            $this->markTestSkipped("Universal projects don't require user auth.");
        } else {
            $this->assertNotEmpty($this->project->getUserAuthUrl());
        }
    }

    public function testGetRequiredBasketParams()
    {
        if ($this->project instanceof UniversalProject) {
            $this->markTestSkipped("Universal projects don't require basket parameters.");
        } else {
            $this->assertNotEmpty($this->project->getRequiredBasketParams());
        }
    }

    public function testListPackages()
    {
        $this->assertNotEmpty($this->project->listPackages());
    }

    public function testGetPlatformName()
    {
        $this->assertNotEmpty($this->project->getPlatformName());
    }
}
