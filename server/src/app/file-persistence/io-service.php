<?php

abstract class BaseIOService
{
    protected string $filePath;

    protected function read(): array
    {
        $fp = fopen($this->filePath, 'r');
        if (!$fp) {
            throw new RuntimeException("Cannot open file for reading: {$this->filePath}");
        }
        flock($fp, LOCK_SH);

        $contents = stream_get_contents($fp);
        $data = $contents ? json_decode($contents, true) : [];

        flock($fp, LOCK_UN);
        fclose($fp);

        return $data;
    }

    protected function write(array $data): void
    {
        $fp = fopen($this->filePath, 'c+');
        if (!$fp) {
            throw new RuntimeException("Cannot open file for writing: {$this->filePath}");
        }
        flock($fp, LOCK_EX);

        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, json_encode($data, JSON_UNESCAPED_UNICODE));

        flock($fp, LOCK_UN);
        fclose($fp);
    }

    public function clear(): void
    {
        $this->write([]);
    }
}

