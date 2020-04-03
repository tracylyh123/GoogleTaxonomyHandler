<?php
namespace GoogleTaxonomyHandler;

class Tier
{
    private $id;

    private $name;

    private $next;

    private $child;

    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isEqual(int $id): bool
    {
        return $id === $this->id;
    }

    public function isSame(string $name): bool
    {
        return $name === $this->name;
    }

    public function setNext(Tier $tier): Tier
    {
        $this->next = $tier;
        return $this;
    }

    public function getNext(): Tier
    {
        return $this->next;
    }

    public function setChild(Tier $tier): Tier
    {
        $this->child = $tier;
        return $this;
    }

    public function getChild(): Tier
    {
        return $this->child;
    }

    public function hasChild(): bool
    {
        return !empty($this->child);
    }

    public function hasNext(): bool
    {
        return !empty($this->next);
    }

    public function resolve(): array
    {
        if (false !== strpos($this->name, ', ')) {
            return explode(', ', str_replace(' & ', ', ', $this->name));
        } elseif (false !== strpos($this->name, ' & ') || false !== strpos($this->name, ' and ')) {
            $result = [];
            $this->combine($this->parse($this->name), $result);
            return $result;
        }
        return [$this->name];
    }

    private function parse(string $name): array
    {
        $items = explode(' ', $name);
        $parsed = [];
        foreach ($items as $index => $item) {
            if (isset($items[$index + 1]) && in_array($items[$index + 1], ['&', 'and'])) {
                $group = [$items[$index]];
            } elseif (isset($items[$index - 1]) && in_array($items[$index - 1], ['&', 'and'])) {
                $group[] = $items[$index];
                $parsed[$index] = $group;
            } elseif (!in_array($item, ['&', 'and'])) {
                $parsed[$index] = $item;
            }
        }
        return $parsed;
    }

    private function combine(array $parsed, array &$result)
    {
        foreach ($parsed as $key => $item) {
            if (is_array($item)) {
                $current = $key;
                break;
            }
        }
        if (isset($current)) {
            list($first, $second) = $parsed[$current];
            $parsed[$current] = $first;
            $this->combine($parsed, $result);
            $parsed[$current] = $second;
            $this->combine($parsed, $result);
        } else {
            $result[] = implode(' ', $parsed);
        }
    }

    public function find(int $id): ?Tier
    {
        return $this->_find($id, $this);
    }

    private function _find(int $id, Tier $tier): ?Tier
    {
        if ($tier->isEqual($id)) {
            return $tier;
        }

        if ($tier->hasNext()) {
            $next = $this->_find($id, $tier->getNext());
            if ($next) {
                return $next;
            }
        }

        if ($tier->hasChild()) {
            $child = $this->_find($id, $tier->getChild());
            if ($child) {
                return $child;
            }
        }

        return null;
    }

    public function toArray(bool $isResolve = false): array
    {
        return $this->_toArray($this, $isResolve);
    }

    private function _toArray(Tier $tier, bool $isResolve): array
    {
        $results = [];
        ADD:
        $item = [
            'id' => $tier->getId(),
            'name' => $tier->getName(),
        ];
        if ($isResolve) {
            $item['resolved'] = $tier->resolve();
        }
        if ($tier->hasChild()) {
            $item['child'] = $this->_toArray($tier->getChild(), $isResolve);
        }
        $results[] = $item;
        if ($tier->hasNext()) {
            $tier = $tier->getNext();
            goto ADD;
        }
        return $results;
    }
}
