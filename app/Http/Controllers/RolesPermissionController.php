<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class RolesPermissionController extends Controller{
    public function all_users_roles_permissions(){   
        $all_permissions = Permission::orderBy('id', 'asc')->get();
        $all_roles = Role::all();
        $all_users_role_permission = User::with('roles')->get();      
        return view('users.index', compact('all_users_role_permission','all_permissions','all_roles'));
    }

    // public function search_user(Request $request){
    //     $search = $request->get('search');
    //     $emails = User::when($search, fn($q) =>
    //         $q->where('email', 'like', "%{$search}%")
    //     )->orderBy('id', 'desc')->paginate(10);

    //     return view('users.index', compact('emails', 'search'));
    // }

    public function edit_user_role_permissions($id){
        $get_user = User::find($id);
        $all_roles = Role::all();
        $all_permissions = Permission::orderBy('id', 'asc')->get();
        $get_current_role = $get_user->getRoleNames()->toArray();
        $get_current_permissions = $get_user->getPermissionNames()->toArray();
        
        return view('users.edit-user', compact('get_user','get_current_role', 'get_current_permissions','all_roles','all_permissions'));
    }

    public function set_role_permission_to_a_user(Request $request, $id){
        $role_id = $request->input('user_role');
        $permissions = $request->input('user_permissions');
        
        $user = User::find($id);
        $role = Role::find($role_id); 

        // sync role
        $user->syncRoles($role); 

        // sync permissions   
        $user->syncPermissions($permissions);        
        
        return redirect('/admin/users')->with('status', 'User Role and Permissions Updated!');
        
    }

    
    public function remove_user_role_permissions($id){        
        $user = User::find($id);        
        $logged_in_user = Auth::user();  

        $get_current_role = $user->getRoleNames()->toArray();
        $get_current_role_str = implode(' ', $get_current_role); 

        if(!empty($get_current_role_str) &&  $logged_in_user->email != $user->email){  
        $user->removeRole($get_current_role_str);
        }

        $get_current_permissions = $user->getPermissionNames()->toArray(); 

        if(!empty($get_current_permissions) &&  $logged_in_user->email != $user->email){         
        $user->revokePermissionTo($get_current_permissions);
        }
       
        return redirect('/admin/users')->with('status', 'User Role and Permissions Deleted!');
        
    }

    public function delete_user_role_permissions($id){
        $logged_in_user = Auth::user();  
        $user = User::find($id);
        if($logged_in_user->email != $user->email){
            $user->delete();  
            return redirect('/admin/users')->with('status', 'User Deleted Successfully!');
        }else{
            return redirect('/admin/users')->with('status', 'You can not delete the user!');
        }
    }


}
