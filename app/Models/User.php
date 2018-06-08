<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use DB;
use PDO;
use App\Models\UserRole;
use Illuminate\Support\Facades\Log;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

    use Authenticatable, CanResetPassword, EntrustUserTrait, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['first_name', 'last_name', 'email', 'password', 'phone_no', 'cell_no', 'agency_id'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * get details of user.
     *
     * @param int $id id of user
     *
     * @return Assocation array of user data
     */
    public function getUserById($id)
    {
        $data = DB::table($this->table. ' as u')
            ->join('role_user as ru', 'ru.user_id', '=', 'u.id')
            ->where('id','=',$id)
            ->select('*')
            ->orderBy('id', 'asc')
            ->get()->first();
        return $data;
    }

    /**
     * save user info.
     *
     * @param Request $request.
     *
     * @return bool $success - true/false.
     */
    public function saveUser($request)
    {
        $success = false;
        DB::beginTransaction();
        try {
            $user = $this->getUserData($request);
            $user['agency_id'] = $request->agency_id == 0 ? null : $request->agency_id;
            $user['created_by'] = Auth::user()->id;
            $userId = DB::table($this->table)->insertGetId($user);

            $userRole = new UserRole();
            $userRole->saveUserRole($userId, $request->role_id);

            DB::commit();
            $success = true;
        } catch (\Exception $ex) {
            Log::error('Error :'. $ex);
            DB::rollback();
        }
        return $success;
    }

    /**
     * update user info.
     *
     * @param Request $request.
     * @param string $userId.
     *
     * @return bool $success - true/false.
     */
    public function updateUser($request, $userId)
    {
        $success = false;
        DB::beginTransaction();
        try {
            $user = $this->getUserData($request);
            $user['active'] =  $userId == Auth::user()->id ? 1 : $request->active;
            DB::table($this->table)
                ->where('id', $userId)
                ->update($user);

            if($userId != Auth::user()->id){
                $userRole = new UserRole();
                $userRole->updateUserRole($userId, $request->role_id);
            }

            DB::commit();
            $success = true;
        } catch (\Exception $ex) {
            Log::error('Error :'. $ex);
            DB::rollback();
        }
        return $success;
    }

    /**
     * update user password.
     *
     * @param string $password.
     * @param string $id.
     *
     */
    public function updatePassword($password, $id)
    {
        DB::table($this->table)
            ->where('id', $id)
            ->update(['password' => $password]);
    }

    /**
     * get the list of users.
     *
     * @param integer $take number of records need to fetch, used for displaying data only.
     * @param integer $skip number of records need to skip, used for displaying data only.
     * @param string $search filter criteria's, used for displaying data only.
     * @param string $agency_id filter agency, used for displaying data only.
     * @param string $sortField name of column to sort, used for displaying data only.
     * @param string $sortDirection order of column, used for displaying data only.
     *
     * @return object of users.
     */
    public function getUsers($take = 10, $skip = 0, $search = '', $agency_id = '0', $sortField = 'u.first_name', $sortDirection = 'asc')
    {
        $query = $this->getUsersQuery($search, $agency_id);

        return $data = $query->select(DB::Raw('u.id, u.first_name, u.password, u.last_name, u.email, u.contact_info, r.display_name as role, a.name as agency, CASE WHEN u.active = 1 THEN "Active" ELSE "Inactive" END as active '))
                            ->skip($skip)->take($take)
                            ->orderBy($sortField, $sortDirection)
                            ->get();
    }

    /**
     * get the count of users.
     *
     * @param string $search filter criteria's.
     * @param string $agency_id filter agency.
     *
     * @return count of users.
     */
    public function getUsersCount($search, $agency_id = '0')
    {
        $query = $this->getUsersQuery($search, $agency_id);

        return $query = $query->select('u.id')->count();
    }

    /**
     * return query to get users list.
     *
     * @param string $search filter criteria's.
     * @param string $agency_id filter agency.
     *
     * @return string $query.
     */
    private function getUsersQuery($search, $agency_id)
    {
        $query = DB::table($this->table. ' as u')
                                ->leftJoin('agency as a', 'a.id', '=', 'u.agency_id')
                                ->join('role_user as ru', 'ru.user_id', '=', 'u.id')
                                ->join('roles as r', 'r.id', '=', 'ru.role_id')
                                ->where('u.deleted', 0);

        if(!empty($search)){
            $query = $query->where(function($query) use ($search)
            {
                $query = $query->where('u.first_name', 'LIKE', '%' . $search . '%')
                    ->orwhere('u.last_name', 'LIKE', '%' . $search . '%')
                    ->orwhere('u.email', 'LIKE', '%' . $search . '%')
                    ->orwhere('r.display_name', 'LIKE', '%' . $search . '%')
                    ->orwhere('a.name', 'LIKE', '%' . $search . '%');
            });
        }

        if($agency_id > 0){
            $query = $query->where('a.id', $agency_id);
        }

        return $query;
    }

    /**
     * set the user data.
     * It is used to save/update the user data.
     *
     * @param array $request values of user.
     *
     * @return array of $user
     */
    private function getUserData($request)
    {
        $user = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'contact_info' => $request->contact_info,
            'schedule_color' => $request->schedule_color,
            'updated_by' => Auth::user()->id,
        ];

        return $user;
    }

    /**
     * get the list of all agency users by id.
     *
     * @param string $agency_id.
     *
     * @return array of agency users.
     */
    public function getAgencyUsers($agency_id)
    {
        return DB::table($this->table . ' as u')
            ->select(DB::RAW('u.id, CONCAT_WS(" ", u.first_name, u.last_name) as name, u.schedule_color'))
            ->where('u.agency_id', '=', $agency_id)
            ->where('u.active', '=', 1)
            ->where('u.deleted', '=', 0)
            ->orderBy('name', 'ASC')
            ->get();
    }

    /**
     * delete user by id.
     *
     * @param string $id .
     * @param datetime $current_est_date .
     *
     * @throws \Exception
     */
    public function deleteUser($id, $current_est_date)
    {
        DB::beginTransaction();
        try {
            DB::table('schedules')
                ->where('user_id', $id)
                ->whereDate('date', '>=' , $current_est_date)
                ->delete();

            $user = DB::table($this->table)
                ->where('id', $id)
                ->first();

            DB::table('password_resets')
                ->where('email', $user->email)->delete();

            DB::table($this->table)
                ->where('id', $id)
                ->update(['active'=> 0, 'deleted' => 1]);

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }
}