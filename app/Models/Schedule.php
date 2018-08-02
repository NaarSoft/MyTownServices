<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use PDO;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;

class Schedule extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'schedules';

    /**
     * Primary key of table.
     *
     * @var string
     */
    protected $primaryKey = "id";

    /**
     * delete slots.
     *
     * @param int $id.
     *
     * @return int status.
     */
    public function deleteSlot($id)
    {
        try {
            $slot = DB::table($this->table)
                ->where('id', $id)
                ->where('booked_by','0')
                ->count();

            if($slot > 0 ){
                DB::table($this->table)
                    ->where('id', $id)
                    ->delete();
                return 1;
            }else{
                return 2;
            }
        }
        catch(\Exception $ex) {
            return 0;
        }
    }

    /**
     * save schedule for selected users.
     *
     * @param array $schedules.
     * @param array $users.
     * @param date $start_date.
     * @param date $end_date.
     * @param date $working_days.
     *
     * @throws \Exception
     */
    public function saveSchedules($schedules, $users, $start_date, $end_date, $working_days)
    {
        DB::beginTransaction();
        try {
            $booked_dates = DB::table($this->table)
                ->where('booked_by', '>', '0')
                ->whereIn('user_id', $users)
                ->whereBetween('date', [$start_date, $end_date])
                ->distinct()
                ->pluck('date')->toArray();

            if(count($booked_dates) > 0){
                foreach ($booked_dates as $date) {
                    foreach ($users as $user_id) {
                        $count = DB::table($this->table)
                            ->where('date', $date)
                            ->where('user_id', $user_id)
                            ->where('booked_by', '>', '0')
                            ->count();

                        if ($count == 0) {
//                                DB::table($this->table)
//                                    ->where('user_id', $user_id)
//                                    ->where('date', $date)
//                                    ->delete();
                            DB::delete('DELETE FROM schedules WHERE user_id = ' . $user_id . ' AND date = "' . $date . '" AND WEEKDAY(date) IN (' . $working_days . ')');
                        }
                    }
                }
            }

//            $query = DB::table($this->table)
//                ->whereIn('user_id', $users)
//                ->whereBetween('date', [$start_date, $end_date])
//                ->whereNotIn('date', $booked_dates)
//                ->where(DB::raw('WEEKDAY(date)'), $working_days)
//                ->toSql();

            $query = 'DELETE FROM schedules WHERE date BETWEEN "'. $start_date .'" AND "'. $end_date .'"';

            if(!is_null($users) && !empty($users)){
                if(count($users) == 1)
                    $query .= ' AND user_id = '. $users[0];
                else
                    $query .= ' AND user_id IN ('. implode(',', $users) .')';
            }

            if(!is_null($booked_dates) && !empty($booked_dates)) {
                $booked_dates = implode('", "', $booked_dates);
                $booked_dates = '"' . $booked_dates . '"';
                $query .= ' AND date NOT IN ('. $booked_dates .')';
            }

            if(trim($working_days) != '') {
                $query .= ' AND WEEKDAY(date) IN ('. $working_days . ')';
            }

            DB::delete($query);

            //->get()->toArray();
            //->toSql();
            //->getBindings();

            DB::table($this->table)->insert($schedules);
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }

    /**
     * get schedule for selected users.
     *
     * @param array $users
     * @param date $start_date.
     *
     * @return schedules.
     */
    public function getSchedules($users, $start_date)
    {
        return DB::table($this->table . ' as s')
            ->leftJoin('users as u', 's.user_id', '=', 'u.id')
            ->select(DB::RAW('s.id, user_id as user, CONCAT(first_name, " ", last_name) AS user_name, CONCAT(date, " ", start_time) AS start, CONCAT(date, " ", end_time) AS end, s.date, CASE booked_by WHEN 0 THEN u.schedule_color ELSE "#D3D3D3" END AS color, booked_by, CASE booked_by WHEN 0 THEN "white" ELSE "black" END AS textColor'))
            ->where('user_id', $users)
            ->where('date', '>=', $start_date)
            ->get();
    }

    /**
     * get booked schedules by user.
     *
     * @param int $user_id.
     * @param date $start_date.
     * @param date $end_date.
     *
     * @return schedules.
     */
    public function getAppointments($user_id, $start_date, $end_date)
    {
        $query = DB::table($this->table . ' as s')
            ->join('users as u', 's.user_id', '=', 'u.id')
            ->join('responses as r', 's.booked_by', '=', 'r.id')
            ->select(DB::RAW('s.id, r.id as response_id, user_id as user, CONCAT(date, " ", start_time) AS start, CONCAT(date, " ", end_time) AS end, s.date, u.schedule_color AS color, r.name as title'))
            ->where('booked_by', '!=', '0')
            ->whereBetween('date', [$start_date, $end_date]);

        if($user_id != null)
        {
            $query = $query->where('user_id', $user_id);
        }

        return $query->get();
    }

    /**
     * save appointment for respondent.
     *
     * @param string $scheduleIds.
     * @param string $responseId.
     * @param string $requestId.
     *
     * @throws \Exception
     */
    public function bookAppointment($scheduleIds, $responseId, $locationId, $requestId)
    {
        DB::beginTransaction();
        try {

            DB::table('responses')
                ->where('id', $responseId)
                ->update(['request_id' => $requestId, 'updated_at' => Carbon::now()]);

            foreach($scheduleIds as $scheduleId){
                DB::table($this->table)
                    ->where('id', $scheduleId)
                    ->update(['booked_by' => $responseId, 'location_id' => $locationId]);
            }

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }

    /**
     * get scheduled appointment data by response id.
     *
     * @param string $responseId.
     *
     * @return schedule.
     */
    public function getScheduledAppointments($responseId)
    {
        DB::setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_GROUP);

        $data = DB::table($this->table . ' as s')
            ->join('users as u', 'u.id', '=', 's.user_id')
            ->join('agency as a', 'a.id', '=', 'u.agency_id')
            ->join('responses as r', 'r.id', '=', 's.booked_by')
            ->join('locations as l', 'l.id', '=', 's.location_id')
            ->select(DB::raw('a.id AS agency_id, a.name as agency_name, a.contact_info, a.image_path, a.service_id, DATE_FORMAT(s.start_time, "%h:%i %p") AS start_time, DATE_FORMAT(s.date, "%b, %d, %Y") AS date,
                            DATE_FORMAT(s.end_time, "%h:%i %p") AS end_time, u.email as user_email, r.name AS respondent, r.email_address as respondent_email_address, s.id as schedule_id, l.location'))
            ->where('s.booked_by', $responseId)
            ->orderBy('s.start_time')
            ->get();

        DB::setFetchMode(PDO::FETCH_CLASS);
        return $data;
    }

    /**
     * get the list of selected Agencies exist in DB saved for respondent at the time of book appointment.
     *
     * @param string $responseId.
     *
     * @return agencies.
     */
    public function getAgencyForRescheduling($responseId)
    {
        $gap_hours = Config::get('app.future_events_after_hours');
        $future_date = Carbon::now()->addHours($gap_hours)->format('Y-m-d H:i:s');

        $data = DB::table($this->table . ' as s')
            ->join('users as u', 'u.id', '=', 's.user_id')
            ->join('agency as a', 'a.id', '=', 'u.agency_id')
            ->join('responses as r', 'r.id', '=', 's.booked_by')
            ->leftJoin("schedules as sc", function($join)use($future_date){
                $join->on("u.id", "=", "sc.user_id")
                    ->on("sc.booked_by", "=", DB::raw("'0'"))
                    ->on(DB::raw("CONCAT(sc.date, ' ', sc.start_time)"), ">=", DB::raw("'$future_date'"));
            })
            ->select(DB::raw('a.*, COUNT(sc.id) AS available_slots'))
            ->where('s.booked_by', $responseId)
            ->groupBy('a.id')
            ->orderBy('a.name')
            ->get();

        return $data;
    }

    /**
     * save cancellation reason and cancel scheduled appointment for respondent by admin.
     *
     * @param string $reason.
     * @param integer $responseId.
     *
     * @throws \Exception
     */
    public function cancelAppointmentAndSchedule($reason, $responseId)
    {
        DB::beginTransaction();
        try {
            $this->saveCancelledAppointments($responseId);

            DB::table('responses')
                ->where('id', $responseId)
                ->update(['cancellation_reason' => $reason]);

            DB::table($this->table)
                ->where('booked_by', $responseId)
                ->update(['booked_by' => '0']);

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }

    /**
     * save schedule for selected scheduleIds for respondent.
     *
     * @param string $scheduleIds.
     * @param string $responseId.
     *
     * @throws \Exception
     */
    public function rescheduleAppointment($scheduleIds, $responseId)
    {
        DB::beginTransaction();
        try {
            DB::table($this->table)
                ->where('booked_by', $responseId)
                ->update(['booked_by' => 0]);

            foreach($scheduleIds as $scheduleId){
                DB::table($this->table)
                    ->where('id', $scheduleId)
                    ->update(['booked_by' => $responseId]);
            }

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }

    /**
     * To check appointment on any day.
     *
     * @param date $date.
     *
     * @return count of booked and empty slots.
     */
    public function checkAppointmentOnDate($date)
    {
        return DB::table($this->table . ' as s')
            ->select(DB::RAW('COUNT(CASE WHEN booked_by > 0 THEN id END) AS appointment_count, COUNT(id) AS schedule_count'))
            ->whereDate('date', '=', $date)
            ->get()->all();
    }

    /**
     * delete schedule for selected date.
     *
     * @param date $date.
     *
     * @return schedule.
     */
    public function deleteEmptySchedule($date)
    {
        return DB::table($this->table)
            ->whereDate('date', '=', $date)
            ->delete();
    }

    private function saveCancelledAppointments($responseId){
        try {
            $data = DB::table($this->table)
                ->select('id as schedule_id', 'user_id','date', 'start_time', 'end_time', 'booked_by', 'created_by')
                ->where('booked_by', $responseId)
                ->get()->toArray();

            $schedules = json_decode(json_encode($data), true);

            DB::table('cancelled_appointments')->insert($schedules);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function getUserAvailableTimeSlots($userId, $dateTime)
    {
        $date = date('Y-m-d', strtotime($dateTime));
        try{
             return DB::table($this->table)
                ->select('start_time')
                ->where('user_id', '=', $userId)
                ->where("booked_by", "=", DB::raw("'0'"))
                ->where('date', '=', $date)
                ->where(DB::raw("CONCAT(date, ' ', start_time)"), ">=", DB::raw("'$dateTime'"))
                ->get()->all();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function getBookedTimeSlots($userId, $dateTime, $notAtLocationId)
    {
        $date = date('Y-m-d', strtotime($dateTime));
        try {
            return DB::table($this->table)
                ->select('start_time')
                ->where('user_id', '=', $userId)
                ->where("booked_by", '!=', DB::raw("'0'"))
                ->where('location_id', '!=', $notAtLocationId)
                ->where('date', '=', $date)
                ->where(DB::raw("CONCAT(date, ' ', start_time)"), ">=", DB::raw("'$dateTime'"))
                ->get()->all();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function getAvailableScheduleId($agencyId, $date, $time)
    {
        try {
            return DB::table($this->table . ' as s')
                ->join('users as u', 's.user_id', '=',  'u.id')
                ->select('s.id')
                ->where('u.agency_id', $agencyId)
                ->where('u.active', 1)
                ->where('s.date', '=' , $date)
                ->where('s.start_time', '=', $time)
                ->where('s.booked_by', '=', 0)
                ->orderBy('s.id')
                ->get()->first();
        } catch(\Exception $ex) {
            throw $ex;
        }
    }

    public function getFutureAppointmentsCount($locationId)
    {
        try{
            return DB::table($this->table)
                ->select('id')
                ->where('location_id', '=', $locationId)
                ->where('booked_by', '!=', 0)
                ->where(DB::raw("CONCAT(date, ' ', start_time)"), ">=", DB::raw('now()'))
                ->count();
        } catch(\Exception $ex) {
            throw $ex;
        }
    }

    public function getAppointmentTimeSlotsCount($agencyId)
    {
        try{
            $gap_hours = Config::get('app.future_events_after_hours');
            $nextDateTime = Carbon::now()->addHours($gap_hours)->format('Y-m-d H:i:s');
            return DB::table($this->table . ' as s')
                ->join('users as u', 'u.id', '=', 's.user_id')
                ->select('id')
                ->where('u.agency_id', '=', $agencyId)
                ->where('s.booked_by', '=', 0)
                ->where(DB::raw("CONCAT(s.date, ' ', s.start_time)"), ">=", $nextDateTime)
                ->count();
        } catch(\Exception $ex) {
            throw $ex;
        }
    }

    public function getFirstOpenTimeSlot($locationId, $agencyIds)
    {

    }
}
