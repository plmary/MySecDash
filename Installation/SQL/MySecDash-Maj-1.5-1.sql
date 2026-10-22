--
-- Date : 02/12/2025
-- Auteur : Pierre-Luc MARY
--

--
-- Création des index pour optimiser les lectures
--

CREATE UNIQUE INDEX idx_u_tgs_code
 ON public.tgs_tags
 ( tgs_code );

CREATE UNIQUE INDEX idx_u_tgs_libelle
 ON public.tgs_tags
 ( tgs_libelle );
 
CREATE UNIQUE INDEX idx_u_cvl_civilities
 ON public.cvl_civilites
 ( cvl_nom, cvl_prenom );

CREATE UNIQUE INDEX idx_u_prf_libelle
 ON public.prf_profils
 ( prf_libelle );

 CREATE UNIQUE INDEX idx_u_tap_code_libelle
 ON public.tap_types_application
 ( tap_code_libelle );

CREATE UNIQUE INDEX idx_u_app_applications
 ON public.ain_applications_internes
 ( ain_localisation );

CREATE INDEX idx_ain_tap_id
 ON public.ain_applications_internes
 ( tap_id );

CREATE INDEX idx_ain_maintenance
 ON public.ain_applications_internes
 ( ain_maintenance );

CREATE UNIQUE INDEX idx_u_tfr_types_fournisseur
 ON public.tfr_types_fournisseur
 ( tfr_nom_code );

CREATE INDEX idx_hac_id_application_metier
 ON public.hac_historiques_activites
 ( hac_id_application_metier );

CREATE INDEX idx_hac_tpa_id
 ON public.hac_historiques_activites
 ( tpa_id );

CREATE INDEX idx_hac_tpo_id
 ON public.hac_historiques_activites
 ( tpo_id );

CREATE INDEX idx_hac_crs_id
 ON public.hac_historiques_activites
 ( crs_id );

CREATE INDEX idx_hac_date
 ON public.hac_historiques_activites
 ( hac_date DESC );

CREATE INDEX idx_hac_composite
 ON public.hac_historiques_activites
 ( tpa_id, tpo_id, hac_date DESC );

CREATE UNIQUE INDEX idx_u_frn_nom
 ON public.frn_fournisseurs
 ( frn_nom );

CREATE INDEX idx_frn_tfr_id
 ON public.frn_fournisseurs
 ( tfr_id );

CREATE INDEX idx_frtg_frn_id
 ON public.frtg_frn_tgs
 ( frn_id );

CREATE INDEX idx_adf_tad_id
 ON public.adf_actions_defaut
 ( tad_id );

CREATE UNIQUE INDEX idx_u_rpp_nom_code
 ON public.rpp_roles_parties_prenantes
 ( rpp_nom_code );

CREATE UNIQUE INDEX idx_u_prs_nom
 ON public.prs_parametres_systeme
 ( prs_nom );

CREATE UNIQUE INDEX idx_u_lbr_composite
 ON public.lbr_libelles_referentiel
 ( lng_id, lbr_code );

CREATE INDEX idx_lbr_lng_id
 ON public.lbr_libelles_referentiel
 ( lng_id );

CREATE UNIQUE INDEX idx_u_drt_code_libelle
 ON public.drt_droits
 ( drt_code_libelle );

CREATE INDEX idx_caa_ain_id
 ON public.caa_controle_acces_application_interne
 ( ain_id );

CREATE INDEX idx_caa_drt_id
 ON public.caa_controle_acces_application_interne
 ( drt_id );

CREATE UNIQUE INDEX idx_u_sct_nom
 ON public.sct_societes
 ( sct_nom );

CREATE INDEX idx_ete_nom_code
 ON public.ete_echelle_temps
 ( ete_nom_code );

CREATE UNIQUE INDEX ete_u_composite
 ON public.ete_echelle_temps
 ( ete_poids, sct_id );

CREATE INDEX idx_ete_sct_id
 ON public.ete_echelle_temps
 ( sct_id );

CREATE INDEX idx_sctg_sct_id
 ON public.sctg_sct_tgs
 ( sct_id );

CREATE UNIQUE INDEX idx_u_idn_login
 ON public.idn_identites
 ( idn_login );

CREATE INDEX idx_idn_sct_id
 ON public.idn_identites
 ( sct_id );

CREATE INDEX idx_idn_cvl_id
 ON public.idn_identites
 ( cvl_id );

CREATE INDEX idx_idn_derniere_connexion
 ON public.idn_identites
 ( idn_derniere_connexion );

CREATE INDEX idx_idn_date_expiration
 ON public.idn_identites
 ( idn_date_expiration );

CREATE INDEX idx_idn_desactiver
 ON public.idn_identites
 ( idn_desactiver );

