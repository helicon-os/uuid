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
class Util extends Core
{


    /**
     * Autodetect the best available random generator
     */
    const RANDOM_AUTODETECT = 0;

    /**
     * Use MT_RAND to generate random data
     */
    const RANDOM_MT = 1;

    /**
     * Use openssl_random_pseudo_bytes to generate random data
     */
    const RANDOM_OPENSSL = 2;

    /**
     * @var integer UUID version used by default 
     */
    public static $configDefaultGenerator = self::GENERATOR_V4_ASC;

    /**
     * @var integer UUID format used by default 
     */
    public static $configDefaultFormat = self::FORMAT_HEX_SHORT;

    /**
     * @var Integer Specifies the used random generator.
     *              NOTE: If the value is self::RANDOM_AUTODETECT, the value is changed
     *              to the best available random generator at first call 
     */
    public static $configDefaultRandomGenerator = self::RANDOM_AUTODETECT;
    
    /**
     * Just for Testing
     * 
     * @var array 
     */
    public static $configTestOptions = null; 

    
    /**
     * @var array Last UUID V4Asc data (used to ensure ascending values)
     */
    protected static $lastV4AscUuid = self::NIL_BIN;
    
    /**
     * @var int Last V4Asc Subcounter
     */
    protected static $nextV4AscSc = 0;

    /**
     * Static utility function to compare two uuids
     * 
     * @param type $uuid1
     * @param type $uuid2
     * @return int  -1 if $uuid1 &lt; $uuid2, +1 if $uuid1 &gt; $uuid2, 0 if equal
     */
    public static function compare($uuid1, $uuid2)
    {
        $uuid1 = ($uuid1 instanceof Uuid) ? $uuid1->getAsBin() : self::convertToBin($uuid1);
        $uuid2 = ($uuid2 instanceof Uuid) ? $uuid2->getAsBin() : self::convertToBin($uuid2);
        return strcmp($uuid1, $uuid2);
    }

    /**
     * Static utility function to convert a binary UUID-Representation into a formatted string
     * 
     * This function does not perform any validy checks. The function assumes that
     * The binary representation is Big-Endian
     * 
     * @param type $uuid Binary string representation of Uuid
     * @param type $format
     * @return string
     * @throws Exception
     */
    public static function formatFromBin($uuid, $format)
    {
        $majorFormat = $format & 0xFF0000;
        switch ($majorFormat) {
            case self::FORMAT_BIN: {
                    return $uuid; // ===> RETURN
                }
            case self::FORMAT_ENC32:;
                {
                    return self::baseXEncode($uuid, self::ENC32_ALPHABET);
                }
            case self::FORMAT_ENC64:;
                {
                    return self::baseXEncode($uuid, self::ENC64_ALPHABET);
                }
            case self::FORMAT_HEX_SHORT:;
            case self::FORMAT_HEX_GROUPED:;
            case self::FORMAT_HEX_FULL:;
            case self::FORMAT_URN: {
                    $result = bin2hex($uuid);

                    if ($majorFormat != self::FORMAT_HEX_SHORT) {
                        $result = substr($result, 0, 8) . '-' .
                                substr($result, 8, 4) . '-' .
                                substr($result, 12, 4) . '-' .
                                substr($result, 16, 4) . '-' .
                                substr($result, 20, 12);
                    }

                    if ($format & self::FORMAT_HEX_OPT_UPPER) {
                        $result = strtoupper($result);
                    }

                    if ($majorFormat == self::FORMAT_URN) {
                        $result = 'urn:uuid:' . $result;
                    }

                    if ($majorFormat == self::FORMAT_HEX_FULL) {
                        $result = '{' . $result . '}';
                    }

                    return $result; // ===> RETURN
                }
            default: {
                    throw new Exception('Unknown format option ' . $format);
                }
        }
    }

    /**
     * Static utility function to detect the format of the given Uuid
     * 
     * @param type $uuid
     * @result bool|integer FORMAT_Xxxx or false
     */
    public static function guessUuidFormat($uuid)
    {
        if (strpos($uuid, 'urn:uuid:') === 0) {
            return self::FORMAT_URN;
        } else {
            switch (strlen($uuid)) {
                case 16: return self::FORMAT_BIN;
                case 22: return self::FORMAT_ENC64;
                case 26: return self::FORMAT_ENC32;
                case 32: return self::FORMAT_HEX_SHORT;
                case 36: return self::FORMAT_HEX_GROUPED;
                case 38: return self::FORMAT_HEX_FULL;
            }
        }
        return false;
    }

