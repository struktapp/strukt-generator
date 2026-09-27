<?php

namespace Strukt;

/**
 * Parses annotation lines such as @Route(/users) into an array.
 */
class BasicBlock
{
    /**
     * Parses parenthesized annotations from a comment block.
     *
     * Repeated annotation names are collected into a list. A single argument
     * is returned as a string; comma-separated arguments are returned as a
     * list of trimmed strings.
     *
     * @param string $doc Complete or partial docblock.
     * @return array<string, string|list<string>> Parsed annotations.
     */
    public static function parse(string $doc): array
    {
        $result = [];
        $plain = DocBlocker::deBlock($doc);

        foreach (preg_split('/\R/', $plain) ?: [] as $line) {
            if (!preg_match('/^\s*@([A-Za-z_][A-Za-z0-9_]*)\s*\((.*)\)\s*$/', trim($line), $matches)) {
                continue;
            }

            $name = $matches[1];
            $values = self::arguments($matches[2]);
            $value = count($values) === 1 ? $values[0] : $values;

            if (!array_key_exists($name, $result)) {
                $result[$name] = $value;
                continue;
            }

            $existing = is_array($result[$name]) ? $result[$name] : [$result[$name]];
            $incoming = is_array($value) ? $value : [$value];
            $result[$name] = array_values(array_merge($existing, $incoming));
        }

        return $result;
    }

    /**
     * Splits annotation arguments without breaking quoted or nested values.
     *
     * @param string $value Argument text.
     * @return list<string> Trimmed arguments.
     */
    private static function arguments(string $value): array
    {
        if (trim($value) === '') {
            return [''];
        }

        $parts = [];
        $buffer = '';
        $depth = 0;
        $quote = null;

        for ($index = 0, $length = strlen($value); $index < $length; $index++) {
            $character = $value[$index];
            if ($quote !== null) {
                $buffer .= $character;
                if ($character === $quote && ($index === 0 || $value[$index - 1] !== '\\')) {
                    $quote = null;
                }
                continue;
            }

            if ($character === '"' || $character === "'") {
                $quote = $character;
                $buffer .= $character;
                continue;
            }

            if ($character === '(' || $character === '[' || $character === '{') {
                $depth++;
            } elseif ($character === ')' || $character === ']' || $character === '}') {
                $depth--;
            }

            if ($character === ',' && $depth === 0) {
                $parts[] = trim($buffer);
                $buffer = '';
                continue;
            }

            $buffer .= $character;
        }

        $parts[] = trim($buffer);

        return $parts;
    }
}
