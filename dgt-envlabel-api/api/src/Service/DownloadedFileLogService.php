<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;

class DownloadedFileLogService
{
    private const LAST_HASH_SQL = 'SELECT file_hash FROM envlabel.t_downloaded_file_log '.
                                    'ORDER BY tms_creation DESC LIMIT 1';
    private const NEW_HASH_SQL = 'INSERT INTO envlabel.t_downloaded_file_log(file_hash) VALUES(:hash)';

    private EntityManagerInterface $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @throws Exception
     */
    public function checkFileHash(string $hash): bool
    {
        $resultSet = $this->entityManager->getConnection()->executeQuery(self::LAST_HASH_SQL);
        if (false === $lastHash = $resultSet->fetchOne()) {
            return true; // The very first file. Impressive.
        }

        return ($hash === $lastHash);
    }

    /**
     * @throws Exception
     */
    public function logFileHash(string $hash): void
    {
        $this->entityManager->getConnection()->executeQuery(self::NEW_HASH_SQL, ['hash' => $hash,]);
    }

}
