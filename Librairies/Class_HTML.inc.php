<?php

// Charge les constantes du projet.
include_once 'Constants.inc.php';
include_once HBL_DIR_LIBRARIES . '/Class_HBL_Authentifications_PDO.inc.php';

class HTML extends HBL_Authentifications {
/**
* Cette classe gère l'affichage de certaines parties des écrans.
*
* \license Copyleft
* \author Pierre-Luc MARY
* \package MySecDash
* \date 2023-12-04
*
*/

public $Version_Outil; // Version de l'outil (précisé dans le constructeur)
public $Nom_Outil;
public $Nom_Outil_TXT;
public $Nom_Societe;
public $Nom_Outil_Continuity;


public function __construct() {
/**
* Charge les variables d'environnements
*
* \license Copyleft
* \author Pierre-Luc MARY
* \date 2015-07-22
*
*/
	$this->Version_Outil = '1.3-4'; // Version de l'outil
	
	$this->Nom_Outil = '<span style="color: #717D11;">My</span><span style="color: #C34A36;">Sec</span><span style="color: #44808A;">Dash</span>';
	$this->Nom_Outil_TXT = 'MySecDash';
	$this->Nom_Societe = 'Loxense';
	$this->Nom_Outil_Continuity = '<span style="color: #44808A;">My</span><span style="color: #717D11;">Continuity</span>';
	
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
* \license Copyleft
* \author Pierre-Luc MARY
* \date 2023-12-18
*
* \param[in] $Titre_Page Titre à afficher dans la fenêtre des navigateurs.
* \param[in] $Fichiers_JavaScript Script(s) Javascript spécifiques à appeler au démarrage de la page HTML.
* \param[in] $CSS_Minimal Flag pour l'appel ou non de la feuille de styles minimaliste.
* 
* \param[out] $Entete Objet HTML à intégrer dans la page.
*
* \return Retourne la chaîne d'entête d'une page HTML.
*/
	include DIR_LIBELLES . "/" . $_SESSION[ 'Language' ] . "_libelles_generiques.php";
	
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
	 "  <link type=\"image/svg+xml\" sizes=\"48x48\" href=\"favicon.svg\" rel=\"icon\">\n" .
	 "  <!-- Chargement des feuilles de styles -->\n" .
	 "  <link rel=\"stylesheet\" type=\"text/css\" href=\"" . URL_LIBRAIRIES . "/css/bootstrap-icons/font/bootstrap-icons.css\" media=\"screen\">\n" .
	 "  <link rel=\"stylesheet\" type=\"text/css\" href=\"" . URL_LIBRAIRIES . "/bootstrap-dist/css/bootstrap.min.css\" media=\"screen\">\n";
	
	switch ( $CSS_Minimal ) {
	 default:
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
		
	 case 3:
		$Entete .= "  <link rel=\"stylesheet\" type=\"text/css\" href=\"" . URL_LIBRAIRIES . "/css/MyContinuity.css\" media=\"screen\">\n" .
			'  <link rel="stylesheet" type="text/css" href="' . URL_LIBRAIRIES . '/css/bootstrap-colorselector.css" />' . "\n" .
			'  <link rel="stylesheet" type="text/css" href="' . URL_LIBRAIRIES . '/css/plmTree.css" media="screen">' . "\n" .
			'  <link rel="stylesheet" type="text/css" href="' . URL_LIBRAIRIES . '/summernote-dist/summernote-bs4.css" />' . "\n";
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
	 "   Parameters['URL_CHARTJS'] = '" . URL_CHARTJS . "';\n\n" .
	 "   Parameters['SCRIPT'] = '" . $_SERVER[ 'SCRIPT_NAME' ] . "';\n\n" .
	 "   Parameters['TravailEnCours'] = '" . $L_Travail_En_Cours . "';\n" .
	 "   Parameters['internal_timer_message']; // 'Timer' général pour l'affichage des messages.\n" .
	 "  </script>\n" .
	 " </head>\n\n" .
	 " <body>\n";

	return $Entete ;
}



public function construireNavbar( $Nom_Fichier_Logo = '' ) {
/**
* Standardisation de la barre de menu (options de menu et information sur l'utilisateur).
*
* \license Copyleft
* \author Pierre-Luc MARY
* \date 2023-12-18
*
* \param[in] $Nom_Fichier_Logo Nom du fichier image contenant le logo à utiliser.
*
* \param[out] $Barre_Menu Objet HTML représentant la barre de menu est à intégrer dans sa page HTML
*
*$Barre_Menu
* \return Retourne la chaîne standardisant l'affichage du menu principal.
*/
	include DIR_LIBELLES . "/" . $_SESSION[ 'Language' ] . "_libelles_generiques.php";

	$Permissions = $this->permissionsGroupees();

	$Barre_Menu = "  <nav class=\"navbar navbar-expand-lg fixed-top\">\n" .
		"   <div class=\"container-fluid\">\n";

	if ( file_exists( URL_IMAGES . '/' . $Nom_Fichier_Logo ) ) {
		$Nom_Fichier_Logo = URL_IMAGES . '/' . $Nom_Fichier_Logo;
	} else {
		$Nom_Fichier_Logo = URL_IMAGES . '/Logo-MySecDash.svg';
	}

	$Barre_Menu .= "    <a class=\"navbar-brand\" data-bs-toggle=\"offcanvas\" href=\"#offcanvasChangerUnivers\" role=\"button\" aria-controls=\"offcanvasChangerUnivers\"><img src=\"" . $Nom_Fichier_Logo . "\" alt=\"Logo\" height=\"25\" /></a>\n" .
		"     <button class=\"navbar-toggler btn-outline-secondary\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#navbarNavPrincipal\" aria-controls=\"navbarNavPrincipal\" aria-expanded=\"false\" aria-label=\"Toggle navigation\">\n" .
		"      <i class=\"bi-list\"></i>\n" .
		"     </button>\n" .
		"     <div class=\"collapse navbar-collapse\" id=\"navbarNavPrincipal\">\n" .
		"      <ul class=\"navbar-nav me-auto mb-2 mb-lg-0\">\n";

	// Contrôle si l'utilisateur a au moins accès à une option d'Admnisitration pour lui donner accès.
	if ( isset( $Permissions['MySecDash-Parametres.php'] )
	 || isset( $Permissions['MySecDash-ReferentielsConformite.php'] ) ) {
		$Acces_Administration = true;
		$Referentiel_Interne = true;
	} else {
		$Acces_Administration = false;
		$Referentiel_Interne = false;
	}

	if ( isset( $Permissions['MySecDash-Historiques.php'] )
	 || isset( $Permissions['MySecDash-ExportBase.php'] ) ) {
		$Acces_Administration = true;
	} else {
		$Acces_Administration = false;
	}

	if ( $_SESSION['idn_super_admin'] === true || $Acces_Administration === true ) {
		$Barre_Menu .= "       <li class=\"nav-item dropdown\">\n" .
			"        <a href=\"#\" class=\"nav-link dropdown-toggle\" id=\"navbarDropdownMenuAdmin\" data-bs-toggle=\"dropdown\" role=\"button\" aria-expanded=\"false\">" . $L_Administration . "</a>\n" .
			"        <ul class=\"dropdown-menu\" aria-labelledby=\"navbarDropdownMenuAdmin\">\n";
	}
	
	if ( $_SESSION['idn_super_admin'] === true || $Referentiel_Interne === true ) {
		$Barre_Menu .= "        <li class=\"dropdown-submenu\">\n" .
			"         <a href=\"#\" class=\"dropdown-item\">" . $L_Referentiel_Interne . "</a>\n" .
			"         <ul class=\"dropdown-menu\">\n";
	}

	if ( $_SESSION['idn_super_admin'] === true || isset( $Permissions['MySecDash-Parametres.php'] ) ) {
		$Barre_Menu .= "          <li><a href=\"" . URL_BASE . "/MySecDash-Parametres.php\" class=\"dropdown-item\">" . $L_Parametres_Base . "</a></li>\n";
	}

	if ( $_SESSION['idn_super_admin'] === true || $Referentiel_Interne === true ) {
		$Barre_Menu .= "         </ul>\n" .
			"        </li>\n";
	}

	if ( $_SESSION['idn_super_admin'] === true || $Control_Acces === true ) {
		$Barre_Menu .= "        <li class=\"dropdown-submenu\">\n" .
			"         <a href=\"" . URL_BASE . "/MySecDash-Utilisateurs.php\" class=\"dropdown-item\">" . $L_Controle_Acces . "</a>\n" .
			"         <ul class=\"dropdown-menu\">\n";
	}
	
	if ( $_SESSION['idn_super_admin'] === true || isset( $Permissions['MySecDash-Societes.php'] ) ) {
		$Barre_Menu .= "          <li><a href=\"" . URL_BASE . "/MySecDash-Societes.php\" class=\"dropdown-item\">" . $L_Societes . "</a></li>\n";
	}
	
	if ( $_SESSION['idn_super_admin'] === true || isset( $Permissions['MySecDash-Entites.php'] ) ) {
		$Barre_Menu .= "          <li><a href=\"" . URL_BASE . "/MySecDash-Entites.php\" class=\"dropdown-item\">" . $L_Entites . "</a></li>\n";
	}

	if ( $_SESSION['idn_super_admin'] === true || isset( $Permissions['MySecDash-Civilites.php'] ) ) {
		$Barre_Menu .= "          <li><a href=\"" . URL_BASE . "/MySecDash-Civilites.php\" class=\"dropdown-item\">" . $L_Civilites . "</a></li>\n";
	}

	if ( $_SESSION['idn_super_admin'] === true || isset( $Permissions['MySecDash-ApplicationsInternes.php'] ) ) {
		$Barre_Menu .= "          <li><a href=\"" . URL_BASE . "/MySecDash-ApplicationsInternes.php\" class=\"dropdown-item\">" . $L_ApplicationsInternes . "</a></li>\n";
	}

	if ( $_SESSION['idn_super_admin'] === true || isset( $Permissions['MySecDash-Profils.php'] ) ) {
		$Barre_Menu .= "          <li><a href=\"" . URL_BASE . "/MySecDash-Profils.php\" class=\"dropdown-item\">" . $L_Profils . "</a></li>\n";
	}

	if ( $_SESSION['idn_super_admin'] === true || isset( $Permissions['MySecDash-Utilisateurs.php'] ) ) {
		$Barre_Menu .= "          <li><a href=\"" . URL_BASE . "/MySecDash-Utilisateurs.php\" class=\"dropdown-item\">" . $L_Utilisateurs . "</a></li>\n";
	}

/*	if ( $_SESSION['idn_super_admin'] === true || isset( $Permissions['MySecDash-Gestionnaires.php'] ) ) {
		$Barre_Menu .= "          <li><a href=\"" . URL_BASE . "/MySecDash-Gestionnaires.php\" class=\"dropdown-item\">" . $L_Gestionnaires . "</a></li>\n";
	} */

	if ( $_SESSION['idn_super_admin'] === true || isset( $Permissions['MySecDash-Etiquettes.php'] ) ) {
		$Barre_Menu .= "          <li><a href=\"" . URL_BASE . "/MySecDash-Etiquettes.php\" class=\"dropdown-item\">" . $L_Etiquettes . "</a></li>\n";
	}

	if ( $_SESSION['idn_super_admin'] === true || $Control_Acces === true ) {
		$Barre_Menu .= "         </ul>\n" .
			"        </li>\n";
	}

	if ( $_SESSION['idn_super_admin'] === true || isset( $Permissions['MySecDash-Historiques.php'] ) ) {
		$Barre_Menu .= "        <li><hr class=\"dropdown-divider\"></li>\n" .
			"        <li><a href=\"" . URL_BASE . "/MySecDash-Historiques.php\" class=\"dropdown-item\">" . $L_Historique . "</a></li>\n";
	}

	if ( $_SESSION['idn_super_admin'] === true || isset( $Permissions['MySecDash-ExportBase.php'] ) ) {
		$Barre_Menu .= "        <li><hr class=\"dropdown-divider\"></li>\n" .
			"        <li><a href=\"" . URL_BASE . "/MySecDash-ExportBase.php\" class=\"dropdown-item\">" . $L_Export_Base . "</a></li>\n";
	}

	if ( $_SESSION['idn_super_admin'] === true || $Acces_Administration === true ) {
		$Barre_Menu .= "       </ul>\n" .
			"      </li>\n";
	}


	// ======


	if ( $_SESSION['idn_super_admin'] === true
	 || isset( $Permissions['MySecDash-Conformite.php']  )
	 || isset( $Permissions['MySecDash-EditionConformite.php']  )
	 || isset( $Permissions['MySecDash-MatriceConformite.php']  ) ) {
		$Option_Gestion_Conformite = true;
	} else {
		$Option_Gestion_Conformite = false;
	}


	if ( $_SESSION['idn_super_admin'] === true || $Option_Gestion_Conformite === true ) {
		$Barre_Menu .= "      <li class=\"nav-item dropdown\">\n" .
			"       <a href=\"#\" class=\"nav-link dropdown-toggle\" id=\"navbarDropdownMenuConfo\" data-bs-toggle=\"dropdown\" role=\"button\" aria-expanded=\"false\">" . $L_Gestion_Conformite . " <span class=\"caret\"></span></a>\n" .
			"       <ul class=\"dropdown-menu\" aria-labelledby=\"navbarDropdownMenuConfo\">\n";
	}

	if ( $_SESSION['idn_super_admin'] === true || isset( $Permissions['MySecDash-Conformite.php'] ) ) {
		$Barre_Menu .= "        <li><a href=\"" . URL_BASE . "/MySecDash-Conformite.php\" class=\"dropdown-item\">" . $L_Gerer_Conformite . "</a></li>\n";
	}
	
	if ( $_SESSION['idn_super_admin'] === true || isset( $Permissions['MySecDash-EditionConformite.php'] ) ) {
		$Barre_Menu .= "        <li><a href=\"" . URL_BASE . "/MySecDash-EditionConformite.php\" class=\"dropdown-item\">" . $L_Editer_Conformite . "</a></li>\n";
	}
	
/*	if ( $_SESSION['idn_super_admin'] === true || isset( $Permissions['MySecDash-MatriceConformite.php'] ) ) {
		$Barre_Menu .= "        <li><a href=\"" . URL_BASE . "/MySecDash-MatriceConformite.php\" class=\"dropdown-item\">" . $L_Matrice_Conformite . "</a></li>\n";
	} */

	
	if ( $_SESSION['idn_super_admin'] === true || $Option_Gestion_Conformite === true ) {
			$Barre_Menu .= "       </ul>\n" .
			"      </li>\n";
	}


	// ======


	if ( $_SESSION['idn_super_admin'] === true
	 || isset( $Permissions['MySecDash-Actions.php'] )
	 || isset( $Permissions['MySecDash-EditionsActions.php'] )) {
		$Option_Gestion_Actions = true;
	} else {
		$Option_Gestion_Actions = false;
	}


	if ( $_SESSION['idn_super_admin'] === true || $Option_Gestion_Actions === true ) {
		$Barre_Menu .= "      <li class=\"nav-item dropdown\">\n" .
			"       <a href=\"#\" class=\"nav-link dropdown-toggle\" id=\"navbarDropdownMenuAction\" data-bs-toggle=\"dropdown\" role=\"button\" aria-expanded=\"false\">" . $L_Gestion_Actions . " <span class=\"caret\"></span></a>\n" .
			"       <ul class=\"dropdown-menu\" aria-labelledby=\"navbarDropdownMenuAction\">\n";
	}

	if ( $_SESSION['idn_super_admin'] === true || isset( $Permissions['MySecDash-Actions.php'] ) ) {
		$Barre_Menu .= "        <li><a href=\"" . URL_BASE . "/MySecDash-Actions.php\" class=\"dropdown-item\">" . $L_Gerer_Actions . "</a></li>\n";
	}

	if ( $_SESSION['idn_super_admin'] === true || isset( $Permissions['MySecDash-EditionsActions.php'] ) ) {
		$Barre_Menu .= "        <li><a href=\"" . URL_BASE . "/MySecDash-EditionsActions.php\" class=\"dropdown-item\">" . $L_Editer_Actions . "</a></li>\n";
	}

	
	if ( $_SESSION['idn_super_admin'] === true || $Option_Gestion_Actions === true ) {
		$Barre_Menu .= "       </ul>\n" .
			"      </li>\n";
	}


	// ======

/*
	if ( $_SESSION['idn_super_admin'] === true
		or isset( $Permissions['MySecDash-ActifsPrimordiauxTags.php'] )
		or isset( $Permissions['MySecDash-ActifsSupportsTags.php'] )
		or isset( $Permissions['MySecDash-AppreciationRisquesTags.php'] )
		or isset( $Permissions['MySecDash-TraitementRisquesTags.php'] )
		) {
		$Option_Gestion_Tags = true;
	} else {
		$Option_Gestion_Tags = false;
	}


	if ( $_SESSION['idn_super_admin'] === true or $Option_Gestion_Tags === true ) {
		$Barre_Menu .= "      <li class=\"nav-item dropdown\">\n" .
			"       <a href=\"#\" class=\"nav-link dropdown-toggle\" id=\"navbarDropdownMenuTags\" data-bs-toggle=\"dropdown\" role=\"button\" aria-expanded=\"false\">" . $L_Vision_Consolidee . " <span class=\"caret\"></span></a>\n" .
			"       <ul class=\"dropdown-menu\" aria-labelledby=\"navbarDropdownMenuTags\">\n";
	}

	if ( $_SESSION['idn_super_admin'] === true or isset( $Permissions['MySecDash-ActifsPrimordiauxTags.php'] ) ) {
		$Barre_Menu .= "        <li><a href=\"" . URL_BASE . "/MySecDash-ActifsPrimordiauxTags.php\" class=\"dropdown-item\">" . $L_Actifs_Primordiaux . "</a></li>\n";

		$Barre_Separation = true;
	}

	if ( $_SESSION['idn_super_admin'] === true or isset( $Permissions['MySecDash-ActifsSupportsTags.php'] ) ) {
		$Barre_Menu .= "        <li><a href=\"" . URL_BASE . "/MySecDash-ActifsSupportsTags.php\" class=\"dropdown-item\">" . $L_Actifs_Supports . "</a></li>\n";

		$Barre_Separation = true;
	}

	if ( $Barre_Separation == true ) $Barre_Menu .= "		<li><hr class=\"dropdown-divider\"></li>\n";

	if ( $_SESSION['idn_super_admin'] === true or isset( $Permissions['MySecDash-AppreciationRisquesTags.php'] ) ) {
		$Barre_Menu .= "          <li><a href=\"" . URL_BASE . "/MySecDash-AppreciationRisquesTags.php\" class=\"dropdown-item\">" . $L_Appreciation_Risques . "</a></li>\n";

		$Barre_Separation = false;
	}

	if ( $_SESSION['idn_super_admin'] === true or isset( $Permissions['MySecDash-TraitementRisquesTags.php'] ) ) {
		$Barre_Menu .= "          <li><a href=\"" . URL_BASE . "/MySecDash-TraitementRisquesTags.php\" class=\"dropdown-item\">" . $L_Traitement_Risques . "</a></li>\n";

		$Barre_Separation = false;
	}

	if ( $_SESSION['idn_super_admin'] === true or $Option_Gestion_Tags === true ) {
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

		"  <div class=\"offcanvas offcanvas-end\" tabindex=\"-1\" id=\"offcanvasDetailUtilisateur\" aria-labelledby=\"offcanvasChangerUnivers\">\n" .
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
		"    <button id=\"chgMdP\" type=\"button\" class=\"btn btn-outline-secondary btn-sm\">" . $L_Changer_Mot_Passe_Obligatoire . "</button></span></p>\n" .
		"   </div> <!-- /.offcanvas-body -->\n" .
		"  </div> <!-- /.offcanvas -->\n" .

		"  <div class=\"offcanvas offcanvas-start\" tabindex=\"-1\" id=\"offcanvasChangerUnivers\" aria-labelledby=\"offcanvasChangerUnivers\">\n" .
		"   <div class=\"offcanvas-header\">\n" .
		"    <h5 class=\"offcanvas-title fg_couleur_2\" id=\"offcanvasExampleLabel\">" . $L_Changement_Univers . "</h5>\n" .
		"    <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"offcanvas\" aria-label=\"Close\"></button>\n" .
		"   </div> <!-- /.offcanvas-header -->\n" .
		"   <div class=\"offcanvas-body\" id=\"corps_tableau_univers\">\n" .
		"    <div class=\"row liste mysecdash\">\n" .
		"     <div class=\"col-2 align-middle text-center\"><img src=\"" . URL_IMAGES . "/Logo-MySecDash.svg\" width=\"50\" class=\"mx-auto\"></div>\n" .
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



public function construireNavbarJson( $Nom_Fichier_Logo = '', $Nom_Fichier_Json = '' ) {
	/**
	 * Standardisation de la barre de menu (options de menu et information sur l'utilisateur) et utilisant
	 * un fichier JSON.
	 *
	 * \license Copyleft
	 * \author Pierre-Luc MARY
	 * \date 2024-01-09
	 *
	 * \param[in] $Nom_Fichier_Logo Nom du fichier image contenant le logo à utiliser.
	 * \param[in] $Nom_Fichier_Json Nom du fichier Json qui décrit les options de la barre de menu.
	 *
	 * \param[out] $Barre_Menu Objet HTML représentant la barre de menu est à intégrer dans sa page HTML
	 *
	 *$Barre_Menu
	 * \return Retourne la chaîne standardisant l'affichage du menu principal.
	 */
	include DIR_LIBELLES . "/" . $_SESSION[ 'Language' ] . "_libelles_generiques.php";
	
	$Permissions = $this->permissionsGroupees();
	
	$Barre_Menu = "  <nav class=\"navbar navbar-expand-lg fixed-top\">\n" .
		"   <div class=\"container-fluid\">\n";
	
	if ( file_exists( DIR_IMAGES . '/' . $Nom_Fichier_Logo ) ) {
		$Nom_Fichier_Logo = URL_IMAGES . '/' . $Nom_Fichier_Logo;
	} else {
		$Nom_Fichier_Logo = URL_IMAGES . '/Logo-MySecDash.svg';
	}
	
	// Partie invariante de la barre de menu.
	$Barre_Menu .= "    <a class=\"navbar-brand\" data-bs-toggle=\"offcanvas\" href=\"#offcanvasChangerUnivers\" role=\"button\" aria-controls=\"offcanvasChangerUnivers\"><img src=\"" . $Nom_Fichier_Logo . "\" alt=\"Logo\" height=\"25\" /></a>\n" .
		"     <button class=\"navbar-toggler btn-outline-secondary\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#navbarNavPrincipal\" aria-controls=\"navbarNavPrincipal\" aria-expanded=\"false\" aria-label=\"Toggle navigation\">\n" .
		"      <i class=\"bi-list\"></i>\n" .
		"     </button>\n" .
		"     <div class=\"collapse navbar-collapse\" id=\"navbarNavPrincipal\">\n" .
		"      <ul class=\"navbar-nav me-auto mb-2 mb-lg-0\">\n";
	
	// Contrôle si l'utilisateur a au moins accès à une option d'Admnisitration pour lui donner accès.
//	if ( isset( $Permissions['MySecDash-Parametres.php'] )
	$Flux_JSON = json_decode( file_get_contents( CHEMIN_APPLICATION . DIRECTORY_SEPARATOR . $Nom_Fichier_Json ) );
	$Barre_Menu .= $this->construireBarreMenu( $Flux_JSON );
	

	// Partie invariante de la barre de menu.
	$Barre_Menu .= "     </ul>\n" .
		"     <form id=\"info_utilisateur\" class=\"d-flex\">\n" .
		"      <span id=\"nom_utilisateur\"><i class=\"bi-person-fill\"></i>&nbsp;" . $_SESSION[ 'cvl_prenom' ] . " " . $_SESSION[ 'cvl_nom' ] . "</span>\n" .
		"      <button id=\"code_utilisateur\" tabindex=\"0\" type=\"button\" class=\"btn btn-outline-secondary btn-sm\" data-bs-toggle=\"offcanvas\" data-bs-target=\"#offcanvasDetailUtilisateur\" aria-controls=\"offcanvasDetailUtilisateur\">" . $_SESSION[ 'idn_login' ] . "</button>\n" .
		"      <span>" . $L_ExpireDans . "</span>\n" .
		"      <button id=\"temps_session\" type=\"button\" class=\"btn btn-outline-secondary btn-sm\">" . $this->recupererParametre( 'expiration_time' ) . "</button>\n" .
		"      <span>mn</span>\n" .
		"     </form>\n" .
		"    </div><!-- /#main-menu -->\n" .
		"   </div><!-- /.container-fluid -->\n" .
		"  </nav>\n\n" .

		"  <div class=\"offcanvas offcanvas-end\" tabindex=\"-1\" id=\"offcanvasDetailUtilisateur\" aria-labelledby=\"offcanvasChangerUnivers\">\n" .
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
		"    <button id=\"chgMdP\" type=\"button\" class=\"btn btn-outline-secondary btn-sm\">" . $L_Changer_Mot_Passe_Obligatoire . "</button></span></p>\n" .
		"   </div> <!-- /.offcanvas-body -->\n" .
		"  </div> <!-- /.offcanvas -->\n" .

		"  <div class=\"offcanvas offcanvas-start\" tabindex=\"-1\" id=\"offcanvasChangerUnivers\" aria-labelledby=\"offcanvasChangerUnivers\">\n" .
		"   <div class=\"offcanvas-header\">\n" .
		"    <h5 class=\"offcanvas-title fg_couleur_2\" id=\"offcanvasExampleLabel\">" . $L_Changement_Univers . "</h5>\n" .
		"    <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"offcanvas\" aria-label=\"Close\"></button>\n" .
		"   </div> <!-- /.offcanvas-header -->\n" .
		"   <div class=\"offcanvas-body\" id=\"corps_tableau_univers\">\n" .
		"    <div class=\"row liste mysecdash\">\n" .
		"     <div class=\"col-2 align-middle text-center\"><img src=\"" . URL_IMAGES . "/Logo-MySecDash.svg\" width=\"50\" class=\"mx-auto\"></div>\n" .
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



public function traiterItemPrincipaleMenu($Item) {
	include DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php';

	$Barre_Menu = '';
	
	foreach($Item as $Objet => $Options) {
		if ( isset(${$Options->LibelleItemPrincipale}) ) {
			$Options->LibelleItemPrincipale = ${$Options->LibelleItemPrincipale};
		}

		$Barre_Menu .= "       <li class=\"nav-item dropdown\">\n" .
			"        <a href=\"#\" class=\"nav-link dropdown-toggle\" id=\"navbarDropdownMenuAdmin\" data-bs-toggle=\"dropdown\" role=\"button\" aria-expanded=\"false\">" . $Options->LibelleItemPrincipale . "</a>\n" .
			"        <ul class=\"dropdown-menu\" aria-labelledby=\"navbarDropdownMenuAdmin\">\n";
		
		foreach($Options->Items as $Option) {
			if ($Option->Type == 'option') {
				$Barre_Menu .= $this->traiterOptionMenu($Option);
			} elseif ($Option->Type == 'sous-menu') {
				$Barre_Menu .= $this->traiterSousMenu($Option);
			} elseif ($Option->Type == 'separator') {
				$Barre_Menu .= "        <li><hr class=\"dropdown-divider\"></li>\n";
			} else {
				print 'Erreur : type d\'option de menu iconnu ['.$Option->Type.']';
				exit();
			}
		}

		$Barre_Menu .= "        </ul>\n" .
			"       </li>\n";
	}
	
	return $Barre_Menu;
}



public function traiterOptionMenu($Option) {
	include DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php';

	if ( isset(${$Option->LibelleItem}) ) {
		$Option->LibelleItem = ${$Option->LibelleItem};
	}


	if ( $this->controlerPermission($Option->Lien, 'RGH_1') === false ) {
		return "          <li><a class=\"dropdown-item disabled\" >" . $Option->LibelleItem . "</a></li>\n";
	} else {
		return "          <li><a href=\"" . URL_BASE . "/" . $Option->Lien . "\" class=\"dropdown-item\">" . $Option->LibelleItem . "</a></li>\n";
	}
}



public function traiterSousMenu($Option) {
	include DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php';

	if ( isset(${$Option->LibelleItem}) ) {
		$Option->LibelleItem = ${$Option->LibelleItem};
	}

	$Barre_Menu = "        <li class=\"dropdown-submenu\">\n" .
		"         <a href=\"#\" class=\"dropdown-item\">" . $Option->LibelleItem . "</a>\n" .
		"         <ul class=\"dropdown-menu\">\n";

	foreach($Option->Items as $Option_SM) {
		if ($Option_SM->Type == 'option') {
			$Barre_Menu .= $this->traiterOptionMenu($Option_SM);
		} elseif ($Option_SM->Type == 'sous-menu') {
			$Barre_Menu .= $this->traiterSousMenu($Option_SM);
		} elseif ($Option_SM->Type == 'separator') {
			$Barre_Menu .= "        <li><hr class=\"dropdown-divider\"></li>\n";
		} else {
			print 'Erreur : type d\'option de menu iconnu ['.$Option_SM->Type.']';
			exit();
		}
	}
	
	return $Barre_Menu .= "         </ul>\n" .
		"        </li>\n";
}

public function construireBarreMenu( $Flux_JSON = '' ) {
	// Recherche la bonne racine pour valider à minima que l'on traite le bon flux JSON.
	if ($Flux_JSON == '') return 'JSON file unavailable';

	foreach($Flux_JSON as $Objet => $Menu) {
		if ($Objet == 'ItemsPrincipales') {
			return $this->traiterItemPrincipaleMenu($Flux_JSON->$Objet);
		} else {
			return 'Objet "ItemsPrincipales" attendu ("' . $Objet . '" trouvé)';
		}
	}
}



public function construireTitreEcran( $Titre_Ecran, $Societes_Autorisees = [], $Boutons_Alternatifs = [], 
	$Options_Titre_1 = '', $Onglets = '', $Options_Titre_2 = '', $Options_Titre_3 = '' ) {
/**
* Standardisation du titre et des informations contextuelles des écrans.
*
* \license Copyleft
* \author Pierre-Luc MARY
* \date 2023-12-18
*
* \param[in] $Titre_Ecran Titre de l'écran courant.
* \param[in] $Societes_Autorisees Liste des sociétés autorisées pour l'utilisateur (si plusieurs sociétés, on affiche une Dropdownlist pour pouvoir changer, sinon on affiche simplement l'information).
* \param[in] $Boutons_Alternatifs Permet l'affichage de boutons alternatifs (juste à droite du titre).
* \param[in] $Options_Titre_1 Permet d'afficher une liste (en fonction du contexte).
*
* \param[out] $Objet_Titre_Ecran Objet HTML représentant la barre de titre et ses éventuelles options est à intégrer dans sa page HTML
*
* \return Retourne la chaîne standardisant l'affichage du menu contextuel (sous forme de liste déroulante).
*/
	include DIR_LIBELLES . "/" . $_SESSION[ 'Language' ] . "_libelles_generiques.php";


	$Objet_Titre_Ecran = "  <!-- === Zone : titre de l'écran === -->\n" .
		"  <div class=\"container-fluid\" id=\"titre_ecran\">\n" .
		"  <div class=\"row\">\n";


	if ( $Societes_Autorisees != [] and $Societes_Autorisees != '' ) {
		$Objet_Titre_Ecran .= "   <div class=\"col-lg-6 mb-2\">\n" .
			"    <div class=\"input-group input-group-sm\">\n" .
			"     <span class=\"input-group-text\">" . $L_Societe . "</span>\n";

		if ( ! isset($_SESSION['s_sct_id']) || $_SESSION['s_sct_id'] == '' || $_SESSION['s_sct_id'] == '---' ) {
			$_SESSION['s_sct_id'] = $Societes_Autorisees[0]->sct_id;
		}

		switch ( count($Societes_Autorisees) ) {
		 default:
			$Objet_Titre_Ecran .= "     <select id=\"s_sct_id\" class=\"form-select form-select-sm gris\">\n";

			foreach( $Societes_Autorisees as $Societe_Autorisee) {
				$Defaut = '';
				if ( isset( $_SESSION['s_sct_id'] ) ) {
					if ( $_SESSION['s_sct_id'] == $Societe_Autorisee->sct_id ) {
						$Defaut = ' selected ';
					}
				}

				$Objet_Titre_Ecran .= "      <option value=\"" . $Societe_Autorisee->sct_id . "\"" . $Defaut . ">" . $Societe_Autorisee->sct_nom . "</option>\n";
			}

			$Objet_Titre_Ecran .= "     </select>\n";

			break;

		 case 1:
			$Objet_Titre_Ecran .= "     <input type=\"text\" id=\"s_sct_id\" class=\"form-control gris\" data-value=\"" . $Societes_Autorisees[0]->sct_id . "\" value=\"" . $Societes_Autorisees[0]->sct_nom . "\" disabled>\n";
			
			break;

		 case 0:
			$Objet_Titre_Ecran .= "     <input type=\"text\" id=\"s_sct_id\" class=\"form-control gris\" data-value=\"\" value=\"---\" disabled>\n";
			
			break;
		}

		$Objet_Titre_Ecran .= "    </div> <!-- input-group -->\n" .
			"   </div> <!-- .col-lg-6 -->\n\n";
	}

	if ( $Options_Titre_1 != '' and $Options_Titre_1 != [] ) {
		$Objet_Titre_Ecran .= "   <div class=\"col-lg-6\">\n" .
			"    <div class=\"input-group  input-group-sm\">\n" .
			"    <span class=\"input-group-text\">" . $Options_Titre_1['libelle'] . "</span>\n";

		$tmpOptions = '';

		$Objet_Titre_Ecran .= "     <select id=\"" . $Options_Titre_1['id'] . "\" class=\"form-select form-select-sm gris\">\n";
		if ( isset($Options_Titre_1['options']) ) {
			if (!isset($_SESSION[$Options_Titre_1['id']]) || $_SESSION[$Options_Titre_1['id']] == '---') {
				$_SESSION[$Options_Titre_1['id']] = $Options_Titre_1['options'][0]['id'];
			}
			
			foreach( $Options_Titre_1['options'] as $Options_Titre_1 ) {
				$Infos_Complementaires = '';
				
				if ( array_key_exists('infos',$Options_Titre_1) ) {
					foreach( $Options_Titre_1['infos'] as $Info ) {
						$Infos_Complementaires .= " data-" . $Info['nom'] . "=\"" . $Info['valeur'] . "\"";
					}
				}
				
				$tmpOptions .= "      <option value=\"". $Options_Titre_1['id'] . "\"" . $Infos_Complementaires . ">". $Options_Titre_1['nom'] . "</option>\n";
			}

			$Objet_Titre_Ecran .= $tmpOptions;
		} else {
			$Objet_Titre_Ecran .= "     <option value=\"\">---</option>\n";
		}

		$Objet_Titre_Ecran .= "     </select>\n" .
			"    </div> <!-- .input-group -->\n" .
			"   </div> <!-- .col-lg-6 -->\n";
	}


	$Objet_Titre_Ecran .= "   <div id=\"titre-menu\" class=\"col-lg-7\">\n" .
		"    <div class=\"input-group input-group-lg\">\n" .
		"     <span class=\"input-group-text libelle-titre-menu\">" . $Titre_Ecran . "</span>\n";
	
	if ( $Boutons_Alternatifs != [] and $Boutons_Alternatifs != '' ) {
		foreach ($Boutons_Alternatifs as $Bouton_Alternatif) {
			$Objet_Titre_Ecran .= '     <button class="btn btn-outline-secondary ' . $Bouton_Alternatif['class'] . '" type="button" title="' . $Bouton_Alternatif['libelle'] . '">' .
				'<i class="bi-' . $Bouton_Alternatif['glyph'] . '"></i>' .
				"</button>\n";
		}
	}

	$Objet_Titre_Ecran .= "    </div> <!-- input-group -->\n" .
		"   </div> <!-- #titre-menu -->\n\n";

	$tmpOptions = '';

	if ( $Options_Titre_2 != '' and $Options_Titre_2 != [] ) {
		$Objet_Titre_Ecran .= "   <div id=\"option-titre-menu\" class=\"col-lg-5 mt-1\">\n" .
			"    <div class=\"input-group col-lg-5\">\n" .
			"    <span class=\"input-group-text libelle-alternative-titre-menu\">" . $Options_Titre_2['libelle'] . "</span>\n" .
			"     <select id=\"" . $Options_Titre_2['id'] . "\" class=\"form-select\">\n";

		if (isset($Options_Titre_2['options'])) {
			if (!isset($_SESSION[$Options_Titre_2['id']]) || $_SESSION[$Options_Titre_2['id']] == '---') {
				$_SESSION[$Options_Titre_2['id']] = $Options_Titre_2['options'][0]['id'];
			}

			switch( count($Options_Titre_2['options']) ) {
				default:
					foreach( $Options_Titre_2['options'] as $Option_Titre_2 ) {
						$Infos_Complementaires = '';
						
						if ( array_key_exists('infos',$Option_Titre_2) ) {
							foreach( $Option_Titre_2['infos'] as $Info ) {
								$Infos_Complementaires .= " data-" . $Info['nom'] . "=\"" . $Info['valeur'] . "\"";
							}
						}
						if ( isset($_SESSION[$Options_Titre_2['id']]) ) {
							if ( $_SESSION[$Options_Titre_2['id']] == $Option_Titre_2['id'] ) {
								$Opt_Defaut = ' selected';
							} else {
								$Opt_Defaut = '';
							}
						}
						$tmpOptions .= "      <option value=\"". $Option_Titre_2['id'] . "\"" . $Infos_Complementaires . $Opt_Defaut . ">". $Option_Titre_2['nom'] . "</option>\n";
					}

					break;

				case 0:
					$tmpOptions .= "      <option value=\"\">---</option>\n";

					break;
			}
		} else {
			$tmpOptions .= "      <option value=\"\">---</option>\n";
		}

		$Objet_Titre_Ecran .= $tmpOptions .
			"     </select>\n" .
			"    </div> <!-- .input-group -->\n" .
			"   </div> <!-- #option-titre-menu 2 -->\n";
	}

	if ( $Options_Titre_3 != '' and $Options_Titre_3 != [] ) {
		$Objet_Titre_Ecran .= "   <div id=\"option-titre-menu\" class=\"col-lg-5 mt-1\">\n" .
			"    <div class=\"input-group col-lg-5\">\n" .
			"    <span class=\"input-group-text\">" . $Options_Titre_3['libelle'] . "</span>\n" .
			"     <input id=\"" . $Options_Titre_3['id'] . "\" class=\"form-control\">\n" .
			"    </div> <!-- .input-group -->\n" .
			"   </div> <!-- #option-titre-menu 3 -->\n";
	}

	$Objet_Titre_Ecran .= "   </div> <!-- .row -->\n";
	
	if ( $Onglets != '' ) {
		$Objet_Titre_Ecran .= $Onglets;
	}
	
	$Objet_Titre_Ecran .= "  </div> <!-- /#titre_ecran -->\n";

	return $Objet_Titre_Ecran;
}



public function construireDebutEnteteTableau() {
/**
* Standardisation du début de l'entête du tableau central.
*
* \license Copyleft
* \author Pierre-Luc MARY
* \date 2015-07-23
*
* \return Retourne la chaîne à afficher.
*/

	$Texte = "  <!-- === Zone : entête du tableau central === -->\n" .
		"  <div class=\"container-fluid\" id=\"entete_tableau\">\n";

	return $Texte;
}



public function construireDebutCorpsTableau() {
/**
* Standardisation du début du corps du tableau central.
*
* \license Copyleft
* \author Pierre-Luc MARY
* \date 2015-07-23
*
* \return Retourne la chaîne à afficher.
*/

	$Texte = "  <!-- === Zone : corps du tableau central === -->\n" .
		"  <div class=\"container-fluid\" id=\"corps_tableau\">\n";

	return $Texte;
}



public function construireDebutPiedTableau() {
/**
* Standardisation du début du pied du tableau central.
*
* \license Copyleft
* \author Pierre-Luc MARY
* \date 2015-07-23
*
* \return Retourne la chaîne à afficher.
*/

	$Texte = "  <!-- === Zone : pied du tableau central === -->\n" .
		"  <div class=\"container-fluid\" id=\"pied_tableau\">\n";

	return $Texte;
}



public function construireFinTableau() {
/**
* Standardisation des fins de partie du tableau central (utilisable pour l'entête, le corps et le pied du tableau).
*
* \license Copyleft
* \author Pierre-Luc MARY
* \date 2015-07-23
*
* \return Retourne la chaîne à afficher.
*/

	$Texte = "  </div>\n\n";

	return $Texte;
}



public function construireEnteteTableau( $Colonnes ) {
/**
* Standardisation de l'entête du tableau central.
*
* \license Copyleft
* \author Pierre-Luc MARY
* \date 2015-08-04
*
* \param[in] $Colonnes_Entete Tableau matérialisant les colonnes composant l'entête.
*
* \return Retourne la chaîne à afficher.
*/
	include DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php';
	
	$Texte = $this->construireDebutEnteteTableau();

	// Affichage des titres de colonne de l'écran.
	$Texte .= "   <div class=\"row\">\n";

	foreach ( $Colonnes[ 'Colonnes' ] as $Colonne ) {
		$Class = '';
		$Sens_Tri = '';
		$Marquer_colonne = '';
	
		// Vérifie si la colonne est triable.
		if ( isset( $Colonne[ 'triable' ] ) && strtolower( $Colonne[ 'triable' ] ) == 'oui' ) {
			$Class .= ' triable';
			$Marquer_colonne = '&nbsp;<i class="bi-chevron-down"></i> ';
		}

		// Vérifie si le tri est actif sur cette colonne au départ.
		if ( isset( $Colonne[ 'tri_actif' ] ) && strtolower( $Colonne[ 'tri_actif' ] ) == 'oui' ) {
			$Class .= ' active';
		}

		// Vérifie s'il y a un sens de tri à appliquer.
		if ( isset( $Colonne[ 'sens_tri' ] ) ) {
			$Sens_Tri = 'data-sens-tri="' . $Colonne[ 'sens_tri' ] . '"';
		}

		// Mise en place de la colonne
		$Texte .= "    <div class=\"col-lg-" . $Colonne[ 'taille' ] . $Class . "\" " . $Sens_Tri . ">" .
			$Colonne['titre'] . $Marquer_colonne . "</div>\n";
	}


	if ( isset( $Colonnes[ 'Actions' ] ) ) {
		if ( ! isset($Colonnes[ 'Actions' ][ 'titre' ]) ) {
			$Colonnes[ 'Actions' ][ 'titre' ] = '';
		}

		$tmpActionClass = '';

		if ( isset($Colonnes[ 'Actions' ][ 'affichage' ]) ) {
			$tmpActionClass = ' affichage';
			if ( $Colonnes[ 'Actions' ][ 'affichage' ] == 'cacher' ) {
				$tmpActionClass = ' hide';
			} elseif ( $Colonnes[ 'Actions' ][ 'affichage' ] == 'invisible' ) {
				$tmpActionClass = ' invisible';
			} elseif ( $Colonnes[ 'Actions' ][ 'affichage' ] == 'invisible-droit' ) {
				$tmpActionClass = ' invisible text-end';
			}
		}

		$Texte .= "    <div class=\"btn-actions col-lg-" . $Colonnes[ 'Actions' ][ 'taille' ] . $tmpActionClass . "\">" . $Colonnes[ 'Actions' ][ 'titre' ] . "</div>\n";
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
		if ( isset( $Colonne[ 'recherche' ] ) && $Criteres_Recherche == '' ) {
			$Flag_Recherche = 1;
			$Texte .= "   <div class=\"row recherche\">\n";
		}

		if ( isset( $Colonne[ 'recherche' ] ) ) {
			if ( isset( $Colonne[ 'recherche' ][ 'nom' ] ) ) {
				$Criteres_Recherche .= '<div class="col-lg-' . $Colonne[ 'taille' ] . '">';

				if ( ! isset( $Colonne[ 'recherche' ][ 'type' ] ) ) {
					$Colonne[ 'recherche' ][ 'type' ] = 'input';
				}

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

	if ( $Flag_Recherche == 1 ) {
		$Texte .= $Criteres_Recherche . "   </div> <!-- /.row .recherche -->\n";
	}

	$Texte .= $this->construireFinTableau();

	return $Texte;
}



public function construirePiedTableau( $Total = 0, $Bouton_Alternatif = '' ) {
/**
* Standardisation de la création des occurrences dans les parties du tableau central.
*
* \license Copyleft
* \author Pierre-Luc MARY
* \date 2023-12-20
*
* \param[in] $Total Indique le nombre total dans le tableau (par défaut 0)
* \param[in] $Bouton_Alternatif Permet d'afficher un bouton alertnatif en bas du tableau et à droite du compteur.
*
* \return Retourne la chaîne afficher.
*/
	include DIR_LIBELLES . "/" . $_SESSION[ 'Language' ] . "_libelles_generiques.php";

	$Texte = $this->construireDebutPiedTableau() . "   <div class=\"row\">\n" .
		'<h6 style="margin-top: 0.5rem;">' . $L_Total . ' : <span class="badge bg-secondary" id="totalOccurrences">' . sprintf( "%03d", $Total ) . "</span></h6>";


	if ( $Bouton_Alternatif != '' ) {
		$Texte .= '<button class="btn btn-outline-secondary btn-sm ' . $Bouton_Alternatif['class'] . '">' . $Bouton_Alternatif['libelle'] . '</button>';
	}

	$Texte .= "   </div>\n" . 
		$this->construireFinTableau();

	return $Texte;
}



public function contruireTableauVide( $Colonnes_Entete, $Bouton_Alternatif = '' ) {
/**
* Standardisation de la création d'un tableau central vide (il sera rempli par un appel AJAX ultérieur).
*
* \license Copyleft
* \author Pierre-Luc MARY
* \date 2015-10-11
*
* \param[in] $Colonnes_Entete Indique le format des colonnes d'entête à afficher.
* \param[in] $Bouton_Alternatif Permet d'afficher un bouton alertnatif en bas du tableau.
*
* \return Retourne la chaîne afficher.
*/

	$Codes_HTML = $this->construireEnteteTableau( $Colonnes_Entete ) .
		$this->construireDebutCorpsTableau() .
		$this->construireFinTableau() .
		$this->construirePiedTableau( 0, $Bouton_Alternatif );

	return $Codes_HTML;
}



public function construireModal( $Id_Modal, $Titre, $Corps, $Id_Bouton, $Libelle_Bouton, $Bouton_Fermer = true ) {
/**
* Standardisation des écrans de type "modal".
* La standardisation définit une fenêtre complète.
*
* \license Copyleft
* \author Pierre-Luc MARY
* \date 2023-12-20
*
* \param[in] $Id_Modal ID de la "modal" dans le DOM de la page.
* \param[in] $Titre Indique le titre à afficher en haut de la fenêtre "modal".
* \param[in] $Corps Corps à afficher dans le corps de la fenêtre "modal".
* \param[in] $Id_Bouton Indique l'ID associé au bouton "action" de la fenêtre "modal".
* \param[in] $Libelle_Bouton Indique le libellé à associer au bouton "action" de la fenêtre "modal".
* \param[in] $Bouton_Fermer Indicateur pour afficher le bouton "fermer" à la fenêtre "modal".
*
* \return Retourne la chaîne à afficher.
*/

	if ( $Id_Modal == '' ) {
		$Id_Modal = 'plmModal';
	}

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

	if ( $Bouton_Fermer == true ) {
		$Texte .= "	<button type=\"button\" class=\"btn btn-outline-secondary\" data-dismiss=\"modal\">" . $L_Fermer . "</button>\n";
	}

	if ( $Id_Bouton != NULL && $Id_Bouton != '' ) {
		$Texte .= "	<button type=\"button\" class=\"btn btn-primary\" id=\"" . $Id_Bouton . "\">" . $Libelle_Bouton . "</button>\n";
	}

	$Texte .= "   </div>\n" .
		"  </div>\n" .
		" </div>\n" .
		"</div>\n";

	return $Texte;
}



public function construireFooter() {
/**
* Standardisation de l'affichage des bas de page, ainsi que l'appel aux scripts JS standards
*
* \license Copyleft
* \author Pierre-Luc MARY
* \date 2023-12-20
*
*
* \return Retourne l'objet HTML à afficher
*/
	include DIR_LIBELLES . "/" . $_SESSION[ 'Language' ] . "_libelles_generiques.php";
	include DIR_LIBELLES . "/" . $_SESSION[ 'Language' ] . "_MySecDash-Connexion.php";


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
	 "   Loxense <img src=\"" . URL_IMAGES . "/copyleft.svg\" alt=\"copyleft\" width=\"12px\">, " . $this->Nom_Outil . " v" . $this->Version_Outil . "\n" .
	 "  </footer>\n";

	return $Texte;
}



public function construirePiedHTML() {
/**
* Standardisation des fins de page HTML.
*
* \license Copyleft
* \author Pierre-Luc MARY
* \date 2015-07-23
*
* \return Retourne une chaîne à afficher
*/
	include DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php';

	return "<div class=\"modal\" id=\"fenetre_attente\" tabindex=\"-1\" style=\"z-index: 3000;\">\n" .
		" <div class=\"modal-dialog\" role=\"document\">\n" .
		"  <div class=\"modal-content\">\n" .
		"   <div class=\"modal-body\">\n" .
		"	<img src=\"" . URL_IMAGES . "/ajax-loader-2.gif\" /><span style=\"margin-left:20px;font-weight:bold;font-size:20px;\">" . $L_Travail_En_Cours . "</span>\n" .
		"   </div>\n" .
		"  </div>\n" .
		" </div>\n" .
		"</div>\n" .
		" </body>\n" .
		"</html>\n" ;
}



public function afficherNotification( $Message, $Flag_Avertissement = false ) {
/**
* Affiche une boîte d'information.
*
* \license Copyleft
* \author Pierre-Luc MARY
* \version 1.0
* \date 2016-04-23
*
* \param[in] $Message Message à afficher
* \param[in] $Flag_Avertissement Permet d'afficher le message en mode "Avertissement"
*
* \return Retourne une chaîne à afficher
*/
	include DIR_LIBELLES . "/" . $_SESSION[ 'Language' ] . "_libelles_generiques.php";
	

	if ( $Flag_Avertissement == false ) {
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

	if ( $IdMessage != '' ) {
		$Texte .= "id=\"" . $IdMessage . "\" ";
	}

	$Texte .= "style=\"position:absolute;top:175px;width:80%;left:10%;cursor:pointer;\" onClick=\"javascript:effacerMessage('" . $IdMessage . "');\">" .
		" <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"" . $L_Fermer . "\"></button>" .
		" <span class=\"glyphicon glyphicon-" . $Icon . " aria-hidden=\"true\"></span> " . $Message .
		"</div>";

	return $Texte . "		</div>\n";
}



public function construirePageAlerte( $Message, $Script = 'MySecDash-Principal.php', $Type_Message = 2, $Nom_Fichier_Logo = '' ) {
/**
* Affiche d'une page HTML invisible qui fait rediriger vers un script spécifique qui devra afficher le message envoyé dans le formulaire.
*
* \license Copyleft
* \author Pierre-Luc MARY
* \date 2023-12-18
*
* \param[in] $Message Message à afficher
* \param[in] $Script Script à exécuter sur le clique du bouton retour
* \param[in] $TypeMessage Précise quel type d'icône présenter (0 = information, 1 = Avertissement, 2 = Erreur).
*
* \return Retourne une chaîne matérialisant l'affichage de cet écran d'information
*/
	include DIR_LIBELLES . "/" . $_SESSION[ 'Language' ] . "_libelles_generiques.php";
	
	switch( $Type_Message ) {
		case 0:
			$Titre_Page = $L_Information;
			$Nom_Image = '<i class="bi-info-circle text-info"></i>';
			$Couleur_Fond = 'panel-info';
			break;
		case 1:
			$Titre_Page = $L_Warning;
			$Nom_Image = '<i class="bi-exclamation-triangle text-warning"></i>';
			$Couleur_Fond = 'panel-warning';
			break;
		default:
		case 2:
			$Titre_Page = $L_Error;
			$Nom_Image = '<i class="bi-dash-circle-fill text-danger"></i>';
			$Couleur_Fond = 'panel-danger';
			break;
	}

	if ( $Nom_Fichier_Logo == '' ) {
		$Nom_Fichier_Logo = 'Logo-MySecDash.svg';
	}
	
	return $this->construireEnteteHTML( $Titre_Page ) .
	"  <p id=\"logo-img\" class=\"text-center\"><img src=\"" . URL_IMAGES . "/" . $Nom_Fichier_Logo . "\" alt=\"Logo MySecDash\" height=\"50px\" /></p>\n\n" .
		"  <div id=\"principal-container\" class=\"container\">\n" .
		"   <div class=\"card " . $Couleur_Fond . "\">\n" .
		"	<div class=\"card-header\">\n" .
		"	 <h3 class=\"card-title text-center\">" . $Titre_Page . "</h3>\n" .
		"	</div>\n" .
		"	<div class=\"card-body\">\n" .
		"	 <h2 class=\"text-center\">" . $Nom_Image . "&nbsp;" . $Message . "</h2>\n" .
		"	 <form method=\"post\" action=\"" . URL_BASE . '/' . $Script . "\">\n" .
		"	  <div class=\"form-group text-center\">\n" .
		"	   <button class=\"btn btn-outline-secondary btn-connexion\">" . $L_Retour . "</button>\n" .
		"	  </div>\n" .
		"	 </form>\n" .
		"	</div>\n" .
		"   </div>\n" .
		"  </div>\n" .
		$this->construireFooter( false ) .
		$this->construirePiedHTML();

}


public function routage( $Message, $Script, $TypeMessage = 0 ) {
/**
* Affiche une page HTML invisible qui fait rediriger vers un script spécifique qui devra afficher le message envoyé dans le formulaire.
*
* \license Copyleft
* \author Pierre-Luc MARY
* \date 2015-08-06
*
* \param[in] $Message Message à afficher
* \param[in] $Script Script à exécuter sur le clique du bouton retour
* \param[in] $TypeMessage Précise quel type d'icône présenter.
*
* \return Retourne une chaîne matérialisant l'affichage de cet écran d'information
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



public static function calculCouleurCellule( $code_couleur_fond ) {
	/**
	 * Calcul la couleur du texte (blanc ou noir) en fonction de la couleur de fond.
	 *
	 * \license Copyleft
	 * \author Pierre-Luc MARY
	 * \since 2023-12-20
	 *
	 * \param[in] $code_couleur_fond Code couleur du fond
	 * 
	 * \return Retourne le mot codifié HTML associé à la couleur de fond
	 */
	$code_1 = substr( $code_couleur_fond, 0, 2 );
	$code_2 = substr( $code_couleur_fond, 2, 2 );
	$code_3 = substr( $code_couleur_fond, 4, 2 );

	$calcul = (hexdec( $code_1 ) + hexdec( $code_2 ) + hexdec( $code_3)) / 3;

	if ( $calcul < ((hexdec( '66' ) + hexdec( '66' ) + hexdec( '66' )) / 3) ) {
		return 'white';
	} else {
		return 'black';
	}
}


public static function calculCouleurCelluleHexa( $code_couleur_fond ) {
	/**
	 * Calcul la couleur du texte (blanc ou noir) en fonction de la couleur de fond.
	 * La valeur de retour est un nom standisé HTML.
	 *
	 * \license Copyleft
	 * \author Pierre-Luc MARY
	 * \since 2023-12-20
	 *
	 * \param[in] $code_couleur_fond Code couleur du fond
	 *
	 * \return Retourne le code hexadécimal de la couleur à utiliser
	 */
	$code_1 = substr( $code_couleur_fond, 0, 2 );
	$code_2 = substr( $code_couleur_fond, 2, 2 );
	$code_3 = substr( $code_couleur_fond, 4, 2 );

	$calcul = (hexdec( $code_1 ) + hexdec( $code_2 ) + hexdec( $code_3)) / 3;

	if ( $calcul < ((hexdec( '66' ) + hexdec( '66' ) + hexdec( '66' )) / 3) ) {
		return 'FFFFFF';
	} else {
		return '000000';
	}
}


function creerOccurrenceCorpsTableau( $Id, $Valeurs, $Format_Colonnes_Corps ) {
	/**
	 * Crée l'occurrence du tableau à afficher.
	 *
	 * \license Copyleft
	 * \author Pierre-Luc MARY
	 * \since 2023-12-20
	 *
	 * \param[in] Id Id de l'occurrence à dessiner
	 * \param[in] $Valeurs Valeurs des cellules
	 * \param[in] $Format_Colonnes_Corps Format de l'occurrence (à définir dans les programmes appelants)
	 *
	 * \return Retourne le code hexadécimal de la couleur à utiliser
	 */
	include DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_HBL_Generiques.inc.php';
	include DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_libelles_generiques.php';


	if ( isset( $Format_Colonnes_Corps['Actions'] ) ) {
		if ( ! array_key_exists('historique', $Format_Colonnes_Corps['Actions']['boutons'] ) ) {
			$Format_Colonnes_Corps['Actions']['boutons']['historique'] = false;
		}

		if ( ! array_key_exists('visualiser', $Format_Colonnes_Corps['Actions']['boutons'] ) ) {
			$Format_Colonnes_Corps['Actions']['boutons']['visualiser'] = false;
		}

		if ( ! array_key_exists('dupliquer', $Format_Colonnes_Corps['Actions']['boutons'] ) ) {
			$Format_Colonnes_Corps['Actions']['boutons']['dupliquer'] = false;
		}

		if ( ! array_key_exists('modifier', $Format_Colonnes_Corps['Actions']['boutons'] ) ) {
			$Format_Colonnes_Corps['Actions']['boutons']['modifier'] = false;
		}

		if ( ! array_key_exists('supprimer', $Format_Colonnes_Corps['Actions']['boutons'] ) ) {
			$Format_Colonnes_Corps['Actions']['boutons']['supprimer'] = false;
		}

		if ( ! array_key_exists('supprimer_libelle', $Format_Colonnes_Corps['Actions']['boutons'] ) ) {
			$Format_Colonnes_Corps['Actions']['boutons']['supprimer_libelle'] = false;
		}

		if ( ! array_key_exists('ignorer_risque', $Format_Colonnes_Corps['Actions']['boutons'] ) ) {
			$Format_Colonnes_Corps['Actions']['boutons']['ignorer_risque'] = false;
		}

		if ( ! array_key_exists('generer', $Format_Colonnes_Corps['Actions']['boutons'] ) ) {
			$Format_Colonnes_Corps['Actions']['boutons']['generer'] = false;
		}

		if ( ! array_key_exists('telecharger', $Format_Colonnes_Corps['Actions']['boutons'] ) ) {
			$Format_Colonnes_Corps['Actions']['boutons']['telecharger'] = false;
		}

		if ( ! array_key_exists('telecharger_e', $Format_Colonnes_Corps['Actions']['boutons'] ) ) {
			$Format_Colonnes_Corps['Actions']['boutons']['telecharger_e'] = false;
		}

		if ( ! array_key_exists('telecharger_w', $Format_Colonnes_Corps['Actions']['boutons'] ) ) {
			$Format_Colonnes_Corps['Actions']['boutons']['telecharger_w'] = false;
		}

		if ( ! array_key_exists('exporter', $Format_Colonnes_Corps['Actions']['boutons'] ) ) {
			$Format_Colonnes_Corps['Actions']['boutons']['exporter'] = false;
		}

		if ( ! array_key_exists('restaurer', $Format_Colonnes_Corps['Actions']['boutons'] ) ) {
			$Format_Colonnes_Corps['Actions']['boutons']['restaurer'] = false;
		}

		if ( ! array_key_exists('imprimer', $Format_Colonnes_Corps['Actions']['boutons'] ) ) {
			$Format_Colonnes_Corps['Actions']['boutons']['imprimer'] = false;
		}

		if ( ! array_key_exists('valider', $Format_Colonnes_Corps['Actions']['boutons'] ) ) {
			$Format_Colonnes_Corps['Actions']['boutons']['valider'] = false;
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
				if ( $Colonne['affichage'] == 'img' ) {
					$Affichage = 'img';
				}
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
			if ($Valeur != null) {
				$Valeur = htmlspecialchars( $Valeur, ENT_QUOTES | ENT_HTML5 );
			}
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
			if ( $Format_Colonnes_Corps[ 'Actions' ][ 'affichage' ] == 'cacher' ) {
				$tmpActionClass = ' hide';
			} elseif ( $Format_Colonnes_Corps[ 'Actions' ][ 'affichage' ] == 'invisible' ) {
				$tmpActionClass = ' invisible';
			} elseif ( $Format_Colonnes_Corps[ 'Actions' ][ 'affichage' ] == 'invisible-droit' ) {
				$tmpActionClass = ' invisible text-end';
			}
		}

		$Occurrence .= '<div class="btn-actions col-lg-' . $Format_Colonnes_Corps['Actions']['taille'] . $tmpActionClass . '">';

		if ( $Format_Colonnes_Corps['Actions']['boutons']['historique'] === true ) {
			$Occurrence .= '<button class="btn btn-outline-secondary btn-sm btn-historique" data-id="' . $Id . '" title="' . $L_Consulter_Historique . '" type="button">' .
			'<i class="bi-clock"></i>' .
			'</button>';
		}

		$Occurrence .= ' ';

		if ( $Format_Colonnes_Corps['Actions']['boutons']['visualiser'] === true ) {
			$Occurrence .= '<button class="btn btn-outline-secondary btn-sm btn-visualiser" data-id="' . $Id . '" title="' . $L_Visualiser . '" type="button">' .
			'<i class="bi-eye-fill"></i>' .
			'</button>';
		}

		$Occurrence .= ' ';

		if ( $Format_Colonnes_Corps['Actions']['boutons']['dupliquer'] === true ) {
			$Occurrence .= '<button class="btn btn-outline-secondary btn-sm btn-dupliquer" data-id="' . $Id . '" title="' . $L_Dupliquer . '" type="button">' .
			'<i class="bi-files"></i>' .
			'</button>';
		}

		$Occurrence .= ' ';

		if ( $Format_Colonnes_Corps['Actions']['boutons']['modifier'] === true ) {
			$Occurrence .= '<button class="btn btn-outline-secondary btn-sm btn-modifier" data-id="' . $Id . '" title="' . $L_Modify . '" type="button">' .
			'<i class="bi-pencil-fill"></i>' .
			'</button>';
		}

		$Occurrence .= ' ';

		if ( $Format_Colonnes_Corps['Actions']['boutons']['exporter'] === true ) {
			$Occurrence .= '<button class="btn btn-outline-secondary btn-sm btn-exporter" data-id="' . $Id . '" title="' . $L_Exporter_Base . '" type="button">' .
			'<i class="bi-cloud-upload"></i>' . // import save-file
			'</button>';
		}

		$Occurrence .= ' ';

		if ( $Format_Colonnes_Corps['Actions']['boutons']['restaurer'] === true ) {
			$Occurrence .= '<button class="btn btn-outline-secondary btn-sm btn-restaurer" data-id="' . $Id . '" title="' . $L_Restaurer_Base . '" type="button">' .
			'<i class="bi-box-arrow-in-down-right"></i>' . // import save-file
			'</button>';
		}

		$Occurrence .= ' ';

		if ( $Format_Colonnes_Corps['Actions']['boutons']['supprimer'] === true ) {
			$Occurrence .= '<button class="btn btn-outline-secondary btn-sm btn-supprimer" data-id="' . $Id . '" title="' . $L_Delete . '" type="button">' .
			'<i class="bi-x-circle"></i>' .
			'</button>';
		}

		$Occurrence .= ' ';

		if ( $Format_Colonnes_Corps['Actions']['boutons']['supprimer_libelle'] === true ) {
			$Occurrence .= '<button class="btn btn-outline-secondary btn-sm btn-supprimer_libelle" data-id="' . $Id . '" title="' . $L_Supprimer_Libelle . '" type="button">' .
			'<i class="bi-x-circle-fill"></i>' .
			'</button>';
		}

		$Occurrence .= ' ';

		if ( $Format_Colonnes_Corps['Actions']['boutons']['ignorer_risque'] === true ) {
			$Occurrence .= '<button class="btn btn-outline-secondary btn-sm btn-ignorer_risque" data-id="' . $Id . '" title="' . $L_Ignorer_Risque . '" type="button">' .
			'<i class="bi-slash-circle"></i>' .
			'</button>';
		}

		$Occurrence .= ' ';

		if ( $Format_Colonnes_Corps['Actions']['boutons']['generer'] === true ) {
			$Occurrence .= '<button class="btn btn-outline-secondary btn-sm btn-generer" data-id="' . $Id . '" title="' . $L_Generer_Impression . '" type="button">' .
			'<i class="bi-arrow-repeat"></i>' .
			'</button>';
		}

		$Occurrence .= ' ';

		if ( $Format_Colonnes_Corps['Actions']['boutons']['telecharger'] === true ) {
			$Occurrence .= '<button class="btn btn-outline-secondary btn-sm btn-telecharger" data-id="' . $Id . '" title="' . $L_Telecharger_Impression . '" type="button">' .
			'<i class="bi-download"></i>' . // import save-file
			'</button>';
		}

		$Occurrence .= ' ';

		if ( $Format_Colonnes_Corps['Actions']['boutons']['telecharger_e'] === true ) {
			$Occurrence .= '<button class="btn btn-outline-secondary btn-sm btn-telecharger-e" data-id="' . $Id . '" title="' . $L_Telecharger_Excel . '" type="button">' .
			'<img src="' . URL_IMAGES . '/Excel-2-icon.png" alt="Excel"/>' .
			'</button>';
		}

		$Occurrence .= ' ';

		if ( $Format_Colonnes_Corps['Actions']['boutons']['telecharger_w'] === true ) {
			$Occurrence .= '<button class="btn btn-outline-secondary btn-sm btn-telecharger-w" data-id="' . $Id . '" title="' . $L_Telecharger_Word . '" type="button">' .
			'<img src="' . URL_IMAGES . '/Word-2-icon.png" alt="Word"/>' .
			'</button>';
		}

		$Occurrence .= ' ';

		if ( $Format_Colonnes_Corps['Actions']['boutons']['imprimer'] === true ) {
			$Occurrence .= '<button class="btn btn-outline-secondary btn-sm btn-imprimer" data-id="' . $Id . '" title="' . $L_Imprimer . '" type="button">' .
			'<i class="bi-printer-fill"></i>' .
			'</button>';
		}

		$Occurrence .= ' ';

		if ( $Format_Colonnes_Corps['Actions']['boutons']['valider'] === true ) {
			$Occurrence .= '<button class="btn btn-outline-secondary btn-sm btn-valider" data-id="' . $Id . '" title="' . $L_Valider . '" type="button">' .
				'<i class="bi-clipboard-check"></i>' .
				'</button>';
		}

		$Occurrence .= '</div>';
	}

	$Occurrence .= '</div>';


	return $Occurrence;
}



public function construitBoutonCompteur( $Valeur ) {
	/**
	 * Création d'un bouton compteur standardisé.
	 *
	 * \license Copyleft
	 * \author Pierre-Luc MARY
	 * \since 2023-12-20
	 *
	 * \param[in] $Valeur Valeur à afficher dans le bouton
	 *
	 * \return Retourne l'objet HTML à afficher
	 */
	if ( $Valeur > 0 ) {
		$Icone = '<i class="bi-chevron-right"></i>';
	} else {
		$Icone = '<i class="bi-plus-circle-fill"></i>';
	}

	return '<button class="btn btn-outline-secondary btn-sm btn-compteur col-lg-6">' . $Valeur . '&nbsp;' . $Icone . '</button>';
}



public function construireCompteurListe( $Valeur ) {
	/**
	 * Création d'un bouton compteur de liste standardisé.
	 *
	 * \license Copyleft
	 * \author Pierre-Luc MARY
	 * \since 2023-12-20
	 *
	 * \param[in] $Valeur Valeur à afficher dans le bouton
	 *
	 * \return Retourne l'objet HTML à afficher
	 */
	if ( $Valeur == 0 ) {
		$HTML = '<span class="badge bg-secondary align-middle">' . $Valeur . '</span>';
	} else {
		$HTML = '<span class="badge bg-vert_normal align-middle">' . $Valeur . '</span>';
	}

	return $HTML;
}

} // Fin class HTML.

?>