<?php

namespace Strukt;

/**
 * Renders lightweight text templates with values, loops, and conditions.
 */
class Templator
{
    /**
     * Renders all supported template directives.
     *
     * Direct values use {{name}}, loops use {{begin:items}} ... {{end:items}},
     * and conditions use {if enabled} ... {/if}.
     *
     * @param string $template Template text.
     * @param array<string, mixed> $data Values used by the directives.
     * @return string Rendered template.
     */
    public static function create(string $template, array $data): string
    {
        $template = static::loop($template, $data);
        $template = static::element($template, $data);

        return static::condition($template, $data);
    }

    /**
     * Removes leading, trailing, and repeated blank lines.
     *
     * @param string|array<int|string, string> $text Text or text values.
     * @return string|array<int|string, string> Trimmed text with its input shape.
     */
    public static function trimBlanks(string|array $text): string|array
    {
        if (is_array($text)) {
            return array_map(
                static fn (string $value): string => self::trimBlanks($value),
                $text,
            );
        }

        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $text = preg_replace("/\n[ \t]*\n+/", "\n", $text) ?? $text;

        return trim($text);
    }

    /**
     * Replaces direct value placeholders.
     *
     * @param string $template Template text.
     * @param array<string, mixed> $data Values used by placeholders.
     * @return string Template with values inserted.
     */
    public static function element(string $template, array $data): string
    {
        $result = preg_replace_callback(
            '/\{\{([A-Za-z_][A-Za-z0-9_]*)\}\}/',
            static function (array $matches) use ($data): string {
                $value = $data[$matches[1]] ?? '';

                return self::stringify($value);
            },
            $template,
        );

        return $result ?? $template;
    }

    /**
     * Expands list loops.
     *
     * Each list item must be an array. Placeholders inside the loop are
     * rendered against the current item.
     *
     * @param string $template Template text.
     * @param array<string, mixed> $data Values used by loops.
     * @return string Template with loop sections expanded.
     */
    public static function loop(string $template, array $data): string
    {
        $pattern = '/\{\{begin:([A-Za-z_][A-Za-z0-9_]*)\}\}(.*?)\{\{end:\1\}\}/s';
        $result = preg_replace_callback(
            $pattern,
            static function (array $matches) use ($data): string {
                $items = $data[$matches[1]] ?? [];
                if (!is_array($items)) {
                    return '';
                }

                $rendered = [];
                foreach ($items as $item) {
                    if (!is_array($item)) {
                        continue;
                    }

                    $rendered[] = self::element($matches[2], $item);
                }

                return self::trimBlanks(implode('', $rendered));
            },
            $template,
        );

        return $result ?? $template;
    }

    /**
     * Expands truthy conditional sections.
     *
     * @param string $template Template text.
     * @param array<string, mixed> $data Values used by conditions.
     * @return string Template with conditional sections resolved.
     */
    public static function condition(string $template, array $data): string
    {
        $result = preg_replace_callback(
            '/\{if\s+([A-Za-z_][A-Za-z0-9_]*)\}(.*?)\{\/if\}/s',
            static function (array $matches) use ($data): string {
                return !empty($data[$matches[1]]) ? trim($matches[2]) : '';
            },
            $template,
        );

        return trim($result ?? $template);
    }

    /**
     * Expands optional console color sections.
     *
     * If a compatible color() helper is not loaded, the section content is
     * returned without color transformation.
     *
     * @param string $template Template text.
     * @return string Console-ready template.
     */
    public static function console(string $template): string
    {
        if (!class_exists(\Strukt\Console\Color::class) || !function_exists('color')) {
            return $template;
        }

        $result = preg_replace_callback(
            '/\{color\s+([A-Za-z0-9_-]+)\}(.*?)\{\/color\}/s',
            static fn (array $matches): string => (string) color($matches[1], $matches[2]),
            $template,
        );

        return $result ?? $template;
    }

    /**
     * Converts a template value into safe text.
     *
     * @param mixed $value Value to render.
     * @return string Text representation.
     */
    private static function stringify(mixed $value): string
    {
        if ($value === null || $value === false) {
            return '';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        if (is_object($value) && method_exists($value, '__toString')) {
            return (string) $value;
        }

        return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '';
    }
}
