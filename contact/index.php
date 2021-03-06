<?
include($_SERVER['DOCUMENT_ROOT']."/php/cookiescontrol.php");
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>UniSharing</title>
	<link href="../css/bootstrap.css" rel="stylesheet" media="screen">
	<link href="../css/style.css" rel="stylesheet" media="screen">
	<link href="../css/footer.css" rel="stylesheet" media="screen">
	<link href="../css/navbar.css" rel="stylesheet" media="screen">
	<script src="../js/jquery.1.12.js"></script>
	<script src="../js/bootstrap.min.js"></script>
	<script src="../js/jquery.cookie.js"></script>
	<script src="../js/functions.js"></script>
	<script src="js/main.js"></script>
</head>
<body>
	<? include($_SERVER['DOCUMENT_ROOT']."/php/navbar.php"); ?>
	<div class="contaniner">
        <ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<? echo "http://".$_SERVER["HTTP_HOST"]; ?>/research/home/index.php">Home</a></li>
        	<li class="breadcrumb-item active">Contatti</li>
        </ol>
		<div class="col-lg-3">
		</div>
		<div class="col-lg-6">
			<h1>Segnalazione</h1>
			<hr>
			<div class="row-fluid">
				<div class="form-group col-lg-12" id="formname">
					<Label>Oggetto della segnalazione</Label>
					<div class="input-group" style="width:100%;">
						<input type="text" id="object" class="form-control" placeholder="Oggetto della segnalazione" aria-describedby="basic-addon1" required>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="form-group col-md-12">
					<label>Descrizione</label>
					<textarea id="message" class="form-control" placeholder="Inserisci una breve descrizione del problema" style="resize:vertical;height:100px;"></textarea>
				</div>
			</div>
			<div class="col-md-12">
				<br>
				<input style='margin-left: 5px' type="button" class="pull-right btn btn-primary" id="btn-segnalazione" onclick="sendReport()" value="Conferma">
                <input type="button" class="pull-right btn btn-default" id="btn-annulla" value="Annulla">
			</div>
		</div>
		<div class="col-lg-3">
		</div>
	</div>
	<? include($_SERVER['DOCUMENT_ROOT']."/php/footer.php"); ?>
</body>
</html>
