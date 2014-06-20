<?php set_time_limit(120);
	/**
	 * запоросы на другие сайты
	 * @author n1k
	 *
	 */
	class cURL{


		private $curl = NULL;
		var $url;
		var $post_data;
		var $data;
		var $user_cookie_file = '';
		var $cookie_in_file;
		var $user_agent = 'Mozilla/5.0 (Windows NT 6.1; rv:15.0) Gecko/20100101 Firefox/15.0.1';


		var $error;

		var $referer;


		function init(){

			$this->curl = curl_init();

			if( !$this->curl ){
				$this->error = curl_error($this->curl);
				return;
			}
			$this->set_opt(CURLOPT_RETURNTRANSFER,true);
			$this->set_opt(CURLOPT_CONNECTTIMEOUT,30);
			$this->set_opt(CURLOPT_USERAGENT,$this->user_agent);
			$this->set_opt(CURLOPT_HEADER,false);



			//$this->set_opt(CURLINFO_HEADER_OUT,true);
			$this->set_opt(CURLOPT_HTTPHEADER,array('Accept-Language: ru-ru,ru;q=0.8,en-us;q=0.5,en;q=0.3', 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8' ,'Connection: keep-alive',
				'User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:29.0) Gecko/20100101 Firefox/29.0',
				) );

			$this->set_opt(CURLOPT_USERAGENT, "Mozilla/5.0 (iPhone; CPU iPhone OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A403 Safari/8536.25");
			$this->set_opt(CURLOPT_REFERER, "http://iphoneunlockstore.ru/");
			//
			$this->set_opt(CURLOPT_ENCODING,'gzip,deflate');
			///////////////////////////////////////////////////////////////////////////////////////////////////////////
			$this->set_opt(CURLOPT_FOLLOWLOCATION,true);


			$this->set_opt(CURLOPT_COOKIE, "__utma=73185099.2033703484.1401362458.1401362458.1401362458.1; __utmb=73185099.2.10.1401362458; __utmc=73185099; __utmz=73185099.1401362458.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none)");
			$this->set_opt(CURLOPT_COOKIESESSION,true);
			$this->set_opt(CURLOPT_COOKIEFILE,'cookiefile');
			//echo $this->user_cookie_file;
			if($this->cookie_in_file){
				$this->set_opt(CURLOPT_COOKIEFILE, $this->user_cookie_file);
				$this->set_opt(CURLOPT_COOKIEJAR,  $this->user_cookie_file);
			}else{
				$this->set_opt(CURLOPT_COOKIESESSION,true);
				$this->set_opt(CURLOPT_COOKIEFILE,'cookiefile');
			}

			if( !empty($this->referer) ){
				$this->set_opt(CURLOPT_REFERER,$this->referer);
				

			}
		}


	    function __destruct() {
	    	if( $this->curl ){
				curl_close($this->curl);
				$this->curl = NULL;
			}
	    }


	    function error(){
	    	return $this->error;
	    }


	    function data(){
	    	return $this->data;
	    }


		 function set_opt($opt,$val){
			if( !curl_setopt($this->curl,$opt,$val) ){

				$this->error = curl_error($this->curl);
				return false;
			}
			return true;
		}


		function to_file($name){

			if( $f = fopen($name,'w') ){

				fwrite($f,$this->data);
				fclose($f);
				return true;
			}
			else{
				$this->error = 'Не удалось записать в файл. Проверьте правильность пути или права на файл.';
			}

			return false;
		}


		function get($url){

			$this->url = $url;

			if( empty($this->url) ){
				$this->error = 'Не указан URL';
				return false;
			}

			$this->set_opt(CURLOPT_URL,$this->url);
			$this->set_opt(CURLOPT_POST,false);

			return $this->exec();
		}


		function https_get($url){

			$this->url = $url;

			if( empty($this->url) ){
				$this->error = 'Не указан URL';
				return false;
			}

			$this->set_opt(CURLOPT_URL,$this->url);
			$this->set_opt(CURLOPT_SSL_VERIFYHOST,0);
			$this->set_opt(CURLOPT_SSL_VERIFYPEER,false);

			return $this->exec();
		}


		private function exec(){

			if( false == ($this->data = curl_exec($this->curl)) ){

				$this->error = curl_error($this->curl);
				return false;
			}
			return true;
		}


		function post($url,$post_data){
			$this->url = $url;
			$this->post_data = $post_data;

			if( empty($this->url) ){
				$this->error = 'Non URL in POST DATA';
				return false;
			}

			$this->set_opt(CURLOPT_URL,$this->url);

			// POST
			$this->set_opt(CURLOPT_POST,true);
			$this->set_opt(CURLOPT_POSTFIELDS,$this->post_data);

			return $this->exec();
		}
	}

?>