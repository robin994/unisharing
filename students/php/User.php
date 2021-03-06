<?

include "Account.php";


interface IUser{

	// metodo che permette di registrare un nuovo utente
	public function signin($post);

	// metodo che effettua il login
	public function login($post);

	// metodo che cambia lo score
	public function setScore($post);

	// metodo che preleva lo score
	public function getScore($post);

	// metodo che fornisce le info dell'utente
	public function getProfile($idUser);

	// metodo per la modifica dei dati utente
	public function modifyProfile($param);

	// metodo per aggiunge un utente alla propria blacklist
	public function addUserToBlackList($param);

	// metodo per restituisce la blacklist di un utente
	public function getBlackList($param);

	// metodo che rimuove l'utente dalla blacklist
	public function removeFromBlackList($param);

	//metodo che invia un suggerimento al gestore del sistema
	public function sendReport($param);

}

class User extends Account implements IUser{

	//private $connect;
	private $notify;

	// cookie dell'utente
	private $cookie;

	// costruttore della classe
	public function __construct(){}

		public function init(){

			//istanzio l'oggetto ConnectionDB
			//$this->connect = new ConnectionDB();

			// inizializzo la classe Account che estende
			$this->initialize();

			//inizializza l'oggetto Notification
			$this->notify = new Notification();

			// prelevo l'eventuale cookie dell'utente connesso
			$this->cookie = json_decode($_COOKIE["user"], false);

		}


		///////////////////////////////////////////////////////////
		/////////// METODO CHE EFFETTUA L'ISCRIZIONE //////////////
		///////////////////////////////////////////////////////////

		public function signin($post){

			$account = $post["account"];
			$user = $post["user"];
			$user["address"] = str_replace("'","\'",	$user["address"]);
			// invoco il metodo esteso da Account per inserire l'account
			$objJSON = $this->saveAccount($account);

			//controllo se il metodo di Account ha restituito errore, in questo caso lo restituisco al client ed esco
			if(!$objJSON["success"]){
				return json_encode($objJSON);
			}

			//re-inizializzo il json da restituire come risultato del metodo
			$objJSON = array();

			//eseguo la connessione al database definita in ConnectionDB.php sfruttando l'oggetto connect creato nella classe Account che estende
			$this->connect->connetti();
			//var_dump($user);
			//formulo la query di inserimento
			$query = "INSERT INTO _user (	name,
				surname,
				email,
				birthOfDay,
				telephone,
				description,
				address,
				typeStudent,
				pathImage,
				faculty,
				latitude,
				longitude
			) VALUES (
				'".$user["name"]."',
				'".$user["surname"]."',
				'".$account["username"]."',
				'".$user["bday"]."',
				'".$user["cellulare"]."',
				'".$user["description"]."',
				'".$user["address"]."',
				'".$user["tipo_studente"]."',
				'img/avatar/".$user["email"]."/',
				'".$user["facolta"]."',
				'".$user["latitude"]."',
				'".$user["longitude"]."'
				)";

				//var_dump($query);

				//la passo la motore MySql
				$result = $this->connect->myQuery($query);
				$idUser = $this->connect->insert_id();

