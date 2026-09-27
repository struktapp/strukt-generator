<?php

namespace Strukt;

/**
 * Removes and renders PHPDoc-style comment delimiters.
 */
class DocBlocker
{
    /** @var list<string> Comment lines without delimiters. */
    private array $lines;

    /**
     * Creates a docblock renderer.
     *
     * @param string $block Plain annotation text or a complete docblock.
     */
    public function __construct(string $block)
    {
        $this->lines = explode("\n", trim(self::deBlock($block)));
    }

    /**
     * Removes opening, closing, and line-star comment tokens.
     *
     * @param string $block Complete or partial docblock.
     * @return string Plain block text.
     */
    public static function deBlock(string $block): string
    {
        $block = str_replace(["\r\n", "\r"], "\n", $block);
        $block = preg_replace('/^\s*\/\*\*/', '', $block) ?? $block;
        $block = preg_replace('/\*\/\s*$/', '', $block) ?? $block;

        $lines = [];
        foreach (explode("\n", $block) as $line) {
            $line = ltrim($line);
            if (str_starts_with($line, '*')) {
                $line = ltrim(substr($line, 1));
            }

            $lines[] = rtrim($line);
        }

        return implode("\n", $lines);
    }

    /**
     * Renders a normalized PHPDoc block.
     *
     * @return string Formatted docblock, or an empty string for blank input.
     */
    public function __toString(): string
    {
        $meaningful = array_filter($this->lines, static fn (string $line): bool => trim($line) !== '');
        if ($meaningful === []) {
            return '';
        }

        $lines = array_map(static fn (string $line): string => '* ' . $line, $this->lines);

        return "/**\n" . implode("\n", $lines) . "\n*/";
    }
}
