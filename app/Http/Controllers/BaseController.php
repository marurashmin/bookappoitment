<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Support\Facades\Auth;


class BaseController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message)
    {
        $response = [
            'status' => true,
            'data'    => $result,
            'message' => $message,
        ];
        //dd($response);


        return response()->json($response, 200);
    }

    public function currentuser(Type $var = null)
    {
        return Auth::guard('api2')->check() ? Auth::guard('api2')->user() : false;
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
    	$response = [
            'status' => false,
            //'message' => $errorMessages,
        ];
        // dd($errorMessages);
        if(!empty($errorMessages)){
            if(!is_array($errorMessages)){
                foreach($errorMessages->messages() as $err){
                    $response['message'] = $err[0];
                }

            }else{
                $msg = '';
                foreach($errorMessages as $err){
                    if(is_array($err)){
                        foreach ($err as $er) {
                            $msg .= $er.',';
                        }
                    }else{
                        $msg .= $err[0];
                    }
                }
                $response['message'] = $msg;
            }
        }else{
            $response['message'] = $error;
        }

        /*if(!empty($errorMessages)){
            $response['message'] = $errorMessages;
        }*/
        return response()->json($response, $code);
    }

    public function sendResponseEmpty($result, $message)
    {
        $response = [
            'status' => false,
            'data'    => $result,
            'message' => $message,
        ];
        //dd($response);


        return response()->json($response, 200);
    }

    public function sendResponseEmptyPagination($result, $message,$total)
    {
        $response = [
            'status' => false,
            'data'    => $result,
            'message' => $message,
            'total' => $total,
        ];
        //dd($response);


        return response()->json($response, 200);
    }

    public function sendResponsePagination($result, $message,$total)
    {
        $response = [
            'status' => true,
            'data'    => $result,
            'message' => $message,
            'total' => $total,
        ];
        //dd($response);


        return response()->json($response, 200);
    }

    public function currentuserDoctor(Type $var = null)
    {
        return Auth::guard('api')->check() ? Auth::guard('api')->user() : false;
    }


}