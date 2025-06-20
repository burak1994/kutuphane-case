<?php
namespace Tests\Helpers;
use PHPUnit\Framework\TestCase;
use Helpers\CategoryHelpers;
class CategoryHelpersTest extends TestCase
{
    public function testFilterData(): void
    {
        $input = [
            'name' => 'Mizah',
            'description' => 'Bu bölümde mizah kitapları yer alır.'
        ];
        $expected = ['success' => true];
        $this->assertSame($expected, CategoryHelpers::filterTheData($input));
    }

    public function testFilterDataInvalidName(): void
    {
        $input = [
            'name' => '', // Invalid name
            'description' => 'Bu bölümde mizah kitapları yer alır.'
        ];
        $expected = ['success' => false, 'message' => 'All fields are required'];
        $this->assertSame($expected, CategoryHelpers::filterTheData($input));
    }

 
}


?>