<?php

namespace App\DataFixtures;

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
