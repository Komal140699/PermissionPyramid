<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Task;

class TaskController extends Controller
{
    function __construct() {
        parent::__construct();
    }

    public function List()
    {
        $user = auth()->user();

        // get user role
        $userRole = auth()->user()->getRoleNames();
        $userRole=$userRole[0];
        $role = Role::findByName($userRole);         
        $permissionRequired = 'view-task';

        // checking user permission
        $permission =   $role->hasPermissionTo($permissionRequired);
        if($this->checkAndGetErrors()) {
            if($permission)
            {

                if($userRole=='employee')
                {
                    $rows=Task::where('user_id',$user->id)->orderBy("id","desc")->paginate(5);
                }
                else
                {
                    $rows=Task::orderBy("id","desc")->paginate(5);
                }

                $this->res["message"]='Success';            
                $this->res['status_code'] = 200;
                $this->res['status'] = true;
                $this->res['data'] = $rows;
            }
            else {
                $this->res["message"] ="you are not authorised to view tasks";
            }
        }

        return response()->json($this->res,$this->res['status_code']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'task_name' => 'required'        
        ]);

        // get user role
        $userRole = auth()->user()->getRoleNames();
        $userRole=$userRole[0];
        $role = Role::findByName($userRole);        
        $permissionRequired = 'create-task';

        // checking user permission
        $permission =   $role->hasPermissionTo($permissionRequired);
        if($this->checkAndGetErrors()) {
            if($permission)
            {
                Task::create($request->all());
                $this->res['status_code'] = 200;
                $this->res['status'] = true;
                $this->res['message'] = "Task Created";
            }
            else {
                $this->res["message"] ="you are not authorised to create this task";
            }
        }
        return response()->json($this->res,$this->res['status_code']);

    }

    public function allocate(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:tasks',
            'user_id' => 'required|exists:users,id'     
        ]);

        // get user role
        $userRole = auth()->user()->getRoleNames();
        $userRole=$userRole[0];
        $role = Role::findByName($userRole);         
        $permissionRequired = 'allocate-task';

        // checking user permission
        $permission =   $role->hasPermissionTo($permissionRequired);
        $nuser_id=User::find($request->user_id);
        if($this->checkAndGetErrors()) {
            if($permission)
            {
                if($nuser_id->hasRole('employee')){
                    Task::where('id', $request->id)->update(['user_id' => $nuser_id->id]);
                    $this->res['status_code'] = 200;
                    $this->res['status'] = true;
                    $this->res['message'] = "Task Allocated";
                }
                else
                {
                    $this->res["message"] ="This user can not be allocated this task";
                }
            }
            else {
                $this->res["message"] ="you are not authorised to create this task";
            }
        }
        return response()->json($this->res,$this->res['status_code']);

    }

    public function deleteTask(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:tasks'        
        ]);
        // get user role

        $userRole = auth()->user()->getRoleNames();
        $userRole=$userRole[0];
        $role = Role::findByName($userRole);         
        $permissionRequired = 'delete-task';

        // checking user permission
        $permission =   $role->hasPermissionTo($permissionRequired);
        if($permission)
        {
            Task::where('id',$request->id)->delete();
            $this->res['status_code'] = 200;
            $this->res['status'] = true;
            $this->res['message'] = "Task Created";
        }
        else {
            $this->res["message"] ="you are not authorised to create this task";
        }
        return response()->json($this->res,$this->res['status_code']);

    }

}
