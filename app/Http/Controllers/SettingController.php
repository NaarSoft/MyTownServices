<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Http\Requests\SettingFormRequest;
use App\Http\Requests\HolidayFormRequest;
use App\Models\Holiday;
use App\Models\Setting;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    /**
     * Return Setting Index view.
     *
     * @return view
     */
    public function index()
    {
        $setting = Setting::first();
        $yearList = array();

        for($year = date('Y')-5; $year<= date('Y')+5 ;$year++)
            $yearList["$year"] = $year;

        return view('admin.setting.index')->with(['setting' => $setting, 'yearList' => $yearList]);
    }

    /**
     * Create setting and redirect to index page.
     *
     * @param SettingFormRequest $request
     *
     * @return view
     */
    public function save(SettingFormRequest $request)
    {
        $setting = new Setting();

        // If record exists then update otherwise create
        if(isset($request->id)){
            $setting = Setting::find($request->id);
            $setting->id = $request->id;
            $setting->updated_by = Auth::user()->id;
        }else{
            $setting->created_by = Auth::user()->id;
        }

        if(isset($request->startTimeHour)) {
            $setting->office_start_time = $request->startTimeHour;
            $setting->office_start_time = date("H:i", strtotime($setting->office_start_time));
        }


        if(isset($request->endTimeHour)) {
            $setting->office_end_time = $request->endTimeHour;
            $setting->office_end_time = date("H:i", strtotime($setting->office_end_time));
        }

        $setting->office_days = implode (",", $request->office_days);
        $setting->save();
        return redirect('admin/setting/index');
    }

    /**
     * Update setting and redirect to index page.
     *
     * @param HolidayFormRequest $request
     *
     * @return view
     */
    public function addHoliday(HolidayFormRequest $request)
    {
        $day = date_create($request->day);

        $schedule = new Schedule();
        $data = $schedule->checkAppointmentOnDate($day->format('Y-m-d'));

        if($data[0]->appointment_count > 0) {
            return response()->json(['success' => 0, 'message' => 'Due to some appointment on this day, holiday can\'t be marked. Reschedule or Cancelled the appointment first.']);
        }
        else if($data[0]->schedule_count == 0 || $request->confirm == 1) {
            $holiday = new Holiday();
            $success = $holiday->addHoliday($request, $request->confirm);

            $message = $success == true ? 'Holiday saved successfully.' : 'Error in adding Holiday. Please try again.';
            return response()->json(['success' => $success, 'message' => $message]);
        }
        else if($data[0]->schedule_count > 0) {
            return response()->json(['success' => 2, 'message' => 'There are some empty schedule for users on this day. Adding holiday on this day will delete empty slots of those users. Do you want to continue.']);
        }
    }

    /**
     * Delete holiday and return success json.
     *
     * @param $id
     *
     * @return view
     */
    public function deleteHoliday($id)
    {
        $success = false;
        try{
            $holiday = Holiday::find($id);
            $holiday->delete();
            $success = true;
        }catch(\Exception $ex){
            Log::error('Error :'. $ex);
        }
        return response()->json(['success'=> $success]);
    }

    /**
     * Get list of holidays from database.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHolidays(Request $request)
    {
        try{
            $year = $request->year;
            $holiday = new Holiday();
            $response['data'] = $holiday->getHoliday($year);
            $response['recordCount'] = $holiday->getHolidayCount($year);
            return response()->json(['draw'=> $request->draw, 'recordsTotal'=> $response['recordCount'], 'recordsFiltered' => $response['recordCount'], 'data' => $response['data']]);
        }catch(\Exception $ex){
            Log::error('Error :'. $ex);
        }
    }
}
