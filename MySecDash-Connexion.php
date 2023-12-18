<?php

/**
* Ce script gère la connexion, la déconnexion et le changement de mot de passe
* des utilisateurs.
*
* @license Copyleft
* @author Pierre-Luc MARY
* @package MySecDash
* @version 1.0
* @date 2023-11-03
*
*/

// Charge les constantes du projet.
include( 'Constants.inc.php' );


// Démarre le gestionnaire de session du serveur.
session_save_path( DIR_SESSION );
session_start();


// Initialise les variables standards.
$Script = $_SERVER[ 'SCRIPT_NAME' ];
$URI = $_SERVER[ 'REQUEST_URI' ];
$IP_Source = $_SERVER[ 'REMOTE_ADDR' ];
$Action = '';
$Choose_Language = 1;


// Force la connexion en HTTPS.
if ( ! array_key_exists( 'HTTPS', $_SERVER ) )
	header( 'Location: ' . URL_BASE . $URI );


// Initialise la langue Française par défaut.
if ( ! isset( $_SESSION[ 'Language' ] ) ) $_SESSION[ 'Language' ] = 'fr';


// Récupère le code langue, quand celui-ci est précisé.
if ( array_key_exists( 'Lang', $_GET ) ) {
	$_SESSION[ 'Language' ] = $_GET[ 'Lang' ];
}


// Charge les fichiers de libellés.
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_' . basename( $Script ) );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php' );


// Charge les classes utiles à l'écran.
include( DIR_LIBRAIRIES . '/Class_HTML.inc.php' );


// Récupère l'action spécifique à réaliser dans ce script.
if ( array_key_exists( 'action', $_GET ) ) {
	$Action = strtoupper( $_GET[ 'action' ] );
}


// Initialise l'objet de gestion des pages HTML.
$PageHTML = new HTML();


