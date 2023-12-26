<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    public $res,$status_code,$validator;
    function __construct() {
        $this->res = ['status'=>false,'message'=>'Invalid Request.','errors'=>[],'data'=>[],'status_code'=>200];
        $this->validator = false;
    }

    function checkAndGetErrors() {
        $d = [];
        if($this->validator && $this->validator->fails()){            
            $errors = $this->validator->errors();
            $error_arr=$errors->getMessages();
            foreach ($error_arr as $key => $value) {         
                $d[$key] = !isset($d[$key]) ? $value[0] : $d[$key];
            }
            if(count($d)>0){ 
                $this->res['message']=$d[array_key_first($d)]; 
                $this->res['status_code'] = 400;
            }
            $this->res['errors'] = $d;            
        }
        return count($d) > 0 ? false : true;
    }
}
