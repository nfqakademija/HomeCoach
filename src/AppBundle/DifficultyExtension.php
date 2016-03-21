<?php
/**
 * Created by PhpStorm.
 * User: grand
 * Date: 16.3.21
 * Time: 12.53
 */

namespace AppBundle;

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
        switch($number)
        {
            case 1:
                echo '<span class="veryEasyInfo">Labai lengva</span>';
                break;
            case 2:
                echo '<span class="easyInfo"> Lengva </span>';
                break;
            case 3:
                echo '<span class="mediumInfo"> Vidutinė </span>';
                break;
            case 4:
                echo '<span class="hardInfo"> Sunki </span>';
                break;
            case 5:
                echo '<span class="veryHardInfo"> Labai sunki </span>';
                break;
        }
    }

    public function getName()
    {
        return 'difficulty_extension';
    }
}
