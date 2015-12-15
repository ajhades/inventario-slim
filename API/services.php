<?php
//-----------Usuarios------------------//
$app->get('/users',function ($request, $response ){
	$all_users = find_all_user();
	return echoResponse(200,$all_users,$response);
});
//-----------./Usuarios------------------//
//-----------Productos------------------//
$app->group('/products',function (){

  $this->get('/all',function ($request, $response){
    $products = join_product_table();
    return echoResponse(200,$products,$response);
  });

  $this->get('/{type}/{name}',function ($request, $response,  $args){
	
    if ($args['name']!= '' && $args['type'] =='list') {
      $products = find_product_by_title($name);
      return echoResponse(200,$products,$response);
    }elseif ($args['type']=='single'){
      global $db;
      $product_title = remove_junk($db->escape($args['name']));
      $results = find_all_product_info_by_title($product_title);
      return echoResponse(200,$results,$response);
    }
  });
});
//-----------./Productos------------------//
//-----------Categorias------------------//
$app->get('/category',function ($request, $response ){
	$all_categories = find_all('categories');
	return echoResponse(200,$all_categories,$response);
});
$app->post('/category',function ($request, $response ) use ($app) {
  $input = $request->getParsedBody();
  $req_field = array('categorie-name');
  verifyRequiredParams($req_field,$input, $response);

  global $db;
  $cat_name = remove_junk($db->escape($input['categorie-name']));
  if(empty($errors)){
      $sql  = "INSERT INTO categories (name)";
      $sql .= " VALUES ('{$cat_name}')";
      if($db->query($sql)){
        $arrOut['message'] = "Categoria agregada";
        return echoResponse(201,$arrOut,$response);
      } else {
        $arrOut['message'] = "Lo sentimos, no se pudo agregar";
        return echoResponse(400,$arrOut,$response);
      }
   } else {
     $arrOut['message'] =  "Error: ".$errors;
     return echoResponse(500,$arrOut,$response);
   }
});

$app->put('/category/{id}', function ($request, $response, $args) use ($app) {
  $input = $app->request->put();

  $categorie = find_by_id('categories',(int)$args['id']);
  if(!$categorie){
    $arrOut['message'] = "No se encontro el Id de la categoria ".$args['id'];
    return echoResponse(404,$arrOut,$response);
  }
  $req_field = array('categorie-name');
  verifyRequiredParams($req_field,$input, $response);
  global $db;
  $cat_name = remove_junk($db->escape($input['categorie-name']));
  if(empty($errors)){
        $sql = "UPDATE categories SET name='{$cat_name}'";
       $sql .= " WHERE id='{$categorie['id']}'";
     $result = $db->query($sql);
     if($result && $db->affected_rows() === 1) {
       $arrOut['message'] = "Categoria actualizada";
       return echoResponse(200,$arrOut,$response);
     } else {
       $arrOut['message'] = "Lo sentimos! No se pudo actualizar.";
       return echoResponse(400,$arrOut,$response);
     }
  } else {
    $arrOut['message'] = "Error: ".$errors;
    return echoResponse(500,$arrOut,$response);
  }    
});

