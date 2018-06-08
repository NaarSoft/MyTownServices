<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use DB;

class Holiday extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'holidays';

    /**
     * Primary key of table.
     *
     * @var string
     */
    protected $primaryKey = "id";

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['day', 'created_at', 'updated_at'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'day'=> 'date'
    ];

    /**
     * Get the formatted date to display.
     *
     * @return string
     */
    function getDayAttribute()
    {
        if($this->attributes['day'] != '')
            return Carbon::parse($this->attributes['day'])->format('m/d/Y');
        else
            return null;
    }

    /**
     * get the list of holidays.
     *
     * @param string $year.
     *
     * @return object of holiday.
     */
    public function getHoliday($year)
    {
        $data = DB::table($this->table)
                ->select(DB::Raw('id, name, DATE_FORMAT(day, "%m/%d/%Y") as day') )
                ->whereYear("day", $year)
                ->orderBy('day')
                ->get();
        return $data;
    }

    /**
     * get the count of holidays.
     *
     * @param string $year.
     *
     * @return count of holiday.
     */
    public function getHolidayCount($year)
    {
        $data = DB::table($this->table)
                ->select('id')
                ->whereYear("day", $year)
                ->count();
        return $data;
    }

    /**
     * save holiday.
     *
     * @param Request $request.
     * @param bool $delete_schedule.
     *
     * @return bool $success true/false.
     */
    public function addHoliday($request, $delete_schedule)
    {
        $success = false;
        $day = date_create($request->day);

        DB::beginTransaction();
        try {
            if($delete_schedule == 1) {
                $schedule = new Schedule();
                $schedule->deleteEmptySchedule($day);
            }

            $holiday = new Holiday();
            $holiday->name = $request->name;
            $holiday->day = $day->format('Y-m-d');
            $holiday->created_by = \Auth::user()->id;
            $holiday->save();

            DB::commit();
            $success = true;
        } catch (\Exception $ex) {
            DB::rollback();
            Log::error('Error :'. $ex);
        }
        return $success;
    }
}
