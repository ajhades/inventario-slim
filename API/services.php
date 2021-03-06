<?php
//-----------Usuarios------------------//
$app->get('/users',$autenticacion_v2(['1']),function () use ($app){
    $all_users = find_all_user();
    echoResponse(200,$all_users); 
});
//-----------./Usuarios------------------//
//-----------Productos------------------//
$app->get('/products(/:type(/:name))',function ($type='',$name='') {
	
  if ($name != '' && $type =='list') {
    $products = find_product_by_title($name);
    echoResponse(200,$products);
  }elseif ($type =='single'){
    global $db;
    $product_title = remove_junk($db->escape($name));
    $results = find_all_product_info_by_title($product_title);
    echoResponse(200,$results);
  }else{
    $products = join_product_table();
    echoResponse(200,$products);
  }
});
//-----------./Productos------------------//
//-----------Categorias------------------//
$app->get('/categories',function (){
	$all_categories = find_all('categories');
	echoResponse(200,$all_categories);
});
$app->post('/category',$autenticacion_v2(['1']),function () use ($app) {
    $input = $app->request->post();
    $req_field = array('categorie-name');
    verifyRequiredParams($req_field,$input);

    global $db;
    $cat_name = remove_junk($db->escape($input['categorie-name']));
    if(empty($errors)){
        $sql  = "INSERT INTO categories (name)";
        $sql .= " VALUES ('{$cat_name}')";
        if($db->query($sql)){
          $arrOut['message'] = "Categoria agregada";
          echoResponse(201,$arrOut);
        } else {
          $arrOut['message'] = "Lo sentimos, no se pudo agregar";
          echoResponse(400,$arrOut);
        }
     } else {
       $arrOut['message'] =  "Error: ".$errors;
       echoResponse(500,$arrOut);
     }
});

$app->put('/category/:id',$autenticacion_v2(['1']), function ($id) use ($app) {
    $input = $app->request->put();

    $categorie = find_by_id('categories',(int)$id);
    if(!$categorie){
      $arrOut['message'] = "No se encontro el Id de la categoria ".$id;
      echoResponse(404,$arrOut);
    }
    $req_field = array('categorie-name');
    verifyRequiredParams($req_field,$input);
    global $db;
    $cat_name = remove_junk($db->escape($input['categorie-name']));
    if(empty($errors)){
          $sql = "UPDATE categories SET name='{$cat_name}'";
         $sql .= " WHERE id='{$categorie['id']}'";
       $result = $db->query($sql);
       if($result && $db->affected_rows() === 1) {
         $arrOut['message'] = "Categoria actualizada";
         echoResponse(200,$arrOut);
       } else {
         $arrOut['message'] = "Lo sentimos! No se pudo actualizar.";
         echoResponse(400,$arrOut);
       }
    } else {
      $arrOut['message'] = "Error: ".$errors;
      echoResponse(500,$arrOut);
    }
});

$app->delete('/category/:id',$autenticacion_v2(['1']),function ($id) use ($app){
    $categorie = find_by_id('categories',(int)$id);
    if (!$categorie) {
        $arrOut['message'] = "No existe la categoria ".$id;
        echoResponse(404,$arrOut);
    }else{
      $delete_id = delete_by_id('categories',(int)$categorie['id']);
      if($delete_id){
          $arrOut['message'] = "Categoria borrada.";
          echoResponse(200,$arrOut);
      } else {
          $arrOut['message'] = "Error al borrar categoria.";
          echoResponse(400,$arrOut);
      }
    }
    
});
//-----------./Categorias------------------//
//-----------Ventas------------------//
$app->get('/sale/:idUser',$autenticacion_v2(['3']),function ($idUser) use ($app){
    $sales = find_all_sale_by_id($idUser);
    echoResponse(200,$sales);
});
$app->get('/sales',$autenticacion_v2(['3']),function () use ($app){
  	$sales = find_all_sale(1);
  	echoResponse(200,$sales);
});
$app->get('/sales/:id',$autenticacion_v2(['3']),function ($id) use ($app){
    $sale = find_by_id('sales',(int)$id);
    echoResponse(200,$sale);
});
$app->get('/sales/:report',$autenticacion_v2(['3']),function ($report) use ($app){
  switch ($report) {
    case 'daily':
      $year  = date('Y');
      $month = date('m');
      $sales = dailySales($year,$month);
      echoResponse(200,$sales);
      break;
    case 'monthly':
      $year = date('Y');
      $sales = monthlySales($year);
      echoResponse(200,$sales);
      break;
    
    default:
      $arrOut['message']= "Tipo de reporte incorrecto.";
      echoResponse(400,$arrOut);
      break;
  }
  
});
$app->post('/sales',$autenticacion_v2(['3']),function () use ($app) {
	$input = $app->request->post();
	$req_fields = array('s_id','quantity','price','total' );
    verifyRequiredParams($req_fields,$input);
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
                  echoResponse(201,$arrOut);
                } else {
                  $arrOut['message'] = "Error: ".$error;
                  echoResponse(400,$arrOut) ;
                }
        } else {
           $arrOut['message'] = "Error 2: ".$error;
           echoResponse(400,$arrOut) ;
        }
});
$app->put('/sale/:id',$autenticacion_v2(['3']) , function ($id) use ($app) {
    //Update book identified by $id
  $sale = find_by_id('sales',$id);
  if (!$sale) {
    $arrOut['message'] = "No existe el producto ".$id;
    echoResponse(404,$arrOut) ;
  }
  $product = find_by_id('products',$sale['product_id']);

  $input = $app->request->post();
  $req_fields = array('quantity','price','total', 'date' );
  verifyRequiredParams($req_fields, $input);
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
      echoResponse(200,$arrOut);
    } else {
      $arrOut['message'] = "Fallo actualizar venta";
      echoResponse(400,$arrOut);
    }
  } else {
    $arrOut['message'] = "Error: ".$errors;
    echoResponse(400,$arrOut);
 }
});
$app->delete('/sale/:id',$autenticacion_v2(['3']) ,function ($id)use ($app){
  $delete_id = delete_by_id('sales',(int)$id);
  if($delete_id){
      $arrOut['message'] = 'Venta Borrada.';
      echoResponse(200,$arrOut);
  } else {
      $arrOut['message'] = "No se puedo borrar la venta.";
      echoResponse(400,$arrOut);
      // echoResponse(200,$res);
      // $app->response->setStatus(400);
      // $arrOut['message'] = 'Sale deletion failed.';
      // echo json_encode($arrOut);
  }
});
//-----------./Ventas------------------//
?>