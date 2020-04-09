<?php
namespace GoogleTaxonomyHandler;

interface InterfaceTree extends InterfaceNode
{
    function getId(): int;

    function isEqual(InterfaceTree $tree): bool;

    function setNext(InterfaceTree $tree): InterfaceTree;

    function getNext(): InterfaceTree;

    function setChild(InterfaceTree $tree): InterfaceTree;

    function getChild(): InterfaceTree;

    function setParent(InterfaceTree $tree): InterfaceTree;

    function getParent(): InterfaceTree;

    function setLast(InterfaceTree $tree): InterfaceTree;

    function getLast(): InterfaceTree;

    function hasChild(): bool;

    function hasNext(): bool;

    function hasParent(): bool;

    function hasLast(): bool;

    function find(int $id): InterfaceTree;

    function toArray(bool $isResolve = false): array;

    function getRoot(): InterfaceTree;

    function isRoot(): bool;

    function prune();

    function isPruned(): bool;

    function clearChild(): InterfaceTree;

    function clearNext(): InterfaceTree;

    function clearLast(): InterfaceTree;
}
