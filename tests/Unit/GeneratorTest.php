<?php

use Strukt\BasicBlock;
use Strukt\ClassBuilder;
use Strukt\DocBlocker;
use Strukt\Templator;

test('renders values, loops, and conditions', function (): void {
    $template = "<h1>{{title}}</h1>\n{{begin:people}}\n<b>{{name}}</b> {{surname}}<br />\n{{end:people}}\n{if enabled}\n{{footer}}\n{/if}";

    expect(Templator::create($template, [
        'title' => 'The Title',
        'people' => [
            ['name' => 'Steve', 'surname' => 'Johnson'],
            ['name' => 'James', 'surname' => 'Johnson'],
        ],
        'enabled' => true,
        'footer' => 'Foot',
    ]))->toBe("<h1>The Title</h1>\n<b>Steve</b> Johnson<br />\n<b>James</b> Johnson<br />\nFoot");
});

test('handles missing and false values without warnings', function (): void {
    expect(Templator::element('{{present}} {{missing}} {{disabled}}', [
        'present' => 'yes',
        'disabled' => false,
    ]))->toBe('yes  ')
        ->and(Templator::condition('{if enabled}yes{/if}', ['enabled' => false]))->toBe('');
});

test('deblocks and renders PHPDoc text', function (): void {
    $docblock = "\t/**\n\t* @Route(/)\n\t* @Method(GET, POST)\n\t*/";
    $plain = "@Route(/)\n@Method(GET, POST)";

    expect(trim(DocBlocker::deBlock($docblock)))->toBe($plain)
        ->and((string) new DocBlocker($plain))->toBe("/**\n* @Route(/)\n* @Method(GET, POST)\n*/")
        ->and(deblock($docblock))->toBe($plain);
});

test('parses repeated and comma-separated annotations', function (): void {
    $annotations = BasicBlock::parse(<<<'DOC'
/**
 * @Route(/roles)
 * @Method(GET, POST)
 * @Middleware(AuthToken)
 * @Middleware(GVerify)
 */
DOC);

    expect($annotations)->toBe([
        'Route' => '/roles',
        'Method' => ['GET', 'POST'],
        'Middleware' => ['AuthToken', 'GVerify'],
    ]);
});

test('builds classes without duplicating methods on repeated rendering', function (): void {
    $builder = new ClassBuilder([
        'ns' => 'Payroll\\AuthModule\\Controller',
        'class_name' => 'Role',
        'extends' => 'AbstractController',
        'methods' => [
            [
                'name' => 'find',
                'params' => 'int $id = 1',
                'body' => "\treturn \$id;",
                'block' => [
                    'param' => 'int $id',
                    'return' => 'Role',
                ],
            ],
        ],
    ]);
    $builder->addInit(['string $name']);

    $first = (string) $builder;
    $second = (string) $builder;

    expect($first)->toBe($second)
        ->and($first)->toContain(
            'namespace Payroll\\AuthModule\\Controller;',
            'class Role extends AbstractController{',
            'public function __construct(string $name){',
            '@param(int $id)',
            'public function find(int $id = 1){',
        );
});

test('registers all generator helpers', function (): void {
    expect(helper('generator'))->toContain('template', 'huecli', 'deblock')
        ->and(huecli('plain'))->toBe('plain');
});
