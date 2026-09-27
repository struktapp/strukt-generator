# Strukt Generator

[![Latest Stable Version](https://img.shields.io/packagist/v/strukt/generator.svg)](https://packagist.org/packages/strukt/generator)
[![License](https://img.shields.io/packagist/l/strukt/generator.svg)](https://packagist.org/packages/strukt/generator)

Typed PHP class generation, lightweight text templating, and PHPDoc
annotation utilities for PHP 8.2 and later.

This package is the Strukt 3 rewrite of 'strukt2/generator'. It keeps the
template, huecli, and deblock helpers while making parsing deterministic,
handling missing values safely, adding runtime validation, and avoiding
stateful rendering bugs.

## Installation

```bash
composer require strukt/generator
```

The package depends on:

- strukt/base for helper registration;
- strukt/commons for the string and application utility layer.

Optional console color rendering is available when a compatible
strukt/console package provides Strukt\Console\Color and the color() helper.

## Quick start

Render a template:

```php
<?php

$html = template(
    '<h1>{{title}}</h1>',
    ['title' => 'Dashboard'],
);
```

Parse annotations:

```php
$annotations = Strukt\BasicBlock::parse(<<<'DOC'
/**
 * @Route(/users)
 * @Method(GET, POST)
 */
DOC);

// [
//     'Route' => '/users',
//     'Method' => ['GET', 'POST'],
// ]
```

Build a class:

```php
$source = (string) new Strukt\ClassBuilder([
    'ns' => 'App\\Controller',
    'class_name' => 'UserController',
    'extends' => 'BaseController',
    'methods' => [
        [
            'name' => 'index',
            'params' => '',
            'body' => "\treturn [];",
        ],
    ],
]);
```

## Global helpers

The autoloaded src/helpers.php registers each helper conditionally and adds
its name to the Strukt helper registry.

| Helper | Parameters | Return type | Description |
| --- | --- | --- | --- |
| template | string $template, array $data | string | Renders values, loops, and conditions. |
| huecli | string $template | string | Resolves optional console color sections. |
| deblock | string $input | string | Removes PHPDoc comment delimiters. |

The helpers do not overwrite functions already supplied by an application.

## Template syntax

### Values

Use a word-shaped key between double braces:

```text
Hello {{name}}!
```

Missing values render as an empty string. Null and false values also render as
empty text. Scalar values are converted to strings; arrays and objects are
JSON encoded unless an object exposes `__toString()`.

### Lists

Use a matching begin and end key:

```text
{{begin:people}}
<b>{{name}}</b> {{surname}}
{{end:people}}
```

The value at people must be an array of arrays. Each item is rendered against
its own values. Empty lines around the expanded list are normalized.

### Conditions

Use a truthy data key:

```text
{if enabled}
This section is visible.
{/if}
```

The complete section is trimmed when enabled and removed when the value is
missing or false.

### Rendering order

Templator::create() applies directives in this order:

1. list loops;
2. direct values;
3. conditions.

This ordering allows placeholders inside list items to be resolved before
conditional sections are finalized.

## Templator API

Strukt\Templator contains static operations for the supported template syntax.

### create

```php
public static function create(
    string $template,
    array $data,
): string
```

Renders loops, direct values, and conditions in the documented order.

### trimBlanks

```php
public static function trimBlanks(
    string|array $text,
): string|array
```

Normalizes line endings, removes surrounding whitespace, and collapses
repeated blank lines. Arrays are mapped recursively at the first level while
retaining their keys.

### element

```php
public static function element(
    string $template,
    array $data,
): string
```

Replaces direct placeholders shaped like {{name}}.

### loop

```php
public static function loop(
    string $template,
    array $data,
): string
```

Expands matching {{begin:name}} and {{end:name}} blocks. Non-array or missing
list values produce no rendered items.

### condition

```php
public static function condition(
    string $template,
    array $data,
): string
```

Resolves {if name} and {/if} blocks using PHP truthiness.

### console

```php
public static function console(string $template): string
```

Resolves {color name} and {/color} sections when both a console color class
and color() helper are available. Without them, the template is returned
unchanged.

## DocBlocker

Strukt\DocBlocker converts between plain annotation text and PHPDoc comment
blocks.

### Constructor

```php
public function __construct(string $block)
```

Accepts a complete docblock or plain lines. Opening and closing delimiters,
leading stars, and surrounding whitespace are normalized.

### deBlock

```php
public static function deBlock(string $block): string
```

Returns the plain contents of a PHPDoc block without its comment delimiters.
Blank lines inside the block are retained.

### `__toString`

```php
public function __toString(): string
```

Returns a normalized PHPDoc block. Blank input returns an empty string.

Example:

```php
$plain = "@Route(/)\n@Method(GET, POST)";
$docblock = (string) new Strukt\DocBlocker($plain);

// /**
// * @Route(/)
// * @Method(GET, POST)
// */
```

## BasicBlock

Strukt\BasicBlock parses annotation lines with parenthesized values.

### parse

```php
public static function parse(string $doc): array
```

Returns an array keyed by annotation name. A single argument returns as a
string, comma-separated arguments return as a list, and repeated annotation
names are merged into one list.

```php
$result = BasicBlock::parse(<<<'DOC'
/**
 * @Route(/roles)
 * @Method(GET, POST)
 * @Middleware(AuthToken)
 * @Middleware(GVerify)
 */
DOC);

// [
//     'Route' => '/roles',
//     'Method' => ['GET', 'POST'],
//     'Middleware' => ['AuthToken', 'GVerify'],
// ]
```

Arguments are split while respecting quoted text and nested brackets, so
values containing commas can be represented safely.

## ClassBuilder

Strukt\ClassBuilder renders a PHP class declaration with optional inheritance,
interfaces, constructors, methods, and method annotations.

### Constructor

```php
public function __construct(array $params)
```

The params array requires:

| Key | Type | Description |
| --- | --- | --- |
| ns | string | Namespace declaration. |
| class_name | string | Class name. |

Optional keys are:

| Key | Type | Description |
| --- | --- | --- |
| extends | string | Parent class name. |
| implements | string or array | Interface name or comma-separated interface names. |
| methods | array | Method definitions. |

Missing or empty ns and class_name values throw InvalidArgumentException.

### addInit

```php
public function addInit(
    ?array $params = null,
): static
```

Adds a constructor with the supplied parameter source strings. It returns the
builder for fluent use.

```php
$builder->addInit(['string $name', 'int $id']);
```

### Method definitions

Each methods entry may contain:

```php
[
    'name' => 'find',
    'params' => 'int $id = 1',
    'body' => "\treturn $id;",
    'block' => [
        'param' => 'int $id',
        'return' => 'User',
    ],
]
```

The block map becomes PHPDoc tags. Array tag values are joined with commas.
The body defaults to a comment placeholder when omitted.

### `__toString`

```php
public function __toString(): string
```

Returns generated PHP source. Rendering is repeatable: calling it more than
once does not duplicate configured methods.

Example output shape:

```php
namespace App\Controller;

class UserController extends BaseController{

    /**
     * @return array
     */
    public function index(){

        return [];
    }
}
```

## Fixtures

The fixtures directory contains a small Payroll application and Contract
abstract classes for generator experiments. It is included through Composer's
development autoload configuration and is not part of the runtime package.

## Performance and safety

- Template rendering uses one regex pass per directive type.
- Missing placeholders do not emit PHP notices.
- Annotation parsing avoids repeated string-wrapper allocation.
- ClassBuilder does not mutate its configured method list while rendering.
- Generated blocks are indented in one pass.
- Invalid class-builder configuration fails early with a clear exception.

## Development

Install development dependencies and run the Pest suite:

```bash
composer update
composer test
```

Validate package metadata:

```bash
composer validate --no-check-publish
```

The test suite covers templates, loops, conditions, safe missing values,
docblock round-tripping, annotation parsing, class generation, repeatable
rendering, and helper registration.

## License

Strukt Generator is released under the MIT license.
