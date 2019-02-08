<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Session;
use Hash;

class userController extends Controller
{
    //validate user input and pass to User model if ok
    public function logIn(request $request)
    {        
        try
        {
            $username = $request['username'];
            $password = $request['password'];    
            //dd($username);
            
            //check email address is valid format
            if($username && strlen($username) > 0 )
            {
                //check password set and over 6 chars long
                if($password && strlen($password) >= 6)
                {
                    $user = new User();
                    $result =  $user->checkUser($username, $password);  
                    
                    //if login successfule set user session values
                    if(isset($result['loggedIn']) && $result['loggedIn'] == true)
                    {                        
                        Session()->put('id', $result['id']);
                        Session()->put('loggedIn',true);
                        Session()->put('user', $result['user']);
                        Session()->put('level', $result['accessLevel']);                         
                        //redirect to home and show full navebar
                        return redirect("/");
                    } else {
                        Session::flash('error', 'User not found. Please check your details and try again.');                        
                    }
                } 
                else 
                {
                    Session::flash('error', 'There is a problem with the supplied password. Please check your details and try again');
                }
            }
            else 
            {
                Session::flash('error', 'There is a problem with the supplied username. Please check your details and try again.');
            }
            //if login failed redirect to login page and show errors
            if(Session::has('error'))
            {
                return redirect("/");
            }            
        } catch (Exception $ex) {
            Session::flash('error', $ex->getMessage);
            return redirect('/');
        }
    }
    
    public function logOut()
    {
        Session::flush();        
        return redirect('/');
    }
    
    public function getStaff()
    {
        try
        {
            $staff = new User();
            $result = $staff->getStaff();            
            if(isset($result['errors']) || isset($result['exception']))
            {
                return view ("admin", ["errors"=>$result]);
                Session::flash('error', $result['errors']);
                return redirect('/Admin');
            }
            else if(sizeof($result) > 0)
            {
                return view ("admin", ["staff"=>$result]);
            }
            
            
            
        } catch (Exception $ex) {
            Session::flash('error', $ex->getMessage);
            return redirect('/Admin');
        }
    }
}
