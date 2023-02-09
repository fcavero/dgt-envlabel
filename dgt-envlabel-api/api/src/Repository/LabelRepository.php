<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Label;
use App\Exception\Label\LabelNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Label>
 *
 * @method Label|null find($id, $lockMode = null, $lockVersion = null)
 * @method Label|null findOneBy(array $criteria, array $orderBy = null)
 * @method Label[]    findAll()
 * @method Label[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LabelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Label::class);
    }

    public function findLabelByIdOrFail(int $id): Label
    {
        if (null === $label = $this->find($id)) {
            throw LabelNotFoundException::fromLabelId($id);
        }

        return $label;
    }

    public function findLabelByDescriptionOrFail(string $description): Label
    {
        if (null === $label = $this->findOneBy(['description' => $description,])) {
            throw LabelNotFoundException::fromLabelDescription($description);
        }

        return $label;
    }

    public function save(Label $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Label $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

}
