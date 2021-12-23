<?php

namespace App\Repository;

use App\Entity\EDT;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EDT|null find($id, $lockMode = null, $lockVersion = null)
 * @method EDT|null findOneBy(array $criteria, array $orderBy = null)
 * @method EDT[]    findAll()
 * @method EDT[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EDTRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EDT::class);
    }

    public function getNiceLookingArray(): array
    {
        $jours = array(
            '0' => 'Lundi',
            '1' => 'Mardi',
            '2' => 'Mercredi',
            '3' => 'Jeudi',
            '4' => 'Vendredi',
            '5' => 'Samedi',
            '6' => 'Dimanche'
        );

        $edts = $this->findAll();
        $edtswithusers = array();
        foreach($edts as $edt)
        {
            $kine = $edt->getIdKine();

            $edtswithusers[$kine->getId()]['email'] = $kine->getEmail();
            $edtswithusers[$kine->getId()]['nom'] = $kine->getNom();
            $edtswithusers[$kine->getId()]['prenom'] = $kine->getPrenom();
            $edtswithusers[$kine->getId()]['edt'][$edt->getJour()]["nom"] = $jours[$edt->getJour()];
            $edtswithusers[$kine->getId()]['edt'][$edt->getJour()]["periode"][$edt->getPeriode()]['heureDebut'] = $edt->getHeureDebut();
            $edtswithusers[$kine->getId()]['edt'][$edt->getJour()]["periode"][$edt->getPeriode()]['heureFin'] = $edt->getHeureFin();
            ksort($edtswithusers[$kine->getId()]['edt']);
        }
        return $edtswithusers;
    }

    // /**
    //  * @return EDT[] Returns an array of EDT objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EDT
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
