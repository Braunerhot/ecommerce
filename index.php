<?php 

session_start();

require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Product;

$app = new Slim();

$app->config('debug', true);

// Rota
$app->get("/", function() {
    
	$page = new Page();

	$page->setTpl("index");

});

// Rota
$app->get("/admin", function() {

	User::verifyLogin();
    
	$page = new PageAdmin();

	$page->setTpl("index");

});

// Rota
$app->get("/admin/login", function() {
	
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("login");

});

// Rota
$app->post("/admin/login", function() {

	User::login($_POST["login"], $_POST["password"]);
	
	header("Location: /admin");
	exit;

});

// Rota para sair
$app->get("/admin/logout", function(){
	
	User::logout();

	header("Location: /admin/login");
	exit;
});

// Rota Deletar Usuário
$app->get("/admin/users/:iduser/delete", function($iduser){

	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$user->delete();

	header("Location: /admin/users");
	exit;
});

// Rota para listar todos os usuários
$app->get("/admin/users", function(){
	
	User::verifyLogin();
	
	$users = User::listAll();
	
	$page = new PageAdmin();
	
	$page->setTpl("users", array(
		"users"=>$users
	));
});

// Rota chama template de usuario novo
$app->get("/admin/users/create", function(){
	
	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("users-create");

});

// Rota EDITAR GET chama o template editar usuário
$app->get("/admin/users/:iduser", function($iduser){
	
	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$page = new PageAdmin();

	$page->setTpl("users-update", array(
		"user"=>$user->getValues()
	));

});

// Rota SALVAR usuario novo
$app->post("/admin/users/create", function(){

	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

	$_POST['despassword'] = password_hash($_POST["despassword"], PASSWORD_DEFAULT, [

 		"cost"=>12

 	]);

	$user->setData($_POST);

	$user->save();

	header("Location: /admin/users");
	exit;

});

// Rota Editar Usuário POST
$app->post("/admin/users/:iduser", function($iduser){

	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

	$user->get((int)$iduser);

	$user->setData($_POST);

	$user->update();

	header("Location: /admin/users");
	exit;

});

// Rota lista todos os produtos
$app->get("/admin/products", function() {

	User::verifyLogin();

	$products = Product::listAll();

	$page = new PageAdmin();

	$page->setTpl("products", array(
		"products"=>$products
	));

});

// Rota chama o template de produto novo
$app->get("/admin/products/create", function(){

	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("products-create");

});

$app->run();

?>