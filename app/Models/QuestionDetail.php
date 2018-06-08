<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class QuestionDetail extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'question_detail';

    /**
     * get the list of all Question detail exist in DB.
     * It is used for building the questionnaire wizard.
     *
     * @param string $serviceId.
     *
     * @return array of question details.
     */
    public function getQuestionDetailsByServiceId($serviceId = null)
    {
        $data = DB::table($this->table. ' as qd')
            ->leftJoin('questions as q', 'qd.question_id', '=', 'q.id')
            ->select('qd.question_id', 'qd.detail', 'qd.condition1')
            ->orderBy('qd.question_id', 'asc')
            ->where('q.service_id', $serviceId)
            ->get()->all();
        return $data;
    }
}
