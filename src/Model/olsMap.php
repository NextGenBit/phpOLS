<?php

namespace phpOLS\Model;

use phpOLS\Interface\Map;
use phpOLS\Interface\AxisMap;
use phpOLS\Traits\Build;
use phpOLS\Interface\DataPresentation;

class olsMap implements Map
{
    use Build;

    private const SIZE = 57; //Accepted size of map

    private string $mapName;
    private string $mapId;
    private ?string $mapAddress = null;
    private bool $signedInt = false;
    private int $precision = 0;
    private int $radix;
    private float $valueFactor;
    private int $rows = 0;
    private int $columns = 0;

    private DataOrg $dataOrg;
    private ?Axis $axisX = null;
    private ?Axis $axisY = null;

    function __construct($map)
    {
        if (count($map) !== self::SIZE) {
            throw new \Exception("Expected map size of " . self::SIZE . ", but got " . count($map));
        }

        $this->dataOrg = new DataOrg($map[6]);
        $this->mapName = $map[0];
        $this->mapId = $map[1];
        $this->mapAddress = (empty($map[24])) ? null : $this->getAddressDec($map[24]);
        $this->signedInt = ($map[8] == 1);
        $this->precision = ($map[17] < 0) ? 0 : $map[17];
        $this->radix = $map[15];
        $this->rows = $map[14];
        $this->columns = $map[13];
        $this->valueFactor = (float) $map[22];

        if ($this->columns > 1)
            $this->axisX = new Axis($map[25], $this->getAddressDec($map[37]), $map[38], $map[33], $map[34], $map[35], $map[28], $this->columns);

        if ($this->rows > 1)
            $this->axisY = new Axis($map[41], $this->getAddressDec($map[53]), $map[54], $map[49], $map[50], $map[51], $map[44], $this->rows);
    }

    public function getAxis(string $axis): ?AxisMap
    {
        return ($axis == 'x') ? $this->axisX : $this->axisY;
    }

    public function getValueFactor(): float
    {
        return $this->valueFactor;
    }

    public function getTotalSize(): int
    {
        if ($this->columns > 0) {
            return $this->rows * $this->columns;
        }

        return $this->rows;
    }

    public function getReadSize(): int
    {
        return $this->getTotalSize() * $this->getBitSize() / 8;
    }

    public function getRows(): int
    {
        return $this->rows;
    }

    public function getColumns(): int
    {
        return $this->columns;
    }

    public function getMapName(): string
    {
        return $this->mapName;
    }

    public function getRadix(): int
    {
        return $this->radix;
    }

    public function getPrecision(): int
    {
        return $this->precision;
    }

    public function isSignedInt(): bool
    {
        return $this->signedInt;
    }

    public function getMapId(): string
    {
        return $this->mapId;
    }

    public function getMapAddress(): string
    {
        return $this->mapAddress;
    }

    public function getBitSize(): int
    {
        return $this->dataOrg->getBitSize();
    }

    public function getPackCode(): string
    {
        return $this->dataOrg->getPackCode();
    }

    private function getAddressDec($address): int
    {
        if (substr($address, 0, 1) == '$') {
            $address = substr($address, 1);
        } else {
            $address = $address;
        }

        if (ctype_xdigit($address)) {
            $address = hexdec($address);
        }

        return $address;
    }
}
