<?php 
namespace Tests\Helpers;

use PHPUnit\Framework\TestCase;
use Helpers\AuthorHelpers;
use Models\Author;

class AuthorHelpersTest extends TestCase
{
    public function testFilterData()
    {
        $input = [
            'name' => 'Burak',
            'email'=> 'burak@example.com'
        ];
        $expected = ['success' => true];
        $this->assertEquals($expected, AuthorHelpers::filterTheData($input));

    }
    public function testFilterDataInValidEmail()
    {
        $input = [
            'name' => 'Burak',
            'email'=> 'burakexample.com'
        ];
        $expected = ['success' => false, 'message' => 'Email must be a valid email address'];
        $this->assertEquals($expected,AuthorHelpers::filterTheData($input));
    }

}

?>