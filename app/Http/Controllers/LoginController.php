<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use JWTAuth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class LoginController extends Controller
{
    //
    function __construct() {
        parent::__construct();
    }

    public function login(Request $request)
    {
        $user= User::where("email",$request->username)->first();
        $this->res['data'] = null;

        if($this->checkAndGetErrors()) {
            if($token = JWTAuth::attempt(["email"=>$request->username,"password"=>$request->password]))
            {   
                $customClaims = [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->getRoleNames(),
                    'permissions' => $user->getAllPermissions() 
                ];

                $token = JWTAuth::claims($customClaims)->attempt(["email"=>$request->username,"password"=>$request->password]);
                $user->authorization_token = compact('token');
                $token=$user->createToken('authToken')->accessToken;
                $this->res["message"] ="You are now Logged-In";
                $this->res['status_code'] = 200;
                $this->res['status'] = true;
                $this->res['user'] = $user;

            }
            else{
                $this->res['status_code'] = 401;
                $this->res['status'] = true;
                $this->res['message'] = 'Invalid Credentials';
            }
        }
        return response()->json($this->res,$this->res['status_code']);

    }

    public function List()
    {
        $user = auth()->user();
        // get user role
        $userRole = auth()->user()->getRoleNames();
        $userRole=$userRole[0];
        $role = Role::findByName($userRole);         
        $permissionRequired = 'view-employee';

        // checking user permission
        $permission =   $role->hasPermissionTo($permissionRequired);
        if($this->checkAndGetErrors()) {
            if($permission)
            {
                $rows=User::orderBy("id","desc")->paginate(5);
                $this->res["message"]='Success';            
                $this->res['status_code'] = 200;
                $this->res['status'] = true;
                $this->res['data'] = $rows;
            }
            else {
                $this->res["message"] ="you are not authorised to view employees";
            }
        }

        return response()->json($this->res,$this->res['status_code']);
    }

    public function register(Request $request)
    {
        $this->validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => ['required',Password::min(8)->letters()->mixedCase()->numbers()],
            'password_confirmation' => 'required|same:password',
            'role'=>['required','in:dept_head,employee']
        ]);

        if($this->checkAndGetErrors()) {
            $characters ="0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
            $user = User::create([
                "name" => $request->name,
                "email" => $request->email,
                "password" => Hash::make($request->password),
            ]);            

            // AssignRole to the newly created user
            $user->assignRole(Role::where('name',$request->role)->first());
           
            $this->res['status_code'] = 200;
            $this->res['status'] = true;
            $this->res['message'] = "Your account has been created successfully. We've sent a verification email to your email address.";
            $this->res['encrypted_user_id']= base64_encode($user->id);
        }

        return response()->json($this->res,$this->res['status_code']);
    }

    public function deleteUser(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users'        
        ]);

        // get user role
        $userRole = auth()->user()->getRoleNames();
        $userRole=$userRole[0];
        $role = Role::findByName($userRole);   
        $permissionRequired = 'delete-employee';

        // checking user permission
        $permission =   $role->hasPermissionTo($permissionRequired);

        if($permission)
        {
            User::where('id',$request->id)->delete();
            $this->res['status_code'] = 200;
            $this->res['status'] = true;
            $this->res['message'] = "Deleted Employee";
        }
        else {
            $this->res["message"] ="you are not authorised to delete this user";
        }
        return response()->json($this->res,$this->res['status_code']);

    }

}
