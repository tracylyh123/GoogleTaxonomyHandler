<?php
namespace GoogleTaxonomyHandler;

class TierTable implements \IteratorAggregate, \ArrayAccess
{
    private $tiers = [];

    public function append(TierLine $line): TierTable
    {
        $this->tiers[$line->getId()] = $line;
        return $this;
    }

    /**
     * @param int $offset
     * @param TierLine $value
     */
    public function offsetSet($offset, $value)
    {
        if (!$value instanceof TierLine) {
            throw new \InvalidArgumentException('invalid type of value');
        }
        if ($offset !== $value->getId()) {
            throw new \InvalidArgumentException('invalid offset value');
        }
        $this->tiers[$value->getId()] = $value;
    }

    public function offsetExists($offset): bool
    {
        return isset($this->tiers[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->tiers[$offset]);
    }

    /**
     * @param int $offset
     * @return TierLine|null
     */
    public function offsetGet($offset)
    {
        return $this->tiers[$offset] ?? null;
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->tiers);
    }

    public function match(string $string, string $delimiter): int
    {
        $splited = array_map('trim', explode($delimiter, $string));

        // start computing
        $base = 100;
        $resultId = 0;
        $maxScore = 0;
        // rule 1: the higher tier in Google Taxonomy has higher score
        // rule 2: each word can only be matched once
        // rule 3: if one tier can fully matched, it will get higer score than just matched a part
        foreach ($this->tiers as $id => $line) {
            $score = 0;
            $matched = [];
            /**
             * @var $node Node
             */
            foreach ($line as $offset => $node) {
                foreach ($splited as $index => $item) {
                    if (in_array($index, $matched)) {
                        continue;
                    }
                    $bias = 1 + $offset;
                    if ($node->isSame($item, true)) {
                        $matched[] = $index;
                        $score += ($base / $bias);
                        continue;
                    }
                    $len1 = strlen($item);
                    foreach ($node->resolve() as $gitem) {
                        $result = preg_match('/\b' . preg_quote($gitem, '/') . '\b/i', $item, $matches, PREG_OFFSET_CAPTURE);
                        if ($result < 1) {
                            continue;
                        }
                        $matched[] = $index;
                        $len2 = strlen($gitem);
                        // loss is the average value of two criterias
                        $loss = 1 - (($len2 + (end($matches)[1] + $len2)) / $len1) / 2;
                        $score += ($base / ($bias + $loss));
                        break;
                    }
                }
            }
            if ($score > $maxScore) {
                $resultId = $id;
                $maxScore = $score;
            }
        }
        return $resultId;
    }
}