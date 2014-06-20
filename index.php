<?header('Content-Type: text/html; charset=utf-8');
error_reporting (E_ALL);
include('cURL.php');
/**
 * Yandex metrika Parser class
 * @author Danila Dergachev <gnomdan@yandex.ru>
 * @copyright 2014 Future Group
**/

/** @noinspection PhpUnusedPrivateFieldInspection */
class Parse{
	/**
	 * @var object of cURL class
	**/
	private $curl;


	/**
	 * @var string stores url of the parsing page
	**/
	private $urlcallslist = "https://metrika.yandex.ru/api/stat/markedphones/log.json?id={cid}&lang=ru&reverse=1&per_page=1000&offset=1&group=all&date1={date1}&date2={date2}&table_mode=tree&selected_country=russia";

    /**
     * @var string login
     */
    private $login;

    /**
     * @var string password
     */
    private $password;

    /**
     * Construct - replaces login string with login and password from params
     *
     * @param string $login
     * @param string $password
     * @return \parse
     */
    public function __construct($login,$password){
        $this -> login = $login;
        $this -> password = $password;
        $this -> curl =  new cURL();
        $this -> curl -> init();
        $this -> curl -> set_opt(CURLOPT_SSL_VERIFYPEER, false);
        $this -> curl -> set_opt(CURLOPT_SSL_VERIFYHOST, false);
    }


	/**
	 * Connects to the parsing page
	 *
	 * Making a login and gets a cookie
	 *
	 * @return void
	**/
	private  function Connect(){
        $url = "https://passport.yandex.ru/auth?retpath=https%3A%2F%2Fpassport.yandex.ru%2Fpassport%3Fmode%3Dpassport&ncrnd=449489";
        $post = preg_replace(Array("#{login}#is", "#{password}#is"), Array($this -> login, $this -> password), "login={login}&passwd={password}&retpath=https%3A%2F%2Fpassport.yandex.ru%2Fpassport%3Fmode%3Dpassport");
        try{
            if (!$this -> curl -> get("https://passport.yandex.ru/")){
                throw new Exception("не удалось подключиться к серверу");
            }
            if (!$this -> curl -> post($url,$post)){
                throw new Exception("не удалось отправить post запрос");
            }
        } catch (Exception $e){
            echo 'Ошибка: ', $e -> getMessage(), "\n";
        }

	}


	/**
	* Replaces id and dates with params
	*
	* Accepts three integers and returns the Array
	*
	* @param int $counter_id an id of the counter in ya.metrika
	* @param int $date1 a date to start getting data from
	* @param int $date2 a date to end getting data at
	* @return array json decoded data got from parsing page
	**/
	public function GetCalls($counter_id,$date1,$date2){
        $this -> Connect();
		$url = preg_replace (Array("#{cid}#is", "#{date1}#is", "#{date2}#is"),Array($counter_id, $date1, $date2),$this -> urlcallslist);
		$this -> curl -> get($url);
		return(json_decode($this -> curl -> data));
	}
}
?>

<?
// Инициализация переменных
$yaLogin = "ventacom";
$yaPassword = "3,enskrbhjvf";
$counterID = "25307600";
$dateFrom = "20140613";
$dateTo = "20140619";
// создание объекта, получение массива
$obj = new Parse($yaLogin,$yaPassword);
$obj -> GetCalls($counterID, $dateFrom, $dateTo);
?>