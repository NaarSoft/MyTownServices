<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Response;
use App\Models\ResponseDetail;
use App\Models\Setting;
use App\Models\Question;
use App\Models\Service;
use App\Models\QuestionDetail;
use Illuminate\Support\Facades\Log;
use Mail;
use Zizaco\Entrust\EntrustRole;

class ResponseController extends Controller
{
    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        $this->middleware('role:admin|agency');
    }

    /**
     * Show the Manage Response index page.
     *
     * @return setting.
     */
    public function index()
    {
        $setting = Setting::find(1);
        return view('admin.response.index')->with(['setting' => $setting]);
    }

    /**
     * populate Manage Response grid data.
     *
     * @param $request.
     *
     * @return json object of $response data.
     */
    public function getResponses(Request $request)
    {
        try{
            $take = json_decode($request->length);
            $skip = json_decode($request->start);
            $search = !empty($request->search)  ? $request->search : '';
            $sortColumnIndex = $request->order[0]['column'];
            $sortDirection = $request->order[0]['dir'];
            $sortField = $request->columns[$sortColumnIndex]['name'];
            $show_all = $request->show_all;

            $response = new Response();

            $user_id = null;
            if(Auth::user()->hasRole('agency')){
                $user_id = Auth::user()->id;
            }

            $response['data'] = $response->getResponses($user_id, $show_all, $take, $skip, $search, $sortField, $sortDirection);
            $response['recordCount'] = $response->getResponseCount($user_id, $show_all, $search);
            return response()->json(['draw'=> $request->draw, 'recordsTotal'=> $response['recordCount'], 'recordsFiltered' => $response['recordCount'], 'data' => $response['data']]);
        }catch(\Exception $ex){
            Log::error('Error :'. $ex);
        }
    }

    /**
     * populate respondent questionnaire data.
     *
     * @param $request.
     *
     * @return questionnaire data.
     */
    public function getQuestionnaireData(Request $request){
        try{
            $responseId = $request->response_id;
            //get responder details
            $response = Response::find($responseId);
            if($response){
                $responseDetailObj = new ResponseDetail();
                $responseDetails = $responseDetailObj->getResponseDetails($responseId);
                $responseBasicInfo = array();
                $servicesRequested = array();
                if($responseDetails){
                    foreach($responseDetails as $row){
                        if(!$row->service_ids){
                            $responseBasicInfo[] = $row;
                        } else {
                            $servicesRequested[] = $row;
                        }
                    }
                }
                $data = array(
                    'response' => $response,
                    'respondent_basic_info' => $responseBasicInfo,
                    'requested_services' => $servicesRequested
                );
                return view('questionnaire._questionnaire_view')->with($data)->render();
            }
        }catch(\Exception $ex){
            Log::error('Error :'. $ex);
        }
    }

    /**
     * populate services list selected by respondent for appointments with agency.
     *
     * @param $request.
     *
     * @return schedule data.
     */
    public function getServices(Request $request){
        try{
            $responseId = $request->response_id;
            $schedule = new Schedule();
            $agencies = $schedule->getAgencyForRescheduling($responseId);
            $data = array('agencies' => $agencies, 'responseId' => $responseId);
            return view('service._schedule')->with($data)->render();
        }catch(\Exception $ex){
            Log::error('Error :'. $ex);
        }
    }

    /**
     * cancel appointment and send notification by mail to agency user and respondent user.
     *
     * @param $request.
     *
     * @return success.
     */
    public function cancelAppointmentAndSchedule(Request $request){
        $success = false;
        try{
            $responseId = $request->response_id;
            $response_data = Response::find($responseId);

            // Get old schedule
            $schedule = new Schedule();
            $old_schedules = $schedule->getScheduledAppointments($responseId);
            $service_booked = $old_schedules->toArray();

            // Get booking date from array
            reset($service_booked);
            $first_key = key($service_booked);
            $booking_date = $service_booked[$first_key][0]->date;

            $schedule = new Schedule();
            $schedule->cancelAppointmentAndSchedule($request->reason, $responseId);

            $data = array('service_booked' => $service_booked, 'booking_date' => $booking_date, 'response' => $response_data);

            // Send mail to Respondent
            $email = $response_data->email_address;
            Mail::send('service.cancel_mail', $data, function($message)use ($email)
            {
                $message->to($email);
                $message->subject('Cancel Appointment');
            });

            // Send mail to Agency
            foreach($old_schedules as $key => $schedule){
                $this->sendCancelScheduleMailToAgency($schedule[0], $booking_date);
            }
            $success = true;
        }catch(\Exception $ex){
            Log::error('Error :'. $ex);
        }

        return response()->json(['success' => $success]);
    }

    /**
     * reschedule appointment and send notification by mail to agency user and respondent user.
     *
     * @param $request.
     *
     * @return $slot_book.
     */
    public function rescheduleAppointment(Request $request){
        $success = false;
        try{
            $responseId = $request->response_id;
            $serviceIds = implode("," , $request->service_ids);
            $bookingDate = $request->booking_date;

            $service = new Service();
            $data = $service->getAvailableSlotsForBooking($serviceIds,$bookingDate);
            $slot_book = 0;

            if(count($data) > 0)
            {
                $scheduleIds = explode(',', $data[0]->schedule_id);

                // Get old schedule
                $schedule = new Schedule();
                $old_schedules = $schedule->getScheduledAppointments($responseId);

                $schedule = new Schedule();
                $schedule->rescheduleAppointment($scheduleIds, $responseId);

                // Get new schedule after rescheduling
                $schedule = new Schedule();
                $new_schedules = $schedule->getScheduledAppointments($responseId);

                $this->sendRescheduleMailToRespondent($responseId, $old_schedules, $new_schedules);

                foreach($old_schedules as $key => $schedule){
                    if (in_array($schedule[0]->service_id, $request->service_ids)) {
                        $new_schedule = $new_schedules[$key][0];

                        //send reschedule mail to agency user
                        $this->sendRescheduleMailToAgency($schedule[0], $new_schedule, $bookingDate);

                        // send cancel mail to agency user
    //                    if($schedule[0]['userId'])
    //                        $this->sendCancelScheduleMailToAgency($schedule[0], $bookingDate);
                    }else{
                        //send cancel mail to agency user
                        $this->sendCancelScheduleMailToAgency($schedule[0], $bookingDate);
                    }
                }

                $slot_book = 1;
            }
            $success = true;
        }catch(\Exception $ex){
            Log::error('Error :'. $ex);
        }
        return response()->json(['slot_book' => $slot_book, 'success' => $success]);
    }

    /**
     * Send reschedule mail to requested respondent.
     *
     * @param string $responseId.
     * @param array $old_schedules.
     * @param array $new_schedules.
     *
     * @return status of mail send.
     */
    private function sendRescheduleMailToRespondent($responseId, $old_schedules, $new_schedules)
    {
        $status = false;
        try{
            $new_schedules = $new_schedules->toArray();
            reset($new_schedules);
            $first_key = key($new_schedules);
            $booking_date = $new_schedules[$first_key][0]->date;

            $data = array('service_booked' => $new_schedules, 'old_schedules' => $old_schedules->toArray(), 'booking_date' => $booking_date);

            $response = Response::find($responseId);
            $email = $response->email_address;

            Mail::send('service.schedule_mail', $data, function($message)use ($email)
            {
                $message->to($email);
                $message->subject('Service Appointment Rescheduled');
            });
            return true;
        }
        catch(\Exception $ex)
        {
            Log::error('Error :'. $ex);
            return $status;
        }
    }

    /**
     * Send Reschedule Mail to respective agency.
     *
     * @param array $old_schedule.
     * @param array $new_schedule.
     * @param date $bookingDate.
     *
     * @return status of mail send.
     */
    private function sendRescheduleMailToAgency($old_schedule, $new_schedule, $bookingDate)
    {
        $status = false;
        try{
            $data = array('old_schedule' => $old_schedule, 'new_schedule' => $new_schedule, 'booking_date' => $bookingDate);
            $email = $new_schedule->user_email;

            Mail::send('service.reschedule_mail_agency', $data, function($message)use ($email)
            {
                $message->to($email);
                $message->subject('Service Appointment Rescheduled');
            });
            return true;
        }
        catch(\Exception $ex)
        {
            Log::error('Error :'. $ex);
            return $status;
        }
    }

    /**
     * Send Cancel Schedule Mail to respective agency.
     *
     * @param array $old_schedule.
     * @param date $bookingDate.
     *
     * @return status of mail send.
     */
    private function sendCancelScheduleMailToAgency($old_schedule, $bookingDate)
    {
        $status = false;
        try{
            $data = array('old_schedule' => $old_schedule, 'booking_date' => $bookingDate);
            $email = $old_schedule->user_email;

            Mail::send('service.cancel_mail_agency', $data, function($message)use ($email)
            {
                $message->to($email);
                $message->subject('Cancel Appointment');
            });
            return true;
        }
        catch(\Exception $ex)
        {
            Log::error('Error :'. $ex);
            return $status;
        }
    }
}
