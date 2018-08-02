<?php

namespace App\Http\Controllers;
use App\Models\AgencyLocation;
use App\Models\Response;
use App\Models\ResponseDetail;
use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Agency;
use App\Models\Location;
use App\Models\User;
use App\Models\Setting;
use App\Models\Holiday;
use App\Models\Question;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use Mail;
use MongoDB\Driver\Exception\SSLConnectionException;

class ServiceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        $this->middleware('guest',['except' => ['index', 'getAvailableSlotsForBooking', 'cancelAppointment', 'bookAppointment', 'appointment']]);
    }

    /**
     * Return Service Index view.
     *
     * @param Request $request.
     *
     * @return view.
     */
    public function index(Request $request)
    {
        $responseId = $request->session()->get('responseId');
        if(!$responseId){
            return redirect('index');
        }
        //$request->session()->forget('responseId');
        $agency = new Agency();
        if(isset($request->service_ids)){
            $service_ids = explode(',', $request->service_ids);
            $agencies = $agency->getAgencyByServiceIds($service_ids);
        }else{
            $agencies = $agency->getAgencyForBooking($responseId);
        }

        return view('service.index')->with(
            array(
                'agencies' => $agencies,
                'responseId' => $responseId
            )
        );
    }

    public function getServiceAgencies(Request $request)
    {
        $data = array();
        try{
            $selectedQuestionIds = $request->selected_questions;
            //get Agencies based on Question Ids
            $agencyObj = new Agency();
            $agencies = $agencyObj->getAgenciesByQuestionIds($selectedQuestionIds);
            if(!empty($agencies)){
                $scheduleObj = new Schedule();
                foreach($agencies as $row) {
                    //get appointment slots count
                    $appointmentSlotsCount = $scheduleObj->getAppointmentTimeSlotsCount($row->id);
                    $webSiteFullURL = explode('/', $row->website);
                    $row->website = trim($webSiteFullURL[0]);
                    $row->available_slots = $appointmentSlotsCount;
                    $data[] = $row;
                }
            }
        } catch(\Exception $ex){
            Log::error('Error :'. $ex);
        }
        return response()->json(['data' => $data]);

    }

    public function getLocationsWiseAvailableSlots(Request $request)
    {
        $data = array();
        try{
            $agencyIds = $request->agency_ids;
            $locations = array();
            if(!empty($agencyIds)) {
                //get agency Locations & details
                $count = 1;
                foreach ($agencyIds as $agencyId) {
                    $agencyLocationIds = array();
                    $agencyLocationObj = new AgencyLocation();
                    $agencyLocations = $agencyLocationObj->getAgencyLocations($agencyId);
                    if ($agencyLocations) {
                        foreach ($agencyLocations as $row) {
                            $agencyLocationIds[] = $row->location_id;
                        }
                    }

                    if ($count == 1) {
                        $locations = $agencyLocationIds;
                    } else {
                        $locations = array_intersect($locations, $agencyLocationIds);
                    }
                    $count++;
                }

                if (count($locations)) {
                    //current date + 24 hours
                    $gap_hours = Config::get('app.future_events_after_hours');
                    //@todo: need to modify this back
                    $nextDateTime = Carbon::now()->addHours($gap_hours)->format('Y-m-d H:i:s');
                    foreach ($locations as $locationId) {
                        $locationObj = new Location();
                        $locationDetails = $locationObj->getLocationDetails($locationId);
                        for($i = 0; $i < 60; $i++){
                            //calculate date
                            if($i > 0){
                                $forDateTime = date('Y-m-d 00:00:00', strtotime("+{$i} day", strtotime($nextDateTime)));
                            } else {
                                $forDateTime = $nextDateTime;
                            }
                            $availableTimeSlots = $this->getAvailableSlotsForLocation($locationId, $forDateTime, $agencyIds);
                            if(!empty($availableTimeSlots)){
                                break;
                            }
                        }

                        $data[] = array(
                            'location_id' => $locationId,
                            'location_name' => $locationDetails->location,
                            'date' => date('Y-m-d', strtotime($forDateTime)),
                            'formatted_date' => date('M d, Y', strtotime($forDateTime)),
                            'available_slots' => array_slice($availableTimeSlots, 0, 8)
                        );
                        //get schedule of assigned location agency users
                    }
                }
            }
        } catch(\Exception $ex){
            Log::error('Error :'. $ex);
        }
        return response()->json(['data' => $data]);
    }

    private function getAvailableSlotsForLocation($locationId, $dateTime, $agencyIds)
    {
        $noOfAgencies = count($agencyIds);
        $slotDuration = Config::get('app.slot_duration');
        $aptDuration = ($noOfAgencies * $slotDuration);
        //agency users timeslots
        $userObj = new User();
        $scheduleObj = new Schedule();
        //$agencyUsersSchedule = array();
        $agencySchedule = array();
        //$availableAgencyUsers = array();
        $firstAgencyId = $agencyIds[0];
        foreach($agencyIds as $agencyId){
            //agency users of this location
            $agencySchedule[$agencyId] = array();
            $agencyUsers = $userObj->getAgencyUsersForLocation($agencyId, $locationId);
            if($agencyUsers) {
                foreach ($agencyUsers as $user){
                    $userId = $user->id;
                    //$availableAgencyUsers[$agencyId][] = $userId;
                    //get users booked appointments
                    $userBookedTimeSlotsArray = $scheduleObj->getBookedTimeSlots($userId, $dateTime, $notAtLocationId = $locationId);
                    $userUnavailableTimeSlots = array();
                    if($userBookedTimeSlotsArray){
                        foreach($userBookedTimeSlotsArray as $row){
                            $timeParts = explode(':', $row->start_time);
                            $timeInMinutes = ((60 * $timeParts[0]) + $timeParts[1]);
                            $appointmentStartTime = $timeInMinutes;
                            $appointmentEndTime = ($timeInMinutes + $slotDuration);
                            $unAvailableStartTime = ($appointmentStartTime - 60);
                            $unAvailableEndTime = ($appointmentEndTime + 60);
                            while($unAvailableStartTime < $unAvailableEndTime){
                                $userUnavailableTimeSlots[] = $unAvailableStartTime;
                                $unAvailableStartTime = $unAvailableStartTime + 15;
                            }
                        }
                    }

                    //get user schedule
                    $userTimeSlotsArray = $scheduleObj->getUserAvailableTimeSlots($userId, $dateTime);
                    $userTimeSlots = array();
                    if($userTimeSlotsArray){
                        foreach($userTimeSlotsArray as $row){
                            $timeParts = explode(':', $row->start_time);
                            $timeInMinutes = ((60 * $timeParts[0]) + $timeParts[1]);
                            if(!in_array($timeInMinutes, $userUnavailableTimeSlots)) {
                                $userTimeSlots[$timeInMinutes] = $row->start_time;
                                if (!array_key_exists($timeInMinutes, $agencySchedule[$agencyId])) {
                                    $agencySchedule[$agencyId][$timeInMinutes] = $row->start_time;
                                }
                            }
                        }
                        //$agencyUsersSchedule[$agencyId][$userId] = $userTimeSlots;
                    }
                }
            }
        }

        $firstAgencySchedule = $agencySchedule[$firstAgencyId];
        $availableTimeSlots = array();
        if($firstAgencySchedule) {
            ksort($firstAgencySchedule, SORT_NUMERIC);
            $minTimeMin = min(array_keys($firstAgencySchedule));
            $maxTimeMin = max(array_keys($firstAgencySchedule));
            $maxTimeSlotLimit = (($maxTimeMin + $slotDuration) - $aptDuration);
            while ($minTimeMin <= $maxTimeSlotLimit) {
                $timeSlotAvailable = true;
                $timeSlotToCheck = $minTimeMin;
                for ($i = 0; $i < $noOfAgencies; $i++) {
                    if (!array_key_exists($timeSlotToCheck, $agencySchedule[$agencyIds[$i]])) {
                        $timeSlotAvailable = false;
                        break;
                    }
                    $timeSlotToCheck = $timeSlotToCheck + $slotDuration;
                }

                if ($timeSlotAvailable && isset($firstAgencySchedule[$minTimeMin])) {
                    $availableTimeSlots[] = substr($firstAgencySchedule[$minTimeMin], 0, 5);
                }

                $minTimeMin += $slotDuration;
            }
        }

        return $availableTimeSlots;
    }

    /**
     * Gets the appointments available for selected services by respondent.
     *
     * @param Request $request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailableSlotsForBooking(Request $request)
    {
        $calendarTimeSlots = array();
        try{
            $agencyIds = $request->agency_ids;
            $locationId = $request->location_id;
            $startDate = $request->start;
            $formattedStartDate = str_replace('-', '', $startDate);

            $locationObj = new Location();
            $locationDetails = $locationObj->getLocationDetails($locationId);
            //current date + 24 hours
            $gap_hours = Config::get('app.future_events_after_hours');
            $slotDuration = Config::get('app.slot_duration');
            $showFromDate = Carbon::now()->addHours($gap_hours)->format('Ymd');
            $preWeekStartDate = '';
            if($formattedStartDate > $showFromDate) {
                $preWeekStartDate = date('Y-m-d', strtotime('-7 days', strtotime($formattedStartDate)));
            }
            $nextWeekStartDate = date('Y-m-d', strtotime('+7 days', strtotime($formattedStartDate)));
            $setting = Setting::find(1);
            $officeStartTimeParts = explode(":" , $setting->office_start_time);
            $officeStartTimeInMin = (($officeStartTimeParts[0] * 60) + $officeStartTimeParts[1]);
            $officeEndTimeParts = explode(":", $setting->office_end_time);
            $officeEndTimeInMin = (($officeEndTimeParts[0] * 60) + $officeEndTimeParts[1]);

            $daySlots = array();
            $loopStartTime = $officeStartTimeInMin;
            while($loopStartTime < $officeEndTimeInMin){
                $hr = floor($loopStartTime/60);
                if(strlen($hr) == 1){
                    $hr = '0'.$hr;
                }
                $min = $loopStartTime%60;
                if(strlen($min) == 1){
                    $min = '0'.$min;
                }
                $time = $hr.":".$min;
                $daySlots[] = $time;
                $loopStartTime += $slotDuration;
            }

            if(!empty($agencyIds) && $locationId && $startDate){
                for($i = 0; $i < 7; $i++) {
                    //calculate date
                    $nextDate = date('Y-m-d', strtotime("+{$i} day", strtotime($startDate)));
                    $nextDateTime = date('Y-m-d 00:00:00', strtotime("+{$i} day", strtotime($startDate)));
                    $formattedDate = date('Ymd', strtotime($nextDate));
                    if($formattedDate == $showFromDate){
                        $nextDateTime = Carbon::now()->addHours($gap_hours)->format('Y-m-d H:i:s');
                    }
                    $calendarTimeSlots[$nextDate] = array();
                    if($formattedDate >= $showFromDate) {
                        $availableTimeSlots = $this->getAvailableSlotsForLocation($locationId, $nextDateTime, $agencyIds);
                        $loopStartTime = $officeStartTimeInMin;
                        while($loopStartTime < $officeEndTimeInMin){
                            $hr = floor($loopStartTime/60);
                            if(strlen($hr) == 1){
                                $hr = '0'.$hr;
                            }
                            $min = $loopStartTime%60;
                            if(strlen($min) == 1){
                                $min = '0'.$min;
                            }
                            $time = $hr.":".$min;
                            $calendarTimeSlots[$nextDate][$time] = 0;
                            if(in_array($time, $availableTimeSlots)){
                                $calendarTimeSlots[$nextDate][$time] = 1;
                            }
                            $loopStartTime += $slotDuration;
                        }
                    }
                }
            }
        }catch(\Exception $ex){
            Log::error('Error :'. $ex);
        }
        $data = array(
            'location_name' => $locationDetails->location,
            'pre_from_date' => $preWeekStartDate,
            'next_from_date' => $nextWeekStartDate,
            'day_slots' => $daySlots,
            'available_time_slots' => $calendarTimeSlots
        );
        return response()->json(['data' => $data]);
    }

    /**
     * Book appointment on selected date from calendar.
     *
     * @param Request $request.
     *
     * @return json response - true/false.
     */
    public function bookAppointment(Request $request)
    {
        try{
            $name = $request->name;
            $emailAddress = $request->email_address;
            $cellPhone = $request->cell_phone;
            $gender = $request->gender;
            $age = $request->age;

            $questionObj = new Question();
            $questions = $questionObj->getQuestions();
            $basicQuestionsResponses = array();
            if(!empty($questions)) {
                foreach($questions as $row){
                    if(is_null($row->service_ids)){
                        $basicQuestionsResponses[$row->id] = $request->{$row->id};
                    }
                }
            }


            //help questions
            $selectedServiceQuestions = $request->selected_questions;
            $locationId = $request->location_id;
            $bookingDate = $request->appointment_date;
            $timeSlot = $request->appointment_time . ':00';
            $agencyIds = $request->agencies;

            $responseObj = new Response();
            $responseArray = array(
                'name' => $name,
                'email_address' => $emailAddress,
                'cell_phone' => $cellPhone,
                'gender' => $gender,
                'age' => $age,
                'updated_at' => Carbon::now()
            );
            $responseId = $responseObj->saveResponse($responseArray);
            if($responseId) {
                //insert response details
                $responseDetailObj = new ResponseDetail();
                if(!empty($basicQuestionsResponses)){
                    foreach($basicQuestionsResponses as $questionId => $questionResponse){
                        $responseDetailsArray = array(
                            'response_id' => $responseId,
                            'question_id' => $questionId,
                            'answer' => $questionResponse
                        );
                        $responseDetailObj->saveResponseDetails($responseDetailsArray);
                    }
                }

                if(!empty($selectedServiceQuestions)){
                    foreach($selectedServiceQuestions as $serviceQuestionId){
                        $responseDetailsArray = array(
                            'response_id' => $responseId,
                            'question_id' => $serviceQuestionId,
                            'answer' => 1
                        );
                        $responseDetailObj->saveResponseDetails($responseDetailsArray);
                    }
                }

                $slotDuration = Config::get('app.slot_duration');
                //get Available schedule Ids of agencies
                $scheduleIds = array();
                $slot_book = 0;
                if (!empty($agencyIds)) {
                    $schedule = new Schedule();
                    foreach ($agencyIds as $agencyId) {
                        $scheduleIdRes = $schedule->getAvailableScheduleId($agencyId, $bookingDate, $timeSlot);
                        if ($scheduleIdRes) {
                            $scheduleIds[] = $scheduleIdRes->id;
                            $dateTime = date('Y-m-d') . ' ' . $timeSlot;
                            $timeSlot = date('H:i:s', strtotime("+{$slotDuration} minutes", strtotime($dateTime)));
                        } else {
                            break;
                        }
                    }

                    if (!empty($scheduleIds)) {
                        $schedule->bookAppointment($scheduleIds, $responseId, $locationId, uniqid());

                        $data = $this->getScheduledAppointment($responseId);
                        // Send mail to respondent
                        $this->sendAppointmentMail($responseId, $data);

                        // Send appointment mails to selected agencies
                        foreach ($data['service_booked'] as $key => $schedule) {
                            //send schedule mail to agency user
                            $this->sendScheduleMailToAgency($schedule[0], $data['booking_date'], $data['location']);
                        }
                        return redirect("service/appointment?response_id={$responseId}");
                    } else {
                        return redirect("service/appointmentNotAvailable");
                    }
                }
            }
        }
        catch(\Exception $ex){
            Log::error('Error :'. $ex);
        }
    }

    /**
     * Return scheduled appointment view.
     *
     * @param Request $request.
     *
     * @return view.
     */
    public function appointment(Request $request)
    {
        $responseId = $request->response_id;
        $data = $this->getScheduledAppointment($responseId);
        return view('service.appointment')->with($data);
    }

    /**
     * Return scheduled appointment print view.
     *
     * @param Request $request.
     *
     * @return view.
     */
    public function printAppointment(Request $request)
    {
        $responseId = $request->response_id;
        $data = $this->getScheduledAppointment($responseId);
        return view('service.schedule_mail')->with($data);
    }

    /**
     * Save Cancellation reason in database.
     *
     * @param Request $request.
     *
     * @return home view.
     */
    public function cancelAppointment(Request $request)
    {
        try{
            $response = new Response();
            $response->cancelAppointment($request->txtReason, $request->response_id);
            return redirect('home');
        } catch (\Exception $ex) {
            Log::error('Error :'. $ex);
            \Session::flash('message', "Some error occurred.");
            return Redirect::back();
        }
    }

    /**
     * return index.
     *
     * @param Request $request.
     *
     * @return home view.
     */
    public function goToPrevious(Request $request)
    {
        $responseId = $request->responseId;
        $request->session()->put('responseId', $responseId);
        $request->session()->put('loadData', true);
        return redirect('index');
    }

    /**
     * Send appointment mail to respondent.
     *
     * @param string $responseId.
     *
     * @return status of mail send.
     */
    private function sendAppointmentMail($responseId, $data){
        $status = false;
        try{
            $response = Response::find($responseId);
            $email = $response->email_address;

            Mail::send('service.schedule_mail', $data, function ($message) use ($email) {
                $message->to($email);
                $message->subject('Service Appointment');
            });
            return true;
        }catch (\Exception $ex){
            Log::error('Error :'. $ex);
            return $status;
        }
    }

    /**
     * Send Schedule Mail to respective agency.
     *
     * @param array $schedule.
     * @param date $bookingDate.
     *
     * @return status of mail send.
     */
    private function sendScheduleMailToAgency($schedule, $bookingDate, $bookingAtLocation)
    {
        $status = false;
        try{
            $data = array('new_schedule' => $schedule, 'booking_date' => $bookingDate, 'location' => $bookingAtLocation);
            $email = $schedule->user_email;

            Mail::send('service.schedule_mail_agency', $data, function($message)use ($email)
            {
                $message->to($email);
                $message->subject('Service Appointment Scheduled');
            });
            Log::error('Successfully sent mail to Agency. Email:'. $schedule->user_email);
            return true;
        }
        catch(\Exception $ex)
        {
            Log::error('Failed to send mail to Agency. Email:'. $schedule->user_email);
            Log::error('Error :'. $ex);
            return $status;
        }
    }

    /**
     * Get scheduled appointment data by response id.
     *
     * @param string $responseId.
     *
     * @return scheduled appointment data .
     */
    private function getScheduledAppointment($responseId)
    {
        $schedule = new Schedule();
        $service_booked = $schedule->getScheduledAppointments($responseId);
        $service_booked = $service_booked->toArray();

        reset($service_booked);
        $first_key = key($service_booked);
        $booking_date = $service_booked[$first_key][0]->date;
        $booking_time = $service_booked[$first_key][0]->start_time;
        $location = $service_booked[$first_key][0]->location;

        $data = array(
            'service_booked' => $service_booked,
            'response_id' => $responseId,
            'booking_date' => $booking_date,
            'booking_time' => $booking_time,
            'location' => $location
        );
        return $data;
    }

    public function appointmentNotAvailable()
    {
        return view('service.appointment_not_available');
    }
}