				//Righe che gestiscono casi di errore di chiamata al database
				if($this->connect->errno()){

					//la chiamata non ha avuto successo
					$objJSON["success"] = false;
					$objJSON["messageError"] = "Errore:";
					$objJSON["error"] = $this->connect->error();

					//Disconnetto dal database
					$this->connect->disconnetti();
					return json_encode($objJSON);

				}else{

					//la chiamata ha avuto successo
					$objJSON["success"] = true;
					$objJSON["results"] = array();


					// inserisco le features dell'utente
					$post["user"]["idUser"] = $idUser;
					$this->setUserHasFeatures($post);


					//se non esiste la cartella avatar la creo
					if(!is_dir("../img/avatar")){
						mkdir("../img/avatar");
					}

					//se non esiste la cartella dell'utente la creo
					if(!is_dir("../img/avatar/".$user["email"])){
						mkdir("../img/avatar/".$user["email"]);
					}


					// creo l'avatar dell'utente
					if($post["image"]["caricata"] == "true"){


						$pathImage = base64_decode($post["image"]["image"]);
						$jpeg_quality = 90;
						$img_r = imagecreatefromstring($pathImage);
						$dst_r = imagecreatetruecolor(250,250);
						imagecopyresampled($dst_r,$img_r,0,0,$post["image"]['cx'],$post["image"]['cy'],250,250, $post["image"]['cw'],$post["image"]['ch']);
						imagejpeg($dst_r,"../img/avatar/".$user["email"]."/icon250x250.jpg",$jpeg_quality);

						$dst_r2 = imagecreatetruecolor(80, 80);
						imagecopyresampled($dst_r2,$img_r,0,0,$post["image"]['cx'],$post["image"]['cy'],80,80, $post["image"]['cw'],$post["image"]['ch']);
						imagejpeg($dst_r2,"../img/avatar/".$user["email"]."/icon80x80.jpg",$jpeg_quality);

						$dst_r3 = imagecreatetruecolor(40, 40);
						imagecopyresampled($dst_r3,$img_r,0,0,$post["image"]['cx'],$post["image"]['cy'],40,40, $post["image"]['cw'], $post["image"]['ch']);
						imagejpeg($dst_r3,"../img/avatar/".$user["email"]."/icon40x40.jpg",$jpeg_quality);

					}else{

						// non è stata caricata nessuna immagine inserisco l'avatar di default
						copy("../img/profile250x250.jpg", "../img/avatar/".$user["email"]."/icon250x250.jpg");
						copy("../img/profile80x80.jpg", "../img/avatar/".$user["email"]."/icon80x80.jpg");
						copy("../img/profile40x40.jpg", "../img/avatar/".$user["email"]."/icon40x40.jpg");

					}



					//////////////////////////////////////////////////
					/////////// INVIO L'EMAIL DI BENVENUTO ///////////
					//////////////////////////////////////////////////


					$from = "l.vitale@live.it";
					$to = $user["email"];
					$object = "Benvenuto in unisharing!";
					$message = "<html><body style='font-family:courier;font-size:16px;'>Benvenuto in unisharing,<br>Di seguito le credenziali per l'accesso<br><br>:::::::::::::::::::::::::::::<br>user: ".$to."<br>pass: ".$account["password"]."<br>:::::::::::::::::::::::::::::<br></body></html>";

					//creo il messaggio di benvenuto all'utente iscritto
					$this->notify->send($from, $to, $object, $message);

					//Disconnetto dal database e restituisco il risultato
					$this->connect->disconnetti();

				}

