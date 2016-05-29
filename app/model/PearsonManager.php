<?php

namespace App\Model;

use Nette\Database\Context;
use Nette\Object;
use Tracy\Debugger;

/**
 * Class PearsonManager
 * @package App\Model
 */
class PearsonManager extends Object{

    /**
     * @var Context
     */
    private $database;

    /**
     * @var array|\Nette\Database\Table\IRow[]
     */
    private $users;

    /**
     * minimum rating songs for calculating coefficient
     */
    const N = 20;

    /**
     * PearsonManager constructor.
     * @param Context $database
     */
    public function __construct(Context $database){
        $this->database = $database;
        $this->users = $this->database->table("user")->fetchAll();
    }

    /**
     * @param $currentUser
     * @param $user
     * @return float|int
     */
    private function compareGenres($currentUser, $user){
        $genreSimilarity = 0;

        for($i=1;$i<=3;$i++){
            if($currentUser['genre'.$i]== $user['genre1']){
                $genreSimilarity += 0.1;
            }
            else if($currentUser['genre'.$i]== $user['genre2']){
                $genreSimilarity += 0.1;
            }
            else if($currentUser['genre'.$i]== $user['genre3']){
                $genreSimilarity += 0.1;
            }
        }
        return $genreSimilarity;
    }

    /**
     * @param $currentUser
     * @param $user
     * @return array|int - return coefficient & and count of different songs
     */
    private function getCoeficient($currentUser, $user){
        $genreSimilarity = $this->compareGenres($currentUser,$user);

        $ratingX = array_values($this->database->table("rating")->where("user_id = ?",$currentUser['id'])->fetchAll());
        $ratingY = array_values($this->database->table("rating")->where("user_id = ?",$user['id'])->fetchAll());

        $offset_Y = sizeof($ratingY);
        $offset_X = sizeof($ratingX);

        // check if user has enought data
        if(sizeof($ratingX) < (self::N/2) || sizeof($ratingY) == 0)
            return array(-1,0);


        // Pearsons mathematical formula

        $X = array();
        $Y = array();

        for($i = 0; $i < $offset_X;$i++){
            $Y[$i] = 0;
            $X[$i] = 0;
        }

        $similarSongs = 0;
        for($i = 0; $i < $offset_X; $i++){
            $X[$i] = $ratingX[$i]['rating'] / 10;
            for($j = 0; $j < $offset_Y; $j++){
                if($ratingX[$i]['song_id'] == $ratingY[$j]['song_id']){
                    $Y[$i] = $ratingY[$j]['rating'] / 10;
                    $similarSongs++;
                    break;
                }
            }
        }

        if(!$similarSongs)
            return array(-1,0);

        $avgX = (array_sum($X)) / $offset_X;
        $avgY = (array_sum($Y)) / $offset_X;

        $tmp = 0;
        foreach($X as $rating){
            $tmp += pow(($rating - $avgX),2);
        }
        $sx = sqrt((1 / ($offset_X - 1))*$tmp);

        if($sx == 0)
            $sx = 0.01;

        $tmp = 0;
        foreach($Y as $rating){
            $tmp += pow(($rating - $avgY),2);
        }
        $sy = sqrt((1 / ($offset_Y - 1))*$tmp);

        if($sy == 0)
            $sy = 0.01;

        $tmp = 0;
        for($i = 0 ; $i < $offset_X ; $i++)
        {
            $tmp += (($X[$i] - $avgX) / $sx) * (($Y[$i] - $avgY) / $sy);
        }
        $q = ( 1 / ($offset_X - 1)) * $tmp;

        $q += $genreSimilarity;

        $differentSongs = sizeof($ratingY)-$similarSongs;
        return array($q,$differentSongs);
}


    /**
     * @param $currentUser
     */
    public function computeUser($currentUser){

        // compare user with other users
        foreach($this->users as $user){
            if($currentUser['id'] != $user['id']){
                $coeficient = $this->getCoeficient($currentUser, $user);
                $similarUsers[$user['id']] = $coeficient[0];
                $differentSongs[$user['id']] = $coeficient[1];
            }
        }
        asort($similarUsers);

        // set the internal pointer of an array to its last element
        $best_match_id = -1;
        end($similarUsers);
        for($i = 0 ; $i < sizeof($similarUsers) ; $i++){
            // if users are similar, check count of different songs
            // if there are no different songs check next similar user in row until 0.2
            if(current($similarUsers) > 0.2) {
                $id = key($similarUsers);

                prev($similarUsers);
                if($differentSongs[$id] > 0) {
                    $best_match_id = $id;
                    break;
                }
            }
            else {
                break;
            }
        }
        $toInsert = array("user_id" => $currentUser['id'],"best_match" => $best_match_id);
        $this->database->table("pearson")->insert($toInsert);
    }

    /**
     * loop through all users and calculate coefficient
     */
    public function compute(){
        // delete all old data
        $this->database->table("pearson")->delete();

        foreach($this->users as $user){
            $this->computeUser($user);
        }
    }
}