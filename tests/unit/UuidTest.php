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
class UuidTest extends BaseTestCase
{
    
    protected function formatUuidForOutput($uuid)
    {
        if (strlen($uuid) > 16)
        {
            $result = $uuid;
            $uuidBin = helicon\uuid\Uuid::convertToBin($uuid, null, false);
        }
        else
        {
            $result .= '(binary:'.bin2hex($uuid).')';
            $uuidBin = $uuid;
        }
        if (strlen($uuidBin) == 16)
        {
            $result .= helicon\uuid\Uuid::formatFromBin($uuidBin, helicon\uuid\Uuid::FORMAT_HEX_FULL);
        }
        return $result;
    }

    public function testGenerateDefaultFormat()
    {
        $uuid = \helicon\uuid\Uuid::newUuid();
        $this->assertTrue($uuid->validate(4));
    }

    public function testGenerateV4()
    {
        $uuid = \helicon\uuid\Uuid::newV4Uuid();
        $this->assertTrue($uuid->validate(4));
    }
    
    public function testNewUuidDirect()
    {
        $uuid = new helicon\uuid\Uuid();
        $this->assertTrue($uuid->validate(4));
    }
    
    /**
     * @param type $format
     * @param type $expected
     */
    public function testFormat()
    {
        $uuid = helicon\uuid\Uuid::newFromValue($this->getStdTestUuidBin());
        $testData = $this->stdTestUuidTestDataProvider();
        foreach ($testData as $v)
        {
            $this->assertEquals($v['formatted'], $uuid->format($v['format']));
        }
    }
    
    public function testUuidLt()
    {
        $uuid1 = helicon\uuid\Uuid::newFromValue('be0c8b753c0948e69653b149db655cad');
        $uuid2 = helicon\uuid\Uuid::newFromValue('be0c8b753c0948e69653b149db655cae');
        $this->assertTrue($uuid1->lt($uuid2));
    }
    
    public function testGenerateV4AscObjectsIsSequential()
    {
        $values = array();
        $failed = false;
        $lv = null;
        for ($n = 0; $n < 2000; $n++)
        {
            $v = helicon\uuid\Uuid::newV4AscUuid();
            if ($lv !== null && $v->lt($lv))
            {
                $failed = $this->formatUuidForOutput($v). ' smaller than '.$this->formatUuidForOutput($lv);
            }
        }
        if ($failed)
            $this->fail($failed);
    }
    
    
    
    

}
