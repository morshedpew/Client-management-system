<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PersonFormRequest;
use App\Http\Requests\PersonEditFormRequest;

use App\Person;

class PersonController extends Controller
{
    public function index()
    {
        $persons = Person::all();

        return view('persons', ['persons' => $persons, 'serial' => 0]);
    }

    public function addPersonView()
    {
        return view('addperson');
    }

    public function addPerson(PersonFormRequest $request)
    {
        $person = new Person;

        $person->name = $request->name;
        $person->designation = $request->designation;
        $person->contact_number = $request->contact_number;
        $person->email = $request->email;

        $person->save();

        return redirect()->back()->withStatus('Office Member Added Successfully !');
    }

    public function removePerson($person_id)
    {
        $person = Person::find($person_id);

        $person->delete();

        return redirect()->back();
    }

    public function editPersonView($person_id)
    {
        $person = Person::find($person_id);

        return view('editperson', compact('person'));
    }

    public function editPerson(PersonEditFormRequest $request, $person_id)
    {
        $person = Person::find($person_id);

        $person->name = $request->name;
        $person->designation = $request->designation;

        $person_contact_numbers = Person::where('contact_number', '=', $request->contact_number)->get();

        if(count($person_contact_numbers) == 0)
        {
            $person->contact_number = $request->contact_number;
        }
        else if(count($person_contact_numbers) == 1 && $person_contact_numbers[0]['contact_number'] != $person->contact_number)
        {
            return redirect()->back()->with(['error_contact_number' => 'The phone number - '.$request->contact_number.' already exists.']);
        }


        $person_emails = Person::where('email', '=', $request->email)->get();

        if(count($person_emails) == 0)
        {
            $person->email = $request->email;
        }
        else if(count($person_emails) == 1 && $person_emails[0]['email'] != $person->email)
        {
            return redirect()->back()->with(['error_email' => 'The email - '.$request->email.' already exists.']);
        }


        $person->save();

        return redirect()->back()->withStatus('Office Member Updated Successfully !');
    }
}
