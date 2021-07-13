<?php
// DEFINE O FUSO HORARIO COMO O HORARIO DE BRASILIA
    date_default_timezone_set('America/Sao_Paulo');
// Exibir todos os erros do PHP
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
// Incluir o composer apenas para algumas ferramentas do symfony
	include_once('../vendor/autoload.php');

// Classe já preparada com métodos que facilitam a leitura e formatação das requisições.
	require('includes/class/class.curl.php');
// Classe da trackCash	
	require('includes/class/class.trackCash.php');
// Classe do Bling para garimpar os dados.	
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

	// Solicitamos a lista de compras, passando a data desejada.
		$listaDeComprasBling = $Bling::listaDeCompras($inicio, $fim);
	// O retorno dos dados será um JSON, vamos transformar em array para melhor leitura
	$listaDeComprasBling = json_decode($listaDeComprasBling)->retorno;
	
	// Tratamos o erro de compra não existem no período.
	if(isset($listaDeComprasBling->erros)){
		foreach ($listaDeComprasBling->erros as $key => $value) {
			// Para testes aparecerá na tela, mas pode ser enviado por e-mail para o admin da empresa
			dump('Sem compras para o período consultado de '.$inicio.' a '.$fim);
			// Exibe o erro para depuração do desenvolvedor.
			dump($value->erro);
		}
	}else{
		dump($listaDeComprasBling);
		// Agora na classe $trackCash enviados os dados coletados do bling para formatação.
			$trackCash::setPedidos($listaDeComprasBling->pedidos);
		// Podemos exibir os dados formatados com o método abaixo.
			$pedidos = $trackCash::getPedidos();
		// Por fim dados o comando para subir os pedidos para a base.
			$insert = $trackCash::cadastrarPedidos();
		
		dump('------------------ RETORNO DA INSERÇÃO NA TRACKCASH -------------------------');
		dump(json_decode($insert));
	}
