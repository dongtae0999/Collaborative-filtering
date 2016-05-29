<?php

namespace App\Presenters;

use Nette;
use App\Model;
use Nette\Application\UI\Form;

/**
 * Class SignPresenter
 * @package App\Presenters
 */
class SignPresenter extends BasePresenter
{
	/**
	 * @var Model\RegisterManager
     */
	private $registerManager;

	/**
	 * SignPresenter constructor.
	 * @param Model\RegisterManager $registerManager
     */
	public function __construct(Model\RegisterManager $registerManager){
		$this->registerManager = $registerManager;
	}

	/**
	 * register form
	 * @return Form
     */
	protected function createComponentRegister(){
		$form = new Nette\Application\UI\Form;

		$form->addText('nickname', 'Nickname:')
			->addRule(Form::MIN_LENGTH, 'Nickname must have at least %d characters.', 2)
			->addRule(Form::PATTERN, 'Use just alphanumeric characters.', '[0-9a-zA-ZěščřžýáíéúůŮÚĚŠČŘŽÝÁÍÉťŤňŇ]*')
			->setRequired('Please enter nickname.');

		$form->addPassword('password', 'Password:')
			->addRule(Form::MIN_LENGTH, 'Password must have at least %d characters.', 4)
			->setRequired('Please enter password.');

		$form->addPassword('passwordCheck', 'Password:')
			->setRequired('Please enter the password again for checking.')
			->addRule(Form::EQUAL, 'Passwords must match.', $form['password']);

		$genresDB = $this->registerManager->getGenres();

		// get genres from song database
		$genres = array();
		foreach($genresDB as $genre){
			$genres[$genre['genre']] = $genre['genre'];
		}

		$form->addSelect('genre1', 'Genres:', $genres)
			->setRequired('Please choose your genre');

		$form->addSelect('genre2', '', $genres)
			->addRule(Form::NOT_EQUAL,"Genres must be different from each other",$form['genre1']);

		$form->addSelect('genre3', '', $genres)
			->addRule(Form::NOT_EQUAL,"Genres must be different from each other",$form['genre1'])
			->addRule(Form::NOT_EQUAL,"Genres must be different from each other",$form['genre2']);

		$form->addSubmit('send', 'Send');

		$form->onSuccess[] = array($this, 'registerSucceeded');

		$this->bootstrapForm($form);

		return $form;
	}

	/**
	 * @param $form
     */
	public function registerSucceeded($form){
		$values = $form->values;

		$this->registerManager->addUser($values->nickname, $values->password, $values->genre1,$values->genre2,$values->genre3);
		$this->flashMessage('Thank you for your registration.', 'alert-success');
		$this->redirect('Homepage:');
	}

	/**
	 * login form
	 * @return Form
     */
	protected function createComponentSignInForm(){
		$form = new Nette\Application\UI\Form;
		$form->addText('nickname', 'Nickname:')
			->addRule(Form::PATTERN, 'Use just alphanumeric characters.', '[0-9a-zA-ZěščřžýáíéúůŮÚĚŠČŘŽÝÁÍÉťŤňŇ]*')
			->setRequired('Please enter nickname.');

		$form->addPassword('password', 'Password:')
			->setRequired('Please enter password.');

		$form->addCheckbox('remember', 'Remember me');

		$form->addSubmit('send', 'Log in');

		$form->onSuccess[] = array($this, 'signInFormSucceeded');
		$this->bootstrapForm($form);
		return $form;
	}

	/**
	 * @param $form
     */
	public function signInFormSucceeded($form){
		$values = $form->values;

		try {
			$this->getUser()->login($values->nickname, $values->password);

			if ($values->remember)
				$this->getUser()->setExpiration('14 days', FALSE);
			else
				$this->getUser()->setExpiration('20 minutes', TRUE);

			$this->redirect('Homepage:');

		} catch (Nette\Security\AuthenticationException $e) {
			$this->flashMessage('Login incorrect.', 'alert-danger');
		}
	}

	/**
	 * logout
     */
	public function actionOut(){
		$this->getUser()->logout();
		$this->flashMessage('You have been successfully signed out.', 'alert-info');
		$this->redirect('Homepage:');
	}

}
