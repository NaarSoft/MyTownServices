<?php
/**
 * Created by PhpStorm.
 * Date: 06-07-2018
 * Time: 02:22
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use DB;


class UserLocation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_locations';

    /**
     * Primary key of table.
     *
     * @var string
     */
    protected $primaryKey = "id";

    public function getUserLocations($userId)
    {
        $data = DB::table($this->table)
            ->select(DB::Raw('location_id'))
            ->where('user_id', '=', $userId)
            ->where('is_deleted', '=', 0)
            ->get();
        return $data;
    }

    public function deleteUserLocations($userId){
        DB::beginTransaction();
        try {
            DB::table($this->table)
                ->where('user_id', '=', $userId)
                ->where('is_deleted', '=', 0)
                ->delete();
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }

    public function addUserLocation($data)
    {
        DB::beginTransaction();
        $success = false;
        try {
            $userLocationId = DB::table($this->table)->insertGetId($data);
            if($userLocationId){
                $success = true;
            }
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
        return $success;
    }
}