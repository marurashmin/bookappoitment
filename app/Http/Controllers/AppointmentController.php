<?php
namespace App\Http\Controllers;
use App\Models\HealthcareProfessional;
use App\Models\Appointment;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;
use DB;


class AppointmentController extends BaseController{
    
    /**
     * Appointment book API
     * @param healthcare_professional_id
     * @param appointment_start_time
     * @param appointment_end_time
     * @param status
     */
    public function bookAppointment(Request $request){
        $input = $request->all();
        $validator = Validator::make($request->all(), [
            'healthcare_professional_id' => 'required|exists:healthprofessionals,id',
            'appointment_start_time' =>'required|date_format:Y-m-d H:i',
            'appointment_end_time' =>'required|date_format:Y-m-d H:i|after:appointment_start_time',
            'status' =>'required|in:booked,completed,cancelled'
        ]);
        if($validator->fails()){
            return $this->sendError('There are errors in your input data.', $validator->errors());
        }

        $drList = DB::table('appointments')
        ->select('*')
        ->where('healthcare_professional_id','=',$input['healthcare_professional_id'])
        ->where(function ($query) use($input){
            $query->where(function ($query) use($input){
                    $query->where('appointment_start_time','>=',$input['appointment_start_time'])
                        ->where('appointment_end_time','<',$input['appointment_start_time']);
                })
                ->orWhere(function ($query) use($input){
                    $query->where('appointment_start_time','>=',$input['appointment_end_time'])
                        ->where('appointment_end_time','<',$input['appointment_end_time']);
                });
        })
        ->where('status','!=','cancelled')
        ->get();
        if(count($drList) >= 0){
            return $this->sendError('Appointment time is not available', []);
        }
        
        $input['user_id'] = Auth::user()->id;
        $doctor = Appointment::create($input);
        return $this->sendResponse($doctor, __('Appointment booked successfully'));
    }

    /**
     * Get all user appointments
     * @param user_id
     */
    public function userAppointment(Request $request){
        $appointment = Appointment::where('user_id',Auth::user()->id)->get();
        return $this->sendResponse($appointment, __('User Appointment fetch successfully'));
    }

    /**
     * Mark appointment as completed
     * @param appointment_id
     */
    public function markCompleteAppointment(Request $request){
        $validate = Validator::make($request->all(),[
            'appointment_id' =>'required|exists:appointments,id'
        ]);
        if($validate->fails()){
            return $this->sendError('There are errors in your input data.',$validate->errors());
        }
        $input = $request->all();
        $appointment = Appointment::find($request->appointment_id);
        $appointment->status = 'completed';
        $appointment->save();
        return $this->sendResponse($appointment,'Appointment mark completed');
    }

    /**
     * Cancel Appointment
     * @param appointment_id
     */
    public function cancelAppointment(Request $request){
        $validate = Validator::make($request->all(),[
            'appointment_id' =>'required|exists:appointments,id'
        ]);
        if($validate->fails()){
            return $this->sendError('There are errors in your input data.',$validate->errors());
        }
 
        $drList = DB::table('appointments')
        ->select('*')
        ->where('id','=',$request->appointment_id)
        ->where('appointment_start_time', '>=', Carbon::now()->subHours()->toDateTimeString())
        ->get();
        if(count($drList) > 0){
            $appointment = Appointment::find($request->appointment_id);
            $appointment->status = 'cancelled';
            $appointment->save();
            return $this->sendResponse($appointment,'Cancel mark completed');
        }else{
            return $this->sendError('Appointment 24 hours old so not cancel', []);
        }
    
    }

}