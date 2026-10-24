<?php

declare(strict_types=1);

use Strukt\Annotation\Parser;

it('parses class, property, and method annotations', function () {
    $notes = (new Parser(new ReflectionClass(Annotated::class)))->getNotes();

    expect($notes['class_name'])->toBe(Annotated::class);
    expect($notes['class']['Name']['item'])->toBe('worker');
    expect($notes['class']['Role']['item'])->toBe('lead');
    expect($notes['properties']['name']['Type']['item'])->toBe('full');
    expect($notes['methods']['ping']['Route']['item'])->toBe('/ping');
    expect($notes['methods']['ping']['Method']['item'])->toBe('GET');
});

it('combines repeated annotations into one entry', function () {
    $notes = (new Parser(new ReflectionClass(Tagged::class)))->getNotes();

    expect($notes['class']['Tag']['items'])->toBe(['one', 'two']);
});

it('supports key value annotation syntax', function () {
    $notes = (new Parser(new ReflectionClass(KvHolder::class)))->getNotes();

    expect($notes['properties']['driver']['Config']['items'])->toBe(['driver' => 'memory', 'ttl' => '60']);
});

/**
 * Annotated worker used by the parser tests.
 *
 * @Name(worker)
 * @Role(lead)
 */
final class Annotated
{
    /** @Type(full) */
    private string $name = 'strukt';

    /**
     * @Route(/ping)
     * @Method(GET)
     */
    public function ping(): string
    {
        return 'pong';
    }
}

/**
 * @Tag(one)
 * @Tag(two)
 */
final class Tagged
{
}

abstract class KvHolder
{
    /**
     * @Config(driver=memory, ttl=60)
     */
    protected string $driver;
}