CREATE INDEX idx_idn_super_admin
 ON public.idn_identites
 ( idn_super_admin );

CREATE INDEX idx_idpr_prf_id
 ON public.idpr_idn_prf
 ( prf_id );

CREATE INDEX idx_idtg_idn_id
 ON public.idtg_idn_tgs
 ( idn_id );

CREATE INDEX idx_idsc_sct_id
 ON public.idsc_idn_sct
 ( sct_id );

CREATE UNIQUE INDEX idx_u_ent_composite
 ON public.ent_entites
 ( ent_nom, sct_id );

CREATE INDEX idx_ent_sct_id
 ON public.ent_entites
 ( sct_id );

CREATE INDEX idx_entg_tgs_id
 ON public.entg_ent_tgs
 ( ent_id );

CREATE INDEX idx_iden_ent_id
 ON public.iden_idn_ent
 ( ent_id );

CREATE UNIQUE INDEX idx_u_sts_composite
 ON public.sts_sites
 ( sct_id, sts_nom );

CREATE INDEX idx_sts_sct_id
 ON public.sts_sites
 ( sct_id );

CREATE INDEX idx_sttg_sts_id
 ON public.sttg_sts_tgs
 ( sts_id );

CREATE UNIQUE INDEX idx_u_ppr_composite
 ON public.ppr_parties_prenantes
 ( sct_id, ppr_nom, ppr_prenom );

CREATE INDEX idx_ppr_sct_id
 ON public.ppr_parties_prenantes
 ( sct_id );

CREATE INDEX idx_pptg_tgs_id
 ON public.pptg_ppr_tgs
 ( ppr_id );

CREATE UNIQUE INDEX idx_u_cmp_composite
 ON public.cmp_campagnes
 ( sct_id, cmp_date );

CREATE INDEX idx_cmp_sct_id
 ON public.cmp_campagnes
 ( sct_id );

CREATE INDEX idx_cmp_idn_id
 ON public.cmp_campagnes
 ( idn_id );

CREATE INDEX idx_cmp_date
 ON public.cmp_campagnes
 ( cmp_date );

CREATE INDEX idx_cmp_flag_validation
 ON public.cmp_campagnes
 ( cmp_flag_validation );

CREATE UNIQUE INDEX idx_u_act_composite
 ON public.act_activites
 ( act_nom, ent_id, cmp_id );

CREATE INDEX idx_act_ent_id
 ON public.act_activites
 ( ent_id );

CREATE INDEX idx_act_cmp_id
 ON public.act_activites
 ( cmp_id );

CREATE INDEX idx_act_ppr_responsable
 ON public.act_activites
 ( ppr_id_responsable );

CREATE INDEX idx_act_ppr_suppleant
 ON public.act_activites
 ( ppr_id_suppleant );

CREATE INDEX idx_rut_act_id
 ON public.rut_redemarrage_utilisateurs
 ( act_id );

CREATE INDEX idx_acfr_frn_id
 ON public.acfr_act_frn
 ( frn_id );

CREATE INDEX idx_acfr_ete_id
 ON public.acfr_act_frn
 ( ete_id );

CREATE INDEX idx_actg_act_id
 ON public.actg_act_tgs
 ( act_id );

CREATE INDEX idx_cmst_sts_id
 ON public.cmst_cmp_sts
 ( sts_id );

CREATE INDEX idx_acst_composite
 ON public.acst_act_sts
 ( cmp_id, sts_id );

CREATE UNIQUE INDEX idx_u_nim_nom_code
 ON public.nim_niveaux_impact
 ( nim_nom_code, cmp_id );

CREATE UNIQUE INDEX idx_u_nim_numero
 ON public.nim_niveaux_impact
 ( cmp_id, nim_numero );

CREATE UNIQUE INDEX idx_u_nim_poids
 ON public.nim_niveaux_impact
 ( cmp_id, nim_poids );

CREATE INDEX idx_nim_cmp_id
 ON public.nim_niveaux_impact
 ( cmp_id );

CREATE UNIQUE INDEX idx_u_tim_nom_code
 ON public.tim_types_impact
 ( tim_nom_code, cmp_id );

CREATE UNIQUE INDEX idx_u_tim_poids
 ON public.tim_types_impact
 ( cmp_id, tim_poids );

CREATE INDEX idx_tim_cmp_id
 ON public.tim_types_impact
 ( cmp_id );

CREATE UNIQUE INDEX idx_u_mim_composite
 ON public.mim_matrice_impacts
 ( nim_id, tim_id, cmp_id );

CREATE INDEX idx_mim_cmp_id
 ON public.mim_matrice_impacts
 ( cmp_id );

CREATE INDEX idx_mim_nim_id
 ON public.mim_matrice_impacts
 ( nim_id );

CREATE INDEX idx_mim_tim_id
 ON public.mim_matrice_impacts
 ( tim_id );