// Exécute l'action spécifique à réaliser.
switch( $Action ) {
 case 'AJAX_CTRL_TEMPS_SESSION':  // Réactualise le temps de session d'un utilisateur.
	if ( $PageHTML->validerTempsSession() ) {
		$Resultat = array( 'statut' => 'OK', 'expire' => $PageHTML->voirTempsSession() );
	} else {
		$Resultat = array( 'statut' => 'KO' );
	}

	print( json_encode( $Resultat ) );

	exit();


 case 'AJAX_SAUVER_TEMPS_SESSION':  // Réactualise le temps de session d'un utilisateur.
	$PageHTML->sauverTempsSession();

	print( json_encode( array( 'statut' => 'OK', 'expire' => $PageHTML->recupererParametre( 'expiration_time' ) ) ) );

	exit();


 case 'AJAX_LBL_CHG_MDP': // Gère les libellés relatifs au changement de mot de passe.
	$PageHTML->sauverTempsSession();

 	echo json_encode( array(
 		'Titre' => $L_Modify_Your_Password,
 		'MdP' => $L_Password,
 		'Nouveau_MdP' => $L_New_Password,
 		'Conf_MdP' => $L_Conf_Password,
 		'Modifier' => $L_Modifier,
 		'Fermer' => $L_Fermer
 		) );

	exit();


 // Enregistre le changement de mot de passe de l'utilisateur.
 case 'AJAX_CHG_MDP':
	$Error = 0;

	if ( $_POST[ 'N_Password' ] == '' or $_POST[ 'C_Password' ] == '' ) {
		$Error_Message = $L_ERR_Mandatories_Fields;
		$Error = 1;
	}

	
	if (  $_POST[ 'N_Password' ] != $_POST[ 'C_Password' ] ) {
		$Error_Message = $L_ERR_Password_Confirmation;
		$Error = 1;
	}

	
	if ( strlen( $_POST[ 'N_Password' ] ) < ($PageHTML->recupererParametre( 'min_size_password' )) ) {
		if ( $Error == 1 ) {
			$Error_Message .= ', ' . $L_ERR_Min_Size;
		} else {
			$Error_Message = $L_ERR_Min_Size;
			$Error = 1;
		}
	}
	
	
	if ( ! $PageHTML->controlerComplexiteMotPasse( $_POST[ 'N_Password' ],
	 $PageHTML->recupererParametre( 'password_complexity' ) ) ) {
		if ( $Error == 1 ) {
			$Error_Message .= ', ' . ${'L_ERR_Complexity_' . $PageHTML->recupererParametre( 'password_complexity' )} ;
		} else {
			$Error_Message = ${'L_ERR_Complexity_' . $PageHTML->recupererParametre( 'password_complexity' )} ;
			$Error = 1;
		}
	}

	
	if ( $Error == 1 ) {
		echo json_encode( array(
			'statut' => 'error',
			'titreMsg' => $L_Error,
			'texteMsg' => $Error_Message
		) );

		$PageHTML->ecrireEvenement( 'ATP_CHG_MDP', 'OTP_CTRL_ACCES', 'idn_login: "' . $_SESSION['idn_login'] . '", ' . $Error_Message );

		exit();
	}


	try {
		if ( ! $PageHTML->changerMotPasse( $_SESSION[ 'idn_id' ], $_POST[ 'O_Password' ], $_POST[ 'N_Password' ] ) ) {
			echo json_encode( array(
				'statut' => 'error',
				'titreMsg' => $L_Error,
				'texteMsg' => $L_ERR_Modify_Password
			) );

			$PageHTML->ecrireEvenement( 'ATP_CHG_MDP', 'OTP_CTRL_ACCES', 'idn_login: "' . $_SESSION['idn_login'] . '", ' . $L_ERR_Modify_Password );

			exit();
		}
	} catch( Exception $e ) {
		echo json_encode( array(
			'statut' => 'error',
			'titreMsg' => $L_Error,
			'texteMsg' => $e->getMessage()
		) );

		$PageHTML->ecrireEvenement( 'ATP_CHG_MDP', 'OTP_CTRL_ACCES', 'idn_login: "' . $_SESSION['idn_login'] . '", ' . $e->getMessage() );

		exit();
	}

	echo json_encode( array(
		'statut' => 'success',
		'titreMsg' => $L_Success,
		'texteMsg' => $L_Password_Modified
	) );

	$PageHTML->ecrireEvenement( 'ATP_CHG_MDP', 'OTP_CTRL_ACCES', 'idn_login: "' . $_SESSION['idn_login'] . '", ' . $L_Password_Modified );

	exit();


 // Contrôle les éléments d'authentification.
 case 'AJAX_CNX':
	if ( $_POST[ 'Code_Utilisateur' ] == '' || $_POST[ 'Mot_Passe' ] == '' ) {
		echo json_encode( array(
			'statut' => 'error',
			'titreMsg' => $L_Error,
			'texteMsg' => $L_ERR_Mandatories_Fields
		) );

		$PageHTML->ecrireEvenement( 'ATP_CNX', 'OTP_CTRL_ACCES', $L_ERR_Mandatories_Fields );

		exit();
	}
	
	$Authentication_Type = strtoupper( $PageHTML->recupererParametre( 'authentification_type' ) ) ;


	try {
		// Contrôle l'authentification à partir des éléments fournis.
		if ( ! $PageHTML->authentification( $_POST[ 'Code_Utilisateur' ],
		 $_POST[ 'Mot_Passe' ], $Authentication_Type ) ) {
			echo json_encode( array(
				'statut' => 'error',
				'titreMsg' => $L_Error,
				'texteMsg' => $L_Err_Auth
			) );

			$PageHTML->ecrireEvenement( 'ATP_CNX', 'OTP_CTRL_ACCES', 'idn_login: "' . $_POST[ 'Code_Utilisateur' ] . '", ' . $L_Err_Auth );

			exit();
		}
	} catch( Exception $e ) {
		echo json_encode( array(
			'statut' => 'error',
			'titreMsg' => $L_Error,
			'texteMsg' => $e->getMessage()
		) );

		$PageHTML->ecrireEvenement( 'ATP_CNX', 'OTP_CTRL_ACCES', 'idn_login: "' . $_POST['Code_Utilisateur'] . '", ' . $e->getMessage() );

		exit();
	}

	// Si l'indicateur de changement de mot de passe est à "vrai". L'utilisateur doit changer son mot de passe.
	if ( $_SESSION[ 'idn_changer_authentifiant' ] == 1 && $Authentication_Type != 'L' ) {
		echo json_encode( array(
			'statut' => 'warning',
			'titreMsg' => $L_Warning,
			'texteMsg' => 'CHG_MDP'
		) );
		
		exit();
	}

	// Tout est normal, l'utilisateur peut arriver sur son tableau de bord.
	echo json_encode( array(
		'statut' => 'success',
		'titreMsg' => $L_Success,
		'texteMsg' => ''
	) );

	$PageHTML->ecrireEvenement( 'ATP_CNX', 'OTP_CTRL_ACCES', 'idn_login: "' . $_SESSION['idn_login'] . '"' );
   
	exit();


 // Recueille les informations d'authentification.
 default:
	$Fichiers_JavaScript = 'MySecDash-Connexion/Lecture.js';

	print( $PageHTML->construireEnteteHTML( $L_Controle_Acces, $Fichiers_JavaScript, TRUE ) );

	if ( array_key_exists( 'notification', $_GET ) ) {
		if ( isset( $_POST[ 'Message'] ) and isset( $_POST[ 'Type_Message' ] ) ) {
			print( $PageHTML->afficherNotification( $_POST[ 'Message'], $_POST[ 'Type_Message' ] ) );
		} else if ( isset( $_GET[ 'expired'] ) ) {
			print( $PageHTML->afficherNotification( $L_Session_Expired, $L_Error ) );
		}
	}

	$Img_Langue_fr = "<img src=\"Images/drapeaux/fr.png\" alt=\"" . $L_Langue_fr . "\">";
	$Img_Langue_en = "<img src=\"Images/drapeaux/en.png\" alt=\"" . $L_Langue_en . "\">";

	if ( $_SESSION[ 'Language' ] == 'fr') {
		$Img_Langue_Active = $Img_Langue_fr;
	} else {
		$Img_Langue_Active = $Img_Langue_en;
	}

	print( "  <p id=\"logo-img\" class=\"text-center\">" .
		"<img src=\"Images/Logo-MySecDash.svg\" alt=\"Logo MySecDash\" />" );
	
	if ( file_exists( 'Images/Logo-Client.svg' ) ) {
		print( "<img src=\"Images/Logo-Client.svg\" alt=\"Logo Client\" />" );
	} elseif ( file_exists( 'Images/Logo-Client.png' ) ) {
		print( "<img src=\"Images/Logo-Client.png\" alt=\"Logo Client\" />" );
	}
	
	print( "</p>\n\n" .
		"  <div id=\"principal-container\" class=\"container\">\n" .
		"   <h1 class=\"text-center\">" . $L_Accueil . "</h1>\n" .
		"   <form id=\"login-form\" method=\"post\" action=\"#\" autocomplete=\"on\">\n" .
		"    <div class=\"input-group mb-3\">\n" .
		"     <input type=\"text\" name=\"Code_Utilisateur\" placeholder=\"" . $L_Username . "\" class=\"form-control\" autofocus required />\n" .
		"     <span class=\"input-group-text\"><i class=\"bi-person-fill\"></i></span>\n" .
		"    </div>\n" .
		"    <div class=\"input-group mb-3\">\n" .
		"     <input type=\"password\" name=\"Mot_Passe\" placeholder=\"" . $L_Password . "\" class=\"form-control\" required />\n" .
		"     <span class=\"input-group-text\"><i class=\"bi-unlock-fill\"></i></span>\n" .
		"    </div>\n" .
		"    <div class=\"dropdown mb-3\">\n" .
		"     <a id=\"langue_active\" role=\"button\" class=\"btn btn-outline-secondary dropdown-toggle\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">\n" .
		"      ". $Img_Langue_Active . "&nbsp;\n" .
		"     </a>\n" .
		"     <ul id=\"liste_chg_langue\" aria-labelledby=\"langue_active\" class=\"dropdown-menu\">\n" .
		"      <li><a id=\"fr\" href=\"" . $Script . "?Lang=fr\" class=\"dropdown-item\">" . $Img_Langue_fr . $L_Langue_fr . "</a></li>\n" .
		"      <li><a id=\"en\" href=\"" . $Script . "?Lang=en\" class=\"dropdown-item\">" . $Img_Langue_en . $L_Langue_en . "</a></li>\n" .
		"     </ul>\n" .
		"    </div>\n" .
		"    <div class=\"form-group\">\n" .
		"     <button type=\"submit\" class=\"btn btn-outline-secondary\" id=\"btn-connexion\">" . $L_Connect . "</button>\n" .
		"    </div>\n" .
		"   </form>\n" .
		"  </div>\n" .
		$PageHTML->construireFooter( FALSE ) .
		$PageHTML->construirePiedHTML()
	);

	break;


 case 'DCNX':  // Traite la déconnexion d'un utilisateur.
	if ( array_key_exists( 'expired', $_GET ) ) {
		if ( strpos( $Script, '?' ) === false ) {
			$Signal = '?expired&notification';
		} else {
			$Signal = '&expired&notification';
		}
	} else $Signal = '';

	$PageHTML->ecrireEvenement( 'ATP_DCNX', 'OTP_CTRL_ACCES', 'idn_login: "' . $_SESSION['idn_login'] . '"');

	$PageHTML->deconnecter();
   
	header( 'Location: ' . URL_BASE . $Script . $Signal );

	break;

}

?>