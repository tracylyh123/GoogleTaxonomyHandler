<?php
namespace GoogleTaxonomyHandler;

interface InterfaceTree extends InterfaceNode
{
    function getId(): int;

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

    function prune();
}
