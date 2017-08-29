<?php

class ContentController extends Controller
{
    /**
     * Construct this object by extending the basic Controller class
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Handles what happens when user moves to URL/index/index - or - as this is the default controller, also
     * when user moves to /index or enter your application at base level
     */
    public function maintain()
    {
        $csrf = Csrf::makeToken();
        $this->View->render('content/maintain', array('csrf'=>$csrf));
    }

    public function generatetc(){
        //TourModel::generateTourQuestions();
    }

    
    public function generateqc(){
        //TourModel::generateQuestionCodes();
    }

    public function generatetp(){
        //TourModel::generateTourPins();
    }

    public function cleanupforme($tourId){
        TourModel::cleanupTour($tourId);    
    }

    public function resetme(){
        Cookie::delete('memberToken');
    }

}
