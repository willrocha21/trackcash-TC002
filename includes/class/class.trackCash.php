<?php

/**
 * Classe para operações da trackCash.
 */
class trackCash extends mycurl{

	private static $token = false;

	private static $url 		= 'https://trackcash.com.br/api/';
	private static $urlAPi 		= false;
	private static $rota	 	= false;
	private static $tipagem		= false;
	private static $pedidos 	= false;	
	protected static $dadosFormatados = false;

	public function __construct(){
		// Starta a classe já definindo os tokens
		$this::setTokens();
	}

	private static function setTokens(){
		// Os tokens podem ser salvos em arquivos criptografados, banco de dados ou através de consulta a API. Aqui vamos setar nesta função.
		self::$token = (object)[
			'Bling' 		=> 'ce4b72b9775e1bdf72af674703268c2c920f5a6d39d8b43683074c92c4598ba3739aa4f7',
			'trackCash'		=> 'cmVnaXMuc2FudG9zQHRyYWNrY2FzaC5jb20uYnI6bXVkYXIhQCM=',
			'MercadoLivre' 	=> 'inventado-asdnfaihawhf988ureufoiahdvkjlzncl',
			'MercadoPago' 	=> 'inventado---asdnfaihawhf988ureufoiahdvkjlzncl',
		];
	}

	public static function cadastrarPedidos(){
		// Caso não tenha passado o array com os pedidos antes de chamar o método retornaremos um erro.
		if(self::$pedidos === false){
			dump('É necessário passar os pedidos coletados pelos integrados para inserir na base da TrackCash.');
			return false;	
		} 
		self::setRota('orders'); // Rota para url de consulta da Bling APi
		self::setUrlAPi();

		
		// Retorna a URL produzida e salva nesta variável
		$urlApi = self::getUrlAPi();
		dump(self::$urlAPi);

		self::setDados();

		// Instancia a classe extendida para consulta via CURL.
		/*$curl = new mycurl($urlApi);
		$curl->createCurl();
		if($curl->getHttpStatus() === 200){
			return $curl->getPagina();
		}else{
			dump('Código da consulta: '.$curl->getHttpStatus());
			return false;
		}*/
	}

	protected static function setDados(){
		// Aqui vamos formatar os dados conforme o scopo da API da TrackCash.
		// Se não houver dados enviados retorna uma mensagem e retorna false.
		if(static::$dadosFormatados === false){
			dump('Sem pedidos enviados para formatação.');
			return false;
		}
		$orders = (object)[
			'orders' => ''
		];
		foreach (self::getPedidos() as $k => $v) {
			dump($v);
		}
	}

	private static function setUrlApi($extraParam = false){
		$rota = (self::$rota !== false ? self::$rota : '');
		$exp  = ($extraParam !== false ? $extraParam : '');
		$url  = self::$url.$rota.$exp;		
		self::$urlAPi = $url;
	}
	private static function setRota($rota){
		self::$rota = $rota;
	}

	public static function setPedidos($pedidos){
		self::$pedidos = $pedidos;
	}

	public static function getPedidos(){
		return self::$pedidos;
	}

	public static function getUrlAPi(){
		return self::$urlAPi;
	}
	public static function getToken($k){
		// Retorna o Token solicitado pelo parâmetro
		if(self::$token !== false){ // Se estiver setado corretamente continua.
			// Se existir o token solicitado retorna.
			if(isset(self::$token->$k)) return self::$token->$k;
			// Se não retorna false para indicar que não existe o token.
			return false;
		}
		// Se chegamos até aqui é porque a função setTokens() não definiu nenhum token.
		dump('Tokens não definidos corretamente na função.');
		return false;		
	}	
}