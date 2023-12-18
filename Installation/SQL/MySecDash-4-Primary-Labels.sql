--
-- Auteur  : Pierre-Luc MARY
-- Date    : 2017-02-17
-- Package : Loxense
--
-- Commentaire :
-- Ce script insère les libellés de base du référentiel de "Loxense".
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

SET search_path = public, pg_catalog;


INSERT INTO lng_langages (lng_id, lng_libelle) VALUES
('fr', 'Français'),
('en', 'Anglais');



INSERT INTO lbr_libelles_referentiel (lbr_code, lng_id, lbr_libelle) VALUES
('APR_TYPE_1', 'en', 'Information'),
('APR_TYPE_1', 'fr', 'Information'),
('APR_TYPE_2', 'en', 'Function'),
('APR_TYPE_2', 'fr', 'Fonction');

INSERT INTO lbr_libelles_referentiel (lbr_code, lng_id, lbr_libelle) VALUES
('ATP_CHG_MDP', 'fr', 'Changer de mot de passe'),
('ATP_CHG_MDP', 'en', 'Changing password'),
('ATP_CNX', 'fr', 'Connexion'),
('ATP_CNX', 'en', 'Login'),
('ATP_DCNX', 'fr', 'Déconnexion'),
('ATP_DCNX', 'en', 'Logout'),
('ATP_LECTURE', 'fr', 'Lecture'),
('ATP_LECTURE', 'en', 'Reading'),
('ATP_ECRITURE', 'fr', 'Ecriture'),
('ATP_ECRITURE', 'en', 'Writing'),
('ATP_MODIFICATION', 'fr', 'Modification'),
('ATP_MODIFICATION', 'en', 'Updating'),
('ATP_SUPPRESSION', 'fr', 'Suppression'),
('ATP_SUPPRESSION', 'en', 'Deleting'),
('ATP_DUPLICATION', 'fr', 'Duplication'),
('ATP_DUPLICATION', 'en', 'Duplicating'),
('ATP_GENERATION', 'fr', 'Génération'),
('ATP_GENERATION', 'en', 'Generate');

INSERT INTO lbr_libelles_referentiel (lbr_code, lng_id, lbr_libelle) VALUES
('LNG_fr', 'fr', 'Français'),
('LNG_en', 'fr', 'Anglais'),
('LNG_fr', 'en', 'French'),
('LNG_en', 'en', 'English');

INSERT INTO lbr_libelles_referentiel (lbr_code, lng_id, lbr_libelle) VALUES
('MCR_ETAT_0', 'fr', 'Non applicable'),
('MCR_ETAT_0', 'en', 'Not applicable'),
('MCR_ETAT_1', 'fr', 'Pas en place'),
('MCR_ETAT_1', 'en', 'Not in place'),
('MCR_ETAT_2', 'fr', 'Appliqué avec resctrictions'),
('MCR_ETAT_2', 'en', 'Applied with restrictions'),
('MCR_ETAT_3', 'fr', 'Appliqué sans resctriction'),
('MCR_ETAT_3', 'en', 'Applied without restriction');

