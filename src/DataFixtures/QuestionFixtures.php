<?php

namespace App\DataFixtures;

use App\Entity\Question;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;

class QuestionFixtures extends Fixture implements DependentFixtureInterface
{


  public function load(ObjectManager $manager): void
  {

    $faker = Faker\Factory::create('fr_FR');

    for ($i = 1; $i < 4; $i++) {
      for ($y = 1; $y < 4; $y++) {
        $question = new Question;
        $question->setQuestion($faker->text($maxNbChars = 200));
        $question->setAnswers([[$faker->sentence($nbWords = 6, $variableNbWords = true), false], [$faker->sentence($nbWords = 6, $variableNbWords = true), true], [$faker->sentence($nbWords = 6, $variableNbWords = true), true]]);
        $question->setCourse($this->getReference('course' . $i));
        $manager->persist($question);
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
