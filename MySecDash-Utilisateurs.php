<?php

/**
* Ce script gère les Utilisateurs de MySecDash.
*
* \license Copyleft Loxense
* \author Pierre-Luc MARY
* \package MySecDash
* \version 1.0
* \date 2015-10-15
*/

// Charge les constantes du projet.
include( 'Constants.inc.php' );

include( DIR_LIBRAIRIES . '/Loxense-Entete-Standard.php' );


// Charge les libellés en fonction de la langue sélectionnée.
include( HBL_DIR_LABELS . '/' . $_SESSION[ 'Language' ] . '_HBL_Societes.inc.php' );
include( HBL_DIR_LABELS . '/' . $_SESSION[ 'Language' ] . '_HBL_Entites.inc.php' );
include( HBL_DIR_LABELS . '/' . $_SESSION[ 'Language' ] . '_HBL_Civilites.inc.php' );
include( HBL_DIR_LABELS . '/' . $_SESSION[ 'Language' ] . '_HBL_Identites.inc.php' );
include( HBL_DIR_LABELS . '/' . $_SESSION[ 'Language' ] . '_HBL_Profils.inc.php' );
include( DIR_LIBELLES . '/' . $_SESSION[ 'Language' ] . '_' . basename( $Script ) );

// Charge les classes utiles à cet écran.
include_once( DIR_LIBRAIRIES . '/Class_HBL_Societes_PDO.inc.php' );
include_once( DIR_LIBRAIRIES . '/Class_HBL_Entites_PDO.inc.php' );
include_once( DIR_LIBRAIRIES . '/Class_HBL_Civilites_PDO.inc.php' );
include_once( DIR_LIBRAIRIES . '/Class_HBL_Identites_PDO.inc.php' );
include_once( DIR_LIBRAIRIES . '/Class_HBL_Identites_Societes_PDO.inc.php' );
include_once( DIR_LIBRAIRIES . '/Class_HBL_Identites_Entites_PDO.inc.php' );
include_once( DIR_LIBRAIRIES . '/Class_HBL_Identites_Profils_PDO.inc.php' );
include_once( DIR_LIBRAIRIES . '/Class_HBL_Profils_PDO.inc.php' );
//include_once( DIR_LIBRAIRIES . '/Class_Gestionnaires_PDO.inc.php' );
include_once( DIR_LIBRAIRIES . '/Class_Etiquettes_PDO.inc.php' );


// Création des autres objets utiles à la gestion des Utilisateurs.
$objIdentites = new HBL_Identites();
$objSocietes = new HBL_Societes();
$objCivilites = new HBL_Civilites();
$objEntites = new HBL_Entites();
$objProfils = new HBL_Profils();
$objIdentites_Societes = new HBL_Identites_Societes();
$objIdentites_Entites = new HBL_Identites_Entites();
$objIdentites_Profils = new HBL_Identites_Profils();


// Définit le format des colonnes du tableau central.
$Format_Colonnes[ 'Prefixe' ] = 'IDN';
$Format_Colonnes[ 'Fonction_Ouverture' ] = 'ouvrirChamp';
$Format_Colonnes[ 'Id' ] = array( 'nom' => 'idn_id' );
/* $Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'sct_nom', 'titre' => $L_Societe, 'taille' => '2',
	'triable' => 'oui', 'tri_actif' => 'oui', 'sens_tri' => 'entity', 'type' => 'select',
	'fonction' => 'listerEntites', 'modifiable' => 'oui' ); */
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'idn_login', 'titre' => $L_Nom_Utilisateur, 'taille' => '2',
	'triable' => 'oui', 'sens_tri' => 'username', 'type' => 'input', 'modifiable' => 'oui' );
if ( $_SESSION['idn_super_admin'] === TRUE ) $Modifiable = 'oui';
else $Modifiable ='non';
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'cvl_label', 'titre' => $L_Civilite, 'taille' => '4',
	'triable' => 'oui', 'sens_tri' => 'civilite', 'type' => 'select', 'fonction' => 'listerCivilites',
	'modifiable' => 'oui' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'idn_super_admin', 'titre' => $L_Super_Admin, 'taille' => '2',
	'triable' => 'oui', 'sens_tri' => 'administrateur', 'type' => 'select', 'liste' => '0='.$L_No.';1='.$L_Yes,
	'affichage' => 'img', 'modifiable' => $Modifiable );	
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'idn_desactiver', 'titre' => $L_Desactiver, 'taille' => '2',
	'triable' => 'oui', 'sens_tri' => 'desactive', 'type' => 'select', 'liste' => '0='.$L_No.';1='.$L_Yes,
	'modifiable' => 'oui' );
$Format_Colonnes[ 'Colonnes' ][] = array( 'nom' => 'statut', 'titre' => $L_Status, 'affichage' => 'img',
	'taille' => '1' );
$Format_Colonnes[ 'Actions' ] = array( 'taille' => '1', 'titre' => $L_Actions,
	'boutons' => array( 'modifier' => $Droit_Modifier, 'supprimer' => $Droit_Supprimer ) );