$app->delete('/category/{id}', function ($request, $response, $args) {
    $categorie = find_by_id('categories',(int)$args['id']);
    if (!$categorie) {
        $arrOut['message'] = "No existe la categoria ".$args['id'];
        return echoResponse(404,$arrOut,$response);
    }else{
      $delete_id = delete_by_id('categories',(int)$categorie['id']);
      if($delete_id){
          $arrOut['message'] = "Categoria borrada.";
          return echoResponse(200,$arrOut,$response);
      } else {
          $arrOut['message'] = "Error al borrar categoria.";
          return echoResponse(400,$arrOut,$response);
      }
    }
    
});
//-----------./Categorias------------------//
//-----------Ventas------------------//
$app->get('/sales/all',function ($request, $response ){
	$sales = find_all_sale();
	return echoResponse(200,$sales,$response);
});
$app->get('/sales/{id}',function ($request, $response, $args){
  if (is_numeric($args['id'])) {
    $sale = find_by_id('sales',(int)$args['id']);
    return echoResponse(200,$sale,$response);
  }elseif (is_string($args['id'])){
    switch ($args['id']) {
      case 'daily':
        $year  = date('Y');
        $month = date('m');
        $sales = dailySales($year,$month);
        return echoResponse(200,$sales,$response);
        break;
      case 'monthly':
        $year = date('Y');
        $sales = monthlySales($year);
        return echoResponse(200,$sales,$response);
        break;
      default:
        $arrOut['message']= "Tipo de reporte incorrecto.";
        return echoResponse(400,$arrOut,$response,$response);
        break;
    }
  }else{
    $arrOut['message']= "Parametro incorrecto.";
    return echoResponse(400,$arrOut,$response,$response);
  }  
});
$app->post('/sales',function ($request, $response ) use ($app) {
	$input = $request->getParsedBody();
	$req_fields = array('s_id','quantity','price','total' );
    verifyRequiredParams($req_fields,$input, $response);
    global $db;
        if(empty($errors)){
          $p_id      = $db->escape((int)$input['s_id']);
          $s_qty     = $db->escape((int)$input['quantity']);
          $s_total   = $db->escape($input['total']);
          $s_date    = make_date();

          $sql  = "INSERT INTO sales (";
          $sql .= " product_id,qty,price,date";
          $sql .= ") VALUES (";
          $sql .= "'{$p_id}','{$s_qty}','{$s_total}','{$s_date}'";
          $sql .= ")";

                if($db->query($sql)){
                  update_product_qty($s_qty,$p_id);
                  $arrOut['message'] = 'Nueva venta agregada.';
                  return echoResponse(201,$arrOut,$response);
                } else {
                  $arrOut['message'] = "Error: ".$error;
                  return echoResponse(400,$arrOut,$response) ;
                }
        } else {
           $arrOut['message'] = "Error 2: ".$error;
           return echoResponse(400,$arrOut,$response) ;
        }
});
$app->put('/sale/{id}', function ($request, $response, $args) use ($app) {
    //Update book identified by $args
  $sale = find_by_id('sales',$args['id']);
  if (!$sale) {
    $arrOut['message'] = "No existe el producto ".$args['id'];
    return echoResponse(404,$arrOut,$response) ;
  }
  $product = find_by_id('products',$sale['product_id']);

  $input = $request->getParsedBody();
  $req_fields = array('quantity','price','total', 'date' );
  verifyRequiredParams($req_fields, $input, $response);
  global $db;
  if(empty($errors)){
    $p_id      = $db->escape((int)$product['id']);
    $s_qty     = $db->escape((int)$input['quantity']);
    $s_total   = $db->escape($input['total']);
    $date      = $db->escape($input['date']);
    $s_date    = date("Y-m-d", strtotime($date));

    $sql  = "UPDATE sales SET";
    $sql .= " product_id= '{$p_id}',qty={$s_qty},price='{$s_total}',date='{$s_date}'";
    $sql .= " WHERE id ='{$sale['id']}'";
    $result = $db->query($sql);
    if( $result && $db->affected_rows() === 1){
      update_product_qty($s_qty,$p_id);
      $arrOut['message'] = "Venta Actualizada";
      return echoResponse(200,$arrOut,$response);
    } else {
      $arrOut['message'] = "Fallo actualizar venta";
      return echoResponse(400,$arrOut,$response);
    }
  } else {
    $arrOut['message'] = "Error: ".$errors;
    return echoResponse(400,$arrOut,$response);
 }
});
$app->delete('/sale/{id}',function ($request, $response, $args)use ($app){
  $delete_id = delete_by_id('sales',(int)$args['id']);
  if($delete_id){
      $arrOut['message'] = 'Venta Borrada.';
      return echoResponse(200,$arrOut,$response);
  } else {
      $arrOut['message'] = "No se puedo borrar la venta.";
      return echoResponse(400,$arrOut,$response);
      // return echoResponse(200,$res);
      // $app->response->setStatus(400);
      // $arrOut['message'] = 'Sale deletion failed.';
      // echo json_encode($arrOut);
  }
});
//-----------./Ventas------------------//
?>