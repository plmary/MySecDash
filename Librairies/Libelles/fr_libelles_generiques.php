<?php
/**
* Libellé générique en Français.
*
* @warning Ce fichier doit impérativement être au format UTF-8 pour une gestion cohérente des caractères accentués.
*
* @copyright Copyright Loxense
* @author Pierre-Luc MARY
* @date 2015-07-23
*/

include_once( 'Constants.inc.php');


if ( ! defined( 'INIT_STRING' ) ) {
	function mb_ucfirst( $Chaine ) {
		/**
		* Met en majuscule la prémière lettre d'une chaine de caractère.
		*
		* @license Loxense
		* @author Pierre-Luc MARY
		* @date 2018-03-14
		*
		* @param[in] $Chaine Chaine à transformer.
		*
		* @return Renvoi la chaine transformée.
		*/

		return mb_strtoupper(mb_substr( $Chaine, 0, 1 ), "UTF-8").mb_substr( $Chaine, 1 );
	}


	function mb_lcfirst( $Chaine )
	{
		/**
		* Met en minuscule la prémière lettre d'une chaine de caractère.
		*
		* @license Loxense
		* @author Pierre-Luc MARY
		* @date 2018-03-14
		*
		* @param[in] $Chaine Chaine à transformer.
		*
		* @return Renvoi la chaine transformée.
		*/

		return mb_strtolower(mb_substr( $Chaine, 0, 1 ), "UTF-8").mb_substr( $Chaine, 1 );
	}

	define( 'INIT_STRING', 'yes' );
}


include( HBL_DIR_LABELS . '/' . $_SESSION[ 'Language' ] . '_HBL_Generiques.inc.php' );

$L_Welcome = 'Bienvenue';
$L_ExpireDans = 'Expire dans';

$L_Libelle = 'Libellé';

$L_Administration = "Administration";

$L_Referentiel = 'Référentiel';
$L_Referentiel_Interne = "Référentiel interne";

$L_Parametres_Base = "Paramètres de base";
$L_Gestion_Parametres_Base = "Gestion des " . mb_lcfirst( $L_Parametres_Base );

$L_Proprietaire = 'Propriétaire';
$L_Proprietaires = 'Propriétaires';

$L_Types_Actif_Support = "Types d'actif support";
$L_Type_Actif_Support = "Type d'actif support";
$L_Gestion_Types_Actif_Support = "Gestion des " . mb_lcfirst( $L_Types_Actif_Support );

$L_Menaces = 'Menaces';
$L_Menace = 'Menace';
$L_Menaces_Generiques = $L_Menaces . " génériques";
$L_Menace_Generique = $L_Menace . " générique";
$L_Gestion_Menaces_Generiques = "Gestion des " . mb_lcfirst( $L_Menaces_Generiques );

$L_Types_Menace_Generique = "Types de menace générique";
$L_Type_Menace_Generique = "Type de menace générique";
$L_Gestion_Types_Menace_Generique = "Gestion des types de menace générique";

$L_Vulnerabilites = 'Vulnérabilités';
$L_Vulnerabilite = 'Vulnérabilité';
$L_Vulnerabilites_Generiques = $L_Vulnerabilites . " génériques";
$L_Vulnerabilite_Generique = $L_Vulnerabilite . " générique";
$L_Gestion_Vulnerabilites_Generiques = 'Gestion des ' . mb_lcfirst( $L_Vulnerabilites_Generiques );

$L_Sources_Menaces = "Sources de menaces";
$L_Source_Menace = "Source de menace";
$L_Gestion_Sources_Menaces = "Gestion des " . mb_lcfirst( $L_Sources_Menaces );

$L_Source = 'Source';
$L_Sources = 'Sources';
$L_Cible = 'Cible';
$L_Cibles = 'Cibles';
$L_Sources_Risques = "Sources des risques";
$L_Source_Risque = "Source de risques";
$L_Gestion_Sources_Risques = "Gestion des " . mb_lcfirst( $L_Sources_Risques );

$L_Objectifs_Vises = 'Objectifs visés';
$L_Objectif_Vise = 'Objectif visé';
$L_Gestion_Objectifs_Vises = "Gestion des " . mb_lcfirst( $L_Objectifs_Vises );

