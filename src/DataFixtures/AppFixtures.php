<?php

namespace App\DataFixtures;

use App\Repository\EDTRepository;
use App\Repository\RDVRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\EDT;
use App\Entity\RDV;
use Doctrine\Persistence\ObjectRepository;
use Faker\Factory;
use Cocur\Slugify\Slugify;
use Symfony\Component\Console\Output\ConsoleOutput;

class AppFixtures extends Fixture
{
    private \Faker\Generator $faker;
    private \Cocur\Slugify\Slugify $slugify;

    public function __construct(EDTRepository $EDTRepository, RDVRepository $RDVRepository) {
        $this->faker = Factory::create('fr_FR');
        $this->slugify = new Slugify();
    }

    public function load(ObjectManager $manager): void
    {
        $output = new ConsoleOutput();
        $EDTRepository = $manager->getRepository(EDT::class);
        $RDVRepository = $manager->getRepository(RDV::class);

        define('NB_OF_USER', 5);
        define('NB_OF_KINE', 5);

        $allUsers = array();
        $allKines = array();
        //Création des kinés
        for($i = 0; $i < NB_OF_KINE; $i++) {
            $kine = $this->getKineWithEdt();
            foreach($kine->getEDTs() as $edt) {
                $manager->persist($edt);
            }
            $allUsers[] = $allKines[] = $kine;
            $manager->persist($kine);
        }
        //Création des utilisateurs
        for($i = 0; $i < NB_OF_USER; $i++) {
            $user = $this->getUser();
            $allUsers[] = $user;
            $manager->persist($user);
        }

        //Ajout de rendez-vous à chaque utilisateur
        define('NB_RDV_PER_USER', 50);
        date_default_timezone_set('Europe/Paris');
        $firstDayOfCurrentWeek = strtotime('Monday this week');
        $lastDayOfNextWeek = strtotime('Sunday next week');
        foreach($allUsers as $user) {
            for($i=0; $i < NB_RDV_PER_USER; $i++) {
                $kine = $allKines[array_rand($allKines)];
                $date = rand($firstDayOfCurrentWeek, $lastDayOfNextWeek);
                $heure = ['09','10','11','12','14','15','16','17'][rand(0,7)] . ':00';

                $rdv = new RDV();
                $rdv
                    ->setHeureDebut($heure)
                    ->setDate((new \DateTime)->setTimestamp($date))
                    ->setUser($user)
                    ->setKine($kine);
                $manager->persist($rdv);
            }
        }



        //Création du compte Admin
        $user = new User();
        $user->setPassword(password_hash('jeanjean', PASSWORD_BCRYPT))
            ->setPrenom('Thibaud')
            ->setNom('Leclere')
            ->setEmail('thibaud.leclere@gmail.com')
            ->setRoles(['ROLE_ADMIN']);
        $manager->persist($user);


        $manager->flush();
    }

    private function getUser(array $roles = ['ROLE_USER']): User
    {
        $nom = $this->faker->lastName;
        $prenom = $this->faker->firstName;
        $email =
            $this->slugify->slugify($prenom) .
            '.' .
            $this->slugify->slugify($nom) .
            '@gmail.com';
        $password = password_hash('jeanjean', PASSWORD_BCRYPT);

        $user = new User();
        $user
            ->setRoles($roles)
            ->setEmail($email)
            ->setNom($nom)
            ->setPrenom($prenom)
            ->setPassword($password);
        return $user;
    }

    private function getKineWithEdt(): User
    {
        $user = $this->getUser(['ROLE_KINE']);

        //Création de l'EDT
        $debutMatin = ['08', '09'];
        $finMatin = ['12', '13'];
        $finAprem = ['16', '17', '18'];
        $workOnSunday = rand(1, 5) == 1;       //Une chance sur 5 de travailler le dimanche
        $workOnWednesday = !(rand(1, 3) == 1); //Une chance sur 3 de ne pas travailler le mercredi
        $workOnSaturday = rand(1, 2) == 1;     //Une chance sur 2 de travailler le samedi
        for ($j = 0; $j < 7; $j++) {
            if (   ($j != 2 || $workOnWednesday)
                && ($j != 5 || $workOnSaturday)
                && ($j != 6 || $workOnSunday)
            ) {
                if (rand(0, 1)) { //Une chance sur 2 d'avoir une pause à midi
                    $edtMatin = new EDT();
                    $heureFinMatinTmp = $finMatin[array_rand($finMatin)];
                    $edtMatin
                        ->setPeriode('matin')
                        ->setHeureDebut($debutMatin[array_rand($debutMatin)] . ':00')
                        ->setHeureFin($heureFinMatinTmp . ':00')
                        ->setJour($j);
                    $user->addEDT($edtMatin);

                    $edtAprem = new EDT();
                    $edtAprem
                        ->setPeriode('aprem')
                        ->setHeureDebut(((int)$heureFinMatinTmp + rand(1, 2)) . ':00')
                        ->setHeureFin($finAprem[array_rand($finAprem)] . ':00')
                        ->setJour($j);
                    $user->addEDT($edtAprem);
                }
                else {
                    $edt = new EDT();
                    $edt
                        ->setPeriode('all')
                        ->setHeureDebut($debutMatin[array_rand($debutMatin)] . ':00')
                        ->setHeureFin($finAprem[array_rand($finAprem)] . ':00')
                        ->setJour($j);
                    $user->addEDT($edt);
                }
            }
        }
        return $user;
    }


}
