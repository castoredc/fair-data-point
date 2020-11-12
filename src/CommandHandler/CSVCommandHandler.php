<?php
declare(strict_types=1);

namespace App\CommandHandler;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use function count;
use function fclose;
use function feof;
use function fopen;
use function fputcsv;
use function fread;
use function rewind;

abstract class CSVCommandHandler implements MessageHandlerInterface
{
    /**
     * @param string[]     $columns
     * @param array<mixed> $data
     */
    protected function generateCsv(array $columns, array $data, string $delimiter = ',', string $enclosure = '"'): string
    {
        $handle = fopen('php://temp', 'r+');

        if ($handle === false) {
            return '';
        }

        $contents = null;

        if (count($data) === 0) {
            return '';
        }

        fputcsv($handle, $columns, $delimiter, $enclosure);

        foreach ($data as $line) {
            $row = [];

            foreach ($columns as $column) {
                $row[] = $line[$column] ?? null;
            }

            fputcsv($handle, $row, $delimiter, $enclosure);
        }

        rewind($handle);

        while (! feof($handle)) {
            $contents .= fread($handle, 8192);
        }

        fclose($handle);

        return $contents;
    }
}
