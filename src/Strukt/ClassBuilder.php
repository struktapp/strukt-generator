<?php

namespace Strukt;

/**
 * Builds a PHP class from a namespace, class name, and method definitions.
 */
class ClassBuilder
{
    /** @var array<string, mixed> Class configuration. */
    private array $params;

    /** @var list<string> Rendered method blocks. */
    private array $methods = [];

    /**
     * Creates a class builder.
     *
     * Required parameters are ns and class_name. Optional parameters are
     * extends, implements, and methods.
     *
     * @param array<string, mixed> $params Class configuration.
     *
     * @throws \InvalidArgumentException When required parameters are absent.
     */
    public function __construct(array $params)
    {
        foreach (['ns', 'class_name'] as $required) {
            if (!isset($params[$required]) || !is_string($params[$required]) || trim($params[$required]) === '') {
                throw new \InvalidArgumentException(sprintf('ClassBuilder requires a non-empty [%s].', $required));
            }
        }

        $this->params = $params;
    }

    /**
     * Adds a constructor method.
     *
     * @param array<int|string, string>|null $params Constructor parameter text.
     * @return static This builder.
     */
    public function addInit(?array $params = null): static
    {
        $parameterText = $this->parameters($params ?? []);
        $this->methods[] = $this->method('__construct', $parameterText, "\t//");

        return $this;
    }

    /**
     * Renders the configured class.
     *
     * @return string Generated PHP class source.
     */
    public function __toString(): string
    {
        $methods = $this->methods;
        foreach (($this->params['methods'] ?? []) as $method) {
            if (!is_array($method) || !isset($method['name']) || !is_string($method['name'])) {
                continue;
            }

            $block = is_array($method['block'] ?? null) ? $this->docBlock($method['block']) : '';
            $methods[] = $this->method(
                $method['name'],
                $this->parameters($method['params'] ?? ''),
                (string) ($method['body'] ?? "\t//"),
                $block,
            );
        }

        $body = $methods === [] ? "\t//" : implode("\n\n", $methods);
        $extends = $this->classPart('extends');
        $implements = $this->classPart('implements');

        return sprintf(
            "namespace %s;\n\nclass %s%s%s{\n\n%s\n}",
            trim((string) $this->params['ns']),
            trim((string) $this->params['class_name']),
            $extends,
            $implements,
            $body,
        );
    }

    /**
     * Formats an extends or implements class clause.
     *
     * @param string $key Configuration key.
     * @return string Rendered clause.
     */
    private function classPart(string $key): string
    {
        $value = $this->params[$key] ?? null;
        if (is_array($value)) {
            $value = implode(', ', $value);
        }

        return is_string($value) && trim($value) !== ''
            ? sprintf(' %s %s', $key, trim($value))
            : '';
    }

    /**
     * Formats a method block and indents it for a class body.
     *
     * @param string $name Method name.
     * @param string $params Method parameter text.
     * @param string $body Method body.
     * @param string $docBlock Optional rendered docblock.
     * @return string Indented method source.
     */
    private function method(string $name, string $params, string $body, string $docBlock = ''): string
    {
        $method = sprintf(
            "%spublic function %s(%s){\n\n%s\n}",
            $docBlock === '' ? '' : $docBlock . "\n",
            $name,
            $params,
            $body,
        );

        return $this->indent($method);
    }

    /**
     * Renders a method docblock from annotation values.
     *
     * @param array<string, string|array<int, string>> $block Docblock tags.
     * @return string Rendered docblock.
     */
    private function docBlock(array $block): string
    {
        $lines = [];
        foreach ($block as $name => $value) {
            if (is_array($value)) {
                $value = implode(',', $value);
            }

            $lines[] = sprintf('@%s(%s)', $name, $value);
        }

        return (string) new DocBlocker(implode("\n", $lines));
    }

    /**
     * Converts parameter configuration into source text.
     *
     * @param string|array<int|string, string> $params Parameters.
     * @return string Parameter list.
     */
    private function parameters(string|array $params): string
    {
        if (is_string($params)) {
            return trim($params);
        }

        return implode(', ', array_values(array_map('strval', $params)));
    }

    /**
     * Indents every line in a generated block.
     *
     * @param string $value Multiline source.
     * @return string Indented source.
     */
    private function indent(string $value): string
    {
        return implode(
            "\n",
            array_map(static fn (string $line): string => "\t" . $line, explode("\n", $value)),
        );
    }
}