    /**
     * Static utility function to get the UUid Version
     * 
     * @param type $uuid
     * @param int $uuidFormat FORMAT_Xxxx or null for autodetection (null)
     * @result int Uuid Version
     * @throws Exception
     */
    public static function getUuidVersion($uuid, $uuidFormat = null)
    {
        $binUuid = self::convertToBin($uuid, $uuidFormat);
        return ((ord($binUuid[6]) & 0xF0) >> 4);
    }

    /**
     * Static utility function to tests if the passed uuid-string is basically valid
     * 
     * @param string $uuid Uuid
     * @param int|null $format Required format 
     * @param int|null $version Required version. If not specified, not version check is performed
     */
    public static function validateUuid($uuid, $format = null, $version = null)
    {
        $result = false;
        $uuidBin = static::convertToBin($uuid, $format, false);
        if (strlen($uuidBin) == 16) {
            $result = true;
            if ($version !== null) {
                $ver = ((ord($uuidBin[6]) & 0xF0) >> 4);
                return ($ver == $version || $ver == 0);
            }
        }
        return $result;
    }

    /**
     * Static utility function to convert a formatted (non-binary) UUID-Representation into a 16 byte binary string
     * 
     * @param string    $uuid   Uuid string representation
     * @param int|null  $format a Format mask or null (default) to autodetect
     * @exception Exception
     */
    public static function convertToBin($uuid, $format = null, $throwException = true)
    {
        $result = false;
        try {
            if ($uuid instanceof Uuid) {
                return $uuid->getAsBin();
            } else {
                isset($format) || $format = self::guessUuidFormat($uuid);
                if ($format !== false) {
                    $majorFormat = $format & 0xFF0000;
                    switch ($majorFormat) {
                        case self::FORMAT_BIN: {
                                $result = $uuid;
                                break;
                            }
                        case self::FORMAT_ENC32: {
                                if (strlen($uuid) == 26) {
                                    $result = self::baseXDecode($uuid, self::ENC32_ALPHABET);
                                }
                                break;
                            }
                        case self::FORMAT_ENC64: {
                                if (strlen($uuid) == 22) {
                                    $result = self::baseXDecode($uuid, self::ENC64_ALPHABET);
                                }
                                break;
                            }
                        case self::FORMAT_HEX_SHORT:;
                        case self::FORMAT_HEX_GROUPED:;
                        case self::FORMAT_HEX_FULL:
                        case self::FORMAT_URN: {
                                $result = hex2bin(preg_replace('/[^a-zA-Z0-9]/', '', str_replace('urn:uuid:', '', $uuid)));
                            }
                    }
                }
            }
        } catch (Exception $exc) {
            if ($throwException)
                throw $exc;
            else
                $result = false;
        }
        if (!$result && $throwException) {
            throw new Exception('Invalid Uuid ' . $uuid);
        }
        return $result;
    }
    
    /**
     * Returns the current microtime
     */
    protected static function getCurrentUuidV4AscMicrotime()
    {
        list($result['secFrac'], $result['sec']) = explode(' ', microtime(false));
        $result['sec'] = ((int)((float)$result['sec'] - 0x40000000)) & 0xFFFFFFFF;
        $result['secFrac'] = (float)$result['secFrac'];
        if (!empty(self::$configTestOptions) && 
             is_array(self::$configTestOptions) && 
             isset(self::$configTestOptions['badTimer']))
        {
            $result['sec'] = floor($result['sec'] / 10) * 10;
            $result['secFrac'] = 0;
        }
        return $result;
    }
    

    /**
     * Static utility function to generate a Uuid in the requested format
     * 
     * @param int|null $generator   Generator Identifier or null (default) to use the current default generator
     * @param int|null $format      Format Flags or null (default) to use the current default format
     * @return string
     * @throws Exception
     */
    public static function generate($generator = null, $format = null)
    {
        isset($generator) || $generator = self::$configDefaultGenerator;

        switch ($generator) {
            case self::GENERATOR_NIL: return static::generateNil($format);
            case self::GENERATOR_V4: return static::generateV4($format);
            case self::GENERATOR_V4_ASC: return static::generateV4Asc($format);
            default: throw new Exception('Unknown generator ' . $generator);
        }
    }

    /**
     * Static utility function to generate a Nil-Uuid
     * 
     * @param type $format
     * @return string
     */
    public static function generateNil($format = null)
    {
        if ($format == self::FORMAT_BIN) {
            return self::NIL_BIN;
        } else {
            return self::formatFromBin(self::NIL_BIN, $format);
        }
    }

