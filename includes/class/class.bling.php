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

	// 
	public function Bling($token, $versao = 'v1'){
		if(!isset($token)){
			dump('Necessário passar o Token para APi Bling.');
			return;
		}
		// Define o Token para consulta Bling
			self::setToken($token);
		// Define o endereço padrão para consulta
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
		// Se estiver uma rota já definida por algum método, já será definida na URL de consulta
			$rota = (self::$rota !== false ? self::$rota.'/' : '');
		// Se passado dados extras serão adicionados na URL	
			$exp  = ($extraParam !== false ? $extraParam : '');
		// Por fim monta a URL para consulta e salva nesta varíavel de forma didática.
			$url  = self::$url.$rota.self::getTipagem().$exp;		
			self::$urlAPi = $url;
	}
	private static function setTipagem($t){
		//Seta a tipagem que pode ser XML ou JSON. Por isso a necessidade do método.
		self::$tipagem = $t;
	}
	private static function setRota($rota){
		// Define uma rota na url da API, pode ser /orders/, /pedidos/, /clientes/ e etc...
		self::$rota = $rota;
	}
	private static function getTipagem(){
		// Retorna a tipagem dos dados recebidos e enviados.
		return self::$tipagem;
	}
	public static function getToken(){
		// Retorna o Token solicitado pelo parâmetro
		return static::$token;
	}
	public static function listaDeCompras($dataInicio = false, $dataFim = false){
		self::setTipagem('json'); // Formato do retorno dos dados consultados
		self::setRota('pedidos'); // Rota para url de consulta da Bling APi
		
		$dataString = '';
		if($dataInicio !== false){
			$dataString = '&filters=dataEmissao['.$dataInicio.' TO '.$dataFim.']';
		}
		self::setUrlAPi('&apikey=' . self::getToken().$dataString); // Seta na URL o Token

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