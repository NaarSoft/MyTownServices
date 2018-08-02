<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use App\Models\Agency;
use App\Models\Service;
use App\Models\Response;
use App\Models\question1;	
use App\Models\ResponseDetail;
use App\Models\Question;
use App\Models\QuestionDetail;
use Illuminate\Support\Facades\Log;
use Mail;
use App\Http\Requests\ContactUsRequest;




class PublicController extends Controller
{
		

	
    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => ['home', 'about_us', 'create']]);
    }
    /**
     * Create a new controller insert instance.
     *
     */
	  public function score()
    {
        return view('score');
    }
	 
	 public function result(){
		$score = 0;
if ($_POST['q1'] == 'no')
$score++;
if ($_POST['q2'] == 'yes')
$score++;
if ($_POST['q3'] == 'yes')
$score++;
if ($_POST['q4'] == 'yes')
$score++;
if ($_POST['q5'] == 'yes')
$score++;
 echo '<b>Your coolness score was ' . $score . '/5</b><br><br>';
if ($score < 3)
echo 'You are not very cool!';
else if ($score == 5)
echo 'Congratulations, a perfect score!';
else
echo 'Not bad, you scored average';

	}
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	  public function home1(){
		$question = question1::all();
		return view('question', ['question' => $question]);
	}
	 
	 
	public function add(Request $request){
		//$this->validate($request, [
		//'q1' => 'required',
		//'q2' => 'required',
		//'q3' => 'required',
		//'q4' => 'required',
		//'q5' => 'required',
		//'q6' => 'required',
		//'q7' => 'required',
		//'q8' => 'required',
		//'q9' => 'required',
		//'q10' => 'required'
		
	     // ]);
		//$question = new question;
		//$question->q1 = $request->input('q1');
		$question->q2 = $request->input('q2');
		$question->q3 = $request->input('q3');
		$question->q4 = $request->input('q4');
		$question->q5 = $request->input('q5');
		$question->q6 = $request->input('q6');
		$question->q7 = $request->input('q7');
		$question->q8 = $request->input('q8');
		$question->q9 = $request->input('q9');
		$question->q10 = $request->input('q10');
		
	
		$question->save();
		return redirect('/')->with('info', 'YOur score is......');
	}
