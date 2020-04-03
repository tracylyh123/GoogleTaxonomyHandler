<?php
namespace GoogleTaxonomyHandler;

class TierTable implements \IteratorAggregate
{
    private $tiers = [];

    public function append(TierLine $line): TierTable
    {
        $this->tiers[] = $line;
        return $this;
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
             * @var $tier Tier
             */
            foreach ($line as $offset => $tier) {
                foreach ($splited as $index => $item) {
                    if (in_array($index, $matched)) {
                        continue;
                    }
                    $bias = 1 + $offset;
                    if ($tier->isSame($item, true)) {
                        $matched[] = $index;
                        $score += ($base / $bias);
                        continue;
                    }
                    $len1 = strlen($item);
                    foreach ($tier->resolve() as $gitem) {
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
