<?php

namespace App\Presenters;

use Nette;
use App\Model;

/**
 * Class HomepagePresenter
 * @package App\Presenters
 */
class HomepagePresenter extends BasePresenter
{
	const 	ITEMS_PER_PAGE = 8;
	const 	PEARSON = 'pearson';

	/**
	 * @var Model\PaginatorManager
     */
	private $paginatorManager;
	/**
	 * @var Model\RatingManager
     */
	private $ratingManager;
	/**
	 * @var Model\SongManager
     */
	private $songManager;
	/**
	 * @var Model\RegisterManager
     */
	private $registerManager;

	/**
	 * HomepagePresenter constructor.
	 * @param Model\PaginatorManager $paginatorManager
	 * @param Model\RatingManager $ratingManager
	 * @param Model\SongManager $songManager
	 * @param Model\RegisterManager $registerManager
     */
	public function __construct(Model\PaginatorManager $paginatorManager, Model\RatingManager $ratingManager, Model\SongManager $songManager, Model\RegisterManager $registerManager){
		$this->paginatorManager = $paginatorManager;
		$this->ratingManager = $ratingManager;
		$this->songManager = $songManager;
		$this->registerManager = $registerManager;
	}

	/**
	 * @param $position
	 * @param null $genre - from filter
     */
	public function renderDefault($position, $genre = null){
		$paginator = new Nette\Utils\Paginator;
		$paginator->setItemCount($this->paginatorManager->getItemCount($genre));
		$paginator->setItemsPerPage(self::ITEMS_PER_PAGE);
		$paginator->setPage($position);

		$this->getComponent("filterForm")->setDefaults(array('genre' => $genre));
		if($this->getUser()->isLoggedIn())
			$this->template->recommended = $this->songManager->getDifference($this->getUser()->getIdentity()->getId(),self::PEARSON,0,20);

		$this->template->songs = $this->paginatorManager->getItemsPagination($paginator->getLength(), $paginator->getOffset(),$genre);
		$this->template->paginator = $paginator;
		$this->template->genre = $genre;
	}

	/**
	 * @param $rate
	 * @param $id
	 * @param $position
	 * @param $genre
     */
	public function actionRating($rate, $id, $position, $genre){
		if(!$this->getUser()->isLoggedIn()){
			$this->flashMessage("You have to be logged in to rate songs",'alert-danger');
		}
		else{
			$this->flashMessage($rate . ' stars for song with id: ' . $id , 'alert-info' );
			$this->ratingManager->rateSong($rate,$id,$this->getUser()->getIdentity()->getId());

		}
		if($this->isAjax()){
			$this->redrawControl();
		}
		else{
			$this->redirect('Homepage:',$position,$genre);
		}
	}

	/**
	 * Select for songs filtering
	 * @return Nette\Application\UI\Form
     */
	public function createComponentFilterForm(){
		$form = new Nette\Application\UI\Form;

		$renderer = $form->getRenderer();
		$renderer->wrappers['controls']['container'] = NULL;
		$renderer->wrappers['pair']['container'] = 'div class=form-group';
		$renderer->wrappers['pair']['.error'] = 'has-error';
		$renderer->wrappers['control']['description'] = 'span class=help-block';
		$renderer->wrappers['control']['errorcontainer'] = 'span class=help-block';

		$genresDB = $this->registerManager->getGenres();

		$genres = array();
		foreach($genresDB as $genre)
		{
			$genres[$genre['genre']] = $genre['genre'];
		}

		$form->addSelect('genre', 'Genre:', $genres)
			->setPrompt('All');
		$form->addSubmit('send', 'Filter');

		$form->onSuccess[] = array($this, 'filterSucceeded');

		$form['genre']->getControlPrototype()->addClass('form-control');
		$form->getElementPrototype()->class('form-inline');
		$form['send']->getControlPrototype()->addClass('btn btn-primary');
		return $form;
	}

	/**
	 * @param $form
     */
	public function filterSucceeded($form){
		$values = $form->values;

		$this->redirect('Homepage:', 1, $values->genre);
	}
}
