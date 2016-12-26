<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>UniSharing</title>
        <link href="../../css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../css/research.css" rel="stylesheet" media="screen">
        <script src="../../js/jquery.1.12.js"></script>
    	<script src="../../js/bootstrap.min.js"></script>
        <script src="../../js/functions.js"></script>
        <script src="../../js/jquery.cookie.js"></script>
        <script>
			$(function() {
				$("#btn-start-research").on("click", function() {
					console.log("HO CLICCATO SUL TASTO DELLA RICERCA");
					var arr_features = [];
					var boo = false;
					$(".features").each(function(){
						if($(this).is(":checked")){
							arr_features.push({"features": $(this).val()});
							boo = true;
						}
					});
					console.log(arr_features);
					if(!boo){
							var tmp = '<center><br>';
							tmp += '<div class="alert alert-warning">';
							tmp += '<i class="glyphicon glyphicon-delete"/> ';
							tmp += '<span style="font-size:18px;">Non hai selezionato nessuna caratteristica</span>';
							tmp += '</div>';
							tmp += '</center>';
							$("#Message").html(tmp);
							return;
					}
					function callBackUsers(data){
						
						if(!data.success){
							var tmp = '<center><br>';
							tmp += '<div class="alert alert-danger">';
							tmp += '<i class="glyphicon glyphicon-delete"/> ';
							tmp += '<span style="font-size:18px;">'+data.messageError+' '+data.error+'</span>';
							tmp += '</div>';
							tmp += '</center>';
							$("#Message").html(tmp);
							return;
						}
	
						var tmp = "";
						for(var i = 0; i < data.results.length;i++){
							console.log(data.results[i]);
							tmp += '<div class="col-lg-4">';
							tmp += '<table class="table user-list">';
							tmp += 	'<tbody>';
							tmp += 		'<tr>';
							tmp += '			<td>';
							tmp += '				<img src="../../'+data.results[i]["pathImage"]+'/icon80x80.jpg" style="border-radius: 50px; float:left; margin-right: 3%; width: 80px; height: 80px" alt="">';
							tmp += '				<h5><a href="" class="user-link">'+data.results[i]["name"]+' '+data.results[i]["surname"]+'</a></h5>';
							tmp += '				<input type="hidden" class="nome_user" value="'+data.results[i]["name"]+'" />';
							tmp += '				<button class="addUser btn btn-success btn-xs" user-subhead" user="'+data.results[i]["id"]+'">Aggiungi        <span class="glyphicon glyphicon-plus"></span></button>';
							tmp += '			</td>';
							tmp += '		</tr>';
							tmp += '	</tbody>';
							tmp += '</table>';
							tmp += '</div>';
						}
						$("#ris").html("");
						$("#ris").html(tmp);
						
						//creo un cookie listaUtenti dove salvo le informazioni degli utenti che aggiungo alla lista
						$(".addUser").on("click", function() {
							
							if($.cookie("listaUtenti")){
								
								var cookie_lista = JSON.parse($.cookie("listaUtenti"));	
									
							}else{
								var cookie_lista = [];	
							}
							
							// rimpieri
							// prendo il cookie e gli faccio il parse di json
							lista_array.push({
								nome: Lorenzo,
								congome: Vitale,
								
							});
							
							var username = $(this).attr("user");
							
							var nome_user = $(this).parent().find(".nome_user").val();
							
							console.log("HO CLICCATO SUL TASTO AGGIUNGI UTENTE");
							
							var idealList;
							
							//Li aggiungo solo se l'idealist ha meno di 10 utenti
							if(ideaList < 10) {
								var cook = {
									// devo identificare l'utente giusto
									"idUser":data.results[this].idUser,
									"username":data.results[this].username, 
									"name":data.results[this].name, 
									"surname":data.results[this].surname, 
									"pathImage":data.results[this].pathImage
								}							
								// creo il cookie
								$.cookie('listaUtenti', JSON.stringify(cook));
								idealList++;
							} 
							//altrimenenti lancio un alert
							else {
								alert("Puoi inserire al massimo 10 utenti nella tua lista ideale");
							}
						});						
					}

					$.unisharing("Research", "researchUsers", "private", {"features":  arr_features}, false, callBackUsers);

				});
			});
		</script>
	</head>
	<body>
        <header>
            <nav class="navbar navbar-default navbar-static-top">
                <div class="container">
                    <div class="navbar-header">
                        <a href="index.html" class="navbar-brand">UniSharing</a>
                        <button class="navbar-toggle" data-toggle="collapse" data-target="#navHeaderCollapse" >
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </div>
                    <div class="collapse navbar-collapse" id="navHeaderCollapse">
                        <ul class= "nav navbar-nav navbar-right">
                            <li class="active"><a href="index.html">Home</a></li>
                            <li><a href="">Profilo</a></li>
                            <li><a href="">Lista nera</a></li>
                            <li class="dropdown">
                                <a href="" class="dropdown-toggle" data-toggle="dropdown">Gruppi <b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li><a href="">A cui partcipo</a></li>
                                    <li><a href="">Di cui sono amministratore</a></li>
                                </ul>
                             </li>
                            <li><a href=""> Segnalazione</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>
        <div id="container">
        	<div class="row">
            	<div class="col-lg-4"></div>
                <div class="col-lg-4" id="Message"></div>
                <div class="col-lg-4"></div>
           	</div>
            <div class="row">
            	<div class="col-lg-4">
                </div>
                <div class="col-lg-4">
                	<center><img src="../../img/logo.jpg" class="img-responsive" alt="logo"></center>
                    <div class="input-group">
                  	<input type="text" class="form-control" placeholder="Search">
                  	<span class="input-group-btn">
                    	<button class="btn btn-default" id="btn-start-research" type="button">Avvia</button>
                  	</span>
                	</div>
                    <h5 style="text-align:right"><a href="#advancedsearch" data-toggle="collapse">ricerca avanzata</a></h5>
                    <div id="advancedsearch" class="collapse filter-panel">
                    <div class="panel with-nav-tabs panel-default">
                    	<div class="panel-heading">
                    		<ul class="nav nav-tabs">
                     			<li class="active"><a href="#personality" data-toggle="tab">Personalità</a></li>
                                <li><a href="#knowledge" data-toggle="tab">Conoscenze</a></li>
                               	<li><a href="#geolocalizzazione" data-toggle="tab">Geolocalizzazione</a></li>
                            </ul>
                        </div>
                        <div class="panel-body">
                        	<div class="tab-content">
                        		<div class="tab-pane fade in active" id="personality">
                               	<div class="col-lg-6">
                                    	<div class="checkbox">
                                            <div class="row" style="margin-bottom: 2px">
                                                <label>
                                                    <input type="checkbox" value="Simpatico" class="features">
                                                    <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>Simpatico
                                                </label>
                                            </div>
                                            <div class="row" style="margin-bottom: 2px">
                                                <label>
                                                    <input type="checkbox" value="Cordiale" class="features">
                                                    <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>Cordiale
                                                </label>
                                            </div>
                                            <div class="row" style="margin-bottom: 2px">
                                                <label>
                                                    <input type="checkbox" value="Diligente" class="features">
                                                    <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>Diligente
                                                </label>
                                            </div>
                                        </div>
                                	</div>
                                	<div class="col-lg-6">
                                    	<div class="checkbox">
                                        	<div class="row" style="margin-bottom: 2px">
                                                <label>
                                                    <input type="checkbox" value="Socievole" class="features">
                                                    <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>Socievole
                                                </label>
                                            </div>
                                        	<div class="row" style="margin-bottom: 2px">
                                                <label>
                                                    <input type="checkbox" value="Timido" class="features">
                                                    <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>Timido
                                                </label>
                                            </div>
                                        	<div class="row" style="margin-bottom: 2px">
                                                <label>
                                                    <input type="checkbox" value="" class="features">
                                                    <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>Estroverso
                                                </label>
                                            </div>
                                        </div>
                                	</div>
                                </div>
                            	<div class="tab-pane fade" id="knowledge">
                               	<div class="col-lg-6">
                                    	<div class="checkbox">
                                            <div class="row" style="margin-bottom: 2px">
                                                <label>
                                                    <input type="checkbox" value="">
                                                    <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>Informatica
                                                </label>
                                            </div>
                                            <div class="row" style="margin-bottom: 2px">
                                                <label>
                                                    <input type="checkbox" value="">
                                                    <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>Matematica
                                                </label>
                                            </div>
                                            <div class="row" style="margin-bottom: 2px">
                                                <label>
                                                    <input type="checkbox" value="">
                                                    <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>Fisica
                                                </label>
                                            </div>
                                            <div class="row" style="margin-bottom: 2px">
                                                <label>
                                                    <input type="checkbox" value="">
                                                    <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>Scienze
                                                </label>
                                            </div>
                                            <div class="row" style="margin-bottom: 2px">
                                                <label>
                                                    <input type="checkbox" value="">
                                                    <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>Biologia
                                                </label>
                                            </div>
                                            <div class="row" style="margin-bottom: 2px">
                                                <label>
                                                    <input type="checkbox" value="">
                                                    <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>Chimica
                                                </label>
                                            </div>
                                            <div class="row" style="margin-bottom: 2px">
                                                <label>
                                                    <input type="checkbox" value="">
                                                    <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>Architettura
                                                </label>
                                            </div>
                                            <div class="row" style="margin-bottom: 2px">
                                                <label>
                                                    <input type="checkbox" value="">
                                                    <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>Diritto ed Economia
                                                </label>
                                            </div>
                                        </div>
                                	</div>
                                	<div class="col-lg-6">
                                    	<div class="checkbox">
                                        	<div class="row" style="margin-bottom: 2px">
                                                <label>
                                                    <input type="checkbox" value="">
                                                    <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>Geografia
                                                </label>
                                            </div>
                                        	<div class="row" style="margin-bottom: 2px">
                                                <label>
                                                    <input type="checkbox" value="">
                                                    <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>Storia e Filosofia
                                                </label>
                                            </div>
                                        	<div class="row" style="margin-bottom: 2px">
                                                <label>
                                                    <input type="checkbox" value="">
                                                    <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>Lettere
                                                </label>
                                            </div>
                                            <div class="row" style="margin-bottom: 2px">
                                                <label>
                                                    <input type="checkbox" value="">
                                                    <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>Latino e Greco
                                                </label>
                                            </div>
                                            <div class="row" style="margin-bottom: 2px">
                                                <label>
                                                    <input type="checkbox" value="">
                                                    <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>Inglese
                                                </label>
                                            </div>
                                            <div class="row" style="margin-bottom: 2px">
                                                <label>
                                                    <input type="checkbox" value="">
                                                    <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>Francesce
                                                </label>
                                            </div>
                                            <div class="row" style="margin-bottom: 2px">
                                                <label>
                                                    <input type="checkbox" value="">
                                                    <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>Spagnolo
                                                </label>
                                            </div>
                                        </div>
                                	</div>
                                </div>
                            	<div class="tab-pane fade" id="geolocalizzazione">
                                	<div class="col-lg-6">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" value="">
                                                <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>Attiva
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                    	<div class="input-group">
                                        	<input type="number" class="form-control" placeholder="Chilometri di distanza" disabled>
                                        </div>
                                    </div>
                                </div>
                           	</div>
                    	</div>
                	</div>
                    </div>
                </div>
                <div class="col-lg-4">
                </div>
            </div>
            <!-- RISULTATI DELLA RICERCA -->
            <div class="row">
            		<div class="col-lg-2"></div>
                    <div class="col-lg-8" id="ris"></div>
                    <div class="col-lg-2"></div>
            </div>
        </div>
        <footer>
        </footer>
	</body>
</html>
