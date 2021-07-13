<?php

/**
 * 
**/
class Bling extends mycurl{

	private static $token 		= false;
	private static $url 		= 'https://bling.com.br/Api/v2/';
	private static $urlAPi 		= false;
	private static $rota	 	= false;
	private static $tipagem		= false;

	public function Bling($token, $versao = 'v1'){
		if(!isset($token)){
			dump('Necessário passar o Token para APi Bling.');
			return;
		}		
		self::setToken($token);
		self::setUrlAPi();
	}
	private static function setToken($token){
		// Se não passar o token retornamos uma mensagem.
		if(!isset($token)){
			dump('Necessário passar o Token para APi Bling.');
			return;
		}
		self::$token = $token;
	}
	private static function setUrlApi($extraParam = false){
		$rota = (self::$rota !== false ? self::$rota.'/' : '');
		$exp  = ($extraParam !== false ? $extraParam : '');
		$url  = self::$url.$rota.self::getTipagem().$exp;		
		self::$urlAPi = $url;
	}
	private static function setTipagem($t){
		self::$tipagem = $t;
	}
	private static function setRota($rota){
		self::$rota = $rota;
	}
	private static function getTipagem(){
		return self::$tipagem;
	}
	public static function getToken(){
		return static::$token;
	}
	public static function listaDeCompras(){
		self::setTipagem('json'); // Formato do retorno dos dados consultados
		self::setRota('pedidos'); // Rota para url de consulta da Bling APi
		self::setUrlAPi('&apikey=' . self::getToken()); // Seta na URL o Token

		// Retorna a URL produzida e salva nesta variável
		$urlApi = self::getUrlAPi();

		// Instancia a classe extendida para consulta via CURL.
		$curl = new mycurl($urlApi);
		$curl->createCurl();
		if($curl->getHttpStatus() === 200){
			return $curl->getPagina();
		}else{
			dump('Código da consulta: '.$curl->getHttpStatus());
			return false;
		}
	}
	public static function getUrlAPi(){
		return self::$urlAPi;
	}



}