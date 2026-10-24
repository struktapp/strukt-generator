<?php

declare(strict_types=1);

namespace Strukt\Annotation;

/**
 * Reads single line annotations from class, property, and method docblocks.
 *
 * The parser recognises `@Name`, `@Name(item)`, `@Name(a, b, c)`, and
 * `@Name(key=value, key=value)` forms. It returns a nested array keyed by the
 * reflection member that carries the annotation, which the injectables use to
 * describe routes, forms, and facets.
 *
 * ```php
 * /** @Name(valid) *\/
 * final class Validator {}
 *
 * (new Parser(new \ReflectionClass(Validator::class)))->getNotes();
 * // ['class_name' => Validator::class, 'class' => ['Name' => ['name' => 'Name', 'item' => 'valid']]]
 * ```
 */
final class Parser
{
    /** @var array<string, mixed> Parsed annotations. */
    private array $notes = [];

    public function __construct(private \ReflectionClass $reflection)
    {
        $this->readClassNotes();
        $this->readPropertyNotes();
        $this->readMethodNotes();
    }

    /**
     * Returns every parsed annotation.
     *
     * The result carries a `class_name` key plus optional `class`,
     * `properties`, and `methods` keys holding the per member annotations.
     *
     * @return array<string, mixed> Parsed annotations.
     */
    public function getNotes(): array
    {
        return ['class_name' => $this->reflection->getName()] + $this->notes;
    }

    /**
     * Reads annotations from the class docblock.
     *
     * @return void
     */
    private function readClassNotes(): void
    {
        $doc = $this->reflection->getDocComment();
        if (is_string($doc) && $doc !== '') {
            $this->notes['class'] = $this->resolve($doc);
        }
    }

    /**
     * Reads annotations from property docblocks.
     *
     * @return void
     */
    private function readPropertyNotes(): void
    {
        foreach ($this->reflection->getProperties() as $property) {
            $doc = $property->getDocComment();
            if (is_string($doc) && $doc !== '') {
                $this->notes['properties'][$property->getName()] = $this->resolve($doc);
            }
        }
    }

    /**
     * Reads annotations from method docblocks.
     *
     * @return void
     */
    private function readMethodNotes(): void
    {
        foreach ($this->reflection->getMethods() as $method) {
            $doc = $method->getDocComment();
            if (is_string($doc) && $doc !== '') {
                $this->notes['methods'][$method->getName()] = $this->resolve($doc);
            }
        }
    }

    /**
     * Splits a docblock into trimmed annotation lines.
     *
     * @param string $docBlock Raw docblock.
     * @return list<string> Annotation lines without a leading `@`.
     */
    private function sanitize(string $docBlock): array
    {
        $doc = str_replace(['/**', '*/', '*'], '', $docBlock);

        return array_values(array_map(
            static fn (string $value): string => trim((string) preg_replace('/^@/', '', trim($value))),
            explode("\n", trim($doc)),
        ));
    }

    /**
     * Parses the annotations carried by one docblock.
     *
     * @param string $docBlock Raw docblock.
     * @return array<string, array<string, mixed>>|null Annotations keyed by name.
     */
    private function resolve(string $docBlock): ?array
    {
        $notes = null;

        foreach ($this->sanitize($docBlock) as $line) {
            if ($line === '') {
                continue;
            }

            preg_match('/\w+(?=\((.*)\))/', $line, $matches);
            $name = $matches[0] ?? '';
            if ($name === '') {
                continue;
            }

            $item = $matches[1] ?? '';
            $note = ['name' => $name, 'item' => $item];

            if (str_contains($item, ',')) {
                $note['items'] = array_map('trim', explode(',', $item));
            }

            if (str_contains($item, '=')) {
                $parts = $note['items'] ?? [$item];
                unset($note['items']);
                foreach ($parts as $part) {
                    [$key, $value] = array_pad(explode('=', $part, 2), 2, '');
                    $note['items'][trim($key)] = trim($value);
                }
            }

            if (!empty($note['items'])) {
                unset($note['item']);
            }

            $notes = $this->merge($notes, $note);
        }

        return $notes;
    }

    /**
     * Merges a note into the accumulated map, combining repeated names.
     *
     * @param array<string, array<string, mixed>>|null $notes Accumulated notes.
     * @param array<string, mixed> $note Note to merge.
     * @return array<string, array<string, mixed>> Updated notes.
     */
    private function merge(?array $notes, array $note): array
    {
        $notes ??= [];
        $key = (string) $note['name'];

        if (!array_key_exists($key, $notes)) {
            $notes[$key] = $note;

            return $notes;
        }

        $incoming = $note['items'] ?? $note['item'];
        $incoming = is_array($incoming) ? $incoming : [$incoming];
        $existing = $notes[$key]['items'] ?? $notes[$key]['item'];
        $existing = is_array($existing) ? $existing : [$existing];

        $notes[$key] = ['name' => $key, 'items' => array_merge($existing, $incoming)];

        return $notes;
    }
}
