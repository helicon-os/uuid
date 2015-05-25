# helicon-os/uuid Basic UUID functions for PHP

Copyright (c) 2015, Andreas Prucha, Helicon Software Development
All rights reserved.

## Overview

Provides basic functions to generate UUIDs on the client side. UUIDs can be generated in following formats:

- 16 byte Binary string: Big-Endian binary representation of an uuid
- 32 byte hex string: 32 chacter hex representation without dashes. The default in this library.
- 36 byte hex string: The usual string representation of uuids.
- 38 byte hex string: String representation with braces.
- 45 byte hex string: with urn:uuid-prefix
- 22 byte Base64-like string (Experimental)
- 26 byte Base32-like string (Experimental)

The library supports the following uuid sub-versions:

- V4 Random UUIDs
- "Sequential" V4 Random UIIDs (Time/Random-Based UUIDs inspired by COMB-UUIds, but slightly different)

The default version is V4Asc (Ascending Random)

If more sophisticated functions are necessary, the famous [https://packagist.org/packages/ramsey/uuid] may be worth a try.

## Pitfalls and Limitations

###Byte Order

This library assumes Network Byte Order (Big Endian) as described in the RFC 4122 in the 
binary string representation. Unfortunately Microsoft uses Little Endian, thus conversion to the 
string representation may return another result there. Additionally, On systems using little endian,
the V4Asc algorithm does hot have the desired effect.

###Format

32 char hex strings are used by default which can be stored in CHAR(32) fields. Please make sure
that the field uses an 8bit characterset (ASCII) and a collation with no overhead (binary or similar). 

Format (Constant)         | Db Type / charset / collation       | Comment
------------------------- | ----------------------------------- | ----------------------
Util::FORMAT_BIN          | BINARY(16) BINARY / BINARY          | Binary 
Util::FORMAT_HEX_SHORT    | CHAR(32) ASCII / ASCII or binary    | Just Hex
Util::FORMAT_HEX_GROUPED  | CHAR(36) ASCII / ASCII or binary    | Hex + Formatting characters
Util::FORMAT_HEX_FULL     | CHAR(38) ASCII / ASCII or binary    | Hex + Formatting characters
Util::FORMAT_URN          | CHAR(45) ASCII / ASCII or binary    | Hex with urn:uuid:-prefix
Util::FORMAT_ENC32        | CHAR(26) ASCII / ASCII or binary    | Experimental Base32-like sortable encoding (numbers+lc-chars)
Util::FORMAT_ENC64        | CHAR(26) ASCII / ASCII or binary    | Experimental Base64-like sortable encoding (numbers+alpha)

Note about chracter set and collation:

Choosing the right db type, charset and collation can have a huge impact on performance.
It's not reccommended to use UTF-8 for a UUID-Field because it's a waste of space and unicode-comparisons
are usually slower. Usually the best choice is to use a binary or ASCII-collation and
ASCII charset (except for FORMAT_BIN, which has to be BINARY)

- All encodings are sortable and big-endian. 
- All encodings except FORMAT_BIN are Url-safe
- For all encodings except FORMAT_BIN it's reccommended to use an ASCII-Character set and a binary collation
  in order to increase performance
- For all encodings *except* FORMAT_BIN and FORMAT_ENC64 it is *possible* to use a case insensitive collation 
  because the string representation are never mixed case, but it should not be done.

Another option is to use the 16byte "binary" format.

Advantage of the 36byte hex format:
- No Endian-Conflict
- Readable using the usual representation
- Easy to use in hand-written queries
Disadvantage of 36byte hex format:
- Uses more space
- may be slightly slower

## Example

Generating a UUID is quite simple: 

```php
  $newUuid = \helicon\uuid\Util::generate(); // Generates a new in default format and version
```

## Installation