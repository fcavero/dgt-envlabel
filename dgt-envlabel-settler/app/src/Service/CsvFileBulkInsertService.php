<?php

declare(strict_types=1);

namespace Settler\Service;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Psr\Log\LoggerInterface;

class CsvFileBulkInsertService
{
    private string $esPlateRegexp;

    private EntityManagerInterface $entityManager;

    private LoggerInterface $logger;


    public function __construct(
        string $esPlateRegexp,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    )
    {
        $this->esPlateRegexp = $esPlateRegexp;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * @throws Exception
     */
    public function loadAndInsertLineByLine(string $file): void
    {
        foreach ($this->getLines($file) as $line) {
            // Data comes with the following syntax: 0000BBB|SIN DISTINTIVO
            $items = explode("|", $this->sanitizeLine($line));

            // Need to check if line starts with a valid spanish license plate
            if ($this->checkValidESPlate($items[0])) {
                $this->entityManager->getConnection()
                    ->insert('envlabel.tmp_file', [
                        'txt_plate'   => $items[0],
                        'txt_dgt_tag' => $items[1],
                    ]);
            }
        }
    }

    private function getLines(string $filepath): Generator
    {
        $this->logger->info(\sprintf('Starting %s file streaming...', $filepath));
        $file = fopen($filepath, 'rb');

        try {
            while ($line = fgets($file)) {
                yield $line;
            }

        } finally {
            $this->logger->info(\sprintf('Closing %s file!', $filepath));
            fclose($file);
        }
    }

    private function checkValidESPlate(string $plate): int|bool
    {
        return preg_match($this->esPlateRegexp, $plate);
    }

    private function sanitizeLine(string $line): string
    {
        return str_replace(['.', "\n", "\t", "\r"], '', $line);
    }

}
