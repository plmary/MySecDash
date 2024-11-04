<?php
/**
* Libellé générique en Anglais.
*
* @warning Ce fichier doit impérativement être au format UTF-8 pour une gestion cohérente des caractères accentués.
*
* @copyright Copyright Loxense
* @author Pierre-Luc MARY
* @date 2013-03-25
*/

include_once( 'Constants.inc.php');

include( HBL_DIR_LABELS . '/' . $_SESSION[ 'Language' ] . '_HBL_Generiques.inc.php' );

$L_Welcome = 'Welcome';
$L_ExpireDans = 'Expire in';

$L_Libelle = 'Label';

$L_Administration = "Administration";

$L_Referentiel = 'Referential';
$L_Referentiel_Interne = "Internal referential";

$L_Parametres_Base = "Basic settings";
$L_Gestion_Parametres_Base = "Basic settings management";

$L_Proprietaire = 'Owner';
$L_Proprietaires = 'Owners';

$L_Types_Actif_Support = "Support asset types";
$L_Type_Actif_Support = "Support asset type";
$L_Gestion_Types_Actif_Support = $L_Types_Actif_Support . " management";

$L_Menaces = 'Threats';
$L_Menace = 'Threat';
$L_Menaces_Generiques = "Generics " . mb_strtolower( $L_Menaces );
$L_Menace_Generique = "Generic " . mb_strtolower( $L_Menace );
$L_Gestion_Menaces_Generiques = $L_Menaces_Generiques . " management" ;

$L_Types_Menace_Generique = "Generic threat types";
$L_Type_Menace_Generique = "Generic threat type";
$L_Gestion_Types_Menace_Generique = $L_Types_Menace_Generique . " management";

$L_Vulnerabilites = 'Vulnerabilities';
$L_Vulnerabilite = 'Vulnerability';
$L_Vulnerabilites_Generiques = "Generics vulnerabilities";
$L_Vulnerabilite_Generique = "Generic vulnerability";
$L_Gestion_Vulnerabilites_Generiques = $L_Vulnerabilites_Generiques . ' management';

$L_Source = 'Source';
$L_Sources = 'Sources';
$L_Cible = 'Target';
$L_Cibles = 'Targets';
$L_Sources_Risques = "Sources of risks";
$L_Source_Risque = "Source of risks";
$L_Gestion_Sources_Risques = $L_Sources_Risques . ' management';

$L_Sources_Menaces = "Sources of threat";
$L_Source_Menace = "Source of threat";
$L_Gestion_Sources_Menaces = $L_Sources_Menaces . ' management';

$L_Objectifs_Vises = 'Targets objectives';
$L_Objectif_Vise = 'Target objective';
$L_Gestion_Objectifs_Vises = $L_Objectifs_Vises . ' management';

$L_Partie_Prenante = 'Stakeholder';
$L_Parties_Prenantes = 'Stakeholders';
$L_Gestion_Parties_Prenantes = $L_Parties_Prenantes . ' management';

$L_Risques_Generiques = "Generics risks";
$L_Risque_Generique = "Generic risk";
$L_Gestion_Risques_Generiques = $L_Risques_Generiques . ' management';

$L_Aucun_Risque_Generique_Associe = 'No generic risk associated';
$L_Aucune_Action_Generique_Associee = 'No generic action associated';
$L_Aucune_Mesure_Referentiel_Associee = 'No referential control associated';

$L_Types_Traitement_Risques = "Risks treatment types";
$L_Type_Traitement_Risques = "Risks treatment type";
$L_Gestion_Types_Traitement_Risques = $L_Types_Traitement_Risques . ' management';

$L_Impacts_Generiques = "Generics impacts";
$L_Impact_Generique = "Generic impact";
$L_Gestion_Impacts_Generiques = $L_Impacts_Generiques . ' management';

