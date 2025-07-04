--
-- Auteur  : Pierre-Luc MARY
-- Date    : 2025-02-17
-- Package : Loxense
--
-- Commentaire :
-- Ce script insère les libellés de base du référentiel interne de 'MySecDash'.
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
('__LRI_ACTIVITE','en','Activity'),
('__LRI_ACTIVITE','fr','Activité'),
('__LRI_ACTIVITES','fr','Activités'),
('__LRI_ACTIVITES','en','Activities'),
('__LRI_AUCUN','fr','Aucun'),
('__LRI_AUCUN','en','None'),
('__LRI_CORRESPONDANT_PCA','en','BCP Correspondent'),
('__LRI_CORRESPONDANT_PCA','fr','Correspondant PCA'),
('__LRI_CPCA','fr','CPCA'),
('__LRI_CPCA','en','BCPC'),
('__LRI_CREATION_LIBELLES_REFERENTIEL','fr','Création du libellé dans le référentiel'),
('__LRI_CREATION_LIBELLES_REFERENTIEL','en','Creating a label in the repository'),
('__LRI_DATE_ENTRETIEN','en','Interview date'),
('__LRI_DATE_ENTRETIEN','fr','Date de l''entretien'),
('__LRI_DESCRITPION_ENTRAIDES_INTERNE_EXTERNE','en','Description of mutual aid (internal / external)'),
('__LRI_DESCRITPION_ENTRAIDES_INTERNE_EXTERNE','fr','Description des entraides (interne / externe)'),
('__LRI_DESCRITPION_STRATEGIE_MONTEE_CHARGE','en','Description of the ramp-up strategy'),
('__LRI_DESCRITPION_STRATEGIE_MONTEE_CHARGE','fr','Description de la stratégie de la montée en charge'),
('__LRI_ECHELLES','fr','Echelles'),
('__LRI_ECHELLES','en','Ladders'),
('__LRI_EFFECTIF','en','Headcount'),
('__LRI_EFFECTIF','fr','Effectif'),
('__LRI_EFFECTIF_TOTAL_ENTITE','fr','Effectif total de l''Entité'),
('__LRI_EFFECTIF_TOTAL_ENTITE','en','Entity''s total Headcount'),
('__LRI_EFFECTIFS','fr','Effectifs'),
('__LRI_EFFECTIFS','en','Headcount'),
('__LRI_ERR_SYS_CHAMPS_OBLIGATOIRES','fr','Des champs obligatoires n''ont pas été renseignés'),
('__LRI_ERR_SYS_CHAMPS_OBLIGATOIRES','en','Mandatory fields have not been completed'),
('__LRI_ERR_SYS_DEJA_EXISTANT','fr','Objet déjà existant'),
('__LRI_ERR_SYS_DEJA_EXISTANT','en','Object already exists'),
('__LRI_ERR_SYS_PAS_LES_DROITS','fr','Vous n''avez pas les droits pour cette action'),
('__LRI_ERR_SYS_PAS_LES_DROITS','en','You don''t have the rights for this action'),
('__LRI_EXTERNE','fr','Externe'),
('__LRI_EXTERNE','en','External'),
('__LRI_GESTION_LIBELLES_REFERENTIEL','en','Managing repository labels'),
('__LRI_GESTION_LIBELLES_REFERENTIEL','fr','Gestion des libellés du référentiel'),
('__LRI_INTERNE','en','Internal'),
('__LRI_INTERNE','fr','Interne'),
('__LRI_LIBELLE_REFERENTIEL_CREE','en','Label in the repository created'),
('__LRI_LIBELLE_REFERENTIEL_CREE','fr','Libellé dans le référentiel créé'),
('__LRI_LIBELLE_REFERENTIEL_MODIFIE','fr','Libellé dans le référentiel modifié'),
('__LRI_LIBELLE_REFERENTIEL_MODIFIE','en','The label in the repository modified'),
('__LRI_LIBELLE_REFERENTIEL_SUPPRIME','en','The label in the repository deleted'),
('__LRI_LIBELLE_REFERENTIEL_SUPPRIME','fr','Libellé dans le référentiel supprimé'),
('__LRI_LIBELLES_REFERENTIEL','fr','Libellés du référentiel'),
('__LRI_LIBELLES_REFERENTIEL','en','Repository labels'),
('__LRI_LISTE_INTERDEPENDANCES','en','List of interdependencies'),
('__LRI_LISTE_INTERDEPENDANCES','fr','Liste des interdépendances'),
('__LRI_LISTE_PERSONNES_PRIORITAIRES','fr','Liste des personnes prioritaires'),
('__LRI_LISTE_PERSONNES_PRIORITAIRES','en','List of priority persons'),
('__LRI_MODIFICATION_LIBELLES_REFERENTIEL','fr','Modification du libellé dans le référentiel'),
('__LRI_MODIFICATION_LIBELLES_REFERENTIEL','en','Modifying the label in the repository'),
('__LRI_ORGANISATION','fr','Organisation'),
('__LRI_ORGANISATION','en','Organization'),
('__LRI_PERSONNES_PRIORITAIRES','fr','Personnes prioritaires'),
('__LRI_PERSONNES_PRIORITAIRES','en','Priority people'),
('__LRI_PLANNING','en','Planning'),
('__LRI_PLANNING','fr','Planning'),
('__LRI_SITE_NOMINAL','en','Nominal site'),
('__LRI_SITE_NOMINAL','fr','Site nominal'),
('__LRI_SITE_SECOURS','en','Emergency site'),
('__LRI_SITE_SECOURS','fr','Site de secours'),
('__LRI_SUPPRESSION_LIBELLES_REFERENTIEL','fr','Suppression du libellé dans le référentiel'),
('__LRI_SUPPRESSION_LIBELLES_REFERENTIEL','en','Deleting the label in the repository'),
('__LRI_SYNTHESE','en','Summary'),
('__LRI_SYNTHESE','fr','Synthèse'),
('__LRI_SYS_AJOUTER','fr','Ajouter'),
('__LRI_SYS_AJOUTER','en','Add'),
('__LRI_SYS_CODE','en','Code'),
('__LRI_SYS_CODE','fr','Code'),
('__LRI_SYS_CREER','fr','Créer'),
('__LRI_SYS_CREER','en','Create'),
('__LRI_SYS_ERREUR','fr','Erreur'),
('__LRI_SYS_ERREUR','en','Error'),
('__LRI_SYS_FERMER','en','Close'),
('__LRI_SYS_FERMER','fr','Fermer'),
('__LRI_SYS_LANGUAGE','en','Language'),
('__LRI_SYS_LANGUAGE','fr','Language'),
('__LRI_SYS_LANGUE','fr','Langue'),
('__LRI_SYS_LANGUE','en','Language'),
('__LRI_SYS_LIBELLE','en','Label'),
('__LRI_SYS_LIBELLE','fr','Libellé'),
('__LRI_SYS_MODIFIER','fr','Modifier'),
('__LRI_SYS_MODIFIER','en','Modify'),
('__LRI_SYS_SUCCES','fr','Succès'),
('__LRI_SYS_SUCCES','en','Success'),
('__LRI_SYS_SUPPRIMER','en','Delete'),
('__LRI_SYS_SUPPRIMER','fr','Supprimer'),
('__LRI_TAUX_OCCUPATION','en','Occupancy rate'),
('__LRI_TAUX_OCCUPATION','fr','Taux d''occupation');