<?php

namespace App\DataFixtures;


use App\Entity\User;
use Faker;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;
    public const INSTRUCTOR_REFERENCE = 'instructor';
    public const STUDENT_REFERENCE = 'student';

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($i = 1; $i < 4; $i++) {
            $instructor = new User;
            $instructor->setEmail('instructor' . $i . '@test.fr');
            $instructor->setRoles(["ROLE_USER", "ROLE_INSTRUCTOR"]);
            $instructor->setPassword($this->hasher->hashPassword($instructor, 'Password1'));
            $instructor->setFirstname($faker->firstName());
            $instructor->setLastname($faker->lastName());
            $instructor->setPicture('user-1-62534b9b7a517.png');
            $instructor->setSkills($faker->text());
            $instructor->setIsValidated(true);
            $manager->persist($instructor);
        }


        for ($i = 1; $i < 4; $i++) {
            $student = new User;
            $student->setEmail('student' . $i . '@test.fr');
            $student->setNickname($faker->userName());
            $student->setRoles(["ROLE_USER"]);
            $student->setPassword($this->hasher->hashPassword($student, 'Password1'));
            $student->setIsValidated(true);
            $manager->persist($student);
        }


        $manager->flush();

        $this->addReference('instructor', $instructor);
        $this->addReference('student', $student);
    }
}
