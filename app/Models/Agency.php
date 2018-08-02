<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use App\Helpers\Helper;

class Agency extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'agency';

    /**
     * Primary key of table.
     *
     * @var string
     */
    protected $primaryKey = "id";

    public function agency_service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /**
     * get the list of agencies.
     *
     * @param integer $take number of records need to fetch, used for displaying data only.
     * @param integer $skip number of records need to skip, used for displaying data only.
     * @param string $search filter criteria's.
     * @param string $sortField name of column to sort, used for displaying data only.
     * @param string $sortDirection order of column, used for displaying data only.
     *
     * @return object of agencies
     */
    public function getAgency($take = 10, $skip = 0, $search = '', $sortField = 'created_at', $sortDirection = 'desc')
    {
        $query = $this->getQuery($search);

        return $query->select('a.id', 'a.name', 'a.created_at', 's.name as service_type')
                                ->skip($skip)->take($take)
                                ->orderBy($sortField, $sortDirection)
                                ->get();
    }

    /**
     * get the count of agencies.
     *
     * @param string $search filter criteria's.
     *
     * @return count of agencies
     */
    public function getAgencyCount($search)
    {
        $query = $this->getQuery($search);

        return $query->select('a.id')
                                ->count();
    }

    /**
     * return query to get agency list.
     *
     * @param string $search filter criteria's.
     *
     * @return string $query.
     */
    private function getQuery($search)
    {
        $query = DB::table($this->table . ' as a')
                        ->join('services as s', 'a.service_id', '=', 's.id');

        if(!empty($search)){
            $query = $query->where(function($query) use ($search)
            {
                $query = $query->where('a.name', 'LIKE', '%' . $search . '%')
                    ->orwhere('s.name', 'LIKE', '%' . $search . '%');
            });
        }

        return $query;
    }

    /**
     * get the list of all Agency (which have future open slots) exist in DB for selected ids.
     * It is used for schedule appointment.
     *
     * @param string $service_ids.
     *
     * @return array of Agency by service Ids.
     */
    public function getAgenciesByQuestionIds($questionIds)
    {
        return DB::table($this->table . ' as a')
            ->join('service_questions as sq', 'a.service_id', '=', 'sq.service_id')
            ->select('a.id', 'a.name', 'a.address', 'a.contact_info', 'a.website', 'a.htmlcontent', 'a.image_path')
            ->whereIn('sq.question_id', $questionIds)
            ->groupBy('a.service_id')
            ->orderBy('a.service_id')
            ->get()
            ->all();
    }

    /**
     * get the list of all Agency exist in DB for response id.
     * It is used for reschedule appointment.
     *
     * @param string $responseId.
     *
     * @return array of Agency by response Id.
     */
    public function getAgencyForBooking($responseId)
    {
        $gap_hours = Config::get('app.future_events_after_hours');
        $future_date = Helper::getESTDateTimeFromUTC(Carbon::now()->addHours($gap_hours));

        $data =  DB::table($this->table. ' as a')
                    ->leftJoin('users as u', 'a.id', '=', 'u.agency_id')
                    ->join("response_details as rd",function($join){
                        $join->on("rd.service_id","=","a.service_id")
                            ->where("rd.answer",'1');
                    })
                    ->join("questions as q",function($join){
                        $join->on("rd.question_id","=","q.id")
                            ->where("q.service_identifier", '1');
                    })
                    ->leftJoin("schedules as sc", function($join)use($future_date){
                        $join->on("u.id", "=", "sc.user_id")
                            ->on("sc.booked_by", "=", DB::raw("'0'"))
                            ->on(DB::raw("CONCAT(sc.date, ' ', sc.start_time)"), ">=", DB::raw("'$future_date'"));
                    })
                    ->select(DB::raw('DISTINCT a.*, COUNT(DISTINCT sc.id) AS available_slots'))
                    ->where('rd.response_id', $responseId)
                    ->groupBy('a.id')
                    ->orderBy('a.service_id', 'asc')
                    ->get()->all();

        return $data;
    }
}
