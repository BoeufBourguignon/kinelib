<?php

namespace App\Repository;

use App\Entity\EDT;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

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

    public function getNiceLookingArrayFindByKine(UserInterface $Iuser, UserRepository $userRepository): array
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



        $edts = $this->findBy(['idKine' => $userRepository->findBy(['email' => $Iuser->getUserIdentifier()])]);
        $niceEdt = array();
        foreach($edts as $edt)
        {
            $niceEdt[$edt->getJour()]['nom']                                        = $jours[$edt->getJour()];
            $niceEdt[$edt->getJour()]['periode'][$edt->getPeriode()]['heureDebut']  = $edt->getHeureDebut();
            $niceEdt[$edt->getJour()]['periode'][$edt->getPeriode()]['heureFin']    = $edt->getHeureFin();
            ksort($niceEdt);
            //krsort($niceEdit[$edt->getJour()]["periode"]);
        }
        return $niceEdt;
    }

    public function getNiceLookingArrayFindAll(): array
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
            krsort($edtswithusers[$kine->getId()]['edt'][$edt->getJour()]["periode"]);
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
