<?php

namespace TK\Dotenv;

class StringUtil
{

    private static array $truthy_values = ['true', 'yes', 'on'];
    private static array $falsy_values = ['false', 'no', 'off'];
    private static array $bool_values = ['true', 'yes', 'on', 'false', 'no', 'off']; // all truthy and falsy values
    private static array $null_values = ['null', 'none'];

    // ################################################################################
    // EVALUATION FUNCTIONS
    // ################################################################################

    public static function isBoolean(string $value): bool
    {
        $value = strtolower($value);
        return in_array($value, self::$bool_values);
    }

    public static function isNull(string $value): bool
    {
        $value = strtolower($value);
        return in_array($value, self::$null_values);
    }

    public static function isNumber(string $value): bool
    {
        $isInt = ctype_digit($value);
        $isFloat = is_numeric($value);
        $isHex = str_starts_with($value, "0x") && ctype_xdigit(str_replace("0x", "", $value));
        $isBin = str_starts_with($value, "0b") && ctype_xdigit(str_replace("0b", "", $value));
        $isOct = str_starts_with($value, "0o") && ctype_xdigit(str_replace("0o", "", $value));
        return $isInt or $isFloat or $isHex or $isBin or $isOct;
    }

    public static function startsWithNumber(string $line): bool
    {
        return is_numeric(substr($line, 0, 1));
    }

    // ################################################################################
    // FUNCTIONS TO MODIFY A STRING
    // ################################################################################

    public static function getAsBoolean(string $value): bool
    {
        $value = strtolower($value);
        if (in_array($value, self::$truthy_values)) {
            return true;
        }
        if (in_array($value, self::$falsy_values)) {
            return false;
        }
        return throw new \InvalidArgumentException("Value '$value' is not a known boolean representation.");
    }

    public static function getAsNumber(string $value): int|float
    {
        // decode normal integer
        if (ctype_digit($value)) {
            return intval($value);
        }
        if (is_numeric($value)) {
            return floatval($value);
        }
        // decode hexadecimal string e.g. '0xa0' => 160
        if (str_starts_with($value, "0x") && ctype_xdigit(str_replace("0x", "", $value))) {
            return hexdec($value);
        }
        // decode binary strings e.g. '0b110011' => 51
        if (str_starts_with($value, "0b") && preg_match("/^[10]+$/", str_replace("0b", "", $value))) {
            return bindec($value);
        }
        // decode otal strings e.g. '0o77' => 63
        if (str_starts_with($value, "0o") && preg_match("/^[0-7]+$/", str_replace("0o", "", $value))) {
            return octdec($value);
        }
        throw new \InvalidArgumentException("Value '$value' is not a parsable number.");
    }

    /** if string contains a specfic key-char, remove everything right of the needle */
    public static function stripComments(string $value, string $needle = '#'): string
    {
        $value = explode($needle, $value, 2);
        return trim($value[0]);
    }
}
