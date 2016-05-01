<?php
/**
 * Created by PhpStorm.
 * User: grand
 * Date: 16.3.21
 * Time: 12.53
 */

namespace AppBundle\Twig;

class DifficultyExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('difficulty', array($this,'difficultyFilter')),
        );
    }

    public function difficultyFilter($number)
    {
        switch ($number) {
            case 1:
                echo '<figure class="circleVeryEasy"></figure>';
                break;
            case 2:
                echo '<figure class="circleEasy"></figure>';
                break;
            case 3:
                echo '<figure class="circleMedium"></figure>';
                break;
            case 4:
                echo '<figure class="circleHard"></figure>';
                break;
            case 5:
                echo '<figure class="circleVeryHard"></figure>';
                break;
        }
    }

    public function getName()
    {
        return 'difficulty_extension';
    }
}