$L_Mesures_Generiques = "Loxense measures";
$L_Mesure_Generique = "Loxense measure";
$L_Gestion_Mesures_Generiques = $L_Mesures_Generiques . ' management';

$L_Mesures_Referentiels = "Referentials controls";
$L_Mesure_Referentiel = "Referential control";
$L_Gestion_Mesures_Referentiels = $L_Mesures_Referentiels . ' management';

$L_Referentiels_Conformite = "Compliances referentials";
$L_Referentiel_Conformite = "Compliance referential";
$L_Gestion_Referentiels_Conformite = $L_Referentiels_Conformite . ' management';

$L_Controle_Acces = "Access control";
$L_Controles_Acces = "Access controls";
$L_Gestion_Controles_Acces = "Access controls management";

$L_Gestion_Entites = "Entities management";
$L_Entites = "Entities";
$L_Entite = "Entity";
$L_Entite_Change = 'Entity changed';

$L_Gestion_Civilites = "Civilities management";
$L_Civilites = "Civilities";
$L_Civilite = "Civility";

$L_Gestion_Utilisateurs = "Users management";
$L_Utilisateurs = "Users";
$L_Utilisateur = "User";

$L_Gestion_Profils = "Profiles management";
$L_Profils = "Profiles";
$L_Profil = "Profile";

$L_Gestion_Applications = "Applications management";
$L_Applications = 'Applications';
$L_Application = "Application";

$L_Gestion_ApplicationsInternes = "Internal application management";
$L_ApplicationsInternes = 'Internal applications';
$L_ApplicationInterne = "Internal application";

$L_Gestion_Privileges = "Rights management";
$L_Privileges = "Rights";
$L_Privilege = "Right";

$L_Consultation_Historique = "View historical of actions";
$L_Historique = "Historical of actions";

$L_Export_Base = "Database Export";

$L_Gestion_Risques = "Risks management";

$L_Cartographies = "Maps";
$L_Cartographie = "Map";
$L_Cartographies_Risques = "Risks maps";
$L_Cartographie_Risques = "Risk map";
$L_Gestion_Cartographies_Risques = $L_Cartographies_Risques . " management";
$L_Gerer_Cartographies  = 'Manage the maps';

$L_Criteres_Valorisation_Actifs = "Asset rating criteria"; //criteres de valorisation des actifs - D/I/C
$L_Critere_Valorisation_Actifs = "Asset rating criteria"; //criteres de valorisation des actifs - D/I/C
$L_Gestion_Criteres_Valorisation_Actifs = $L_Criteres_Valorisation_Actifs . " management";

$L_Criteres_Appreciation_Risques = "Risk assessment and risk acceptance criteria"; // Critères d'appréciation et d'acceptation des risques (Probabilité de survenance...)
$L_Critere_Appreciation_Risques = "Risk assessment and risk acceptance criteria"; // Critères d'appréciation et d'acceptation des risques (Probabilité de survenance...)
$L_Gestion_Criteres_Appreciation_Acceptation_Risques = $L_Criteres_Appreciation_Risques . " management";

$L_Editions_Risques = "Risks edit";
$L_Edition_Risques = "Risks edit";
$L_Gestion_Editions_Risques = $L_Editions_Risques . " management";

$L_Matrices_Risques = "Risks matrix";
$L_Matrice_Risques = "Risks matrix";
$L_Visualisation_Matrices_Risques = "View " . mb_strtolower( $L_Matrices_Risques );

$L_Actifs = "Assets";
$L_Actif = "Asset";
$L_Actifs_Primordiaux = "Primary assets";
$L_Actif_Primordial = "Primary Asset";
$L_Gestion_Actifs_Primordiaux = $L_Actifs_Primordiaux . ' management';

$L_Actifs_Supports = "Support assets";
$L_Actif_Support = "Support asset";
$L_Gestion_Actifs_Supports = $L_Actifs_Supports . ' management';
$L_Referentiel_Actifs_Supports = $L_Actifs_Supports . ' referential';
$L_Gestion_Referentiel_Actifs_Supports = $L_Actifs_Supports . ' referential management';

