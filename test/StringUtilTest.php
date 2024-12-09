<?php

namespace Tebex\Util;

use PHPUnit\Framework\TestCase;

class StringUtilTest extends TestCase
{

    public function testContainsString()
    {
        self::assertTrue(StringUtil::containsString("foobar", "foo"));
    }

    public function testContainsEmptyString()
    {
        self::assertFalse(StringUtil::containsString("", "bar"));
    }

    public function testDoesNotContainString() {
        self::assertFalse(StringUtil::containsString("foobar", "baz"));
    }
}