CREATE INDEX idx_ppac_act_id
 ON public.ppac_ppr_act
 ( act_id );

CREATE INDEX idx_ppac_cmp_id
 ON public.ppac_ppr_act
 ( cmp_id );

CREATE UNIQUE INDEX idx_u_dma_composite
 ON public.dma_dmia_activite
 ( act_id, ete_id, mim_id, cmp_id );

CREATE INDEX idx_dma_act_id
 ON public.dma_dmia_activite
 ( act_id );

CREATE INDEX idx_dma_ete_id
 ON public.dma_dmia_activite
 ( ete_id );

CREATE INDEX idx_dma_mim_id
 ON public.dma_dmia_activite
 ( mim_id );

CREATE INDEX idx_dma_cmp_id
 ON public.dma_dmia_activite
 ( cmp_id );

CREATE INDEX idx_cmen_cmp_id
 ON public.cmen_cmp_ent
 ( cmp_id );

CREATE INDEX idx_cmen_ppr_cpca
 ON public.cmen_cmp_ent
 ( ppr_id_cpca );

CREATE INDEX idx_cmen_ppr_validation
 ON public.cmen_cmp_ent
 ( ppr_id_validation );

CREATE UNIQUE INDEX idx_pac_composite
 ON public.pac_plan_actions
 ( cmp_id, pac_nom );

CREATE INDEX idx_pac_cmp_id
 ON public.pac_plan_actions
 ( cmp_id );

CREATE UNIQUE INDEX idx_u_app_nom
 ON public.app_applications
 ( app_nom );

CREATE INDEX idx_app_frn_id
 ON public.app_applications
 ( frn_id );

CREATE INDEX idx_app_sct_id
 ON public.app_applications
 ( sct_id );

CREATE INDEX idx_scap_sct_id
 ON public.scap_sct_app
 ( sct_id );

CREATE INDEX idx_scap_ete_dima
 ON public.scap_sct_app
 ( ete_id_dima_dsi );

CREATE INDEX idx_acap_app_id
 ON public.acap_act_app
 ( app_id );

CREATE INDEX idx_acap_ete_dima
 ON public.acap_act_app
 ( ete_id_dima );

CREATE INDEX idx_acap_ete_pdma
 ON public.acap_act_app
 ( ete_id_pdma );

CREATE INDEX idx_acap_composite
 ON public.acap_act_app
 ( act_id, app_id );

CREATE INDEX idx_aptg_app_id
 ON public.aptg_app_tgs
 ( app_id );


--
-- Suppression des anciens index
--

DROP INDEX IF EXISTS act_u_nom_idx CASCADE;

DROP INDEX IF EXISTS app_applications_u1 CASCADE;

DROP INDEX IF EXISTS app_u_nom_idx CASCADE;

DROP INDEX IF EXISTS cmp_u_nom_idx CASCADE;

DROP INDEX IF EXISTS cvl_civilities_u1 CASCADE;

DROP INDEX IF EXISTS dma_u_idx CASCADE;

DROP INDEX IF EXISTS rgh_rights_u2 CASCADE;

DROP INDEX IF EXISTS ent_u_nom_idx CASCADE;

DROP INDEX IF EXISTS frn_u_nom_idx CASCADE;

DROP INDEX IF EXISTS hac_idx_1 CASCADE;

DROP INDEX IF EXISTS idn_identities_u1 CASCADE;

DROP INDEX IF EXISTS lbr_idx_nom_lng CASCADE;

DROP INDEX IF EXISTS mim_u1_idx CASCADE;

DROP INDEX IF EXISTS nim_u_nom_idx CASCADE;

DROP INDEX IF EXISTS nim_u_numero_idx CASCADE;

DROP INDEX IF EXISTS nim_u_poids_idx CASCADE;

DROP INDEX IF EXISTS pac_u_nom_idx CASCADE;

DROP INDEX IF EXISTS ppr_u_nom_idx CASCADE;

DROP INDEX IF EXISTS prf_profils_u1 CASCADE;

DROP INDEX IF EXISTS spr_idx CASCADE;

DROP INDEX IF EXISTS rpp_u_nom_idx CASCADE;

DROP INDEX IF EXISTS sct_u_nom_idx CASCADE;

DROP INDEX IF EXISTS sts_u_nom_idx CASCADE;

DROP INDEX IF EXISTS tap_application_types_u1 CASCADE;

DROP INDEX IF EXISTS tfr_u_nom_idx CASCADE;

DROP INDEX IF EXISTS tgs_code_u CASCADE;

DROP INDEX IF EXISTS tgs_libelle_u CASCADE;

DROP INDEX IF EXISTS tim_u_nom_idx CASCADE;

DROP INDEX IF EXISTS tim_u_poids_idx CASCADE;

