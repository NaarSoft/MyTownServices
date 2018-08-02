<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Question extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'questions';

    /**
     * Primary key of table.
     *
     * @var string
     */
    protected $primaryKey = "id";
    
    /**
     * get the list of all Questions exist in DB.
     * It is used for building the questionnaire wizard.
     *
     * @param string $responseId.
     * @param string $serviceId.
     * @param bool $view_response.
     *
     * @return array of questions.
     */
    public function getQuestionsByServiceId($responseId = null, $serviceId = null, $view_response=false)
    {
        $query = DB::table($this->table. ' as q')
                ->select('q.show_if', 'q.service_id', 'q.id', 'q.text', 'q.parent_question_id', 'q.visible', 'q.position', 'q.condition1', 'q.is_text', 'rd.answer')
                ->leftJoin("response_details as rd",function($join)use($responseId){
                    $join->on("rd.question_id", "=", "q.id")
                        ->on("rd.response_id", "=", DB::raw("'$responseId'"));
                });

        if($serviceId < 9)
            $query = $query->where('q.service_id', $serviceId);

        if($view_response)
            $query = $query->orWhere('q.service_id', null);

        $data = $query->orderBy('q.visible', 'desc')
                ->orderBy('q.position', 'asc')
                ->get()->all();
        //->toSql();
        //dd($data);
        return $data;
    }

    public function getQuestions()
    {
        return DB::table($this->table . ' as q')
            ->leftJoin('service_questions as sq', 'q.id', '=', 'sq.question_id')
            ->select('q.id', 'q.text', DB::raw('GROUP_CONCAT(sq.service_id) as service_ids'))
            ->where('q.visible', 1)
            ->groupBy('q.id')
            ->orderBy('q.sort_order')
            ->get();
    }
}
