<?php

// Charge les constantes du projet.
include_once( 'Constants.inc.php' );
include_once( HBL_DIR_LIBRARIES . '/Class_HBL_Authentifications_PDO.inc.php' );

class HTML extends HBL_Authentifications {
/**
* Cette classe gère l'affichage de certaines parties des écrans.
*
* @license Copyleft
* @author Pierre-Luc MARY
* @package MySecDash
* @date 2023-12-04
*
*/

public $Version_Outil; // Version de l'outil (précisé dans le constructeur)
public $Nom_Outil;
public $Nom_Outil_TXT;


public function __construct() {
/**
* Charge les variables d'environnements
*
* @license Copyleft
* @author Pierre-Luc MARY
* @date 2015-07-22
*
*/
	$this->Version_Outil = '1.0-0'; // Version de l'outil
	
	$this->Nom_Outil = '<span style="color: #717D11;">My</span><span style="color: #C34A36;">Sec</span><span style="color: #44808A;">Dash</span>';
	$this->Nom_Outil_TXT = 'MySecDash';

	try {
		parent::__construct();
	} catch( Exception $e ) {
		if ( $e->getCode() == 7 ) {
			print(
				'<h1>Erreur <small>interne</small></h1>' .
				'<hr>' .
				'<p>' . $e->getMessage() . '</p>' .
				'<hr>'
			);
			exit();
		}
	}
	
	return ;
}



public function construireEnteteHTML( $Titre_Page = "", $Fichiers_JavaScript = "", $CSS_Minimal = 0 ) {
/**
* Standardisation de la construction des pages HTML et de l'affichage des hauts de page.
*
* @license Copyleft
* @author Pierre-Luc MARY
* @date 2023-12-18
*
* @param[in] $Titre_Page Titre à afficher dans la fenêtre des navigateurs.
* @param[in] $Fichiers_JavaScript Script(s) Javascript spécifiques à appeler au démarrage de la page HTML.
* @param[in] $CSS_Minimal Flag pour l'appel ou non de la feuille de styles minimaliste.
* 
* @param[out] $Entete Objet HTML à intégrer dans la page.
*
* @return Retourne la chaîne d'entête d'une page HTML.
*/
	include( DIR_LIBELLES . "/" . $_SESSION[ 'Language' ] . "_libelles_generiques.php" );
	
	$Entete = "<!doctype html>\n\n" .
	 "<html lang=\"en\">\n" .
	 " <head>\n" .
	 "  <!-- Définition des métadonnées -->\n" .
	 "  <meta charset=\"utf-8\" />\n" .
	 "  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n" .
	 "  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n" .
	 "  <meta http-equiv=\"Content-Type\" content=\"text/html;\" />\n" .
	 "  <meta name=\"description\" content=\"" . $this->Nom_Outil_TXT . ", gestion des tableaux de bord de sécurité\" />\n" .
	 "  <meta name=\"author\" content=\"Pierre-Luc MARY\" />\n" .
	 "  <meta name=\"copyright\" content=\"" . $this->Nom_Outil_TXT . "\" />\n" .
	 "  <title>" . $Titre_Page . "</title>\n" .
	 "  <link type=\"image/png\" sizes=\"48x34\" href=\"favicon.png\" rel=\"icon\">\n" .
	 "  <!-- Chargement des feuilles de styles -->\n" .
	 "  <link rel=\"stylesheet\" type=\"text/css\" href=\"" . URL_LIBRAIRIES . "/css/bootstrap-icons/bootstrap-icons.css\" media=\"screen\">\n" .
	 "  <link rel=\"stylesheet\" type=\"text/css\" href=\"" . URL_LIBRAIRIES . "/bootstrap-dist/css/bootstrap.min.css\" media=\"screen\">\n";
	
	switch ( $CSS_Minimal ) {
	 case 0:
		$Entete .= '  <link rel="stylesheet" type="text/css" href="' . URL_LIBRAIRIES . '/css/MySecDash-1.css" media="screen">' . "\n" .
			'  <link rel="stylesheet" type="text/css" href="' . URL_LIBRAIRIES . '/css/bootstrap-colorselector.css" />' . "\n" .
			'  <link rel="stylesheet" type="text/css" href="' . URL_LIBRAIRIES . '/summernote-dist/summernote-bs4.css" />' . "\n";
		break;

	 case 1:
		$Entete .= "  <link rel=\"stylesheet\" type=\"text/css\" href=\"" . URL_LIBRAIRIES . "/css/MySecDash.css\" media=\"screen\">\n";
		break;

	 case 2:
		$Entete .= "  <link rel=\"stylesheet\" type=\"text/css\" href=\"" . URL_LIBRAIRIES . "/css/MySecDash-1.css\" media=\"screen\">\n" .
			"  <link rel=\"stylesheet\" type=\"text/css\" href=\"" . URL_LIBRAIRIES . "/css/plmTree.css\" media=\"screen\">\n";
		break;
	}


	$Entete .= "  <!-- Chargement de Jquery clé de voute -->\n" .
		"  <script src=\"" . URL_LIBRAIRIES . "/js/jquery.min.js\"></script>\n" .
		"  <!-- Chargement de \"moment\" bibliothèque de gestion des Dates -->\n" .
		"  <script src=\"" . URL_LIBRAIRIES . "/js/moment.min.js\"></script>\n";

	if ( $Fichiers_JavaScript != '' ) {
		$Entete .= "  <!-- Chargement des JavaScripts spécifiques -->\n";

		if ( is_array( $Fichiers_JavaScript ) ) {
			foreach( $Fichiers_JavaScript as $Fichier ) {
				if ( file_exists( DIR_LIBRAIRIES . "/js/" . $Fichier ) ) {
					$Entete .= "  <script src=\"" . URL_LIBRAIRIES . "/js/" . $Fichier . "\"></script>\n";
				}
			}
		} else {
			if ( file_exists( DIR_LIBRAIRIES . "/js/" . $Fichiers_JavaScript ) ) {
				$Entete .= "  <script src=\"" . URL_LIBRAIRIES . "/js/" . $Fichiers_JavaScript . "\"></script>\n";
			}
		}
	}

	// On veut que main.js soit le dernier script lancé
	$Entete .= "  <script>\n" .
	 "   var Parameters = new Array(); // Paramètre global pour communiquer entre les Javascripts\n".
	 "   Parameters['URL_BASE'] = '" . URL_BASE . "';\n\n" .
	 "   Parameters['URL_PICTURES'] = '" . URL_IMAGES . "';\n\n" .
	 "   Parameters['SCRIPT'] = '" . $_SERVER[ 'SCRIPT_NAME' ] . "';\n\n" .
	 "   Parameters['TravailEnCours'] = '" . $L_Travail_En_Cours . "';\n" .
	 "   Parameters['internal_timer_message']; // 'Timer' général pour l'affichage des messages.\n" .
	 "  </script>\n" .
	 " </head>\n\n" .
	 " <body>\n";

	return $Entete ;
}



public function construireNavbar( $Nom_Fichier_Logo ) {
/**
* Standardisation de la barre de menu (options de menu et information sur l'utilisateur).
*
* @license Copyleft
* @author Pierre-Luc MARY
* @date 2023-12-18
*
* @param[in] $Nom_Fichier_Logo Nom du fichier image contenant le logo à utiliser.
*
* @param[out] $Barre_Menu Objet HTML représentant la barre de menu est à intégrer dans sa page HTML
*
*$Barre_Menu
* @return Retourne la chaîne standardisant l'affichage du menu principal.
*/
	include (DIR_LIBELLES . "/" . $_SESSION[ 'Language' ] . "_libelles_generiques.php");

	$Permissions = $this->permissionsGroupees();

	$Barre_Menu = "  <nav class=\"navbar navbar-expand-lg fixed-top\">\n" .
		"   <div class=\"container-fluid\">\n";

	if ( file_exists( URL_IMAGES . '/' . $Nom_Fichier_Logo ) ) $Nom_Fichier_Logo = URL_IMAGES . '/' . $Nom_Fichier_Logo;
	else $Nom_Fichier_Logo = URL_IMAGES . '/Logo-MySecDash.svg';

	$Barre_Menu .= "    <a class=\"navbar-brand\" data-bs-toggle=\"offcanvas\" href=\"#offcanvasChangerUnivers\" role=\"button\" aria-controls=\"offcanvasChangerUnivers\"><img src=\"" . $Nom_Fichier_Logo . "\" alt=\"Logo\" height=\"25\" /></a>\n" .
		"     <button class=\"navbar-toggler btn-outline-secondary\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#navbarNavPrincipal\" aria-controls=\"navbarNavPrincipal\" aria-expanded=\"false\" aria-label=\"Toggle navigation\">\n" .
		"      <i class=\"bi-list\"></i>\n" .
		"     </button>\n" .
		"     <div class=\"collapse navbar-collapse\" id=\"navbarNavPrincipal\">\n" .
		"      <ul class=\"navbar-nav me-auto mb-2 mb-lg-0\">\n";

	// Contrôle si l'utilisateur a au moins accès à une option d'Admnisitration pour lui donner accès.
	if ( isset( $Permissions['Loxense-Parametres.php'] )
		or isset( $Permissions['Loxense-ReferentielsConformite.php'] ) ) {
		$Acces_Administration = TRUE;
		$Referentiel_Interne = TRUE;
	} else {
		$Acces_Administration = FALSE;
		$Referentiel_Interne = FALSE;
	}

	if ( isset( $Permissions['Loxense-Historiques.php'] )
		or isset( $Permissions['Loxense-ExportBase.php'] ) ) {
		$Acces_Administration = TRUE;
	} else {
		$Acces_Administration = FALSE;
	}

	if ( $_SESSION['idn_super_admin'] === TRUE or $Acces_Administration === TRUE ) {
		$Barre_Menu .= "       <li class=\"nav-item dropdown\">\n" .
			"        <a href=\"#\" class=\"nav-link dropdown-toggle\" id=\"navbarDropdownMenuAdmin\" data-bs-toggle=\"dropdown\" role=\"button\" aria-expanded=\"false\">" . $L_Administration . "</a>\n" .
			"        <ul class=\"dropdown-menu\" aria-labelledby=\"navbarDropdownMenuAdmin\">\n";
	}
	
	if ( $_SESSION['idn_super_admin'] === TRUE or $Referentiel_Interne === TRUE ) {
		$Barre_Menu .= "        <li class=\"dropdown-submenu\">\n" .
			"         <a href=\"#\" class=\"dropdown-item\">" . $L_Referentiel_Interne . "</a>\n" .
			"         <ul class=\"dropdown-menu\">\n";
	}

	if ( $_SESSION['idn_super_admin'] === TRUE or isset( $Permissions['Loxense-Parametres.php'] ) ) {
		$Barre_Menu .= "          <li><a href=\"" . URL_BASE . "/Loxense-Parametres.php\" class=\"dropdown-item\">" . $L_Parametres_Base . "</a></li>\n";
	}

	if ( $_SESSION['idn_super_admin'] === TRUE or $Referentiel_Interne === TRUE ) {
		$Barre_Menu .= "         </ul>\n" .
			"        </li>\n";
	}

	if ( $_SESSION['idn_super_admin'] === TRUE or $Control_Acces === TRUE ) {
		$Barre_Menu .= "        <li class=\"dropdown-submenu\">\n" .
			"         <a href=\"" . URL_BASE . "/Loxense-Utilisateurs.php\" class=\"dropdown-item\">" . $L_Controle_Acces . "</a>\n" .
			"         <ul class=\"dropdown-menu\">\n";
	}

	if ( $_SESSION['idn_super_admin'] === TRUE or isset( $Permissions['Loxense-Entites.php'] ) ) {
		$Barre_Menu .= "          <li><a href=\"" . URL_BASE . "/Loxense-Entites.php\" class=\"dropdown-item\">" . $L_Entites . "</a></li>\n";
	}

	if ( $_SESSION['idn_super_admin'] === TRUE or isset( $Permissions['Loxense-Civilites.php'] ) ) {
		$Barre_Menu .= "          <li><a href=\"" . URL_BASE . "/Loxense-Civilites.php\" class=\"dropdown-item\">" . $L_Civilites . "</a></li>\n";
	}

	if ( $_SESSION['idn_super_admin'] === TRUE or isset( $Permissions['Loxense-Applications.php'] ) ) {
		$Barre_Menu .= "          <li><a href=\"" . URL_BASE . "/Loxense-Applications.php\" class=\"dropdown-item\">" . $L_Applications . "</a></li>\n";
	}

	if ( $_SESSION['idn_super_admin'] === TRUE or isset( $Permissions['Loxense-Profils.php'] ) ) {
		$Barre_Menu .= "          <li><a href=\"" . URL_BASE . "/Loxense-Profils.php\" class=\"dropdown-item\">" . $L_Profils . "</a></li>\n";
	}

	if ( $_SESSION['idn_super_admin'] === TRUE or isset( $Permissions['Loxense-Utilisateurs.php'] ) ) {
		$Barre_Menu .= "          <li><a href=\"" . URL_BASE . "/Loxense-Utilisateurs.php\" class=\"dropdown-item\">" . $L_Utilisateurs . "</a></li>\n";
	}

	if ( $_SESSION['idn_super_admin'] === TRUE or isset( $Permissions['Loxense-Gestionnaires.php'] ) ) {
		$Barre_Menu .= "          <li><a href=\"" . URL_BASE . "/Loxense-Gestionnaires.php\" class=\"dropdown-item\">" . $L_Gestionnaires . "</a></li>\n";
	}

	if ( $_SESSION['idn_super_admin'] === TRUE or isset( $Permissions['Loxense-Etiquettes.php'] ) ) {
		$Barre_Menu .= "          <li><a href=\"" . URL_BASE . "/Loxense-Etiquettes.php\" class=\"dropdown-item\">" . $L_Etiquettes . "</a></li>\n";
	}

	if ( $_SESSION['idn_super_admin'] === TRUE or $Control_Acces === TRUE ) {
		$Barre_Menu .= "         </ul>\n" .
			"        </li>\n";
	}

	if ( $_SESSION['idn_super_admin'] === TRUE or isset( $Permissions['Loxense-Historiques.php'] ) ) {
		$Barre_Menu .= "        <li role=\"separator\" class=\"divider\"></li>\n" .
			"        <li><a href=\"" . URL_BASE . "/Loxense-Historiques.php\" class=\"dropdown-item\">" . $L_Historique . "</a></li>\n";
	}

	if ( $_SESSION['idn_super_admin'] === TRUE or isset( $Permissions['Loxense-ExportBase.php'] ) ) {
		$Barre_Menu .= "        <li role=\"separator\" class=\"divider\"></li>\n" .
			"        <li><a href=\"" . URL_BASE . "/Loxense-ExportBase.php\" class=\"dropdown-item\">" . $L_Export_Base . "</a></li>\n";
	}

	if ( $_SESSION['idn_super_admin'] === TRUE or $Acces_Administration === TRUE ) {
		$Barre_Menu .= "       </ul>\n" .
			"      </li>\n";
	}


	// ======


	if ( $_SESSION['idn_super_admin'] === TRUE
		or isset( $Permissions['Loxense-Conformite.php']  )
		or isset( $Permissions['Loxense-EditionConformite.php']  )
		or isset( $Permissions['Loxense-MatriceConformite.php']  ) ) {
		$Option_Gestion_Conformite = TRUE;
	} else {
		$Option_Gestion_Conformite = FALSE;
	}


	if ( $_SESSION['idn_super_admin'] === TRUE or $Option_Gestion_Conformite === TRUE ) {
		$Barre_Menu .= "      <li class=\"nav-item dropdown\">\n" .
			"       <a href=\"#\" class=\"nav-link dropdown-toggle\" id=\"navbarDropdownMenuConfo\" data-bs-toggle=\"dropdown\" role=\"button\" aria-expanded=\"false\">" . $L_Gestion_Conformite . " <span class=\"caret\"></span></a>\n" .
			"       <ul class=\"dropdown-menu\" aria-labelledby=\"navbarDropdownMenuConfo\">\n";
	}

	if ( $_SESSION['idn_super_admin'] === TRUE or isset( $Permissions['Loxense-Conformite.php'] ) ) {
		$Barre_Menu .= "        <li><a href=\"" . URL_BASE . "/Loxense-Conformite.php\" class=\"dropdown-item\">" . $L_Gerer_Conformite . "</a></li>\n";
	}
	
	if ( $_SESSION['idn_super_admin'] === TRUE or isset( $Permissions['Loxense-EditionConformite.php'] ) ) {
		$Barre_Menu .= "        <li><a href=\"" . URL_BASE . "/Loxense-EditionConformite.php\" class=\"dropdown-item\">" . $L_Editer_Conformite . "</a></li>\n";
	}
	
/*	if ( $_SESSION['idn_super_admin'] === TRUE or isset( $Permissions['Loxense-MatriceConformite.php'] ) ) {
		$Barre_Menu .= "        <li><a href=\"" . URL_BASE . "/Loxense-MatriceConformite.php\" class=\"dropdown-item\">" . $L_Matrice_Conformite . "</a></li>\n";
	} */

	
	if ( $_SESSION['idn_super_admin'] === TRUE or $Option_Gestion_Conformite === TRUE ) {
			$Barre_Menu .= "       </ul>\n" .
			"      </li>\n";
	}


	// ======


	if ( $_SESSION['idn_super_admin'] === TRUE
		or isset( $Permissions['Loxense-Actions.php'] )
		or isset( $Permissions['Loxense-EditionsActions.php'] )) {
		$Option_Gestion_Actions = TRUE;
	} else {
		$Option_Gestion_Actions = FALSE;
	}


	if ( $_SESSION['idn_super_admin'] === TRUE or $Option_Gestion_Actions === TRUE ) {
		$Barre_Menu .= "      <li class=\"nav-item dropdown\">\n" .
			"       <a href=\"#\" class=\"nav-link dropdown-toggle\" id=\"navbarDropdownMenuAction\" data-bs-toggle=\"dropdown\" role=\"button\" aria-expanded=\"false\">" . $L_Gestion_Actions . " <span class=\"caret\"></span></a>\n" .
			"       <ul class=\"dropdown-menu\" aria-labelledby=\"navbarDropdownMenuAction\">\n";
	}

	if ( $_SESSION['idn_super_admin'] === TRUE or isset( $Permissions['Loxense-Actions.php'] ) ) {
		$Barre_Menu .= "        <li><a href=\"" . URL_BASE . "/Loxense-Actions.php\" class=\"dropdown-item\">" . $L_Gerer_Actions . "</a></li>\n";
	}

	if ( $_SESSION['idn_super_admin'] === TRUE or isset( $Permissions['Loxense-EditionsActions.php'] ) ) {
		$Barre_Menu .= "        <li><a href=\"" . URL_BASE . "/Loxense-EditionsActions.php\" class=\"dropdown-item\">" . $L_Editer_Actions . "</a></li>\n";
	}

	
	if ( $_SESSION['idn_super_admin'] === TRUE or $Option_Gestion_Actions === TRUE ) {
		$Barre_Menu .= "       </ul>\n" .
			"      </li>\n";
	}


	// ======

/*
	if ( $_SESSION['idn_super_admin'] === TRUE
		or isset( $Permissions['Loxense-ActifsPrimordiauxTags.php'] )
		or isset( $Permissions['Loxense-ActifsSupportsTags.php'] )
		or isset( $Permissions['Loxense-AppreciationRisquesTags.php'] )
		or isset( $Permissions['Loxense-TraitementRisquesTags.php'] )
		) {
		$Option_Gestion_Tags = TRUE;
	} else {
		$Option_Gestion_Tags = FALSE;
	}


	if ( $_SESSION['idn_super_admin'] === TRUE or $Option_Gestion_Tags === TRUE ) {
		$Barre_Menu .= "      <li class=\"nav-item dropdown\">\n" .
			"       <a href=\"#\" class=\"nav-link dropdown-toggle\" id=\"navbarDropdownMenuTags\" data-bs-toggle=\"dropdown\" role=\"button\" aria-expanded=\"false\">" . $L_Vision_Consolidee . " <span class=\"caret\"></span></a>\n" .
			"       <ul class=\"dropdown-menu\" aria-labelledby=\"navbarDropdownMenuTags\">\n";
	}

	if ( $_SESSION['idn_super_admin'] === TRUE or isset( $Permissions['Loxense-ActifsPrimordiauxTags.php'] ) ) {
		$Barre_Menu .= "        <li><a href=\"" . URL_BASE . "/Loxense-ActifsPrimordiauxTags.php\" class=\"dropdown-item\">" . $L_Actifs_Primordiaux . "</a></li>\n";

		$Barre_Separation = TRUE;
	}

	if ( $_SESSION['idn_super_admin'] === TRUE or isset( $Permissions['Loxense-ActifsSupportsTags.php'] ) ) {
		$Barre_Menu .= "        <li><a href=\"" . URL_BASE . "/Loxense-ActifsSupportsTags.php\" class=\"dropdown-item\">" . $L_Actifs_Supports . "</a></li>\n";

		$Barre_Separation = TRUE;
	}

	if ( $Barre_Separation == TRUE ) $Barre_Menu .= "		<li><hr class=\"dropdown-divider\"></li>\n";

	if ( $_SESSION['idn_super_admin'] === TRUE or isset( $Permissions['Loxense-AppreciationRisquesTags.php'] ) ) {
		$Barre_Menu .= "          <li><a href=\"" . URL_BASE . "/Loxense-AppreciationRisquesTags.php\" class=\"dropdown-item\">" . $L_Appreciation_Risques . "</a></li>\n";

		$Barre_Separation = FALSE;
	}

	if ( $_SESSION['idn_super_admin'] === TRUE or isset( $Permissions['Loxense-TraitementRisquesTags.php'] ) ) {
		$Barre_Menu .= "          <li><a href=\"" . URL_BASE . "/Loxense-TraitementRisquesTags.php\" class=\"dropdown-item\">" . $L_Traitement_Risques . "</a></li>\n";

		$Barre_Separation = FALSE;
	}

	if ( $_SESSION['idn_super_admin'] === TRUE or $Option_Gestion_Tags === TRUE ) {
		$Barre_Menu .= "       </ul>\n" .
			"      </li>\n";
	}
*/

	$Barre_Menu .= "     </ul>\n" .
		"     <form id=\"info_utilisateur\" class=\"d-flex\">\n" .
		"      <span id=\"nom_utilisateur\"><i class=\"bi-person-fill\"></i>&nbsp;" . $_SESSION[ 'cvl_prenom' ] . " " . $_SESSION[ 'cvl_nom' ] . "</span>\n" .
		"      <button id=\"code_utilisateur\" tabindex=\"0\" type=\"button\" 
class=\"btn btn-outline-secondary btn-sm\"
data-bs-toggle=\"offcanvas\"
data-bs-target=\"#offcanvasDetailUtilisateur\" 
aria-controls=\"offcanvasDetailUtilisateur\">" . $_SESSION[ 'idn_login' ] . "</button>\n" .
		"      <span>" . $L_ExpireDans . "</span>\n" .
		"      <button id=\"temps_session\" type=\"button\" class=\"btn btn-outline-secondary btn-sm\">" . $this->recupererParametre( 'expiration_time' ) . "</button>\n" .
		"      <span>mn</span>\n" .
		"     </form>\n" .
		"    </div><!-- /#main-menu -->\n" .
		"   </div><!-- /.container-fluid -->\n" .
		"  </nav>\n\n" .

		"  <div class=\"offcanvas offcanvas-end\" tabindex=\"-1\" id=\"offcanvasChangerUnivers\" aria-labelledby=\"offcanvasChangerUnivers\">\n" .
		"   <div class=\"offcanvas-header\">\n" .
		"    <h5 class=\"offcanvas-title fg_couleur_2\" id=\"offcanvasRightLabel\">" . stripslashes( $L_User_Informations ) . "</h5>" .
		"    <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"offcanvas\" aria-label=\"Close\"></button>" .
		"   </div> <!-- /.offcanvas-header -->\n" .
		"   <div class=\"offcanvas-body\">\n" .
		"    <p>" . stripslashes( $L_Last_Connection_Date ) . "&nbsp;:<br/>\n" .
		"    <b>" . $_SESSION[ 'idn_derniere_connexion' ] . "</b></p>\n" .
		"    <p>" . stripslashes( $L_Last_Password_Change ) . "&nbsp;: <br/>\n" .
		"    <b>" . $_SESSION[ 'idn_date_modification_authentifiant' ] . "</b></p>\n" .
		"    <p><span><button id=\"dcnx\" type=\"button\" class=\"btn btn-outline-secondary btn-sm\">" . $L_Deconnexion . "</button>\n" .
		"    <button id=\"chgMdP\" type=\"button\" class=\"btn btn-outline-secondary btn-sm\">" . $L_Changer_Mot_Passe . "</button></span></p>\n" .
		"   </div> <!-- /.offcanvas-body -->\n" .
		"  </div> <!-- /.offcanvas -->\n" .

		"  <div class=\"offcanvas offcanvas-start\" tabindex=\"-1\" id=\"offcanvasChangerUnivers\" aria-labelledby=\"offcanvasChangerUnivers\">\n" .
		"   <div class=\"offcanvas-header\">\n" .
		"    <h5 class=\"offcanvas-title fg_couleur_2\" id=\"offcanvasExampleLabel\">" . $L_Changement_Univers . "</h5>\n" .
		"    <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"offcanvas\" aria-label=\"Close\"></button>\n" .
		"   </div> <!-- /.offcanvas-header -->\n" .
		"   <div class=\"offcanvas-body\" id=\"corps_tableau_univers\">\n" .
		"    <div class=\"row liste mysecdash\">\n" .
		"     <div class=\"col-2 align-middle text-center\"><img src=\"" . URL_IMAGES . "/Logo-MySecDash-4.svg\" width=\"50\" class=\"mx-auto\"></div>\n" .
		"     <div class=\"col gris\">" . $L_Mes_Tableaux_Bord . "</div>\n" .
		"    </div> <!-- /.row  -->\n" .
		"    <div class=\"row liste mycontinuity\">\n" .
		"     <div class=\"col-2 align-middle text-center\"><img src=\"" . URL_IMAGES . "/Logo-MyContinuity.svg\" width=\"50\"></div>\n" .
		"     <div class=\"col gris\">" . $L_Gestion_Continuite . "</div>\n" .
		"    </div>  <!-- /.row  -->\n" .
		"    <div class=\"row liste myrisk\">\n" .
		"     <div class=\"col-2 align-middle text-center\"><img src=\"" . URL_IMAGES . "/Logo-MyRisk.svg\" width=\"50\"></div>\n" .
		"     <div class=\"col gris\">" . $L_Gestion_Carto_Risques . "</div>\n" .
		"    </div>  <!-- /.row  -->\n" .
		"   </div> <!-- /.offcanvas-body -->\n" .
		"  </div> <!-- /.offcanvas -->\n" ;


	return $Barre_Menu;
}



public function construireTitreEcran( $Titre_Ecran, $Societes_Autorisees = [], $Boutons_Alternatifs = [], $Options_Titre = '' ) {
/**
* Standardisation du titre et des informations contextuelles des écrans.
*
* @license Copyleft
* @author Pierre-Luc MARY
* @date 2023-12-18
*
* @param[in] $Titre_Ecran Titre de l'écran courant.
* @param[in] $Societes_Autorisees Liste des sociétés autorisées pour l'utilisateur (si plusieurs sociétés, on affiche une Dropdownlist pour pouvoir changer, sinon on affiche simplement l'information).
* @param[in] $Boutons_Alternatifs Permet l'affichage de boutons alternatifs (juste à droite du titre).
* @param[in] $Options_Titre Permet d'afficher une liste (en fonction du contexte).
*
* @param[out] $Objet_Titre_Ecran Objet HTML représentant la barre de titre et ses éventuelles options est à intégrer dans sa page HTML
*
* @return Retourne la chaîne standardisant l'affichage du menu contextuel (sous forme de liste déroulante).
*/
	include (DIR_LIBELLES . "/" . $_SESSION[ 'Language' ] . "_libelles_generiques.php");


	$Objet_Titre_Ecran = "  <!-- === Zone : titre de l'écran === -->\n" .
		"  <div class=\"container-fluid row\" id=\"titre_ecran\">\n";
	
	if ( $Societes_Autorisees != [] ) {
		$Objet_Titre_Ecran .= "   <div class=\"col-lg-6 mb-2\">\n" .
			"    <div class=\"input-group input-group-sm\">\n" .
			"     <span class=\"input-group-text\">" . $L_Societe . "</span>\n";
		
		if ( count($Societes_Autorisees) > 1 ) {
			$Objet_Titre_Ecran .= "     <select id=\"s_sct_id\" class=\"form-select form-select-sm gris\">\n";
			
			foreach( $Societes_Autorisees as $Societe_Autorisee) {
				$Objet_Titre_Ecran .= "      <option value=\"" . $Societe_Autorisee['Id'] . "\">" . $Societe_Autorisee['Nom'] . "</option>\n";
			}
			
			$Objet_Titre_Ecran .= "     </select>\n";
		} else {
			$Objet_Titre_Ecran .= "     <input type=\"text\" id=\"s_sct_id\" class=\"form-control gris\" data-value=\"" . $Societes_Autorisees['Id'] . "\" value=\"" . $Societes_Autorisees['Nom'] . "\" disabled=\"\">\n";
		}

		$Objet_Titre_Ecran .= "    </div> <!-- input-group -->\n" .
			"   </div> <!-- .col-lg-6 -->\n\n";
	}
		
	$Objet_Titre_Ecran .= "   <div id=\"titre-menu\" class=\"col-lg-7\">\n" .
		"    <div class=\"input-group input-group-lg\">\n" .
		"     <span class=\"input-group-text libelle-titre-menu\">" . $Titre_Ecran . "</span>\n";
	
	if ( $Boutons_Alternatifs != [] ) {
		foreach ($Boutons_Alternatifs as $Bouton_Alternatif) {
			$Objet_Titre_Ecran .= '     <button class="btn btn-outline-secondary ' . $Bouton_Alternatif['class'] . '" type="button" title="' . $Bouton_Alternatif['libelle'] . '">' .
				'<i class="bi-' . $Bouton_Alternatif['glyph'] . '"></i>' .
				"</button>\n";
		}
	}

	$Objet_Titre_Ecran .= "    </div> <!-- input-group -->\n" .
		"   </div> <!-- #titre-menu -->\n\n";


	if ( $Options_Titre != '' ) {
		$Objet_Titre_Ecran .= "   <div id=\"option-titre-menu\" class=\"col-lg-5 mt-1\">\n" .
			"    <div class=\"input-group col-lg-5\">\n" .
			"    <span class=\"input-group-text libelle-alternative-titre-menu\">" . $Options_Titre['Libelle'] . "</span>\n" .
			"     <select id=\"" . $Options_Titre['Id'] . "\" class=\"form-select\">\n";

		foreach( $Options_Titre as $Option_Titre ) {
			$Infos_Complementaires = '';
			
			if ( isset($Option_Titre['Infos']) ) {
				foreach( $Option_Titre['Infos'] as $Info ) {
					$Objet_Titre_Ecran .= " data-" . $Info['Nom'] . "\"\"=\"" . $Info['Valeur'] . "\"";
				}
			}
			$Objet_Titre_Ecran .= "      <option value=\"". $Option_Titre['Id'] . "\"" . $Infos_Complementaires . ">". $Option_Titre['Nom'] . "</option>\n";
		}

		$Objet_Titre_Ecran .= "     </select>\n" .
		"    </div> <!-- .input-group -->\n" .
		"   </div> <!-- #option-titre-menu -->\n";
	}
	
	$Objet_Titre_Ecran .= "  </div> <!-- /#titre_ecran -->\n";
	

	return $Objet_Titre_Ecran;
}



public function construireDebutEnteteTableau() {
/**
* Standardisation du début de l'entête du tableau central.
*
* @license Copyleft
* @author Pierre-Luc MARY
* @date 2015-07-23
*
* @return Retourne la chaîne à afficher.
*/

	$Texte = "  <!-- === Zone : entête du tableau central === -->\n" .
		"  <div class=\"container-fluid\" id=\"entete_tableau\">\n";

	return $Texte;
}



public function construireDebutCorpsTableau() {
/**
* Standardisation du début du corps du tableau central.
*
* @license Copyleft
* @author Pierre-Luc MARY
* @date 2015-07-23
*
* @return Retourne la chaîne à afficher.
*/

	$Texte = "  <!-- === Zone : corps du tableau central === -->\n" .
		"  <div class=\"container-fluid\" id=\"corps_tableau\">\n";

	return $Texte;
}



public function construireDebutPiedTableau() {
/**
* Standardisation du début du pied du tableau central.
*
* @license Copyleft
* @author Pierre-Luc MARY
* @date 2015-07-23
*
* @return Retourne la chaîne à afficher.
*/

	$Texte = "  <!-- === Zone : pied du tableau central === -->\n" .
		"  <div class=\"container-fluid\" id=\"pied_tableau\">\n";

	return $Texte;
}



public function construireFinTableau() {
/**
* Standardisation des fins de partie du tableau central (utilisable pour l'entête, le corps et le pied du tableau).
*
* @license Copyleft
* @author Pierre-Luc MARY
* @date 2015-07-23
*
* @return Retourne la chaîne à afficher.
*/

	$Texte = "  </div>\n\n";

	return $Texte;
}



public function construireEnteteTableau( $Colonnes ) {
/**
* Standardisation de l'entête du tableau central.
*
* @license Copyleft
* @author Pierre-Luc MARY
* @date 2015-08-04
*
* @param[in] $Colonnes_Entete Tableau matérialisant les colonnes composant l'entête.
*
* @return Retourne la chaîne à afficher.
*/
	include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php' );
	
	$Texte = $this->construireDebutEnteteTableau();

	// Affichage des titres de colonne de l'écran.
	$Texte .= "   <div class=\"row\">\n";

	foreach ( $Colonnes[ 'Colonnes' ] as $Colonne ) {
		$Class = '';
		$Sens_Tri = '';
		$Marquer_colonne = '';
	
		// Vérifie si la colonne est triable.
		if ( isset( $Colonne[ 'triable' ] ) ) {
			if ( strtolower( $Colonne[ 'triable' ] ) == 'oui' ) {
				$Class .= ' triable';
				$Marquer_colonne = '&nbsp;<i class="bi-chevron-down"></i> ';
			}
		}

		// Vérifie si le tri est actif sur cette colonne au départ.
		if ( isset( $Colonne[ 'tri_actif' ] ) ) {
			if ( strtolower( $Colonne[ 'tri_actif' ] ) == 'oui' ) {
				$Class .= ' active';
			}
		}

		// Vérifie s'il y a un sens de tri à appliquer.
		if ( isset( $Colonne[ 'sens_tri' ] ) ) {
			$Sens_Tri = 'data-sens-tri="' . $Colonne[ 'sens_tri' ] . '"';
		}

		// Mise en place de la colonne
		$Texte .= "	<div class=\"col-lg-" . $Colonne[ 'taille' ] . $Class . "\" " . $Sens_Tri . ">" .
			$Colonne['titre'] . $Marquer_colonne . "</div>\n";
	}


	if ( isset( $Colonnes[ 'Actions' ] ) ) {
		if ( ! isset($Colonnes[ 'Actions' ][ 'titre' ]) ) $Colonnes[ 'Actions' ][ 'titre' ] = '';

		$tmpActionClass = '';

		if ( isset($Colonnes[ 'Actions' ][ 'affichage' ]) ) {
			$tmpActionClass = ' affichage';
			if ( $Colonnes[ 'Actions' ][ 'affichage' ] == 'cacher' ) $tmpActionClass = ' hide';
			elseif ( $Colonnes[ 'Actions' ][ 'affichage' ] == 'invisible' ) $tmpActionClass = ' invisible';
			elseif ( $Colonnes[ 'Actions' ][ 'affichage' ] == 'invisible-droit' ) $tmpActionClass = ' invisible text-end';
		}

		$Texte .= "	<div class=\"btn-actions col-lg-" . $Colonnes[ 'Actions' ][ 'taille' ] . $tmpActionClass . "\">" . $Colonnes[ 'Actions' ][ 'titre' ] . "</div>\n";
	}

	$Texte .= "   </div> <!-- /.row -->\n";

	
	if (isset($Colonnes[ 'Colonnes' ][0]['nom'])) {
		// Affichage des champs de recherche (masqué par défaut).
		$Texte .= "   <div class=\"row criteres_recherche pb-1 pt-1 d-none\">\n";
		
		foreach ( $Colonnes[ 'Colonnes' ] as $Colonne ) {
			// Calcul de la colonne
			$Texte .= "	<div class=\"col-lg-" . $Colonne[ 'taille' ] . "\">" .
				"<input id=\"rech_" . $Colonne['nom'] . "\" type=\"text\" class=\"form-control\"></div>\n";
		}
		
		
		if ( isset( $Colonnes[ 'Actions' ] ) ) {
			$Texte .= "	<div class=\"btn-actions col-lg-" . $Colonnes[ 'Actions' ][ 'taille' ] . "\">" .
				"<button class=\"btn btn-outline-secondary btn-sm lancer-recherche\">" . $L_Rechercher . "</button></div>\n";
		}
		
		$Texte .= "   </div> <!-- /.row -->\n";
	}
	
	// Mise en place de l'occurrence de recherche.
	$Criteres_Recherche = '';
	$Flag_Recherche = 0;

	foreach ( $Colonnes[ 'Colonnes' ] as $Colonne ) {
		if ( isset( $Colonne[ 'recherche' ] ) and $Criteres_Recherche == '' ) {
			$Flag_Recherche = 1;
			$Texte .= "   <div class=\"row recherche\">\n";
		}

		if ( isset( $Colonne[ 'recherche' ] ) ) {
			if ( isset( $Colonne[ 'recherche' ][ 'nom' ] ) ) {
				$Criteres_Recherche .= '<div class="col-lg-' . $Colonne[ 'taille' ] . '">';

				if ( ! isset( $Colonne[ 'recherche' ][ 'type' ] ) ) $Colonne[ 'recherche' ][ 'type' ] = 'input';

				switch( $Colonne[ 'recherche' ][ 'type' ] ) {
				 case 'input':
					$Criteres_Recherche .= '<input name="rech-' . $Colonne[ 'recherche' ][ 'nom' ] . '" class="form-control" type="text" ';

					if ( isset( $Colonne[ 'recherche' ][ 'arriere_plan' ] ) ) {
						$Criteres_Recherche .= 'placeholder="' . $Colonne[ 'recherche' ][ 'arriere_plan' ] . '" ';
					}

					if ( isset( $Colonne[ 'recherche' ][ 'info_bulle' ] ) ) {
						$Criteres_Recherche .= 'title="' . $Colonne[ 'recherche' ][ 'info_bulle' ] . '" ';
					}

					$Criteres_Recherche .= '>';

					break;

				 case 'select':
					$Criteres_Recherche .= '<select name="rech-' . $Colonne[ 'recherche' ][ 'nom' ] . '" class="form-select">';

					$Criteres_Recherche .= $Colonne[ 'recherche' ][ 'fonction' ]();

					$Criteres_Recherche .= '</select>';

					break;
				}

				$Criteres_Recherche .= '</div>';
			} else {
				$Criteres_Recherche .= '<div class="col-lg-' . $Colonne[ 'taille' ] . '">';

				foreach ( $Colonne[ 'recherche' ] as $Critere_Recherche ) {
					if ( ! isset( $Critere_Recherche[ 'type' ] ) ) $Critere_Recherche[ 'type' ] = 'input';

					if ( $Critere_Recherche[ 'type' ] == 'input' ) {
						$Criteres_Recherche .= '<input name="rech-' . $Critere_Recherche[ 'nom' ] . '" class="form-control" type="text" ';

						if ( isset( $Critere_Recherche[ 'arriere_plan' ] ) ) {
							$Criteres_Recherche .= 'placeholder="' . $Critere_Recherche[ 'arriere_plan' ] . '" ';
						}

						if ( isset( $Critere_Recherche[ 'info_bulle' ] ) ) {
							$Criteres_Recherche .= 'title="' . $Critere_Recherche[ 'info_bulle' ] . '" ';
						}

						$Criteres_Recherche .= '>';
					}
				}

				$Criteres_Recherche .= '</div>';
			}
		}

	}

	if ( $Flag_Recherche == 1 )	$Texte .= $Criteres_Recherche . "   </div> <!-- /.row .recherche -->\n";

	$Texte .= $this->construireFinTableau();

	return $Texte;
}



public function construireCorpsTableau( $Format_Colonnes, $Occurrences ) {
/**
* Standardisation de la création des occurrences dans les parties du tableau central.
*
* @license Copyleft
* @author Pierre-Luc MARY
* @date 2015-08-05
*
* @param[in] $Format_Colonnes Indique le format d'affichage des colonnes
* @param[in] $Occurrences Valeur des colonnes à afficher.
*
* @return Retourne la chaîne afficher.
*/
	include (DIR_LIBELLES . "/" . $_SESSION[ 'Language' ] . "_libelles_generiques.php");

	$Texte = $this->construireDebutCorpsTableau();

	foreach( $Occurrences as $Occurrence ) { // Lit les occurrences une par une.
		$Corps_Occurrence = ''; // Réinitialisation de la variable à chaque boucle.
		$Data_Maximum = '';
		$Data_Lignes = '';

		foreach ( $Occurrence as $Nom => $Valeur ) { // Traite les colonnes une par une.
			if ( $Format_Colonnes[ $Nom ][ 'type' ] == 'id' ) {
				$ID = $Valeur;
				$Entete_Occurrence = "   <div class=\"row liste\" id=\"" . $Format_Colonnes[ 'Prefixe' ] . '_' . $ID . "\">\n" ;
				continue;
			}

			if ( isset( $Format_Colonnes[ $Nom ][ 'type' ] ) ) {
				$Data_Type = ' data-type="' . $Format_Colonnes[ $Nom ][ 'type' ] . '"';
			}

			if ( isset( $Format_Colonnes[ $Nom ][ 'type_input' ] ) ) {
				$Data_Maximum = ' data-type_input="' . $Format_Colonnes[ $Nom ][ 'type_input' ] . '"';
			}

			if ( isset( $Format_Colonnes[ $Nom ][ 'maximum' ] ) ) {
				$Data_Maximum = ' data-maximum="' . $Format_Colonnes[ $Nom ][ 'maximum' ] . '"';
			}

			if ( isset( $Format_Colonnes[ $Nom ][ 'casse' ] ) ) {
				$Data_Maximum = ' data-casse="' . $Format_Colonnes[ $Nom ][ 'casse' ] . '"';
			}

			if ( isset( $Format_Colonnes[ $Nom ][ 'lignes' ] ) ) {
				$Data_Lignes = ' data-lignes="' . $Format_Colonnes[ $Nom ][ 'lignes' ] . '"';
			}

			if ( isset( $Format_Colonnes[ $Nom ][ 'modifiable' ] ) ) {
				if ( strtolower( $Format_Colonnes[ $Nom ][ 'modifiable' ] ) == 'oui' ) {
					$Class = 'modifiable';
				}
			}

			$Corps_Occurrence .= '	<div class="col-lg-' . $Format_Colonnes[ $Nom ][ 'taille' ] . '"' . $Data_Type .
				$Data_Maximum . $Data_Lignes . ' data-src="' . $Nom . '">' . "\n" .
				'	 <span class="' . $Class . '" onClick="' . $Format_Colonnes['fonction_ouverture'] . '(event,' . $ID . ');">' .
				$Valeur . "</span>\n" .
				"	</div>\n";
			
		}

		$Texte .= $Entete_Occurrence . $Corps_Occurrence;

		// Traitement spécial de la colonne "Actions" (affichage des boutons): 'dupliquer', 'modifier', 'supprimer', 'supprimer_libelle', 'ignorer_risque'
		$Boutons = '';

		foreach( $Format_Colonnes[ 'Actions' ][ 'boutons' ] as $Bouton ) {
			switch( $Bouton ) {
				case 'historique':
					$Libelle = $L_Consulter_Historique;
					$Glyph = 'clock';
					break;

				case 'visualiser':
					$Libelle = $L_Visualiser;
					$Glyph = 'eye-fill';
					break;

				case 'dupliquer':
					$Libelle = $L_Dupliquer;
					$Glyph = 'files';
					break;

				case 'modifier':
					$Libelle = $L_Modifier;
					$Glyph = 'pencil';
					break;

				case 'supprimer':
					$Libelle = $L_Supprimer;
					$Glyph = 'x-circle';
					break;

				case 'supprimer_libelle':
					$Libelle = $L_Supprimer_Libelle;
					$Glyph = 'x-circle-fill';
					break;

				case 'ignorer_risque':
					$Libelle = $L_Ignorer_Risque;
					$Glyph = 'slash-circle';
					break;

				case 'generer':
					$Libelle = $L_Generer_Impression;
					$Glyph = 'arrow-repeat';
					break;

				case 'telecharger':
					$Libelle = $L_Telecharger_Impression;
					$Glyph = 'cloud-download'; // 'import'
					break;

				case 'telecharger_w':
					$Libelle = $L_Telecharger_Word;
					$Glyph = 'Word-2-icon.png';
					break;

				case 'telecharger_e':
					$Libelle = $L_Telecharger_Excel;
					$Glyph = 'Excel-2-icon.png';
					break;

				case 'exporter':
					$Libelle = $L_Exporter_Base;
					$Glyph = 'cloud-upload';
					break;

				case 'restaurer':
					$Libelle = $L_Restaurer_Base;
					$Glyph = 'cloud-arrow-down';
					break;

				case 'imprimer':
					$Libelle = $L_Imprimer;
					$Glyph = 'printer-fill';
					break;
			}

			$Boutons .= '	 <button class="btn btn-outline-secondary btn-xs btn-' . $Bouton . '" type="button" title="' . $Libelle . '"' .
			' data-id="' . $ID . '">' .
				'<i class="bi-' . $Glyph . '"></i>' .
				"</button>\n";
		}

		$tmpActionClass = '';

		if ( isset($Format_Colonnes[ 'Actions' ][ 'affichage' ]) ) {
			if ( $Format_Colonnes[ 'Actions' ][ 'affichage' ] == 'cacher' ) $tmpActionClass = ' hide';
			if ( $Format_Colonnes[ 'Actions' ][ 'affichage' ] == 'invisible' ) $tmpActionClass = ' invisible';
			if ( $Format_Colonnes[ 'Actions' ][ 'affichage' ] == 'invisible-droit' ) $tmpActionClass = ' invisible text-end';
		}

		$Texte .= "	<div class=\"btn-actions col-lg-" . $Format_Colonnes[ 'Actions' ][ 'taille' ] . $tmpActionClass ."\">\n" . $Boutons . "	</div>\n";

		$Texte .= "   </div>\n";
	}

	$Texte .= $this->construireFinTableau();

	return $Texte;
}



public function construirePiedTableau( $Total = '', $Bouton_Alternatif = '' ) {
/**
* Standardisation de la création des occurrences dans les parties du tableau central.
*
* @license Copyleft
* @author Pierre-Luc MARY
* @date 2015-08-05
*
* @param[in] $Format_Colonnes Indique le format d'affichage des colonnes
* @param[in] $Occurrences Valeur des colonnes à afficher.
* @param[in] $Bouton_Alternatif Permet d'afficher un bouton alertnatif en bas du tableau.
*
* @return Retourne la chaîne afficher.
*/
	include (DIR_LIBELLES . "/" . $_SESSION[ 'Language' ] . "_libelles_generiques.php");

	$Texte = $this->construireDebutPiedTableau() . "   <div class=\"row\">\n";
		//"	<div class=\"col-12\">";

	if ( $Total !== '' ) {
		$Texte .= '<h6 style="margin-top: 0.5rem;">' . $L_Total . ' : <span class="badge bg-secondary" id="totalOccurrences">' . sprintf( "%03d", $Total ) . "</span></h6>";
	} else {
		$Texte .= "&nbsp;";
	}


	if ( $Bouton_Alternatif != '' ) {
		$Texte .= '<button class="btn btn-outline-secondary btn-sm ' . $Bouton_Alternatif['class'] . '">' . $Bouton_Alternatif['libelle'] . '</button>';
	}

	$Texte .= //"</div>\n" .
		"   </div>\n" . 
	$this->construireFinTableau();

	return $Texte;
}



public function contruireTableauVide( $Colonnes_Entete, $Bouton_Alternatif = '' ) {
/**
* Standardisation de la création d'un tableau central vide (il sera rempli par un appel AJAX ultérieur).
*
* @license Copyleft
* @author Pierre-Luc MARY
* @date 2015-10-11
*
* @param[in] $Colonnes_Entete Indique le format des colonnes d'entête à afficher.
* @param[in] $Bouton_Alternatif Permet d'afficher un bouton alertnatif en bas du tableau.
*
* @return Retourne la chaîne afficher.
*/

	$Codes_HTML = $this->construireEnteteTableau( $Colonnes_Entete ) .
		$this->construireDebutCorpsTableau() .
		$this->construireFinTableau() .
		$this->construirePiedTableau( 0, $Bouton_Alternatif );

	return $Codes_HTML;
}



public function construireModal( $Id_Modal = 'plmModal', $Titre, $Corps, $Id_Bouton, $Libelle_Bouton, $Bouton_Fermer = TRUE ) {
/**
* Standardisation des écrans de type "modal".
* La standardisation définit une fenêtre complète.
*
* @license Copyleft
* @author Pierre-Luc MARY
* @date 2015-07-23
*
* @param[in] $Id_Modal ID de la "modal" dans le DOM de la page.
* @param[in] $Titre Indique le titre à afficher en haut de la fenêtre "modal".
* @param[in] $Corps Corps à afficher dans le corps de la fenêtre "modal".
* @param[in] $Id_Bouton Indique l'ID associé au bouton de la fenêtre "modal".
* @param[in] $Libelle_Bouton Indique le libellé à associer au bouton de la fenêtre "modal".
* @param[in] $Bouton_Fermer Indicateur pour afficher le bouton "fermer" à la fenêtre "modal".
*
* @return Retourne la chaîne à afficher.
*/

	$Texte = "<!-- Modal -->\n" .
		"<div class=\"modal fade\" id=\"" . $Id_Modal . "\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"" . $Id_Modal . "Label\">\n" .
		" <div class=\"modal-dialog\" role=\"document\">\n" .
		"  <div class=\"modal-content\">\n" .
		"   <div class=\"modal-header\">\n" .
		"	<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"" . $L_Fermer . "\"><span aria-hidden=\"true\">&times;</span></button>\n" .
		"	<h4 class=\"modal-title\" id=\"" . $Id_Modal . "Label\">" . $Titre . "</h4>\n" .
		"   </div>\n" .
		"   <div class=\"modal-body\">\n" .
		$Corps . "\n" .
		"   </div>\n" .
		"   <div class=\"modal-footer\">\n";

	if ( $Bouton_Fermer == TRUE ) $Texte .= "	<button type=\"button\" class=\"btn btn-outline-secondary\" data-dismiss=\"modal\">" . $L_Fermer . "</button>\n";

	if ( $Id_Bouton != NULL and $Id_Bouton != '' ) $Texte .= "	<button type=\"button\" class=\"btn btn-primary\" id=\"" . $Id_Bouton . "\">" . $Libelle_Bouton . "</button>\n";

	$Texte .= "   </div>\n" .
		"  </div>\n" .
		" </div>\n" .
		"</div>\n";

	 return $Texte;
}


public function construireFooter( $Flag_Connexion = TRUE ) {
/**
* Standardisation de l'affichage des bas de page.
*
* @license Copyleft
* @author Pierre-Luc MARY
* @date 2015-07-23
*
* @param[in] $Flag_Connexion Indique s'il s'agit de la page de connexion. Si c'est le cas, on n'affiche pas les boutons de déconnexion et de changement de mot de passe.
*
* @return Retourne une chaîne à afficher
*/
	include (DIR_LIBELLES . "/" . $_SESSION[ 'Language' ] . "_libelles_generiques.php");
	include (DIR_LIBELLES . "/" . $_SESSION[ 'Language' ] . "_Loxense-Connexion.php");


	$Texte = "  <!-- Chargement des JavaScripts -->\n" .
	 "  <script src=\"" . URL_LIBRAIRIES . "/bootstrap-dist/js/bootstrap.bundle.min.js\"></script>\n" .
	 "  <script src=\"" . URL_LIBRAIRIES . "/js/bootstrap-colorselector.js\"></script>\n" .
	 "  <script src=\"" . URL_LIBRAIRIES . "/summernote-dist/summernote-bs4.js\"></script>\n" .
	 "  <script src=\"" . URL_LIBRAIRIES . "/js/main.js\"></script>\n\n";

	if ( isset( $_SESSION[ 'idn_derniere_connexion' ] ) ) {
		// Activation des "popover".
		$Texte .= "<script>var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle=\"popover\"]'));
			var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
				return new bootstrap.Popover(popoverTriggerEl);
			})</script>";
	}

	$Texte .= "  <footer>\n" .
	 "   Loxense &copy;, " . $this->Nom_Outil . " v" . $this->Version_Outil . "\n";

	if ( $Flag_Connexion === TRUE ) {
		$Texte .= "   <span><button id=\"dcnx\" type=\"button\" class=\"btn btn-outline-secondary btn-sm\">" . $L_Deconnexion . "</button>\n" .
			"	<button id=\"chgMdP\" type=\"button\" class=\"btn btn-outline-secondary btn-sm\">" . $L_Changer_Mot_Passe . "</button></span>\n";
	}
	
	$Texte .= "  </footer>\n";

	return $Texte;
}