// Exécute l'action identifie
switch( $Action ) {
 default:
	if ( $_SESSION['idn_super_admin'] === TRUE ) {
		$Liste_Societes = $objSocietes->rechercherSocietes();
	} else {
		$Liste_Societes = $objSocietes->rechercherSocietes('', '', $_SESSION['idn_id'] );
	}

	if ( $Droit_Ajouter === TRUE ) {
		$Boutons_Alternatifs[] = ['class'=>'btn-ajouter', 'libelle'=>$L_Ajouter, 'glyph'=>'plus'];
	}
	$Boutons_Alternatifs[] = ['class'=>'btn-rechercher', 'libelle'=>$L_Rechercher, 'glyph'=>'search'];

	print( $PageHTML->construireEnteteHTML( $L_Gestion_Utilisateurs, $Fichiers_JavaScript ) .
		$PageHTML->construireNavbarJson('Logo-MySecDash.svg', 'nav-items.json') .
		$PageHTML->construireTitreEcran( $L_Gestion_Utilisateurs, $Liste_Societes, $Boutons_Alternatifs )
	);


	if ( $Droit_Lecture === TRUE ) {
		// Construit un tableau central vide.
		print( $PageHTML->contruireTableauVide( $Format_Colonnes ) );
	}

	print( $PageHTML->construireFooter( TRUE ) .
		$PageHTML->construirePiedHTML() );

	break;


 /* ========================================================================
 ** Réponses aux appels AJAX
 */

 case 'AJAX_Libeller':
	$Parametres = array(
		'Statut' => 'success',
		'L_Fermer' => $L_Fermer,
		'L_Titre_Ajouter' => $L_Ajouter_Utilisateur,
		'L_Titre_Modifier' => $L_Modifier_Utilisateur,
		'L_Titre_Supprimer' => $L_Supprimer_Utilisateur,
		'L_Creer' => $L_Creer,
		'L_Ajouter' => $L_Ajouter,
		'L_Modifier' => $L_Modify,
		'L_Supprimer' => $L_Delete,
		'L_Confirmer' => $L_Confirmer_Suppression_Utilisateur,
		'L_Nom' => $L_Nom,
		'L_Prenom' => $L_Prenom,
		'L_Entite' => $L_Entite,
		'L_Nom_Utilisateur' => $L_Nom_Utilisateur,
		'L_Civilite' => $L_Civilite,
		'L_Desactiver' => $L_Desactiver,
		'L_Super_Admin' => $L_Super_Admin,
		'L_Actions' => $L_Actions,
		'L_Entites' => $L_Entites,
		'L_Societes' => $L_Societes,
		'L_Societe' => $L_Societe,
		'L_Profiles' => $L_Profiles,
		'L_Aucun' => $L_Neither,
		'L_Aucune' => $L_Neither_f,
		'L_Administrateur' => $L_Administrateur,
		'L_Derniere_Connexion' => $L_Derniere_Connexion,
		'L_Date_Expiration' => $L_Date_Expiration,
		'L_Tentative' => $L_Tentative,
		'L_Activer_Utilisateur' => $L_Activer_Utilisateur,
		'L_Desactiver_Utilisateur' => $L_Desactiver_Utilisateur,
		'L_Reset' => $L_Reset,
		'L_Jamais_Connecte' => $L_Jamais_Connecte,
		'L_Date_Derniere_Connexion_Ancienne' => $L_Date_Derniere_Connexion_Ancienne,
		'L_Reinitialiser_Mot_Passe' => $L_Reinitialiser_Mot_Passe,
		'max_attempt' => $PageHTML->recupererParametre('max_attempt'),
		'account_lifetime' => $PageHTML->recupererParametre('account_lifetime'),
		'is_super_admin' => $_SESSION['idn_super_admin'],
		'L_Gestionnaires' => $L_Gestionnaires,
		'L_Courriel' => $L_Courriel,
		'L_Etiquettes' => $L_Etiquettes,
		's_sct_id' => $_SESSION['s_sct_id'],
		'Civilites' => $objCivilites->rechercherCivilites(),
		'Droits_Civilites' => $PageHTML->permissionsGroupees('MySecDash-Civilites.php'),
		'L_Tout_Cocher_Decocher' => $L_Tout_Cocher_Decocher
		);

	if ( $Droit_Lecture === TRUE ) {
		if ( isset( $_POST['charger'] ) ) {
			if ( $_POST['charger'] !== '' ) {
				try {
					$Identite = $objIdentites->detaillerIdentite( $_POST['charger'] );
	
					$Parametres['username'] = $Identite->idn_login;
					$Parametres['super_admin'] = $Identite->idn_super_admin;
					$Parametres['cvl_id'] = $Identite->cvl_id;
					$Parametres['sct_id'] = $Identite->sct_id;
					$Parametres['attempt'] = $Identite->idn_tentative;
					$Parametres['disable'] = $Identite->idn_desactiver;
					$Parametres['last_connection'] = $Identite->idn_derniere_connexion;
					$Parametres['expiration_date'] = $Identite->idn_date_expiration;
					$Parametres['updated_authentification'] = $Identite->idn_date_modification_authentifiant;
					$Parametres['is_super_admin'] = $_SESSION['idn_super_admin'];
					$Parametres['email'] = $Identite->idn_courriel;
					$Parametres['Societes'] = $objIdentites_Societes->rechercherSocietesUtilisateur( $_POST['charger'] );
					$Parametres['Entites'] = $objIdentites_Entites->rechercherEntitesUtilisateur( '*', $_POST['charger'] );
					$Parametres['Profils'] = $objIdentites_Profils->rechercherProfilsIdentite( $_POST['charger'] );
					
				} catch( Exception $e ) {
					$Parametres = array(
						'statut' => 'error',
						'texteMsg' => $e->getMessage()
						);		
				}
			} else {
				$Parametres['Societes'] = $objSocietes->rechercherSocietes();
				$Parametres['Entites'] = $objEntites->rechercherEntites('*');
				$Parametres['Profils'] = $objProfils->rechercherProfils();
				
			}
		}
	} else {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
		) );
	}

	print( json_encode( $Parametres ) );

	exit();


 case 'AJAX_Ajouter':
	if ( $Droit_Ajouter === TRUE ) {
		if ( isset($_POST['idn_login']) and isset($_POST['cvl_id']) and isset($_POST['sct_id']) ) {
			$_POST['idn_login'] = $PageHTML->controlerTypeValeur( $_POST['idn_login'], 'ASCII' );
			if ( $_POST['idn_login'] == -1 ) {
				echo json_encode( array(
				'statut' => 'error',
				'texteMsg' => $L_Invalid_Value . ' (idn_login)'
				) );

				exit();
			}

			$_POST['cvl_id'] = $PageHTML->controlerTypeValeur( $_POST['cvl_id'], 'NUMERIC' );
			if ( $_POST['cvl_id'] == -1 ) {
				echo json_encode( array(
				'statut' => 'error',
				'texteMsg' => $L_Invalid_Value . ' (cvl_id)'
				) );

				exit();
			}

			$_POST['idn_courriel'] = $PageHTML->controlerTypeValeur( $_POST['idn_courriel'], 'ASCII' );
			if ( $_POST['idn_courriel'] == -1 ) {
				echo json_encode( array(
				'statut' => 'error',
				'texteMsg' => $L_Invalid_Value . ' (idn_courriel)'
				) );

				exit();
			}

			try {
				// Crée la nouvelle identité.
				$objIdentites->majIdentite( '', $_POST['idn_login'], '', $_POST['idn_super_admin'],
					$_SESSION['s_sct_id'], $_POST['cvl_id'], $_POST['idn_courriel'] );
				
				$Id_Identity = $objIdentites->LastInsertId;

				$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_IDENTITE', 'idn_id = "' . $Id_Identity . '", ' .
					'idn_login="' . $_POST[ 'idn_login' ] . '", idn_super_admin="' . $_POST['idn_super_admin'] . '", ' .
					'cvl_id="' . $_POST[ 'cvl_id' ] . '", sct_id="' . $_POST[ 'sct_id' ] . '", idn_courriel="' . $_POST[ 'idn_courriel' ] . '"' );
			} catch (Exception $e) {
				$Message = $e->getMessage();
				
				if ( $e->getCode() == 23505 ) { // Gestion d'un doublon
					$Message = $L_ERR_DUPL_Identite;
				}

				$Resultat = array(
					'statut' => 'error',
					'texteMsg' => $Message
				);

				echo json_encode( $Resultat );
				exit();
			}

			try {
				// Créé les relations entre les Profils et l'Identité (si nécessaire).
				if ( isset($_POST['liste_profils']) and $_POST['liste_profils'] != '' ) {
					foreach ( $_POST['liste_profils'] as $Profil ) {
						$objIdentites_Profils->ajouterProfilIdentite( $Id_Identity, $Profil );

						$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_IDENTITE', 'idn_id = "' . $Id_Identity . '" <=> ' .
							'prf_id="' . $Profil . '"' );
					}
				}
			} catch (Exception $e) {
				$Message = $e->getMessage();
				
				if ( $e->getCode() == 23505 ) { // Gestion d'un doublon
					$Message = 'Pb doublon sur Profils';
				}
				
				$Resultat = array(
					'statut' => 'error',
					'texteMsg' => $Message
				);
				
				echo json_encode( $Resultat );
				exit();
			}


			try {
				// Créé les relations entre les Entités et l'Identité (si nécessaire).
				if ( isset($_POST['liste_entites']) and $_POST['liste_entites'] != '' ) {
					foreach ( $_POST['liste_entites'] as $Entite ) {
						$objIdentites_Entites->ajouterEntiteIdentite( $Id_Identity, $Entite['ent_id'], $Entite['admin'] );

						$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_IDENTITE', 'idn_id = "' . $Id_Identity . '" <=> ' .
							'ent_id="' . $Entite['ent_id'] . '", ent_admin="' . $Entite['admin'] . '"' );
					}
				}
			} catch (Exception $e) {
				$Message = $e->getMessage();
				
				if ( $e->getCode() == 23505 ) { // Gestion d'un doublon
					$Message = 'Pb doublon sur Entités';
				}
				
				$Resultat = array(
					'statut' => 'error',
					'texteMsg' => $Message
				);
				
				echo json_encode( $Resultat );
				exit();
			}
			
			
			try {
				// Créé les relations entre les Sociétés et l'Identité (si nécessaire).
				if ( isset($_POST['liste_societes']) and $_POST['liste_societes'] != '' ) {
					foreach ( $_POST['liste_societes'] as $Societe ) {
						$objIdentites_Societes->ajouterSocieteIdentite( $Id_Identity, $_SESSION['s_sct_id'] );
						
						$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_IDENTITE', 'idn_id = "' . $Id_Identity . '" <=> ' .
							'sct_id="' . $_SESSION['s_sct_id'] . '"' );
					}
				}
			} catch (Exception $e) {
				$Message = $e->getMessage();
				
				if ( $e->getCode() == 23505 ) { // Gestion d'un doublon
					$Message = 'Pb doublon sur Sociétés';
				}
				
				$Resultat = array(
					'statut' => 'error',
					'texteMsg' => $Message
				);
				
				echo json_encode( $Resultat );
				exit();
			}

			// Prépare les données à l'affichage.
			$Valeurs = new stdClass();
			$Valeurs->cvl_label = $_POST['cvl_label'];
			$Valeurs->idn_login = $_POST['idn_login'];
