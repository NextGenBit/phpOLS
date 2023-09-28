<?php

namespace phpOLS\Service;

use phpOLS\Model\olsMap;
use phpOLS\Interface\Map;

class Damos
{
    private string $filePath;

    /**
     * @var resource
     */
    private $filePointer;
    private array $damosMaps = [];

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
        $this->filePointer = fopen($this->filePath, "r");

        if (!$this->filePointer) {
            throw new \Exception('Failed to open damos file');
        }
    }

    public function load(): void
    {
        fgetcsv($this->filePointer, 0, ';'); // Read and discard headers

        while ($row = fgetcsv($this->filePointer, 0, ";", '"')) {
            $this->damosMaps[$row[1]] = new olsMap($row);
        }
        fclose($this->filePointer);
    }

    public function getMaps(): array
    {
        return $this->damosMaps;
    }

    public function getMapById(string $idMap): olsMap
    {

        if (array_key_exists($idMap, $this->damosMaps)) {
            return $this->damosMaps[$idMap];
        }

        throw new \Exception("ID map does not exist in Damos");
    }

    public function getFileSize(): int
    {
        return filesize($this->filePath);
    }
}
