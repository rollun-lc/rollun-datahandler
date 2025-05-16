<?php

namespace rollun\test\datahandler\Validator;

use Laminas\Validator\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use rollun\datahandler\Validator\IsColumnExist;

/**
 * Class IsColumnExistTest
 * @package rollun\test\datahandler\Validator
 */
class IsColumnExistTest extends TestCase
{
    public function testPositiveOneColumnValidTrue()
    {
        $validatedColumn = 'key';
        $validator = new IsColumnExist([
            'validateColumns' => $validatedColumn
        ]);

        $this->assertTrue($validator->isValid(['key' => 'value']));
    }

    public function testPositiveSeveralColumnValidTrue()
    {
        $validatedColumn = [
            'key1',
            'key2'
        ];
        $validator = new IsColumnExist([
            'validateColumns' => $validatedColumn
        ]);

        $this->assertTrue(
            $validator->isValid([
                'key1' => 'values1',
                'key2' => 'values2',
            ])
        );
    }

    public function testPositiveValidFalse()
    {
        $validatedColumn = [
            'key1',
            'key2'
        ];
        $validator = new IsColumnExist([
            'validateColumns' => $validatedColumn
        ]);

        $this->assertFalse(
            $validator->isValid([
                'key2' => 'values2',
            ])
        );
    }

    public function testNegativeMissingValidateColumns()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Missing 'validateColumns' option");
        $validator = new IsColumnExist();
        $validator->getValidateColumns();
    }

    public function testNegativeUnsetValidateColumns()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid option 'validateColumns'");
        $validator = new IsColumnExist([
            'validateColumns' => null
        ]);
    }
}
