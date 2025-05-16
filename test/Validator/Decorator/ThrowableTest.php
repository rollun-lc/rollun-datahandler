<?php

namespace rollun\test\datahandler\Validator\Decorator;

use PHPUnit\Framework\TestCase;
use rollun\datahandler\Validator\Decorator\Throwable;
use Laminas\Validator\Digits;
use Laminas\Validator\Exception\RuntimeException;

/**
 * Class ThrowableDecoratorTest
 * @package rollun\test\datahandler\Validator
 */
class ThrowableTest extends TestCase
{
    public function testExceptionWithoutExceptionMassage()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The input must contain only digits');
        $validator = new Throwable(new Digits());
        $validator->isValid('a');
    }

    public function testExceptionWithExceptionMassage()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Wait for exception: The input must contain only digits');
        $validator = new Throwable(new Digits(), 'Wait for exception');
        $validator->isValid('a');
    }

    public function testValidTrue()
    {
        $validator = new Throwable(new Digits());
        $this->assertTrue($validator->isValid('123'));
    }
}
