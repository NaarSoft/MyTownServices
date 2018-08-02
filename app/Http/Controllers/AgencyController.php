<?php

namespace App\Http\Controllers;

use App\Http\Requests\AgencyFormRequest;
use App\Models\Agency;
use App\Models\AgencyLocation;
use App\Models\Service;
use App\Models\Location;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AgencyController extends Controller
{
    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    /**
     * Return Agency Index view.
     *
     * @return view
     */
    public function index()
    {
        return view('admin.agency.index');
    }

    /**
     * Return agency create view.
     *
     * @param Request $request
     *
     * @return view
     */
    public function create(Request $request)
    {
        $services = Service::pluck('name', 'id')->all();

        $data = array('services' => $services);
        return view('admin.agency.create')->with($data);
    }

    /**
     * Return Agency edit view.
     *
     * @param Request $request
     *
     * @return view
     */
    public function edit(Request $request)
    {
        $agency = Agency::findOrFail($request->id);
        $agencyLocationObj = new AgencyLocation();
        $agencyLocationsData = $agencyLocationObj->getAgencyLocations($agency->id);
        $agencyLocations = array();
        if($agencyLocationsData){
            foreach($agencyLocationsData as $agencyLocationRow){
                array_push($agencyLocations, $agencyLocationRow->location_id);
            }
        }
        $services = Service::pluck('name', 'id')->all();
        $locationObj = new Location();
        $locationsArray = $locationObj->getLocations();
        $locations = array();
        foreach($locationsArray as $locationDetails){
            $locations[$locationDetails->id] = $locationDetails->location;
        }
        $data = array('agency' => $agency, 'agency_locations' => $agencyLocations, 'services' => $services, 'locations' => $locations);
        return view('admin.agency.edit')->with($data);
    }

    /**
     * Create agency and redirect to index page.
     *
     * @param AgencyFormRequest $request
     *
     * @return view
     */
    public function add(AgencyFormRequest $request)
    {
        $agency = new Agency();
        $agency->name = $request->name;
        $agency->address = $request->address;
        $agency->contact_info = $request->contact_info;
        $agency->website = $request->website;
        $agency->htmlcontent = $request->htmlcontent;
        $agency->service_id = $request->service_id;
        $agency->save();

        return redirect('admin/agency/index');
    }

    /**
     * Update agency and redirect to index page.
     *
     * @param AgencyFormRequest $request
     * @param $id
     *
     * @return view
     */
    public function update(AgencyFormRequest $request, $id)
    {
        $agency = Agency::find($id);

        if (isset($request->image) && !is_null($request->image)) {
            // Create PDF folder in storage folder
            $storage_dir =  public_path('assets'.DIRECTORY_SEPARATOR.'agency'. DIRECTORY_SEPARATOR);

            if(!is_dir($storage_dir))
                mkdir($storage_dir, 0777);

            $file_name = $id.'.'.$request->image->getClientOriginalExtension();
            $request->image->move($storage_dir, $file_name);
            $agency->image_path = $file_name;
        }

        // If record exists then update otherwise create
        $agency->id = $id;
        $agency->name = $request->name;
        $agency->address = $request->address;
        $agency->contact_info = $request->contact_info;
        $agency->website = $request->website;
        //$agency->htmlcontent = $request->htmlcontent;
        $agency->service_id = $request->service_id;
        $agency->save();

        $service = Service::find($request->service_id);
        $service->name = $request->service_name;
        $service->save();

        //delete all agency locations
        $agencyLocation = new AgencyLocation();
        $agencyLocation->deleteAgencyLocations($id);
        if(count($request->agency_locations)){
            foreach($request->agency_locations as $agencyLocationId){
                $data = array(
                    'agency_id' => $id,
                    'location_id'=> $agencyLocationId
                );
                $agencyLocation->addAgencyLocation($data);
            }
        }


        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        return redirect('admin/agency/index');
    }

    /**
     * Delete agency and return success json.
     *
     * @param $id
     *
     * @return view
     */
    public function delete($id)
    {
        $success = false;
        try{
            $agency = Agency::find($id);
            $agency->delete();
            $success = true;
        }catch(\Exception $ex){
            Log::error('Error :'. $ex);
        }
        return response()->json(['success'=> $success]);
    }

    /**
     * Get list of agencies from database.
     *
     * @param Request $request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAgencies(Request $request)
    {
        try{
            $take = json_decode($request->length);
            $skip = json_decode($request->start);
            $search = !empty($request->search)  ? $request->search : '';
            $sortColumnIndex = $request->order[0]['column'];
            $sortDirection = $request->order[0]['dir'];
            $sortField = $request->columns[$sortColumnIndex]['name'];

            $agency = new Agency();

            $response['data'] = $agency->getAgency($take, $skip, $search, $sortField, $sortDirection);
            $response['recordCount'] = $agency->getAgencyCount($search);

            $request->session()->forget('contact_id');
            return response()->json(['draw'=> $request->draw, 'recordsTotal'=> $response['recordCount'], 'recordsFiltered' => $response['recordCount'], 'data' => $response['data']]);
        }catch(\Exception $ex){
            Log::error('Error :'. $ex);
        }
    }
}
