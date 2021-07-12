<?php

/**
 * Classe para operações da trackCash.
 */
class trackCash{

	private static $token = false;

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
?>