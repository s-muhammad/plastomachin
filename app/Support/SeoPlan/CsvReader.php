<?php

namespace App\Support\SeoPlan;

final class CsvReader
{
    /**
     * Read a UTF-8 CSV file and key every row by its (lowercase) header.
     *
     * @return array{headers: list<string>, rows: list<array<string, string|null>>}
     */
    public static function read(string $path): array
    {
        $handle = fopen($path, 'rb');

        if ($handle === false) {
            return ['headers' => [], 'rows' => []];
        }

        $headers = null;
        $rows = [];

        while (($line = fgetcsv($handle, null, ',', '"', '')) !== false) {
            if ($line === [null]) {
                continue;
            }

            if ($headers === null) {
                $candidate = array_map(self::cleanHeader(...), $line);

                if (implode('', $candidate) === '') {
                    continue;
                }

                $headers = $candidate;

                continue;
            }

            $row = [];

            foreach ($headers as $index => $header) {
                if ($header === '') {
                    continue;
                }

                $value = $line[$index] ?? null;
                $row[$header] = $value === null ? null : trim((string) $value);
            }

            if (array_filter($row, fn (?string $value) => $value !== null && $value !== '') === []) {
                continue;
            }

            $rows[] = $row;
        }

        fclose($handle);

        return ['headers' => $headers ?? [], 'rows' => $rows];
    }

    private static function cleanHeader(?string $header): string
    {
        $header = preg_replace('/^\xEF\xBB\xBF/', '', (string) $header);

        return strtolower(trim((string) $header));
    }
}
