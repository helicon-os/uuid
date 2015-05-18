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
class Util
{

    const NULL_BIN = "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0";
    const NULL_HEX_SHORT = '00000000000000000000000000000000';
    const NULL_HEX_GROUPED = '00000000-0000-0000-0000-000000000000';
    const NULL_HEX_FULL = '{00000000-0000-0000-0000-000000000000}';

    /**
     * Generates a random V4 Uuid
     * @see generateV4()
     */
    const GENERATOR_V4 = 0x40;

    /**
     * Generates an incrementing Uuid based on time and random
     * @see generateV4Asc()
     */
    const GENERATOR_V4_ASC = 0x41;

    /**
     * Uuid in 16 byte binary format
     * 
     */
    const FORMAT_BIN = 0x010000;

    /**
     * Uuid in Hex format without dashes and brackets
     */
    const FORMAT_HEX_SHORT = 0x020000;

    /**
     * Uuid in Hex format with dashes, but without brackets
     */
    const FORMAT_HEX_GROUPED = 0x030000;

    /**
     * Uuid in Hex format with dashes and brackets
     */
    const FORMAT_HEX_FULL = 0x040000;

    /**
     * Uuid in Urn Format
     */
    const FORMAT_URN = 0x050000;

    /**
     * Generate hex uuid uppercase (Can be combined with any FORMAT_HEX_Xxxx value
     */
    const FORMAT_HEX_OPT_UPPER = 0x000001;
    
    /**
     * Uuid as array
     * 
     * Uuid as zero based array of parts (00000000-1111-2222-3333-4444444444)
     * 
     * [0]: Numeric value or binary string of part 0
     * [1]: Numeric value of part 1
     * [2]: Numeric value of part 2
     * [3]: Numeric value of part 3
     * [4]: binary string of part 4
     * 
     */
    const FORMAT_ARRAY = 0x0F0000;

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
     * @var integer UUID format used by default 
     */
    public static $defaultFormat = self::FORMAT_HEX_SHORT;

    /**
     * @var integer UUID version used by default 
     */
    public static $defaultVersion = self::GENERATOR_V4_ASC;

    /**
     * @var Integer Specifies the used random generator.
     *              NOTE: If the value is self::RANDOM_AUTODETECT, the value is changed
     *              to the best available random generator at first call 
     */
    public static $defaultRandomGenerator = self::RANDOM_AUTODETECT;
    
    /**
     * 
     */
    private static $__testBadTimer = false;

    /**
     * @var Double Last Timestamp used for V4Asc-Uuids. 
     */
    protected static $lastV4AscTs = 0;

    /**
     * @var int     Internal counter for V4 Asc Uuids
     */
    protected static $v4AscSubCounter = 0;
    
    /**
     * Returns the result of microtime(true)
     */
    public static function microtimeAsFloat()
    {
        if (self::$__testBadTimer)
        {
            return round(microtime(true)); // For testing purpose only!
        }
        else
        {
            return microtime(true);
        }
    }

    /**
     * Generates random bytes
     * 
     * @param type $length
     * @param integer $randomGenerator 
     * 
     * @return string
     */
    public static function randomBytes($length, $randomGenerator = null)
    {
        $randomGenerator || $randomGenerator = self::$defaultRandomGenerator;

        $result = false;

        if (!$randomGenerator) {
            if (function_exists('openssl_random_pseudo_bytes')) {
                self::$defaultRandomGenerator = self::RANDOM_OPENSSL;
            } else {
                self::$defaultRandomGenerator = self::RANDOM_MT;
            }
            $randomGenerator = self::$defaultRandomGenerator;
        }

        switch ($randomGenerator) {
            case self::RANDOM_OPENSSL: {
                    $result = openssl_random_pseudo_bytes($length);
                    break;
                }
            default: {
                    $result = str_pad('', $length, "\x00");
                    for ($n = 0; $n < $length; $n++) {
                        $result[$n] = chr(mt_rand(0, 0xFF));
                    }
                }
        }
        return $result;
    }
    
    /**
     * Returns a NULL-UUID
     * 
     * Returns a Null-UUID in the requested format
     * 
     * @param null|int $format null (default) for default format or a Format-Mask
     * @return string
     * @throws Exception
     */
    public function nullUuid($format = null)
    {
        isset($format) || $format = self::$defaultFormat;
        $majorFormat = $format & 0xFF0000;
        switch ($majorFormat) {
            case self::FORMAT_BIN: return self::NULL_BIN;
            case self::FORMAT_HEX_GROUPED: return self::NULL_HEX_GROUPED;
            case self::FORMAT_HEX_SHORT: return self::NULL_HEX_SHORT;
            case self::FORMAT_HEX_FULL: return self::NULL_HEX_FULL;
            case self::FORMAT_URN: return self::NULL_URN;
            default: {
                    throw new Exception('Unknown format option ' . $format);
                }
        }
    }