/**
     * Return insert view.
     *
     * @return view.
     */
    public function create()
    {
        return view('qcreate');
    }
    /**
     * Return AboutUs view.
     *
     * @return view.
     */
    public function about_us()
    {
        return view('aboutus');
    }
   /**
     * Return services view.
     *
     * @return view.
     */
    public function services()
    {
        return view('services');
    }
	   /**
     * Return service view.
     *
     * @return view.
     */
    public function service()
    {
        return view('service');
    }
	
	 /**
     * Return services view.
     *
     * @return view.
     */
	
	  /**
     * Return formview view.
     *
     * @return view.
     */
	 
	  
   
	 
    public function location()
    {
        return view('location');
    }
	 /**
     * Return trauma view.
     *
     * @return view.
     */
    public function trauma()
    {
        return view('trauma');
    }
	 public function qcreateview()
    {
        return view('qcreateview');
    }
	/**
     * Return trauma view.
     *
     * @return view.
     */
		 
    public function traumaindex()
    {
		return view('traumaindex');
    }
	
    /**
     * Return ContactUs view.
     *
     * @return view.
     */
    public function contact_us()
    {
        return view('contactus');
    }
    public function test()
    {
        return view('test');
    }

	 public function haward()
    {
        return view('haward');
    }
	
	 public function greenville()
    {
        return view('greenville');
    }
	
	
	public function stanton()
    {
        return view('stanton');
    }
	
	
	
	
    /**
     * Return Agency view.
     *
     * @param Request $id.
     *
     * @return view.
     */
    public function agency($id)
    {
        $agency = Agency::find($id);
        return view('agency')->with(['agency' => $agency]);
    }

    /**
     * Return home view.
     *
     * @return view
     */
    public function home()
    {
        $agencies = Agency::all();
        return view('home')->with(['agencies' => $agencies]);
    }

    /**
     * Return Questionnaire index view.
     *
     * @param Request $request.
     *
     * @return view.
     */
    public function index(Request $request)
    {
        $loadData = $request->session()->get('loadData');
        if($loadData != 1) {
            session()->forget('responseId');
        }
        session()->forget('loadData');

        $questionObj = new Question();
        $questions = $questionObj->getQuestions();
        $basicInfoQuestions = array();
        $serviceQuestions = array();
        if(!empty($questions)) {
            foreach($questions as $row){
                if(is_null($row->service_ids)){
                    $basicInfoQuestions[] = $row;
                } else {
                    $serviceQuestions[] = $row;
                }
            }
        }
        $noOfQuestionsPerCol = ceil(count($serviceQuestions)/2);
        $serviceQuestionsColOne = array();
        $serviceQuestionsColTwo = array();
        if(!empty($serviceQuestions)){
            $count = 1;
            foreach($serviceQuestions as $row){
                if($count <= $noOfQuestionsPerCol){
                    $serviceQuestionsColOne[] = $row;
                } else {
                    $serviceQuestionsColTwo[] = $row;
                }
                $count++;
            }
        }
        $data = array(
            'basic_info_questions' => $basicInfoQuestions,
            'service_questions_one' => $serviceQuestionsColOne,
            'service_questions_two' => $serviceQuestionsColTwo
        );
        return view('index')->with($data);
    }
 /**
     * Return Questionnaire index1 view.
     *
     * @param Request $request.
     *
     * @return view.
     */
    public function index1(Request $request)
    {
        $loadData = $request->session()->get('loadData');
        if($loadData != 1) {
            session()->forget('responseId');
        }

        $data = $this->getQuestionnaireData($request);
        session()->forget('loadData');

        return view('index1')->with($data);
    }
    /**
     * Return questionnaire data by service id.
     *
     * @param Request $request.
     *
     * @return view.
     */
    public function getQuestionnaireDataByStep(Request $request){

        if($request->service_id == 9){
            $data = $this->getQuestionnaireData($request);
            return view('questionnaire._questionnaire_view')->with($data)->render();
        }
        else{
            $data = $this->getQuestionnaireStepData($request);
            return view('questionnaire._questionnaire_controls')->with($data)->render();
        }
    }

    /**
     * Save questionnaire step data and return saved data.
     *
     * @param Request $request
     *
     * @return view
     */
    public function saveQuestionnaireData(Request $request)
    {
        try {
            $input = Input::except(['_method', '_token']);
            $responseId = isset($input['response_id']) ? $input['response_id'] : null;
            $serviceId = isset($input['service_id']) ? $input['service_id'] : null;

            $response_array = Array();
            $response_detail_array = Array();
            $index = 0;

            foreach ($input as $key => $value) {
                if (strpos($key, '-') !== false) {
                    $input_array = explode('-', $key);
                    $table_name = $input_array[0];
                    $column_name = $input_array[1];

                    if ($table_name == 'response') {
                        $response_array[$column_name] = $value;
                    } else if ($table_name == 'response_details') {
                        $response_detail_array[$index]['service_id'] = $serviceId;
                        $response_detail_array[$index]['question_id'] = $column_name;
                        $response_detail_array[$index]['answer'] = $value;
                        $index++;
                    }
                }
            }

            if (count($response_array) > 0) {
                $response = new Response();
                $response_array['updated_at'] = Carbon::now();
                if ($responseId > 0){
                    $response->updateResponse($response_array, $responseId);
                }else{
                    $responseId = $response->saveResponse($response_array);
                }
                $request->session()->put('responseId', $responseId);
            }

            if (count($response_detail_array) > 0) {
                $responseDetail = new ResponseDetail();
                foreach ($response_detail_array as $key => $value) {
                    $value['response_id'] = $responseId;
                    $response_detail_array[$key] = $value;
                }
                $responseDetail->saveResponseDetailsById($response_detail_array, $responseId, $serviceId);
            }

            $data = $this->getQuestionnaireStepData($request);
            return view('questionnaire._questionnaire_controls')->with($data)->render();
        }catch(\Exception $ex){
            Log::error('Error :'. $ex);
        }
    }

    /**
     * Save Basic Info data and redirect user to index page.
     *
     * @param Request $request
     *
     * @return view
     */
    public function saveBasicInfo(Request $request)
    {
        $input = Input::except(['_method', '_token']);
        $response = new Response();
        $input['updated_at'] = Carbon::now();
        $responseId = $response->saveResponse($input);
        $request->session()->put('responseId', $responseId);
        $request->session()->put('loadData', true);

        return redirect('index');
    }

    /**
     * send contact us mail to admin user.
     *
     * @param ContactUsRequest $request.
     *
     * @return view.
     */
    public function sendMailFromContactUs(ContactUsRequest $request)
    {
        try{
            $input = Input::except(['_method', '_token']);
            $email = (string)\Config::get('app.admin_email');

            $data = array('input' => $input);

            Mail::send('contactemail', $data, function($message)use ($email)
            {
                $message->to($email);
                $message->subject('Contact Us');
            });

            \Session::flash('success_message', 'Mail sent successfully.');
            return redirect('contactus');
        }
        catch(\Exception $ex)
        {
            \Session::flash('fail_message', 'Mail sending failed.'. $ex->getMessage());
            Log::error('Error :'. $ex);
            return redirect('contactus');
        }
    }

    /**
     * gets questionnaire data by service id.
     *
     * @param Request $request.
     *
     * @return questionnaire all steps data.
     */
    private function getQuestionnaireData(Request $request){
        try{
            $serviceId = $request->service_id;
            $responseId = $request->session()->get('responseId');

            $services = Service::all();

            $question = new Question();
            $questions = $question->getQuestionsByServiceId($responseId, $serviceId);

            $questionDetail = new QuestionDetail();
            $questionDetails = $questionDetail->getQuestionDetailsByServiceId();

            $response = Response::where('id', '=',$responseId)->get()->first();

            $data = array('services' => $services, 'questions' => $questions,  'question_details' => $questionDetails, 'response' => $response, 'responseId' => $responseId, 'serviceId' => $serviceId);
            return $data;
        }catch(\Exception $ex){
            Log::error('Error :'. $ex);
        }
    }

    /**
     * gets questionnaire data by service id.
     *
     * @param Request $request.
     *
     * @return questionnaire step data.
     */
    private function getQuestionnaireStepData(Request $request){
        try{
            $serviceId =  empty($request->service_id) ? null :  $request->service_id ;
            $responseId = $request->session()->get('responseId');

            $service = new Service();
            $service_info = $service->getServiceInfoById($serviceId);

            $question = new Question();
            $questions = $question->getQuestionsByServiceId($responseId, $serviceId);

            $questionDetail = new QuestionDetail();
            $questionDetails = $questionDetail->getQuestionDetailsByServiceId($serviceId);

            $response = Response::where('id', '=',$responseId)->get()->first();

            $data = array('service_info' => $service_info, 'questions' => $questions, 'question_details' => $questionDetails, 'response' => $response, 'responseId' => $responseId, 'serviceId' => $serviceId);
            return $data;
        }catch(\Exception $ex){
            Log::error('Error :'. $ex);
        }
    }

    /**
     * Return Session Expire View.
     * Due to inactivity for n seconds, redirect user to this page and delete all sessions and cookies.
     *
     * @param Request $request.
     *
     * @return view.
     */
    public function session_expire(Request $request)
    {
        $request->session()->forget('responseId');
        return view('session_expire');
    }
}