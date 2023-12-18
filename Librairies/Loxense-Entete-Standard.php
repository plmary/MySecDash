<?php

// Démarre le gestionnaire de session du serveur.
session_save_path( DIR_SESSION );
session_start();

// Initialise la langue Française par défaut.
if ( ! isset( $_SESSION[ 'Language' ] ) ) $_SESSION[ 'Language' ] = 'fr';

// Récupère le code langue, quand celui-ci est précisé.
if ( array_key_exists( 'Lang', $_GET ) ) {
	$_SESSION[ 'Language' ] = $_GET[ 'Lang' ];
}

$Script = $_SERVER[ 'SCRIPT_NAME' ];
$URI = $_SERVER[ 'REQUEST_URI' ];
$IP_Source = $_SERVER[ 'REMOTE_ADDR' ];

// Force la connexion en HTTPS.
if ( ! array_key_exists( 'HTTPS', $_SERVER ) )
	header( 'Location: ' . URL_BASE . $URI );
	
// Charge les libellés en fonction de la langue sélectionnée.
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php' );

// Charge les classes utiles à cet écran.
include( DIR_LIBRAIRIES . '/Class_HTML.inc.php' );

// Instanciation des différents objets.
$PageHTML = new HTML();


// Vérifie si la session de l'utilisateur n'a pas expiré.
if ( $PageHTML->validerTempsSession() ) {
	$PageHTML->sauverTempsSession();
} else {
	print( $PageHTML->construirePageAlerte( $L_Session_Expired, '/Loxense-Connexion.php' ) );
	exit();
}


// Récupère les droits de l'utilisateur sur ce script.
$Permissions = $PageHTML->permissionsGroupees( $Script );


// Vérifie que l'utilisateur est habilité en lecture sur ce script.
if ( $PageHTML->permission( $Script ) === false ) {
	include( HBL_DIR_LABELS . '/' . $_SESSION[ 'Language' ] . '_HBL_Autorisations.inc.php' );
	print( $PageHTML->construirePageAlerte( $L_No_Authorize_Script, '/Loxense-Principal.php' ) );
	exit();
}


// Identifie l'action à réaliser.
if ( array_key_exists( 'Action', $_GET ) ) {
	$Action = $_GET[ 'Action' ];
} else {
	$Action = '';
}


// Charge les javascripts relatifs aux droits de l'utilisateur.
$Droit_Lecture = FALSE;
$Droit_Ajouter = FALSE;
$Droit_Modifier = FALSE;
$Droit_Supprimer = FALSE;

$Path_Parts = pathinfo( $Script );
$Fichiers_JavaScript = [];

foreach ($Permissions[ basename( $Script ) ]["rights"] as $Droit) {
	switch ($Droit) {
		case 'RGH_1':
			$Droit_Lecture = TRUE;
			$Fichiers_JavaScript[] = $Path_Parts[ 'filename' ] . '/Lecture.js';
			break;
		case 'RGH_2':
			$Droit_Ajouter = TRUE;
			$Fichiers_JavaScript[] = $Path_Parts[ 'filename' ] . '/Ajouter.js';
			break;
		case 'RGH_3':
			$Droit_Modifier = TRUE;
			$Fichiers_JavaScript[] = $Path_Parts[ 'filename' ] . '/Modifier.js';
			break;
		case 'RGH_4':
			$Droit_Supprimer = TRUE;
			$Fichiers_JavaScript[] = $Path_Parts[ 'filename' ] . '/Supprimer.js';
			break;
	}
}


// Récupère les droits de l'utilisateur sur tous les scripts.
$Permissions = $PageHTML->permissionsGroupees();


?>