    /**
     * 
     * @param type $format
     * @param type $version
     * @return type
     * @throws Exception
     */
    public static function generate($format = null, $version = null)
    {
        isset($version) || $version = self::$defaultVersion;
        switch ($version) {
            case self::GENERATOR_V4:
                return self::generateV4($format);
            case self::GENERATOR_V4_ASC:
                return self::generateV4Asc($format);
            default:
                throw new Exception('Unsupported or invalid Uuid format requested');
        }
    }

    /**
     * Converts a binary representation of an uuid id into the requested format
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
     * Generates a V4 Style Uuid
     * 
     * @param type $format Format Flags or null (default), to use the default format specified in {@link self::$defaultFormat} 
     * @return string
     */
    public static function generateV4($format = null)
    {
        isset($format) || $format = self::$defaultFormat;

        $b = self::randomBytes(16, self::$defaultRandomGenerator);

        $b[6] = chr(ord($b[6]) & 0x0F | 0x40);
        $b[8] = chr(ord($b[8]) & 0x3F | 0x80);

        if ($format === self::FORMAT_BIN) {
            return $b;   // Binary requested ===> RETURN 
        } else {
            return self::formatFromBin($b, $format);
        }
    }

    /**
     * Generates an (usually) ascending Uuid
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
     * @param type $format Format Flags or null (default), to use the default format specified in {@link self::$defaultFormat} 
     * @return string
     */
    public static function generateV4Asc($format = null)
    {
        isset($format) || $format = self::$defaultFormat;

        $ts = microtime(true) - 0x40000000; // 
        
        if ($ts > self::$lastV4AscTs || !self::$v4AscSubCounter)
        {
            self::$v4AscSubCounter = mt_rand(0, 0xFFFF);
        }
        else
        {
            self::$v4AscSubCounter++;
        }
        self::$lastV4AscTs = $ts;
        
        $tsSec = floor(self::$lastV4AscTs);
        $tsFrac = (self::$lastV4AscTs - $tsSec);

        $b = pack('Nnn', (int) $tsSec, 
                (int) (floor($tsFrac * 0xFFFF) & 0xFFFF), 
                self::$v4AscSubCounter & 0xFFFF) .
                self::randomBytes(8, self::$defaultRandomGenerator);

        $b[6] = chr(ord($b[6]) & 0x0F | 0x40);
        $b[8] = chr(ord($b[8]) & 0x3F | 0x80);

        if ($format === self::FORMAT_BIN) {
            return $b;   // Binary requested ===> RETURN 
        } else {
            return self::formatFromBin($b, $format);
        }
    }

    /**
     * Checks if Version 4 bits are set in uuid
     * 
     * @param type $uuid
     * @throws Exception
     */
    public static function getUuidVersion($uuid)
    {
        $l = strlen($uuid);
        if ($l < 16) {
            throw new Exception('Cannot be a valid Uuid (Too Short)');
        } else {
            if ($l > 16) {
                $uuid = hex2bin(preg_replace('/[^a-zA-Z0-9]/', '', str_replace('urn:uuid:', '', $uuid)));
            }
        }
        return ((ord($uuid[6]) & 0xF0) >> 4);
    }

    /**
     * Tests if the passed uuid is basically valid
     * 
     * @param string $uuid Uuid
     * @param int|null $format Required format 
     * @param int|null $version Required version. If not specified, not version check is performed
     */
    public static function validateUuid($uuid, $format = null, $version = null)
    {
        $result = false;
        if ($format === null) {
            $result = (self::validateUuid($uuid, self::FORMAT_BIN) ||
                    self::validateUuid($uuid, self::FORMAT_HEX_SHORT) ||
                    self::validateUuid($uuid, self::FORMAT_HEX_GROUPED) ||
                    self::validateUuid($uuid, self::FORMAT_HEX_FULL) ||
                    self::validateUuid($uuid, self::FORMAT_URN));
        } else {
            $majorFormat = $format & 0xFF0000;
            switch ($majorFormat) {
                case self::FORMAT_BIN: {
                        $result = strlen($uuid) == 16;
                        break;
                    }
                case self::FORMAT_HEX_SHORT: {
                        $result = preg_match('/^[a-f0-9]{32}$/i', $uuid);
                        break;
                    }
                case self::FORMAT_HEX_GROUPED: {
                        $result = preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i', $uuid);
                        break;
                    }
                case self::FORMAT_HEX_FULL: {
                        $result = preg_match('/^\{[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}\}$/i', $uuid);
                        break;
                    }
                case self::FORMAT_URN: {
                        $result = preg_match('/^urn:uuid:([a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12})$/i', $uuid);
                        break;
                    }
                default: {
                        throw new Exception('Unknown format option ' . $format);
                    }
            }
        }
        if ($result && isset($version)) {
            $result = (self::getUuidVersion($uuid) == $version);
        }
        return (bool) $result;
    }

}
