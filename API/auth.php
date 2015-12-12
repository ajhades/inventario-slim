<?php
$app->get('/session',function (){

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

$app->post('/login', function () use($app) {
	/*global $session;
	$input = $app->request->post();

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
	           // redirect user to group home page by user level
			if($user['user_level'] === '1'):
				$arrOut['message'] = "Hola ".$user['username'].",Bienvenido.";
        		echoResponse(200,$arrOut);
			elseif ($user['user_level'] === '2'):
				$arrOut['message'] = "Hola ".$user['username'].",Bienvenido.";
        		echoResponse(200,$arrOut);
			else:
				$arrOut['message'] = "Hola ".$user['username'].",Bienvenido.";
        		echoResponse(200,$arrOut);
			endif;

		else:
			$arrOut['message'] = "Usuario y contraseña incorrectos.";
        	echoResponse(400,$arrOut);
		endif;

	} else {

		$arrOut['message'] = "Error: ".$errors;
        echoResponse(409,$arrOut);
	}*/
	$username = $app->request->post('username');
    $password = $app->request->post('password');
	$result = $app->authenticator->authenticate($username, $password);
        if ($result->isValid()) {
            // $app->redirect('/');
          $all_users = array('message'=> 'hola,mundo');
          echoResponse(200,$all_users);
        } else {
            $messages = $result->getMessages();
            $hashedPassword =password_hash("user", PASSWORD_DEFAULT);
            // $app->flashNow('error', $messages[0]);
            $all_users = array('message'=> $messages, 'hash' => $hashedPassword);
            echoResponse(401,$all_users);
        }

});
$app->get('/logout', function () use ($app) {
    if ($app->auth->hasIdentity()) {
        $app->auth->clearIdentity();
        $all_users = array('message'=> 'hola,mundo');
        echoResponse(200,$all_users);
        
    }
    // $app->redirect('/');
});