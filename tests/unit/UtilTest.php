<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once (__DIR__.'/BaseTestCase.php');

/**
 * Description of Util
 *
 * @author Andreas Prucha, Helicon Software Development
 */
class UtilTest extends BaseTestCase
{

    
    protected function formatUuidForOutput($uuid)
    {
        if (strlen($uuid) > 16)
        {
            $result = $uuid;
            $uuidBin = helicon\uuid\Util::convertToBin($uuid, null, false);
        }
        else
        {
            $result .= '(binary:'.bin2hex($uuid).')';
            $uuidBin = $uuid;
        }
        if (strlen($uuidBin) == 16)
        {
            $result .= helicon\uuid\Util::formatFromBin($uuidBin, helicon\uuid\Util::FORMAT_HEX_FULL);
        }
        return $result;
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
    
    public function testGenerateV4Enc32()
    {
        $uuid = \helicon\uuid\Util::generateV4(\helicon\uuid\Util::FORMAT_ENC32);
        $this->assertTrue(\helicon\uuid\Util::validateUuid($uuid, helicon\uuid\Util::FORMAT_ENC32, 4));
    }
    
    public function testGenerateV4Enc64()
    {
        $uuid = \helicon\uuid\Util::generateV4(\helicon\uuid\Util::FORMAT_ENC64);
        $this->assertTrue(\helicon\uuid\Util::validateUuid($uuid, helicon\uuid\Util::FORMAT_ENC64, 4));
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
        \helicon\uuid\Util::$configDefaultRandomGenerator = \helicon\uuid\Util::RANDOM_MT;
        $uuid = \helicon\uuid\Util::generateV4(\helicon\uuid\Util::FORMAT_HEX_FULL | \helicon\uuid\Util::FORMAT_HEX_OPT_UPPER);
        $this->assertEquals(4, \helicon\uuid\Util::getUuidVersion($uuid));
    }
    
    public function testOpenSslGenerator()
    {
        \helicon\uuid\Util::$configDefaultRandomGenerator = \helicon\uuid\Util::RANDOM_OPENSSL;
        $uuid = \helicon\uuid\Util::generateV4(\helicon\uuid\Util::FORMAT_HEX_FULL | \helicon\uuid\Util::FORMAT_HEX_OPT_UPPER);
        $this->assertEquals(4, \helicon\uuid\Util::getUuidVersion($uuid));
    }
    
    public function testGenerateV4AscDefaultIsSequential()
    {
        $values = array();
        for ($n = 0; $n < 2000; $n++)
        {
            $values[] = helicon\uuid\Util::generateV4Asc();
        }
        $failed = false;
        $lv = null;
        foreach ($values as $v)
        {
            if ($lv !== null && $lv > $v)
            {
                $failed = $this->formatUuidForOutput($v). ' smaller than '.$this->formatUuidForOutput($lv);
            }
            $lv = $v;
        }
        if ($failed)
            $this->fail($failed);
    }
    
    public function testGenerateV4AscDefaultIsSequentialOnMachineWithBadTimer()
    {
        \helicon\uuid\Util::$configTestOptions['badTimer'] = true;
        $values = array();
        for ($n = 0; $n < 2000; $n++)
        {
            $values[] = helicon\uuid\Util::generateV4Asc();
        }
        $failed = false;
        $lv = null;
        foreach ($values as $v)
        {
            if ($lv !== null && $lv > $v)
            {
                $failed = $this->formatUuidForOutput($v). ' smaller than '.$this->formatUuidForOutput($lv);
            }
            $lv = $v;
        }
        if ($failed)
            $this->fail($failed);
    }
    
    
    public function testGenerateV4AscEnc32IsSequential()
    {
        $values = array();
        for ($n = 0; $n < 2000; $n++)
        {
            $values[] = helicon\uuid\Util::generateV4Asc(helicon\uuid\Util::FORMAT_ENC32);
        }
        $failed = false;
        $lv = null;
        foreach ($values as $v)
        {
            if ($lv !== null && $lv > $v)
            {
                $failed = $this->formatUuidForOutput($v). ' smaller than '.$this->formatUuidForOutput($lv);
            }
            $lv = $v;
        }
        if ($failed)
            $this->fail($failed);
    }
    
    public function testGenerateV4AscEnc64IsSequential()
    {
        $values = array();
        for ($n = 0; $n < 2000; $n++)
        {
            $values[] = helicon\uuid\Util::generateV4Asc(helicon\uuid\Util::FORMAT_ENC64);
        }
        $failed = false;
        $lv = null;
        foreach ($values as $v)
        {
            if ($lv !== null && $lv > $v)
            {
                $failed = $this->formatUuidForOutput($v). ' smaller than '.$this->formatUuidForOutput($lv);
            }
            $lv = $v;
        }
        if ($failed)
            $this->fail($failed);
    }
    
    
    public function testValidateUuid()
    {
        $this->assertFalse(\helicon\uuid\Util::validateUuid('Thiscannotbeauuid'));
    }
    
    public function testConvertUuidToBin()
    {
        $testData = $this->stdTestUuidTestDataProvider();
        foreach ($testData as $v)
        {
            $this->assertEquals($this->getStdTestUuidBin(), helicon\uuid\Util::convertToBin($v['formatted']));
        }
    }
    
    
    

}
