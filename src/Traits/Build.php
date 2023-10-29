<?php

namespace phpOLS\Traits;

use phpOLS\Model\olsMap;
use phpOLS\Model\Axis;
use phpOLS\Model\MapValue;

trait Build
{
    private $fetchedValues = [];

    public function getFetchedValues(): array
    {
        return $this->fetchedValues;
    }

    public function build(?string $mapBytes): self
    {
        if ($mapBytes === null) {
            if ($this instanceof Axis) {
                $mapValues = range(0, $this->getTotalSize() - 1);
            }
        } else {

            $radix = $this->getRadix();
            $isSignedInt = $this->isSignedInt();
            $factor = $this->getValueFactor();
            $mapAdress = $this->getMapAddress();
            $precision = $this->getPrecision();
            $bit = $this->getBitSize();
            $packcode = $this->getPackCode();

            if ($radix == 10) {
                $map = array_values(unpack($packcode, $mapBytes));

                $mapValues = [];

                foreach ($map as $k => $value) {
                    if ($isSignedInt && $value >= pow(2, $bit - 1)) {
                        $value -= pow(2, $bit);
                    }

                    $mapValues[$k] = new MapValue($precision, $packcode, $value, $factor, $mapAdress + ($k * $bit / 8));
                }
            } else {
                $mapValues = str_split(unpack('H*', $mapBytes)[1], $bit / 4);
            }


            if ($this instanceof olsMap) {
                #$mapValues = array_chunk($mapValues, $this->getColumns());
            }
        }

        $this->fetchedValues = $mapValues;
        return $this;
    }
}