//			$Valeurs->statut = '<img src="' . URL_IMAGES . '/s_notice.png" alt="KO" data-toggle="tooltip" data-placement="bottom" title="' . $L_Jamais_Connecte . '" />';
			$Valeurs->statut = '<button class="btn btn-sm text-warning border-warning" title="' . $L_Jamais_Connecte . '"><i class="bi-exclamation-triangle-fill"></i></button>';
			$Valeurs->idn_desactiver = $L_No;

			if ( strtoupper($_POST['idn_super_admin']) == 'TRUE' ) $Valeurs->idn_super_admin = $L_Yes; //'<span class="glyphicon glyphicon-check"></span>';
			else $Valeurs->idn_super_admin = $L_No; //'<span class="glyphicon glyphicon-unchecked"></span>';


			// Formatte les données en une occurrence au format HTML.
			$Occurrence = $PageHTML->creerOccurrenceCorpsTableau( $Id_Identity, $Valeurs, $Format_Colonnes );

			$Limitation = $PageHTML->recupererParametre('limitation_utilisateurs');

			$Resultat = array( 'statut' => 'success',
				'texteMsg' => $L_Utilisateur_Cree,
				'texte' => $Occurrence,
				'id' => $Id_Identity,
				'droit_modifier' => $Droit_Modifier,
				'droit_supprimer' => $Droit_Supprimer,
				'limitation' => $Limitation,
				'libelle_limitation' => $L_Limitation_Licence
				);

			echo json_encode( $Resultat );
		} else {
			$Resultat = array(
				'statut' => 'error',
				'texteMsg' => $L_ERR_Champs_Obligatoires
				);

			echo json_encode( $Resultat );
			exit();
		}
	} else {
		$Resultat = array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
			);

		echo json_encode( $Resultat );
		exit();
	}
	break;


 case 'AJAX_Modifier_Champ':
	if ( $Droit_Modifier === TRUE ) {
		if ( isset($_POST['id']) && isset($_POST['source']) && isset($_POST['valeur']) ) {
			if ( $_POST['id'] == $_SESSION['idn_id'] ) {
				echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Pas_Droit_Ressource . ' (idn_id="' . $_POST['idn_id'] . '")'.' [' . __LINE__ . ']'
				) );

				$PageHTML->ecrireEvenement( 'ATP_ALERTE', 'OTP_SECURITE', $L_Pas_Droit_Ressource . ' (idn_id="' . $_POST['idn_id'] . '")'.' [' . __LINE__ . ']' );

				exit();
			}

			try {
				$objIdentites->majIdentiteParChamp($_POST['id'], $_POST['source'], $_POST['valeur']);

				$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_IDENTITE', $_POST[ 'source' ] . '="' . $_POST['valeur'] . '"' );

				$Resultat = array(
					'statut' => 'success',
					'texteMsg' => $L_Utilisateur_Modifie
					);
			} catch (Exception $e) {
				$Message = $e->getMessage();

				if ( $e->getCode() == 23505 ) { // Cas d'un doublon
					$Message = $L_ERR_DUPL_Identite;
				}

				$Resultat = array(
					'statut' => 'error',
					'texteMsg' => $Message
					);
			}
			
			echo json_encode( $Resultat );
		}
	} else {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
		) );
	}
	break;


 case 'AJAX_Supprimer':
	if ( $Droit_Supprimer === TRUE ) {
		if ( isset($_POST['id']) ) {
			$Detail_Identite = $objIdentites->detaillerIdentite( $_POST['id'] );
			
			try  {
				$objIdentites->supprimerIdentite( $_POST['id'] );
	
				$PageHTML->ecrireEvenement( 'ATP_SUPPRESSION', 'OTP_IDENTITE', 'idn_id="' . $_POST['id'] . '", ' .
					'idn_login="' . $_POST[ 'idn_login' ] . '", ' .
					'cvl_label="' . $_POST[ 'cvl_label' ] . '", ' .
					'sct_nom="' . $_POST[ 'sct_nom' ] . '" ' );
	
				$Limitation = $PageHTML->recupererParametre('limitation_utilisateurs');
	
				$Resultat = array( 'statut' => 'success',
					'titreMsg' => $L_Success,
					'texteMsg' => $L_Utilisateur_Supprime,
					'limitation' => $Limitation,
					'libelle_limitation' => $L_Limitation_Licence
					);
			} catch (Exception $e) {
				$Resultat = array( 'statut' => 'error',
					'titreMsg' => $L_Error,
					'texteMsg' => $e->getMessage() );
			}
	
			echo json_encode( $Resultat );
		} else {
			$Resultat = array( 'statut' => 'error',
				'titreMsg' => 1000,
				'texteMsg' => 'No ID specified' );
	
			echo json_encode( $Resultat );
		}
	} else {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
		) );
	}
	break;


 case 'AJAX_Trier':
	if ( $Droit_Lecture === TRUE ) {
		if ( ! isset( $_SESSION['s_sct_id'] ) ) {
			$_SESSION['s_sct_id'] = $_SESSION['sct_id'];
		}

		$Trier = $_POST[ 'trier' ];
		
		try {
			if ( ! $PageHTML->verifierSocieteAutorisee($_SESSION['s_sct_id']) ) {
				print( json_encode( array( 'Statut' => 'error',
					'texteMsg' => $L_Pas_Droit_Ressource . ' (sct_id="' . $sct_id . '")'.' [' . __LINE__ . ']' ) ) );
				
				$PageHTML->ecrireEvenement( 'ATP_ALERTE', 'OTP_SECURITE', $L_Pas_Droit_Ressource . ' (sct_id="' . $sct_id . '")'.' [' . __LINE__ . ']' );
				
				exit();
			}

			$Identites = $objIdentites->rechercherIdentites( $_SESSION['s_sct_id'], $Trier );
			$Total = $objIdentites->RowCount;

			$Texte_HTML = '';
			
			foreach ($Identites as $Occurrence) {
				// Matérialise la civilité en assemblant le prénom et le nom.
				$Occurrence->cvl_label = $Occurrence->cvl_prenom . ' ' . $Occurrence->cvl_nom;

				$Libelle_Alerte = '';

				// ===============
				// Matérialise le statut global de l'utilisateur (test les différents incidents possibles)
				
				// Teste le nombre maximum d'essai.
				$Nombre_Maximum_Essai = $PageHTML->recupererParametre( 'max_attempt' );
				if ( $Nombre_Maximum_Essai == '' ) $Nombre_Maximum_Essai = 3;

				if ( $Occurrence->idn_tentative > $Nombre_Maximum_Essai ) {
					if ( $Libelle_Alerte != '' ) $Libelle_Alerte .= ', ';
					$Libelle_Alerte .= $L_Nombre_Connexion_Atteinte;
				}

				// Teste la date d'expiration.
				if ( $Occurrence->idn_date_expiration != '0000-00-00 00:00:00' ) {
					if ( $Occurrence->idn_date_expiration < date( 'Y-m-d' ) ) {
						$Libelle_Alerte .= $L_Utilisateur_Expire . ' (' .
						 $L_Date_Expiration_Atteinte . ')';
					}
				}

				// Vérifie si l'utilisateur n'a pas expiré.
				// Date de dernière connexion supérieure au temps de vie d'un utilisateur.
/*				if ( $Occurrence->idn_derniere_connexion != '0000-00-00 00:00:00' and $Occurrence->idn_derniere_connexion != '') {
					$datetime1 = new DateTime( date( 'Y-m-d' ) );
					$datetime2 = new DateTime( $Occurrence->idn_derniere_connexion );

					$interval = $datetime1->diff( $datetime2 );
					
					$Inactivite_Compte = $PageHTML->recupererParametre( 'inactivite_compte' );
					if ( $Inactivite_Compte == '' ) $Inactivite_Compte = 6;

					if ( $interval->format('%R') == '-' ) {
						if ( $interval->format('%m') >= $Inactivite_Compte ) {
							if ( $Libelle_Alerte != '' ) $Libelle_Alerte .= ', ';
							$Libelle_Alerte .= $L_Utilisateur_Expire . ' (' .
							 $L_Date_Derniere_Connexion_Ancienne . ')';
						}
					}
				} else { */
				if ( $Occurrence->idn_derniere_connexion == '0000-00-00 00:00:00' or $Occurrence->idn_derniere_connexion == '') {
					if ( $Libelle_Alerte != '' ) $Libelle_Alerte .= ', ';
					$Libelle_Alerte .= $L_Jamais_Connecte;
				}

				if ( $Libelle_Alerte == '' ) {
					$_title = $L_Derniere_Connexion . ' ' . $Occurrence->idn_derniere_connexion;
					//$Occurrence->statut = '<img src="' . URL_IMAGES . '/s_okay.png" alt="OK" title="' . $_title . '" />';
					$Occurrence->statut = '<button class="btn btn-sm text-success border-success" title="' . $_title . '"><i class="bi-check2-circle"></i></button>';
				} else {
					//$Occurrence->statut = '<img src="' . URL_IMAGES . '/s_notice.png" alt="KO" data-toggle="tooltip" data-placement="bottom" title="' . $Libelle_Alerte . '" />';
					$Occurrence->statut = '<button class="btn btn-sm text-warning border-warning" title="' . $Libelle_Alerte . '"><i class="bi-exclamation-triangle-fill"></i></button>';
				}

				// Matérialise si l'utilisateur est un "super admin".
				if ( $Occurrence->idn_super_admin == TRUE ) $Occurrence->idn_super_admin = $L_Yes; //'<span class="glyphicon glyphicon-check"></span>';
				else $Occurrence->idn_super_admin = $L_No; //'<span class="glyphicon glyphicon-unchecked"></span>';

				// Matérialise si l'utilisateur a été déscativé.
				if ( $Occurrence->idn_desactiver == TRUE ) $Occurrence->idn_desactiver = $L_Yes;
				else $Occurrence->idn_desactiver = $L_No;

				if ( $Occurrence->idn_id == $_SESSION['idn_id'] and $_SESSION['idn_login'] != 'root' ) {
					$Format_Colonnes[ 'Actions' ]['boutons']['modifier'] = FALSE;
				} else {
					$Format_Colonnes[ 'Actions' ]['boutons']['modifier'] = $Droit_Modifier;
				}

				$Texte_HTML .= $PageHTML->creerOccurrenceCorpsTableau( $Occurrence->idn_id, $Occurrence, $Format_Colonnes );
			}

			$Limitation = $PageHTML->recupererParametre('limitation_utilisateurs');

			echo json_encode( array(
				'statut' => 'success',
				'texteHTML' => $Texte_HTML,
				'total' => $Total,
				'droit_modifier' => $Droit_Modifier,
				'droit_supprimer' => $Droit_Supprimer,
				'limitation' => $Limitation,
				'libelle_limitation' => $L_Limitation_Licence
				) );
		} catch( Exception $e ) {
			echo json_encode( array(
				'statut' => 'error',
				'texteMsg' => $e->getMessage()
				) );
		}
	} else {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
		) );
	}	
	break;


 case 'AJAX_Charger':
	if ( $Droit_Lecture === TRUE ) {
		try {
			$Identite = $objIdentites->detaillerIdentite( $_POST['idn_id'] );
	
			echo json_encode( array(
				'statut' => 'success',
				'username' => $Identite->idn_login,
				'super_admin' => $Identite->idn_super_admin,
				'id_civility' => $Identite->cvl_id,
				'id_entity' => $Identite->ent_id,
				'attempt' => $Identite->idn_tentative,
				'disable' => $Identite->idn_desactiver,
				'last_connection' => $Identite->idn_derniere_connexion,
				'expiration_date' => $Identite->idn_date_expiration,
				'updated_authentification' => $Identite->idn_date_modification_authentifiant,
				'is_super_admin' => $_SESSION['idn_super_admin'],
				'email' => $Identite->idn_courriel
				) );
		} catch( Exception $e ) {
			echo json_encode( array(
				'statut' => 'error',
				'texteMsg' => $e->getMessage()
				) );		
		}
	} else {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
		) );
	}

	break;


 case 'AJAX_Modifier':
	if ( $Droit_Modifier === TRUE ) {
		if ( ! isset($_POST['cvl_id']) || ! isset($_POST['ent_id']) || ! isset($_POST['id'])
		 || ! isset($_POST['idn_login']) || ! isset($_POST['idn_super_admin']) ) {
			try {
//				$objGestionnaires = new Gestionnaires();
				$objEtiquettes = new Etiquettes();


				$_POST['id'] = $PageHTML->controlerTypeValeur( $_POST['id'], 'NUMERIC' );
				if ( $_POST['id'] === -1 ) {
					echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (id)'
					) );

					exit();
				}

/*				$_POST['ent_id'] = $PageHTML->controlerTypeValeur( $_POST['ent_id'], 'NUMERIC' );
				if ( $_POST['ent_id'] === -1 ) {
					echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (ent_id)'
					) );

					exit();
				} */

				$_POST['idn_login'] = $PageHTML->controlerTypeValeur( $_POST['idn_login'], 'ASCII' );
				if ( $_POST['idn_login'] === -1 ) {
					echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (idn_login)'
					) );

					exit();
				}

				$_POST['idn_super_admin'] = $PageHTML->controlerTypeValeur( $_POST['idn_super_admin'], 'BOOLEAN' );
				if ( $_POST['idn_super_admin'] === -1 ) {
					echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (idn_super_admin)'
					) );

					exit();
				}

				$_POST['cvl_id'] = $PageHTML->controlerTypeValeur( $_POST['cvl_id'], 'NUMERIC' );
				if ( $_POST['cvl_id'] === -1 ) {
					echo json_encode( array(
					'statut' => 'error',
					'texteMsg' => $L_Invalid_Value . ' (cvl_id)'
					) );

					exit();
				}

				if ( $_POST['id'] == $_SESSION['idn_id'] ) {
					echo json_encode( array(
						'statut' => 'error',
						'texteMsg' => $L_Pas_Droit_Ressource . ' (idn_id="' . $_POST['idn_id'] . '")'.' [' . __LINE__ . ']'
					) );
					
					$PageHTML->ecrireEvenement( 'ATP_ALERTE', 'OTP_SECURITE', $L_Pas_Droit_Ressource . ' (idn_id="' . $_POST['idn_id'] . '")'.' [' . __LINE__ . ']' );
					
					exit();
				}

				$objIdentites->majIdentite( $_POST['id'], $_POST['idn_login'], '',
					$_POST['idn_super_admin'], $_SESSION['s_sct_id'], $_POST['cvl_id'], $_POST['idn_courriel'] );

				$PageHTML->ecrireEvenement( 'ATP_MODIFICATION', 'OTP_IDENTITE', 'idn_id="' . $_POST['id'] . 
					'", idn_login="' . $_POST[ 'idn_login' ] . '", idn_super_admin="' . $_POST['idn_super_admin'] . '"' .
					', sct_id="' . $_SESSION['s_sct_id'] . '", cvl_id="' . $_POST['cvl_id'] . '", idn_courriel="' . $_POST['idn_courriel'] . '"' );


				try {
					if ( isset($_POST['liste_SCT_a_supprimer']) and $_POST['liste_SCT_a_supprimer'] != '' ) {
						foreach( $_POST['liste_SCT_a_supprimer'] as $Societe ) {
							$objIdentites_Societes->supprimerSocieteIdentite( $_POST['id'], $Societe );

							$PageHTML->ecrireEvenement( 'ATP_SUPPRESSION', 'OTP_IDENTITE', 'idn_id="' . $_POST['id'] .
								'" => sct_id="' . $Societe );
						}
					}

					if ( isset($_POST['liste_SCT_a_ajouter']) and $_POST['liste_SCT_a_ajouter'] != '' ) {
						foreach( $_POST['liste_SCT_a_ajouter'] as $Societe ) {
							$objIdentites_Societes->ajouterSocieteIdentite( $_POST['id'], $Societe );
							
							$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_IDENTITE', 'idn_id="' . $_POST['id'] .
								'" => sct_id="' . $Societe );
						}
					}


					if ( isset($_POST['liste_ENT_a_supprimer']) and $_POST['liste_ENT_a_supprimer'] != '' ) {
						foreach( $_POST['liste_ENT_a_supprimer'] as $Entite ) {
							$objIdentites_Entites->supprimerEntiteIdentite( $_POST['id'], $Entite );

							$PageHTML->ecrireEvenement( 'ATP_SUPPRESSION', 'OTP_IDENTITE', 'idn_id="' . $_POST['id'] .
								'" => ent_id="' . $Entite );
						}
					}

					if ( isset($_POST['liste_ENT_a_ajouter']) and $_POST['liste_ENT_a_ajouter'] != '' ) {
/*						for( $i = 0; $i < count($_POST['liste_ENT_a_ajouter']); $i++ ) {
							$objIdentites_Entites->ajouterEntiteIdentite( $_POST['id'], $_POST['ajouterEntites'][$i][0],
								$_POST['ajouterEntites'][$i][1] );
						}*/
						foreach( $_POST['liste_ENT_a_ajouter'] as $Entite ) {
							$objIdentites_Entites->ajouterEntiteIdentite( $_POST['id'], $Entite );
							
							$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_IDENTITE', 'idn_id="' . $_POST['id'] . '" => ' .
								'ent_id="' . $Entite );
						}
					}

