<?php

namespace TK\Dotenv;

class Dotenv
{

    public static function parse(string $content, bool $convert = false): array
    {
        $raw_lines = array_filter(self::splitContent($content), 'strlen');
        if (empty($raw_lines)) { // .env file has no defined variables
            return [];
        }
        return self::parseContent($raw_lines, $convert);
    }

    protected static function splitContent(string $content): array
    {
        return explode("\n", str_replace(["\r\n", "\n\r", "\r"], "\n", $content));;
    }

    protected static function parseContent(array $raw_lines, bool $convert): array
    {
        $variables = [];
        $line_number = 0; // helper for better error messages

        foreach ($raw_lines as $raw_line) {
            $line_number++;
            $line = trim($raw_line);

            if (str_starts_with($line, '#') || !$line) { // ignore comments and empty lines
                continue;
            }

            [$key, $value] = self::parseLine($raw_line, $line_number, $convert);
            $variables[$key] = $value;
        }

        return $variables;
    }

    /** parses and validates a single line into a key value pair */
    protected static function parseLine(string $raw_line, int $line_number, bool $convert): array
    {
        [$raw_key, $raw_value] = self::splitToKeyValuePair($raw_line, $line_number);

        $key = self::validateKey($raw_key, $line_number);
        $value = self::validateValue($raw_value, $line_number);

        // when needed, try parsing strings to primitive types
        if ($convert == true) {
            $value = self::tryConvertValue($value);
        }

        return [$key, $value];
    }

    /** splits a single line into a key value pair */
    protected static function splitToKeyValuePair(string $raw_line, int $line_number)
    {
        $key_value = explode("=", $raw_line, 2);

        if (count($key_value) !== 2) {
            throw new ParseException("Each line must be of following format: KEY=value", $raw_line, $line_number);
        }

        return $key_value;
    }

    protected static function validateKey(string $key, int $line_number): string
    {
        $key = trim($key);
        if (!ctype_alnum(str_replace('_', '', $key)) || StringUtil::startsWithNumber($key)) {
            $message = sprintf("Key can only contain characters [a-zA-Z0-9_] and can't start with a number: %s", $key);
            throw new ParseException($message, $key, $line_number);
        }
        return $key;
    }

    protected static function validateValue(string $value, int $line_number): mixed
    {
        $value = trim($value);
        $value = StringUtil::stripComments($value);

        // if is in quoted strings, remove quotes
        if (str_starts_with($value, "\"")) {
            if (!str_ends_with($value, "\"")) {
                throw new ParseException("Value '$value' started with a double quote that was not closed.", $value, $line_number);
            }
            $value = substr($value, 1, strlen($value) - 2);
        } elseif (str_starts_with($value, "'")) {
            if (!str_ends_with($value, "'")) {
                throw new ParseException("Value '$value' started with a single quote that was not closed.", $value, $line_number);
            }
            $value = substr($value, 1, strlen($value) - 2);
        }
        return $value;
    }

    protected static function tryConvertValue(string $value): mixed
    {
        if (StringUtil::isBoolean($value)) {
            $value = StringUtil::getAsBoolean($value);
        } elseif (StringUtil::isNull($value)) {
            $value = null;
        } elseif (StringUtil::isNumber($value)) {
            $value = StringUtil::getAsNumber($value);
        }
        return $value;
    }
}