INSERT INTO lbr_libelles_referentiel (lbr_code, lng_id, lbr_libelle) VALUES
('OTP_ACTIF_PRIMORDIAL', 'fr', 'Actif primordial'),
('OTP_ACTIF_PRIMORDIAL', 'en', 'Primary asset'),
('OTP_ACTIF_SUPPORT', 'fr', 'Actif support'),
('OTP_ACTIF_SUPPORT', 'en', 'Support asset'),
('OTP_GESTIONNAIRE', 'fr', 'Equipe de gestionnaires'),
('OTP_GESTIONNAIRE', 'en', 'Owner''s Team'),
('OTP_APPRECIATION_RISQUE', 'en', 'Risk assessment'),
('OTP_REFERENTIEL', 'fr', 'Référentiel'),
('OTP_REFERENTIEL', 'en', 'Referential'),
('OTP_THEME_REFERENTIEL', 'en', 'Referential theme'),
('OTP_OBJECTIF_REFERENTIEL', 'fr', 'Objectif du référentiel'),
('OTP_THEME_REFERENTIEL', 'fr', 'Thème du référentiel'),
('OTP_OBJECTIF_REFERENTIEL', 'en', 'Referential objective'),
('OTP_MESURE_REFERENTIEL', 'fr', 'Mesure du référentiel'),
('OTP_MESURE_REFERENTIEL', 'en', 'Referential control'),
('OTP_PARAMETRE', 'fr', 'Paramètre'),
('OTP_PARAMETRE', 'en', 'Parameter'),
('OTP_LIBELLE_INTERNE', 'fr', 'Libellé interne'),
('OTP_LIBELLE_INTERNE', 'en', 'Internal label'),
('OTP_REF_INTERNE', 'fr', 'Référentiel interne'),
('OTP_REF_INTERNE', 'en', 'Internal referential'),
('OTP_ENTITE', 'fr', 'Entité'),
('OTP_ENTITE', 'en', 'Entity'),
('OTP_CIVILITE', 'fr', 'Civilité'),
('OTP_CIVILITE', 'en', 'Civility'),
('OTP_IDENTITE', 'fr', 'Identité'),
('OTP_IDENTITE', 'en', 'Identity'),
('OTP_PROFIL', 'fr', 'Profil'),
('OTP_PROFIL', 'en', 'Profile'),
('OTP_CTRL_ACCES', 'fr', 'Contrôle d''accès'),
('OTP_CTRL_ACCES', 'en', 'Access control'),
('OTP_APPLICATION', 'fr', 'Application'),
('OTP_APPLICATION', 'en', 'Application'),
('OTP_TYPE_TRAITEMENT_RISQUE', 'fr', 'Type de traitement des risques'),
('OTP_TYPE_TRAITEMENT_RISQUE', 'en', 'Risk treatment type'),
('OTP_APPRECIATION_RISQUE', 'fr', 'Appréciation des risques'),
('OTP_TRAITEMENT_RISQUE', 'en', 'Risk treatment'),
('OTP_CARTOGRAPHIE_RISQUES', 'fr', 'Cartographie des risques'),
('OTP_CARTOGRAPHIE_RISQUES', 'en', 'Risks map'),
('OTP_CRITERE_VALORISATION', 'fr', 'Critère de valorisation des actifs'),
('OTP_CRITERE_VALORISATION', 'en', 'Asset rating criteria'),
('OTP_CRITERE_APPRECIATION', 'fr', 'Critère d''appréciation et d''acceptation des risques'),
('OTP_CRITERE_APPRECIATION', 'en', 'Risk assessment and risk acceptance criteria'),
('OTP_EVENEMENT_REDOUTE', 'fr', 'Evénement redouté'),
('OTP_EVENEMENT_REDOUTE', 'en', 'Feared event'),
('OTP_TRAITEMENT_RISQUE', 'fr', 'Traitement des risques'),
('OTP_DROIT', 'fr', 'Droit'),
('OTP_DROIT', 'en', 'Right'),
('OTP_RISQUE_GENERIQUE', 'fr', 'Risque Générique'),
('OTP_RISQUE_GENERIQUE', 'en', 'Generic risk'),
('OTP_MESURE_GENERIQUE', 'fr', 'Mesures de Loxense'),
('OTP_MESURE_GENERIQUE', 'en', 'Loxense controls'),
('OTP_IMPACT_GENERIQUE', 'fr', 'Impacts générique de Loxense'),
('OTP_IMPACT_GENERIQUE', 'en', 'Loxense generic impacts'),
('OTP_VULNERABILITE_GENERIQUE', 'fr', 'Vulnérabilité générique'),
('OTP_VULNERABILITE_GENERIQUE', 'en', 'Generic vulnerability'),
('OTP_TYPE_ACTIF_SUPPORT', 'fr', 'Type d''actif support'),
('OTP_TYPE_ACTIF_SUPPORT', 'en', 'Asset type support'),
('OTP_MENACE_GENERIQUE', 'fr', 'Menace générique'),
('OTP_MENACE_GENERIQUE', 'en', 'Generic threat'),
('OTP_SOURCE_MENACE', 'fr', 'Source de menace'),
('OTP_SOURCE_MENACE', 'en', 'Origin of threat'),
('OTP_TYPE_MENACE_GENERIQUE', 'fr', 'Type de menace générique'),
('OTP_TYPE_MENACE_GENERIQUE', 'en', 'Generic threat type'),
('OTP_ACTION', 'fr', 'Action'),
('OTP_ACTION', 'en', 'Action'),
('OTP_TAG', 'fr', 'Etiquette'),
('OTP_TAG', 'en', 'Tag'),
('OTP_MESURE_CONFORMITE', 'fr', 'Mesure de conformité'),
('OTP_MESURE_CONFORMITE', 'en', 'Conformity measure'),
('OTP_OBJECTIF_VISE', 'fr', 'Objectif visé'),
('OTP_OBJECTIF_VISE', 'en', 'Target objective'),
('OTP_SOURCE_RISQUE', 'fr', 'Source de risques'),
('OTP_SOURCE_RISQUE', 'en', 'Source of risks'),
('OTP_PARTIE_PRENANTE', 'fr', 'Partie prenante'),
('OTP_PARTIE_PRENANTE', 'en', 'Stakeholder');

