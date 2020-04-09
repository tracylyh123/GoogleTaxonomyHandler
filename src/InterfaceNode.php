<?php
namespace GoogleTaxonomyHandler;

interface InterfaceNode
{
    function getName(): string;

    function isSame(string $name, $caseInsensitive = false): bool;

    function resolve(): array;

    function isNil(): bool;
}
