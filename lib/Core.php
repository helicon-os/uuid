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
 * Description of Core
 *
 * @author Andreas Prucha, Helicon Software Development
 */
class Core
{
    const NIL_BIN = "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0";

    /**
     * Alphabet used for ENC32
     */
    const ENC32_ALPHABET = '0123456789abcdefghijkmnpqrstuwxy';

    /**
     * Alphabet used for ENC64
     */
    const ENC64_ALPHABET = '-.0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

    /**
     * Generates a Nil uuid (all bits set to 0)
     * @see generateV4()
     */
    const GENERATOR_NIL = 0x00;

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
     * Encoded using 32 characters
     */
    const FORMAT_ENC32 = 0x060000;

    /**
     * Encoded using 64 characters
     */
    const FORMAT_ENC64 = 0x070000;

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
    
}