public function construirePiedHTML() {
/**
* Standardisation des fins de page HTML.
*
* @license Copyleft
* @author Pierre-Luc MARY
* @date 2015-07-23
*
* @return Retourne une chaîne à afficher
*/
	return " </body>\n</html>\n" ;
} 



public function afficherNotification( $Message, $Flag_Avertissement = FALSE ) {
/**
* Affiche une boîte d'information.
*
* @license Copyleft
* @author Pierre-Luc MARY
* @version 1.0
* @date 2016-04-23
*
* @param[in] $Message Message à afficher
* @param[in] $Flag_Avertissement Permet d'afficher le message en mode "Avertissement"
*
* @return Retourne une chaîne à afficher
*/
	include (DIR_LIBELLES . "/" . $_SESSION[ 'Language' ] . "_libelles_generiques.php");
	

	if ( $Flag_Avertissement == FALSE ) {
		$Type = "success";
		$Icon = "ok";

		$Texte = "<script>\n" .
			"function effacerMessage() {\n" .
			"	$('.alert').alert('close');\n" .
			"	clearInterval(timer_message);\n" .
			"}\n" .
			"var timer_message = setInterval('effacerMessage()', 1000 * 5); // Déclenche la fonction toutes les 5 secondes.\n" .
			"</script>";

		$IdMessage = '';
	} else {
		$Type = "warning";
		$Icon = "remove";

		$Texte = '';

		$IdMessage = 'alert-' . md5(microtime());
	}

	$Texte .= "<div class=\"container alert alert-" . $Type . " alert-dismissible fade show\" role=\"alert\" " ;

	if ( $IdMessage != '' ) $Texte .= "id=\"" . $IdMessage . "\" ";
	
	$Texte .= "style=\"position:absolute;top:175px;width:80%;left:10%;cursor:pointer;\" onClick=\"javascript:effacerMessage('" . $IdMessage . "');\">" .
//		" <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"" . $L_Fermer . "\"><span aria-hidden=\"true\">&times;</span></button>" .
		" <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"" . $L_Fermer . "\"></button>" .
		" <span class=\"glyphicon glyphicon-" . $Icon . " aria-hidden=\"true\"></span> " . $Message .
		"</div>";

	return $Texte . "		</div>\n";
}



