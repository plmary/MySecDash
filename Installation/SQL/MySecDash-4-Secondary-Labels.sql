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
('__LRI_SYS_AJOUTER', 'en', 'Add'),
('__LRI_SYS_AJOUTER', 'fr', 'Ajouter'),

('__LRI_SYS_CREER', 'en', 'Create'),
('__LRI_SYS_CREER', 'fr', 'Créer'),

('__LRI_SYS_MODIFIER', 'en', 'Modify'),
('__LRI_SYS_MODIFIER', 'fr', 'Modifier'),

('__LRI_SYS_SUPPRIMER', 'en', 'Delete'),
('__LRI_SYS_SUPPRIMER', 'fr', 'Supprimer'),

('__LRI_SYS_FERMER', 'en', 'Close'),
('__LRI_SYS_FERMER', 'fr', 'Fermer'),

('__LRI_SYS_CODE', 'en', 'Code'),
('__LRI_SYS_CODE', 'fr', 'Code'),

('__LRI_SYS_LIBELLE', 'en', 'Label'),
('__LRI_SYS_LIBELLE', 'fr', 'Libellé'),

('__LRI_SYS_SUCCES', 'en', 'Success'),
('__LRI_SYS_SUCCES', 'fr', 'Succès'),

('__LRI_SYS_ERREUR', 'en', 'Error'),
('__LRI_SYS_ERREUR', 'fr', 'Erreur'),

('__LRI_ERR_SYS_PAS_LES_DROITS', 'en', 'You don''t have the rights for this action'),
('__LRI_ERR_SYS_PAS_LES_DROITS', 'fr', 'Vous n''avez pas les droits pour cette action'),

('__LRI_ERR_SYS_DEJA_EXISTANT', 'en', 'Object already exists'),
('__LRI_ERR_SYS_DEJA_EXISTANT', 'fr', 'Objet déjà existant'),

('__LRI_ERR_SYS_CHAMPS_OBLIGATOIRES', 'en', 'Mandatory fields have not been completed'),
('__LRI_ERR_SYS_CHAMPS_OBLIGATOIRES', 'fr', 'Des champs obligatoires n''ont pas été renseignés'),


-- ************************************
-- Libellés liés aux écrans.
('__LRI_GESTION_LIBELLES_REFERENTIEL', 'en', 'Managing repository labels'),
('__LRI_GESTION_LIBELLES_REFERENTIEL', 'fr', 'Gestion des libellés du référentiel'),

('__LRI_CREATION_LIBELLES_REFERENTIEL', 'en', 'Creating a label in the repository'),
('__LRI_CREATION_LIBELLES_REFERENTIEL', 'fr', 'Création du libellé dans le référentiel'),

('__LRI_LIBELLE_REFERENTIEL_CREE', 'en', 'Label in the repository created'),
('__LRI_LIBELLE_REFERENTIEL_CREE', 'fr', 'Libellé dans le référentiel créé'),

('__LRI_MODIFICATION_LIBELLES_REFERENTIEL', 'en', 'Modifying the label in the repository'),
('__LRI_MODIFICATION_LIBELLES_REFERENTIEL', 'fr', 'Modification du libellé dans le référentiel'),

('__LRI_LIBELLE_REFERENTIEL_MODIFIE', 'en', 'The label in the repository modified'),
('__LRI_LIBELLE_REFERENTIEL_MODIFIE', 'fr', 'Libellé dans le référentiel modifié'),

('__LRI_SUPPRESSION_LIBELLES_REFERENTIEL', 'en', 'Deleting the label in the repository'),
('__LRI_SUPPRESSION_LIBELLES_REFERENTIEL', 'fr', 'Suppression du libellé dans le référentiel'),

('__LRI_LIBELLE_REFERENTIEL_SUPPRIME', 'en', 'The label in the repository deleted'),
('__LRI_LIBELLE_REFERENTIEL_SUPPRIME', 'fr', 'Libellé dans le référentiel supprimé'),

('__LRI_ERR_LIBELLE_REFERENTIEL_DEJA_EXISTANT', 'en', 'Label already exists in repository'),
('__LRI_ERR_LIBELLE_REFERENTIEL_DEJA_EXISTANT', 'fr', 'Libellé déja existant dans le référentiel'),
;