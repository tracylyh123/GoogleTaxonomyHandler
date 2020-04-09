<?php
namespace GoogleTaxonomyHandler;

class Table implements \IteratorAggregate, \ArrayAccess
{
    private $lines = [];

    public function append(Line $line): Table
    {
        $this->offsetSet($line->getId(), $line);
        return $this;
    }

    /**
     * @param int $offset
     * @param Line $line
     */
    public function offsetSet($offset, $line)
    {
        if (!$line instanceof Line) {
            throw new \InvalidArgumentException('invalid type of value');
        }
        if ($offset !== $line->getId()) {
            throw new \InvalidArgumentException('invalid offset value');
        }
        $this->lines[$line->getId()] = $line;
    }

    public function offsetExists($offset): bool
    {
        return isset($this->lines[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->lines[$offset]);
    }

    /**
     * @param int $offset
     * @return Line|null
     */
    public function offsetGet($offset)
    {
        if (!isset($this->lines[$offset])) {
            throw new \InvalidArgumentException("offset {$offset} was not found");
        }
        return $this->lines[$offset];
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->lines);
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
        foreach ($this->lines as $id => $line) {
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
