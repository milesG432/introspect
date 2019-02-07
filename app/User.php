<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
Use DB;
Use Hash;

class User extends Authenticatable
{
    use Notifiable;
    
    public function checkUser($username, $password)
    {
        $errors = [
            
        ];
        $login = [
            
        ];
        try
        {
            if($username && $password)
            {
                $results = DB::select("SELECT * FROM staff WHERE username = '" . $username . "' LIMIT 1;");                
                //if(sizeof($results) == 1 && Hash::check($password, $results[0]->password))
                if(sizeof($results) == 1 && $results[0]->password == $password)
                {   
                    $login['id'] = $results[0]->id;
                    $login['loggedIn'] = true;
                    $login['user'] = $results[0] -> firstName;
                    $login['accessLevel'] = $results[0] -> accessLevelID;
                }
                else
                {
                    $errors['noUser'] = "There was no user located with the credentials provided. Please check your details and try again.";
                }
                if(sizeof($login) > 0)
                {
                    return $login;
                }
                elseif(sizeof($errors) > 0)
                {
                    return $errors;
                }
            }
            else
            {
                $errors['Error finding login credentials. Please contact heart systems.'];
            }
        } catch (Exception $ex) {
            $errors['exception'] = $ex->getMessage;
            return $errors;
        }
    }
}
