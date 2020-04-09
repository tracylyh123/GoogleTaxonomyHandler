<?php
namespace GoogleTaxonomyHandler;

class NilNode implements InterfaceNode
{
    public function getName(): string
    {
        throw new \LogicException('cannot operate this node');
    }

    public function isSame(string $name, $caseInsensitive = false): bool
    {
        throw new \LogicException('cannot operate this node');
    }

    public function resolve(): array
    {
        throw new \LogicException('cannot operate this node');
    }

    public function isNil(): bool
    {
        return true;
    }
}
