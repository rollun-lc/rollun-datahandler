<?php

namespace rollun\test\datahandler\Validator\Decorator;

use PHPUnit\Framework\TestCase;
use rollun\datahandler\Validator\Decorator\ArrayValidator;
use Zend\Validator\Digits;
use Zend\Validator\Exception\InvalidArgumentException;

/**
 * Class ArrayValidatorTest
 * @package rollun\test\datahandler\Validator
 */
class ArrayValidatorTest extends TestCase
{
    public function testPositiveOneColumnValid()
    {
        $validator = new Digits();
        $object = new ArrayValidator($validator, [
            'columnsToValidate' => 'key1'
        ]);
        $isValid = $object->isValid([
            'key1' => '1234'
        ]);

        $this->assertTrue($isValid);
    }

    public function testNegativeOneColumnValid()
    {
        $validator = new Digits();
        $object = new ArrayValidator($validator, [
            'columnsToValidate' => 'key1'
        ]);
        $isValid = $object->isValid([
            'key1' => 'abcd1234'
        ]);

        $this->assertFalse($isValid);
    }

    public function testPositiveSeveralColumnValid()
    {
        $validator = new Digits();
        $object = new ArrayValidator($validator, [
            'columnsToValidate' => ['key1', 'key2', 'key3']
        ]);
        $isValid = $object->isValid([
            'key1' => '12345',
            'key2' => '45678',
            'key3' => '89012',
        ]);

        $this->assertTrue($isValid);
    }

    public function testNegativeSeveralColumnValid()
    {
        $validator = new Digits();
        $object = new ArrayValidator($validator, [
            'columnsToValidate' => ['key1', 'key2', 'key3']
        ]);
        $isValid = $object->isValid([
            'key1' => '12345',
            'key2' => 'asdsf',
            'key3' => '89012',
        ]);

        $this->assertFalse($isValid);
    }

    public function testInvalidColumnException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("INVALID_COLUMN doesn't exist in incoming value");
        $validator = new Digits();
        $object = new ArrayValidator($validator, [
            'columnsToValidate' => ['key1', 'key2', 'INVALID_COLUMN']
        ]);
        $object->isValid([
            'key1' => '12345',
            'key2' => 'asdsf',
            'key3' => '89012',
        ]);
    }

    public function testInvalidColumnsToValidate()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid option 'columnsToValidate'");
        $validator = new Digits();
        new ArrayValidator($validator, [
            'columnsToValidate' => null
        ]);
    }
}
