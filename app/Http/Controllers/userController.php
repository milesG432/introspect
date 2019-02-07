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
            dd($username);
            
            //check email address is valid format
            if($username && strlen($username) > 0 )
            {
                //check password set and over 6 chars long
                if($password && strlen($password) >= 6)
                {
                    $user = new User();
                    $result =  $user->checkUser($email, $password);  
                    
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
                return redirect("login");
            }            
        } catch (Exception $ex) {
            Session::flash('error', $ex->getMessage);
            return redirect('/login');
        }
    }
    
    public function logOut()
    {
        Session::flush();        
        return redirect('/');
    }
    
    public function getAdmins()
    {
        try
        {        
        $user = new User();
        //get users with admin or heart access levels
        $result = $user->getAdmins();
        //if users exist
        if(isset($result['errors']) || isset($result['exception']))
        {
            return view ("admin", ["errors"=>$result]);
            Session::flash('error', $result['errors']);
            return redirect('/admin');
        }
        else if(sizeof($result) > 0)
        {
            return view ("admin", ["admins"=>$result]);
        }
        
        } catch (Exception $ex) {
            Session::flash('error', $ex->getMessage);
            return redirect('/admin');
        }        
    }
    
    public function newAdmin(request $request)
    {
        try
        {
            $user = new User();
            $admins = $user->getAdmins();
            //dd($admins);
            foreach($admins as $admin)
            {                
                if($admin->email == $request['email'])
                {
                    Session::flash('error', 'Email address already in use.');
                    return redirect('/admin');
                }
            }            
            //validate firstName field
            if($request['firstName'] && strlen($request['firstName']) > 1)
            {
                $firstName = $request['firstName'];
            }
            else
            {
                Session::flash('error', 'First name can not be blank');
                $errors['firstName'] = "First Name field can not be blank";
            }
            
            //validate lastName field
            if($request['lastName'] && strlen($request['lastName']) > 1)
            {
                $lastName = $request['lastName'];
            }
            else
            {
                Session::flash('error', 'Last name must not be blank');                
            }
            
            //validate email
            if($request['email'] && strpos($request['email'], "@") !== false)
            {
                $email = $request['email'];
            }
            else
            {
                Session::flash('error', 'Email address can not be blank and must be in a standard email format');                
            }
            
            //validate password
            if($request['password'] && strlen($request['password']) >= 6)
            {
                $password = Hash::make($request['password']);
            }
            else 
            {
                Session::flash('error', 'Password must not be blank and must be at least 6 characters long.');                
            }
            //if errors return to admin screen and display errors else proceed to insert new admin
            if(Session::has('error'))
            {
                return redirect ('/admin');
            }
            else 
            {       
                $result = $user->addAdmin($firstName, $lastName, $email, $password, $request['accessLevel'], $request['company']);
            }
            
            if(true == $result)
            {                
                Session::flash('message', 'User ' . $firstName . ' ' . $lastName . ' created.');
                return redirect('/admin');
            } else 
            {
                Session::flash('error', 'There has been a problem creating this admin. Please try again');
                return redirect('/admin');
            }            
        } catch (Exception $ex) {
            Session::flash('error', $ex->getMessage);
            return redirect('/admin');
        }
    }
    
    public function editAdmin()
    {
        try
        {
            $errros = [];
            
            $id = $_GET['id'];
            $user = new User();            
            $admin = $user->getAdmins($id);
            if(sizeof($admin) > 0)
            {
               echo json_encode($admin);
            }
            else 
            {
               $errors['noAdmin'] = "Could not find specified admin details.";
            }
           
        } catch (Exception $ex) {
            $errros['noAdmin'] = $ex->getMessage;
        }
    }
    
    public function insertEdittedAdmin(request $request)
    {
        try
        {
            if($request['id'])
            {
                $admin = 
                [
                    'id' => $request['id'],
                    'firstname' => $request['firstName'],
                    'lastname' => $request['lastName'],
                    'email' => $request['email'], 
                    'password' => $request['password'],
                    'accessLevel' => $request['accessLevel'],
                    'company' => $request['company']
                ];
                
                $user = new User();
                $result = $user->insertEdittedAdmin($admin);
                if(1 == $result)
                {
                    Session::flash('message', "Admin has been successfully edited, well done you!");
                    return redirect('/admin');
                }
                else
                {
                    Session::flash('error', "Account could not be edited, Oh flip. :(");
                    return redirect('/admin');
                }
            }
            else 
            {
                Session::flash('error', "Admin account not found. Please contact Dev.");
                return redirect('admin');
            }
        } catch (Exception $ex) {
            Session::flash('error', $ex->getMessage);
            return redirect('/admin');
        }
    }
    
    public function deleteAdmin()
    {
        try
        {
            $id = $_GET['id'];
            if($id)
            {
                $user = new User();
                $admins = $user->getAdmins();
                $result = $user->deleteAdmin($id);
                if(1 == $result)
                {
                    Session::flash('message', 'Admin deleted');
                    return redirect('/admin');
                }
                else
                {                    
                    Session::flash('error', 'There was a problem deleting this admin. Please contact the site administrator');
                }
            }
            else
            {                
                Session::flash('error', 'Unable to delete admin at this time. Please consult the Necronomicon ');
            }
            if(Session::has('error'))
            {
                return redirect('/admin');                
            }
        } catch (Exception $ex) {
            Session::flash('error', $ex->getMessage);
            return redirect('/admin');
        }
    }
    
}
