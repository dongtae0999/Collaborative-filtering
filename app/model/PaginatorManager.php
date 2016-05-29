<?php

namespace App\Model;

use Nette;

/**
 * Class PaginatorManager
 * @package App\Model
 */
class PaginatorManager extends Nette\Object
{
    /**
     * @var Nette\Database\Context
     */
    private $database;

    /**
     * PaginatorManager constructor.
     * @param Nette\Database\Context $database
     */
    public function __construct(Nette\Database\Context $database){
        $this->database = $database;
    }

    /**
     * @param null $genre
     * @return int - count of songs in database
     */
    public function getItemCount($genre=null){
        if($genre){
            return $this->database->table('songs')->where("genre = ?",$genre)->count("*");
        }
        return $this->database->table('songs')->count("*");
    }

    /**
     * @param $length - songs per page
     * @param $offset
     * @param null $genre - is not null if user enable filter
     * @return Nette\Database\Table\Selection
     */
    public function getItemsPagination($length, $offset, $genre = null){
        if($genre){
            return $this->database->table('songs')->where("genre = ?",$genre)->limit($length, $offset);
        }
        return $this->database->table('songs')->limit($length, $offset);
    }
}
