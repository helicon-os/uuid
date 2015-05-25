<?php

/*
 * Copyright (c) 2015, Andreas Prucha, Helicon Software Development
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * * Redistributions of source code must retain the above copyright notice, this
 *   list of conditions and the following disclaimer.
 * * Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

/**
 * Description of BaseTestCase
 *
 * @author Andreas Prucha, Helicon Software Development
 */
class BaseTestCase extends \PHPUnit_Framework_TestCase
{
    //put your code here
    
    protected $tests = 0;
    protected $backupUuidConfigTestOptions = null;
    protected $backupUuidUtilConfigDefaultGenerator = null;
    protected $backupUuidUtilConfigDefaultFormat = null;
    protected $backupUuidUtilConfigDefaultRandomGenerator = null;

    protected function setUp()
    {
        if (!$this->tests) {
            $this->backupUuidConfigTestOptions = \helicon\uuid\Util::$configTestOptions;
            $this->backupUuidUtilConfigDefaultFormat = \helicon\uuid\Util::$configDefaultFormat;
            $this->backupUuidUtilConfigDefaultGenerator = \helicon\uuid\Util::$configDefaultGenerator;
            $this->backupUuidUtilConfigDefaultRandomGenerator = \helicon\uuid\Util::$configDefaultRandomGenerator;
        }
        $this->tests++;
        \helicon\uuid\Util::$configTestOptions = $this->backupUuidConfigTestOptions;
        \helicon\uuid\Util::$configDefaultFormat = $this->backupUuidUtilConfigDefaultFormat;
        \helicon\uuid\Util::$configDefaultGenerator = $this->backupUuidUtilConfigDefaultGenerator;
        \helicon\uuid\Util::$configDefaultRandomGenerator = $this->backupUuidUtilConfigDefaultRandomGenerator;
        
        parent::setUp();
    }
    
    protected function getStdTestUuidBin()
    {
        return hex2bin('be0c8b753c0948e69653b149db655cad');        
    }
    
    /**
     * Returns an array of expected format()-Results of the uuid be0c8b75-3c09-48e6-9653-b149db655cad
     * @return type
     */
    public function stdTestUuidTestDataProvider()
    {
        return array(
            array('format' => helicon\uuid\Util::FORMAT_BIN, 'formatted' => hex2bin('be0c8b753c0948e69653b149db655cad')),
            array('format' => helicon\uuid\Util::FORMAT_HEX_SHORT, 'formatted' => 'be0c8b753c0948e69653b149db655cad'),
            array('format' => helicon\uuid\Util::FORMAT_HEX_GROUPED, 'formatted' => 'be0c8b75-3c09-48e6-9653-b149db655cad'),
            array('format' => helicon\uuid\Util::FORMAT_HEX_FULL, 'formatted' => '{be0c8b75-3c09-48e6-9653-b149db655cad}'),
            array('format' => helicon\uuid\Util::FORMAT_HEX_GROUPED | helicon\uuid\Util::FORMAT_HEX_OPT_UPPER, 'formatted' => 'BE0C8B75-3C09-48E6-9653-B149DB655CAD'),
            array('format' => helicon\uuid\Util::FORMAT_ENC32, 'formatted' => 'pq68nw9u154ed5ijn54wnraum5'),
            array('format' => helicon\uuid\Util::FORMAT_ENC64, 'formatted' => 'jUm9RHk7GCOKIv37qqJQf.')
        );
    }
    
    
}
