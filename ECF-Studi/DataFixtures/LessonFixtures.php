<?php

namespace App\DataFixtures;

use App\Entity\Lesson;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class LessonFixtures extends Fixture implements DependentFixtureInterface
{


  public function load(ObjectManager $manager): void
  {



    for ($i = 1; $i < 13; $i++) {
      for ($y = 1; $y < 3; $y++) {
        $lesson = new Lesson;
        $lesson->setTitle('lesson' . $y);
        $lesson->setVideo('https://www.youtube.com/embed/bLPONCBPDeQ');
        $lesson->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus vestibulum tortor nec nisi posuere interdum. Phasellus porta dolor vel turpis tristique consectetur. Pellentesque sit amet erat elit. Nullam eget leo vitae arcu eleifend condimentum a quis lectus. Donec viverra dui sed magna semper, id eleifend odio tempus. Vestibulum luctus ipsum vel justo pretium, at lobortis arcu maximus.');
        $lesson->setContainedIn($this->getReference('section' . $i));
        $lesson->setCreatedBy($this->getReference('instructor'));
        if ($y === 1) {
          $lesson->setResources(['1-62534b9b7a517.png']);
        }
        $manager->persist($lesson);
      }
    }


    $manager->flush();
  }

  public function getDependencies()
  {
    return [
      SectionFixtures::class,
    ];
  }
}
