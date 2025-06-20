<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use Helpers\AppHelpers;

class AppHelpersTest extends TestCase
{
    public function testCleanString()
    {
        $input = '<script>alert("xss")</script>Test and string';
        $expected = 'alert(&quot;xss&quot;)Test and string';
        $this->assertSame($expected, AppHelpers::cleanString($input));
    }

    public function testCleanArray()
    {
        $input = [
            'name' => '<b>Burak</b>',
            'email' => 'Burak@example.com<script>',
            'age' => 30
        ];
        $expected = [
            'name' => 'Burak',
            'email' => 'Burak@example.com',
            'age' => 30
        ];
        $this->assertSame($expected, AppHelpers::cleanArray($input));
    }

    public function testGetSqlErrorMessageKnown()
    {
        $code = 1062;
        $message = 'Duplicate entry';
        $this->assertSame('Duplicate entry', AppHelpers::getSqlErrorMessage($code, $message));
    }

    public function testGetSqlErrorMessageUnknown()
    {
        $code = 9999;
        $this->assertSame('Unknown SQL error.', AppHelpers::getSqlErrorMessage($code));
    }

    public function testIsValidIdValid()
    {
        $result = AppHelpers::isValidId(5);
        $this->assertTrue($result['success']);
        $this->assertSame(5, $result['id']);
    }

    public function testIsValidIdInvalid()
    {
        $result = AppHelpers::isValidId('abc');
        $this->assertFalse($result['success']);
        $this->assertNull($result['id']);
    }
}
