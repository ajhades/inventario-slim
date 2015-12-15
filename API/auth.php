<?php
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

$app->get('/session',function ($request, $response){

	global $session;
	$user = current_user();
	if ($session->isUserLoggedIn(true)) { 
		$arrOut['message'] = "Bienvenido de vuelta ".remove_junk(ucfirst($user['name']));
		$arrOut['name'] = $user['name'];
		$arrOut['id'] = $user['id'];
		$arrOut['level'] = $user['user_level'];
        return  echoResponse(200,$arrOut, $response);
	}
})->setName('session');

$app->post('/login', function ($request, $response) {
	global $session;
	$input =$request->getParsedBody();

	if ($session->isUserLoggedIn(true)) { 
		$response = $response->withRedirect('session');
		return $response;
	}
		$req_fields = array('username','password' );
		// verifyRequiredParams($req_fields,$input);
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
	        		return  echoResponse(200,$arrOut, $response);
				elseif ($user['user_level'] === '2'):
					$arrOut['message'] = "Hola ".$user['username'].",Bienvenido.";
	        		return  echoResponse(200,$arrOut, $response);
				else:
					$arrOut['message'] = "Hola ".$user['username'].",Bienvenido.";
	        		return  echoResponse(200,$arrOut, $response);
				endif;

			else:
				$arrOut['message'] = "Usuario y contraseña incorrectos.";
	        	return  echoResponse(400,$arrOut,$response);
			endif;

		} else {

			$arrOut['message'] = "Error: ".$errors;
	        return  echoResponse(409,$arrOut, $response);
		}
});

$app->get('/logout',function ($request, $response){
	global $session;
	$session->logout();
	$arrOut['message'] = "Sesion cerrada";
    return echoResponse(200,$arrOut,$response);	
});
$app->post('/foo',function ($request, $response){
	$vars = $request->getParsedBody();

	// $response = $response->withAddedHeader('Content-type', 'application/json');
	/*$response =  $response->withStatus(400);
	$body = $response->getBody();
	$out = json_encode($vars);
	$body->write($out);*/

	$arrOut['message'] = "Sesion cerrada";
	return echoResponse(200,$arrOut,$response);
});