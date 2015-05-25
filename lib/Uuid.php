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

namespace helicon\uuid;

/**
 * Uuid Generator
 *
 * @author Andreas Prucha, Helicon Software Development
 */
class Uuid extends Core
{
    /**
     * @var string      Binary Big Endian representation of this Uuid
     * @param null|int  Generator to use. Can be any GENERATOR_Xxxx-Constant or 
     *                  null (default). If null is passed, the default generator is used.
     *                  In order to generate a Nil-Uuid, specify generator 0.
     *                   
     */
    private $uuidValueBin = self::NIL_BIN;

    /**
     * Cached formatted representations 
     * @var array 
     */
    private $cachedUuidValueRepresentations = array();

    /**
     * Creates a new Uuid-Instance
     * 
     * @param null|int|string $generatorOrUuid  Generator-Id or Uuid-Value
     * @param null|int $format                  Optional Format-Hint if $generatorOrUuid is a Uuid
     * @throws Exception
     */
    public function __construct($generatorOrUuid = null, $format = null)
    {
        if (is_int($generatorOrUuid) || $generatorOrUuid === null) {
            isset($generatorOrUuid) || $generatorOrUuid = Util::$configDefaultGenerator;
            switch ($generatorOrUuid) {
                case self::GENERATOR_NIL:
                    $this->setNil();
                    break;
                case self::GENERATOR_V4:
                    $this->setNewV4();
                    break;
                case self::GENERATOR_V4_ASC:
                    $this->setNewV4Asc();
                    break;
                default:
                    throw new Exception('Unsupported or invalid Uuid format requested');
            }
        } else {
            $this->setAsBin(Util::convertToBin($generatorOrUuid, $format));
        }
    }

    /**
     * Sets a magic property 
     * 
     * @param type $name
     * @param type $value
     * @return type
     * @throws Exception
     */
    public function __set($name, $value)
    {
        switch ($name) {
            case 'asBin': return $this->setAsBin($uuid);
            default: throw new Exception('Unknown property ' . $name);
        }
    }

    /**
     * Returns a magic property 
     * 
     * @param type $name
     * @return type
     * @throws Exception
     */
    public function __get($name)
    {
        switch ($name) {
            case 'asBin': return $this->getAsBin();
            default: throw new Exception('Unknown property ' . $name);
        }
    }


    /**
     * Creates a new Uuid-Object with the given Uuid
     * @param \helicon\uuid\Uuid|string|binary $uuid
     * @param type $format
     * @return \helicon\uuid\Uuid   New Uuid-Object
     */
    public static function newFromValue($uuid, $format = null)
    {
        return new static((string)$uuid, $format);
    }

    /**
     * Creates a new Uuid using the default generator
     * 
     * @return Uuid
     */
    public static function newUuid()
    {
        return new static(Util::$configDefaultGenerator);
    }

    /**
     * Creates a new V4 (Random) Uuid
     * 
     * @return Uuid
     */
    public static function newV4Uuid()
    {
        return new static(self::GENERATOR_V4);
    }

    /**
     * Creates a new V4 (Random) Uuid
     * 
     * @return Uuid
     */
    public static function newV4AscUuid()
    {
        return new static(self::GENERATOR_V4_ASC);
    }

    /**
     * Generates a new V4 Uuid for this instance
     */
    public function setNewV4()
    {
        $this->setAsBin(Util::generateV4(self::FORMAT_BIN));
    }

    /**
     * Generates a new (usually) ascending Uuid
     */
    protected function setNewV4Asc()
    {
        $this->setAsBin(Util::generateV4Asc(self::FORMAT_BIN));
    }

    /**
     * Sets the Uuid to NIL (All bits 0)
     */
    public function setNil()
    {
        $this->setAsBin(self::NIL_BIN);
    }

    /**
     * Returns the Uuid as binary string
     */
    public function getAsBin()
    {
        return empty($this->uuidValueBin) ? self::NIL_BIN : $this->uuidValueBin;
    }

    /**
     * Set the Uuid by hex string 
     * 
     * Note: This function filters all invalid characters, thus accepts any hex format
     * 
     * @param type $uuid
     */
    public function setAsHex($uuid)
    {
        $uuid = hex2bin(preg_replace('/[^a-zA-Z0-9]/', '', str_replace('urn:uuid:', '', $uuid)));
        $this->setAsBin($uuid);
    }

    /**
     * Returns the Uuid as hex string w/o groupings or braces
     * @return type
     */
    public function getAsHexShort()
    {
        return $this->format(self::FORMAT_HEX_SHORT);
    }

    /**
     * 
     * @param type $uuid
     * @return type
     */
    public function setAsHexShort($uuid)
    {
        $this->setAsHex($uuid);
    }

    /**
     * Returns the Uuid as Grouped Hex string
     */
    public function getAsHexGrouped()
    {
        return $this->format(self::FORMAT_HEX_GROUPED);
    }

    /**
     * Sets the Uuid as grouped Hex string
     * 
     * @param type $uuid
     * @return type
     */
    public function setAsHexGrouped($uuid)
    {
        $this->setAsHex($uuid);
    }

    /**
     * Returns the Uuid as Grouped Hex string enclosed in braces
     */
    public function getAsHexFull()
    {
        return $this->format(self::FORMAT_HEX_FULL);
    }

    /**
     * Sets the Uuid as full Hex string with separators and braces
     * 
     * @param type $uuid
     * @return type
     */
    public function setAsHexFull($uuid)
    {
        $this->setAsHex($uuid);
    }

    /**
     * Returns the Uuid as Grouped Hex string enclosed in braces
     */
    public function getAsEnc32()
    {
        return $this->format(self::FORMAT_ENC32);
    }

