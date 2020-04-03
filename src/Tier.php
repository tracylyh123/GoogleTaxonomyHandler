<?php
namespace GoogleTaxonomyHandler;

class Tier
{
    private $id;

    private $name;

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
}
