<?php
/**
 * Created by PhpStorm.
 * User: mahesh.lavanam
 * Date: 04-07-2018
 * Time: 21:25
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use DB;


class AgencyLocation
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'agency_locations';

    /**
     * Primary key of table.
     *
     * @var string
     */
    protected $primaryKey = "id";

    public function getAgencyLocations($agencyId)
    {
        $data = DB::table($this->table)
            ->select(DB::Raw('location_id'))
            ->where('agency_id', '=', $agencyId)
            ->get();
        return $data;
    }

    public function deleteAgencyLocations($agencyId){
        DB::beginTransaction();
        try {
            DB::table($this->table)
                ->where('agency_id', '=', $agencyId)
                ->delete();
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            throw $ex;
        }
    }

    public function addAgencyLocation($data)
    {
        DB::beginTransaction();
        $success = false;
        try {
            $agencyLocationId = DB::table($this->table)->insertGetId($data);
            if($agencyLocationId){
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