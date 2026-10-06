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
        $normalized = strtolower($value);
        $isInt = ctype_digit($value);
        $isFloat = is_numeric($value);
        $isHex = preg_match('/^0x[0-9a-f]+$/i', $normalized) === 1;
        $isBin = preg_match('/^0b[01]+$/i', $normalized) === 1;
        $isOct = preg_match('/^0o[0-7]+$/i', $normalized) === 1;
        return $isInt || $isFloat || $isHex || $isBin || $isOct;
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
        $normalized = strtolower($value);

        // decode normal integer, optionally signed
        if (preg_match('/^[+-]?\d+$/', $value) === 1) {
            return intval($value);
        }
        if (is_numeric($value)) {
            return floatval($value);
        }
        // decode hexadecimal string e.g. '0xa0' => 160
        if (preg_match('/^0x[0-9a-f]+$/i', $normalized) === 1) {
            return hexdec($normalized);
        }
        // decode binary strings e.g. '0b110011' => 51
        if (preg_match('/^0b[01]+$/i', $normalized) === 1) {
            return bindec(substr($normalized, 2));
        }
        // decode octal strings e.g. '0o77' => 63
        if (preg_match('/^0o[0-7]+$/i', $normalized) === 1) {
            return octdec(substr($normalized, 2));
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
