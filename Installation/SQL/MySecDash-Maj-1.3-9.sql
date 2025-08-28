--
-- Auteur  : Pierre-Luc MARY
-- Date    : 2025-07-10
-- Package : MySecDash
--
-- Commentaire :
-- Ce script réalise les mises à jour pour passer à la version 1.3-9.
--


-- Changement du nom d'une colonne (on ne récupère plus l'identité de la personne connecté, mais l'identité de la partie prenante qui a validé)
ALTER TABLE IF EXISTS public.cmen_cmp_ent
    RENAME idn_id_validation TO ppr_id_validation;