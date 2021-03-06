<?php

namespace ValueObjects\Tests\Web;

use ValueObjects\Structure\Dictionary;
use ValueObjects\Tests\TestCase;
use ValueObjects\Web\QueryString;

class QueryStringTest extends TestCase
{
    public function testValidQueryString()
    {
        $query = new QueryString('?foo=bar');

        $this->assertInstanceOf('ValueObjects\Web\QueryString', $query);
    }

    /** @expectedException ValueObjects\Exception\InvalidNativeArgumentException */
    public function testInvalidQueryString()
    {
        new QueryString('invalìd');
    }

    public function testToDictionary()
    {
        $query = new QueryString('?foo=bar&array[]=one&array[]=two');
        $dictionary = $query->toDictionary();

        $this->assertInstanceOf('ValueObjects\Structure\Dictionary', $dictionary);

        $array = array(
            'foo'   => 'bar',
            'array' => array(
                'one',
                'two'
            )
        );
        $expectedDictionary = Dictionary::fromNative($array);

        $this->assertTrue($expectedDictionary->equals($dictionary));
    }
}