<?php

namespace App\DataFixtures;

use App\Entity\Course;
use DateTimeImmutable;
use Faker;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CourseFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');


        for ($i = 1; $i < 7; $i++) {

            $course = new Course;
            $course->setTitle('Formation' . $i);
            $course->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus vestibulum tortor nec nisi posuere interdum. Phasellus porta dolor vel turpis tristique consectetur. Pellentesque sit amet erat elit. Nullam eget leo vitae arcu eleifend condimentum a quis lectus. Donec viverra dui sed magna semper, id eleifend odio tempus. Vestibulum luctus ipsum vel justo pretium, at lobortis arcu maximus.');
            $course->setPicture('1-62534b9b7a517.png');
            $course->setIsPublished(true);
            $course->setPublishedAt(DateTimeImmutable::createFromMutable($faker->datetime));
            $course->setCreateBy($this->getReference('instructor'));
            $course->addChosenBy($this->getReference('student'));
            $manager->persist($course);
            $this->addReference('course' . $i, $course);
        }


        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
