<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Source;

class SourceController extends Controller
{
    public function index()
    {
        $sources = Source::all();

        return view('sources', ['sources' => $sources, 'serial' => 0]);
    }

    public function addSourceView()
    {
        return view('addsource');
    }

    public function addSource(Request $request)
    {
        $this->validate($request, [
            'name' => 'required | min:3'
        ], [
            'name.required' => 'Please enter a name.',
            'name.min' => 'Name must contain at least 3 characters.'
        ]);

        $source = new Source;

        $source->name = $request->name;

        $source->save();

        return redirect()->back()->withStatus('Source Added Successfully !');
    }

    public function removeSource($source_id)
    {
        $source = Source::find($source_id);

        $source->delete();

        return redirect()->back();
    }

    public function editSourceView($source_id)
    {
        $source = Source::find($source_id);

        return view('editsource', compact('source'));
    }

    public function editSource(Request $request, $source_id)
    {
        $this->validate($request, [
            'name' => 'required | min:3'
        ], [
            'name.required' => 'Name can not be empty.',
            'name.min' => 'Name must contain at least 3 characters.'
        ]);

        $source = Source::find($source_id);

        $source->name = $request->name;

        $source->save();

        return redirect()->back()->withStatus('Source Updated Successfully !');
    }
}
