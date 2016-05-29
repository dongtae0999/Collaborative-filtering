<?php

namespace App\Model;

use Nette;
use Tracy\Debugger;

/**
 * Class RatingManager
 * @package App\Model
 */
class RatingManager extends Nette\Object{

    /**
     * comparing only first LIMIT songs
     */
    const LIMIT = 20;

    /** @var Nette\Database\Context */
    private $database;

    /**
     * RatingManager constructor.
     * @param Nette\Database\Context $database
     */
    public function __construct(Nette\Database\Context $database){
        $this->database = $database;
    }

    /**
     * Rate song
     * @param $rate - song rating
     * @param $songId
     * @param $userId
     */
    public function rateSong($rate, $songId, $userId){
        //if song is already rated, update it
        $isRated = $this->database->table("rating")->where("song_id = ?", $songId);

        if($isRated->count() && $isRated->where("user_id = ?", $userId)->count()){
            $toUpdate = array("rating"=>$rate);
            $isRated->update($toUpdate);
        }
        else {
            $toInsert = array("user_id"=>$userId,"song_id"=>$songId,"rating"=>$rate);
            //if there are already songs rated by current user, delete song with lowest rating
            $ratedSongs = $this->database->table("rating")->where("user_id = ?",$userId)->select("*")->order("rating");
            if($ratedSongs->count() >= self::LIMIT){
                $toDelete= $ratedSongs->fetch();
                $toDelete->update($toInsert);
            }
            else{
                $this->database->table("rating")->insert($toInsert);
            }
        }
    }
}