public function construirePageAlerte( $Message, $Script = 'MySecDash-Principal.php', $Type_Message = 2, $Nom_Fichier_Logo = '' ) {
/**
* Affiche d'une page HTML invisible qui fait rediriger vers un script spécifique qui devra afficher le message envoyé dans le formulaire.
*
* @license Copyleft
* @author Pierre-Luc MARY
* @date 2023-12-18
*
* @param[in] $Message Message à afficher
* @param[in] $Script Script à exécuter sur le clique du bouton retour
* @param[in] $TypeMessage Précise quel type d'icône présenter (0 = information, 1 = Avertissement, 2 = Erreur).
*
* @return Retourne une chaîne matérialisant l'affichage de cet écran d'information
*/
	include (DIR_LIBELLES . "/" . $_SESSION[ 'Language' ] . "_libelles_generiques.php");
	
	switch( $Type_Message ) {
		case 0:
			$Titre_Page = $L_Information;
			$Nom_Image = '<i class="bi-info-circle"></i>';
			$Couleur_Fond = 'panel-info';
			break;
		case 1:
			$Titre_Page = $L_Warning;
			$Nom_Image = '<i class="bi-exclamation-triangle"></i>';
			$Couleur_Fond = 'panel-warning';
			break;
		case 2:
			$Titre_Page = $L_Error;
			$Nom_Image = '<i class="bi-dash-circle-fill"></i>';
			$Couleur_Fond = 'panel-danger';
			break;
	}

	if ( $Nom_Fichier_Logo == '' ) {
		$Nom_Fichier_Logo = 'Logo-MySecDash-4.svg';
	}
	
	return $this->construireEnteteHTML( $Titre_Page ) .
	"  <p id=\"logo-img\" class=\"text-center\"><img src=\"" . URL_IMAGES . "/" . $Nom_Fichier_Logo . "\" alt=\"Logo Loxense\" height=\"50px\" /></p>\n\n" .
		"  <div id=\"principal-container\" class=\"container\">\n" .
		"   <div class=\"panel " . $Couleur_Fond . "\">\n" .
		"	<div class=\"panel-heading\">\n" .
		"	 <h3 class=\"panel-title\">" . $Titre_Page . "</h3>\n" .
		"	</div>\n" .
		"	<div class=\"panel-body\">\n" .
		"	 <h2 class=\"text-center\">" . $Nom_Image . "&nbsp;" . $Message . "</h2>\n" .
		"	 <form method=\"post\" action=\"" . URL_BASE . '/' . $Script . "\">\n" .
		"	  <div class=\"form-group text-center\">\n" .
		"	   <button class=\"btn btn-outline-secondary btn-connexion\">" . $L_Retour . "</button>\n" .
		"	  </div>\n" .
		"	 </form>\n" .
		"	</div>\n" .
		"   </div>\n" .
		"  </div>\n" .
		$this->construireFooter( FALSE ) .
		$this->construirePiedHTML();

}


