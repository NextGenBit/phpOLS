<?php

namespace phpOLS\Model;

class MapValue
{
    private $precision;
    private $packCode;
    private $rawValue;
    private $realValue;
    private $factor;
    private $offset;

    public function __construct($precision, $packCode, $rawValue, $factor, $offset)
    {
        $this->precision = $precision;
        $this->packCode = $packCode;
        $this->rawValue = $rawValue;
        $this->realValue = $this->rawValue / $factor;
        $this->factor = $factor;
        $this->offset = $offset;
    }

    public function getPrecision()
    {
        return $this->precision;
    }

    public function getPackCode()
    {
        return $this->packCode;
    }

    public function getRawValue()
    {
        return $this->rawValue;
    }

    public function getRealValue()
    {
        return $this->realValue;
    }

    public function getFactor()
    {
        return $this->factor;
    }

    public function getOffset()
    {
        return $this->offset;
    }

    public function getWriteValue()
    {
        return pack($this->packCode, $this->rawValue);
    }

    public function __toString()
    {
        return sprintf("%.{$this->precision}f", $this->realValue);
    }
}
