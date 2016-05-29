<?php

namespace App\Presenters;

use App\Model\SongManager;
use Nette;

/**
 * Class UserspacePresenter
 * @package App\Presenters
 */
class UserspacePresenter extends BasePresenter
{
    const ITEMS_PER_PAGE = 8;
    /**
     * @var SongManager
     */
    private $songManager;

    /**
     * UserspacePresenter constructor.
     * @param SongManager $songManager
     */
    public function __construct(SongManager $songManager)
    {
        $this->songManager = $songManager;
    }

    /**
     * @param $position
     */
    public function renderDefault($position){

        $userId = $this->getUser()->getIdentity()->getId();

        $paginator = new Nette\Utils\Paginator;
        $paginator->setItemCount($this->songManager->getUserSongsCount($userId));
        $paginator->setItemsPerPage(self::ITEMS_PER_PAGE);
        $paginator->setPage($position);

        $songs = $this->songManager->getUserSongs($userId,$paginator->getLength(), $paginator->getOffset());

        $this->template->paginator = $paginator;
        $this->template->songs = $songs;
    }
}