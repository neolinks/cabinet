<?php
/**
 * Created by PhpStorm.
 * User: ernar
 * Date: 11/6/15
 * Time: 2:55 PM
 */

namespace App\Http\Controllers;
use App\Role;
use App\Permission;
use App\User;
class AdministrationController  extends Controller{
    public function createRole(){
        return view('personal.index');
    }
    public function index(){

    }

} 