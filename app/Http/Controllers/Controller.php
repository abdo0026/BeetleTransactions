<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;


class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function InternalDispatcher(Request $request) :object
    {
        $returnedData = new \stdClass;

        preg_match("/[^\/]+$/", $request->url(), $matches);
        $internalRequest = $matches[0];
//        $internalRequest = $requestedAPI ? $requestedAPI : $request->get('requestedAPI');
        if (!$internalRequest) {
            $returnedData->Error = ['something went wrong! please restart and try again'];
            return response()->json($returnedData , Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            \DB::connection()->getPdo()->beginTransaction();
            $code = null;
            $returnedData->Warning = array();
            $requestArray = $request->all();
            
            if(!isset($requestArray['check_warning'])){
               $requestArray['check_warning'] = true;
            }

            call_user_func_array([$this, $internalRequest], [$requestArray,&$returnedData]);

            $output =  new \stdClass;
            
            

            if(isset($returnedData->Error)) {
               \DB::connection()->getPdo()->rollBack();

                if(isset($returnedData->code)) {
                    $code = $returnedData->code;
                    unset($returnedData->code);
                }
                else
                    $code = Response::HTTP_UNPROCESSABLE_ENTITY;

                if(!is_array($returnedData->Error)){
                    $msg = $returnedData->Error;
                    $returnedData->Error = array($msg);
                }
                    
                if(sizeof($returnedData->Error) == 0){
                   $returnedData->Error[0] = Response::$statusTexts[$code];
                }
                $output->Error = $returnedData->Error;
            }elseif($requestArray['check_warning'] && isset($returnedData->Warning) && sizeof($returnedData->Warning) > 0){
                \DB::connection()->getPdo()->rollBack();
                if(isset($returnedData->code)) {
                    $code = $returnedData->code;
                    unset($returnedData->code);
                }
                else
                    $code = Response::HTTP_OK;
                   
                $returnedData->request_body = $request->all();
                $output->data = $returnedData;
                $output->current_datetime = Carbon::now()->toDateTimeString();
            }
            else
            {
                if(isset($returnedData->code)) {
                    $code = $returnedData->code;
                    unset($returnedData->code);
                }
                else
                    $code = Response::HTTP_OK;

                
                if(isset($returnedData->Warning)){
                    unset($returnedData->Warning);
                }
                
                $output->data = $returnedData;
                $output->current_datetime = Carbon::now()->toDateTimeString();

                if(isset($returnedData->no_need_commit) && $returnedData->no_need_commit == true) {
                    unset($output->data->no_need_commit);
                    \DB::connection()->getPdo()->rollBack();
                }
                else {
                    if(isset($returnedData->no_need_commit))
                        unset($output->data->no_need_commit);
                    \DB::connection()->getPdo()->commit();
                }
            }

            if(isset($returnedData->return_link))
                return $returnedData->return_link;

            return response()->json($output , $code);
        } catch (\PDOException $e) {
            Log::info($e);
            \DB::connection()->getPdo()->rollBack();
            $returnedData->Error = ['something went wrong! please restart and try again' , $e->getMessage()];
            return response()->json($returnedData , Response::HTTP_UNPROCESSABLE_ENTITY);
        }

    }

}