public function routage( $Message, $Script, $TypeMessage = 0 ) {
/**
* Affiche une page HTML invisible qui fait rediriger vers un script spécifique qui devra afficher le message envoyé dans le formulaire.
*
* @license Copyleft
* @author Pierre-Luc MARY
* @date 2015-08-06
*
* @param[in] $Message Message à afficher
* @param[in] $Script Script à exécuter sur le clique du bouton retour
* @param[in] $TypeMessage Précise quel type d'icône présenter.
*
* @return Retourne une chaîne matérialisant l'affichage de cet écran d'information
*/
	if ( strpos( $Script, '?' ) === false ) {
		$Script .= '?notification';
	} else {
		$Script .= '&notification';
	}

	if ( ! preg_match( "/^\//i", $Script ) ) {
		$Script = '/' . $Script;
	}

	return $this->enteteHTML( 'Error Page' ) .
		 "	 <body>\n" .
		 "	  <form name=\"redirection\" method=\"post\" action=\"" . URL_BASE . $Script. "\" >\n" .
		 "	   <input type=\"hidden\" name=\"Message\" value=\"" . $Message . "\" />\n" .
		 "	   <input type=\"hidden\" name=\"Type_Message\" value=\"" . $TypeMessage . "\" />\n" .
		 "	  </form>\n" .
		 "	  <script>\n" .
		 "document.redirection.submit();" .
		 "	  </script>\n" .
		 "	 </body>\n" .
		 $this->piedPageHTML();
}


