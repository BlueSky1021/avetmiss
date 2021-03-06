<?php

namespace Bdt\Avetmiss\Tests;

use Bdt\Avetmiss\Utilities;


class UtilitiesTest extends TestCase
{


    public function testValidMysqlDate()
    {
        $this->assertEquals('15022014', Utilities::toDate('2014-02-15'));
    }


    public function testValidMysqlDateTime()
    {
        $this->assertEquals('07122014', Utilities::toDate('2014-12-07 20:11:02'));
    }


    public function testNullReturnsNull()
    {
        $this->assertNull(Utilities::toDate(null));
    }

    /**
     * @expectedException Exception
     */
    public function testInvalidDateThrowsException()
    {
        Utilities::toDate('foo');
    }


    public function testToNameForEncryption()
    {
        $this->assertEquals('bond, james', Utilities::toNameForEncryption('james', 'bond'));
    }

    public function testSingularNameToNameForEncryption()
    {
        $this->assertEquals('james bond,', Utilities::toNameForEncryption('james bond', ''));
    }

    public function testMiddleNameToNameForEncryption()
    {
        $this->assertEquals("bond, james herbert", Utilities::toNameForEncryption("james", "bond", "herbert"));
    }
}
