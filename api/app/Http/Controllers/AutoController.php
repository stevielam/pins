<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Requests;

use App\Auto;

class AutoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return Auto::with('relay')->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return "Nope.";
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Validate Input and create new schedule
        $auto = new Auto;
        return $this->validateAndSave($request, $auto);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        return Auto::with('relay')->find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        return "Nope.";
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //Validate Input and update schedule
        $auto = Auto::findOrFail($id);
        return $this->validateAndSave($request, $auto);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //Delete schedule
        Auto::find($id)->delete();
        return "Success";
    }

    public function validateAndSave(Request $request, Auto $auto){
        //Validate input
        $v = Validator::make($request->all(), [
            'master_enable'  => 'required|boolean',
            'monday_enable'  => 'required|boolean',
            'tuesday_enable'  => 'required|boolean',
            'wednesday_enable'  => 'required|boolean',
            'thursday_enable'  => 'required|boolean',
            'friday_enable'  => 'required|boolean',
            'saturday_enable'  => 'required|boolean',
            'sunday_enable'  => 'required|boolean',
            'start_time'    => 'required|date_format:H:i:s',
            'end_time'    => 'required|date_format:H:i:s',
            'relay_id'    => 'required|integer'
        ]);

        if( $v->fails() ){
            return json_encode(array(
                'success' => false,
                'messages'  => $v->messages()
            ));
        }else{
            $auto->master_enable = $request->master_enable;
            $auto->monday_enable = $request->monday_enable;
            $auto->tuesday_enable = $request->tuesday_enable;
            $auto->wednesday_enable = $request->wednesday_enable;
            $auto->thursday_enable = $request->thursday_enable;
            $auto->friday_enable = $request->friday_enable;
            $auto->saturday_enable = $request->saturday_enable;
            $auto->sunday_enable = $request->sunday_enable;
            $auto->start_time = $request->start_time;
            $auto->end_time = $request->end_time;
            $auto->relay_id = $request->relay_id;

            $auto->save();

            return json_encode(array(
                'success'    => true,
                'messages'  => $v->messages()
            ));
        }
    }
}