$L_Partie_Prenante = 'Partie prenante';
$L_Parties_Prenantes = 'Parties prenantes';
$L_Gestion_Parties_Prenantes = "Gestion des " . mb_lcfirst( $L_Parties_Prenantes );

$L_Risques_Generiques = "Risques génériques";
$L_Risque_Generique = "Risque générique";
$L_Gestion_Risques_Generiques = "Gestion des " . mb_lcfirst( $L_Risques_Generiques );

$L_Aucun_Risque_Generique_Associe = 'Aucun risque générique associé';
$L_Aucune_Action_Generique_Associee = 'Aucune action générique associée';
$L_Aucune_Mesure_Referentiel_Associee = 'Aucune mesure d\'un référentiel associée';

$L_Types_Traitement_Risques = "Types de traitement des risques";
$L_Type_Traitement_Risques = "Type de traitement des risques";
$L_Gestion_Types_Traitement_Risques = "Gestion des " . mb_lcfirst( $L_Types_Traitement_Risques );

$L_Impacts_Generiques = "Impacts génériques";
$L_Impact_Generique = "Impact générique";
$L_Gestion_Impacts_Generiques = "Gestion des " . mb_lcfirst( $L_Impacts_Generiques );

$L_Mesures_Generiques = "Mesures génériques";
$L_Mesure_Generique = "Mesure générique";
$L_Gestion_Mesures_Generiques = "Gestion des " . mb_lcfirst( $L_Mesures_Generiques );

$L_Mesures_Referentiels = "Mesures des référentiels";
$L_Mesure_Referentiel = "Mesure d'un référentiel";
$L_Gestion_Mesures_Referentiels = 'Gestion des ' . mb_lcfirst( $L_Mesures_Referentiels );

$L_Referentiels_Conformite = "Référentiels de conformité";
$L_Referentiel_Conformite = "Référentiel de conformité";
$L_Gestion_Referentiels_Conformite = "Gestion des " . mb_lcfirst( $L_Referentiels_Conformite );

$L_Controle_Acces = "Contrôle d'accès";
$L_Controles_Acces = "Contrôles d'accès";
$L_Gestion_Controles_Acces = 'Gestion des ' . mb_lcfirst( $L_Controles_Acces );

$L_Entites = "Entités";
$L_Entite = "Entité";
$L_Gestion_Entites = "Gestion des " . mb_lcfirst( $L_Entites );
$L_Entite_Change = 'Entité changée';

$L_Civilites = "Civilités";
$L_Civilite = "Civilité";
$L_Gestion_Civilites = "Gestion des " . mb_lcfirst( $L_Civilites );

$L_Utilisateurs = "Utilisateurs";
$L_Utilisateur = "Utilisateur";
$L_Gestion_Utilisateurs = "Gestion des " . mb_lcfirst( $L_Utilisateurs );

$L_Profils = "Profils";
$L_Profil = "Profil";
$L_Gestion_Profils = "Gestion des " . mb_lcfirst( $L_Profils );

$L_Applications = "Applications";
$L_Application = "Application";
$L_Gestion_Applications = "Gestion des " . mb_lcfirst( $L_Applications );
$L_Gestion_Applications_SI = "Gestion des applications (vision du SI)";
$L_Applications_SI = "Applications (vision du SI)";

$L_Gestion_ApplicationsInternes = "Gestion des applications internes";
$L_ApplicationsInternes = 'Applications internes';
$L_ApplicationInterne = "Application interne";

$L_Privileges = "Privilèges";
$L_Privilege = "Privilège";
$L_Gestion_Privileges = "Gestion des " . mb_lcfirst( $L_Privileges );

$L_Consultation_Historique = "Consultation de l'historique des actions";
$L_Historique = "Historique des actions";

$L_Export_Base = "Export / Import de la base";

$L_Gestion_Risques = "Gestion des risques";

