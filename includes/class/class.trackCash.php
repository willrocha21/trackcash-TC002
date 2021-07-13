<?php
/**
 * Classe para operações da trackCash.
 * Ela já trás pra dentro dela a classe mycurl para consultas via CURL PHP.
 * Para fins didáticos, não extendemos a classe Bling que já extendia a classe mycurl, más após refatoração isso poderá ser feito.
**/
class trackCash extends mycurl{

	private static $token = false;

	private static $url 			= 'https://sistema.trackcash.com.br/api/';
	private static $urlAPi 		= false;
	private static $rota	 		= false;
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
			'trackCash'		=> 'dGVzdGVAaW50ZWdyYWRvci5jb20uYnI6bXVkYXIhQCM=',
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
		// Rota para url de consulta da Bling APi
			self::setRota('orders');
 		// Monta a URL para disparo no CURL.			
			self::setUrlAPi();

		// Retorna a URL produzida e salva nesta variável $urlApi
			$urlApi = self::getUrlAPi();

		// Formata os dados e cria um array no formato de inserção da trackCash
			// Vide: https://sistema.trackcash.com.br/api
		self::setDados();

		// Instancia a classe extendida para consulta via CURL.
			$curl = new mycurl($urlApi);
		// Define o cabeçalho solicitado na documentação da API trackCash
			$curl->setHeader([
				"Content-Type: application/json; charset=utf-8",
				"token: ".self::getToken('trackCash')."" 
			]);
		// Seta os dados que serã enviados via POST em formato JSON.
			$curl->setPost(self::getDados());
		dump($curl);
		// Por fim dispara o POST pelo CURL
			$curl->createCurl();

		// Se deu tudo certo o código de retorno será 200
		if($curl->getHttpStatus() === 200){
			
			// Retorna com a resposta do servidor trackCash
			return $curl->getPagina();

		}else{ // Se deu algum erro na transmisão exibimos o erro para depuração.
			dump('Código da consulta: '.$curl->getHttpStatus());
			return false;
		}
	}

	protected static function setDados(){
		// Aqui vamos formatar os dados conforme o scopo da API da TrackCash.
		// Se não houver dados enviados retorna uma mensagem e retorna false.
		if(static::$pedidos === false){
			dump('Sem pedidos enviados para formatação.');
			return false;
		}

		$pedidos = [];
		foreach (self::getPedidos() as $k => $v) {
			$produtos = [];
			foreach ($v->pedido->itens as $k => $p){
				// Produzimos um Array com os produtos, pois podem ser vários.
				$produtos[] = [
					// Se o SKU vier vazio, vamos gerar uma aleatória para não dar erro na inserção
					'sku' => (!$p->item->codigo ? time() : $p->item->codigo),
			   	'quantity' => (int)$p->item->quantidade,
			   	'selling_price' => (float)$p->item->valorunidade,
			   	'discount' => $p->item->descontoItem
				];
			}

			// Populamos este array com os dados dos pedidos.
			$pedidos[] = [ 
				'id_order' 			=> $v->pedido->numero,
				'invoice' 			=> $v->pedido->numeroPedidoLoja,
				'name'					=> $v->pedido->cliente->nome,
				'status' 				=> '2',
				'date' 					=> $v->pedido->data,
				'partial_total' => $v->pedido->totalprodutos,
				'taxes' 				=> '0',
				'discount' 			=> '0',
				'type_factor' 	=> 'cubagem',
				'type_factor_value' 			=> '0.55',
				'logis_shipping_preview' 	=> '45.00',
				'shipment' 				=> 'B2W Entregas',
				'shipment_value' 	=> $v->pedido->valorfrete,
				'shipment_code' 	=> 'SW'.rand(111111111,999999999).'BR',
				'shipment_date'		=> $v->pedido->dataSaida,
				'delivered' 			=> '1',
				'paid' 						=> $v->pedido->totalvenda,
				'refunded' 				=> '0',
				'total' 					=> $v->pedido->totalvenda,
				'products' 				=> $produtos,
				'point_sale' 			=> '5',
				'point_sale_code' => $v->pedido->loja
     	];
		}
		// Adicionamos a chave 'orders' para a formatação conforme API trackCash
		$orders = [
			'orders' => $pedidos
		];	
		// Salvamos em dadosFormatados já em formato JSON.	
		self::$dadosFormatados = json_encode($orders);
	}

	private static function setUrlApi($extraParam = false){
		$rota = (self::$rota !== false ? self::$rota : '');
		$exp  = ($extraParam !== false ? $extraParam : '');
		$url  = self::$url.$rota.$exp;
		self::$urlAPi = $url;
	}
	private static function setRota($rota){
		// Define uma rota na url da API, pode ser /orders/, /pedidos/, /clientes/ e etc...
		self::$rota = $rota;
	}

	public static function setPedidos($pedidos){
		// Define os pedidos recebidos das integradoras
		self::$pedidos = $pedidos;
	}
	protected static function getDados(){
		// Retorna a self::$dadosFormatados
		return self::$dadosFormatados;
	}
	public static function getPedidos(){
		// Retorna a self::$pedidos
		return self::$pedidos;
	}

	public static function getUrlAPi(){
		// Retorna self::$urlAPi
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