<?php

/**
* Ce script gère la page d'accueil de l'outil et donne accès à toutes les fonctions 
* disponibles à l'utilisateur connecté.
*
* PHP version 5
* @license Loxense
* @author Pierre-Luc MARY
* @package MySecDash
* @version 1.0
* @date 2015-07-23
*
*/

// Charge les constantes du projet.
include( 'Constants.inc.php' );

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
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_Loxense-Connexion.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_' . basename( $Script ) );


// Charge les classes utiles à cet écran.
include( DIR_LIBRAIRIES . '/Class_HTML.inc.php' );


// Crée une instance de l'objet HTML.
$PageHTML = new HTML();


// Vérifie si la session de l'utilisateur n'a pas expiré.
if ( $PageHTML->validerTempsSession() ) {
	$PageHTML->sauverTempsSession();
} else {
	print( $PageHTML->construirePageAlerte( $L_Session_Expired, '/Loxense-Connexion.php', 1 ) );
	exit();
}


// Identifie l'action à réaliser.
if ( array_key_exists( 'Action', $_GET ) ) {
	$Action = $_GET[ 'Action' ];
} else {
	$Action = '';
}

$Permissions = $PageHTML->permissionsGroupees();


// Exécute l'action identifiée
switch( $Action ) {
 default:

	print( $PageHTML->construireEnteteHTML( $L_Dashboard, '' ) .
	 $PageHTML->construireNavbar() );

	if ( array_key_exists( 'notification', $_GET ) ) {
		if ( isset( $_POST[ 'Message'] ) and isset( $_POST[ 'Type_Message' ] ) ) {
			print( $PageHTML->afficherNotification( $_POST[ 'Message'], $_POST[ 'Type_Message' ] ) );
		}
	}


	print( '<div id="titre_ecran" class="container-fluid" data-admin="' . $PageHTML->estAdministrateur() . '">' .
		"<h1 style=\"margin-top: 0;\">" . $L_Bienvenue . " " . $PageHTML->Nom_Outil . "</h1>\n" .
		'<script src="' . URL_LIBRAIRIES . '/js/Loxense-Principal.js"></script>' .
		'<ul class="nav nav-tabs">' .
		'<li class="nav-item"><a id="onglet-administrateur" class="nav-link active" href="#">' . $L_Administrateur . '</a></li>' .
		'<li class="nav-item"><a id="onglet-utilisateur" class="nav-link" href="#">' . $L_Utilisateur . '</a></li>' .
		'</ul>' .
		'</div>' .
		'<div id="corps_tableau" class="container-fluid" style="padding: 18px 0;">' .
		"</div>\n".
	   $PageHTML->construireFooter() .
	   $PageHTML->construirePiedHTML() );

	break;


 case 'AJAX_Tableau_Bord_Admin':
	$texteHTML = '';

	try {
		$Class = 'bg-vert_normal';

		if ( isset( $Permissions['Loxense-CriteresValorisationActifs.php'] )
			|| isset( $Permissions['Loxense-CriteresAppreciationAccepationRisques.php'] )
			|| isset( $Permissions['Loxense-GrillesImpacts.php'] )
			|| isset( $Permissions['Loxense-GrillesVraisemblances.php'] )
			|| isset( $Permissions['Loxense-CartographiesRisques.php'] )
			|| isset( $Permissions['Loxense-ActifsPrimordiaux.php'] )
			|| isset( $Permissions['Loxense-ActifsSupports.php'] )
			|| isset( $Permissions['Loxense-EvenementsRedoutes.php'] )
			//|| isset( $Permissions['Loxense-SourcesMenaces.php'] )
			//|| isset( $Permissions['Loxense-PartiesPrenantes.php'] )
			|| isset( $Permissions['Loxense-AppreciationRisques.php'] )
			|| isset( $Permissions['Loxense-TraitementRisques.php'] )
			|| isset( $Permissions['Loxense-EditionsRisques.php'] )
			|| isset( $Permissions['Loxense-MatricesRisques.php'] )
			) {

			$texteHTML .= "<div class=\"tableau_synthese\">" .
				"<p class=\"titre\">" . $L_Gestion_Risques . "</p>\n" .
				"<div class=\"corps\">\n";


			if ( isset( $Permissions['Loxense-CriteresValorisationActifs.php'] ) ) {
				$texteHTML .= "<a href=\"Loxense-CriteresValorisationActifs.php\" class=\"btn btn-admin btn-principal\">" .
					"<span class=\"me-1\">" .
					( ( $PageHTML->estAdministrateur() ) ? couperLibelle( $L_Gestion_Criteres_Valorisation_Actifs, 39 ) : $L_Gestion_Criteres_Valorisation_Actifs ) .
					"</span>";

				if ( $PageHTML->estAdministrateur() ) {
					include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_Loxense-CriteresValorisationActifs.php' );
					include( DIR_LIBRAIRIES . '/Class_CriteresValorisationActifs_PDO.inc.php' );
					$CriteresValorisationActifs = new CriteresValorisationActifs();

					$Total = $CriteresValorisationActifs->totalCriteresValorisationActifs();

					$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class . "\">" . $Total . "</span></span>";
				}

				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['Loxense-CriteresAppreciationAcceptationRisques.php'] ) ) {
				$texteHTML .= "<a href=\"Loxense-CriteresAppreciationAcceptationRisques.php\" class=\"btn btn-admin btn-principal\">" .
					"<span class=\"me-1\">" .
					( ( $PageHTML->estAdministrateur() ) ? couperLibelle( $L_Gestion_Criteres_Appreciation_Acceptation_Risques, 39 ) : $L_Gestion_Criteres_Appreciation_Acceptation_Risques ) .
					"</span>";

				if ( $PageHTML->estAdministrateur() ) {
					include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_Loxense-CriteresAppreciationAcceptationRisques.php' );
					include( DIR_LIBRAIRIES . '/Class_CriteresAppreciationRisques_PDO.inc.php' );
					$CriteresAppreciationRisques = new CriteresAppreciationRisques();

					$Total = $CriteresAppreciationRisques->totalRepresentationNiveauxRisque();

					$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class . "\">" . $Total . "</span></span>";
				}

				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['Loxense-GrillesImpacts.php'] ) ) {
				$texteHTML .= "<a href=\"Loxense-GrillesImpacts.php\" class=\"btn btn-admin btn-principal\">" .
					"<span class=\"me-1\">" .
					( ( $PageHTML->estAdministrateur() ) ? couperLibelle( $L_Gestion_Grilles_Impacts, 39 ) : $L_Gestion_Grilles_Impacts ) .
					"</span>";

				if ( $PageHTML->estAdministrateur() ) {
					include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_Loxense-GrillesImpacts.php' );
					include_once( DIR_LIBRAIRIES . '/Class_CriteresAppreciationRisques_PDO.inc.php' );
					$CriteresAppreciationRisques = new CriteresAppreciationRisques();

					$Total = $CriteresAppreciationRisques->totalCriteresImpact();

					$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class . "\">" . $Total . "</span></span>";
				}

				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['Loxense-GrillesVraisemblances.php'] ) ) {
				$texteHTML .= "<a href=\"Loxense-GrillesVraisemblances.php\" class=\"btn btn-admin btn-principal\">" .
					"<span class=\"me-1\">" .
					( ( $PageHTML->estAdministrateur() ) ? couperLibelle( $L_Gestion_Grilles_Vraisemblances, 39 ) : $L_Gestion_Grilles_Vraisemblances ) .
					"</span>";

				if ( $PageHTML->estAdministrateur() ) {
					include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_Loxense-GrillesVraisemblances.php' );
					include_once( DIR_LIBRAIRIES . '/Class_CriteresAppreciationRisques_PDO.inc.php' );
					$CriteresAppreciationRisques = new CriteresAppreciationRisques();

					$Total = $CriteresAppreciationRisques->totalCriteresVraisemblance();

					$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class . "\">" . $Total . "</span></span>";
				}

				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['Loxense-CartographiesRisques.php'] ) ) {
				$texteHTML .= "<a href=\"Loxense-CartographiesRisques.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					( ( $PageHTML->estAdministrateur() ) ? couperLibelle( $L_Gestion_Cartographies_Risques, 26 ) : $L_Gestion_Cartographies_Risques ) . "</span>";

				if ( $PageHTML->estAdministrateur() ) {
					include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_Loxense-CartographiesRisques.php' );
					include( DIR_LIBRAIRIES . '/Class_CartographiesRisques_PDO.inc.php' );
					$Cartographies = new CartographiesRisques();

					$Total = $Cartographies->totalCartographiesRisques();
					$Maximum = $PageHTML->recupererParametre( 'limitation_cartographies' );

					if ( $Total >= $Maximum and $Maximum != 0 ) $Class = "bg-orange_normal";
					elseif ( $Maximum == 0 ) $Class = "bg-vert_normal";
					else  $Class = "bg-vert_normal";

					if ( $Maximum == 0 ) $Maximum = $L_Illimite;

					$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class .
						"\">" . $Total . "</span> / <span class=\"badge bg-secondary\">" . $Maximum . "</span></span>";
				}

				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['Loxense-ActifsPrimordiaux.php'] ) ) {
				$texteHTML .= "<a href=\"Loxense-ActifsPrimordiaux.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					( ( $PageHTML->estAdministrateur() ) ? couperLibelle( $L_Gestion_Actifs_Primordiaux, 39 ) : $L_Gestion_Actifs_Primordiaux ) . "</span>";

				if ( $PageHTML->estAdministrateur() ) {
					include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_Loxense-ActifsPrimordiaux.php' );
					include( DIR_LIBRAIRIES . '/Class_ActifsPrimordiaux_PDO.inc.php' );
					$objActifsPrimordiaux = new ActifsPrimordiaux();

					$Total = $objActifsPrimordiaux->totalActifsPrimordiaux();

					$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class .
						"\">" . $Total . "</span></span>";
				}

				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['Loxense-ActifsSupports.php'] ) ) {
				$texteHTML .= "<a href=\"Loxense-ActifsSupports.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Gestion_Actifs_Supports, 39 ) . "</span>";

				if ( $PageHTML->estAdministrateur() ) {
					include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_Loxense-ActifsSupports.php' );
					include( DIR_LIBRAIRIES . '/Class_ActifsSupports_PDO.inc.php' );
					$objActifsSupports = new ActifsSupports();

					$Total = $objActifsSupports->totalActifsSupports();

					$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class .
						"\">" . $Total . "</span></span>";
				}

				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['Loxense-EvenementsRedoutes.php'] ) ) {
				$texteHTML .= "<a href=\"Loxense-EvenementsRedoutes.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Gestion_Evenements_Redoutes, 39 ) . "</span>";

				if ( $PageHTML->estAdministrateur() ) {
					include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_Loxense-EvenementsRedoutes.php' );
					include( DIR_LIBRAIRIES . '/Class_EvenementsRedoutes_PDO.inc.php' );
					$objEvenementsRedoutes = new EvenementsRedoutes();

					$Total = $objEvenementsRedoutes->totalEvenementsRedoutes();

					$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class .
						"\">" . $Total . "</span></span>";
				}

				$texteHTML .= "</a>";
			}
			

