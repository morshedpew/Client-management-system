<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Service;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::all();

        return view('services', ['services' => $services, 'serial' => 0]);
    }

    public function addServiceView()
    {
        return view('addservice');
    }

    public function addService(Request $request)
    {
        $this->validate($request, [
            'name' => 'required | min:3'
        ], [
            'name.required' => 'Please enter a name.',
            'name.min' => 'Name must contain at least 3 characters.'
        ]);

        $service = new Service;

        $service->name = $request->name;

        $service->save();

        return redirect()->back()->withStatus('Service Added Successfully !');
    }

    public function removeService($service_id)
    {
        $service = Service::find($service_id);

        $service->delete();

        return redirect()->back();
    }

    public function editServiceView($service_id)
    {
        $service = Service::find($service_id);

        return view('editservice', compact('service'));
    }

    public function editService(Request $request, $service_id)
    {
        $this->validate($request, [
            'name' => 'required | min:3'
        ], [
            'name.required' => 'Name can not be empty.',
            'name.min' => 'Name must contain at least 3 characters.'
        ]);

        $service = Service::find($service_id);

        $service->name = $request->name;

        $service->save();

        return redirect()->back()->withStatus('Service Updated Successfully !');
    }
}