/*					if ( isset($_POST['modifierEntites']) and $_POST['modifierEntites'] != '' ) {
						for( $i = 0; $i < count($_POST['modifierEntites']); $i++ ) {
							$objIdentites_Entites->majEntiteIdentite( $_POST['idn_id'], $_POST['modifierEntites'][$i][0],
								$_POST['modifierEntites'][$i][1] );
						}
					} */


					if ( isset($_POST['liste_PRF_a_supprimer']) and $_POST['liste_PRF_a_supprimer'] != '' ) {
						foreach( $_POST['liste_PRF_a_supprimer'] as $Profil ) {
							$objIdentites_Profils->supprimerProfilIdentite( $_POST['id'], $Profil );
							
							$PageHTML->ecrireEvenement( 'ATP_SUPPRESSION', 'OTP_IDENTITE', 'idn_id="' . $_POST['id'] .
								'" => prf_id="' . $Profil );
						}
					}

					if ( isset($_POST['liste_PRF_a_ajouter']) and $_POST['liste_PRF_a_ajouter'] != '' ) {
						foreach( $_POST['liste_PRF_a_ajouter'] as $Profil ) {
							$objIdentites_Profils->ajouterProfilIdentite( $_POST['id'], $Profil );
							
							$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_IDENTITE', 'idn_id="' . $_POST['id'] . '" => ' .
								'prf_id="' . $Profil );
						}
					}


					if ( array_key_exists( 'liste_TGS_a_ajouter', $_POST ) ) {
						if ( $_POST['liste_TGS_a_ajouter'] != '' ) {
							foreach( $_POST['liste_TGS_a_ajouter'] as $Etiquette ) {
								$objEtiquettes->ajouterEtiquetteAUtilisateur( $_POST['id'], $Etiquette );

								$PageHTML->ecrireEvenement( 'ATP_ECRITURE', 'OTP_IDENTITE', 'idn_id="' . $_POST['id'] .
									'" => tgs_id="' . $Etiquette );
							}
						}
					}

					if ( array_key_exists( 'liste_TGS_a_supprimer', $_POST ) ) {
						if ( $_POST['liste_TGS_a_supprimer'] != '' ) {
							foreach( $_POST['liste_TGS_a_supprimer'] as $Etiquette ) {
								$objEtiquettes->supprimerEtiquetteAUtilisateur( $_POST['id'], $Etiquette );
								
								$PageHTML->ecrireEvenement( 'ATP_SUPPRESSION', 'OTP_IDENTITE', 'idn_id="' . $_POST['id'] .
									'" => tgs_id="' . $Etiquette );
							}
						}
					}
					
					$Resultat = array( 'statut' => 'success',
						'texteMsg' => $L_Utilisateur_Modifie
					);
				} catch( Exception $e ) {
					echo json_encode( array(
						'statut' => 'error',
						'texteMsg' => $e->getMessage()
					) );
				}


			} catch (Exception $e) {
				$Message = $e->getMessage();
				$Statut = 'error';

				if ( $e->getCode() == 23505 ) {
					$Message = $L_ERR_DUPL_Civilite;
				}

				$Resultat = array(
					'statut' => $Statut,
					'texteMsg' => $Message
					);
			}

			echo json_encode( $Resultat );
		} else {
			$Resultat = array(
				'statut' => 'error',
				'texteMsg' => $L_ERR_Champs_Obligatoires
				);

			echo json_encode( $Resultat );
			exit();
		}
	} else {
		$Resultat = array(
			'statut' => 'error',
			'texteMsg' => $L_No_Authorize
			);

		echo json_encode( $Resultat );
		exit();
	}
	break;


 case 'AJAX_Lister_ENT_LABEL':
 case 'AJAX_listerEntites':
	try {
		if ( ! isset( $_POST['idn_id']) ) $_POST['idn_id'] = '';
		if ( ! isset( $_POST['libelle']) ) $_POST['libelle'] = '';
		if ( ! isset( $_POST['type']) ) $_POST['type'] = '';
		if ( ! isset( $_POST['aucun']) ) $_POST['aucun'] = '';

		$Code_HTML = listerEntites( $_POST['idn_id'], $_POST['libelle'], $_POST['type'] );

		if ( $_POST['aucun'] == 'oui' ) {
			$Code_HTML = '<option value="">(' . $L_Neither_f . ')</option>' . $Code_HTML;
		}

		echo json_encode( array(
			'statut' => 'success',
			'texteMsg' => $Code_HTML
			) );
	} catch( Exception $e ) {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $e->getMessage()
			) );		
	}

	break;


 case 'AJAX_Lister_SCT_LABEL':
	try {
		if ( ! isset( $_POST['idn_id']) ) $_POST['idn_id'] = '';
		if ( ! isset( $_POST['libelle']) ) $_POST['libelle'] = '';
		if ( ! isset( $_POST['type']) ) $_POST['type'] = '';
		if ( ! isset( $_POST['aucun']) ) $_POST['aucun'] = '';

		$Code_HTML = listerSocietes( $_POST['idn_id'], $_POST['libelle'], $_POST['type'] );

		if ( $_POST['aucun'] == 'oui' ) {
			$Code_HTML = '<option value="">(' . $L_Neither_f . ')</option>' . $Code_HTML;
		}

		echo json_encode( array(
			'statut' => 'success',
			'texteMsg' => $Code_HTML
		) );
	} catch( Exception $e ) {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $e->getMessage()
		) );
	}

	break;


 case 'AJAX_Lister_CVL_LABEL':
 case 'AJAX_listerCivilites':
	try {
		if ( ! isset( $_POST['id'] ) ) $_POST['id'] = '';
		if ( ! isset( $_POST['libelle'] ) ) $_POST['libelle'] = '';
		if ( ! isset( $_POST['aucun'] ) ) $_POST['aucun'] = '';

		$Code_HTML = listerCivilites( $_POST['id'], $_POST['libelle'] );

		if ( $_POST['aucun'] == 'oui' ) {
			$Code_HTML = '<option value="">(' . $L_Neither_f . ')</option>' . $Code_HTML;
		}

		echo json_encode( array(
			'statut' => 'success',
			'texteMsg' => $Code_HTML
			) );
	} catch( Exception $e ) {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $e->getMessage()
			) );		
	}

	break;


 case 'AJAX_Lister_PRF_LABEL':
	try {
		if ( ! isset( $_POST['id']) ) $_POST['id'] = '';
		if ( ! isset( $_POST['libelle']) ) $_POST['libelle'] = '';
		if ( ! isset( $_POST['type']) ) $_POST['type'] = '';

		$Code_HTML = listerProfils( $_POST['id'], $_POST['libelle'], $_POST['type'] );

		echo json_encode( array(
			'statut' => 'success',
			'texteMsg' => $Code_HTML
			) );
	} catch( Exception $e ) {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $e->getMessage()
			) );		
	}

	break;


 case 'AJAX_Lister_Gestionnaires':
	try {
		if ( ! isset( $_POST['id']) ) $_POST['id'] = NULL;
		if ( ! isset( $_POST['libelle']) ) $_POST['libelle'] = '';
		if ( ! isset( $_POST['type']) ) $_POST['type'] = '';

		$Code_HTML = listerGestionnaires( $_POST['id'], $_POST['libelle'], $_POST['type'] );

		echo json_encode( array(
			'statut' => 'success',
			'texteMsg' => $Code_HTML
			) );
	} catch( Exception $e ) {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $e->getMessage()
			) );		
	}

	break;


 case 'AJAX_Lister_Etiquettes':
	try {
		if ( ! isset( $_POST['id']) ) $_POST['id'] = NULL;
		if ( ! isset( $_POST['libelle']) ) $_POST['libelle'] = '';
		if ( ! isset( $_POST['type']) ) $_POST['type'] = '';

		$Code_HTML = listerEtiquettes( $_POST['id'], $_POST['libelle'], $_POST['type'] );

		echo json_encode( array(
			'statut' => 'success',
			'texteMsg' => $Code_HTML
			) );
	} catch( Exception $e ) {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $e->getMessage()
			) );		
	}

	break;


 case 'AJAX_Lister_Actions':
	try {
		if ( ! isset( $_POST['id']) ) $_POST['id'] = '';

		$Code_HTML = '<div id="liste-actions">' .
			'<p>Pouet</p>' .
			'</div>';

		echo json_encode( array(
			'statut' => 'success',
			'texteMsg' => $Code_HTML
			) );
	} catch( Exception $e ) {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $e->getMessage()
			) );		
	}

	break;


 case 'AJAX_Reset_Tentative':
	if ( ! isset( $_POST['id']) ) {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_Field_Mandatory
			) );		
	}

	try {
		$PageHTML->reinitialiserTentative( $_POST['id'] );

		echo json_encode( array(
			'statut' => 'success',
			'texteMsg' => $L_Tentative_Reinitialise
			) );
	} catch( Exception $e ) {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $e->getMessage()
			) );		
	}

	break;


 case 'AJAX_Activer':
	if ( ! isset( $_POST['id']) ) {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_Field_Mandatory
			) );		
	}

	try {
		$PageHTML->activerDesactiver( $_POST['id'], FALSE );

		echo json_encode( array(
			'statut' => 'success',
			'texteMsg' => $L_Utilisateur_Active,
			'libelle' => $L_Desactiver_Utilisateur
			) );
	} catch( Exception $e ) {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $e->getMessage()
			) );		
	}

	break;


 case 'AJAX_Desactiver':
	if ( ! isset( $_POST['id']) ) {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_Field_Mandatory,
			) );		
	}

	try {
		$PageHTML->activerDesactiver( $_POST['id'], TRUE );

		echo json_encode( array(
			'statut' => 'success',
			'texteMsg' => $L_Utilisateur_Desactive,
			'libelle' => $L_Activer_Utilisateur
			) );
	} catch( Exception $e ) {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $e->getMessage()
			) );		
	}

	break;


 case 'AJAX_Reset_Expiration':
	if ( ! isset( $_POST['id']) ) {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_Field_Mandatory,
			) );		
	}

	try {
		$NextDate = $PageHTML->reinitialiserDateExpiration( $_POST['id'], TRUE );

		echo json_encode( array(
			'statut' => 'success',
			'texteMsg' => $L_Date_Expiration_Reinitialisee,
			'next_date' => $NextDate
			) );
	} catch( Exception $e ) {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $e->getMessage()
			) );		
	}

	break;


 case 'AJAX_Reset_Password':
	if ( ! isset( $_POST['id']) ) {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_Field_Mandatory,
			) );		
	}

	try {
		$NextDate = $PageHTML->reinitialiserMotPasse( $_POST['id'] );

		echo json_encode( array(
			'statut' => 'success',
			'texteMsg' => $L_Mot_Passe_Reinitialise,
			'next_date' => $NextDate
			) );
	} catch( Exception $e ) {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $e->getMessage()
			) );		
	}

	break;


 case 'AJAX_Associer_Entites':
	if ( ! isset( $_POST['idn_id'] ) ) {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_Field_Mandatory,
			) );

		exit();
	}

	require_once( DIR_LIBRAIRIES . '/Class_HBL_Identites_Entites_PDO.inc.php' );
	$objIdentites_Entites = new HBL_Identites_Entites();

	try {
		if ( isset($_POST['supprimerEntites']) and $_POST['supprimerEntites'] != '' ) {
			foreach( $_POST['supprimerEntites'] as $Entite ) {
				$objIdentites_Entites->supprimerEntiteIdentite( $_POST['idn_id'], $Entite );
			}
		}

		if ( isset($_POST['ajouterEntites']) and $_POST['ajouterEntites'] != '' ) {
			for( $i = 0; $i < count($_POST['ajouterEntites']); $i++ ) {
				$objIdentites_Entites->ajouterEntiteIdentite( $_POST['idn_id'], $_POST['ajouterEntites'][$i][0],
					$_POST['ajouterEntites'][$i][1] );
			}
		}

		if ( isset($_POST['modifierEntites']) and $_POST['modifierEntites'] != '' ) {
			for( $i = 0; $i < count($_POST['modifierEntites']); $i++ ) {
				$objIdentites_Entites->majEntiteIdentite( $_POST['idn_id'], $_POST['modifierEntites'][$i][0],
					$_POST['modifierEntites'][$i][1] );
			}
		}

		echo json_encode( array(
			'statut' => 'success'
			) );
	} catch( Exception $e ) {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $e->getMessage()
			) );		
	}

	break;


 case 'AJAX_Associer_Profils':
	if ( ! isset( $_POST['idn_id'] ) ) {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $L_Field_Mandatory,
			) );

		exit();
	}

	require_once( DIR_LIBRAIRIES . '/Class_HBL_Identites_Profils_PDO.inc.php' );
	$objIdentites_Profils = new HBL_Identites_Profils();

	try {
		if ( isset($_POST['supprimerProfils']) and $_POST['supprimerProfils'] != '' ) {
			foreach( $_POST['supprimerProfils'] as $Profil ) {
				$objIdentites_Profils->supprimerProfilIdentite( $_POST['idn_id'], $Profil );
			}
		}

		if ( isset($_POST['ajouterProfils']) and $_POST['ajouterProfils'] != '' ) {
			foreach( $_POST['ajouterProfils'] as $Profil ) {
				$objIdentites_Profils->ajouterProfilIdentite( $_POST['idn_id'], $Profil );
			}
		}

		echo json_encode( array(
			'statut' => 'success'
			) );
	} catch( Exception $e ) {
		echo json_encode( array(
			'statut' => 'error',
			'texteMsg' => $e->getMessage()
			) );		
	}

	break;


 case 'AJAX_Selectioner_Societe':
	$PageHTML->selectionnerSociete();

	break;
}


