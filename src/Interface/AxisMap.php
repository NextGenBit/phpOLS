<?php

namespace phpOLS\Interface;

interface AxisMap
{
    public function getMapAddress(): ?string;
    public function getBitSize(): int;
    public function getPackCode(): string;
    public function isSignedInt(): bool;
    public function getPrecision(): int;
    public function getValueFactor(): float;
    public function getTotalSize(): int;
    public function getReadSize(): int;
    public function getMapName(): string;
    public function getRadix(): int;
}
