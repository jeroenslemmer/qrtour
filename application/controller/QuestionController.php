<?php

/**
 * The note controller: Just an example of simple create, read, update and delete (CRUD) actions.
 */
class QuestionController extends Controller
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

    public function index(){
        $questions = QuestionModel::getAllQuestions();
        $json = json_encode($questions);
        header("Content-type:application/json");
        echo $json;       
    }

    public function get()
    {
        $questionCode = Request::post('questionCode');

        $response = ['feedback'=>'','success'=>false,'csrf'=>Csrf::makeToken()];
        $member = MemberModel::getMemberByCheckedToken();
        if ($member){
            $team = TeamModel::getTeam($member->team_id);
            if ($team){
                $question = QuestionModel::getQuestionByTourIdAndCode($team->tour_id, $questionCode);
                if ($question){
                    $answer = QuestionModel::getTeamQuestionAnswer($question->id, $team->id);
                    if (!$answer){
                        $options = QuestionModel::getOptions($question->id);
                        if ($options){
                            $response['code'] = $questionCode;
                            $response['title'] = $question->title;
                            $response['location'] = $question->location;
                            $response['description'] = $question->description;
                            $response['options'] = $options;
                            $response['success'] = true;
                        } else {
                            $response['feedback'] .= Text::get('FEEDBACK_QUESTION_INVALID') . ".";                    
                        }
                    } else {
                        $response['feedback'] .= Text::get('FEEDBACK_QUESTION_ANSWERED') . ".";                    
                    }
                } else {
                    $response['feedback'] .= Text::get('FEEDBACK_QUESTION_CODE_INVALID') . "."; 
                }
            }
        }
        $json = json_encode($response);
        header("Content-type:application/json");
        echo $json;
    }

    public function answer()
    {
        $questionCode = Request::post('questionCode');
        $optionId = Request::post('optionId');

        $response = ['feedback'=>'','success'=>false,'csrf'=>Csrf::makeToken()];
        $member = MemberModel::getMemberByCheckedToken();
        if ($member){
            $team = TeamModel::getTeam($member->team_id);
            if ($team){
                $question = QuestionModel::getQuestionByTourIdAndCode($team->tour_id, $questionCode);
                if ($question){
                    $answer = QuestionModel::getTeamQuestionAnswer($question->id, $team->id);
                    if (!$answer){
                        QuestionModel::answer($optionId, $team->id);
                        $option = QuestionModel::getOption($optionId);
                        if ($option->correct){
                            $response['feedback'] .= Text::get('FEEDBACK_QUESTION_CORRECT_ANSWERED') . ".";
                        } else {
                           $response['feedback'] .= Text::get('FEEDBACK_QUESTION_FALSE_ANSWERED') . ".";

                        }
                    } else {
                        $response['feedback'] .= Text::get('FEEDBACK_QUESTION_ANSWERED') . ".";                    
                    }
                } else {
                    $response['feedback'] .= Text::get('FEEDBACK_QUESTION_CODE_INVALID') . "."; 
                }
            }
        }

        $json = json_encode($response);
        header("Content-type:application/json");
        echo $json;

    }

      /*public function edit($note_id)
    {
        $this->View->render('note/edit', array(
            'note' => NoteModel::getNote($note_id)
        ));
    }*/

    public function editSave()
    {
        var_dump($_POST);
        $id = Request::post('id');
        $title = Request::post('title');
        $location = Request::post('location');
        $description = Request::post('description');

        QuestionModel::updateQuestion($id, $title,$location,$description);
    }

    /*public function delete($note_id)
    {
        NoteModel::deleteNote($note_id);
        Redirect::to('note');
    }*/
}
