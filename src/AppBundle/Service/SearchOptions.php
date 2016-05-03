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
    /**
     * @return string
     */
    public function queryDifficulty()
    {
        if ($this->difficulty!=null) {
            return "Workouts.difficulty = :diff AND ";
        }
        return "";
    }
    /**
     * @return string
     */
    public function querySearch()
    {
        if ($this->search!=null) {
            return "Workouts.title LIKE :search AND ";
        }
        return "";
    }
    /**
     * @return string
     */
    public function queryType()
    {
        if ($this->type!=null) {
            return $this->searchTags($this->type, "type");
        }
        return "";
    }
    /**
     * @return string
     */
    public function queryEquipment()
    {
        if ($this->equipment!=null) {
            return $this->searchTags($this->equipment, "equipment");
        }
        return "";
    }
    /**
     * @return string
     */
    public function queryMuscle()
    {
        if ($this->muscle!=null) {
            return $this->searchTags($this->muscle, "muscle_group");
        }
        return "";
    }
    /**
     * @param $tags
     * @param $tag_group
     * @return string
     */
    private function searchTags($tags, $tag_group)
    {
        $whereState = "";
        foreach ($tags as $i) {
            $whereState = $whereState . "FIND_IN_SET(:" . $tag_group . $i . ", Workouts." . $tag_group . ") AND ";
        }
        return $whereState;
    }
}