INSERT INTO lbr_libelles_referentiel (lbr_code, lng_id, lbr_libelle) VALUES
('RCS_ETAT_0', 'fr', 'Non Couvert'),
('RCS_ETAT_0', 'en', 'Not Covered'),
('RCS_ETAT_1', 'fr', 'Partiellement couvert'),
('RCS_ETAT_1', 'en', 'Partially covered'),
('RCS_ETAT_2', 'fr', 'Totalement couvert'),
('RCS_ETAT_2', 'en', 'Completly covered');

INSERT INTO lbr_libelles_referentiel (lbr_code, lng_id, lbr_libelle) VALUES
('RGH_1', 'fr', 'Lecture'),
('RGH_1', 'en', 'Reading'),
('RGH_2', 'fr', 'Ecriture'),
('RGH_2', 'en', 'Writing'),
('RGH_3', 'fr', 'Modification'),
('RGH_3', 'en', 'Updating'),
('RGH_4', 'fr', 'Suppression'),
('RGH_4', 'en', 'Deleting');

INSERT INTO lbr_libelles_referentiel (lbr_code, lng_id, lbr_libelle) VALUES
('TAP_1', 'fr', 'Interactive'),
('TAP_1', 'en', 'Interactive'),
('TAP_2', 'fr', 'Batch'),
('TAP_2', 'en', 'Batch');

INSERT INTO lbr_libelles_referentiel (lbr_code, lng_id, lbr_libelle) VALUES
('RCS_TT_0', 'en', 'Risk modification'),
('RCS_TT_0', 'fr', 'Réduction du risque'),
('RCS_TT_1', 'fr', 'Transfert du risque'),
('RCS_TT_1', 'en', 'Risk sharing'),
('RCS_TT_2', 'fr', 'Refus du risque'),
('RCS_TT_2', 'en', 'Risk avoidance'),
('RCS_TT_3', 'fr', 'Maintien du risque'),
('RCS_TT_3', 'en', 'Risk retention');

INSERT INTO lbr_libelles_referentiel (lbr_code, lng_id, lbr_libelle) VALUES
('SCR_1', 'fr', 'Modifiable'),
('SCR_1', 'en', 'Editable'),
('SCR_2', 'fr', 'Non modifiable'),
('SCR_2', 'en', 'Unchangeable'),
('SCR_3', 'fr', 'Archivée'),
('SCR_3', 'en', 'Archived');

INSERT INTO lbr_libelles_referentiel (lbr_code, lng_id, lbr_libelle) VALUES
('ACT_STATUT_1', 'fr', 'En attente'),
('ACT_STATUT_1', 'en', 'Pending'),
('ACT_STATUT_2', 'fr', 'En cours'),
('ACT_STATUT_2', 'en', 'In progress'),
('ACT_STATUT_3', 'fr', 'Différée'),
('ACT_STATUT_3', 'en', 'Delayed'),
('ACT_STATUT_4', 'fr', 'Abandonnée'),
('ACT_STATUT_4', 'en', 'Abort'),
('ACT_STATUT_5', 'fr', 'Terminée'),
('ACT_STATUT_5', 'en', 'Done');

INSERT INTO lbr_libelles_referentiel (lbr_code, lng_id, lbr_libelle) VALUES
('ACT_FREQUENCE_1', 'fr', 'Aucune'),
('ACT_FREQUENCE_1', 'en', 'None'),
('ACT_FREQUENCE_2', 'fr', 'Quotidienne'),
('ACT_FREQUENCE_2', 'en', 'Dayly'),
('ACT_FREQUENCE_3', 'fr', 'Hebdomadaire'),
('ACT_FREQUENCE_3', 'en', 'Weekly'),
('ACT_FREQUENCE_4', 'fr', 'Mensuelle'),
('ACT_FREQUENCE_4', 'en', 'Monthly'),
('ACT_FREQUENCE_5', 'fr', 'Semestrielle'),
('ACT_FREQUENCE_5', 'en', 'Six-monthly'),
('ACT_FREQUENCE_6', 'fr', 'Annuelle'),
('ACT_FREQUENCE_6', 'en', 'Yearly');

INSERT INTO lbr_libelles_referentiel (lbr_code, lng_id, lbr_libelle) VALUES
('CRS_STATUT_1', 'fr', 'Gabarit'),
('CRS_STATUT_1', 'en', 'Template'),
('CRS_STATUT_2', 'fr', 'En cours'),
('CRS_STATUT_2', 'en', 'In progress'),
('CRS_STATUT_3', 'fr', 'A valider'),
('CRS_STATUT_3', 'en', 'To validate'),
('CRS_STATUT_4', 'fr', 'Validée'),
('CRS_STATUT_4', 'en', 'Validated'),
('CRS_STATUT_5', 'fr', 'Terminée'),
('CRS_STATUT_5', 'en', 'Terminated');