/*
 * Calcul la couleur du texte (blanc ou noir)
 */
public static function calculCouleurCellule( $code_couleur ) {
	$code_1 = substr( $code_couleur, 0, 2 );
	$code_2 = substr( $code_couleur, 2, 2 );
	$code_3 = substr( $code_couleur, 4, 2 );

	$calcul = (hexdec( $code_1 ) + hexdec( $code_2 ) + hexdec( $code_3)) / 3;

	if ( $calcul < ((hexdec( '66' ) + hexdec( '66' ) + hexdec( '66' )) / 3) ) return 'white';
	else return 'black';
}


public static function calculCouleurCelluleHexa( $code_couleur ) {
	$code_1 = substr( $code_couleur, 0, 2 );
	$code_2 = substr( $code_couleur, 2, 2 );
	$code_3 = substr( $code_couleur, 4, 2 );

	$calcul = (hexdec( $code_1 ) + hexdec( $code_2 ) + hexdec( $code_3)) / 3;

	if ( $calcul < ((hexdec( '66' ) + hexdec( '66' ) + hexdec( '66' )) / 3) ) return 'FFFFFF';
	else return '000000';
}


public static function createComboBox($name, $val="", $class=array(), $colonneInfos=array()){
	$class = implode(" ", $class);

	$comboBox = '<select class="'.$class.' selectpicker" '.
	((array_key_exists("title", $colonneInfos))? 'title="'.$colonneInfos["title"].'" ':"").
	//' title="plop"'.
	'name="'.$name.'" '.
	(($colonneInfos["required"])?'required ':'').
	(($colonneInfos["disabled"])?'disabled ':'').
	(($colonneInfos["readonly"])?'readonly ':'').
	' data-live-search="true" '.
	' data-dropup-Auto="false" '.
	(array_key_exists("size", $colonneInfos)?'size="'.$colonneInfos["size"].'" ':"").
	'>';
	$list = $colonneInfos["list"];

	if(! is_array($list)){
		$list = $list();
	}
	if(empty($list)) $list = array();

	foreach ($list as $key => $valeur) {
		$option = array();

		if(is_array($valeur)){
			$option = $valeur["option"];
			$valeur = trim($valeur["valeur"]);
		}
		$class = array_key_exists("class", $option)? "class='".implode(" ", $option["class"])."' ":"";
		$datas = array_key_exists("datas", $option)? implode(" ", $option["datas"]):"";

		$selectOption = '<option '.$class.' '.$datas.' title="'.$valeur.'" value="'.$key.'"';
		if($val == $key)
			$selectOption .= ' selected';

		$selectOption .= '>'. $valeur .'</option>';
		$comboBox.= $selectOption;
	}
	$comboBox .= "</select>";
	return $comboBox;
}


public static function createTextArea($name, $val, $class, $colonneInfos){
	if($colonneInfos["required"]){
		$class[] = "obligatoire";
	}
		

	if(array_key_exists("case", $colonneInfos)){
		switch ($colonneInfos["case"]) {
			case 'upper':
				$class[] = "upperCase";
				break;
			case 'lower':
				$class[] = "lowerCase";
				break;
			case 'firstUpper':
				$class[] = "firstLetterUpperCase";
				break;
		}
	}

	$maxlength = (array_key_exists("maxlength", $colonneInfos))? 
		'maxlength='.$colonneInfos["maxlength"] : 
		'';

	$placeholder = (array_key_exists("placeholder", $colonneInfos))? 
		'placeholder="'.$colonneInfos["placeholder"].'"' : 
		'';	  

	if(! array_key_exists("tabindex", $colonneInfos))	$colonneInfos["tabindex"] = "";


	$class = implode(" ", $class);

	return '<textArea '.
		' class="'.$class.'" '.
		' name="'.$name.'" '.
		$colonneInfos["required"].' '.
		$colonneInfos["disabled"].' '.
		$colonneInfos["readonly"].' '.
		$colonneInfos["tabindex"].' '.
		$placeholder.' '.
		$maxlength.' '.
		(array_key_exists("size", $colonneInfos)? 'size="'.$colonneInfos["size"].'" ':"").
		'>'.$val.'</textarea>';
}