$L_Cartographies = "Cartographies";
$L_Cartographie = "Cartographie";
$L_Cartographies_Risques = "Cartographies des risques";
$L_Cartographie_Risques = "Cartographie des risques";
$L_Gestion_Cartographies_Risques = "Gestion des " . mb_lcfirst( $L_Cartographies_Risques );
$L_Gerer_Cartographies  = 'Gérer les cartographies';

$L_Criteres_Valorisation_Actifs = "Critères de valorisation des actifs";
$L_Critere_Valorisation_Actifs = "Critère de valorisation des actifs";
$L_Gestion_Criteres_Valorisation_Actifs = "Gestion des " . mb_lcfirst( $L_Criteres_Valorisation_Actifs );

$L_Criteres_Appreciation_Risques = "Critères d'appréciation des risques";
$L_Critere_Appreciation_Risques = "Critère d'appréciation des risques";
$L_Gestion_Criteres_Appreciation_Acceptation_Risques = "Gestion des " . mb_lcfirst( $L_Criteres_Appreciation_Risques );

$L_Editions_Risques = "Éditions des risques";
$L_Edition_Risques = "Édition des risques";
$L_Gestion_Editions_Risques = "Gestion des " . mb_lcfirst( $L_Editions_Risques );

$L_Matrices_Risques = "Matrices des risques";
$L_Matrice_Risques = "Matrice des risques";
$L_Visualisation_Matrices_Risques = "Visualisation des " . mb_lcfirst( $L_Matrices_Risques );

$L_Actifs = "Actifs";
$L_Actif = "Actif";
$L_Actifs_Primordiaux = "Actifs primordiaux";
$L_Actif_Primordial = "Actif primordial";
$L_Gestion_Actifs_Primordiaux = 'Gestion des ' . mb_lcfirst( $L_Actifs_Primordiaux );
$L_Referentiel_Actifs_Primordiaux = 'Référentiel des ' . mb_lcfirst( $L_Actifs_Primordiaux );
$L_Gestion_Referentiel_Actifs_Primordiaux = 'Gestion du référentiel des ' . mb_lcfirst( $L_Actifs_Primordiaux );

$L_Actifs_Supports = "Actifs supports";
$L_Actif_Support = "Actif support";
$L_Gestion_Actifs_Supports = 'Gestion des ' . mb_lcfirst( $L_Actifs_Supports );
$L_Referentiel_Actifs_Supports = 'Référentiel des ' . mb_lcfirst( $L_Actifs_Supports );
$L_Gestion_Referentiel_Actifs_Supports = 'Gestion du référentiel des ' . mb_lcfirst( $L_Actifs_Supports );

$L_Evenements_Redoutes = "Événements redoutés";
$L_Evenement_Redoute = "Événement redouté";
$L_Gestion_Evenements_Redoutes = "Gestion des " . mb_lcfirst( $L_Evenements_Redoutes );

$L_Risques = "Risques";
$L_Risque = "Risque";
$L_Identification_Evaluation_Risques = "Identification et évaluation des risques";
$L_Traitement_Risques = "Traitement des risques";
$L_Traitement_Risque = "Traitement du risque";

$L_Gestion_Appreciation_Risques = "Gestion de l'appréciation des risques";
$L_Appreciation_Risques = "Appréciation des risques";	
$L_Gestion_Traitement_Risques = "Gestion du traitement des risques";

$L_Gestion_Conformite = "Gestion de la conformité";
$L_Gerer_Conformite = "Gérer la conformité";
$L_Matrice_Conformite = "Matrice de conformité";
$L_Edition_Conformite = "Edition de la conformité";
$L_Editer_Conformite = "Editer la conformité";

$L_Gestion_Actions = "Gestion des actions";
$L_Gerer_Actions = 'Gérer les actions';
$L_Edition_Actions = "Édition des actions";
$L_Editer_Actions = "Éditer les actions";
$L_Actions_Generiques = 'Actions génériques';
$L_Action_Generique = 'Action générique';

$L_Gestion_ImportExport_Base = 'Importation et Exportation de la Base';
$L_Exporter_Base = 'Exporter la base';
$L_Restaurer_Base = 'Restaurer la base';
$L_Sauvegarder_Base = 'Sauvegarder la base';
$L_Sauvegarde_Terminee = 'Sauvegarde terminée';
$L_Restauration_Terminee = 'Restauration terminée';