				return json_encode($objJSON);

			}
			/////////// FINE METODO CHE EFFETTUA L'ISCRIZIONE /////////


			///////////////////////////////////////////////////////////
			/////////// METODO CHE EFFETTUA LA LOGIN //////////////////
			///////////////////////////////////////////////////////////

			public function login($post){

				return $this->access($post);

			}
			/////////// FINE METODO LOGIN /////////


			//////////////////////////////////////////////////////////////////////////////
			/////////// METODO CHE EFFETTUA L'AGGIORNAMENTO DELLO SCORE //////////////////
			/////////////////////////////////////////////////////////////////////////////

			public function setScore($post){
				//re-inizializzo il json da restituire come risultato del metodo
				$objJSON = array();


				if(count($post["feedbacks"]) > 0){

					//eseguo la connessione al database definita in ConnectionDB.php sfruttando l'oggetto connect creato nella classe Account che estende
					$this->connect->connetti();

					$query = "";
					for($j = 0; $j < count($post["feedbacks"]);$j++){
						$query .= "UPDATE _user SET score = score + ".$post["feedbacks"][$j]["score"].", numberOfFeedback = numberOfFeedback + 1 WHERE email = '".$post["feedbacks"][$j]["user"]."';";
					}


					//la passo la motore MySql
					$result = $this->connect->myMultiQuery($query);

					//Righe che gestiscono casi di errore di chiamata al database
					if($this->connect->errno()){

						//la chiamata non ha avuto successo
						$objJSON["success"] = false;
						$objJSON["messageError"] = "Errore:";
						$objJSON["error"] = $this->connect->error();

						//Disconnetto dal database
						$this->connect->disconnetti();
						return json_encode($objJSON);

					}else{

						//la chiamata ha avuto successo
						$objJSON["success"] = true;
						$objJSON["results"] = array();

					}

					//Disconnetto dal database e restituisco il risultato
					$this->connect->disconnetti();
				}

				return json_encode($objJSON);
			}
			/////////// FINE METODO SET SCORE /////////



			//////////////////////////////////////////////////////////////////////////////
			/////////// METODO CHE PRELEVA LO SCORE DELL'UTENTE /////////////////////////
			/////////////////////////////////////////////////////////////////////////////

			public function getScore($post){
				//re-inizializzo il json da restituire come risultato del metodo
				$objJSON = array();

				//eseguo la connessione al database definita in ConnectionDB.php sfruttando l'oggetto connect creato nella classe Account che estende
				$this->connect->connetti();

				$query = "SELECT score FROM _user WHERE email = '".$post["user"]."'";

				//la passo la motore MySql
				$result = $this->connect->myMultiQuery($query);

				//Righe che gestiscono casi di errore di chiamata al database
				if($this->connect->errno()){

					//la chiamata non ha avuto successo
					$objJSON["success"] = false;
					$objJSON["messageError"] = "Errore:";
					$objJSON["error"] = $this->connect->error();

					//Disconnetto dal database
					$this->connect->disconnetti();
					return json_encode($objJSON);

				}else{

					//la chiamata ha avuto successo
					$objJSON["success"] = true;
					$objJSON["results"] = array();

					$row = mysqli_fetch_array($result);
					$objJSON["score"] = $row["score"];

				}

				//Disconnetto dal database e restituisco il risultato
				$this->connect->disconnetti();
				return json_encode($objJSON);
			}
			/////////// FINE METODO LOGIN /////////


			/////////////////////////////////////////////////////////
			///////////// INSERIMENTO DEI FEATURES PER L'USER ///////
			/////////////////////////////////////////////////////////

			private function setUserHasFeatures($post){

				$objJSON["success"] = true;
				$objJSON["results"] = array();


				if(count($post["user"]["features"]) > 0){

					//re-inizializzo il json da restituire come risultato del metodo
					$objJSON = array();

					//eseguo la connessione al database definita in ConnectionDB.php sfruttando l'oggetto connect creato nella classe Account che estende
					$this->connect->connetti();

					//formulo la query di inserimento delle features
					$values = "";
					for($i = 0; $i < count($post["user"]["features"]);$i++){
						$values .= "(";
						$values .= "'".$post["user"]["features"][$i]."','".$post["user"]["idUser"]."'";
						$values .= "),";
					}

					$values = substr($values, 0, strlen($values)-1);
					$query = "INSERT INTO _userhasfeatures (idFeature, idUser) VALUES ".$values;

					//var_dump($query);

					// eseguo la query nel motore mysql
					$this->connect->myQuery($query);

					//Righe che gestiscono casi di errore di chiamata al database
					if($this->connect->errno()){

						//la chiamata non ha avuto successo
						$objJSON["success"] = false;
						$objJSON["messageError"] = "Errore:";
						$objJSON["error"] = $this->connect->error();

					}else{

						//la chiamata ha avuto successo
						$objJSON["success"] = true;
						$objJSON["results"] = array();
					}

					//Disconnetto dal database e restituisco il risultato
					$this->connect->disconnetti();
				}

				return $objJSON;
			}



			////////////////////////////////////////////////////////////////////
			/////////// METODO RICEVE I DATI DEL PROFILO DELL'UTENTE ///////////
			////////////////////////////////////////////////////////////////////


			public function getProfile($post) {

				//re-inizializzo il json da restituire come risultato del metodo
				$objJSON = array();

				//var_dump($post);


				//eseguo la connessione al database definita in ConnectionDB.php sfruttando l'oggetto connect creato nella classe Account che estende
				$this->connect->connetti();



				// Query per ottenere i feedback
				$query2 = "SELECT * FROM _feedback as fb WHERE fb.account = (SELECT email FROM _user WHERE _user.idUser = ".$post["idUser"].")";

				// Query per ottenere i dati relativi all'utente
				$query = "SELECT * , (5 * (_user.score / _user.numberOfFeedback)) AS perc FROM _user where _user.idUser = ".$post["idUser"];

				//la passo la motore MySql
				$result = $this->connect->myQuery($query);
				$result2 = $this->connect->myQuery($query2);

				//Righe che gestiscono casi di errore di chiamata al database
				if($this->connect->errno()){

					//la chiamata non ha avuto successo

					$objJSON["success"] = false;
					$objJSON["messageError"] = $this->connect->error();

					//var_dump($objJSON);


					//Disconnetto dal database
					$this->connect->disconnetti();
					return $objJSON; //dici qui?

				}else{

					//la chiamata ha avuto successo
					$objJSON["success"] = true;
					$objJSON["results"] = array();

					$cont = 0;
					// ottengo i feedback dell'utente
					while($rowValori = mysqli_fetch_array($result2)){
						$objJSON["results"][$cont]["author"] = $rowValori["author"];
						$objJSON["results"][$cont]["comment"] = $rowValori["comment"];
						$objJSON["results"][$cont]["f1"] = $rowValori["simpatia"];
						$objJSON["results"][$cont]["f2"] = $rowValori["correttezza"];
						$objJSON["results"][$cont]["f3"] = $rowValori["puntualita"];
						$objJSON["results"][$cont]["f4"] = $rowValori["capacita"];

						$cont++;
					}
					/* chi decommenta sta roba e committa e' figlio di una ballerina :)
					$objJSON["results"][0]["idUser"] = $rowValori["idUser"];
					$objJSON["results"][0]["name"] = $rowValori["name"];

					$objJSON["results"][0]["features"] = array();
					if($rowValori["feature"]){
					$features = split(",", $rowValori["feature"]);
					$objJSON["results"][0]["features"] = $features;
				}

				// ottengo i feedback dell'utente
				$feed = new Feedback();
				$feed->init();
				$objJSON_FEED = json_decode($feed->getFeedbacksByUser($post));
				$objJSON["results"][0]["feedbacks"] = $objJSON_FEED->{"results"};
				*/

				$rowValori = mysqli_fetch_array($result);
				$objJSON["idUser"] = $rowValori["idUser"];
				$objJSON["email"] = $rowValori["email"];
				$objJSON["name"] = $rowValori["name"];
				$objJSON["surname"] = $rowValori["surname"];
				$objJSON["telephone"] = $rowValori["telephone"];
				$objJSON["address"] = $rowValori["address"];
				$objJSON["birthOfDay"] = $rowValori["birthOfDay"];
				$objJSON["pathImage"] = $rowValori["pathImage"];
				$objJSON["description"] = $rowValori["description"];
				$objJSON["score"] = $rowValori["score"];
				$objJSON["numberOfFeedback"] = $rowValori["numberOfFeedback"];
				$objJSON["typeStudent"] = $rowValori["typeStudent"];
				$objJSON["latitude"] = $rowValori["latitude"];
				$objJSON["longitude"] = $rowValori["longitude"];
				$idFaculty =$rowValori["faculty"];
				$objJSON["idFaculty"] = $rowValori["faculty"];
				$objJSON["perc"] = $rowValori["perc"];
				// Query per ottenere i nomi della facolta' e dell'universita'
				$query3 = "SELECT _university.name AS \"UF\", _faculty.name AS \"NF\" , _university.idUniversity as \"idU\" FROM _university, _faculty WHERE _faculty.idUniversity=_university.idUniversity AND _faculty.idFaculty =".$idFaculty;
				$result3 = $this->connect->myQuery($query3);
				$rowValori = mysqli_fetch_array($result3);

				$objJSON["idUniversity"] = $rowValori["idU"];
				$objJSON["universita"] = $rowValori["UF"];
				$objJSON["facolta"] = $rowValori["NF"];

				//var_dump($objJSON);

				$query4 = "SELECT * from _userhasfeatures JOIN _features ON _features.idFeature=_userhasfeatures.idFeature where _userhasfeatures.idUser = ".$objJSON["idUser"];
				$result4 = $this->connect->myQuery($query4);
				$cont = 0;
				while($rowValori = mysqli_fetch_array($result4)) {
					$objJSON["features"][$cont]["idFeature"] = $rowValori["idFeature"];
					$objJSON["features"][$cont]["label"] = $rowValori["label"];
					$cont++;
				}


			}

			//Disconnetto dal database
			$this->connect->disconnetti();
			//var_dump($objJSON);

			return json_encode($objJSON);


		}


		////////////////////////////////////////////////////////////////////
		/////////// METODO CHE MODIFICA IL PROFILO DELL'UTENTE /////////////
		////////////////////////////////////////////////////////////////////

		public function modifyProfile($post){

			$account = $post["account"];
			$user = $post["user"];
			$user["address"] = str_replace("'","\'",	$user["address"]);
			// invoco il metodo esteso da Account per inserire l'account
			$objJSON = $this->modifyAccount($account);
			//var_dump($objJSON);
			//controllo se il metodo di Account ha restituito errore, in questo caso lo restituisco al client ed esco
			if(!$objJSON["success"]){
				return json_encode($objJSON);
			}

			//re-inizializzo il json da restituire come risultato del metodo
			$objJSON = array();

			//eseguo la connessione al database definita in ConnectionDB.php sfruttando l'oggetto connect creato nella classe Account che estende
			$this->connect->connetti();

			//formulo la query di inserimento
			$query = "UPDATE _user SET _user.surname='".$user["surname"]."',
			_user.name='".$user["name"]."',
			_user.email='".$user["email"]."',
			_user.birthOfDay='".$user["bday"]."',
			_user.telephone='".$user["cellulare"]."',
			_user.description='".$user["description"]."',
			_user.address='".$user["address"]."',
			_user.typeStudent='".$user["tipo_studente"]."',
			_user.longitude='".$user["longitude"]."',
			_user.latitude='".$user["latitude"]."'
			WHERE _user.email='".$user["usernameOld"]."'";
			//_user.pathImage='img/avatar/".$user["pathImage"]."', AGGIUNGE path immagine alla query
			//var_dump($query);
			//la passo la motore MySql
			$result = $this->connect->myQuery($query);

			// inserisco le features dell'utente
			$queryCancellaFeature = "DELETE FROM _userhasfeatures where _userhasfeatures.idUser = ".$post["user"]["idUser"];
			$this->connect->myQuery($queryCancellaFeature);
			//var_dump($queryCancellaFeature);
			$this->setUserHasFeatures($post);


			//Righe che gestiscono casi di errore di chiamata al database
			if($this->connect->errno()){

				//la chiamata non ha avuto successo
				$objJSON["success"] = false;
				$objJSON["messageError"] = "Errore:";
				$objJSON["error"] = $this->connect->error();

				//Disconnetto dal database
				$this->connect->disconnetti();
				return json_encode($objJSON);

			}else{

				//la chiamata ha avuto successo
				$objJSON["success"] = true;
				$objJSON["results"] = array();


				// inserisco le features dell'utente
				$post["user"]["idUser"] = $idUser;
				$this->setUserHasFeatures($post);

				//se non esiste la cartella avatar la creo
				if(!is_dir("../img/avatar")){
					mkdir("../img/avatar");
				}

				//se non esiste la cartella dell'utente la creo
				if(!is_dir("../img/avatar/".$user["email"])){
					mkdir("../img/avatar/".$user["email"]);
				}


				// creo l'avatar dell'utente
				if($post["image"]["caricata"] == "true"){

					$pathImage = base64_decode($post["image"]["image"]);
					$jpeg_quality = 90;
					$img_r = imagecreatefromstring($pathImage);
					$dst_r = imagecreatetruecolor(250,250);
					imagecopyresampled($dst_r,$img_r,0,0,$post["image"]['cx'],$post["image"]['cy'],250,250, $post["image"]['cw'],$post["image"]['ch']);
					imagejpeg($dst_r,"../img/avatar/".$user["email"]."/icon250x250.jpg",$jpeg_quality);

					$dst_r2 = imagecreatetruecolor(80, 80);
					imagecopyresampled($dst_r2,$img_r,0,0,$post["image"]['cx'],$post["image"]['cy'],80,80, $post["image"]['cw'],$post["image"]['ch']);
					imagejpeg($dst_r2,"../img/avatar/".$user["email"]."/icon80x80.jpg",$jpeg_quality);

					$dst_r3 = imagecreatetruecolor(40, 40);
					imagecopyresampled($dst_r3,$img_r,0,0,$post["image"]['cx'],$post["image"]['cy'],40,40, $post["image"]['cw'], $post["image"]['ch']);
					imagejpeg($dst_r3,"../img/avatar/".$user["email"]."/icon40x40.jpg",$jpeg_quality);


				}else{

					// non è stata caricata nessuna immagine inserisco l'avatar di default
					copy("../img/profile250x250.jpg", "../img/avatar/".$user["email"]."/icon250x250.jpg");
					copy("../img/profile80x80.jpg", "../img/avatar/".$user["email"]."/icon80x80.jpg");
					copy("../img/profile40x40.jpg", "../img/avatar/".$user["email"]."/icon40x40.jpg");

				}

			}

			//Disconnetto dal database e restituisco il risultato
			$this->connect->disconnetti();
			return json_encode($objJSON);
		}

		/////////// FINE METODO CHE EFFETTUA LA MODIFICA DEL PROFILO /////////


		////////////////////////////////////////////////////////////////////
		/////////// METODO CHE AGGIUNGE UN UTENTE ALLA PROPRIA BLACKLIST ///
		////////////////////////////////////////////////////////////////////

		public function addUserToBlackList($post) {


			//re-inizializzo il json da restituire come risultato del metodo
			$objJSON = array();

			//eseguo la connessione al database definita in ConnectionDB.php sfruttando l'oggetto connect creato nella classe Account che estende
			$this->connect->connetti();

			$query = "SELECT email FROM _user where _user.idUser = ".$post["blockedUser"];

			//la passo la motore MySql
			$result = $this->connect->myQuery($query);
			$row = mysqli_fetch_array($result);
			$email = $row["email"];

			//controllo che l'utente non sia gia' bloccato
			//Ritorna 1 se l'utente e' gia' bloccato , 0 se viceversa
			$queryControllo = "SELECT CASE WHEN EXISTS (
				SELECT *
				FROM _blacklist
				WHERE user = '".$this->cookie->{"username"}."' AND blockedUser = '".$email."'
			)
			THEN 1
			ELSE 0 END AS controllo";
			$result = $this->connect->myQuery($queryControllo);
			$row = mysqli_fetch_array($result);
			//var_dump($row);

			if ($row["controllo"] == "1") {
				$objJSON["success"] = false;
				$objJSON["messageError"] = "Errore: ";
				$objJSON["error"] = "Utente gia' bloccato";

				//Disconnetto dal database
				$this->connect->disconnetti();
				//var_dump($objJSON);
				return json_encode($objJSON);
			}
			// formulo la query
			$query = "INSERT IGNORE INTO _blacklist (user, blockedUser) VALUES ('".$this->cookie->{"username"}."','".$email."')";


			//la passo la motore MySql
			$result = $this->connect->myQuery($query);

			//Righe che gestiscono casi di errore di chiamata al database
			if($this->connect->errno()){

				//la chiamata non ha avuto successo

				$objJSON["success"] = false;
				$objJSON["messageError"] = "Errore: ";
				$objJSON["error"] = $this->connect->error();

				//Disconnetto dal database
				$this->connect->disconnetti();
				return json_encode($objJSON);

			}else{



				//la chiamata ha avuto successo
				$objJSON["success"] = true;
				$objJSON["results"] = array();



				if($post["gruppo"]){

					// setto il rifiuto di partecipazione al gruppo
					$gruppo = new Group();
					$gruppo->init();
					$gruppo->refusalInvite($post);

				}

			}

			//Disconnetto dal database
			$this->connect->disconnetti();
			return json_encode($objJSON);
		}


		//////////////////////////////////////////////////////////////////////////////
		/////////////////// METODO CHE PRELEVA LA BLACKLIST /////////////////////////
		/////////////////////////////////////////////////////////////////////////////

		public function getBlackList($post){

			//re-inizializzo il json da restituire come risultato del metodo
			$objJSON = array();

			//eseguo la connessione al database definita in ConnectionDB.php sfruttando l'oggetto connect creato nella classe Account che estende
			$this->connect->connetti();

			$query = "SELECT 	USER.*

			FROM _blacklist

			LEFT JOIN (

				SELECT 	_user.email as email,
				_user.name as name,
				_user.surname as surname,
				_user.pathImage as pathImage
				FROM	_user

			) as USER ON USER.email = _blacklist.blockedUser

			WHERE _blacklist.user = '".$this->cookie->{"username"}."'";

			//la passo la motore MySql
			$result = $this->connect->myQuery($query);

			//Righe che gestiscono casi di errore di chiamata al database
			if($this->connect->errno()){

				//la chiamata non ha avuto successo
				$objJSON["success"] = false;
				$objJSON["messageError"] = "Errore:";
				$objJSON["error"] = $this->connect->error();

				//Disconnetto dal database
				$this->connect->disconnetti();
				return json_encode($objJSON);

			}else{

				//la chiamata ha avuto successo
				$objJSON["success"] = true;
				$objJSON["results"] = array();

				$cont = 0;
				while($row = mysqli_fetch_array($result)){
					$objJSON["results"][$cont]["name"] = $row["name"];
					$objJSON["results"][$cont]["surname"] = $row["surname"];
					$objJSON["results"][$cont]["username"] = $row["email"];
					$objJSON["results"][$cont]["surname"] = $row["surname"];
					$objJSON["results"][$cont]["pathImage"] = $row["pathImage"];
					$cont++;
				}

			}

			//Disconnetto dal database e restituisco il risultato
			$this->connect->disconnetti();
			return json_encode($objJSON);
		}
		/////////// FINE METODO BLACKLIST /////////


		public function removeFromBlackList($post){

			//re-inizializzo il json da restituire come risultato del metodo
			$objJSON = array();

			//eseguo la connessione al database definita in ConnectionDB.php sfruttando l'oggetto connect creato nella classe Account che estende
			$this->connect->connetti();

			$query = "DELETE FROM _blacklist WHERE _blacklist.user = '".$this->cookie->{"username"}."' AND _blacklist.blockedUser = '".$post["user"]."'";

			//la passo la motore MySql
			$result = $this->connect->myQuery($query);

			//Righe che gestiscono casi di errore di chiamata al database
			if($this->connect->errno()){

				//la chiamata non ha avuto successo
				$objJSON["success"] = false;
				$objJSON["messageError"] = "Errore:";
				$objJSON["error"] = $this->connect->error();

				//Disconnetto dal database
				$this->connect->disconnetti();
				return json_encode($objJSON);

			}else{

				//la chiamata ha avuto successo
				$objJSON["success"] = true;
				$objJSON["results"] = array();

			}

			//Disconnetto dal database e restituisco il risultato
			$this->connect->disconnetti();
			return json_encode($objJSON);

		}


		public function sendReport($post) {

			//////////////////////////////////////////////////
			/////////// INVIO L'EMAIL DI BENVENUTO ///////////
			//////////////////////////////////////////////////

			$from = $post['account'];
			$object = $post['object'];

			//creo il messaggio di benvenuto all'utente iscritto
			$message = $post['message'];

			$this->notify->send($from, "robin994@hotmail.it", $object, $message);

		}
	}

	?>