public function prepareTitreMenuControleAcces( $Permissions, $Script ) {
	/**
	* Prépare le titre menu relatif à la gestion des Contrôles d'Accès.
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2015-10-12
	*
	* @param[in] $permissions Liste des permissions de l'utilisateur sur les scripts.
	* @param[in] $script Nom du script courant.
	*
	* @return Renvoi un tableau formaté.
	*/

	// Récupère les libellés relatifs à la langue courante.
	include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php' );

	// Ignore le '/' en début de chaîne.
	$Script = mb_substr( $Script, 1 );

	$Titres_Nav = array();

	if ( isset( $Permissions[ 'Loxense-Entites.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-Entites.php';
		$Options[ 'libelle' ] = $L_Gestion_Entites;

		if ( $Script == 'Loxense-Entites.php' ) $Options[ 'actif'] = 1;

		$Titres_Nav[] = $Options;
	}

	if ( isset( $Options[ 'actif'] ) ) unset( $Options[ 'actif'] );


	if ( isset( $Permissions[ 'Loxense-Civilites.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-Civilites.php';
		$Options[ 'libelle' ] = $L_Gestion_Civilites;

		if ( $Script == 'Loxense-Civilites.php' ) $Options[ 'actif'] = 1;

		$Titres_Nav[] = $Options;
	}

	if ( isset( $Options[ 'actif'] ) ) unset( $Options[ 'actif'] );


	if ( isset( $Permissions[ 'Loxense-Applications.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-Applications.php';
		$Options[ 'libelle' ] = $L_Gestion_Applications;

		if ( $Script == 'Loxense-Applications.php' ) $Options[ 'actif'] = 1;

		$Titres_Nav[] = $Options;
	}

	if ( isset( $Options[ 'actif'] ) ) unset( $Options[ 'actif'] );


	if ( isset( $Permissions[ 'Loxense-Profils.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-Profils.php';
		$Options[ 'libelle' ] = $L_Gestion_Profils;

		if ( $Script == 'Loxense-Profils.php' ) $Options[ 'actif'] = 1;

		$Titres_Nav[] = $Options;
	}

	if ( isset( $Options[ 'actif'] ) ) unset( $Options[ 'actif'] );


	if ( isset( $Permissions[ 'Loxense-Utilisateurs.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-Utilisateurs.php';
		$Options[ 'libelle' ] = $L_Gestion_Utilisateurs;

		if ( $Script == 'Loxense-Utilisateurs.php' ) $Options[ 'actif'] = 1;

		$Titres_Nav[] = $Options;
	}

	if ( isset( $Options[ 'actif'] ) ) unset( $Options[ 'actif'] );


	if ( isset( $Permissions[ 'Loxense-Gestionnaires.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-Gestionnaires.php';
		$Options[ 'libelle' ] = $L_Gestion_Gestionnaires;

		if ( $Script == 'Loxense-Gestionnaires.php' ) $Options[ 'actif'] = 1;

		$Titres_Nav[] = $Options;
	}

	if ( isset( $Options[ 'actif'] ) ) unset( $Options[ 'actif'] );


	if ( isset( $Permissions[ 'Loxense-Etiquettes.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-Etiquettes.php';
		$Options[ 'libelle' ] = $L_Gestion_Etiquettes;

		if ( $Script == 'Loxense-Etiquettes.php' ) $Options[ 'actif'] = 1;

		$Titres_Nav[] = $Options;
	}

	if ( isset( $Options[ 'actif'] ) ) unset( $Options[ 'actif'] );

	return $Titres_Nav;
}


public function prepareTitreMenuHistorique( $Permissions, $Script ) {
	/**
	* Prépare le titre menu relatif à la gestion des Contrôles d'Accès.
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2015-10-12
	*
	* @param[in] $permissions Liste des permissions de l'utilisateur sur les scripts.
	* @param[in] $script Nom du script courant.
	*
	* @return Renvoi un tableau formaté.
	*/

	// Récupère les libellés relatifs à la langue courante.
	include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php' );

	// Ignore le '/' en début de chaîne.
	$Script = mb_substr( $Script, 1 );

	$Titres_Nav = array();

	if ( isset( $Permissions[ 'Loxense-Historiques.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-Historiques.php';
		$Options[ 'libelle' ] = $L_Consultation_Historique;

		if ( $Script == 'Loxense-Historiques.php' ) $Options[ 'actif'] = 1;

		$Titres_Nav[] = $Options;
	}

	return $Titres_Nav;
}


public function prepareTitreMenuReferencielInterne( $Permissions, $Script ) {
	/**
	* prépare la barre de menu relative à la gestion du Référentiel Interne.
	*
	* @license Loxense, 2013
	* @author Pierre-Luc MARY
	* @date 2016-11-19
	*
	* @param[in] $permissions Liste des permissions de l'utilisateur courant.
	* @param[in] $script Nom du script courant.
	*
	* @return Renvoi un tableau formaté.
	*/

	// Récupère les libellés relatifs à la langue courante.
	include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php' );

	// Ignore le '/' en début de chaîne.
	$Script = mb_substr( $Script, 1 );

	$Titres_Nav = array();

	if ( isset( $Permissions[ 'Loxense-Parametres.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-Parametres.php';
		$Options[ 'libelle' ] = $L_Gestion_Parametres_Base;

		if ( $Script == 'Loxense-Parametres.php' ) $Options[ 'actif'] = 1;

		$Titres_Nav[] = $Options;
	}

	if ( isset( $Options[ 'actif'] ) ) unset( $Options[ 'actif'] );


	if ( isset( $Permissions[ 'Loxense-TypesActifSupport.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-TypesActifSupport.php';
		$Options[ 'libelle' ] = $L_Gestion_Types_Actif_Support;

		if ( $Script == 'Loxense-TypesActifSupport.php' ) $Options[ 'actif'] = 1;

		$Titres_Nav[] = $Options;
	}

	if ( isset( $Options[ 'actif'] ) ) unset( $Options[ 'actif'] );
	
	
	if ( isset( $Permissions[ 'Loxense-ReferentielActifsSupports.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-ReferentielActifsSupports.php';
		$Options[ 'libelle' ] = $L_Gestion_Referentiel_Actifs_Supports;
		
		if ( $Script == 'Loxense-ReferentielActifsSupports.php' ) $Options[ 'actif'] = 1;
		
		$Titres_Nav[] = $Options;
	}
	
	if ( isset( $Options[ 'actif'] ) ) unset( $Options[ 'actif'] );
	
	
	if ( isset( $Permissions[ 'Loxense-ReferentielActifsPrimordiaux.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-ReferentielActifsPrimordiaux.php';
		$Options[ 'libelle' ] = $L_Gestion_Referentiel_Actifs_Primordiaux;
		
		if ( $Script == 'Loxense-ReferentielActifsPrimordiaux.php' ) $Options[ 'actif'] = 1;
		
		$Titres_Nav[] = $Options;
	}
	
	if ( isset( $Options[ 'actif'] ) ) unset( $Options[ 'actif'] );
	

	if ( isset( $Permissions[ 'Loxense-TypesMenaceGenerique.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-TypesMenaceGenerique.php';
		$Options[ 'libelle' ] = $L_Gestion_Types_Menace_Generique;

		if ( $Script == 'Loxense-TypesMenaceGenerique.php' ) $Options[ 'actif'] = 1;

		$Titres_Nav[] = $Options;
	}

	if ( isset( $Options[ 'actif'] ) ) unset( $Options[ 'actif'] );


	if ( isset( $Permissions[ 'Loxense-MenacesGeneriques.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-MenacesGeneriques.php';
		$Options[ 'libelle' ] = $L_Gestion_Menaces_Generiques;

		if ( $Script == 'Loxense-MenacesGeneriques.php' ) $Options[ 'actif'] = 1;

		$Titres_Nav[] = $Options;
	}

	if ( isset( $Options[ 'actif'] ) ) unset( $Options[ 'actif'] );


	if ( isset( $Permissions[ 'Loxense-VulnerabilitesGeneriques.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-VulnerabilitesGeneriques.php';
		$Options[ 'libelle' ] = $L_Gestion_Vulnerabilites_Generiques;

		if ( $Script == 'Loxense-VulnerabilitesGeneriques.php' ) $Options[ 'actif'] = 1;

		$Titres_Nav[] = $Options;
	}

	if ( isset( $Options[ 'actif'] ) ) unset( $Options[ 'actif'] );


	if ( isset( $Permissions[ 'Loxense-SourcesMenaces.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-SourcesMenaces.php';
		$Options[ 'libelle' ] = $L_Gestion_Sources_Menaces;

		if ( $Script == 'Loxense-SourcesMenaces.php' ) $Options[ 'actif'] = 1;

		$Titres_Nav[] = $Options;
	}

	if ( isset( $Options[ 'actif'] ) ) unset( $Options[ 'actif'] );


	if ( isset( $Permissions[ 'Loxense-RisquesGeneriques.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-RisquesGeneriques.php';
		$Options[ 'libelle' ] = $L_Gestion_Risques_Generiques;

		if ( $Script == 'Loxense-RisquesGeneriques.php' ) $Options[ 'actif'] = 1;

		$Titres_Nav[] = $Options;
	}

	if ( isset( $Options[ 'actif'] ) ) unset( $Options[ 'actif'] );

	if ( isset( $Permissions[ 'Loxense-TypesTraitementRisques.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-TypesTraitementRisques.php';
		$Options[ 'libelle' ] = $L_Gestion_Types_Traitement_Risques;

		if ( $Script == 'Loxense-TypesTraitementRisques.php' ) $Options[ 'actif'] = 1;

		$Titres_Nav[] = $Options;
	}

	if ( isset( $Options[ 'actif'] ) ) unset( $Options[ 'actif'] );


	if ( isset( $Permissions[ 'Loxense-ImpactsGeneriques.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-ImpactsGeneriques.php';
		$Options[ 'libelle' ] = $L_Gestion_Impacts_Generiques;

		if ( $Script == 'Loxense-ImpactsGeneriques.php' ) $Options[ 'actif'] = 1;

		$Titres_Nav[] = $Options;
	}

	if ( isset( $Options[ 'actif'] ) ) unset( $Options[ 'actif'] );


	if ( isset( $Permissions[ 'Loxense-MesuresGeneriques.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-MesuresGeneriques.php';
		$Options[ 'libelle' ] = $L_Gestion_Mesures_Generiques;

		if ( $Script == 'Loxense-MesuresGeneriques.php' ) $Options[ 'actif'] = 1;

		$Titres_Nav[] = $Options;
	}

	if ( isset( $Options[ 'actif'] ) ) unset( $Options[ 'actif'] );


	if ( isset( $Permissions[ 'Loxense-ReferentielsConformite.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-ReferentielsConformite.php';
		$Options[ 'libelle' ] = $L_Gestion_Referentiels_Conformite;

		if ( $Script == 'Loxense-ReferentielsConformite.php' ) $Options[ 'actif'] = 1;

		$Titres_Nav[] = $Options;
	}

	if ( isset( $Options[ 'actif'] ) ) unset( $Options[ 'actif'] );

	return $Titres_Nav;	
}


public function prepareTitreMenuCartographiesRisques( $Permissions, $Script ) {
	/**
	* prépare la barre de menu relative à la gestion des Cartographies des Risques.
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2016-07-20
	*
	* @param[in] $permissions Liste des permissions de l'utilisateur courant.
	* @param[in] $script Nom du script courant.
	*
	* @return Renvoi un tableau formaté.
	*/

	// Récupère les libellés relatifs à la langue courante.
	include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php' );
	include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_Loxense-CartographiesRisques.php' );

	// Ignore le '/' en début de chaîne.
	$Script = mb_substr( $Script, 1 );

	$Titres_Nav = array();

	if ( isset( $Permissions[ 'Loxense-CriteresValorisationActifs.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-CriteresValorisationActifs.php';
		$Options[ 'libelle' ] = $L_Gestion_Criteres_Valorisation_Actifs;

		if ( $Script == 'Loxense-CriteresValorisationActifs.php' ) $Options[ 'actif'] = 1;

		$Titres_Nav[] = $Options;
	}

	if ( isset( $Options[ 'actif'] ) ) unset( $Options[ 'actif'] );


	if ( isset( $Permissions[ 'Loxense-CriteresAppreciationAcceptationRisques.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-CriteresAppreciationAcceptationRisques.php';
		$Options[ 'libelle' ] = $L_Gestion_Criteres_Appreciation_Acceptation_Risques;

		if ( $Script == 'Loxense-CriteresAppreciationAcceptationRisques.php' ) $Options[ 'actif'] = 1;

		$Titres_Nav[] = $Options;
	}

	if ( isset( $Options[ 'actif'] ) ) unset( $Options[ 'actif'] );


	if ( isset( $Permissions[ 'Loxense-GrillesImpacts.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-GrillesImpacts.php';
		$Options[ 'libelle' ] = $L_Gestion_Grilles_Impacts;

		if ( $Script == 'Loxense-GrillesImpacts.php' ) $Options[ 'actif'] = 1;

		$Titres_Nav[] = $Options;
	}

	if ( isset( $Options[ 'actif'] ) ) unset( $Options[ 'actif'] );


	if ( isset( $Permissions[ 'Loxense-GrillesVraisemblances.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-GrillesVraisemblances.php';
		$Options[ 'libelle' ] = $L_Gestion_Grilles_Vraisemblances;

		if ( $Script == 'Loxense-GrillesVraisemblances.php' ) $Options[ 'actif'] = 1;

		$Titres_Nav[] = $Options;
	}

	if ( isset( $Options[ 'actif'] ) ) unset( $Options[ 'actif'] );


	if ( isset( $Permissions[ 'Loxense-CartographiesRisques.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-CartographiesRisques.php';
		$Options[ 'libelle' ] = $L_Gestion_Cartographies_Risques;

		if ( $Script == 'Loxense-CartographiesRisques.php' ) $Options[ 'actif'] = 1;

		$Titres_Nav[] = $Options;
	}

	if ( isset( $Options[ 'actif'] ) ) unset( $Options[ 'actif'] );


	if ( isset( $Permissions[ 'Loxense-EditionsRisques.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-EditionsRisques.php';
		$Options[ 'libelle' ] = $L_Gestion_Editions_Risques;

		if ( $Script == 'Loxense-EditionsRisques.php' ) $Options[ 'actif'] = 1;

		$Titres_Nav[] = $Options;
	}

	if ( isset( $Options[ 'actif'] ) ) unset( $Options[ 'actif'] );


	if ( isset( $Permissions[ 'Loxense-MatricesRisques.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-MatricesRisques.php';
		$Options[ 'libelle' ] = $L_Visualisation_Matrices_Risques;

		if ( $Script == 'Loxense-MatricesRisques.php' ) $Options[ 'actif'] = 1;

		$Titres_Nav[] = $Options;
	}

	if ( isset( $Options[ 'actif'] ) ) unset( $Options[ 'actif'] );


	return $Titres_Nav;	
}


public function prepareTitreMenuActifs( $Permissions, $Script ) {
	/**
	* prépare la barre de menu relative à la gestion des Actifs.
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2016-07-20
	*
	* @param[in] $permissions Liste des permissions de l'utilisateur courant.
	* @param[in] $script Nom du script courant.
	*
	* @return Renvoi un tableau formaté.
	*/

	// Récupère les libellés relatifs à la langue courante.
	include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php' );
	include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_Loxense-CartographiesRisques.php' );

	// Ignore le '/' en début de chaîne.
	$Script = mb_substr( $Script, 1 );

	$Titres_Nav = array();

	if ( isset( $Permissions[ 'Loxense-ActifsPrimordiaux.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-ActifsPrimordiaux.php';
		$Options[ 'libelle' ] = $L_Gestion_Actifs_Primordiaux;

		if ( $Script == 'Loxense-ActifsPrimordiaux.php' ) $Options[ 'actif'] = 1;

		$Titres_Nav[] = $Options;
	}


	if ( isset( $Options[ 'actif'] ) ) unset( $Options[ 'actif'] );


	if ( isset( $Permissions[ 'Loxense-ActifsSupports.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-ActifsSupports.php';
		$Options[ 'libelle' ] = $L_Gestion_Actifs_Supports;

		if ( $Script == 'Loxense-ActifsSupports.php' ) $Options[ 'actif'] = 1;

		$Titres_Nav[] = $Options;
	}
	
	
/*	if ( isset( $Permissions[ 'Loxense-ReferentielActifsSupports.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-ReferentielActifsSupports.php';
		$Options[ 'libelle' ] = $L_Gestion_Referentiel_Actifs_Supports;
		
		if ( $Script == 'Loxense-ReferentielActifsSupports.php' ) $Options[ 'actif'] = 1;
		
		$Titres_Nav[] = $Options;
	}*/
	
	return $Titres_Nav;	
}


public function prepareTitreMenuActifsTags( $Permissions, $Script ) {
	/**
	* prépare la barre de menu relative à la gestion des Actifs.
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2016-07-20
	*
	* @param[in] $permissions Liste des permissions de l'utilisateur courant.
	* @param[in] $script Nom du script courant.
	*
	* @return Renvoi un tableau formaté.
	*/

	// Récupère les libellés relatifs à la langue courante.
	include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php' );
	include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_Loxense-CartographiesRisques.php' );

	// Ignore le '/' en début de chaîne.
	$Script = mb_substr( $Script, 1 );

	$Titres_Nav = array();

	if ( isset( $Permissions[ 'Loxense-ActifsPrimordiauxTags.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-ActifsPrimordiauxTags.php';
		$Options[ 'libelle' ] = $L_Gestion_Actifs_Primordiaux;

		if ( $Script == 'Loxense-ActifsPrimordiauxTags.php' ) $Options[ 'actif'] = 1;

		$Titres_Nav[] = $Options;
	}

	if ( isset( $Options[ 'actif'] ) ) unset( $Options[ 'actif'] );


	if ( isset( $Permissions[ 'Loxense-ActifsSupportsTags.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-ActifsSupportsTags.php';
		$Options[ 'libelle' ] = $L_Gestion_Actifs_Supports;

		if ( $Script == 'Loxense-ActifsSupportsTags.php' ) $Options[ 'actif'] = 1;

		$Titres_Nav[] = $Options;
	}


	if ( isset( $Options[ 'actif'] ) ) unset( $Options[ 'actif'] );


	if ( isset( $Permissions[ 'Loxense-AppreciationRisquesTags.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-AppreciationRisquesTags.php';
		$Options[ 'libelle' ] = $L_Gestion_Appreciation_Risques;

		if ( $Script == 'Loxense-AppreciationRisquesTags.php' ) $Options[ 'actif'] = 1;

		$Titres_Nav[] = $Options;
	}


	if ( isset( $Options[ 'actif'] ) ) unset( $Options[ 'actif'] );


	if ( isset( $Permissions[ 'Loxense-TraitementRisquesTags.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-TraitementRisquesTags.php';
		$Options[ 'libelle' ] = $L_Gestion_Traitement_Risques;

		if ( $Script == 'Loxense-TraitementRisquesTags.php' ) $Options[ 'actif'] = 1;

		$Titres_Nav[] = $Options;
	}

	return $Titres_Nav;	
}


public function prepareTitreMenuEvenements( $Permissions, $Script ) {
	/**
	* prépare la barre de menu relative à la gestion des Evénements Redoutés.
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2016-07-20
	*
	* @param[in] $permissions Liste des permissions de l'utilisateur courant.
	* @param[in] $script Nom du script courant.
	*
	* @return Renvoi un tableau formaté.
	*/

	// Récupère les libellés relatifs à la langue courante.
	include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php' );
	include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_Loxense-EvenementsRedoutes.php' );

	// Ignore le '/' en début de chaîne.
	$Script = mb_substr( $Script, 1 );

	$Titres_Nav = array();

	if ( isset( $Permissions[ 'Loxense-EvenementsRedoutes.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-EvenementsRedoutes.php';
		$Options[ 'libelle' ] = $L_Gestion_Evenements_Redoutes;

		if ( $Script == 'Loxense-EvenementsRedoutes.php' ) $Options[ 'actif'] = 1;

		$Titres_Nav[] = $Options;
	}

	if ( isset( $Options[ 'actif'] ) ) unset( $Options[ 'actif'] );

	if ( isset( $Permissions[ 'Loxense-SourcesMenaces.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-SourcesMenaces.php';
		$Options[ 'libelle' ] = $L_Gestion_Sources_Menaces;
		
		if ( $Script == 'Loxense-SourcesMenaces.php' ) $Options[ 'actif'] = 1;
		
		$Titres_Nav[] = $Options;
	}
	
	if ( isset( $Options[ 'actif'] ) ) unset( $Options[ 'actif'] );
	
	/*
	 if ( isset( $Permissions[ 'Loxense-PartiesPrenantes.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-PartiesPrenantes.php';
		$Options[ 'libelle' ] = $L_Gestion_Parties_Prenantes;
		
		if ( $Script == 'Loxense-PartiesPrenantes.php' ) $Options[ 'actif'] = 1;
		
		$Titres_Nav[] = $Options;
	}
	
	if ( isset( $Options[ 'actif'] ) ) unset( $Options[ 'actif'] );
*/
	
	return $Titres_Nav;	
}


public function prepareTitreMenuImportExport( $Permissions, $Script ) {
	/**
	* prépare la barre de menu relative à la gestion des Evénements Redoutés.
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2016-07-20
	*
	* @param[in] $permissions Liste des permissions de l'utilisateur courant.
	* @param[in] $script Nom du script courant.
	*
	* @return Renvoi un tableau formaté.
	*/

	// Récupère les libellés relatifs à la langue courante.
	include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php' );
//	include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_Loxense-EvenementsRedoutes.php' );

	// Ignore le '/' en début de chaîne.
	$Script = mb_substr( $Script, 1 );

	$Titres_Nav = array();

	if ( isset( $Permissions[ 'Loxense-ExportBase.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-ExportBase.php';
		$Options[ 'libelle' ] = $L_Gestion_ImportExport_Base;

		if ( $Script == 'Loxense-ExportBase.php' ) $Options[ 'actif'] = 1;

		$Titres_Nav[] = $Options;
	}

	//if ( isset( $Options[ 'actif'] ) ) unset( $Options[ 'actif'] );


	return $Titres_Nav;	
}


public function prepareTitreMenuRisque( $Permissions, $Script ) {
	/**
	* prépare la barre de menu relative à la gestion des Risques.
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2016-07-20
	*
	* @param[in] $permissions Liste des permissions de l'utilisateur courant.
	* @param[in] $script Nom du script courant.
	*
	* @return Renvoi un tableau formaté.
	*/

	// Récupère les libellés relatifs à la langue courante.
	include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php' );
	include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_Loxense-AppreciationRisques.php' );

	// Ignore le '/' en début de chaîne.
	$Script = mb_substr( $Script, 1 );

	$Titres_Nav = array();

	if ( isset( $Permissions[ 'Loxense-AppreciationRisques.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-AppreciationRisques.php';
		$Options[ 'libelle' ] = $L_Gestion_Appreciation_Risques;

		if ( $Script == 'Loxense-AppreciationRisques.php' ) $Options[ 'actif'] = 1;

		$Titres_Nav[] = $Options;
	}

	if ( isset( $Options[ 'actif'] ) ) unset( $Options[ 'actif'] );


	if ( isset( $Permissions[ 'Loxense-TraitementRisques.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-TraitementRisques.php';
		$Options[ 'libelle' ] = $L_Gestion_Traitement_Risques;

		if ( $Script == 'Loxense-TraitementRisques.php' ) $Options[ 'actif'] = 1;

		$Titres_Nav[] = $Options;
	}

	return $Titres_Nav;	
}


public function prepareTitreMenuAction( $Permissions, $Script ) {
	/**
	* prépare la barre de menu relative à la gestion des Actions.
	*
	* @license Loxense
	* @author Pierre-Luc MARY
	* @date 2017-04-29
	*
	* @param[in] $permissions Liste des permissions de l'utilisateur courant.
	* @param[in] $script Nom du script courant.
	*
	* @return Renvoi un tableau formaté.
	*/

	// Récupère les libellés relatifs à la langue courante.
	include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php' );
	include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_Loxense-Actions.php' );

	// Ignore le '/' en début de chaîne.
	$Script = mb_substr( $Script, 1 );

	$Titres_Nav = array();

	if ( isset( $Permissions[ 'Loxense-Actions.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-Actions.php';
		$Options[ 'libelle' ] = $L_Gestion_Actions;

		if ( $Script == 'Loxense-Actions.php' ) $Options[ 'actif'] = 1;

		$Titres_Nav[] = $Options;
	}

	if ( isset( $Options[ 'actif'] ) ) unset( $Options[ 'actif'] );


	if ( isset( $Permissions[ 'Loxense-EditionsActions.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-EditionsActions.php';
		$Options[ 'libelle' ] = $L_Edition_Actions;

		if ( $Script == 'Loxense-EditionsActions.php' ) $Options[ 'actif'] = 1;

		$Titres_Nav[] = $Options;
	}

	return $Titres_Nav;	
}


public function prepareTitreMenuConformite( $Permissions, $Script ) {
	/**
	 * prépare la barre de menu relative à la gestion de la Conformite.
	 *
	 * @license Loxense
	 * @author Pierre-Luc MARY
	 * @date 2019-08-11
	 *
	 * @param[in] $permissions Liste des permissions de l'utilisateur courant.
	 * @param[in] $script Nom du script courant.
	 *
	 * @return Renvoi un tableau formaté.
	 */
	
	// Récupère les libellés relatifs à la langue courante.
	include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php' );
	include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_Loxense-ReferentielsConformite.php' );
	
	// Ignore le '/' en début de chaîne.
	$Script = mb_substr( $Script, 1 );
	
	$Titres_Nav = array();
	
	if ( isset( $Permissions[ 'Loxense-Conformite.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-Conformite.php';
		$Options[ 'libelle' ] = $L_Gestion_Conformite;
		
		if ( $Script == 'Loxense-Conformite.php' ) $Options[ 'actif'] = 1;
		
		$Titres_Nav[] = $Options;
	}
	
/*	if ( isset( $Options[ 'actif'] ) ) unset( $Options[ 'actif'] );
	
	
	if ( isset( $Permissions[ 'Loxense-MatriceConformite.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-MatriceConformite.php';
		$Options[ 'libelle' ] = $L_Matrice_Conformite;
		
		if ( $Script == 'Loxense-MatriceConformite.php' ) $Options[ 'actif'] = 1;
		
		$Titres_Nav[] = $Options;
	} */
	
	if ( isset( $Options[ 'actif'] ) ) unset( $Options[ 'actif'] );
	
	
	if ( isset( $Permissions[ 'Loxense-EditionConformite.php' ] ) ) {
		$Options[ 'lien' ] = 'Loxense-EditionConformite.php';
		$Options[ 'libelle' ] = $L_Edition_Conformite;
		
		if ( $Script == 'Loxense-EditionConformite.php' ) $Options[ 'actif'] = 1;
		
		$Titres_Nav[] = $Options;
	}
	
	return $Titres_Nav;
}


function creerOccurrenceCorpsTableau( $Id, $Valeurs, $Format_Colonnes_Corps ) {
	include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_HBL_Generiques.inc.php' );
	include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php' );


	if ( isset( $Format_Colonnes_Corps['Actions'] ) ) {
		if ( ! array_key_exists('historique', $Format_Colonnes_Corps['Actions']['boutons'] ) ) {
			$Format_Colonnes_Corps['Actions']['boutons']['historique'] = FALSE;
		}

		if ( ! array_key_exists('visualiser', $Format_Colonnes_Corps['Actions']['boutons'] ) ) {
			$Format_Colonnes_Corps['Actions']['boutons']['visualiser'] = FALSE;
		}

		if ( ! array_key_exists('dupliquer', $Format_Colonnes_Corps['Actions']['boutons'] ) ) {
			$Format_Colonnes_Corps['Actions']['boutons']['dupliquer'] = FALSE;
		}

		if ( ! array_key_exists('modifier', $Format_Colonnes_Corps['Actions']['boutons'] ) ) {
			$Format_Colonnes_Corps['Actions']['boutons']['modifier'] = FALSE;
		}

		if ( ! array_key_exists('supprimer', $Format_Colonnes_Corps['Actions']['boutons'] ) ) {
			$Format_Colonnes_Corps['Actions']['boutons']['supprimer'] = FALSE;
		}

		if ( ! array_key_exists('supprimer_libelle', $Format_Colonnes_Corps['Actions']['boutons'] ) ) {
			$Format_Colonnes_Corps['Actions']['boutons']['supprimer_libelle'] = FALSE;
		}

		if ( ! array_key_exists('ignorer_risque', $Format_Colonnes_Corps['Actions']['boutons'] ) ) {
			$Format_Colonnes_Corps['Actions']['boutons']['ignorer_risque'] = FALSE;
		}

		if ( ! array_key_exists('generer', $Format_Colonnes_Corps['Actions']['boutons'] ) ) {
			$Format_Colonnes_Corps['Actions']['boutons']['generer'] = FALSE;
		}

		if ( ! array_key_exists('telecharger', $Format_Colonnes_Corps['Actions']['boutons'] ) ) {
			$Format_Colonnes_Corps['Actions']['boutons']['telecharger'] = FALSE;
		}

		if ( ! array_key_exists('telecharger_e', $Format_Colonnes_Corps['Actions']['boutons'] ) ) {
			$Format_Colonnes_Corps['Actions']['boutons']['telecharger_e'] = FALSE;
		}

		if ( ! array_key_exists('telecharger_w', $Format_Colonnes_Corps['Actions']['boutons'] ) ) {
			$Format_Colonnes_Corps['Actions']['boutons']['telecharger_w'] = FALSE;
		}

		if ( ! array_key_exists('exporter', $Format_Colonnes_Corps['Actions']['boutons'] ) ) {
			$Format_Colonnes_Corps['Actions']['boutons']['exporter'] = FALSE;
		}

		if ( ! array_key_exists('restaurer', $Format_Colonnes_Corps['Actions']['boutons'] ) ) {
			$Format_Colonnes_Corps['Actions']['boutons']['restaurer'] = FALSE;
		}

		if ( ! array_key_exists('imprimer', $Format_Colonnes_Corps['Actions']['boutons'] ) ) {
			$Format_Colonnes_Corps['Actions']['boutons']['imprimer'] = FALSE;
		}
	}
	
	$E_Id = $Format_Colonnes_Corps['Prefixe'] . '_' . $Id;

	$Format_Occurrences = '';
	if ( isset( $Format_Colonnes_Corps['Format_Occurrences'] ) ) {
		$Format_Occurrences = ' ' . $Format_Colonnes_Corps['Format_Occurrences'];
	}

	$Occurrence = '<div id="' . $E_Id . '" class="row liste' . $Format_Occurrences . '">';

	foreach ( $Format_Colonnes_Corps['Colonnes'] as $Colonne ) {
		if ( isset( $Colonne[ 'alignement' ] ) ) $tmpClass = ' '. $Colonne[ 'alignement' ];
		else $tmpClass = '';
		
		$Occurrence .= '<div class="col-lg-' . $Colonne['taille'] . $tmpClass . '" data-src="' . $Colonne['nom'] . '" ';

		if ( isset( $Colonne['type'] ) ) {
			$Type = $Colonne['type'];

			$Occurrence .= 'data-type="' . $Colonne['type'] . '" ';

			if ( $Type == 'select' or $Type == 'button' ) {
				if ( isset( $Colonne['liste'] ) ) $Occurrence .= 'data-liste="' . $Colonne['liste'] . '" ';
				if ( isset( $Colonne['fonction'] ) ) $Occurrence .= 'data-fonction="' . $Colonne['fonction'] . '" ';
			}

		} else $Type = '';

		if ( isset( $Colonne['type_input'] ) ) {
			$Occurrence .= ' data-type_input="' . $Colonne['type_input'] . '" ';
		}

		if ( isset( $Colonne['maximum'] ) ) {
			$Occurrence .= ' data-maximum="' . $Colonne['maximum'] . '" ';
		}

		if ( isset( $Colonne['casse'] ) ) {
			$Occurrence .= ' data-casse="' . $Colonne['casse'] . '" ';
		}

		if ( isset( $Colonne['lignes'] ) ) {
			$Occurrence .= ' data-lignes="' . $Colonne['lignes'] . '" ';
		}

		$Occurrence .= '>';

		$Affichage = '';
		
		if ( $Type != 'button' ) {
			if ( isset( $Colonne['affichage'] ) ) {
				if ( $Colonne['affichage'] == 'img' ) $Affichage = 'img';
			} 


			$Occurrence .= '<span';

			if ( isset( $Colonne['modifiable'] ) ) {
				if ( $Colonne['modifiable'] == 'oui' ) {
					$Occurrence .= ' class="modifiable" ' .
						'onClick="' . $Format_Colonnes_Corps['Fonction_Ouverture'] . '(event,\'' . $Colonne['nom'] . '\',\'' . $E_Id . '\');"';
				}
			}
		}


		if ( is_object($Valeurs) ) {
			$Valeur = $Valeurs->{$Colonne['nom']};
		} else {
			$Valeur = $Valeurs;
		}

		if ( $Affichage != 'img' ) {
			$Valeur = htmlspecialchars( $Valeur, ENT_QUOTES | ENT_HTML5 );
		}

		if ( $Type != 'button' ) {
			$Occurrence .= '>' . $Valeur . '</span>';
		} else {
			$Occurrence .= '<button class="btn btn-outline-secondary" type="button">' . $Valeur . '</button>';
		}

		$Occurrence .= '</div>';
	}

	if ( isset( $Format_Colonnes_Corps['Actions'] ) ) {
		$tmpActionClass = '';

		if ( isset($Format_Colonnes_Corps[ 'Actions' ][ 'affichage' ]) ) {
			if ( $Format_Colonnes_Corps[ 'Actions' ][ 'affichage' ] == 'cacher' ) $tmpActionClass = ' hide';
			elseif ( $Format_Colonnes_Corps[ 'Actions' ][ 'affichage' ] == 'invisible' ) $tmpActionClass = ' invisible';
			elseif ( $Format_Colonnes_Corps[ 'Actions' ][ 'affichage' ] == 'invisible-droit' ) $tmpActionClass = ' invisible text-end';
		}

		$Occurrence .= '<div class="btn-actions col-lg-' . $Format_Colonnes_Corps['Actions']['taille'] . $tmpActionClass . '">';

		if ( $Format_Colonnes_Corps['Actions']['boutons']['historique'] === TRUE ) {
			$Occurrence .= '<button class="btn btn-outline-secondary btn-sm btn-historique" data-id="' . $Id . '" title="' . $L_Consulter_Historique . '" type="button">' .
			'<i class="bi-clock"></i>' .
			'</button>';
		}

		$Occurrence .= ' ';

		if ( $Format_Colonnes_Corps['Actions']['boutons']['visualiser'] === TRUE ) {
			$Occurrence .= '<button class="btn btn-outline-secondary btn-sm btn-visualiser" data-id="' . $Id . '" title="' . $L_Visualiser . '" type="button">' .
			'<i class="bi-eye-fill"></i>' .
			'</button>';
		}

		$Occurrence .= ' ';

		if ( $Format_Colonnes_Corps['Actions']['boutons']['dupliquer'] === TRUE ) {
			$Occurrence .= '<button class="btn btn-outline-secondary btn-sm btn-dupliquer" data-id="' . $Id . '" title="' . $L_Dupliquer . '" type="button">' .
			'<i class="bi-files"></i>' .
			'</button>';
		}

		$Occurrence .= ' ';

		if ( $Format_Colonnes_Corps['Actions']['boutons']['modifier'] === TRUE ) {
			$Occurrence .= '<button class="btn btn-outline-secondary btn-sm btn-modifier" data-id="' . $Id . '" title="' . $L_Modify . '" type="button">' .
			'<i class="bi-pencil-fill"></i>' .
			'</button>';
		}

		$Occurrence .= ' ';

		if ( $Format_Colonnes_Corps['Actions']['boutons']['exporter'] === TRUE ) {
			$Occurrence .= '<button class="btn btn-outline-secondary btn-sm btn-exporter" data-id="' . $Id . '" title="' . $L_Exporter_Base . '" type="button">' .
			'<i class="bi-cloud-upload"></i>' . // import save-file
			'</button>';
		}

		$Occurrence .= ' ';

		if ( $Format_Colonnes_Corps['Actions']['boutons']['restaurer'] === TRUE ) {
			$Occurrence .= '<button class="btn btn-outline-secondary btn-sm btn-restaurer" data-id="' . $Id . '" title="' . $L_Restaurer_Base . '" type="button">' .
			'<i class="bi-box-arrow-in-down-right"></i>' . // import save-file
			'</button>';
		}

		$Occurrence .= ' ';

		if ( $Format_Colonnes_Corps['Actions']['boutons']['supprimer'] === TRUE ) {
			$Occurrence .= '<button class="btn btn-outline-secondary btn-sm btn-supprimer" data-id="' . $Id . '" title="' . $L_Delete . '" type="button">' .
			'<i class="bi-x-circle"></i>' .
			'</button>';
		}

		$Occurrence .= ' ';

		if ( $Format_Colonnes_Corps['Actions']['boutons']['supprimer_libelle'] === TRUE ) {
			$Occurrence .= '<button class="btn btn-outline-secondary btn-sm btn-supprimer_libelle" data-id="' . $Id . '" title="' . $L_Supprimer_Libelle . '" type="button">' .
			'<i class="bi-x-circle-fill"></i>' .
			'</button>';
		}

		$Occurrence .= ' ';

		if ( $Format_Colonnes_Corps['Actions']['boutons']['ignorer_risque'] === TRUE ) {
			$Occurrence .= '<button class="btn btn-outline-secondary btn-sm btn-ignorer_risque" data-id="' . $Id . '" title="' . $L_Ignorer_Risque . '" type="button">' .
			'<i class="bi-slash-circle"></i>' .
			'</button>';
		}

		$Occurrence .= ' ';

		if ( $Format_Colonnes_Corps['Actions']['boutons']['generer'] === TRUE ) {
			$Occurrence .= '<button class="btn btn-outline-secondary btn-sm btn-generer" data-id="' . $Id . '" title="' . $L_Generer_Impression . '" type="button">' .
			'<i class="bi-arrow-repeat"></i>' .
			'</button>';
		}

		$Occurrence .= ' ';

		if ( $Format_Colonnes_Corps['Actions']['boutons']['telecharger'] === TRUE ) {
			$Occurrence .= '<button class="btn btn-outline-secondary btn-sm btn-telecharger" data-id="' . $Id . '" title="' . $L_Telecharger_Impression . '" type="button">' .
			'<i class="bi-download"></i>' . // import save-file
			'</button>';
		}

		$Occurrence .= ' ';

		if ( $Format_Colonnes_Corps['Actions']['boutons']['telecharger_e'] === TRUE ) {
			$Occurrence .= '<button class="btn btn-outline-secondary btn-sm btn-telecharger-e" data-id="' . $Id . '" title="' . $L_Telecharger_Excel . '" type="button">' .
			'<img src="' . URL_IMAGES . '/Excel-2-icon.png" alt="Excel"/>' .
			'</button>';
		}

		$Occurrence .= ' ';

		if ( $Format_Colonnes_Corps['Actions']['boutons']['telecharger_w'] === TRUE ) {
			$Occurrence .= '<button class="btn btn-outline-secondary btn-sm btn-telecharger-w" data-id="' . $Id . '" title="' . $L_Telecharger_Word . '" type="button">' .
			'<img src="' . URL_IMAGES . '/Word-2-icon.png" alt="Word"/>' .
			'</button>';
		}

		$Occurrence .= ' ';

		if ( $Format_Colonnes_Corps['Actions']['boutons']['imprimer'] === TRUE ) {
			$Occurrence .= '<button class="btn btn-outline-secondary btn-sm btn-imprimer" data-id="' . $Id . '" title="' . $L_Imprimer . '" type="button">' .
			'<i class="bi-printer-fill"></i>' .
			'</button>';
		}

		$Occurrence .= '</div>';
	}

	$Occurrence .= '</div>';


	return $Occurrence;
}


public function construitBoutonCompteur( $Valeur ) {
	if ( $Valeur > 0 ) {
		$Icone = '<i class="bi-chevron-right"></i>';
	} else {
		$Icone = '<i class="bi-plus-circle-fill"></i>';
	}

	return '<button class="btn btn-outline-secondary btn-sm btn-compteur col-lg-6">' . $Valeur . '&nbsp;' . $Icone . '</button>';
}


public function construireCompteurListe( $Valeur ) {
	if ( $Valeur == 0 ) $HTML = '<span class="badge bg-secondary align-middle">' . $Valeur . '</span>';
	else $HTML = '<span class="badge bg-vert_normal align-middle">' . $Valeur . '</span>';

	return $HTML;
}


public function construireListeCartographieUtilisateur( $idn_id = '' ) {
	include_once( DIR_LIBRAIRIES . '/Class_CartographiesRisques_PDO.inc.php' );

	$objCartographies = new CartographiesRisques();

	if ( $idn_id == '' ) $idn_id = $_SESSION['idn_id'];

	$Options = '';
	$Compteur = 1;

	foreach( $objCartographies->listerCartographiesRisquesUtilisateurs( $idn_id ) as $_Cartographie )	{
		$Selection = '';

		if ( isset( $_SESSION['CARTOGRAPHIE_SEL'] ) ) {
			if ( $_Cartographie->crs_id == $_SESSION['CARTOGRAPHIE_SEL'] ) {
				$Selection = ' selected';
				$_SESSION['ENTITE_SEL'] = $_Cartographie->entite;
			}
		} elseif ( $Compteur == 1 ) {
			$Selection = ' selected';
			$_SESSION['CARTOGRAPHIE_SEL'] = $_Cartographie->crs_id;
			$_SESSION['ENTITE_SEL'] = $_Cartographie->entite;
		}

		$Options .= '<option value="' . $_Cartographie->crs_id . '" data-ent_id="' . $_Cartographie->entite . '"' . $Selection . '>' . $_Cartographie->ent_libelle .
			' - ' . $_Cartographie->crs_libelle . ' - ' . $_Cartographie->crs_version . '</option>';

		$Compteur += 1;
	}

	return $Options;
}


public function construireListeEntites( $idn_id ) {
	if ( $_SESSION['idn_super_admin'] ) {
		include_once( DIR_LIBRAIRIES . '/Class_HBL_Entites_PDO.inc.php' );

		$objEntites = new HBL_Entites();

		$_Entites = $objEntites->rechercherEntites();
	} else {
		include_once( DIR_LIBRAIRIES . '/Class_HBL_Identites_Entites_PDO.inc.php' );

		$objEntites = new HBL_Identites_Entites();

		$_Entites = $objEntites->rechercherEntitesIdentite( $idn_id );
	}

	$Options = '';
	$Ligne = 0;

	foreach( $_Entites as $_Entite )	{
		$Ligne += 1;
		$Selection = '';

		if ( isset( $_SESSION['ENTITE_SEL'] ) ) {
			if ( $_SESSION['ENTITE_SEL'] == $_Entite->ent_id ) $Selection = ' selected';
		} elseif ( $Ligne == 1 ) {
			$Selection = ' selected';
			$_SESSION['ENTITE_SEL'] = $_Entite->ent_id;
		}

		$Options .= '<option value="' . $_Entite->ent_id . '"' . $Selection . '>' . $_Entite->ent_libelle . '</option>';
	}

	return $Options;
}

} // Fin class HTML.

?>