# helicon-os/uuid Basic UUID functions for PHP

Copyright (c) 2015, Andreas Prucha, Helicon Software Development
All rights reserved.

## Overview

Provides basic functions to generate UUIDs on the client side. UUIDs can be generated in following formats:

- 16 byte Binary string (ATTENTION: The binary representation is always big-endian)
- 32 byte hex string 
- 36 byte hex string (The usual string representation of uuids)
- 38 byte hex string (String representation with braces)
- 45 byte hex string with urn:uuid-prefix

The library supports the following uuid sub-versions:

- V4 Random UUIDs
- "Sequential" V4 Random UIIDs (Time/Random-Based UUIDs inspired by COMB-UUIds, but slightly different)

The default version is V4Seq (Sequential random)

If more sophisticated functions are necessary, the famous [https://packagist.org/packages/ramsey/uuid] may be worth a try.

## Pitfalls and Limitations

- Byte Order. 

## Example

Generating a UUID is quite simple: 

```php
  $newUuid = \helicon\uuid\Util::generate(); // Generates a new in default format and version
```

## Installation