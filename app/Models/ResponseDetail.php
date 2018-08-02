<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class ResponseDetail extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'response_details';

    /**
     * Primary key of table.
     *
     * @var string
     */
    protected $primaryKey = "response_id";


    /**
     * save questionnaire wizard data in response_details table.
     *
     * @param array $response_detail_array
     * @param int @$responseId id of response
     * @param int @$serviceId id of service
     *
     * @throws \Exception
     */
    public function saveResponseDetailsById($response_detail_array, $responseId, $serviceId)
    {
        DB::beginTransaction();
        try {
            DB::table($this->table)->where('service_id', $serviceId)
                ->where('response_id', $responseId)
                ->delete();
            DB::table($this->table)->insert($response_detail_array);
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }

    /**
     * @param $responseDetails
     * @throws \Exception
     */
    public function saveResponseDetails($responseDetails)
    {
        DB::beginTransaction();
        try {
            DB::table($this->table)->insert($responseDetails);
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }

    /**
     * update response_details in DB by response id and question id.
     *
     * @param int $responseId id of response
     * @param int $questionId id of question
     * @param bool $answer of response
     */
    public function updateResponseDetails($responseId, $questionId, $answer)
    {
        DB::table($this->table)
            ->where('response_id', $responseId)
            ->where('question_id', $questionId)
            ->update(['answer'=> $answer]);
    }

    public function getResponseDetails($responseId)
    {
        return DB::table($this->table. ' as rd')
            ->join('questions as q', 'q.id', '=', 'rd.question_id')
            ->leftjoin('service_questions as sq', 'q.id', '=', 'sq.question_id')
            ->select('rd.question_id', 'q.text', 'rd.answer', DB::raw('GROUP_CONCAT(sq.service_id) as service_ids'))
            ->where('rd.response_id', $responseId)
            ->groupBy('q.id')
            ->orderBy('q.sort_order')
            ->get();
    }
}