function listerEntites( $Init_Id = '', $Init_Libelle = '', $Type = '' ) {
	require_once( DIR_LIBRAIRIES . '/Class_HBL_Identites_Entites_PDO.inc.php' );

	$Entities = new HBL_Entites();
	$Identity_Entities = new HBL_Identites_Entites();


	// Récupère toutes les Entités pour lesquelles l'Utilisateur connecté est autorisé.
	if ( $_SESSION['idn_super_admin'] === TRUE ) {
		$Liste_Toutes_Entites_Autorisees = $Entities->rechercherEntites($_SESSION['s_sct_id']);
	} else {
		$Liste_Toutes_Entites_Autorisees = $Identity_Entities->rechercherEntitesIdentite( $_SESSION['idn_id'], TRUE, TRUE );		
		$Liste_Toutes_Entites_Admin = $Identity_Entities->rechercherEntitesIdentite( $_SESSION['idn_id'], TRUE, TRUE, TRUE );		
	}


	switch ( $Type ) {
	 case 'checkbox':
		$Code_HTML = '<div id="liste-entites" class="liste-interne">';

		$Liste_Toutes_Entites_Associees = array();

		// Récupère les Entités pour lesquelles l'Utilisateur a été associé.
		if ( $Init_Id != '' ) {
			$Liste_Toutes_Entites_Associees = $Identity_Entities->rechercherEntitesIdentite( $Init_Id, TRUE, TRUE );
		}

		break;


	 default:
		$Code_HTML = '';

		break;
	}


	foreach ($Liste_Toutes_Entites_Autorisees as $Occurrence) {
		switch ( $Type ) {
		 default:
		 case 'select':
			if ( ($Init_Id != '' and $Init_Id == $Occurrence->ent_id)
				or ($Init_Libelle != '' and $Init_Libelle == $Occurrence->ent_nom) ) $Selected = ' selected';
			else $Selected = '';

			$Code_HTML .= '<option value="' . $Occurrence->ent_id . '"' . $Selected . '>' . $Occurrence->ent_nom . '</option>' ;
			break;


		 case 'checkbox':
			// Vérifie si l'Entité courante est associée à l'Utilisateur courant.
			if ( $Init_Id != ''
			 and ( array_key_exists( $Occurrence->ent_id, $Liste_Toutes_Entites_Associees ) or $Init_Libelle == $Occurrence->ent_nom ) ) {
				$Checked = ' checked';
				$Old_Value = '1';
			} else {
				$Checked = '';
				$Old_Value = '0';
			}


			// Vérifie si l'Utilisateur courant à un droit d'Administration sur l'Entité courante.
			$Checked_Adm = '';
			$Old_Value_Adm = '0';

			if ( $Liste_Toutes_Entites_Associees != [] ) {
				if ( array_key_exists( $Occurrence->ent_id, $Liste_Toutes_Entites_Associees ) ) {
					if ( isset($Liste_Toutes_Entites_Associees[$Occurrence->ent_id]->iden_admin) ) {
						if ( $Liste_Toutes_Entites_Associees[$Occurrence->ent_id]->iden_admin === TRUE ) {
							$Checked_Adm = ' checked';
							$Old_Value_Adm = '1';
						}
					}
				}
			}


			// Désactive l'option d'administration, si l'Utilisateur connecté n'a pas lui même les droits d'accès sur l'Entité courante. 
			$Disabled = '';

			if ( $_SESSION['idn_super_admin'] !== TRUE ) {
				if ( array_key_exists($Occurrence->ent_id, $Liste_Toutes_Entites_Admin) ) {
					if ( $Liste_Toutes_Entites_Admin[$Occurrence->ent_id]->iden_admin === FALSE ) {
						$Disabled = ' disabled';
					}
				} else {
					$Disabled = ' disabled';
				}
			}

			$Code_HTML .= '<div class="row liste">' .
				'<div class="col-lg-8">' .
				'<div class="form-check">' .
				'<input class="form-check-input" type="checkbox" id="chk-ENT-' . $Occurrence->ent_id . '"' . $Checked . ' data-old="' . $Old_Value . '">' .
				'<label class="form-check-label" for="chk-ENT-' . $Occurrence->ent_id . '">' . $Occurrence->ent_nom . '</label>' .
				'</div>' .
				'</div>' .
				'<div class="col-lg-4">' .
				'<div class="form-check">' .
				'<input class="form-check-input" type="checkbox" id="chk-ENT_ADM-' . $Occurrence->ent_id . '"' . $Checked_Adm . $Disabled . ' data-old="' . $Old_Value_Adm . '">' .
				'<label class="form-check-label' . $Disabled . '" for="chk-ENT_ADM-' . $Occurrence->ent_id . '">' . $GLOBALS['L_Administrateur'] . '</label>' .
				'</div>' .
				'</div>' .
				'</div>';
			break;
		}
	}

	switch ( $Type ) {
	 case 'checkbox':
		$Code_HTML .= '</div>';
		break;
	}

	return $Code_HTML;
}



