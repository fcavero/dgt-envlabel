<?php

namespace App\Repository;

use App\Entity\Vehicle;
use App\Exception\Vehicle\VehicleNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Vehicle>
 *
 * @method Vehicle|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vehicle|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vehicle[]    findAll()
 * @method Vehicle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VehicleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vehicle::class);
    }

    public function findByIdOrFail(string $id): Vehicle
    {
        if (null === $vehicle = $this->find($id)) {
            throw VehicleNotFoundException::fromVehicleId($id);
        }

        return $vehicle;
    }

    public function findLatestEnvLabelByPlateOrFail(string $plate): Vehicle
    {
        if (null === $vehicle = $this->findBy(['plate' => $plate,], ['createdAt' => 'DESC',], 1)) {
            throw VehicleNotFoundException::fromVehiclePlate($plate);
        }

        return $vehicle[0];
    }

    public function findAllEnvLabelsByPlateOrFail(string $plate): array
    {
        if (null === $vehicles = $this->findBy(['plate' => $plate,], ['createdAt' => 'DESC',])) {
            throw VehicleNotFoundException::fromVehiclePlate($plate);
        }

        return $vehicles;
    }

    public function save(Vehicle $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Vehicle $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

}
