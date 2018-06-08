<?php

namespace App\Http\Controllers;
use App\Models\Response;
use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Agency;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Mail;

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
        $request->session()->forget('responseId');

        $agency = new Agency();
        if(isset($request->service_ids)){
            $service_ids = explode(',', $request->service_ids);
            $agencies = $agency->getAgencyByServiceIds($service_ids);
        }else{
            $agencies = $agency->getAgencyForBooking($responseId);
        }

        return view('service.index')->with(array('agencies' => $agencies, 'responseId' => $responseId));
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
        try{
            $serviceIds = implode("," , $request->service_ids);
            $service = new Service();
            $data = $service->getAvailableSlotsForBooking($serviceIds);
        }catch(\Exception $ex){
            Log::error('Error :'. $ex);
        }
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
        $success = false;
        try{
            $serviceIds = implode("," , $request->service_ids);
            $service = new Service();
            $data = $service->getAvailableSlotsForBooking($serviceIds, $request->booking_date);
            $slot_book = 0;

            if(count($data) > 0)
            {
                $scheduleIds = explode(',', $data[0]->schedule_id);
                $responseId = $request->response_id;

                $schedule = new Schedule();
                $schedule->bookAppointment($scheduleIds, $responseId, uniqid());

                $data = $this->getScheduledAppointment($responseId);

                // Send mail to respondent
                $this->sendAppointmentMail($responseId, $data);

                // Send appointment mails to selected agencies
                foreach($data['service_booked'] as $key => $schedule){
                    //send schedule mail to agency user
                    $this->sendScheduleMailToAgency($schedule[0], $data['booking_date']);
                }

                $slot_book = 1;
            }
            $success = true;
        }
        catch(\Exception $ex){
            Log::error('Error :'. $ex);
        }
        return response()->json(['slot_book' => $slot_book, 'success' => $success]);
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
    private function sendScheduleMailToAgency($schedule, $bookingDate)
    {
        $status = false;
        try{
            $data = array('new_schedule' => $schedule, 'booking_date' => $bookingDate);
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

        $data = array('service_booked' => $service_booked, 'response_id' => $responseId, 'booking_date' => $booking_date);
        return $data;
    }
}
