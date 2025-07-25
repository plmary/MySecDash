--
-- Auteur  : Pierre-Luc MARY
-- Date    : 2025-07-10
-- Package : MySecDash
--
-- Commentaire :
-- Ce script réalise les mises à jour du modèle 1.3-2.
--


-- Ajout d'une description à la table des Profils
ALTER TABLE IF EXISTS public.prf_profils
    ADD COLUMN prf_description text;


CREATE UNIQUE INDEX prf_profils_u1
 ON public.prf_profils
 ( prf_libelle );

INSERT INTO lbr_libelles_referentiel (lng_id, lbr_code, lbr_libelle) VALUES
('en', '__LRI_FORMATS_OUVERTS', 'Open formats'),
('fr', '__LRI_FORMATS_OUVERTS', 'Formats ouverts'),
('fr', '__LRI_FORMATS_PROPRIETAIRES', 'Formats propriétaires'),
('en', '__LRI_FORMATS_PROPRIETAIRES', 'Proprietary formats'),
('en', '__LRI_SUPPRIME_TOUS_LIBELLES_CODE', 'All labels associated with this "Code" will be deleted'),
('fr', '__LRI_SUPPRIME_TOUS_LIBELLES_CODE', 'Tous les libellés associés à ce "Code" vont être supprimés');
