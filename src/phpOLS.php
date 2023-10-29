<?php

namespace phpOLS;

use phpOLS\Service\BinaryFileHandler;
use phpOLS\Service\Damos;
use phpOLS\Model\olsMap;

class phpOLS
{

    private BinaryFileHandler $binaryFileHandler;
    private Damos $damos;
    private olsMap $mapTable;

    function __construct(string $binaryFile, string $damosFile)
    {
        $this->binaryFileHandler = new BinaryFileHandler($binaryFile);
        $this->damos = new Damos($damosFile);
        $this->damos->load();
    }

    public function createContextForMapById(string $idMap): self
    {
        $this->mapTable = $this->damos->getMapById($idMap);
        return $this;
    }

    public function getDamos()
    {
        return $this->damos;
    }

    public function info(): olsMap
    {
        return $this->mapTable;
    }

    public function build(): self
    {
        $this->mapTable->build($this->binaryFileHandler->readAt($this->mapTable->getMapAddress(), $this->mapTable->getReadSize()));
        $this->mapTable->getAxis('x')?->build($this->binaryFileHandler->readAt($this->mapTable->getAxis('x')->getMapAddress(), $this->mapTable->getAxis('x')->getReadSize()));
        $this->mapTable->getAxis('y')?->build($this->binaryFileHandler->readAt($this->mapTable->getAxis('y')->getMapAddress(), $this->mapTable->getAxis('y')->getReadSize()));

        return $this;
    }

    public function write($values): self
    {
        $this->binaryFileHandler->write($values['table']);
        return $this;
    }

    public function get(): array
    {
        $output = [];

        $output['table'] = $this->mapTable->getFetchedValues();
        $output['x'] = $this->mapTable->getAxis('x')?->getFetchedValues() ?? [];
        $output['y'] = $this->mapTable->getAxis('y')?->getFetchedValues() ?? [];

        return $output;
    }
}