function listerCivilites( $Init_Id = '', $Init_Libelle = '' ) {
	$Civilities = new HBL_Civilites();

	$Liste = $Civilities->rechercherCivilites();

	$Code_HTML = '';

	foreach ($Liste as $Occurrence) {
		if ( ($Init_Id != '' and $Init_Id == $Occurrence->cvl_id)
		 or ($Init_Libelle != '' and $Init_Libelle == $Occurrence->cvl_prenom . ' ' . $Occurrence->cvl_nom) ) {
			$Selected = ' selected';
		} else {
			$Selected = '';
		}

		$Code_HTML .= '<option value="' . $Occurrence->cvl_id . '"' . $Selected . '>' . $Occurrence->cvl_prenom . ' ' . $Occurrence->cvl_nom . '</option>' ;
	}

	return $Code_HTML;
}


function listerProfils( $Init_Id = '', $Init_Libelle = '', $Type = '' ) {
	$Profils = new HBL_Profils();

	$Liste = $Profils->rechercherProfils();


	switch ( $Type ) {
	 case 'checkbox':

		$Code_HTML = '<div id="liste-profils" class="liste-interne">';
		if ( $Init_Id != '' ) {
			require_once( DIR_LIBRAIRIES . '/Class_HBL_Identites_Profils_PDO.inc.php' );
			$Identity_Profiles = new HBL_Identites_Profils();

			$Liste_Identity_Profiles = $Identity_Profiles->rechercherProfilsIdentite( $Init_Id, TRUE );
		}

		break;

	 default:
		$Code_HTML = '';
		break;
	}


	foreach ($Liste as $Occurrence) {
		switch ( $Type ) {
		 default:
		 case 'select':
			if ( $Init_Id != '' and $Init_Id == $Occurrence->prf_id ) $Selected = ' selected';
			else $Selected = '';

			if ( $Init_Libelle != '' and $Init_Libelle == $Occurrence->prf_libelle ) $Selected = ' selected';
			else $Selected = '';

			$Code_HTML .= '<option value="' . $Occurrence->prf_id . '"' . $Selected . '>' . $Occurrence->prf_libelle . '</option>' ;
			break;

		 case 'checkbox':
			if ( $Init_Id != '' and (in_array( $Occurrence->prf_id, $Liste_Identity_Profiles ) or $Init_Libelle == $Occurrence->prf_libelle) ) {
				$Checked = ' checked';
				$Old_Value = '1';
			} else {
				$Checked = '';
				$Old_Value = '0';
			}

			$Code_HTML .= '<div class="row liste">' .
				'<div class="col-lg-12">' .
				'<div class="form-check">' .
				'<input class="form-check-input" type="checkbox" id="chk-PRF-' . $Occurrence->prf_id . '"' . $Checked . ' data-old="' . $Old_Value . '">' .
				'<label class="form-check-label" for="chk-PRF-' . $Occurrence->prf_id . '">' . $Occurrence->prf_libelle . '</label>' .
				'</div>' .
				'</div>' .
				'</div>';
			break;
		}
	}


	switch ( $Type ) {
	 case 'checkbox':
		$Code_HTML .= '</div>';
		break;
	}

	return $Code_HTML;
}


