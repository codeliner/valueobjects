<?php

namespace ValueObjects\Tests\Structure;

use ValueObjects\Number\Integer;
use ValueObjects\Number\Natural;
use ValueObjects\String\String;
use ValueObjects\Structure\Collection;
use ValueObjects\Tests\TestCase;

class CollectionTest extends TestCase
{
    /** @var Collection */
    protected $collection;

    public function setup()
    {
        $array = new \SplFixedArray(3);
        $array->offsetSet(0, new String('one'));
        $array->offsetSet(1, new String('two'));
        $array->offsetSet(2, new Integer(3));

        $this->collection = new Collection($array);
    }

    /** @expectedException \InvalidArgumentException */
    public function testInvalidArgument()
    {
        $array = \SplFixedArray::fromArray(array('one', 'two', 'three'));

        new Collection($array);
    }

    public function testFromNative()
    {
        $array = \SplFixedArray::fromArray(array(
            'one',
            'two',
            array(1, 2)
        ));
        $fromNativeCollection = Collection::fromNative($array);

        $innerArray = new Collection(
            \SplFixedArray::fromArray(array(
                    new String('1'),
                    new String('2')
            ))
        );
        $array = \SplFixedArray::fromArray(array(
            new String('one'),
            new String('two'),
            $innerArray
        ));
        $constructedCollection = new Collection($array);

        $this->assertTrue($fromNativeCollection->equals($constructedCollection));
    }

    public function testEquals()
    {
        $array = \SplFixedArray::fromArray(array(
            new String('one'),
            new String('two'),
            new Integer(3)
        ));
        $collection2 = new Collection($array);

        $array = \SplFixedArray::fromArray(array(
            'one',
            'two',
            array(1, 2)
        ));
        $collection3 = Collection::fromNative($array);

        $this->assertTrue($this->collection->equals($collection2));
        $this->assertTrue($collection2->equals($this->collection));
        $this->assertFalse($this->collection->equals($collection3));

        $mock = $this->getMock('ValueObjects\ValueObjectInterface');
        $this->assertFalse($this->collection->equals($mock));
    }

    public function testCount()
    {
        $three = new Natural(3);

        $this->assertTrue($this->collection->count()->equals($three));
    }

    public function testContains()
    {
        $one = new String('one');
        $ten = new String('ten');

        $this->assertTrue($this->collection->contains($one));
        $this->assertFalse($this->collection->contains($ten));
    }

    public function testToArray()
    {
        $array = array(
            new String('one'),
            new String('two'),
            new Integer(3)
        );

        $this->assertEquals($array, $this->collection->toArray());
    }

    public function testToString()
    {
        $this->assertEquals('ValueObjects\Structure\Collection(3)', $this->collection->__toString());
    }
}