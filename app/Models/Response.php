<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DB;

class Response extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'responses';

    /**
     * Primary key of table.
     *
     * @var string
     */
    protected $primaryKey = "id";

    /**
     * save questionnaire basic info data in responses table.
     *
     * @param array $response_array
     *
     * @return id of response.
     */
    public function saveResponse($response_array)
    {
        return $insertedId = DB::table($this->table)
            ->insertGetId($response_array);
    }

    /**
     * update questionnaire basic info data by response id.
     *
     * @param array $response_array
     * @param int @$responseId id.
     */
    public function updateResponse($response_array, $responseId)
    {
        DB::table($this->table)
            ->where('id', $responseId)
            ->update($response_array);
    }

    /**
     * get the list of responses.
     *
     * @param integer $user_id logged in user id, used for displaying data only.
     * @param bool $show_all check box to show cancelled and incomplete records, used for displaying data only.
     * @param integer $take number of records need to fetch, used for displaying data only.
     * @param integer $skip number of records need to skip, used for displaying data only.
     * @param string $search filter criteria's.
     * @param string $sortField name of column to sort, used for displaying data only.
     * @param string $sortDirection order of column, used for displaying data only.
     *
     * @return object of responses.
     */
    public function getResponses($user_id, $show_all, $take = 10, $skip = 0, $search = '', $sortField = 'r.name', $sortDirection = 'desc')
    {
        $query = $this->getQuery($user_id, $show_all, $search);

        $data1 = $query
                ->select(DB::RAW('r.id, r.name, r.gender, r.age, DATE_FORMAT(CONCAT(sc.date, " ", sc.start_time), "%b, %d, %Y %h:%i %p") AS appointment_time, CONCAT(sc.date, " ", sc.start_time) as appointment_date_time,
                                    DATE_FORMAT(CONVERT_TZ(r.updated_at, "UTC","America/New_York"), "%b, %d, %Y %h:%i %p") as updated_at,
                                    CASE WHEN r.cancellation_reason IS NOT NULL THEN "Cancelled"
                                         WHEN r.request_id IS NOT NULL THEN "Scheduled"
                                         ELSE "Incomplete" END AS status,
                                    (SELECT GROUP_CONCAT(s.name) FROM `schedules` AS sc1
                                        INNER JOIN users AS u ON sc1.user_id = u.id
                                        INNER JOIN agency AS a ON u.agency_id = a.id
                                        INNER JOIN services AS s ON a.service_id = s.id
                                        WHERE sc1.booked_by = sc.booked_by
                                        ) AS services'));

        if(!is_null($user_id) && $show_all == 1){
            $unionQuery = $this->getUnionQuery($user_id, $search);
            $data2 = $unionQuery
                ->select(DB::RAW('r.id, r.name, r.gender, r.age, "" AS appointment_time, "" as appointment_date_time,
                                    DATE_FORMAT(CONVERT_TZ(r.updated_at, "UTC","America/New_York"), "%b, %d, %Y %h:%i %p") as updated_at,
                                    "Cancelled" AS status, "" AS services'));
            $results = $data2->union($data1)->skip($skip)->take($take)
                ->orderBy($sortField, $sortDirection)->get();
        }else{
            $results = $data1->skip($skip)->take($take)
                ->orderBy($sortField, $sortDirection)->get();
        }

        return $results;
    }

    /**
     * get the count of responses.
     *
     * @param integer $user_id logged in user id, used for displaying data only.
     * @param bool $show_all check box to show cancelled and incomplete records, used for displaying data only.
     * @param string $search filter criteria's.
     *
     * @return count of responses.
     */
    public function getResponseCount($user_id, $show_all, $search)
    {
        $count2 = 0;
        $query = $this->getQuery($user_id, $show_all, $search);
        $count1 = DB::table( DB::raw("({$query->select('r.id')->toSql()}) as sub"))->count();

        if(!is_null($user_id) && $show_all == 1) {
            $unionQuery = $this->getUnionQuery($user_id, $search);
            $count2 = DB::table( DB::raw("({$unionQuery->select('r.id')->toSql()}) as sub"))->count();
        }
        $results = $count1 + $count2;

        return $results;
    }

    /**
     * return query to get responses list.
     *
     * @param integer $user_id logged in user id, used for displaying data only.
     * @param bool $show_all check box to show cancelled and incomplete records, used for displaying data only.
     * @param string $search filter criteria's.
     *
     * @return string $query.
     */
    private function getQuery($user_id, $show_all, $search)
    {
        if($show_all == 1){
            $query = DB::table('responses as r')
                ->leftjoin('schedules as sc', 'sc.booked_by', '=', 'r.id')
                ->leftjoin('users as u', 'sc.user_id', '=', 'u.id')
                ->leftjoin('agency as a', 'u.agency_id', '=', 'a.id')
                ->leftjoin('services as s', 'a.service_id', '=', 's.id')
                ->groupBy('r.id');
        }else{
            $query = DB::table('responses as r')
                ->join('schedules as sc', 'sc.booked_by', '=', 'r.id')
                ->join('users as u', 'sc.user_id', '=', 'u.id')
                ->join('agency as a', 'u.agency_id', '=', 'a.id')
                ->join('services as s', 'a.service_id', '=', 's.id')
                ->groupBy('r.id');
        }

        if(!is_null($user_id)){
            $query = $query->where('sc.user_id', DB::Raw("$user_id"));
        }

        if(!empty($search)){
            $query = $query->where('r.name', 'LIKE', DB::Raw("'%$search%'"));
        }

        return $query;
    }

    /**
     * save cancellation reason for respondent before scheduling.
     *
     * @param string $reason.
     * @param integer $responseId.
     *
     */
    public function cancelAppointment($reason, $responseId)
    {
        DB::table($this->table)
            ->where('id', $responseId)
            ->update(['cancellation_reason' => $reason, 'updated_at' => Carbon::now()]);
    }

    /**
     * return query to get responses list.
     *
     * @param integer $user_id logged in user id, used for displaying data only.
     * @param string $search filter criteria's.
     *
     * @return string $query.
     */
    private function getUnionQuery($user_id, $search)
    {
        $unionQuery = DB::table('responses as r')
            ->leftjoin('cancelled_appointments as ca', 'ca.booked_by', '=', 'r.id')
            ->leftjoin('users as u', 'ca.user_id', '=', 'u.id')
            ->leftjoin('agency as a', 'u.agency_id', '=', 'a.id')
            ->leftjoin('services as s', 'a.service_id', '=', 's.id')
            ->groupBy('r.id')
            ->where('ca.user_id', DB::Raw("$user_id"));

        if(!empty($search)){
            $unionQuery = $unionQuery->where('r.name', 'LIKE', DB::Raw("'%$search%'"));
        }

        return $unionQuery;
    }
}
