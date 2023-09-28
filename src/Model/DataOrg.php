<?php

namespace phpOLS\Model;

class DataOrg
{
    public const DATA_ORGS = [
        'eLoHi' => ['bit' => 16, 'packCode' => 'v*'],
        'eLoHiLoHi' => ['bit' => 32, 'packCode' => 'V*'],
        'eHiLoHiLo' => ['bit' => 32, 'packCode' => 'N*'],
        'eHiLo' => ['bit' => 16, 'packCode' => 'n*'],
        'eByte' => ['bit' => 8, 'packCode' => 'C*'],
        'eFloatHiLo' => ['bit' => 32, 'packCode' => 'N*'],
        'eFloatLoHi' => ['bit' => 32, 'packCode' => 'V*']
    ];

    private $dataOrg;

    public function __construct(string $dataOrg)
    {
        if (!isset(self::DATA_ORGS[$dataOrg])) {
            throw new \Exception("Invalid data org $dataOrg");
        }

        $this->dataOrg = $dataOrg;
    }

    public function getBitSize(): int
    {
        return self::DATA_ORGS[$this->dataOrg]['bit'];
    }

    public function getPackCode(): string
    {
        return self::DATA_ORGS[$this->dataOrg]['packCode'];
    }
}
