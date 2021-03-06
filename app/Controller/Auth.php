<?php
/**
 * StupidlySimple - A PHP Framework For Lazy Developers
 *
 * @package		StupidlySimple
 * @author		Fariz Luqman <fariz.fnb@gmail.com>
 * @copyright	2017 Fariz Luqman
 * @license		MIT
 * @link		https://stupidlysimple.github.io/
 */
namespace Controller;

use Sentry;
use Response;
use Request;
use Viewer;

class Auth {
    public function __construct()
    {
    }

    public function displayRegisterPage()
    {
        Viewer::file('resources/views/auth/register');
    }

    public function displayLoginPage()
    {
        Viewer::file('resources/views/auth/login');
    }

    public function doAuthenticate()
    {
        try{
            // Login credentials
            $credentials = array(
                'email'    => Request::get('email'),
                'password' => Request::get('password')
            );
            // Authenticate the user
            $user = Sentry::authenticate($credentials, false);

        }catch (\Cartalyst\Sentry\Users\LoginRequiredException $e){
            Response::redirect('login')->with([
                'login_message'=>'Login credentials not supplied',
                'type'         =>'alert-danger'
            ]);

        }catch (\Cartalyst\Sentry\Users\PasswordRequiredException $e){
            Response::redirect('login')->with([
                'login_message'=>'Password field is required',
                'type'         =>'alert-danger'
            ]);

        }catch (\Cartalyst\Sentry\Users\WrongPasswordException $e){
            Response::redirect('login')->with([
                'login_message'=>'Wrong password, try again.',
                'type'         =>'alert-danger'
            ]);

        }catch (\Cartalyst\Sentry\Users\UserNotFoundException $e){
            Response::redirect('login')->with([
                'login_message'=>'User not found.',
                'type'         =>'alert-danger'
            ]);

        }catch (\Cartalyst\Sentry\Users\UserNotActivatedException $e){
            Response::redirect('login')->with([
                'login_message'=>'User is not activated.',
                'type'         =>'alert-danger'
            ]);

        }finally{
            if(Sentry::check() === true){
                Admin::redirectToAdminHome();
            }else{
                Response::redirect('login')->with([
                    'login_message'=>'Unable to login',
                    'type'         =>'alert-danger'
                ]);
            }
        }
    }

    public function doRegister(){
        try{
            $user = Sentry::register(array(
                'email'    => Request::get('email'),
                'password' => Request::get('password'),
                'first_name' => Request::get('first_name'),
                'last_name' => Request::get('last_name')
            ), $activate = true);

        }catch (\Cartalyst\Sentry\Users\LoginRequiredException $e){
            Response::redirect('register')->with([
                'login_message'=>'Login credentials not supplied',
                'type'         =>'alert-danger'
            ]);

        }catch (\Cartalyst\Sentry\Users\PasswordRequiredException $e){
            Response::redirect('register')->with([
                'login_message'=>'Password field is required',
                'type'         =>'alert-danger'
            ]);

        }catch (\Cartalyst\Sentry\Users\UserExistsException $e){
            Response::redirect('register')->with([
                'login_message'=>'User with that login already exist.',
                'type'         =>'alert-danger'
            ]);

        }catch(\Exception $e){

        }finally{
            if($user){
                Response::redirect('login')->with([
                    'login_message'=>'Registration successful. You can now login.',
                    'type'         =>'alert-success'
                ]);
            }
        }
    }

    public function doLogout(){
        Sentry::logout();
        Response::redirect('login');
    }

}
