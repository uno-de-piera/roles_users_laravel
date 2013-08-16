<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	return View::make('hello');
});




//http://localhost/roles_users_laravel/public/user
Route::get("user", function()
{

	$user = new User;
	$user->role_id = 3;
	$user->username = "unodepiera";
	$user->email = "unodepiera@uno-de-piera.com";
	$user->password = Hash::make("123456");
	$user->save();

});



//rutas para usuarios no logueados
Route::group(array('before' => 'guest'), function()
{

	Route::get("login", function()
	{

		return View::make('login');

	});

});


//http://localhost/roles_users_laravel/public/init
//iniciamos sesión
Route::get("init", function()
{

	$loginData = array(
        "username" => "unodepiera",
        "password" => 123456
    );
    //si el usuario se loguea correctamente lo mandamos a la home
    if(Auth::attempt($loginData, true))
    {

    	return Redirect::to("home");

    }

});

//devuelve el nombre del rol del usuario según el número
if(!function_exists('setRole'))
{
	function getRole($role)
	{

		switch ($role) {
			case 0:
				return "Invitado.";
				break;
			case 1:
				return "Suscriptor.";
				break;
			case 2:
				return "Editor.";
				break;
			case 3:
				return "Administrador.";
				break;			
			default:
				return "Invitado.";
				break;
		}

	}
}

/*ruta con el prefijo home que comprueba primero si 
//existe la sesión, si es así comprueba el rol del usuario
/
/admin: 3
/editor: 2
/suscriptor: 1
/invitado: 0
/
*/
Route::group(array('prefix' => 'home','before' => 'auth'), function()
{

	//http://localhost/roles_users_laravel/public/home/admin
	//sólo pueden acceder usuarios con role_id 3
	Route::get('admin',array("before" => "roles:3,home", function()
	{

		return "Como mínimo tu role debe ser administrador, tu eres " . getRole(Auth::user()->role_id);
	    
	}));

	//http://localhost/roles_users_laravel/public/home/new_post
	//sólo pueden acceder usuarios con role_id 2 y 3
	Route::get('new_post',array("before" => "roles:2-3,home", function()
	{

		return "Como mínimo tu role debe ser editor, tu eres " . getRole(Auth::user()->role_id);
	    
	}));

	//http://localhost/roles_users_laravel/public/home/show_reply
	//sólo pueden acceder usuarios con role_id 1, 2 y 3
	Route::get('show_reply',array("before" => "roles:1-2-3,home", function()
	{

		return "Como mínimo tu role debe ser suscriptor, tu eres " . getRole(Auth::user()->role_id);
	    
	}));

	//http://localhost/roles_users_laravel/public/home
	//si ha iniciado sesión puede acceder, cualquier role
	Route::get('/', function()
	{

		return "Si estás logueado ya puedes acceder aquí, tu eres " . getRole(Auth::user()->role_id);
	    
	});

});

//ruta para cerrar sesión
//http://localhost/roles_users_laravel/public/logout
Route::get("logout", function()
{

	Auth::logout();
	return Redirect::to('login');

});