$L_Gestionnaire = 'Gestionnaire technique';
$L_Gestionnaires = 'Gestionnaires techniques';
$L_Gestion_Gestionnaires = 'Gestion des gestionnaires techniques';

$L_Gestion_Types_Critere_Valorisation_Risques = 'Gestion des types de critère de valorisation des risques';

$L_Vision_Consolidee = 'Vision consolidée';

$L_Logo1 = "Des risques aux actions";
$L_Logo2 = "Des actions à la conformité";

$L_Consulter = "Consulter";
$L_Creer = 'Créer';
$L_Ajouter = "Ajouter";
$L_Ajoute = "Ajouté";
$L_Modifier = "Modifier";
$L_Modifie = 'Modifié';
$L_Supprimer = "Supprimer";
$L_Supprime = "Supprimé";
$L_Associer = 'Associer';
$L_Associe = 'Associé';
$L_Dissocier = 'Dissocier';
$L_Dissocie = 'Dissocié';
$L_Dupliquer = 'Dupliquer';
$L_Duplique = 'Dupliqué';
$L_Charger = 'Charger';
$L_A_Valider = 'A valider';
$L_Charge = 'Chargé';
$L_Consulter_Historique = 'Consulter historique';
$L_Fermer = "Fermer";
$L_Deconnexion = "Déconnexion";
$L_Imprimer = 'Imprimer';
$L_Editer = 'Editer';
$L_Changer_Mot_Passe_Obligatoire = "Changer de mot de passe";
$L_Retour = 'Retour';
$L_Envoyer = 'Envoyer';
$L_Rechercher = 'Rechercher';
$L_Reinitialiser = 'Réinitialiser';
$L_Validation = 'Validation';
$L_Partager = 'Partager';

$L_Selectionner_Cartographie = 'Sélectionner la cartographie';
$L_Regenerer_Riques = 'Regénérer les Risques';
$L_Ignorer_Risque = 'Ignorer risque';
$L_Risques_Regeneres = 'Risques regénérés';
$L_Generer_Impression = 'Générer l\'impression';
$L_Telecharger_Impression = 'Télécharger l\'impression';
$L_Generer_Fichier_Excel = 'Générer le fichier Excel';
$L_Telecharger_Fichier_Excel = 'Télécharger le fichier Excel';
$L_Impression = 'Impression';
$L_Impression_Generee = 'Impression générée';
$L_Reinitialisation = 'Réinitialisation';
$L_Confirmation_Reinitialisation = 'Voulez-vous vraiment réinitialiser les critères ?';
$L_Reinitialisation_Terminee = 'Réinitialisation terminée';
$L_Initialisation_Terminee = 'Initialisation terminée';

$L_Autres_Supports = "Autres supports";
$L_Autre_Support = "Autre support";
$L_Vulnerabilites = "Vulnérabilités";
$L_Vulnerabilite = "Vulnérabilité";
$L_Associations = 'Associations';
$L_Association = 'Association';
$L_Couleur = 'Couleur';
$L_Mesure = "Mesure";
$L_Mesures = "Mesures";
$L_Periode = "Période";
$L_Periodes = "Périodes";
$L_Poids = "Poids";
$L_Cotation = "Cotation";
$L_Cotations = "Cotations";
$L_Impacts = 'Impacts';
$L_Impact = "Impact";
$L_Vraisemblance = "Vraisemblance";
$L_Vraisemblances = "Vraisemblances";
$L_Numero = "Numero";
$L_Version = "Version";
$L_DDA = "DDA";
$L_Couverture = "Couverture";
$L_Justificatif = "Justificatif";
$L_Etat = "État";
$L_ASA = "A.S.A.";
$L_Access_Controls = 'Contrôles d\'accès';
$L_Access_Control = 'Contrôle d\'accès';

$L_Coche = 'Coché';
$L_Non_Coche = 'Non coché';

