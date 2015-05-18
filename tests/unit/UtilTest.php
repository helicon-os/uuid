<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Util
 *
 * @author Andreas Prucha, Helicon Software Development
 */
class UtilTest extends \PHPUnit_Framework_TestCase
{

    protected $tests = 0;
    protected $backupUuidUtilDefaultVersion = null;
    protected $backupUuidUtilDefaultFormat = null;
    protected $backupUuidUtilDefaultRandomGenerator = null;

    protected function setUp()
    {
        if (!$this->tests) {
            $this->backupUuidUtilDefaultFormat = \helicon\uuid\Util::$defaultFormat;
            $this->backupUuidUtilDefaultVersion = \helicon\uuid\Util::$defaultVersion;
            $this->backupUuidUtilDefaultRandomGenerator = \helicon\uuid\Util::$defaultRandomGenerator;
        }
        $this->tests++;
        \helicon\uuid\Util::$defaultFormat = $this->backupUuidUtilDefaultFormat;
        \helicon\uuid\Util::$defaultVersion = $this->backupUuidUtilDefaultVersion;
        \helicon\uuid\Util::$defaultRandomGenerator = $this->backupUuidUtilDefaultRandomGenerator;
        parent::setUp();
    }

    public function testGenerateDefaultFormat()
    {
        $uuid = \helicon\uuid\Util::generate();
        $this->assertTrue(\helicon\uuid\Util::validateUuid($uuid, helicon\uuid\Util::FORMAT_HEX_SHORT));
    }

    public function testGenerateV4Short()
    {
        $uuid = \helicon\uuid\Util::generateV4(\helicon\uuid\Util::FORMAT_HEX_SHORT);
        $this->assertTrue(\helicon\uuid\Util::validateUuid($uuid, helicon\uuid\Util::FORMAT_HEX_SHORT, 4));
    }

    public function testGenerateV4WithDashes()
    {
        $uuid = \helicon\uuid\Util::generateV4(\helicon\uuid\Util::FORMAT_HEX_GROUPED);
        $this->assertTrue(\helicon\uuid\Util::validateUuid($uuid, helicon\uuid\Util::FORMAT_HEX_GROUPED, 4));
    }

    public function testGenerateV4Full()
    {
        $uuid = \helicon\uuid\Util::generateV4(\helicon\uuid\Util::FORMAT_HEX_FULL);
        $this->assertTrue(\helicon\uuid\Util::validateUuid($uuid, helicon\uuid\Util::FORMAT_HEX_FULL, 4));
    }
    
    public function testGenerateV4Urn()
    {
        $uuid = \helicon\uuid\Util::generateV4(\helicon\uuid\Util::FORMAT_URN);
        $this->assertTrue(\helicon\uuid\Util::validateUuid($uuid, helicon\uuid\Util::FORMAT_URN, 4));
    }
    

    public function testGenerateV4FullUpper()
    {
        $uuid = \helicon\uuid\Util::generateV4(\helicon\uuid\Util::FORMAT_HEX_FULL | \helicon\uuid\Util::FORMAT_HEX_OPT_UPPER);
        $this->assertTrue(\helicon\uuid\Util::validateUuid($uuid, \helicon\uuid\Util::FORMAT_HEX_FULL | \helicon\uuid\Util::FORMAT_HEX_OPT_UPPER, 4));
    }
    
    
    public function testIsV4Version()
    {
        $uuid = \helicon\uuid\Util::generateV4(\helicon\uuid\Util::FORMAT_HEX_FULL | \helicon\uuid\Util::FORMAT_HEX_OPT_UPPER);
        $this->assertEquals(4, \helicon\uuid\Util::getUuidVersion($uuid));
    }
    
    public function testMtGenerator()
    {
        \helicon\uuid\Util::$defaultRandomGenerator = \helicon\uuid\Util::RANDOM_MT;
        $uuid = \helicon\uuid\Util::generateV4(\helicon\uuid\Util::FORMAT_HEX_FULL | \helicon\uuid\Util::FORMAT_HEX_OPT_UPPER);
        $this->assertEquals(4, \helicon\uuid\Util::getUuidVersion($uuid));
    }
    
    public function testOpenSslGenerator()
    {
        \helicon\uuid\Util::$defaultRandomGenerator = \helicon\uuid\Util::RANDOM_OPENSSL;
        $uuid = \helicon\uuid\Util::generateV4(\helicon\uuid\Util::FORMAT_HEX_FULL | \helicon\uuid\Util::FORMAT_HEX_OPT_UPPER);
        $this->assertEquals(4, \helicon\uuid\Util::getUuidVersion($uuid));
    }
    
    public function testV4AscIsSequential()
    {
        $values = array();
        for ($n = 0; $n < 1000; $n++)
        {
            $values[] = helicon\uuid\Util::generateV4Asc();
        }
        $sorted = true;
        $lv = null;
        foreach ($values as $v)
        {
            if ($lv !== null && $lv > $v)
            {
                $sorted = false;
            }
            $lv = $v;
        }
        $this->assertTrue($sorted);
    }
    
    public function testValidateUuid()
    {
        $this->assertFalse(\helicon\uuid\Util::validateUuid('Thiscannotbeauuid'));
    }
    
    

}
