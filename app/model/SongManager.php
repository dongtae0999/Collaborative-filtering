<?php

namespace App\Model;

use Nette;

/**
 * Class SongManager
 * @package App\Model
 */
class SongManager extends Nette\Object{

    /** @var Nette\Database\Context */
    private $database;

    /**
     * SongManager constructor.
     * @param Nette\Database\Context $database
     */
    public function __construct(Nette\Database\Context $database){
        $this->database = $database;
    }

    /**
     * @param $songs
     * @return array
     */
    private function fillDifferenceById($songs){
        $toRecommend = array();
        foreach($songs as $song){
            $toRecommend[] = $this->database->table("songs")->where('id = ?',$song['song_id'])->fetch();
        }
        return $toRecommend;
    }

    /**
     * @param $userId
     * @param $algorithm
     * @param $begin
     * @param $offset
     * @return array|Nette\Database\Table\IRow[] - return array of songs for specific user
     */
    public function getDifference($userId, $algorithm, $begin, $offset){

        // get id most similar user
        $id = $this->database->table($algorithm)->where("user_id = ?", $userId)->fetch()['best_match'];

        // if there is no similar user return song by genres
        if(-1 == $id || !$id)
            return $this->getByGenres($userId);

        $userSongs = $this->database->table("rating")->where("user_id = ?",$userId)->order('song_id');
        $songs = $this->database->table("rating")->where("user_id = ?",$id)->order('song_id');

        //odebere stejne songy z vysledne database selection
        foreach($userSongs as $userSong){
            foreach($songs as $key=>$song){
                if($userSong['song_id'] == $song['song_id']) {
                    $songs->offsetUnset($key);
                    continue;
                }
            }
        }
        $toRecommend = $this->fillDifferenceById($songs);

        if($begin>= sizeof($toRecommend)) $begin = 0;
        $end = (($begin+$offset)>sizeof($toRecommend))?(sizeof($toRecommend)-$begin):$offset;
        return array_slice($toRecommend, $begin, $end);

    }

    /**
     * @param $userId
     * @return array|Nette\Database\Table\IRow[]
     */
    private function getByGenres($userId){
        $user = $this->database->table("user")->where('id = ?', $userId)->fetch();
        $genre1 = $user["genre1"];
        $genre2 = $user["genre2"];
        $genre3 = $user["genre3"];

        $songs = $this->database->table("songs")
            ->where('genre = ? OR genre = ? OR genre = ?', array($genre1, $genre2, $genre3))->limit(10)->fetchAll();

        return $songs;
    }

    /**
     * @param $userId
     * @return int
     */
    public function getUserSongsCount($userId){
        return $this->database->table("rating")->where("user_id = ?",$userId)->count("*");
    }

    /**
     * @param $userId
     * @param $length
     * @param $offset
     * @return array|null
     */
    public function getUserSongs($userId, $length, $offset){

        $ids = $this->database->table("rating")->where("user_id = ?", $userId);

        if(!$ids)
            return NULL;

        foreach($ids as $id){
            $songs[] = array("detail"=>$this->database->table("songs")->where("id = ?",$id["song_id"])->fetch(),"rating"=>$id["rating"]);
        }

        return array_splice($songs,$offset,$length);
    }
}
