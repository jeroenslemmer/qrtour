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

    /**
     * This method controls what happens when you move to /note/index in your app.
     * Gets all notes (of the user).
     */
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
                            $response['feedback'] .= Text::get('FEEDBACK_QUESTION_INVALID') . "<br>";                    
                        }
                    } else {
                        $response['feedback'] .= Text::get('FEEDBACK_QUESTION_ANSWERED') . "<br>";                    
                    }
                } else {
                    $response['feedback'] .= Text::get('FEEDBACK_QUESTION_CODE_INVALID') . "<br>"; 
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
                            $response['feedback'] .= Text::get('FEEDBACK_QUESTION_CORRECT_ANSWERED') . "<br>";
                        } else {
                           $response['feedback'] .= Text::get('FEEDBACK_QUESTION_FALSE_ANSWERED') . "<br>";

                        }
                    } else {
                        $response['feedback'] .= Text::get('FEEDBACK_QUESTION_ANSWERED') . "<br>";                    
                    }
                } else {
                    $response['feedback'] .= Text::get('FEEDBACK_QUESTION_CODE_INVALID') . "<br>"; 
                }
            }
        }

        
        $json = json_encode($response);
        header("Content-type:application/json");
        echo $json;

    }

    /**
     * This method controls what happens when you move to /dashboard/create in your app.
     * Creates a new team. This is usually the target of form submit actions.
     * POST request.
     */


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
