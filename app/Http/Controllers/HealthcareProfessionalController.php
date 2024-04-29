<?php
namespace App\Http\Controllers;

use App\Models\Healthprofessionals;
use Illuminate\Http\Request;
use Validator;


class HealthcareProfessionalController extends BaseController{
    public function allHealthcareProfessional(Request $request){
        $doctor = Healthprofessionals::all();
        return $this->sendResponse($doctor,__('All Healthcare Professional'));
    }
    /***
     * Description : crete doctor api
     */
    public function addEditHealthcareProfessional(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'specialty' =>'required',
            'id'=>'sometimes|nullable|exists:healthprofessionals,id'
        ]);
        if($validator->fails()){
            return $this->sendError(__('api_messages.validationError'), $validator->errors());
        }
        if($request->input('id')){
            $doctor = Healthprofessionals::find($request->input('id'));
            $doctor->update($request->all());
            $message = 'update';
        }else{
            $doctor = Healthprofessionals::create($request->all());
            $message = 'added';
        }
        return $this->sendResponse($doctor, 'Health care Professional '.$message.' successfully');
    }
}