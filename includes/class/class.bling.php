<?php

/**
 * 
**/
class Bling extends mycurl{

	private static $token = false;

	private static $urlAPi = 'https://bling.com.br/Api';
	private static $versao = 'v1';
	private static $rota	 = false;

	function Bling($token, $versao = 'v1'){
		if(!isset($token)){
			dump('Necessário passar o Token para APi Bling.');
			return;
		}		
		self::setToken($token);
		self::setVersaoAPi('v2');
		self::setUrlAPi();
	}

	private static function setVersaoAPi($v){
		self::$versao = $v;
	}
	private static function setUrlApi(){
		$rota = (self::$rota !== false ? self::$rota.'/' : '');

		self::$urlAPi = self::$urlAPi.'/'.self::$versao.'/'.$rota;
	}
	private static function setRota($rota){
		self::$rota = $rota;
	}

	private static function setToken($token){
		// Se não passar o token retornamos uma mensagem.
		if(!isset($token)){
			dump('Necessário passar o Token para APi Bling.');
			return;
		}
		static::$token = $token;
	}

	public static function getToken(){
		return static::$token;
	}


	
	public static function listaDeCompras(){
		self::setRota('pedidos');

		dump(self::$urlAPi);

		dump('Vamos listar as compras para este token!');

		//$curl = new curl();
	}



}
?>