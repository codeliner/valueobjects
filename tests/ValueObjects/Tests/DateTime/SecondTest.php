<?php

namespace ValueObjects\Tests\DateTime;

use ValueObjects\Tests\TestCase;
use ValueObjects\DateTime\Second;

class SecondTest extends TestCase
{
    public function testFromNative()
    {
        $fromNativeSecond  = Second::fromNative(13);
        $constructedSecond = new Second(13);

        $this->assertTrue($fromNativeSecond->equals($constructedSecond));
    }

    public function testNow()
    {
        $second = Second::now();
        $this->assertEquals(\intval(date('s')), $second->getValue());
    }

    /** @expectedException ValueObjects\Exception\InvalidNativeArgumentException */
    public function testInvalidSecond()
    {
        new Second(60);
    }

}
