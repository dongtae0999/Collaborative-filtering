<?php

namespace App\Presenters;
use App\Model;

/**
 * Class AlgorithmPresenter
 * @package App\Presenters
 */
class AlgorithmPresenter extends BasePresenter
{
    /**
     * @var Model\PearsonManager
     */
    private $pearsonManager;

    /**
     * AlgorithmPresenter constructor.
     * @param Model\PearsonManager $pearsonManager
     */
    public function __construct(Model\PearsonManager $pearsonManager){
        $this->pearsonManager = $pearsonManager;
    }

    /**
     * calculate coefficients
     */
    public function handlePearson(){
        $this->pearsonManager->compute();
        if($this->isAjax()){
            $this->redrawControl("algorithms");
        }
    }
}