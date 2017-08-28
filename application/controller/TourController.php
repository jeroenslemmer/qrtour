<?php

class TourController extends Controller
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
    public function go()
    {
        $team = false;
        $tour = false;
        $csrf = Csrf::makeToken();
        $member = MemberModel::getMemberByCheckedToken();
        if ($member){
           $team = TeamModel::getTeam($member->team_id);
           $tour = TourModel::getTour($team->tour_id);
        } 
        $this->View->render('tour/go', array('csrf'=>$csrf,'member'=>$member,'team'=>$team,'tour'=>$tour,'feedback'=>''));
    }

    public function board($tourId=0)
    {
        $tour = TourModel::getTour($tourId);
        if ($tour){
            $this->View->render('tour/board', array('tour'=>$tour));
        }
    }

    public function boardscores($tourId=0)
    {
        function cmp ($a,$b){
            if ($a['result']['score'] < $b['result']['score']) return 1;
            if ($a['result']['score'] > $b['result']['score']) return -1;
            if ($a['result']['max'] < $b['result']['max']) return -1;
            if ($a['result']['max'] > $b['result']['max']) return 1;
            return 0;
        }

        $tour = TourModel::getTour($tourId);
        if ($tour){
            $results = [];
            $teams = TeamModel::getAllTeamsByTourId($tourId);
            foreach($teams as $team){
                $result = ['name'=> $team->name];
                $result['result'] = QuestionModel::teamResult($team->id);
                $members = MemberModel::getAllMemberByTeam($team->id);
                $list = '';
                foreach($members as $member){
                    if ($list > '') $list .= ',';
                    $list .= $member->name;
                }
                $result['members'] = $list;
                $results[] = $result;
            }
            usort($results,"cmp");

            $json = json_encode($results);
            header("Content-type:application/json");
            echo $json;
        }
    }


    public function reset()
    {
        Cookie::delete('memberToken');
        echo 'reset';
    }



}
