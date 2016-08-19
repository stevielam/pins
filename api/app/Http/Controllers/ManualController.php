<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Requests;

use App\Manual;

class ManualController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return Manual::with('relay')->get();
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
        $man = new Manual;
        return $this->validateAndSave($request, $man);
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
        return Manual::with('relay')->find($id);
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
        $man = Manual::findOrFail($id);
        return $this->validateAndSave($request, $man);
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
        Manual::find($id)->delete();
        return "Success";
    }

    public function validateAndSave(Request $request, Manual $man){
        //Validate input
        $v = Validator::make($request->all(), [
            'mode'  => 'required',
            'end_time'    => 'required|date_format:H:i:s',
            'relay_id'    => 'required|integer'
        ]);

        if( $v->fails() ){
            return json_encode(array(
                'success' => false,
                'messages'  => $v->messages()
            ));
        }else{
            $man->mode = $request->mode;
            $man->end_time = $request->end_time;
            $man->relay_id = $request->relay_id;

            $man->save();

            return json_encode(array(
                'success'    => true,
                'messages'  => $v->messages()
            ));
        }
    }
}
