<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\User;
use App\Models\Agency;
use App\Role;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use App\Http\Requests\UserFormRequest;
use App\Http\Requests\ChangePasswordFormRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Helpers\Helper;

class UserController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | UserController
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        $this->middleware('role:admin|agency');
    }

    /**
     * Return User Index view.
     *
     * @return view
     */
    public function index()
    {
        $agencies = Agency::pluck( 'name','id')->toArray();
        $data = array('agencies' => $agencies);
        return view('admin.user.index')->with($data);
    }

    /**
     * Return agency create view.
     *
     * @return view
     */
    public function create()
    {
        $roles = Role::pluck('display_name','id')->toArray();
        $agencies = Agency::pluck( 'name','id')->toArray();

        $data = array('roles' => $roles,'agencies' => $agencies);
        return view('admin.user.create')->with($data);
    }

    /**
     * Return User edit view.
     *
     * @param Request $request
     *
     * @return view
     */
    public function edit(Request $request)
    {
        $user = new User();
        $user = $user->getUserById($request->id);

        $schedule_count = $this->getScheduleCount($request->id);

        $roles = Role::pluck('display_name','id')->toArray();
        $agencies = Agency::pluck( 'name','id')->toArray();

        $data = array('user' => $user,'roles' => $roles,'agencies' => $agencies, 'schedule_count' => $schedule_count);
        return view('admin.user.edit')->with($data);
    }

    /**
     * Create User and redirect to index page.
     *
     * @param UserFormRequest $request
     *
     * @return view
     */
    public function add(UserFormRequest $request)
    {
        $user = new User();
        $success = $user->saveUser($request);

        if($success){
            $this->sendAddUserLinkEmail($request);
            return redirect('admin/user/index');
        }
        else{
            \Session::flash('message', "Error in adding user. Please try again.");
            return Redirect::back();
        }
    }

    /**
     * Update User and redirect to index page.
     *
     * @param UserFormRequest $request
     * @param $id
     *
     * @return view
     */
    public function update(UserFormRequest $request, $id)
    {
        $user = new User();
        $success = $user->updateUser($request, $id);

        if($success){
            return redirect('admin/user/index');
        }
        else{
            \Session::flash('message', "Error in updating user. Please try again.");
            return Redirect::back();
        }
    }

    /**
     * Delete User and return success json.
     *
     * @param $id
     *
     * @return json response - true/false.
     */
    public function delete($id)
    {
        $current_est_date =  Helper::getESTDateFromUTC(Carbon::now());
        $success = false;
        try{
            $schedule_count = $this->getScheduleCount($id);

            if($schedule_count == 0){
                $user = new User();
                $user->deleteUser($id, $current_est_date);
            }
            $success = true;
        }catch(\Exception $ex){
            Log::error('Error :'. $ex);
        }
        return response()->json(['success'=> $success, 'schedule_count' => $schedule_count]);
    }

    /**
     * Get list of users from database.
     *
     * @param Request $request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsers(Request $request)
    {
        try{
            $take = json_decode($request->length);
            $skip = json_decode($request->start);
            $search = !empty($request->search) ? $request->search : '' ;
            $agency_id = !empty($request->agency) ? $request->agency : '0' ;

            $sortColumnIndex = $request->order[0]['column'];
            $sortDirection = $request->order[0]['dir'];
            $sortField = $request->columns[$sortColumnIndex]['name'];

            $user = new User();
            $response['data'] = $user->getUsers($take, $skip, $search, $agency_id, $sortField, $sortDirection);
            $response['recordCount'] = $user->getUsersCount($search, $agency_id);

            return response()->json(['draw'=> $request->draw, 'recordsTotal'=> $response['recordCount'], 'recordsFiltered' => $response['recordCount'], 'data' => $response['data']]);
        }catch(\Exception $ex){
            Log::error('Error :'. $ex);
        }
    }

    /**
     * Return change password view.
     *
     * @return view
     */
    public function getChangePassword()
    {
        return view('admin.user.password.change');
    }

    /**
     * Update password and return success message.
     *
     * @param ChangePasswordFormRequest $request
     *
     * @return view
     */
    public function postChangePassword(ChangePasswordFormRequest $request)
    {
        $id = Auth::user()->id;
        $user = User::findOrFail($id);

        $current_password = Input::get('old_password');
        $password = bcrypt(Input::get('password'));

        if (Hash::check($current_password, $user->password) ) {
            try {
                $user1 = new User();
                $user1->updatePassword($password, $id);
                $flag = TRUE;
            }
            catch(\Exception $e){
                $flag = FALSE;
            }
            if($flag){
                \Session::flash('flash_message', 'Password changed successfully.');
                return redirect('/password/change')->with('success', "Password changed successfully.");
            }
            else{
                return redirect('/password/change')->with("danger", "Unable to process request this time. Try again later");
            }
        }
        else{
            return redirect('/password/change')->withErrors(['old_password' => 'Old Password do not match our record.']);
        }
    }

    /**
     * Get count of appointment scheduled for the user.
     *
     * @param string $id.
     *
     * @return count.
     */
    private function getScheduleCount($id){
        $current_est_date = Helper::getESTDateFromUTC(Carbon::now());
        return $schedule_count = Schedule::where('user_id', $id)
                ->where('booked_by', '!=' , 0)
                ->whereDate('date', '>=' , $current_est_date)
                ->select('id')
                ->count();
    }

    /**
     * Resend User Password and return success json.
     *
     * @param Request $request.
     *
     * @return json response - true/false.
     */
    public function resendPassword(Request $request)
    {
        $success = false;
        try {
            $user = User::where('id', $request->id)->first();
            $request['email'] = $user->email;
            $this->sendAddUserLinkEmail($request);
            $success = true;
        } catch (\Exception $ex) {
            Log::error('Error :' . $ex);
        }
        return response()->json(['success' => $success]);
    }
}
