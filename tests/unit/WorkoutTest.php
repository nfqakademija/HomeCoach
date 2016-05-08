<?php

use AppBundle\Entity\Workout;
use AppBundle\Entity\Comments;
class WorkoutTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    public function testMe()
    {
        $workout = new Workout('Creator');
        $this->assertEquals('Creator',$workout->getCreator());
        $workout->setTitle('Pavadinimas');
        $workout->setDescription('aprasymas aprasymas aprasymas aprasymas aprasymas ap');
        $workout->setDifficulty(3);
        $this->assertEquals('Pavadinimas',$workout->getTitle());
        $this->assertEquals('aprasymas aprasymas aprasymas aprasymas aprasymas ap',$workout->getDescription());
        $this->assertEquals(3,$workout->getDifficulty());
    }
}