$L_Liste_Cartographies = "Liste des cartographies";
$L_Cartographie_Courante_Changee = "Cartographie courante changée";
$L_Cartographie_Courante = 'Cartographie courante';
$L_Aucune_Cartographie = "Aucune cartographie";

$L_ERR_Champs_Obligatoires = 'Certains champs sont obligatoires';
$L_ERR_Champ_Obligatoire = 'Ce champ est obligatoire';

$L_Valorisation = 'Valorisation';
$L_Aucune_Valorisation = 'Aucune valorisation';
$L_Aucune_Modification = 'Aucune modification de réalisée';
$L_Modification_Realisee = 'Modification réalisée';
$L_Copie = 'Copie';
$L_Manuel = 'Manuel';
$L_Manuellement = 'Manuellement';
$L_Automatique = 'Automatique';
$L_Automatiquement = 'Automatiquement';

$L_Tout_Selectionner = 'Tout sélectionner';
$L_Tout_Deselectionner = 'Tout désélectionner';

$L_Supprimer_Libelle = 'Supprimer libellé';
$L_Modifier_Libelle = 'Modifier libellé';
$L_Ajouter_Libelle = 'Ajouter libellé';
$L_Creer_Libelle = 'Créer libellé';

$L_Libelle_Supprime = 'Libellé supprimé';
$L_Libelle_Modifie = 'Libellé Modifié';
$L_Libelle_Ajoute = 'Libellé ajouté';
$L_Libelle_Cree = 'Libellé créé';

$L_Description = 'Description';
$L_Commentaire = 'Commentaire';

$L_MaJ_Libelle = 'Libellé mis à jour';

$L_Travail_En_Cours = 'Travail en cours';

$L_A_Definir = 'À définir';
$L_Non_Defini = "Non défini";

$L_Total = 'Total';
$L_Limitation_Licence = 'Vous avez atteint les limites de votre licence';

$L_De_Date = 'De';
$L_A_Date = 'À';
$L_Format_Date_Heure = 'AAAA-MM-JJ HH:MM';
$L_Format_Date = 'AAAA-MM-JJ';

$L_Tous = 'Tous';
$L_Toutes = 'Toutes';

$L_Illimite = 'Illimité';

$L_Langue = 'Langue';
$L_Langue_fr = 'Français';
$L_Langue_en = 'Anglais';

$L_En_Cours = 'En cours';

$L_Criteres_Representation_Niveaux_Risque = 'Critères de représentation des niveaux de risque';
$L_Critere_Representation_Niveaux_Risque = 'Critère de représentation des niveaux de risque';
$L_Grilles_Impact = 'Grilles d\'impact';
$L_Grille_Impact = 'Grille d\'impact';
$L_Types_Impact = 'Types d\'impact';
$L_Type_Impact = 'Type d\'impact';
$L_Niveaux_Impact = 'Niveaux d\'impact';
$L_Niveau_Impact = 'Niveau d\'impact';

$L_Grilles_Vraisemblance = 'Grilles de vraisemblance';
$L_Grille_Vraisemblance = 'Grille de vraisemblance';
$L_Types_Vraisemblance = 'Types de vraisemblance';
$L_Type_Vraisemblance = 'Type de vraisemblance';
$L_Niveaux_Vraisemblance = 'Niveaux de vraisemblance';
$L_Niveau_Vraisemblance = 'Niveau de vraisemblance';

$L_Criteres_Valorisation_Actifs = 'Critères de valorisation des actifs';
$L_Critere_Valorisation_Actifs = 'Critère de valorisation des actifs';

$L_Telecharger_Word = 'Télécharger le fichier Word';
$L_Telecharger_Excel = 'Télécharger le fichier Excel';

$L_Etiquettes = 'Étiquettes';
$L_Etiquette = 'Étiquette';
$L_Gestion_Etiquettes = 'Gestion des étiquettes';

$L_Ordre = 'Ordre';