    /**
     * Sets the Uuid as full Hex string with separators and braces
     * 
     * @param type $uuid
     * @return type
     */
    public function setAsEnc32($uuid)
    {
        $uuid = self::baseXDecode($uuid, self::ENC32_ALPHABET);
        $this->setAsBin($uuid);
    }

    /**
     * Returns the Uuid as Grouped Hex string enclosed in braces
     */
    public function getAsEnc64()
    {
        return $this->format(self::FORMAT_ENC64);
    }

    /**
     * Sets the Uuid as full Hex string with separators and braces
     * 
     * @param type $uuid
     * @return type
     */
    public function setAsEnc64($uuid)
    {
        $uuid = self::baseXDecode($uuid, self::ENC64_ALPHABET);
        $this->setAsBin($uuid);
    }

    /**
     * Sets the Uuid as binary string
     * 
     * @param string $uuid
     */
    public function setAsBin($uuid)
    {
        $this->uuidValueBin = !empty($uuid) ? $uuid : self::NIL_BIN;
    }

    protected static function baseXEncode($dataSource, $alphabet)
    {
        $result = '';
        $bits = ceil(log10(strlen($alphabet)) / log10(2));
        $sBytes = strlen($dataSource);
        $sBits = $sBytes * 8;
        $i = 0;
        $usedBitsMask = 0xFF >> (8 - $bits);
        while ($i < $sBits) {
            $bo = (int) floor($i / 8);
            $io = $i % 8;
            $bon = 8 - ($io + $bits); // remaining unused bits (negative if byte boundaries are crossed)
            if ($bon < 0) {
                if ($bo < $sBytes - 1) {
                    // Crossing byte - Handle two bytes
                    $v = (ord($dataSource[$bo]) << 8) | ord($dataSource[$bo + 1]);
                    $v = ($v >> (8 + $bon)) & $usedBitsMask;
                } else {
                    // Crossing byte boundaries - but no more bytes
                    $v = ord($dataSource[$bo]);
                    $v = $v & (0xFF >> $io);
                }
            } else {
                // Single byte
                $v = ord($dataSource[$bo]);
                $v = ($v >> $bon) & $usedBitsMask;
            }
            $result .= $alphabet[$v];
            $i += $bits;
        }
        return $result;
    }

    protected static function baseXDecode($encodedSrc, $alphabet)
    {
        $result = '';
        $l = strlen($encodedSrc);
        if ($l > 0) {
            $bits = ceil(log10(strlen($alphabet)) / log10(2));
            $b = 0;
            $iOutBit = 0;
            for ($i = 0; $i < $l; $i++) {
                $bo = (int) ($iOutBit / 8);
                $io = $iOutBit % 8;
                $v = strpos($alphabet, $encodedSrc[$i]);
                if ($v !== false) {
                    if ($bo >= strlen($result)) {
                        $result .= "\0";
                    }
                    $bon = 8 - ($io + $bits); // remaining unused bits (negative if byte boundaries are crossed)
                    if ($i < $l - 1 || $bon >= 0) {
                        if ($bon < 0) {
                            $result[$bo] = chr(ord($result[$bo]) | ($v >> ($bon * -1)));
                            $result .= chr(($v << 8 >> ($bon * -1)) & 0xFF);
                        } else {
                            $result[$bo] = chr(ord($result[$bo]) | ($v << $bon));
                        }
                    } else {
                        $result[$bo] = chr(ord($result[$bo]) | $v);
                    }
                } else {
                    throw new Exception('Invalid character in encoded uuid: ' . $encodedSrc[$i]);
                }
                $iOutBit += $bits;
            }
        }
        return $result;
    }

    public function setFromFormat($uuid, $format = null)
    {
        isset($format) || $format = self::guessUuidFormat($uuid);
    }

    /**
     * Returns the uuid in the requested format 
     * 
     * This function does not perform any validy checks
     * 
     * @param type $format
     * @return string
     * @throws Exception
     */
    public function format($format)
    {
        if (!isset($this->cachedUuidValueRepresentations[$format])) {
            $this->cachedUuidValueRepresentations[$format] = Util::formatFromBin($this->uuidValueBin, $format);
        }
        return $this->cachedUuidValueRepresentations[$format];
    }

    /**
     * Checks if Version 4 bits are set in uuid
     * 
     * @result int Uuid Version
     * @throws Exception
     */
    public function getVersion()
    {
        return ((ord($this->uuidValueBin[6]) & 0xF0) >> 4);
    }
    
    /**
     * Returns true, if this < the passed Uuid 
     * @param Uuid|string $anotherUuid
     * @return bool
     */
    public function lt($anotherUuid)
    {
        return (Util::compare($this, $anotherUuid) < 0);
    }
    
    /**
     * Returns true, if this <= the passed Uuid 
     * @param Uuid|string $anotherUuid
     * @return bool
     */
    public function lte($anotherUuid)
    {
        return (Util::compare($this, $anotherUuid) <= 0);
    }
    
    /**
     * Returns true, if this == the passed Uuid 
     * @param Uuid|string $anotherUuid
     * @return bool
     */
    public function eq($anotherUuid)
    {
        return (Util::compare($this, $anotherUuid) == 0);
    }
    
    /**
     * Returns true, if this >= the passed Uuid 
     * @param Uuid|string $anotherUuid
     * @return bool
     */
    public function gte($anotherUuid)
    {
        return (Util::compare($this, $anotherUuid) >= 0);
    }
    
    /**
     * Returns true, if this > the passed Uuid 
     * @param Uuid|string $anotherUuid
     * @return bool
     */
    public function gt($anotherUuid)
    {
        return (Util::compare($this, $anotherUuid) > 0);
    }
    
    public function validate($version = null)
    {
        return Util::validateUuid($this->uuidValueBin, self::FORMAT_BIN, $version);
    }

}
