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
}
