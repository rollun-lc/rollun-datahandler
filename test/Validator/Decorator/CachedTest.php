<?php

namespace rollun\test\datahandler\Validator\Decorator;

use PHPUnit\Framework\TestCase;
use rollun\datahandler\Validator\Decorator\Cached;
use Laminas\Validator\Digits;

/**
 * Class CachedTest
 * @package rollun\test\datahandler\Validator\Decorator
 */
class CachedTest extends TestCase
{
    public function testPositive()
    {
        $cachedDecorator = new Cached(new Digits());
        $this->assertTrue($cachedDecorator->isValid('123'));
        $this->assertFalse($cachedDecorator->isValid('123abc'));
    }
}
