<?php

namespace App\Model;

use Nette;
use Nette\Security\Passwords;

/**
 * Class RegisterManager
 * @package App\Model
 */
class RegisterManager{

    /** @var Nette\Database\Context */
    private $database;

    /**
     * RegisterManager constructor.
     * @param Nette\Database\Context $database
     */
    public function __construct(Nette\Database\Context $database){
        $this->database = $database;
    }

    /**
     * @param $nickname
     * @param $password
     * @param $genre1
     * @param $genre2
     * @param $genre3
     */
    public function addUser($nickname, $password, $genre1, $genre2, $genre3){
        $this->database->table('user')->insert(array(
            'nickname' => $nickname,
            'genre1' => $genre1,
            'genre2' => $genre2,
            'genre3' => $genre3,
            'password' => Passwords::hash($password)
        ));

    }

    /**
     * @return array|Nette\Database\Table\IRow[]
     */
    public function getGenres(){
        return $this->database->table('songs')->select('genre')->group('genre')->fetchAll();
    }
}