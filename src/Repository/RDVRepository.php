<?php

namespace App\Repository;

use App\Entity\RDV;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method RDV|null find($id, $lockMode = null, $lockVersion = null)
 * @method RDV|null findOneBy(array $criteria, array $orderBy = null)
 * @method RDV[]    findAll()
 * @method RDV[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RDVRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RDV::class);
    }

    public function getRdvsByKine(User $kine): ?array {
        $time = time();

        $rdvs = $this
            ->createQueryBuilder('p')
            ->where('p.kine = :kine')
            ->andWhere('p.date >= :date')
            ->setParameter('kine', $kine)
            ->setParameter('date', (new \DateTime())->setTimestamp($time))
            ->getQuery()
            ->getResult();

        $niceLookingRdvs = array();
        foreach($rdvs as $rdv) {
            $niceLookingRdvs[$rdv->getDate()->format('d/m/Y')][$rdv->getHeureDebut()]['userId']
                = $rdv->getUser()->getId();
            $niceLookingRdvs[$rdv->getDate()->format('d/m/Y')][$rdv->getHeureDebut()]['userNom']
                = $rdv->getUser()->getNom();
            $niceLookingRdvs[$rdv->getDate()->format('d/m/Y')][$rdv->getHeureDebut()]['userPrenom']
                = $rdv->getUser()->getPrenom();
            $niceLookingRdvs[$rdv->getDate()->format('d/m/Y')][$rdv->getHeureDebut()]['userMail']
                = $rdv->getUser()->getEmail();

            ksort($niceLookingRdvs[$rdv->getDate()->format('d/m/Y')]);
        }
        ksort($niceLookingRdvs);

        return count($niceLookingRdvs) ? $niceLookingRdvs : null;
    }

    public function getRdvsByUser(User $user): ?array {
        $time = strtotime('today');

        $rdvs = $this
            ->createQueryBuilder('p')
            ->where('p.user = :user')
            ->andWhere('p.date >= :date')
            ->setParameter('user', $user)
            ->setParameter('date', (new \DateTime())->setTimestamp($time))
            ->getQuery()
            ->getResult();

        $niceLookingRdvs = array();
        foreach($rdvs as $rdv) {
            $niceLookingRdvs[$rdv->getDate()->getTimestamp()][$rdv->getHeureDebut()]['kineId']
                = $rdv->getKine()->getId();
            $niceLookingRdvs[$rdv->getDate()->getTimestamp()][$rdv->getHeureDebut()]['kineNom']
                = $rdv->getKine()->getNom();
            $niceLookingRdvs[$rdv->getDate()->getTimestamp()][$rdv->getHeureDebut()]['kinePrenom']
                = $rdv->getKine()->getPrenom();
            $niceLookingRdvs[$rdv->getDate()->getTimestamp()][$rdv->getHeureDebut()]['kineMail']
                = $rdv->getKine()->getEmail();

            ksort($niceLookingRdvs[$rdv->getDate()->getTimestamp()]);
        }
        ksort($niceLookingRdvs);

        return count($niceLookingRdvs) ? $niceLookingRdvs : null;
    }

    // /**
    //  * @return RDV[] Returns an array of RDV objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RDV
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
