<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\DownloadedFileLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DownloadedFileLog>
 *
 * @method DownloadedFileLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method DownloadedFileLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method DownloadedFileLog[]    findAll()
 * @method DownloadedFileLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DownloadedFileLogRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DownloadedFileLog::class);
    }

    public function save(DownloadedFileLog $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DownloadedFileLog $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

}