$L_Evenements_Redoutes = "Feared events";
$L_Evenement_Redoute = "Feared event";
$L_Gestion_Evenements_Redoutes = $L_Evenements_Redoutes . ' management';

$L_Risques = "Risks";
$L_Risque = "Risk";
$L_Identification_Evaluation_Risques = "Risks identification and assessment";
$L_Traitement_Risques = "Risks treatment";
$L_Traitement_Risque = "Risk treatment";

$L_Appreciation_Risques = 'Risks assessment';
$L_Gestion_Appreciation_Risques = $L_Appreciation_Risques .' management';
$L_Gestion_Traitement_Risques = 'Risks treatment management';

$L_Gestion_Conformite = "Compliance management";
$L_Gerer_Conformite = "Manage Compliance";
$L_Matrice_Conformite = "Compliance matrix";
$L_Edition_Conformite = "Editing compliance";
$L_Editer_Conformite = "Edit compliance";

$L_Gestion_Actions = "Actions management";
$L_Gerer_Actions = 'Manage actions';
$L_Edition_Actions = "Editing Actions";
$L_Editer_Actions = "Edit actions";
$L_Actions_Generiques = 'Generics actions';
$L_Action_Generique = 'Generic action';

$L_Gestion_ImportExport_Base = 'Import and Export database';
$L_Exporter_Base = 'Export database';
$L_Restaurer_Base = 'Restore database';
$L_Sauvegarder_Base = 'Save database';
$L_Sauvegarde_Terminee = 'Save terminated';
$L_Restauration_Terminee = 'Restore terminated';

$L_Gestionnaire = 'Technical team';
$L_Gestionnaires = 'Technical team';
$L_Gestion_Gestionnaires = $L_Gestionnaires . ' management';

$L_Vision_Consolidee = 'Consolidated view';

$L_Logo1 = "From risks to actions"; // Risks to Actions
$L_Logo2 = "Actions to compliance"; // Actions to Conformity

$L_Consulter = "View";
$L_Creer = 'Create';
$L_Ajouter = "Add";
$L_Ajoute = "Added";
$L_Modifier = "Modify";
$L_Modifie = 'Modified';
$L_Supprimer = "Remove";
$L_Supprime = "Removed";
$L_Associer = 'Associate';
$L_Associe = 'Associated';
$L_Dissocier = 'Disassociate';
$L_Dissocie = 'Disassociated';
$L_Dupliquer = 'Duplicate';
$L_Duplique = 'Duplicated';
$L_Consulter_Historique = 'View history';
$L_Charger = 'Load';
$L_Fermer = "Close";
$L_A_Valider = 'To validate';
$L_Deconnexion = "Logout";
$L_Imprimer = 'Print';
$L_Editer = 'Edit';
$L_Changer_Mot_Passe_Obligatoire = "Change password";
$L_Retour = 'Return';
$L_Envoyer = 'Send';
$L_Rechercher = 'Search';
$L_Reinitialiser = 'Reset';
$L_Validation = 'Validation';
$L_Partager = 'Share';

$L_Selectionner_Cartographie = 'Map select';
$L_Regenerer_Riques = 'Regenerate risks'; // Remake
$L_Ignorer_Risque = 'Risk ignore';
$L_Risques_Regeneres = 'Risks regenerated';
$L_Generer_Impression = 'Generate printing';
$L_Telecharger_Impression = 'Download printing';
$L_Generer_Fichier_Excel = 'Generate Excel file';
$L_Telecharger_Fichier_Excel = 'Download Excel file';
$L_Impression = 'Print';
$L_Impression_Generee = 'Generated printing';
$L_Reinitialisation = 'Reset';
$L_Confirmation_Reinitialisation = 'Are you sure you want to reset the criteria?';
$L_Reinitialisation_Terminee = 'Reset complete';
$L_Initialisation_Terminee = 'Initialization complete';

