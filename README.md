# Simple Dotenv Parser

small parser for .env files

# normal parsing

```bash
# .env file
KEY1="This is a string"
KEY2=234
KEY3=4.5
KEY4=TRUE
KEY5=OFF
```

```php
# php file
use TK\Dotenv\Dotenv;

$content = file_get_contents("./.env");
$actual = Dotenv::parse($content);
```

```php
# output
$actual = [
    "KEY1" => "This is a string",
    "KEY2" => "234",
    "KEY3" => "4.5",
    "KEY4" => "TRUE",
    "KEY5" => "OFF",
];
```

# parsing with conversion

```bash
# .env file
KEY1="This is a string"
KEY2=234
KEY3=4.5
KEY4=TRUE
KEY5=OFF
```

```php
# php file
use TK\Dotenv\Dotenv;

$content = file_get_contents("./.env");
$actual = Dotenv::parse($content, true);
```

```php
# output
$actual = [
    "KEY1" => "This is a string",
    "KEY2" => 234,
    "KEY3" => 4.5,
    "KEY4" => true,
    "KEY5" => false,
];
```

## conversion

The parse can convert some values to php primitive types, otherwise all values are string.

### booleans

these values will be converted to booleans (case insensitive):  
- truthy_values = ['true', 'yes', 'on']
- falsy_values = ['false', 'no', 'off']

### numbers

these values will be converted to numbers:  
- integers (e.g. `3` or `23424`)
- floats (e.g. `5.76` or `43234.3453`)
- hexadezimal *only with prexif `0x`* (e.g. `0xa0` to `160`)
- binary *only with prexif `0b`* (e.g. `0b110011` to `51`)
- octal *only with prexif `0o`* (e.g. `0o77` to `63`)

### null

these values will be converted to `null` (case insensitive):
- null
- none