    /**
     * Static utility function to generate a V4 Random Uuid
     * 
     * @param type $format Format Flags or null (default), to use the default format specified in {@link self::$configDefaultFormat} 
     * @return string
     */
    public static function generateV4($format = null)
    {
        isset($format) || $format = self::$configDefaultFormat;

        $b = self::randomBytes(16, self::$configDefaultRandomGenerator);

        $b[6] = chr(ord($b[6]) & 0x0F | 0x40);
        $b[8] = chr(ord($b[8]) & 0x3F | 0x80);

        if ($format === self::FORMAT_BIN) {
            return $b;   // Binary requested ===> RETURN 
        } else {
            return self::formatFromBin($b, $format);
        }
    }

    /**
     * Static utility function to generates an (usually) ascending Uuid
     * 
     * The returning guid is marked as random, but it is a combination of a timestamp and random data.
     * 
     * The primary purpose is to generate an ascending uuid in order to increase performance on some
     * DBS if used as primary key.
     * 
     * ATTENTION: This type of UUID is non-standard. UUIDs should not be considered crypto-save.
     * It is not guaranteed that the Uuid is always ascending. If the system time is changed
     * or multiple uuids are generated within a very short time (i.e. on systems with very bad
     * timer resolution), the uuids may get out of order in rare cases. 
     * 
     * The leading 32 bit contain the number of seconds since Unix-Time 0x40000000 as Big Endian, followed by
     * the the fraction of seconds. The Fraction is stored as as 1/65535 seconds.
     * 
     * If two Uuids are requested at the "same time" (meaning outside the resolution of microtime function),
     * The value of the 3rd group is increased by one instead of using a new random number if possible.
     *
     * @param type $format Format Flags or null (default), to use the default format specified in {@link self::$configDefaultFormat} 
     * @return string
     */
    public static function generateV4Asc($format = null)
    {
        (self::$nextV4AscSc) || self::$nextV4AscSc = mt_rand(0x4000, 0x4FFF); // Initialize subcounter at random value
        
        isset($format) || $format = self::$configDefaultFormat;
        
        $b = '';
        
        while (strlen($b) == 0)
        {
            
            $ts = static::getCurrentUuidV4AscMicrotime();

            $tsSec = (int)$ts['sec'];
            $tsFrac = ((int)floor($ts['secFrac'] * 0xFFFF)) & 0xFFFF;
            
            $b = (binary)pack('Nn', $tsSec, $tsFrac);
            
            if (strnatcmp($b, self::$lastV4AscUuid) <= 0)
            {
                // Time did not increase or even went back - reuse last time and increment sub-counter
                $b = (binary)substr(self::$lastV4AscUuid, 0, 6);
                if (self::$nextV4AscSc < 0x4FFF)
                {
                    self::$nextV4AscSc++;
                }
                else
                {
                    $parts = unpack('Np1/np2', $b);
                    $parts['p2'] = $parts['p2'] + 1;

                    $b = (binary)pack('Nn', 
                            $parts['p1']+($parts['p2'] >> 16), 
                            $parts['p2'] & 0xFFFF);
                    
                    self::$nextV4AscSc = mt_rand(0x4000, 0x4FFF);
                }
            }
            else
            {
                self::$nextV4AscSc = mt_rand(0x4000, 0x4FFF);
            }
            
            $b .= (binary)pack('n', self::$nextV4AscSc).
                    self::randomBytes(8, self::$configDefaultRandomGenerator);

            $b[6] = chr(ord($b[6]) & 0x0F | 0x40);
            $b[8] = chr(ord($b[8]) & 0x3F | 0x80);
        }

        // Save values
        self::$lastV4AscUuid = $b;

        if ($format === self::FORMAT_BIN) {
            return $b;   // Binary requested ===> RETURN 
        } else {
            return self::formatFromBin($b, $format);
        }
        
    }

    /**
     * Static utility function to generates random bytes
     * 
     * @param type $length
     * @param integer $randomGenerator 
     * 
     * @return binary|string
     */
    protected static function randomBytes($length, $randomGenerator = null)
    {
        $randomGenerator || $randomGenerator = self::$configDefaultRandomGenerator;

        $result = false;

        if (!$randomGenerator) {
            if (function_exists('openssl_random_pseudo_bytes')) {
                self::$configDefaultRandomGenerator = self::RANDOM_OPENSSL;
            } else {
                self::$configDefaultRandomGenerator = self::RANDOM_MT;
            }
            $randomGenerator = self::$configDefaultRandomGenerator;
        }

        switch ($randomGenerator) {
            case self::RANDOM_OPENSSL: {
                    $result = (binary)openssl_random_pseudo_bytes($length);
                    break;
                }
            default: {
                    $result = (binary)str_pad('', $length, "\x00");
                    for ($n = 0; $n < $length; $n++) {
                        $result[$n] = chr(mt_rand(0, 0xFF));
                    }
                }
        }
        return $result;
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

}
