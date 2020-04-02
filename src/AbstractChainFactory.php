<?php
namespace GoogleTaxonomyHandler;

abstract class AbstractChainFactory
{
    private $file;

    private $loaded = false;

    protected $raw = [];

    public function __construct(string $file)
    {
        $this->file = $file;
    }

    public function load(): AbstractChainFactory
    {
        if (!file_exists($this->file)) {
            throw new \InvalidArgumentException("file: {$this->file} was not found");
        }
        $this->raw = file($this->file);
        $this->loaded = true;
        return $this;
    }

    public function isLoaded(): bool
    {
        return $this->loaded;
    }

    public function create(): Tier
    {
        if (!$this->isLoaded()) {
            throw new \LogicException("no data loaded");
        }
        return $this->build();
    }

    abstract protected function build(): Tier;
}
