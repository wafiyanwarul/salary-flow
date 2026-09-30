<?php

declare(strict_types=1);

/**
 * Tiny JSON-backed persistence layer.
 * This keeps the project dependency-free: no MySQL/SQLite extension is needed.
 */
class JsonStorage
{
    public function __construct(private string $path)
    {
        $dir = dirname($this->path);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        if (!file_exists($this->path)) {
            file_put_contents($this->path, "[]\n", LOCK_EX);
        }
    }

    public function all(): array
    {
        $handle = fopen($this->path, 'c+');
        if (!$handle) {
            throw new RuntimeException('Storage file cannot be opened.');
        }

        try {
            flock($handle, LOCK_SH);
            rewind($handle);
            $json = stream_get_contents($handle) ?: '[]';
            flock($handle, LOCK_UN);
        } finally {
            fclose($handle);
        }

        $data = json_decode($json, true);
        return is_array($data) ? $data : [];
    }

    public function save(array $data): void
    {
        $handle = fopen($this->path, 'c+');
        if (!$handle) {
            throw new RuntimeException('Storage file cannot be opened for writing.');
        }

        try {
            flock($handle, LOCK_EX);
            ftruncate($handle, 0);
            rewind($handle);
            fwrite($handle, json_encode(array_values($data), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL);
            fflush($handle);
            flock($handle, LOCK_UN);
        } finally {
            fclose($handle);
        }
    }
}
