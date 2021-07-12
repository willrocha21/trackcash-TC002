<?php
// Exibir todos os erros do PHP
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
// Incluir o composer apenas para algumas ferramentas do symfony
	include_once('../vendor/autoload.php');

	require('includes/class/class.curl.php');
	require('includes/class/class.trackCash.php');
	require('includes/class/class.bling.php');

	// Instacia a classe da trackCash
		$trackCash = new trackCash;
	// Solicitamos o token de acordo com a API desejada.	
		$tokenBling = $trackCash::getToken('Bling');
	
	$bling = new Bling($tokenBling);
	$tokenSet = $bling::getToken();
	
	$listaDeComprasBling = $bling::listaDeCompras();

	dump($listaDeComprasBling);

	dump($tokenSet);




?>
