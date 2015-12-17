<?php
$app->get('/session', function (){

	global $session;
	$user = current_user();
	if ($session->isUserLoggedIn(true)) { 
		$arrOut['message'] = "Bienvenido de vuelta ".remove_junk(ucfirst($user['name']));
		$arrOut['name'] = $user['name'];
		$arrOut['id'] = $user['id'];
		$arrOut['level'] = $user['user_level'];
        echoResponse(200,$arrOut);
	}
})->name('session');

$app->post('/login',  function () use($app) {
	global $session;
	$input = $app->request->post();
	// $headers = $app->request()->headers()->all();

	if ($session->isUserLoggedIn(true)) { 
		$app->response->redirect($app->urlFor('session'), 303);
		// $arrOut['message'] = "Session abierta";
  //       echoResponse(200,$arrOut);
	}

	$req_fields = array('username','password' );
	verifyRequiredParams($req_fields,$input);
	$username = remove_junk($input['username']);
	$password = remove_junk($input['password']);

	if(empty($errors)){

		$user = authenticate_v2($username, $password);

		if($user):
			
	           //create session with id
			$session->login($user['id']);
	           //Update Sign in time
			updateLastLogIn($user['id']);

			$user['token'] = bin2hex(openssl_random_pseudo_bytes(16)); //generate a random token
 
            $tokenExpiration = date('Y-m-d H:i:s', strtotime('+1 hour'));

            updateToken($user['id'],$user['token'], $tokenExpiration);
	           // redirect user to group home page by user level
			if($user['user_level'] === '1'):
				$arrOut['message'] = "Hola ".$user['username'].",Bienvenido.";
				$arrOut['user'] = $user;
				// $arrOut['headers'] = $headers;
        		echoResponse(200,$arrOut);
			elseif ($user['user_level'] === '2'):
				$arrOut['message'] = "Hola ".$user['username'].",Bienvenido.";
				$arrOut['user'] = $user;
				// $arrOut['headers'] = $headers;
        		echoResponse(200,$arrOut);
			else:
				$arrOut['message'] = "Hola ".$user['username'].",Bienvenido.";
				$arrOut['user'] = $user;
				// $arrOut['headers'] = $headers;
        		echoResponse(200,$arrOut);
			endif;

		else:
			$arrOut['message'] = "Usuario y contraseña incorrectos.";
			/*$arrOut['user'] = $user;
			$arrOut['pass'] = $password;
			
			$arrOut['hash'] = password_hash($password, PASSWORD_DEFAULT);
			$password_request = password_verify($password,$user['password']);
			$arrOut['result_hash'] =$password_request;*/
        	echoResponse(400,$arrOut);
		endif;

	} else {

		$arrOut['message'] = "Error: ".$errors;
        echoResponse(409,$arrOut);
	}

});

$app->get('/logout',function (){
	global $session;
	$session->logout();
	$arrOut['message'] = "Sesion cerrada";
    echoResponse(409,$arrOut);	
});
$app->get('/foo','autenticacion' ,function ()use ($app){

	$arrOut['message'] = "Autorizado";
	$arrOut['user'] = $app->auth_user;
	echoResponse(202,$arrOut);	
});