<?php

/**
* Ce script gère la page d'accueil de l'outil et donne accès à toutes les fonctions 
* disponibles à l'utilisateur connecté.
*
* \license Copyleft Loxense
* \author Pierre-Luc MARY
* \package MySecDash
* \version 1.0
* \date 2015-07-23
*
*/

// Charge les constantes du projet.
include( 'Constants.inc.php' );

include( DIR_LIBRAIRIES . '/Loxense-Entete-Standard.php' );

// Charge les libellés en fonction de la langue sélectionnée.
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MySecDash-Connexion.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_' . basename( $Script ) );


// Exécute l'action identifiée
switch( $Action ) {
 default:
	print( $PageHTML->construireEnteteHTML( $L_Dashboard, '' ) .
	$PageHTML->construireNavbarJson('Logo-MySecDash.svg', 'nav-items.json') );

	if ( array_key_exists( 'notification', $_GET ) ) {
		if ( isset( $_POST[ 'Message'] ) and isset( $_POST[ 'Type_Message' ] ) ) {
			print( $PageHTML->afficherNotification( $_POST[ 'Message'], $_POST[ 'Type_Message' ] ) );
		}
	}


	print( '<div id="titre_ecran" class="container-fluid" data-admin="' . $PageHTML->estAdministrateur() . '">' .
		"<h1 style=\"margin-top: 0;\" class=\"fg_couleur_2\">" . $L_Bienvenue . "&nbsp;(" . $PageHTML->Nom_Outil . ")</h1>\n" .
		'<script src="' . URL_LIBRAIRIES . '/js/MySecDash-Principal.js"></script>' .
		'<ul class="nav nav-tabs">' .
		'<li class="nav-item"><a id="onglet-administrateur" class="nav-link active" href="#">' . $L_Gestion . '</a></li>' .
		'<li class="nav-item"><a id="onglet-utilisateur" class="nav-link" href="#">' . $L_Visualisation . '</a></li>' .
		'</ul>' .
		'</div>' .
		'<div id="corps_tableau" class="container-fluid" style="padding: 19px 0; margin-top: 9px;">' .
		"</div>\n".
		$PageHTML->construireFooter() .
		$PageHTML->construirePiedHTML() );

	break;


 case 'AJAX_Tableau_Bord_Admin':
	$texteHTML = '';

	try {
		$Class = 'bg-vert_normal';

/*		if ( isset( $Permissions['MySecDash-CriteresValorisationActifs.php'] )
			|| isset( $Permissions['MySecDash-CriteresAppreciationAccepationRisques.php'] )
			|| isset( $Permissions['MySecDash-GrillesImpacts.php'] )
			|| isset( $Permissions['MySecDash-GrillesVraisemblances.php'] )
			|| isset( $Permissions['MySecDash-CartographiesRisques.php'] )
			|| isset( $Permissions['MySecDash-ActifsPrimordiaux.php'] )
			|| isset( $Permissions['MySecDash-ActifsSupports.php'] )
			|| isset( $Permissions['MySecDash-EvenementsRedoutes.php'] )
			//|| isset( $Permissions['MySecDash-SourcesMenaces.php'] )
			//|| isset( $Permissions['MySecDash-PartiesPrenantes.php'] )
			|| isset( $Permissions['MySecDash-AppreciationRisques.php'] )
			|| isset( $Permissions['MySecDash-TraitementRisques.php'] )
			|| isset( $Permissions['MySecDash-EditionsRisques.php'] )
			|| isset( $Permissions['MySecDash-MatricesRisques.php'] )
			) {

			$texteHTML .= "<div class=\"tableau_synthese\">" .
				"<p class=\"titre\">" . $L_Gestion_Risques . "</p>\n" .
				"<div class=\"corps\">\n";


			if ( isset( $Permissions['MySecDash-CriteresValorisationActifs.php'] ) ) {
				$texteHTML .= "<a href=\"MySecDash-CriteresValorisationActifs.php\" class=\"btn btn-admin btn-principal\">" .
					"<span class=\"me-1\">" .
					( ( $PageHTML->estAdministrateur() ) ? couperLibelle( $L_Gestion_Criteres_Valorisation_Actifs, 39 ) : $L_Gestion_Criteres_Valorisation_Actifs ) .
					"</span>";

				if ( $PageHTML->estAdministrateur() ) {
					include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MySecDash-CriteresValorisationActifs.php' );
					include( DIR_LIBRAIRIES . '/Class_CriteresValorisationActifs_PDO.inc.php' );
					$CriteresValorisationActifs = new CriteresValorisationActifs();

					$Total = $CriteresValorisationActifs->totalCriteresValorisationActifs();

					$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class . "\">" . $Total . "</span></span>";
				}

				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['MySecDash-CriteresAppreciationAcceptationRisques.php'] ) ) {
				$texteHTML .= "<a href=\"MySecDash-CriteresAppreciationAcceptationRisques.php\" class=\"btn btn-admin btn-principal\">" .
					"<span class=\"me-1\">" .
					( ( $PageHTML->estAdministrateur() ) ? couperLibelle( $L_Gestion_Criteres_Appreciation_Acceptation_Risques, 39 ) : $L_Gestion_Criteres_Appreciation_Acceptation_Risques ) .
					"</span>";

				if ( $PageHTML->estAdministrateur() ) {
					include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MySecDash-CriteresAppreciationAcceptationRisques.php' );
					include( DIR_LIBRAIRIES . '/Class_CriteresAppreciationRisques_PDO.inc.php' );
					$CriteresAppreciationRisques = new CriteresAppreciationRisques();

					$Total = $CriteresAppreciationRisques->totalRepresentationNiveauxRisque();

					$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class . "\">" . $Total . "</span></span>";
				}

				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['MySecDash-GrillesImpacts.php'] ) ) {
				$texteHTML .= "<a href=\"MySecDash-GrillesImpacts.php\" class=\"btn btn-admin btn-principal\">" .
					"<span class=\"me-1\">" .
					( ( $PageHTML->estAdministrateur() ) ? couperLibelle( $L_Gestion_Grilles_Impacts, 39 ) : $L_Gestion_Grilles_Impacts ) .
					"</span>";

				if ( $PageHTML->estAdministrateur() ) {
					include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MySecDash-GrillesImpacts.php' );
					include_once( DIR_LIBRAIRIES . '/Class_CriteresAppreciationRisques_PDO.inc.php' );
					$CriteresAppreciationRisques = new CriteresAppreciationRisques();

					$Total = $CriteresAppreciationRisques->totalCriteresImpact();

					$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class . "\">" . $Total . "</span></span>";
				}

				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['MySecDash-GrillesVraisemblances.php'] ) ) {
				$texteHTML .= "<a href=\"MySecDash-GrillesVraisemblances.php\" class=\"btn btn-admin btn-principal\">" .
					"<span class=\"me-1\">" .
					( ( $PageHTML->estAdministrateur() ) ? couperLibelle( $L_Gestion_Grilles_Vraisemblances, 39 ) : $L_Gestion_Grilles_Vraisemblances ) .
					"</span>";

				if ( $PageHTML->estAdministrateur() ) {
					include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MySecDash-GrillesVraisemblances.php' );
					include_once( DIR_LIBRAIRIES . '/Class_CriteresAppreciationRisques_PDO.inc.php' );
					$CriteresAppreciationRisques = new CriteresAppreciationRisques();

					$Total = $CriteresAppreciationRisques->totalCriteresVraisemblance();

					$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class . "\">" . $Total . "</span></span>";
				}

				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['MySecDash-CartographiesRisques.php'] ) ) {
				$texteHTML .= "<a href=\"MySecDash-CartographiesRisques.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					( ( $PageHTML->estAdministrateur() ) ? couperLibelle( $L_Gestion_Cartographies_Risques, 26 ) : $L_Gestion_Cartographies_Risques ) . "</span>";

				if ( $PageHTML->estAdministrateur() ) {
					include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MySecDash-CartographiesRisques.php' );
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


			if ( isset( $Permissions['MySecDash-ActifsPrimordiaux.php'] ) ) {
				$texteHTML .= "<a href=\"MySecDash-ActifsPrimordiaux.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					( ( $PageHTML->estAdministrateur() ) ? couperLibelle( $L_Gestion_Actifs_Primordiaux, 39 ) : $L_Gestion_Actifs_Primordiaux ) . "</span>";

				if ( $PageHTML->estAdministrateur() ) {
					include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MySecDash-ActifsPrimordiaux.php' );
					include( DIR_LIBRAIRIES . '/Class_ActifsPrimordiaux_PDO.inc.php' );
					$objActifsPrimordiaux = new ActifsPrimordiaux();

					$Total = $objActifsPrimordiaux->totalActifsPrimordiaux();

					$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class .
						"\">" . $Total . "</span></span>";
				}

				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['MySecDash-ActifsSupports.php'] ) ) {
				$texteHTML .= "<a href=\"MySecDash-ActifsSupports.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Gestion_Actifs_Supports, 39 ) . "</span>";

				if ( $PageHTML->estAdministrateur() ) {
					include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MySecDash-ActifsSupports.php' );
					include( DIR_LIBRAIRIES . '/Class_ActifsSupports_PDO.inc.php' );
					$objActifsSupports = new ActifsSupports();

					$Total = $objActifsSupports->totalActifsSupports();

					$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class .
						"\">" . $Total . "</span></span>";
				}

				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['MySecDash-EvenementsRedoutes.php'] ) ) {
				$texteHTML .= "<a href=\"MySecDash-EvenementsRedoutes.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Gestion_Evenements_Redoutes, 39 ) . "</span>";

				if ( $PageHTML->estAdministrateur() ) {
					include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MySecDash-EvenementsRedoutes.php' );
					include( DIR_LIBRAIRIES . '/Class_EvenementsRedoutes_PDO.inc.php' );
					$objEvenementsRedoutes = new EvenementsRedoutes();

					$Total = $objEvenementsRedoutes->totalEvenementsRedoutes();

					$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class .
						"\">" . $Total . "</span></span>";
				}

				$texteHTML .= "</a>";
			}
			
*/
/*
			if ( isset( $Permissions['MySecDash-PartiesPrenantes.php'] ) ) {
				$texteHTML .= "<a href=\"MySecDash-PartiesPrenantes.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Gestion_Parties_Prenantes, 39 ) . "</span>";
					
					if ( $PageHTML->estAdministrateur() ) {
						include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MySecDash-PartiesPrenantes.php' );
						include( DIR_LIBRAIRIES . '/Class_PartiesPrenantes_PDO.inc.php' );
						$objPartiesPrenantes = new PartiesPrenantes();
						
						$Total = $objPartiesPrenantes->totalPartiesPrenantes();
						
						$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class .
						"\">" . $Total . "</span></span>";
					}
					
					$texteHTML .= "</a>";
			}
*/
/*
			if ( isset( $Permissions['MySecDash-AppreciationRisques.php'] ) ) {
				$texteHTML .= "<a href=\"MySecDash-AppreciationRisques.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Gestion_Appreciation_Risques, 39 ) . "</span>";

				if ( $PageHTML->estAdministrateur() ) {
					include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MySecDash-AppreciationRisques.php' );
					include( DIR_LIBRAIRIES . '/Class_Risques_PDO.inc.php' );
					$objRisques = new Risques();

					$Total = $objRisques->totalRisques();

					$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class .
						"\">" . $Total . "</span></span>";
				}

				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['MySecDash-TraitementRisques.php'] ) ) {
				$texteHTML .= "<a href=\"MySecDash-TraitementRisques.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Gestion_Traitement_Risques, 39 ) . "</span>";

				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['MySecDash-EditionsRisques.php'] ) ) {
				$texteHTML .= "<a href=\"MySecDash-EditionsRisques.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Gestion_Editions_Risques, 39 ) . "</span>";

				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['MySecDash-MatricesRisques.php'] ) ) {
				$texteHTML .= "<a href=\"MySecDash-MatricesRisques.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Visualisation_Matrices_Risques, 39 ) . "</span>";

				$texteHTML .= "</a>";
			}

			$texteHTML .=  "</div>" .
				"</div>";

		}
*/
		$Class = 'bg-vert_normal';


		if ( isset( $Permissions['MySecDash-Actions.php'] )
			|| isset( $Permissions['MySecDash-EditionsActions.php'] ) ) {

			$texteHTML .= "<div class=\"tableau_synthese\">" .
				"<p class=\"titre\">" . $L_Gestion_Actions . "</p>\n" .
				"<div class=\"corps\">\n";


			if ( isset( $Permissions['MySecDash-Actions.php'] ) ) {
				$texteHTML .= "<a href=\"MySecDash-Actions.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Gestion_Actions, 39 ) . "</span>";

				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['MySecDash-EditionsActions.php'] ) ) {
				$texteHTML .= "<a href=\"MySecDash-EditionsActions.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Edition_Actions, 39 ) . "</span>";

				$texteHTML .= "</a>";
			}

			$texteHTML .=  "</div>" .
				"</div>";

		}
		
		$Class = 'bg-vert_normal';
		
		
		if ( isset( $Permissions['MySecDash-Conformite.php'] )
			|| isset( $Permissions['MySecDash-EditionConformite.php'] ) ) {
				
			$texteHTML .= "<div class=\"tableau_synthese\">" .
				"<p class=\"titre\">" . $L_Gestion_Conformite . "</p>\n" .
				"<div class=\"corps\">\n";
			
			
			if ( isset( $Permissions['MySecDash-Conformite.php'] ) ) {
				$texteHTML .= "<a href=\"MySecDash-Conformite.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Gestion_Conformite, 39 ) . "</span>";
					
					$texteHTML .= "</a>";
			}
			
			
			if ( isset( $Permissions['MySecDash-EditionConformite.php'] ) ) {
				$texteHTML .= "<a href=\"MySecDash-EditionConformite.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Edition_Conformite, 39 ) . "</span>";
					
					$texteHTML .= "</a>";
			}
			
			$texteHTML .=  "</div>" .
				"</div>";
			
		}
			
		$Class = 'bg-vert_normal';


/*		if ( isset( $Permissions['MySecDash-AppreciationRisquesTags.php'] )
			|| isset( $Permissions['MySecDash-ActifsPrimordiauxTags.php'] )
			|| isset( $Permissions['MySecDash-ActifsSupportsTags.php'] )
			|| isset( $Permissions['MySecDash-TraitementRisquesTags.php'] ) ) {

			$texteHTML .= "<div class=\"tableau_synthese\">" .
				"<p class=\"titre\">" . $L_Vision_Consolidee . "</p>\n" .
				"<div class=\"corps\">\n";


			if ( isset( $Permissions['MySecDash-ActifsPrimordiauxTags.php'] ) ) {
				$texteHTML .= "<a href=\"MySecDash-ActifsPrimordiauxTags.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Actifs_Primordiaux, 39 ) . "</span>";

				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['MySecDash-ActifsSupportsTags.php'] ) ) {
				$texteHTML .= "<a href=\"MySecDash-ActifsSupportsTags.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Actifs_Supports, 39 ) . "</span>";

				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['MySecDash-AppreciationRisquesTags.php'] ) ) {
				$texteHTML .= "<a href=\"MySecDash-AppreciationRisquesTags.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Appreciation_Risques, 39 ) . "</span>";

				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['MySecDash-TraitementRisquesTags.php'] ) ) {
				$texteHTML .= "<a href=\"MySecDash-TraitementRisquesTags.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Traitement_Risques, 39 ) . "</span>";

				$texteHTML .= "</a>";
			}

			$texteHTML .=  "</div>" .
				"</div>";

		}
*/
		$Class = 'bg-vert_normal';


		if ( isset( $Permissions['MySecDash-Societes.php'] )
			|| isset( $Permissions['MySecDash-Entites.php'] )
			|| isset( $Permissions['MySecDash-Civilites.php'] )
			|| isset( $Permissions['MySecDash-Utilisateurs.php'] )
			|| isset( $Permissions['MySecDash-Profils.php'] )
			|| isset( $Permissions['MySecDash-ApplicationsInternes.php'] )
			//|| isset( $Permissions['MySecDash-Gestionnaires.php'] )
			|| isset( $Permissions['MySecDash-Etiquettes.php'] ) ) {
				$texteHTML .= "<div class=\"tableau_synthese\">" .
					"<p class=\"titre\">" . $L_Gestion_Controles_Acces . "</p>\n" .
					"<div class=\"corps\">\n";

			if ( isset( $Permissions['MySecDash-Societes.php'] ) ) {
				$texteHTML .= "<a href=\"MySecDash-Societes.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Gestion_Societes ) . "</span>";

				if ( $PageHTML->estAdministrateur() ) {
					include( DIR_LIBRAIRIES . '/Class_HBL_Societes_PDO.inc.php' );
					$Societes = new HBL_Societes();

					$Total = $Societes->totalSocietes();

					$Class = "bg-vert_normal";

					$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class . "\">" . $Total .
						"</span></span>";

				}

				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['MySecDash-Entites.php'] ) ) {
				$texteHTML .= "<a href=\"MySecDash-Entites.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Gestion_Entites ) . "</span>";
					
					if ( $PageHTML->estAdministrateur() ) {
						include( DIR_LIBRAIRIES . '/Class_HBL_Entites_PDO.inc.php' );
						$Entites = new HBL_Entites();
						
						$Total = $Entites->totalEntites();
						$Class = "bg-vert_normal";
						
						$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class . "\">" . $Total .
							"</span></span>";
						
					}
					
					$texteHTML .= "</a>";
			}
			
			
			if ( isset( $Permissions['MySecDash-Civilites.php'] ) ) {
				$texteHTML .= "<a href=\"MySecDash-Civilites.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Gestion_Civilites ) . "</span>";

				if ( $PageHTML->estAdministrateur() ) {
					include( DIR_LIBRAIRIES . '/Class_HBL_Civilites_PDO.inc.php' );
					$Civilites = new HBL_Civilites();

					$Total = $Civilites->totalCivilites();
					$Maximum = $PageHTML->recupererParametre( 'limitation_civilites' );

					$Class = "bg-vert_normal";

					$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class . "\">" . $Total .
						"</span></span>";
				}

				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['MySecDash-ApplicationsInternes.php'] ) ) {
				$texteHTML .= "<a href=\"MySecDash-ApplicationsInternes.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Gestion_ApplicationsInternes ) . "</span>";

				if ( $PageHTML->estAdministrateur() ) {
					include( DIR_LIBRAIRIES . '/Class_HBL_ApplicationsInternes_PDO.inc.php' );
					$Applications = new HBL_ApplicationsInternes();

					$Total = $Applications->totalApplications();

					$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge bg-vert_normal\">" .
						$Total . "</span></span>";
				}

				$texteHTML .= "</a>";
			}


			if ( isset( $Permissions['MySecDash-Profils.php'] ) ) {
				$texteHTML .= "<a href=\"MySecDash-Profils.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
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


			if ( isset( $Permissions['MySecDash-Utilisateurs.php'] ) ) {
				$texteHTML .= "<a href=\"MySecDash-Utilisateurs.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Gestion_Utilisateurs ) . "</span>";

				if ( $PageHTML->estAdministrateur() ) {
					include( DIR_LIBRAIRIES . '/Class_HBL_Identites_PDO.inc.php' );
					$Identites = new HBL_Identites();

					$Total = $Identites->totalIdentites();
					$Maximum = $PageHTML->recupererParametre( 'limitation_utilisateurs' );

					$Class = "bg-vert_normal";

					$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class . "\">" .
						$Total . "</span></span>";
				}

				$texteHTML .= "</a>";
			}


/*			if ( isset( $Permissions['MySecDash-Gestionnaires.php'] ) ) {
				$texteHTML .= "<a href=\"MySecDash-Gestionnaires.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
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
*/

			if ( isset( $Permissions['MySecDash-Etiquettes.php'] ) ) {
				$texteHTML .= "<a href=\"MySecDash-Etiquettes.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
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

		if ( isset( $Permissions['MySecDash-Parametres.php'] )
			|| isset( $Permissions['MySecDash-TypesActifSupport.php'] )
			|| isset( $Permissions['MySecDash-MenacesGeneriques.php'] )
			|| isset( $Permissions['MySecDash-VulnerabilitesGeneriques.php'] )
			|| isset( $Permissions['MySecDash-SourcesMenaces.php'] )
			//|| isset( $Permissions['MySecDash-ObjectifsVises.php'] )
			|| isset( $Permissions['MySecDash-RisquesGeneriques.php'] )
			|| isset( $Permissions['MySecDash-TypesTraitementRisques.php'] )
			|| isset( $Permissions['MySecDash-ImpactsGeneriques.php'] )
			|| isset( $Permissions['MySecDash-MesuresGeneriques.php'] )
			|| isset( $Permissions['MySecDash-ReferentielsConformite.php'] ) ) {

			$texteHTML .= "<div class=\"tableau_synthese\">" .
				"<p class=\"titre\">" . $L_Referentiel_Interne . "</p>\n" .
				"<div class=\"corps\">\n";


			if ( isset( $Permissions['MySecDash-Parametres.php'] ) ) {
				$texteHTML .= "<a href=\"MySecDash-Parametres.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Gestion_Parametres_Base ) . "</span>";

				if ( $PageHTML->estAdministrateur() ) {
					$Total = $PageHTML->totalParametres();

					$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class . "\">" .
						$Total . "</span></span>";
				}

				$texteHTML .= "</a>";
			}


/*			if ( isset( $Permissions['MySecDash-TypesActifSupport.php'] ) ) {
				$texteHTML .= "<a href=\"MySecDash-TypesActifSupport.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
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


			if ( isset( $Permissions['MySecDash-TypesMenaceGenerique.php'] ) ) {
				$texteHTML .= "<a href=\"MySecDash-TypesMenaceGenerique.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
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


			if ( isset( $Permissions['MySecDash-MenacesGeneriques.php'] ) ) {
				$texteHTML .= "<a href=\"MySecDash-MenacesGeneriques.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
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


			if ( isset( $Permissions['MySecDash-VulnerabilitesGeneriques.php'] ) ) {
				$texteHTML .= "<a href=\"MySecDash-VulnerabilitesGeneriques.php\" class=\"btn btn-admin btn-principal\">" .
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
			
			if ( isset( $Permissions['MySecDash-SourcesMenaces.php'] ) ) {
				$texteHTML .= "<a href=\"MySecDash-SourcesMenaces.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
					couperLibelle( $L_Gestion_Sources_Menaces, 39 ) . "</span>";
					
					if ( $PageHTML->estAdministrateur() ) {
						include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_MySecDash-SourcesMenaces.php' );
						include( DIR_LIBRAIRIES . '/Class_SourcesMenaces_PDO.inc.php' );
						$objSourcesMenaces = new SourcesMenaces();
						
						$Total = $objSourcesMenaces->totalSourcesMenaces();
						
						$texteHTML .= "&nbsp;<span class=\"ms-1\"><span class=\"badge " . $Class .
						"\">" . $Total . "</span></span>";
					}
					
					$texteHTML .= "</a>";
			}
*/			
			
/*			
			if ( isset( $Permissions['MySecDash-SourcesRisques.php'] ) ) {
				$texteHTML .= "<a href=\"MySecDash-SourcesRisques.php\" class=\"btn btn-admin btn-principal\">" .
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
			
			
			if ( isset( $Permissions['MySecDash-ObjectifsVises.php'] ) ) {
				$texteHTML .= "<a href=\"MySecDash-ObjectifsVises.php\" class=\"btn btn-admin btn-principal\">" .
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

/*			if ( isset( $Permissions['MySecDash-RisquesGeneriques.php'] ) ) {
				$texteHTML .= "<a href=\"MySecDash-RisquesGeneriques.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
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


			if ( isset( $Permissions['MySecDash-TypesTraitementRisques.php'] ) ) {
				$texteHTML .= "<a href=\"MySecDash-TypesTraitementRisques.php\" class=\"btn btn-admin btn-principal\">" .
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


			if ( isset( $Permissions['MySecDash-ImpactsGeneriques.php'] ) ) {
				$texteHTML .= "<a href=\"MySecDash-ImpactsGeneriques.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
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


			if ( isset( $Permissions['MySecDash-MesuresGeneriques.php'] ) ) {
				$texteHTML .= "<a href=\"MySecDash-MesuresGeneriques.php\" class=\"btn btn-admin btn-principal\"><span class=\"me-1\">" .
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


			if ( isset( $Permissions['MySecDash-ReferentielsConformite.php'] ) ) {
				$texteHTML .= "<a href=\"MySecDash-ReferentielsConformite.php\" class=\"btn btn-admin btn-principal\">" .
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
*/
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
	$texteHTML = '';

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