$L_Autres_Supports = "Others supports";
$L_Autre_Support = "Other support";
$L_Vulnerabilites = "Vulnerabilities";
$L_Vulnerabilite = "Vulnerability";
$L_Associations = 'Associations';
$L_Association = 'Association';
$L_Couleur = 'Color';
$L_Mesure = "Measure";
$L_Mesures = "Measures";
$L_Periode = "Period";
$L_Periodes = "Periods";
$L_Poids = "Weight";
$L_Cotation = "Notation";
$L_Cotations = "Notations";
$L_Impacts = 'Impacts';
$L_Impact = "Impact";
$L_Vraisemblance = "Likelihood";
$L_Vraisemblances = "Likelihoods";
$L_Numero = "Number";
$L_Version = "Version";
$L_DDA = "SOA"; // Statement of Applicability
$L_Couverture = "Coverage"; // ???
$L_Justificatif = "Justificative"; // ???
$L_Etat = "Status";
$L_ASA = "A.S.A.";
$L_Access_Controls = 'Access controls';
$L_Access_Control = 'Access control';

$L_Coche = 'Checked';
$L_Non_Coche = 'Unchecked';

$L_Liste_Cartographies = "List of maps";
$L_Cartographie_Courante_Changee = "Current mapping changed";
$L_Cartographie_Courante = 'Current mapping';
$L_Aucune_Cartographie = "No mapping";

$L_ERR_Champs_Obligatoires = 'Some fields are mandatory';
$L_ERR_Champ_Obligatoire = 'This field is mandatory';

$L_Valorisation = 'Valuation';
$L_Aucune_Valorisation = 'No valuation';
$L_Aucune_Modification = 'No modification';
$L_Modification_Realisee = 'Modification made';
$L_Copie = 'Copy';
$L_Manuel = 'Manual';
$L_Manuellement = 'Manuelly';
$L_Automatique = 'Automatic';
$L_Automatiquement = 'Automatically';

$L_Tout_Selectionner = 'Select all';
$L_Tout_Deselectionner = 'Deselect all';

$L_Supprimer_Libelle = 'Remove label';
$L_Modifier_Libelle = 'Modify label';
$L_Ajouter_Libelle = 'Add label';
$L_Creer_Libelle = 'Create label';

$L_Libelle_Supprime = 'Removed label';
$L_Libelle_Modifie = 'Modified label';
$L_Libelle_Ajoute = 'Added label';
$L_Libelle_Cree = 'Created label';

$L_MaJ_Libelle = 'Label updated';
$L_Description = 'Description';
$L_Commentaire = 'Comment';

$L_Travail_En_Cours = 'Work in progress';

$L_A_Definir = 'To define';
$L_Non_Defini = "Not defined";

$L_Total = 'Total';
$L_Limitation_Licence = 'You have reached the limits of your license';

$L_De_Date = 'From';
$L_A_Date = 'To';
$L_Format_Date_Heure = 'YYYY-MM-DD HH:MM';
$L_Format_Date = 'YYYY-MM-DD';

$L_Tous = 'All';
$L_Toutes = 'All';

$L_Illimite = 'Unlimited';

$L_Langue = 'Language';
$L_Langue_fr = 'French';
$L_Langue_en = 'English';

$L_En_Cours = 'In progress';

$L_Criteres_Representation_Niveaux_Risque = 'Risk level representation criterias';
$L_Critere_Representation_Niveaux_Risque = 'Risk level representation criterias';
$L_Grilles_Impact = 'Impact grids';
$L_Grille_Impact = 'Impact grid';
$L_Types_Impact = 'Impact types';
$L_Type_Impact = 'Impact type';
$L_Niveaux_Impact = 'Impact levels';
$L_Niveau_Impact = 'Impact level';