function listerGestionnaires( $Init_Id = '', $Init_Libelle = '', $Type = '' ) {
	$objGestionnaires = new Gestionnaires();

	$Liste = $objGestionnaires->listerGestionnairesParUtilisateur( $Init_Id );

	$Code_HTML = '<div id="liste-gestionnaires" class="liste-interne">';

	foreach( $Liste as $Occurrence ) {
		if ( $Occurrence->idn_id != NULL and $Occurrence->idn_id != '' ) {
			$Checked = ' checked';
			$Old_Value = '1';
		} else {
			$Checked = '';
			$Old_Value = '0';
		}

		$Code_HTML .= '<div class="row liste">' .
			'<div class="col-lg-12">' .
			'<div class="form-check">' .
			'<input class="form-check-input" type="checkbox" id="chk-GST-' . $Occurrence->gst_id . '"' . $Checked . ' data-old="' . $Old_Value . '">' .
			'<label class="form-check-label" for="chk-GST-' . $Occurrence->gst_id . '">' . $Occurrence->gst_libelle . '</label>' .
			'</div>' .
			'</div>' .
			'</div>';
	}

	$Code_HTML .= '</div>';

	return $Code_HTML;
}


function listerEtiquettes( $Init_Id = '', $Init_Libelle = '', $Type = '' ) {
	$objEtiquettes = new Etiquettes();

	$Liste = $objEtiquettes->listerEtiquettesParUtilisateur( $Init_Id );

	$Code_HTML = '<div id="liste-etiquettes" class="liste-interne">';

	foreach( $Liste as $Occurrence ) {
		if ( $Occurrence->idn_id != NULL and $Occurrence->idn_id != '' ) {
			$Checked = ' checked';
			$Old_Value = '1';
		} else {
			$Checked = '';
			$Old_Value = '0';
		}

		$Code_HTML .= '<div class="row liste">' .
			'<div class="col-lg-12">' .
			'<div class="form-check">' .
			'<input class="form-check-input" type="checkbox" id="chk-TGS-' . $Occurrence->tgs_id . '"' . $Checked . ' data-old="' . $Old_Value . '">' .
			'<label class="form-check-label" for="chk-TGS-' . $Occurrence->tgs_id . '">' . $Occurrence->tgs_code . ' - ' . $Occurrence->tgs_libelle . '</label>' .
			'</div>' .
			'</div>' .
			'</div>';
	}

	$Code_HTML .= '</div>';

	return $Code_HTML;
}

?>