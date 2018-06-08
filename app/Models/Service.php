<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use PDO;

class Service extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'services';

    /**
     * Primary key of table.
     *
     * @var string
     */
    protected $primaryKey = "id";

    /**
     * get the list of service exist in DB for selected service id.
     *
     * @param string $serviceId.
     *
     * @return object service.
     */
    public function getServiceInfoById($serviceId)
    {
        $data = DB::table($this->table. ' as s')
            ->join('agency as a', 'a.service_id', '=', 's.id')
            ->orderBy('s.position', 'asc')
            ->where('s.id', $serviceId)
            ->get()->first();
        return $data;
    }

    /**
     * get the list of all available dates for booking appointment.
     *
     * @param string $serviceIds.
     * @param date $bookingDate.
     *
     * @return schedule.
     */
    public function getAvailableSlotsForBooking($serviceIds, $bookingDate= null){
        //$data =  DB::select('call usp_getavailableslotsforbooking(?,?,?)', array(trim($serviceIds), $responseId, $bookingDate));
        $data =  DB::select('call usp_getavailableslotsforbooking(?,?)', array(trim($serviceIds), $bookingDate));
        return $data;
    }

    /**
     * get the service exist in DB for selected agency id.
     *
     * @param string $agencyId.
     *
     * @return object service.
     */
    public function getServiceInfoByAgencyId($agencyId)
    {
        $data = DB::table($this->table. ' as s')
            ->join('agency as a', 'a.service_id', '=', 's.id')
            ->orderBy('s.position', 'asc')
            ->where('a.id', $agencyId)
            ->get()->first();
        return $data;
    }
}
