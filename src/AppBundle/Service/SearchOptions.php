<?php
/**
 * Created by PhpStorm.
 * User: darius0021
 * Date: 16.5.2
 * Time: 19.28
 */

namespace AppBundle\Service;

class SearchOptions
{
    /**
     * @var int
     */
    protected $page;
    /**
     * @var string
     */
    protected $sort;
    /**
     * @var int
     */
    protected $difficulty;
    /**
     * @var string
     */
    protected $search;
    /**
     * @var array
     */
    protected $type;
    /**
     * @var array
     */
    protected $equipment;
    /**
     * @var array
     */
    protected $muscle;

    /**
     * SearchOptions constructor.
     * @param int $page
     * @param string $sort
     * @param int $difficulty
     * @param string $search
     * @param array $type
     * @param array $equipment
     * @param array $muscle
     */
    public function __construct($page, $sort, $difficulty, $search, $type, $equipment, $muscle)
    {
        $this->page = $page;
        $this->sort = $sort;
        $this->difficulty = $difficulty;
        $this->search = $search;
        $this->type = $type;
        $this->equipment = $equipment;
        $this->muscle = $muscle;
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @return string
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @return int
     */
    public function getDifficulty()
    {
        return $this->difficulty;
    }

    /**
     * @return string
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * @return array
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getEquipment()
    {
        return $this->equipment;
    }

    /**
     * @return array
     */
    public function getMuscle()
    {
        return $this->muscle;
    }
}
