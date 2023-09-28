<?php

namespace phpOLS\Model;

use phpOLS\Interface\AxisMap;
use phpOLS\Traits\Build;
use phpOLS\Interface\DataPresentation;

class Axis implements AxisMap
{
    use Build;

    private const AXIS = true;

    private ?string $mapAddress = null;
    private string $mapName;
    private bool $signedInt;
    private int $precision;
    private string $dataSrc;
    private float $valueFactor;
    private int $size;
    private int $radix = 10;
    private DataOrg $dataOrg;

    public function __construct($mapName, $mapAddress, $dataOrg, $signedInt, $precision, $dataSrc, $valueFactor, $size)
    {
        $this->mapName = $mapName;

        $this->mapAddress = (empty($mapAddress))  ? null : $mapAddress;
        $this->dataOrg = new DataOrg($dataOrg);
        $this->signedInt = $signedInt;
        $this->precision = $precision;
        $this->dataSrc = $dataSrc;
        $this->valueFactor = $valueFactor;
        $this->size = $size;
    }


    public function getDataSrc(): string
    {
        return $this->dataSrc;
    }

    public function getValueFactor(): float
    {
        return $this->valueFactor;
    }

    public function getTotalSize(): int
    {
        return $this->size;
    }

    public function getReadSize(): int
    {
        return $this->size * $this->getBitSize() / 8;
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

    public function getMapAddress(): ?string
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
}