$L_Informations_Complementaires = 'Informations complémentaires';
$L_Televerser_Informations = 'Téléverser informations';
$L_Telecharger_Informations = 'Télécharger informations';
$L_Telecharger_Depuis = 'Télécharger';
$L_Telecharger_Vers = 'Téléverser';
$L_Transferer_Fichier = 'Téléverser fichier';
$L_Fichier_Transfere = 'Fichier téléversé';
$L_Copy_Loxense_Impossible = 'Impossible de copier le fichier dans le répertoire spécifique de Loxense';
$L_Nom_Fichier_Non_Valide = 'Nom de fichier non valide';
$L_Fichier_Temporaire_Introuvable = 'Le fichier temporaire est introuvable';
$L_Pas_Fichier_A_Transferer = 'Pas de fichier à Transférer';
$L_Fichier_Trop_Gros = 'La taille du fichier excède les limites';

$L_Fichier_Non_Autorise = 'Fichier non autorisé. Seuls les fichiers suivants sont autorisés : ';
$L_Visualiser = 'Visualiser';
$L_Confirmer_Supprimer_Fichier = '<div class="col-lg-12"><span><strong>%libelle_fichier</strong></span> (<span class="fg_couleur_1">%nom_fichier</span>)</div>' .
	'<div class="col-lg-12">Voulez-vous vraiment supprimer ce fichier ?</div>';
$L_Fichier_Supprime = 'Fichier supprimé';

$L_Chercher = 'Chercher';

$L_Parametre_Invalide = 'Paramètre invalide';
$L_Parametres_Invalides = 'Paramètres invalides';
$L_Fichier_Introuvable_Inaccessible = 'Fichier introuvable ou inaccessible';

$L_Gestion_Grilles_Impacts = 'Gestion des grilles d\'impacts';
$L_Gestion_Grilles_Vraisemblances = 'Gestion des grilles de vraisemblances';
$L_Criteres = 'Critères';
$L_Critere = 'Critère';

$L_Disponibilite = 'Disponibilité';
$L_Integrite = 'Intégrité';
$L_Confidentialite = 'Confidentialité';
$L_Preuve = 'Preuve';

$L_Mot_Passe = 'Mot de passe';

$L_Tout_Cocher_Decocher = 'Tout cocher / décocher';

$L_Besoin_Securite = 'Besoin de sécurité';

$L_Total_Cartographies_Associees = 'Total de Cartographies associées';

$L_Changement_Univers = 'Changement d\'Univers';
$L_Mes_Tableaux_Bord = 'Visualisation des tableaux de bord';
$L_Gestion_Continuite = 'Gestion de la continuité';
$L_Gestion_Carto_Risques = 'Gestion des cartographies des risques';

$L_Societe = 'Société';
$L_Societes = 'Sociétés';
$L_Gestion_Societes = 'Gestion des sociétés';
$L_Societe_Change = 'Société changée';

$L_Gestion = 'Gestion';
$L_Visualisation = 'Visualisation';

$L_Nom = 'Nom';
$L_Nouveau_Nom = 'Nouveau nom';

$L_Campagne = 'Campagne';
$L_Campagnes = 'Campagnes';
$L_Gestion_Campagnes = 'Gestion des campagnes';
$L_Campagne_Change = 'Campagne changée';

$L_Activite = 'Activité';
$L_Activites = 'Activités';

$L_Personne_Cle = 'Personne clé';
$L_Personnes_Cles = 'Personnes clés';

$L_Utilisateur_Habilite = 'Utilisateur habilité';
$L_Utilisateurs_Habilites = 'Utilisateurs habilités';

$L_Effectif = 'Effectif';
$L_Effectifs = 'Effectifs';

$L_Site = 'Site';
$L_Sites = 'Sites';
$L_Site_Nominal = 'Site nominal';
$L_Site_Secours = 'Site de secours';

$L_Niveau_Appreciation = 'Niveau d\'appréciation';
$L_Niveaux_Appreciation = 'Niveaux d\'appréciation';

$L_Gestion_Echelles_Temps = 'Gestion des échelles de temps';
$L_Echelles_Temps = 'Echelles de temps';
$L_Echelle_Temps = 'Echelle de temps';

$L_Gestion_Fournisseurs = 'Gestion des fournisseurs';
$L_Fournisseurs = 'Fournisseurs';
$L_Fournisseur = 'Fournisseur';

$L_Types_Fournisseur = 'Types de fournisseur';

