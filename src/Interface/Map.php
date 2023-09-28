<?php

namespace phpOLS\Interface;

use phpOLS\Interface\AxisMap;

interface Map extends AxisMap
{
    public function getAxis(string $axis): ?AxisMap;
    public function getRows(): int;
    public function getColumns(): int;
}