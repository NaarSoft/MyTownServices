<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Schedule;
use App\Models\Setting;
use App\Models\User;
use App\Models\Agency;
use App\Models\Holiday;
use App\Models\Location;
use App\Models\AgencyLocation;
use App\Models\UserLocation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;

class ScheduleController extends Controller
{
    /**
     * Return Schedule Index view.
     *
     * @return view
     */
    public function index()
    {
        $setting = Setting::find(1);
        $agencies = Agency::pluck('name','id')->toArray();
        $holidays = Holiday::whereYear("day", date('Y'))->select('day', 'name')->orderBy('day')->get()->toArray();
        //$holidays = array_column($holidays, 'day');
        //$holidays = json_encode($holidays);
        //$holidays = str_replace("\/","-", $holidays);
        $data = array(
            'setting' => $setting,
            'agencies' => $agencies,
            'agency_users' => array(),
            'holidays' => json_encode($holidays),
            'locations' => array()
        );
        return view('admin.schedule.index')->with($data);
    }

    /**
     * Create schedule.
     *
     * @param Request $request.
     *
     * @return view
     */
    public function saveScheduleData(Request $request)
    {
        $success = false;
        try{
            // BULK INSERT
            $input = Input::except(['_method', '_token']);
            $scheduleArray = $input['scheduleArray'];

            $index = 0;
            $schedules = [];
            foreach($scheduleArray as $schedule){
                $start_date_time =  new \DateTime($schedule['start']);
                $end_date_time = new \DateTime($schedule['end']);

                $schedules[$index]['user_id'] = $schedule['user'];
                $schedules[$index]['date'] = $start_date_time->format('Y-m-d');
                $schedules[$index]['start_time'] = $start_date_time->format('H:i:s');
                $schedules[$index]['end_time'] = $end_date_time->format('H:i:s');
                $schedules[$index]['created_by'] = Auth::user()->id;
                $schedules[$index]['booked_by'] = $schedule['booked_by'];
                $index++;
            }

            $schedule = new Schedule();
            $schedule->saveSchedules($schedules, $request->users, $request->start_date, $request->end_date, $request->working_days);

            //delete & insert
            foreach($request->users as $userId){
                if($userId){
                    $userLocationObj = new UserLocation();
                    $userLocationObj->deleteUserLocations($userId);
                    if(count($request->locations)){
                        foreach($request->locations as $userLocationId){
                            $data = array(
                                'user_id' => $userId,
                                'location_id'=> $userLocationId
                            );
                            $userLocationObj->addUserLocation($data);
                        }
                    }
                }
            }

            $success = true;

        }catch(\Exception $ex){
            Log::error('Error :'. $ex);
        }
        return response()->json(['success'=> $success]);
    }

    /**
     * delete schedule slot.
     *
     * @param Request $request.
     *
     * @return json response - true/false.
     */
    public function deleteSlot(Request $request)
    {
        $success = 0;
        try{
            $schedule = new Schedule();
            $success =  $schedule->deleteSlot($request->event_id);
        }catch(\Exception $ex){
            Log::error('Error :'. $ex);
        }
        return response()->json(['success' => $success]);
    }

    /**
     * gets the list of agency users by id from DB for Agency Users list.
     *
     * @param Request $request.
     *
     * @return json response -  $agency_users.
     */
    public function getAgencyUserById(Request $request)
    {
        try {
            $user = new User();
            $agency_users = $user->getAgencyUsers($request->agency_id);
            return response()->json(['response' => $agency_users]);
        }catch(\Exception $ex){
            Log::error('Error :'. $ex);
        }
    }

    /**
     * gets the list of schedules users from DB for Agency Users list.
     *
     * @param Request $request.
     *
     * @return json response - array of schedules and booked dates.
     */
    public function getSchedules(Request $request)
    {
        try {
            $start_date = Helper::getESTDateFromUTC(Carbon::now());

            $schedule = new Schedule();
            $schedules = $schedule->getSchedules($request->users, $start_date);

            $booked_dates = Schedule::where('booked_by', '>', '0')
                ->where('user_id', $request->users)
                ->where('date', '>=', $start_date)
                ->distinct()
                ->select('date')->get()->toArray();
        }catch(\Exception $ex){
            Log::error('Error :'. $ex);
        }
        return response()->json(['response' => $schedules, 'booked_dates' => $booked_dates]);
    }

    /**
     * gets the list of appointments users from DB for Agency Users list.
     *
     * @param Request $request.
     *
     * @return array of agency users.
     */
    public function getAppointments(Request $request)
    {
        try {
            $user_id = null;
            if(Auth::user()->hasRole('agency') == true){
                $user_id = Auth::user()->id;
            }

            $schedule = new Schedule();
            $appointments = $schedule->getAppointments($user_id, $request->start, $request->end);
        }catch(\Exception $ex){
            Log::error('Error :'. $ex);
        }
        return response()->json(['response' => $appointments]);
    }

    /**
     * gets the list of agency locations by id from DB for Agency Locations list.
     *
     * @param Request $request.
     *
     * @return json response -  $agency_users.
     */
    public function getAgencyLocationsById(Request $request)
    {
        try {
            $agencyLocationObj = new AgencyLocation();
            $agencyLocationIds = $agencyLocationObj->getAgencyLocations($request->agency_id);
            $agencyLocations = array();
            if($agencyLocationIds){
                foreach($agencyLocationIds as $row){
                    $locationObj = new Location();
                    $locationDetails = $locationObj->getLocationDetails($row->location_id);
                    $agencyLocations[] = $locationDetails;
                }
            }
            return response()->json(['response' => $agencyLocations]);
        }catch(\Exception $ex){
            Log::error('Error :'. $ex);
        }
    }

    /**
     * gets the list of user locations by id from DB.
     *
     * @param Request $request.
     *
     * @return json response -  $agency_users.
     */
    public function getUserLocationsById(Request $request)
    {
        try {
            $userIds = $request->user_id;
            $userLocations = array();
            if($userIds && count($userIds)){
                foreach($userIds as $userId){
                    if($userId) {
                        $userLocationObj = new UserLocation();
                        $userLocationIdsResult = $userLocationObj->getUserLocations($userId);
                        if ($userLocationIdsResult) {
                            foreach ($userLocationIdsResult as $userLocationId) {
                                if(!in_array($userLocationId->location_id, $userLocations)) {
                                    $userLocations[] = $userLocationId->location_id;
                                }
                            }
                        }
                    }
                }
            }
            return response()->json(['response' => $userLocations]);
        }catch(\Exception $ex){
            Log::error('Error :'. $ex);
        }
    }
}