$L_Parties_Prenantes = 'Parties prenantes';
$L_Partie_Prenante = 'Partie prenante';

$L_Matrice_Impacts = 'Matrice des Impacts';
$L_Matrices_Impacts = 'Matrices des Impacts';
$L_Niveau = 'Niveau';

$L_Gestion_BIA = 'Gestion des BIA';

$L_Societe_Sans_Campagne = 'Attention, cette Société ne dispose pas de Campagne';
$L_Campagne_Sans_Entite = 'Attention, cette Campagne ne dispose pas d\'Entité';

$L_Role = 'Rôle';
$L_Roles_Parties_Prenantes = 'Rôles des Parties Prenantes';

$L_Aucun = 'Aucun';
$L_Acune = 'Aucune';

$L_Continuite = 'Continuité';

$L_Detail = 'Détail';
$L_Details = 'Détails';

$L_DMIA_Long = 'Durée d\'Interruption Maximale Admissible';
$L_DMIA_Court = 'DMIA';
$L_DMIA_SI = 'DMIA SI';

$L_PDMA_Long = 'Perte de Données Maximale Admissible';
$L_PDMA_Court = 'PDMA';
$L_PDMA_SI = 'PDMA SI';

$L_Gestion_Effectifs = 'Gestion de l\'effectifs';
$L_Gestion_Activites = 'Gestion des activités';
$L_Gestion_Sites = 'Gestion des sites';

$L_Ecrans_Administration = 'Ecrans d\'administration';
$L_Ecrans_Visualisation = 'Ecrans de visualisation';
$L_Ecrans_Gestion = 'Ecrans de gestion';
$L_Ecrans_Referentiel = 'Ecrans du référentiel';

$L_Initialiser = 'Initialiser';
$L_Reinitialiser = 'Réinitialiser';


// Gestion des libellés relatif aux éditions de rapports.
$L_Visualiser_BIA = 'Visualiser les BIA';
$L_Editer_BIA = 'Editer les BIA';
$L_Edition_BIA = 'Edition des BIA';
$L_Gestion_Editions_BIA = 'Gestion des éditions des BIA';

$L_Format_Edition = 'Format de l\'édition';
$L_Tout_Cocher_Decocher = 'Tout cocher / Décocher';
$L_Chapitres = 'Chapitres';

$L_Edition_Terminee = 'Edition terminée';
$L_Dossier_Restitution = 'Dossier de restitution';


// Gestion des erreurs sur les changements de Société, de Campagne et d'Entité
$L_Pas_Societe_Autorisee_Pour_Utilisateur = 'Pas de Société autorisée pour cet utilisateur';
$L_Societe_Plus_Autorisee_Pour_Utilisateur = 'La Société n\'est plus autorisée pour cet utilisateur';
$L_Pas_Campagne_Pour_Societe = 'Cette Société ne dispose pas de Campagne';
$L_Campagne_Existe_Plus = 'Cette Campagne n\'existe plus';
$L_Plus_De_Societe = 'Erreur grave : il n\'y a plus de Société';
$L_Entite_Existe_Plus = 'Cette Entité n\'existe plus';

$L_Annexe = 'Annexe';

$L_Pas_Droit_Ressource = 'Vous n\'avez pas de droit sur cette ressource';

$L_Valider = 'Valider';
$L_Valider_BIA = 'Valider BIA';
$L_Validation_BIA_Entite = 'Validation des BIA de l\'Entité';
$L_Valider_BIA_Entite = 'Valider les BIA de l\'Entité';
$L_Valideur = 'Valideur';
$L_Date_Validation = 'Date de validation';
$L_Informations_Validation = 'Informations de validation';
$L_Entite_Validee = 'BIA de l\'Entité validés';

$L_Accueil = 'Accueil';

$L_Ecran_Synthese = 'Ecran de synthèse';

$L_M_Comparateur_DMIA_Activites = 'Comparateur DMIA (Activités)';

$L_Specifique = 'Spécifique';
$L_Specifique_A = 'Spécifique à';

$L_Libelles_Referentiel = 'Libellés du référentiel';

?>