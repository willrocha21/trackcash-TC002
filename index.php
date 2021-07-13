<?php
// DEFINE O FUSO HORARIO COMO O HORARIO DE BRASILIA
    date_default_timezone_set('America/Sao_Paulo');
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
	
	// Instanciamos a classe Bling para pegar a lista de compras.
		$Bling = new Bling($tokenBling);
	// Solicitamos a lista de pedidos.	

	$inicio = date('01/10/2019');
		dump('Data Início da consulta: '.$inicio);
	$fim    = '10/10/2019';
		dump('Data Fim da consulta: '.$fim);

	$listaDeComprasBling = $Bling::listaDeCompras($inicio, $fim);
	$listaDeComprasBling = json_decode($listaDeComprasBling)->retorno;
	
	if(isset($listaDeComprasBling->erros)){
		foreach ($listaDeComprasBling->erros as $key => $value) {
			dump('Sem compras para o período consultado de '.$inicio.' a '.$fim);
			dump($value->erro);
		}
	}else{
		dump($listaDeComprasBling);
		$trackCash::setPedidos($listaDeComprasBling->pedidos);
		$pedidos = $trackCash::getPedidos();
		$insert = $trackCash::cadastrarPedidos();
		echo $insert;
	}
