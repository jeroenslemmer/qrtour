<?php

/**
 * The note controller: Just an example of simple create, read, update and delete (CRUD) actions.
 */
class TeamController extends Controller
{
    /**
     * Construct this object by extending the basic Controller class
     */
    public function __construct()
    {
        parent::__construct();

        // VERY IMPORTANT: All controllers/areas that should only be usable by logged-in users
        // need this line! Otherwise not-logged in users could do actions. If all of your pages should only
        // be usable by logged-in users: Put this line into libs/Controller->__construct
        //Auth::checkAuthentication();
    }

    /**
     * This method controls what happens when you move to /note/index in your app.
     * Gets all notes (of the user).
     */
    /*public function index()
    {
        $this->View->render('note/index', array(
            'notes' => NoteModel::getAllNotes()
        ));
    }*/

    /**
     * This method controls what happens when you move to /dashboard/create in your app.
     * Creates a new team. This is usually the target of form submit actions.
     * POST request.
     */
    public function create()
    {  
        $tourPin = Request::post('tourPin');
        $teamName = Request::post('teamName');
        $memberName = Request::post('memberName');

        $response = ['feedback'=>'','success'=>false,'csrf'=>Csrf::makeToken(),'status'=>1];
        // check access to tour by pin
        if (!empty($tourPin)){
            $tour = TourModel::getTourByPin($tourPin);
        } else {
            $tour = false;
        }

        if ($tour){
           // check validity of team name: must be unique within tour
            $teamNameOK = TeamModel::validateNewNameOK($teamName,$tour->id);
            if (!$teamNameOK){
                $response['feedback'] .= Text::get('FEEDBACK_TEAM_NAME_INVALID') . ".";
            }

            $memberNameOK = MemberModel::validateNewNameOK($memberName);
            if (!$memberNameOK){
                $response['feedback'] .= Text::get('FEEDBACK_MEMBER_NAME_INVALID') . ".";
            }

            if ($teamNameOK && $memberNameOK){
                $teamId = TeamModel::createTeam($tour->id,$teamName);
                if ($teamId){
                    $team = TeamModel::getTeam($teamId);
                    $memberId = MemberModel::createMember($teamId,$memberName);
                    if ($memberId){
                        $member = MemberModel::getMember($memberId);
                        Cookie::set('memberToken',$member->token);
                        $response['memberToken'] = $member->token;
                        $response['memberName'] = $member->name;
                        $response['teamName'] = $team->name;
                        $response['teamPin'] = $team->pin;
                        $response['tourName'] = $tour->name;
                        $response['success'] = true;
                    }
                }
            }     
        } else {
            $response['feedback'] = Text::get('FEEDBACK_TOUR_PIN_INVALID');
        }

        $json = json_encode($response);
        header("Content-type:application/json");
        echo $json;
    }

    public function join()
    {  
        $teamPin = Request::post('teamPin');
        $memberName = Request::post('memberName');

        $response = ['feedback'=>'','success'=>false,'csrf'=>Csrf::makeToken(),'status'=>1];
        // check access to team by pin
        if (!empty($teamPin)){
            $team = TeamModel::getTeamByPin($teamPin);
        } else {
            $team = false;
        }

        if ($team){
            $tour = TourModel::getTour($team->tour_id);
            $memberNameOK = memberModel::validateNewNameOK($memberName,$team->id);
            if (!$memberNameOK){
                $response['feedback'] .= Text::get('FEEDBACK_MEMBER_NAME_INVALID') . ".";
            }

            if ($memberNameOK){
                $member = MemberModel::getMemberByCheckedToken();
                if ($member) {
                    MemberModel::updateMember($member->id, $team->id,$memberName);
                    $member = MemberModel::getMember($member->id);
                } else {
                    $memberId = MemberModel::createMember($team->id,$memberName);
                    $member = MemberModel::getMember($memberId);
                }
                if ($member){
                        Cookie::set('memberToken',$member->token);
                        $response['memberToken'] = $member->token;
                        $response['memberName'] = $member->name;
                        $response['teamName'] = $team->name;
                        $response['teamPin'] = $team->pin;
                        $response['tourName'] = $tour->name;
                        $response['success'] = true;
                }
            }
        } else {
            $response['feedback'] = Text::get('FEEDBACK_TEAM_PIN_INVALID');
        }

        $json = json_encode($response);
        header("Content-type:application/json");
        echo $json;
    }

    /**
     * This method controls what happens when you move to /note/edit(/XX) in your app.
     * Shows the current content of the note and an editing form.
     * @param $note_id int id of the note
     */
    /*public function edit($note_id)
    {
        $this->View->render('note/edit', array(
            'note' => NoteModel::getNote($note_id)
        ));
    }*/

    /**
     * This method controls what happens when you move to /note/editSave in your app.
     * Edits a note (performs the editing after form submit).
     * POST request.
     */
    /*public function editSave()
    {
        NoteModel::updateNote(Request::post('note_id'), Request::post('note_text'));
        Redirect::to('note');
    }*/

    /**
     * This method controls what happens when you move to /note/delete(/XX) in your app.
     * Deletes a note. In a real application a deletion via GET/URL is not recommended, but for demo purposes it's
     * totally okay.
     * @param int $note_id id of the note
     */
    /*public function delete($note_id)
    {
        NoteModel::deleteNote($note_id);
        Redirect::to('note');
    }*/
}