/*
			if ( isset( $Permissions['Loxense-PartiesPrenantes.php'] ) ) {
				$texteHTML .= "<a href=\"Loxense-PartiesPrenantes.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Gestion_Parties_Prenantes, 39 ) . "</span>";
					
					if ( $PageHTML->estAdministrateur() ) {
						include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_Loxense-PartiesPrenantes.php' );
						include( DIR_LIBRAIRIES . '/Class_PartiesPrenantes_PDO.inc.php' );
						$objPartiesPrenantes = new PartiesPrenantes();
						
						$Total = $objPartiesPrenantes->totalPartiesPrenantes();
						
						$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class .
						"\">" . $Total . "</span></span>";
					}
					
					$texteHTML .= "</a>";
			}
*/

			if ( isset( $Permissions['Loxense-AppreciationRisques.php'] ) ) {
				$texteHTML .= "<a href=\"Loxense-AppreciationRisques.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Gestion_Appreciation_Risques, 39 ) . "</span>";

				if ( $PageHTML->estAdministrateur() ) {
					include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_Loxense-AppreciationRisques.php' );
					include( DIR_LIBRAIRIES . '/Class_Risques_PDO.inc.php' );
					$objRisques = new Risques();

					$Total = $objRisques->totalRisques();

					$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class .
						"\">" . $Total . "</span></span>";
				}

				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['Loxense-TraitementRisques.php'] ) ) {
				$texteHTML .= "<a href=\"Loxense-TraitementRisques.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Gestion_Traitement_Risques, 39 ) . "</span>";

				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['Loxense-EditionsRisques.php'] ) ) {
				$texteHTML .= "<a href=\"Loxense-EditionsRisques.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Gestion_Editions_Risques, 39 ) . "</span>";

				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['Loxense-MatricesRisques.php'] ) ) {
				$texteHTML .= "<a href=\"Loxense-MatricesRisques.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Visualisation_Matrices_Risques, 39 ) . "</span>";

				$texteHTML .= "</a>";
			}

			$texteHTML .=  "</div>" .
				"</div>";

		}

		$Class = 'bg-vert_normal';


		if ( isset( $Permissions['Loxense-Actions.php'] )
			|| isset( $Permissions['Loxense-EditionsActions.php'] ) ) {

			$texteHTML .= "<div class=\"tableau_synthese\">" .
				"<p class=\"titre\">" . $L_Gestion_Actions . "</p>\n" .
				"<div class=\"corps\">\n";


			if ( isset( $Permissions['Loxense-Actions.php'] ) ) {
				$texteHTML .= "<a href=\"Loxense-Actions.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Gestion_Actions, 39 ) . "</span>";

				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['Loxense-EditionsActions.php'] ) ) {
				$texteHTML .= "<a href=\"Loxense-EditionsActions.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Edition_Actions, 39 ) . "</span>";

				$texteHTML .= "</a>";
			}

			$texteHTML .=  "</div>" .
				"</div>";

		}
		
		$Class = 'bg-vert_normal';
		
		
		if ( isset( $Permissions['Loxense-Conformite.php'] )
			|| isset( $Permissions['Loxense-EditionConformite.php'] ) ) {
				
			$texteHTML .= "<div class=\"tableau_synthese\">" .
				"<p class=\"titre\">" . $L_Gestion_Conformite . "</p>\n" .
				"<div class=\"corps\">\n";
			
			
			if ( isset( $Permissions['Loxense-Conformite.php'] ) ) {
				$texteHTML .= "<a href=\"Loxense-Conformite.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Gestion_Conformite, 39 ) . "</span>";
					
					$texteHTML .= "</a>";
			}
			
			
			if ( isset( $Permissions['Loxense-EditionConformite.php'] ) ) {
				$texteHTML .= "<a href=\"Loxense-EditionConformite.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Edition_Conformite, 39 ) . "</span>";
					
					$texteHTML .= "</a>";
			}
			
			$texteHTML .=  "</div>" .
				"</div>";
			
		}
			
		$Class = 'bg-vert_normal';


		if ( isset( $Permissions['Loxense-AppreciationRisquesTags.php'] )
			|| isset( $Permissions['Loxense-ActifsPrimordiauxTags.php'] )
			|| isset( $Permissions['Loxense-ActifsSupportsTags.php'] )
			|| isset( $Permissions['Loxense-TraitementRisquesTags.php'] ) ) {

			$texteHTML .= "<div class=\"tableau_synthese\">" .
				"<p class=\"titre\">" . $L_Vision_Consolidee . "</p>\n" .
				"<div class=\"corps\">\n";


			if ( isset( $Permissions['Loxense-ActifsPrimordiauxTags.php'] ) ) {
				$texteHTML .= "<a href=\"Loxense-ActifsPrimordiauxTags.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Actifs_Primordiaux, 39 ) . "</span>";

				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['Loxense-ActifsSupportsTags.php'] ) ) {
				$texteHTML .= "<a href=\"Loxense-ActifsSupportsTags.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Actifs_Supports, 39 ) . "</span>";

				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['Loxense-AppreciationRisquesTags.php'] ) ) {
				$texteHTML .= "<a href=\"Loxense-AppreciationRisquesTags.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Appreciation_Risques, 39 ) . "</span>";

				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['Loxense-TraitementRisquesTags.php'] ) ) {
				$texteHTML .= "<a href=\"Loxense-TraitementRisquesTags.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Traitement_Risques, 39 ) . "</span>";

				$texteHTML .= "</a>";
			}

			$texteHTML .=  "</div>" .
				"</div>";

		}
			
		$Class = 'bg-vert_normal';


		if ( isset( $Permissions['Loxense-Entites.php'] )
			|| isset( $Permissions['Loxense-Civilites.php'] )
			|| isset( $Permissions['Loxense-Utilisateurs.php'] )
			|| isset( $Permissions['Loxense-Profils.php'] )
			|| isset( $Permissions['Loxense-Applications.php'] )
			|| isset( $Permissions['Loxense-Gestionnaires.php'] )
			|| isset( $Permissions['Loxense-Etiquettes.php'] ) ) {
				$texteHTML .= "<div class=\"tableau_synthese\">" .
					"<p class=\"titre\">" . $L_Gestion_Controles_Acces . "</p>\n" .
					"<div class=\"corps\">\n";

			if ( isset( $Permissions['Loxense-Entites.php'] ) ) {
				$texteHTML .= "<a href=\"Loxense-Entites.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Gestion_Entites ) . "</span>";

				if ( $PageHTML->estAdministrateur() ) {
					include( DIR_LIBRAIRIES . '/Class_HBL_Entites_PDO.inc.php' );
					$Entites = new HBL_Entites();

					$Total = $Entites->totalEntites();
					$Maximum = $PageHTML->recupererParametre( 'limitation_entites' );

					if ( $Total >= $Maximum and $Maximum != 0 ) $Class = "bg-orange_normal";
					elseif ( $Maximum == 0 ) $Class = "bg-vert_normal";
					else  $Class = "bg-vert_normal";

					if ( $Maximum == 0 ) $Maximum = $L_Illimite;

					$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class . "\">" . $Total .
						"</span> / <span class=\"badge bg-secondary\">" . $Maximum . "</span></span>";

				}

				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['Loxense-Civilites.php'] ) ) {
				$texteHTML .= "<a href=\"Loxense-Civilites.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Gestion_Civilites ) . "</span>";

				if ( $PageHTML->estAdministrateur() ) {
					include( DIR_LIBRAIRIES . '/Class_HBL_Civilites_PDO.inc.php' );
					$Civilites = new HBL_Civilites();

					$Total = $Civilites->totalCivilites();
					$Maximum = $PageHTML->recupererParametre( 'limitation_civilites' );

					if ( $Total >= $Maximum and $Maximum != 0 ) $Class = "bg-orange_normal";
					elseif ( $Maximum == 0 ) $Class = "bg-vert_normal";
					else $Class = "bg-vert_normal";

					if ( $Maximum == 0 ) $Maximum = $L_Illimite;

					$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class . "\">" . $Total .
						"</span> / <span class=\"badge bg-secondary\">" . $Maximum . "</span></span>";
				}

				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['Loxense-Applications.php'] ) ) {
				$texteHTML .= "<a href=\"Loxense-Applications.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Gestion_Applications ) . "</span>";

				if ( $PageHTML->estAdministrateur() ) {
					include( DIR_LIBRAIRIES . '/Class_HBL_Applications_PDO.inc.php' );
					$Applications = new HBL_Applications();

					$Total = $Applications->totalApplications();

					$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge bg-vert_normal\">" .
						$Total . "</span></span>";
				}

				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['Loxense-Profils.php'] ) ) {
				$texteHTML .= "<a href=\"Loxense-Profils.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Gestion_Profils ) . "</span>";

				if ( $PageHTML->estAdministrateur() ) {
					include( DIR_LIBRAIRIES . '/Class_HBL_Profils_PDO.inc.php' );
					$Profils = new HBL_Profils();

					$Total = $Profils->totalProfils();
					$Maximum = $PageHTML->recupererParametre( 'limitation_profils' );

					if ( $Total >= $Maximum and $Maximum != 0 ) $Class = "bg-orange_normal";
					elseif ( $Maximum == 0 ) $Class = "bg-vert_normal";
					else $Class = "bg-vert_normal";

					if ( $Maximum == 0 ) $Maximum = $L_Illimite;

					$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class . "\">" . $Total .
						"</span> / <span class=\"badge bg-secondary\">" . $Maximum . "</span></span>";
				}

				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['Loxense-Utilisateurs.php'] ) ) {
				$texteHTML .= "<a href=\"Loxense-Utilisateurs.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Gestion_Utilisateurs ) . "</span>";

				if ( $PageHTML->estAdministrateur() ) {
					include( DIR_LIBRAIRIES . '/Class_HBL_Identites_PDO.inc.php' );
					$Identites = new HBL_Identites();

					$Total = $Identites->totalIdentites();
					$Maximum = $PageHTML->recupererParametre( 'limitation_utilisateurs' );

					if ( $Total >= $Maximum and $Maximum != 0 ) $Class = "bg-orange_normal";
					elseif ( $Maximum == 0 ) $Class = "bg-vert_normal";
					else $Class = "bg-vert_normal";

					if ( $Maximum == 0 ) $Maximum = $L_Illimite;

					$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class . "\">" .
						$Total . "</span> / <span class=\"badge bg-secondary\">" . $Maximum . "</span></span>";
				}

				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['Loxense-Gestionnaires.php'] ) ) {
				$texteHTML .= "<a href=\"Loxense-Gestionnaires.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Gestion_Gestionnaires ) . "</span>";

				if ( $PageHTML->estAdministrateur() ) {
					include( DIR_LIBRAIRIES . '/Class_Gestionnaires_PDO.inc.php' );
					$Gestionnaires = new Gestionnaires();

					$Total = $Gestionnaires->totalGestionnaires();

					$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class . "\">" .
						$Total . "</span></span>";
				}

				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['Loxense-Etiquettes.php'] ) ) {
				$texteHTML .= "<a href=\"Loxense-Etiquettes.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Gestion_Etiquettes ) . "</span>";

				if ( $PageHTML->estAdministrateur() ) {
					include( DIR_LIBRAIRIES . '/Class_Etiquettes_PDO.inc.php' );
					$Etiquettes = new Etiquettes();

					$Total = $Etiquettes->totalEtiquettes();

					$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class . "\">" .
						$Total . "</span></span>";
				}

				$texteHTML .= "</a>";
			}

			$texteHTML .= "</div>" .
				"</div>";

		}


		$Class = 'bg-vert_normal';

		if ( isset( $Permissions['Loxense-Parametres.php'] )
			|| isset( $Permissions['Loxense-TypesActifSupport.php'] )
			|| isset( $Permissions['Loxense-MenacesGeneriques.php'] )
			|| isset( $Permissions['Loxense-VulnerabilitesGeneriques.php'] )
			|| isset( $Permissions['Loxense-SourcesMenaces.php'] )
			//|| isset( $Permissions['Loxense-ObjectifsVises.php'] )
			|| isset( $Permissions['Loxense-RisquesGeneriques.php'] )
			|| isset( $Permissions['Loxense-TypesTraitementRisques.php'] )
			|| isset( $Permissions['Loxense-ImpactsGeneriques.php'] )
			|| isset( $Permissions['Loxense-MesuresGeneriques.php'] )
			|| isset( $Permissions['Loxense-ReferentielsConformite.php'] ) ) {

			$texteHTML .= "<div class=\"tableau_synthese\">" .
				"<p class=\"titre\">" . $L_Referentiel_Interne . "</p>\n" .
				"<div class=\"corps\">\n";


			if ( isset( $Permissions['Loxense-Parametres.php'] ) ) {
				$texteHTML .= "<a href=\"Loxense-Parametres.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Gestion_Parametres_Base ) . "</span>";

				if ( $PageHTML->estAdministrateur() ) {
					$Total = $PageHTML->totalParametres();

					$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class . "\">" .
						$Total . "</span></span>";
				}

				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['Loxense-TypesActifSupport.php'] ) ) {
				$texteHTML .= "<a href=\"Loxense-TypesActifSupport.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Gestion_Types_Actif_Support ) . "</span>";

				if ( $PageHTML->estAdministrateur() ) {
					include( DIR_LIBRAIRIES . '/Class_TypesActifSupport_PDO.inc.php' );
					$TypesActifSupport = new TypesActifSupport();

					$Total = $TypesActifSupport->totalTypesActifSupport();

					$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class .
						"\">" . $Total . "</span></span>";
				}

				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['Loxense-TypesMenaceGenerique.php'] ) ) {
				$texteHTML .= "<a href=\"Loxense-TypesMenaceGenerique.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Gestion_Types_Menace_Generique ) . "</span>";

				if ( $PageHTML->estAdministrateur() ) {
					include( DIR_LIBRAIRIES . '/Class_TypesMenaceGenerique_PDO.inc.php' );
					$TypesMenacesGeneriques = new TypesMenaceGenerique();

					$Total = $TypesMenacesGeneriques->totalTypesMenaceGenerique();

					$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class .
						"\">" . $Total . "</span></span>";
				}
				
				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['Loxense-MenacesGeneriques.php'] ) ) {
				$texteHTML .= "<a href=\"Loxense-MenacesGeneriques.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Gestion_Menaces_Generiques ) . "</span>";

				if ( $PageHTML->estAdministrateur() ) {
					include( DIR_LIBRAIRIES . '/Class_MenacesGeneriques_PDO.inc.php' );
					$MenacesGeneriques = new MenacesGeneriques();

					$Total = $MenacesGeneriques->totalMenacesGeneriques();

					$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class .
						"\">" . $Total . "</span></span>";
				}
				
				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['Loxense-VulnerabilitesGeneriques.php'] ) ) {
				$texteHTML .= "<a href=\"Loxense-VulnerabilitesGeneriques.php\" class=\"btn btn-admin btn-principal\">" .
					'<span class=\"me-1\">' . couperLibelle( $L_Gestion_Vulnerabilites_Generiques ) .
					"</span>";

				if ( $PageHTML->estAdministrateur() ) {
					include( DIR_LIBRAIRIES . '/Class_VulnerabilitesGeneriques_PDO.inc.php' );
					$VulnerabilitesGeneriques = new VulnerabilitesGeneriques();

					$Total = $VulnerabilitesGeneriques->totalVulnerabilitesGeneriques();

					$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class . "\">" . $Total . "</span></span>";
				}

				$texteHTML .= "</a>";
			}
			
			if ( isset( $Permissions['Loxense-SourcesMenaces.php'] ) ) {
				$texteHTML .= "<a href=\"Loxense-SourcesMenaces.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Gestion_Sources_Menaces, 39 ) . "</span>";
					
					if ( $PageHTML->estAdministrateur() ) {
						include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_Loxense-SourcesMenaces.php' );
						include( DIR_LIBRAIRIES . '/Class_SourcesMenaces_PDO.inc.php' );
						$objSourcesMenaces = new SourcesMenaces();
						
						$Total = $objSourcesMenaces->totalSourcesMenaces();
						
						$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class .
						"\">" . $Total . "</span></span>";
					}
					
					$texteHTML .= "</a>";
			}
			
			
