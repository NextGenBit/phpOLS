<?php

namespace phpOLS\Service;

use phpOLS\Model\MapValue;

class BinaryFileHandler
{
    private string $filePath;
    private $filePointer;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
        $this->filePointer = fopen($this->filePath, "r+b");

        if (!$this->filePointer) {
            throw new \Exception('Failed to open binary file.');
        }
    }

    public function readAt(?int $offset, int $length): ?string
    {
        // If offset is null, return null
        if ($offset === null) {
            return null;
        }
        fseek($this->filePointer, $offset);
        return fread($this->filePointer, $length);
    }

    public function write(array $data): void
    {
        foreach ($data as $value) {
            if ($value instanceof MapValue) {

                fseek($this->filePointer, $value->getOffset());
                fwrite($this->filePointer, $value->getWriteValue());
            }
        }
    }

    public function getFileSize(): int
    {
        return filesize($this->filePath);
    }

    public function __destruct()
    {
        fclose($this->filePointer);
    }
}
