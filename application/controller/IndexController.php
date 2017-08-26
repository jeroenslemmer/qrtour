<?php

class IndexController extends Controller
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
    public function index()
    {
        $team = false;
        $tour = false;
        $csrf = Csrf::makeToken();
        $member = MemberModel::getMemberByCheckedToken();
        if ($member){
           $team = TeamModel::getTeam($member->team_id);
           $tour = TourModel::getTour($team->tour_id);
        } 
        $this->View->render('index/index', array('csrf'=>$csrf,'member'=>$member,'team'=>$team,'tour'=>$tour,'feedback'=>''));
    }

    public function reset()
    {
        Cookie::delete('memberToken');
        echo 'reset';
    }
}
