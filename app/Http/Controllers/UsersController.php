<?php
/**
 * Created by PhpStorm.
 * User: ernar
 * Date: 11/30/15
 * Time: 5:10 PM
 */

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Role;
use App\User;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller{
    public function userlists(){
        $users = User::paginate(15);
        return view('personal.users.list',['users'=>$users]);
    }
    public function userEdit($id){
        $user = User::find($id);
        $roles = Role::all();
        return view('personal.users.edit',['user'=>$user,'roles'=>$roles]);
    }
    public function postUserEdit(Request $request){
        $id = $request->get("id");
        $user = User::find($id);
        $user->login = $request->get("login");
        $user->name = $request->get("name");
        $user->email = $request->get("email");
        $role_id = head($user->roles->toArray());
        $role_id = $role_id['id'];
        if(isset($role_id))
            $user->roles()->detach($role_id);
        $user->roles()->attach($request->get('role_select'));
        if(strlen($request->get('password')) != 0 && strlen($request->get('confirm_password')) != 0){
            $user->password = Hash::make($request->get("password"));
        }
        $user->save();
        return redirect('/personal/user/list');
    }
    public function deleteUser($id){
        if(isset($id)){
            $user = User::find($id);
            $user->delete();
            return redirect('/personal/user/list');
        }
    }
    public function createUser(Request $request){
        $roles = Role::all();
        return view('personal.users.create',['roles'=>$roles]);
    }
    public function postCreateUser(Request $request){
        $user = new User();
        $user->login = $request->get("login");
        $user->name = $request->get("name");
        $user->email = $request->get("email");
        $user->password = Hash::make($request->get("password"));
        $user->save();
        $user->roles()->attach($request->get("role_select"));
        return redirect('/personal/user/list');
    }
    private function getRole($user){
        $role = head($user->roles->toArray());
        $role = $role['display_name'];
        return $role;
    }
} 