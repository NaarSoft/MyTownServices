<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use DB;

class Location extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'locations';

    /**
     * Primary key of table.
     *
     * @var string
     */
    protected $primaryKey = "id";

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * get the list of locations.
     *
     * @return object of locations.
     */
    public function getLocations()
    {
        $data = DB::table($this->table)
            ->select(DB::Raw('id, location') )
            ->orderBy('id')
            ->get();
        return $data;
    }

    /**
     * get the count of Locations.
     *
     * @return count of locations.
     */
    public function getLocationsCount()
    {
        $data = DB::table($this->table)
            ->select('id')
            ->count();
        return $data;
    }

    /**
     * save location.
     *
     * @param Request $request.
     *
     * @return bool $success true/false.
     */
    public function addLocation($request)
    {
        $success = false;

        DB::beginTransaction();
        try {
            $location = new Location();
            $location->location = $request->location;
            $location->created_by = \Auth::user()->id;
            $location->save();

            DB::commit();
            $success = true;
        } catch (\Exception $ex) {
            DB::rollback();
            Log::error('Error :'. $ex);
        }
        return $success;
    }

    public function getLocationDetails($id)
    {
        $data = DB::table($this->table)
            ->select(DB::Raw('id, location') )
            ->where('id','=',$id)
            ->get()->first();
        return $data;
    }

    public function updateLocation($request, $locationId)
    {
        $success = false;
        DB::beginTransaction();
        try {
            $data['location'] = $request['location'];
            DB::table($this->table)
                ->where('id', $locationId)
                ->update($data);
            DB::commit();
            $success = true;
        } catch (\Exception $ex) {
            Log::error('Error :'. $ex);
            DB::rollback();
        }
        return $success;
    }
}
