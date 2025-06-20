<?php
namespace Tests\Helpers;
use PHPUnit\Framework\TestCase;
use Helpers\BookHelpers;
class BookHelpersTest extends TestCase
{
    public function testFilterData()
    {
        $input = [
            'title' => 'Beyoğlu Rapsodisi',
            'isbn' => '9781234567890',
            'author_id' => 1,
            'category_id' => 2,
            'publication_year' => 2023,
            'page_count' => 300
        ];
        $expected = ['success' => true];
        $this->assertSame($expected, BookHelpers::filterTheData($input));
    }

    public function testFilterDataInvalidISBN()
    {
        $input = [
            'title' => 'Beyoğlu Rapsodisi',
            'isbn' => '1234567890', # Invalid ISBN
            'author_id' => 1,
            'category_id' => 2,
            'publication_year' => 2023,
            'page_count' => 300
        ];
        $expected = ['success' => false, 'message' => 'ISBN must be 13 digits'];
        $this->assertSame($expected, BookHelpers::filterTheData($input));
    }

    public function testFilterDataInvalidNumericFields()
    {
        $input = [
            'title' => 'Beyoğlu Rapsodisi',
            'isbn' => '9781234567890',
            'author_id' => 'one', # Invalid numeric field
            'category_id' => 2,
            'publication_year' => 2023,
            'page_count' => 300
        ];
        $expected = ['success' => false, 'message' => 'Author ID, Category ID, Publication Year and Page Count must be numeric'];
        $this->assertSame($expected, BookHelpers::filterTheData($input));
    }

    public function testFilterDataInvalidPublicationYear()
    {
        $input = [
            'title' => 'Sample Book',
            'isbn' => '9781234567890',
            'author_id' => 1,
            'category_id' => 2,
            'publication_year' => 23, # Invalid publication year
            'page_count' => 300
        ];
        $expected = ['success' => false, 'message' => 'Publication Year must be a 4-digit year'];
        $this->assertSame($expected, BookHelpers::filterTheData($input));
    }

}


?>