$L_Grilles_Vraisemblance = 'Likelihood grids';
$L_Grille_Vraisemblance = 'Likelihood grid';
$L_Types_Vraisemblance = 'Likelihood types';
$L_Type_Vraisemblance = 'Likelihood type';
$L_Niveaux_Vraisemblance = 'Likelihood levels';
$L_Niveau_Vraisemblance = 'Likelihood level';

$L_Telecharger_Word = 'Download Word file';
$L_Telecharger_Excel = 'Download Excel file';

$L_Etiquettes = 'Tags';
$L_Etiquette = 'Tag';
$L_Gestion_Etiquettes = 'Tags management';

$L_Ordre = 'Order';

$L_Informations_Complementaires = 'Further informations';
$L_Telecharger_Informations = 'Upload informations';
$L_Telecharger_Depuis = 'Download';
$L_Telecharger_Vers = 'Upload';
$L_Transferer_Fichier = 'Upload file';
$L_Fichier_Transfere = 'Uploaded file';
$L_Copy_Loxense_Impossible = 'Unable to copy the file to the specific Loxense directory';
$L_Nom_Fichier_Non_Valide = 'Invalid file name';
$L_Fichier_Temporaire_Introuvable = 'The temporary file could not be found';
$L_Pas_Fichier_A_Transferer = 'No file to upload';
$L_Fichier_Trop_Gros = 'The size of the file exceeds the limits';

$L_Fichier_Non_Autorise = 'File not allowed. Only the following files are allowed: ';
$L_Visualiser = 'View';
$L_Confirmer_Supprimer_Fichier = '<div class="col-lg-12"><span><strong>%libelle_fichier</strong></span> (<span class="fg_couleur_1">%nom_fichier</span>)</div>' .
	'<div class="col-lg-12">Do you really want to delete this file?</div>';
$L_Fichier_Supprime = 'File deleted';

$L_Chercher = 'Search';

$L_Parametre_Invalide = 'Invalid parameter';
$L_Parametres_Invalides = 'Invalid parameters';
$L_Fichier_Introuvable_Inaccessible = 'File not found or inaccessible';

$L_Gestion_Grilles_Impacts = 'Management of impact grids';
$L_Gestion_Grilles_Vraisemblances = 'Management of likelihood grids';
$L_Criteres = 'Criteria';
$L_Critere = 'Criteria';

$L_Mot_Passe = 'Password';

$L_Tout_Cocher_Decocher = 'Check / uncheck all';

$L_Besoin_Securite = 'Security need';

$L_Changement_Univers = 'Change of universe';
$L_Mes_Tableaux_Bord = 'Viewing dashboards';
$L_Gestion_Continuite = 'Continuity management';
$L_Gestion_Carto_Risques = 'Risk mapping management';

$L_Societe = 'Company';
$L_Societes = 'Companies';
$L_Gestion_Societes = 'Companies management';
$L_Societe_Change = 'Compagny changed';

$L_Gestion = 'Managing';
$L_Visualisation = 'Viewing';

$L_Nom = 'Name';
$L_Nouveau_Nom = 'New name';

$L_Campagnes = 'Campaigns';
$L_Campagne = 'Campaign';
$L_Gestion_Campagnes = 'Campaigns Management';
$L_Campagne_Change = 'Campaign changed';

$L_Partie_Prenante = 'Stakeholder';
$L_Parties_Prenantes = 'Stakeholders';

$L_Activite = 'Activity';
$L_Activites = 'Activities';

$L_Personne_Cle = 'Personne clé';
$L_Personnes_Cles = 'Personnes clés';

$L_Utilisateur_Habilite = 'Authorized user';
$L_Utilisateurs_Habilites = 'Authorized users';

$L_Effectif = 'Staff';
$L_Effectifs = 'Staffs';

