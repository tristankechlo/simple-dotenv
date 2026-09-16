<?php

namespace TK\Dotenv;

class Dotenv
{

    public static function parse(string $content, bool $convert = false): array
    {
        return self::parseContent(self::splitContent($content), $convert);
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
            $raw_line = preg_replace('/^\xEF\xBB\xBF/', '', $raw_line);
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
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $key)) {
            $message = sprintf("Key can only contain characters [a-zA-Z0-9_] and can't start with a number: %s", $key);
            throw new ParseException($message, $key, $line_number);
        }
        return $key;
    }

    protected static function validateValue(string $value, int $line_number): mixed
    {
        $value = trim($value);

        // if is in quoted strings, remove quotes
        if (str_starts_with($value, "\"")) {
            $quote_position = self::findClosingQuote($value, '"');
            if ($quote_position === null) {
                throw new ParseException("Value '$value' started with a double quote that was not closed.", $value, $line_number);
            }
            $trailing = trim(substr($value, $quote_position + 1));
            if ($trailing !== '' && !str_starts_with($trailing, '#')) {
                throw new ParseException("Value '$value' has unexpected content after its closing double quote.", $value, $line_number);
            }
            $value = substr($value, 1, $quote_position - 1);
        } elseif (str_starts_with($value, "'")) {
            $quote_position = self::findClosingQuote($value, "'");
            if ($quote_position === null) {
                throw new ParseException("Value '$value' started with a single quote that was not closed.", $value, $line_number);
            }
            $trailing = trim(substr($value, $quote_position + 1));
            if ($trailing !== '' && !str_starts_with($trailing, '#')) {
                throw new ParseException("Value '$value' has unexpected content after its closing single quote.", $value, $line_number);
            }
            $value = substr($value, 1, $quote_position - 1);
        } else {
            $value = StringUtil::stripComments($value);
        }
        return $value;
    }

    private static function findClosingQuote(string $value, string $quote): ?int
    {
        // skip the opening quote and find the first quote that is not escaped.
        for ($position = 1, $length = strlen($value); $position < $length; $position++) {
            if ($value[$position] !== $quote) {
                continue;
            }

            $backslashes = 0;
            // a quote is escaped only when preceded by an odd number of backslashes.
            for ($index = $position - 1; $index >= 0 && $value[$index] === '\\'; $index--) {
                $backslashes++;
            }
            if ($backslashes % 2 === 0) {
                return $position;
            }
        }

        return null;
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
