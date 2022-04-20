<?php

namespace App\DataFixtures;

use App\Entity\Section;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class SectionFixtures extends Fixture implements DependentFixtureInterface
{


  public function load(ObjectManager $manager): void
  {


    $count = 1;
    for ($i = 1; $i < 7; $i++) {
      for ($y = 1; $y < 3; $y++) {
        $section = new Section;
        $section->setTitle('section' . $y);
        $section->setContainedIn($this->getReference('course' . $i));
        $section->setCreatedBy($this->getReference('instructor'));
        $manager->persist($section);
        $this->addReference('section' . $count, $section);
        $count++;
      }
    }


    $manager->flush();
  }

  public function getDependencies()
  {
    return [
      CourseFixtures::class,
    ];
  }
}
