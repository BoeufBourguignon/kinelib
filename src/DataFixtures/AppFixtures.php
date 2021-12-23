<?php

namespace App\DataFixtures;

use App\Entity\EDT;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Faker\Factory;
use Cocur\Slugify\Slugify;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $slugify = new Slugify();

        //Kinés
        for($i = 0; $i < 10; $i++)
        {
            $user = new User();
            $nom = $faker->lastName;
            $prenom = $faker->firstName;

            $user->setRoles(['ROLE_KINE']);
            $user->setEmail($slugify->slugify($prenom).'.'.$slugify->slugify($nom).'@gmail.com');
            $user->setNom($nom);
            $user->setPrenom($prenom);
            $user->setPassword(password_hash('jeanjean', PASSWORD_BCRYPT));

            $min = ['00','30'];
            $matin = ['08','09'];
            $aprem = ['16', '17', '18'];
            for($j = 0; $j < 6; $j++) {
                if ($j != 5 || $i%2==0)
                {
                    // Si 0 pas de pause à midi
                    if (rand(0, 1) == 0) {
                        $edt = new EDT();
                        $edt->setHeureDebut($matin[array_rand($matin)] . ':' . $min[array_rand($min)]);
                        $edt->setHeureFin($aprem[array_rand($aprem)] . ':' . $min[array_rand($min)]);
                        $edt->setPeriode('all');
                        $edt->setJour($j);

                        $user->addEDT($edt);

                        $manager->persist($edt);
                    }
                    // Sinon deux EDT : 1 matin 1 aprem
                    else {
                        //Matin
                        $edtMatin = new EDT();
                        $edtMatin->setPeriode('matin');
                        $edtMatin->setHeureDebut($matin[array_rand($matin)] . ':' . $min[array_rand($min)]);
                        $edtMatin->setHeureFin('12:' . $min[array_rand($min)]);
                        $edtMatin->setJour($j);
                        //Aprem
                        $edtAprem = new EDT();
                        $edtAprem->setPeriode('aprem');
                        $edtAprem->setHeureDebut(['13','14'][array_rand(['13','14'])].':'.$min[array_rand($min)]);
                        $edtAprem->setHeureFin($aprem[array_rand($aprem)].':'.$min[array_rand($min)]);
                        $edtAprem->setJour($j);

                        $user->addEDT($edtMatin);
                        $user->addEDT($edtAprem);

                        $manager->persist($edtMatin);
                        $manager->persist($edtAprem);
                    }
                }
            }

            $manager->persist($user);
        }
        //Compte admin
        $user = new User();
        $user->setPassword(password_hash('jeanjean', PASSWORD_BCRYPT));
        $user->setPrenom('Thibaud');
        $user->setNom('Leclere');
        $user->setEmail('thibaud.leclere@gmail.com');
        $user->setRoles(['ROLE_ADMIN']);
        $manager->persist($user);

        $manager->flush();
    }
}