/*			
			if ( isset( $Permissions['Loxense-SourcesRisques.php'] ) ) {
				$texteHTML .= "<a href=\"Loxense-SourcesRisques.php\" class=\"btn btn-admin btn-principal\">" .
					'<span class=\"me-1\">' . couperLibelle( $L_Gestion_Sources_Risques ) .
					"</span>";
				
				if ( $PageHTML->estAdministrateur() ) {
					include( DIR_LIBRAIRIES . '/Class_SourcesRisques_PDO.inc.php' );
					$SourcesRisques = new SourcesRisques();
					
					$Total = $SourcesRisques->totalSourcesRisques();
					
					$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class . "\">" . $Total . "</span></span>";
				}
				
				$texteHTML .= "</a>";
			}
			
			
			if ( isset( $Permissions['Loxense-ObjectifsVises.php'] ) ) {
				$texteHTML .= "<a href=\"Loxense-ObjectifsVises.php\" class=\"btn btn-admin btn-principal\">" .
					'<span class=\"me-1\">' . couperLibelle( $L_Gestion_Objectifs_Vises ) .
					"</span>";
				
				if ( $PageHTML->estAdministrateur() ) {
					include( DIR_LIBRAIRIES . '/Class_ObjectifsVises_PDO.inc.php' );
					$ObjectifsVises = new ObjectifsVises();
					
					$Total = $ObjectifsVises->totalObjectifsVises();
					
					$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class . "\">" . $Total . "</span></span>";
				}
				
				$texteHTML .= "</a>";
			}
*/

			if ( isset( $Permissions['Loxense-RisquesGeneriques.php'] ) ) {
				$texteHTML .= "<a href=\"Loxense-RisquesGeneriques.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Gestion_Risques_Generiques ) . "</span>";

				if ( $PageHTML->estAdministrateur() ) {
					include( DIR_LIBRAIRIES . '/Class_RisquesGeneriques_PDO.inc.php' );
					$RisquesGeneriques = new RisquesGeneriques();

					$Total = $RisquesGeneriques->totalRisquesGeneriques();

					$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class .
						"\">" . $Total . "</span></span>";
				}

				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['Loxense-TypesTraitementRisques.php'] ) ) {
				$texteHTML .= "<a href=\"Loxense-TypesTraitementRisques.php\" class=\"btn btn-admin btn-principal\">" .
					'<span class=\"me-1\">' . 
					( ( $PageHTML->estAdministrateur() ) ? couperLibelle( $L_Gestion_Types_Traitement_Risques ) : $L_Gestion_Types_Traitement_Risques ) .
					"</span>";

				if ( $PageHTML->estAdministrateur() ) {
					include( DIR_LIBRAIRIES . '/Class_TypesTraitementRisques_PDO.inc.php' );
					$TypesTraitementRisques = new TypesTraitementRisques();

					$Total = $TypesTraitementRisques->totalTypesTraitementRisques();

					$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class . "\">" . $Total . "</span></span>";
				}

				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['Loxense-ImpactsGeneriques.php'] ) ) {
				$texteHTML .= "<a href=\"Loxense-ImpactsGeneriques.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Gestion_Impacts_Generiques ) . "</span>";

				if ( $PageHTML->estAdministrateur() ) {
					include( DIR_LIBRAIRIES . '/Class_ImpactsGeneriques_PDO.inc.php' );
					$ImpactsGeneriques = new ImpactsGeneriques();

					$Total = $ImpactsGeneriques->totalImpactsGeneriques();

					$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class .
						"\">" . $Total . "</span></span>";
				}

				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['Loxense-MesuresGeneriques.php'] ) ) {
				$texteHTML .= "<a href=\"Loxense-MesuresGeneriques.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Gestion_Mesures_Generiques ) . "</span>";

				if ( $PageHTML->estAdministrateur() ) {
					include( DIR_LIBRAIRIES . '/Class_MesuresGeneriques_PDO.inc.php' );
					$MesuresGeneriques = new MesuresGeneriques();

					$Total = $MesuresGeneriques->totalMesuresGeneriques();

					$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class .
						"\">" . $Total . "</span></span>";
				}

				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['Loxense-ReferentielsConformite.php'] ) ) {
				$texteHTML .= "<a href=\"Loxense-ReferentielsConformite.php\" class=\"btn btn-admin btn-principal\">" .
					'<span class=\"me-1\">' . 
					couperLibelle( $L_Gestion_Referentiels_Conformite ) .
					"</span>";

				if ( $PageHTML->estAdministrateur() ) {
					include( DIR_LIBRAIRIES . '/Class_ReferentielsConformite_PDO.inc.php' );
					$Referentiels = new ReferentielsConformite();

					$Total = $Referentiels->totalReferentiels();

					$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class . "\">" . $Total . "</span></span>";
				}

				$texteHTML .= "</a>";
			}

			$texteHTML .= "</div>" .
				"</div>";

		}

		$Class = 'bg-vert_normal';

		print( json_encode( array( 'statut' => 'success', 'texteHTML' => $texteHTML ) ) );
	} catch( Exception $e ) {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $e->getMessage()
			) );
	}
	exit();


 case 'AJAX_Tableau_Bord_Utilisateur':
	include( DIR_LIBRAIRIES . '/Class_CartographiesRisques_PDO.inc.php' );

	$objCartographies = new CartographiesRisques();

	$texteHTML = '<form name="fRechCarto" class="form-horizontal">' .
		'<div class="form-group">' .
		'<div class="col-md-offset-4 col-md-4 input-group"><input id="iRechCarto" type="text" class="form-control">' .
		'<div class="input-group-addon"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></div></div>' .
		//'Recherche' .
		'</div>' .
		'</form>';
	$Tmp = '0';


	foreach( $objCartographies->listerCartographiesRisques() as $Cartographie ) {
		$Totaux = $objCartographies->suiviGlobal( $Cartographie->crs_id );

		$texteHTML .= '<div class="tableau_synthese cartogaphie" id="' . $Cartographie->crs_id . '">' .
			'<p class="titre">' . $Cartographie->crs_libelle . ' - ' . $Cartographie->crs_periode .
			' - ' . $Cartographie->crs_version . '</p>' .
//		'<p class="titre"><select class="form-control">' .
//		'<option>' . $Cartographie->crs_libelle . ' - ' . $Cartographie->crs_periode . ' - ' . $Cartographie->crs_version . '</option>' .
//		'</select></p>' .
//			'<table class="table">' .
//			'<tbody>';
			'<div class="corps">' ;

		if ( $Totaux->risques_a_evaluer > 0 ) $Pourcentage = ( $Totaux->risques_evalues / $Totaux->risques_a_evaluer ) * 100;
		else $Pourcentage = 0;

		$Couleur = '';

		if ( $Pourcentage < 50 ) $Couleur = $Couleur = ' bg-orange_normal';
		if ( $Pourcentage > 75 ) $Couleur = $Couleur = ' bg-vert_normal';

		$_URL = URL_BASE . DIRECTORY_SEPARATOR . 'Loxense-AppreciationRisques.php?crs_id=' . $Cartographie->crs_id;
//		$texteHTML .= '<tr><td><a class="btn btn-outline-title" href="' . $_URL . '">' . $L_Total_Risques_A_Evaluer_Evalues . '</a></td><td><a class="btn btn-outline-button" href="' . $_URL . '"><span class="badge">' . $Totaux->risques_a_evaluer . '</span> / <span class="badge' . $Couleur . '">' . $Totaux->risques_evalues . '</span></a></td></tr>';
		$texteHTML .= '<a class="btn btn-outline-title" href="' . $_URL . '"><span class="me-1">' . $L_Total_Risques_A_Evaluer_Evalues . '</span><span class="ms-1"><span class="badge bg-secondary">' . $Totaux->risques_a_evaluer . '</span> / <span class="badge' . $Couleur . '">' . $Totaux->risques_evalues . '</span></span></a>';
		

		if ( $Totaux->risques_a_traiter > 0 ) $Pourcentage = ( $Totaux->risques_couverts / $Totaux->risques_a_traiter ) * 100;
		else $Pourcentage = 0;

		$Couleur = '';

		if ( $Pourcentage < 50 ) $Couleur = $Couleur = ' bg-orange_normal';
		if ( $Pourcentage > 75 ) $Couleur = $Couleur = ' bg-vert_normal';

		$_URL = URL_BASE . DIRECTORY_SEPARATOR . 'Loxense-TraitementRisques.php?crs_id=' . $Cartographie->crs_id;
//		$texteHTML .= '<tr><td><a class="btn btn-outline-title" href="' . $_URL . '">' . $L_Total_Risques_A_Traiter_Couverts . '</a></td><td><a class="btn btn-outline-button" href="' . $_URL . '"><span class="badge">' . $Totaux->risques_a_traiter . '</span> / <span class="badge' . $Couleur . '">' . $Totaux->risques_couverts . '</span></a></td></tr>';
		$texteHTML .= '<a class="btn btn-outline-title" href="' . $_URL . '"><span class="me-1">' . $L_Total_Risques_A_Traiter_Couverts . '</span><span class="ms-1"><span class="badge bg-secondary">' . $Totaux->risques_a_traiter . '</span> / <span class="badge' . $Couleur . '">' . $Totaux->risques_couverts . '</span></span></a>';
		

		if ( $Totaux->mesures > 0 ) $Pourcentage = ( $Totaux->mesures_en_place / $Totaux->mesures ) * 100;
		else $Pourcentage = 0;

		$Couleur = '';

		if ( $Pourcentage < 50 ) $Couleur = $Couleur = ' bg-orange_normal';
		if ( $Pourcentage > 75 ) $Couleur = $Couleur = ' bg-vert_normal';

		$_URL = URL_BASE . DIRECTORY_SEPARATOR . 'Loxense-TraitementRisques.php?crs_id=' . $Cartographie->crs_id;
//		$texteHTML .= '<tr><td><a class="btn btn-outline-title" href="' . $_URL . '">' . $L_Total_Mesures_Pas_En_Place_En_Place . '</a></td><td><a class="btn btn-outline-button" href="' . $_URL . '"><span class="badge">' . $Totaux->mesures . '</span> / <span class="badge' . $Couleur . '">' . $Totaux->mesures_en_place . '</span></a></td></tr>';
		$texteHTML .= '<a class="btn btn-outline-title" href="' . $_URL . '"><span class="me-1">' . $L_Total_Mesures_Pas_En_Place_En_Place . '</span><span class="ms-1"><span class="badge bg-secondary">' . $Totaux->mesures . '</span> / <span class="badge' . $Couleur . '">' . $Totaux->mesures_en_place . '</span></span></a>';
		

		if ( $Totaux->actions_actives > 0 ) $Pourcentage = ( $Totaux->actions_cloturees / $Totaux->actions_actives ) * 100;
		else $Pourcentage = 0;

		$Couleur = '';

		if ( $Pourcentage < 50 ) $Couleur = $Couleur = ' bg-orange_normal';
		if ( $Pourcentage > 75 ) $Couleur = $Couleur = ' bg-vert_normal';

		$_URL = URL_BASE . DIRECTORY_SEPARATOR . 'Loxense-Actions.php?crs_id=' . $Cartographie->crs_id;
//		$texteHTML .= '<tr><td><a class="btn btn-outline-title" href="' . $_URL . '">' . $L_Total_Actions_Actives_Cloturees . '</a></td><td><a class="btn btn-outline-button" href="' . $_URL . '"><span class="badge">' . $Totaux->actions_actives . '</span> / <span class="badge' . $Couleur . '">' . $Totaux->actions_cloturees . '</span></a></td></tr>';
		$texteHTML .= '<a class="btn btn-outline-title" href="' . $_URL . '"><span class="me-1">' . $L_Total_Actions_Actives_Cloturees . '</span><span class="ms-1"><span class="badge bg-secondary">' . $Totaux->actions_actives . '</span> / <span class="badge' . $Couleur . '">' . $Totaux->actions_cloturees . '</span></span></a>';
		

		$texteHTML .= //'</tbody>' .
			//'</table>' .
			'</div>' .
			'</div>';

	}

	print( json_encode( array( 'statut' => 'success', 'texteHTML' => $texteHTML ) ) );

	break;
}


function couperLibelle( $Libelle, $Limite = 38 ) {
	$Taille = mb_strlen( $Libelle );

	if ( $Taille > $Limite ) {
		$Texte = mb_substr( $Libelle, 0, $Limite );
		$Texte = '<span title="' . $Libelle . '">' . $Texte . '&hellip;</span>';
	} else {
		$Texte = $Libelle;
	}

	return $Texte;
}



?>