$L_Site = 'Site';
$L_Sites = 'Sites';
$L_Site_Nominal = 'Nominal Site';
$L_Site_Secours = 'Backup Site';

$L_Niveau_Appreciation = 'Assessment level';
$L_Niveaux_Appreciation = 'Assessment levels';

$L_Gestion_Echelles_Temps = 'Time scales management';
$L_Echelles_Temps = 'Time scales';
$L_Echelle_Temps = 'Time scale';

$L_Gestion_Fournisseurs = 'Suppliers management';
$L_Fournisseurs = 'Suppliers';
$L_Fournisseur = 'Supplier';

$L_Types_Fournisseur = 'Supplier types';

$L_Parties_Prenantes = 'Stakeholders';
$L_Partie_Prenante = 'Stakeholder';

$L_Matrice_Impacts = 'Impacts Matrix';
$L_Matrices_Impacts = 'Impacts Matrix';
$L_Niveau = 'Level';

$L_Societe_Sans_Campagne = 'Warning, this Company does not have a Campaign';
$L_Campagne_Sans_Entite = 'Warning, this Campaign does not have an Entity';

$L_Role = 'Role';
$L_Roles_Parties_Prenantes = 'Roles of stakeholders';

$L_Aucun = 'None';
$L_Acune = 'None';

$L_Continuite = 'Continuity';

$L_Detail = 'Detail';
$L_Details = 'Details';

$L_DMIA_Long = 'Recovery Time Objective';
$L_DMIA_Court = 'RTO';

$L_PDMA_Long = 'Recovery Point Objective';
$L_PDMA_Court = 'RPO';

$L_Gestion_Effectifs = 'People management';
$L_Gestion_Activites = 'Activities management';
$L_Gestion_Sites = 'Sites management';

$L_Ecrans_Administration = 'Administration screens';
$L_Ecrans_Visualisation = 'Visualization screens';
$L_Ecrans_Gestion = 'Management screens';
$L_Ecrans_Referentiel = 'Referential screens';

$L_Initialiser = 'Initialize';
$L_Reinitialiser = 'Reinitialize';

$L_Editer_BIA = 'Editing BIAs';


// Gestion des libellés relatif aux éditions de rapports.
$L_Visualiser_BIA = 'View BIAs';
$L_Editer_BIAs = 'Edit BIAs';
$L_Edition_BIA = 'BIAs Edition';
$L_Gestion_Editions_BIA = 'Managing BIA editions';

$L_Format_Edition = 'Edition format';
$L_Tout_Cocher_Decocher = 'Check all / Uncheck all';
$L_Chapitres = 'Chapters';

$L_Edition_Terminee = 'Edition done';
$L_Dossier_Restitution = 'Restitution folder';


// Gestion des erreurs sur les changements de Société, de Campagne et d'Entité
$L_Pas_Societe_Autorisee_Pour_Utilisateur = 'No Company authorised for this user';
$L_Societe_Plus_Autorisee_Pour_Utilisateur = 'The Company is no longer authorised for this user';
$L_Pas_Campagne_Pour_Societe = 'This Company does not have a Campaign';
$L_Campagne_Existe_Plus = 'This campaign no longer exists';
$L_Plus_De_Societe = 'Serious error: there is no longer a Company';
$L_Entite_Existe_Plus = 'This entity no longer exists';

$L_Annexe = 'Appendix';

$L_Pas_Droit_Ressource = 'You have no rights to this resource';

$L_Valider = 'Validate';
$L_Valider_BIA = 'Validate BIAs';
$L_Validation_BIA_Entite = 'Entity BIAs validation';
$L_Valider_BIA_Entite = 'Validate Entity BIAs';
$L_Valideur = 'Validator';
$L_Date_Validation = 'Validation date';
$L_Informations_Validation = 'Validation information';
$L_Entite_Validee = 'Entity BIAs validated';

$L_Accueil = 'Homepage';

$L_Ecran_Synthese = 'Overview screen';

?>