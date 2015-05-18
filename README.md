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

```sql
CHAR(32) BINARY ASCII NOT NULL
```

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