--
-- Auteur  : Pierre-Luc MARY
-- Date    : 2025-02-17
-- Package : Loxense
--
-- Commentaire :
-- Ce script insère les libellés de base du référentiel interne de "MySecDash".
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

SET search_path = public, pg_catalog;


INSERT INTO lbr_libelles_referentiel (lbr_code, lng_id, lbr_libelle) VALUES
-- ************************************
-- Libellés systèmes
("279","en","__LRI_ACTIVITE","Activity"),
("280","fr","__LRI_ACTIVITE","Activité"),
("282","fr","__LRI_ACTIVITES","Activités"),
("281","en","__LRI_ACTIVITES","Activities"),
("267","fr","__LRI_AUCUN","Aucun"),
("268","en","__LRI_AUCUN","None"),
("264","en","__LRI_CORRESPONDANT_PCA","BCP Correspondent"),
("263","fr","__LRI_CORRESPONDANT_PCA","Correspondant PCA"),
("260","fr","__LRI_CPCA","CPCA"),
("259","en","__LRI_CPCA","BCPC"),
("208","fr","__LRI_CREATION_LIBELLES_REFERENTIEL","Création du libellé dans le référentiel"),
("207","en","__LRI_CREATION_LIBELLES_REFERENTIEL","Creating a label in the repository"),
("265","en","__LRI_DATE_ENTRETIEN","Interview date"),
("266","fr","__LRI_DATE_ENTRETIEN","Date de l'entretien"),
("295","en","__LRI_DESCRITPION_ENTRAIDES_INTERNE_EXTERNE","Description of mutual aid (internal / external)"),
("296","fr","__LRI_DESCRITPION_ENTRAIDES_INTERNE_EXTERNE","Description des entraides (interne / externe)"),
("293","en","__LRI_DESCRITPION_STRATEGIE_MONTEE_CHARGE","Description of the ramp-up strategy"),
("294","fr","__LRI_DESCRITPION_STRATEGIE_MONTEE_CHARGE","Description de la stratégie de la montée en charge"),
("302","fr","__LRI_ECHELLES","Echelles"),
("301","en","__LRI_ECHELLES","Ladders"),
("285","en","__LRI_EFFECTIF","Headcount"),
("286","fr","__LRI_EFFECTIF","Effectif"),
("288","fr","__LRI_EFFECTIF_TOTAL_ENTITE","Effectif total de l'Entité"),
("287","en","__LRI_EFFECTIF_TOTAL_ENTITE","Entity's total Headcount"),
("299","fr","__LRI_EFFECTIFS","Effectifs"),
("300","en","__LRI_EFFECTIFS","Headcount"),
("234","fr","__LRI_ERR_SYS_CHAMPS_OBLIGATOIRES","Des champs obligatoires n'ont pas été renseignés"),
("233","en","__LRI_ERR_SYS_CHAMPS_OBLIGATOIRES","Mandatory fields have not been completed"),
("232","fr","__LRI_ERR_SYS_DEJA_EXISTANT","Objet déjà existant"),
("231","en","__LRI_ERR_SYS_DEJA_EXISTANT","Object already exists"),
("230","fr","__LRI_ERR_SYS_PAS_LES_DROITS","Vous n'avez pas les droits pour cette action"),
("229","en","__LRI_ERR_SYS_PAS_LES_DROITS","You don't have the rights for this action"),
("271","fr","__LRI_EXTERNE","Externe"),
("272","en","__LRI_EXTERNE","External"),
("206","en","__LRI_GESTION_LIBELLES_REFERENTIEL","Managing repository labels"),
("205","fr","__LRI_GESTION_LIBELLES_REFERENTIEL","Gestion des libellés du référentiel"),
("270","en","__LRI_INTERNE","Internal"),
("269","fr","__LRI_INTERNE","Interne"),
("209","en","__LRI_LIBELLE_REFERENTIEL_CREE","Label in the repository created"),
("210","fr","__LRI_LIBELLE_REFERENTIEL_CREE","Libellé dans le référentiel créé"),
("214","fr","__LRI_LIBELLE_REFERENTIEL_MODIFIE","Libellé dans le référentiel modifié"),
("213","en","__LRI_LIBELLE_REFERENTIEL_MODIFIE","The label in the repository modified"),
("217","en","__LRI_LIBELLE_REFERENTIEL_SUPPRIME","The label in the repository deleted"),
("218","fr","__LRI_LIBELLE_REFERENTIEL_SUPPRIME","Libellé dans le référentiel supprimé"),
("240","fr","__LRI_LIBELLES_REFERENTIEL","Libellés du référentiel"),
("239","en","__LRI_LIBELLES_REFERENTIEL","Repository labels"),
("306","en","__LRI_LISTE_INTERDEPENDANCES","List of interdependencies"),
("305","fr","__LRI_LISTE_INTERDEPENDANCES","Liste des interdépendances"),
("303","fr","__LRI_LISTE_PERSONNES_PRIORITAIRES","Liste des personnes prioritaires"),
("304","en","__LRI_LISTE_PERSONNES_PRIORITAIRES","List of priority persons"),
("212","fr","__LRI_MODIFICATION_LIBELLES_REFERENTIEL","Modification du libellé dans le référentiel"),
("211","en","__LRI_MODIFICATION_LIBELLES_REFERENTIEL","Modifying the label in the repository"),
("278","fr","__LRI_ORGANISATION","Organisation"),
("277","en","__LRI_ORGANISATION","Organization"),
("292","fr","__LRI_PERSONNES_PRIORITAIRES","Personnes prioritaires"),
("291","en","__LRI_PERSONNES_PRIORITAIRES","Priority people"),
("297","en","__LRI_PLANNING","Planning"),
("298","fr","__LRI_PLANNING","Planning"),
("273","en","__LRI_SITE_NOMINAL","Nominal site"),
("274","fr","__LRI_SITE_NOMINAL","Site nominal"),
("275","en","__LRI_SITE_SECOURS","Emergency site"),
("276","fr","__LRI_SITE_SECOURS","Site de secours"),
("216","fr","__LRI_SUPPRESSION_LIBELLES_REFERENTIEL","Suppression du libellé dans le référentiel"),
("215","en","__LRI_SUPPRESSION_LIBELLES_REFERENTIEL","Deleting the label in the repository"),
("283","en","__LRI_SYNTHESE","Summary"),
("284","fr","__LRI_SYNTHESE","Synthèse"),
("220","fr","__LRI_SYS_AJOUTER","Ajouter"),
("219","en","__LRI_SYS_AJOUTER","Add"),
("235","en","__LRI_SYS_CODE","Code"),
("236","fr","__LRI_SYS_CODE","Code"),
("222","fr","__LRI_SYS_CREER","Créer"),
("221","en","__LRI_SYS_CREER","Create"),
("250","fr","__LRI_SYS_ERREUR","Erreur"),
("249","en","__LRI_SYS_ERREUR","Error"),
("227","en","__LRI_SYS_FERMER","Close"),
("228","fr","__LRI_SYS_FERMER","Fermer"),
("243","en","__LRI_SYS_LANGUAGE","Language"),
("244","fr","__LRI_SYS_LANGUAGE","Language"),
("242","fr","__LRI_SYS_LANGUE","Langue"),
("241","en","__LRI_SYS_LANGUE","Language"),
("237","en","__LRI_SYS_LIBELLE","Label"),
("238","fr","__LRI_SYS_LIBELLE","Libellé"),
("224","fr","__LRI_SYS_MODIFIER","Modifier"),
("223","en","__LRI_SYS_MODIFIER","Modify"),
("248","fr","__LRI_SYS_SUCCES","Succès"),
("247","en","__LRI_SYS_SUCCES","Success"),
("225","en","__LRI_SYS_SUPPRIMER","Delete"),
("226","fr","__LRI_SYS_SUPPRIMER","Supprimer"),
("289","en","__LRI_TAUX_OCCUPATION","Occupancy rate"),
("290","fr","__LRI_TAUX_OCCUPATION","Taux d'occupation")
;