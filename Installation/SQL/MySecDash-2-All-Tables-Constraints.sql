--
-- Auteur  : Pierre-Luc MARY
-- Date    : 2023-12-04
-- Package : MySecDash
--
-- Commentaire :
-- Ce script crée toutes les tables et toutes les contraintes de la base de données "mysecdash".
-- Modèle SQL : 1.0



CREATE SEQUENCE public.cvl_civilites_cvl_id_seq;

CREATE TABLE public.cvl_civilites (
                cvl_id BIGINT NOT NULL DEFAULT nextval('public.cvl_civilites_cvl_id_seq'),
                cvl_nom VARCHAR(35) NOT NULL,
                cvl_prenom VARCHAR(25) NOT NULL,
                CONSTRAINT cvl_civilites_pk PRIMARY KEY (cvl_id)
);


ALTER SEQUENCE public.cvl_civilites_cvl_id_seq OWNED BY public.cvl_civilites.cvl_id;

CREATE UNIQUE INDEX cvl_civilities_u1
 ON public.cvl_civilites
 ( cvl_nom, cvl_prenom );

CREATE SEQUENCE public.tpo_types_objet_tpo_id_seq;

CREATE TABLE public.tpo_types_objet (
                tpo_id BIGINT NOT NULL DEFAULT nextval('public.tpo_types_objet_tpo_id_seq'),
                tpo_code_libelle VARCHAR(45) NOT NULL,
                CONSTRAINT tpo_types_objet_pk PRIMARY KEY (tpo_id)
);


ALTER SEQUENCE public.tpo_types_objet_tpo_id_seq OWNED BY public.tpo_types_objet.tpo_id;

CREATE SEQUENCE public.tpa_types_action_tpa_id_seq;

CREATE TABLE public.tpa_types_action (
                tpa_id BIGINT NOT NULL DEFAULT nextval('public.tpa_types_action_tpa_id_seq'),
                tpa_code_libelle VARCHAR(45) NOT NULL,
                CONSTRAINT tpa_types_action_pk PRIMARY KEY (tpa_id)
);


ALTER SEQUENCE public.tpa_types_action_tpa_id_seq OWNED BY public.tpa_types_action.tpa_id;

CREATE SEQUENCE public.tap_types_application_tap_id_seq;

CREATE TABLE public.tap_types_application (
                tap_id BIGINT NOT NULL DEFAULT nextval('public.tap_types_application_tap_id_seq'),
                tap_code_libelle VARCHAR(45) NOT NULL,
                CONSTRAINT tap_types_application_pk PRIMARY KEY (tap_id)
);


ALTER SEQUENCE public.tap_types_application_tap_id_seq OWNED BY public.tap_types_application.tap_id;

CREATE UNIQUE INDEX tap_application_types_u1
 ON public.tap_types_application
 ( tap_code_libelle );

CREATE SEQUENCE public.ain_applications_internes_ain_id_seq;

CREATE TABLE public.ain_applications_internes (
                ain_id BIGINT NOT NULL DEFAULT nextval('public.ain_applications_internes_ain_id_seq'),
                tap_id BIGINT NOT NULL,
                ain_libelle VARCHAR(100) NOT NULL,
                ain_localisation VARCHAR(255) NOT NULL,
                ain_date_expiration INTEGER,
                ain_parametres VARCHAR(255),
                ain_maintenance BOOLEAN DEFAULT true NOT NULL,
                CONSTRAINT ain_applications_internes_pk PRIMARY KEY (ain_id)
);
COMMENT ON COLUMN public.ain_applications_internes.app_date_expiration IS 'une application.';
COMMENT ON COLUMN public.ain_applications_internes.app_maintenance IS 'Pour signaler si une application est en maintenance.';


ALTER SEQUENCE public.ain_applications_internes_ain_id_seq OWNED BY public.ain_applications_internes.ain_id;

CREATE UNIQUE INDEX app_applications_u1
 ON public.ain_applications_internes
 ( app_localisation );

CREATE SEQUENCE public.prf_profils_prf_id_seq;

CREATE TABLE public.prf_profils (
                prf_id BIGINT NOT NULL DEFAULT nextval('public.prf_profils_prf_id_seq'),
                prf_libelle VARCHAR(40) NOT NULL,
                CONSTRAINT prf_profils_pk PRIMARY KEY (prf_id)
);


ALTER SEQUENCE public.prf_profils_prf_id_seq OWNED BY public.prf_profils.prf_id;

CREATE SEQUENCE public.prs_parametres_systeme_prs_id_seq;

CREATE TABLE public.prs_parametres_systeme (
                prs_id BIGINT NOT NULL DEFAULT nextval('public.prs_parametres_systeme_prs_id_seq'),
                prs_groupe VARCHAR(30),
                prs_nom VARCHAR(30) NOT NULL,
                prs_type INTEGER DEFAULT 2 NOT NULL,
                prs_valeur VARCHAR(60) NOT NULL,
                prs_commentaire VARCHAR(100),
                prs_super_admin BOOLEAN DEFAULT false NOT NULL,
                CONSTRAINT prs_parametres_systeme_pk PRIMARY KEY (prs_id)
);
COMMENT ON COLUMN public.prs_parametres_systeme.prs_type IS '0 = Bouléen
1 = Numérique
2 = Alphanumérique';
COMMENT ON COLUMN public.prs_parametres_systeme.prs_super_admin IS 'Flag pour autoriser seulement le super admin';


ALTER SEQUENCE public.prs_parametres_systeme_prs_id_seq OWNED BY public.prs_parametres_systeme.prs_id;

CREATE UNIQUE INDEX spr_idx
 ON public.prs_parametres_systeme
 ( prs_nom );

CREATE SEQUENCE public.hac_historiques_activites_hac_id_seq;

CREATE TABLE public.hac_historiques_activites (
                hac_id BIGINT NOT NULL DEFAULT nextval('public.hac_historiques_activites_hac_id_seq'),
                tpa_id BIGINT NOT NULL,
                tpo_id BIGINT NOT NULL,
                crs_id BIGINT,
                hac_id_application_metier BIGINT,
                hac_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
                hac_utilisateur VARCHAR(65),
                hac_ip_utilisateur VARCHAR(40),
                hac_detail TEXT NOT NULL,
                CONSTRAINT hac_historiques_activites_pk PRIMARY KEY (hac_id)
);


ALTER SEQUENCE public.hac_historiques_activites_hac_id_seq OWNED BY public.hac_historiques_activites.hac_id;

CREATE INDEX hac_idx_1
 ON public.hac_historiques_activites
 ( hac_id_application_metier );

CREATE SEQUENCE public.drt_droits_drt_id_seq;

CREATE TABLE public.drt_droits (
                drt_id BIGINT NOT NULL DEFAULT nextval('public.drt_droits_drt_id_seq'),
                drt_code_libelle VARCHAR(45) NOT NULL,
                CONSTRAINT drt_droits_pk PRIMARY KEY (drt_id)
);
COMMENT ON TABLE public.drt_droits IS '1 - Lecture
2 - Ecriture
3 - Modification
4 - Suppression';


ALTER SEQUENCE public.drt_droits_drt_id_seq OWNED BY public.drt_droits.drt_id;

CREATE UNIQUE INDEX rgh_rights_u2
 ON public.drt_droits
 ( drt_code_libelle );

CREATE TABLE public.caa_controle_acces_application_interne (
                prf_id BIGINT NOT NULL,
                drt_id BIGINT NOT NULL,
                ain_id BIGINT NOT NULL,
                CONSTRAINT caa_controle_acces_application_interne_pk PRIMARY KEY (prf_id, drt_id, ain_id)
);


CREATE TABLE public.lng_langages (
                lng_id CHAR(2) NOT NULL,
                lng_libelle VARCHAR(50) NOT NULL,
                lng_langue_geree BOOLEAN DEFAULT false NOT NULL,
                CONSTRAINT lng_langages_pk PRIMARY KEY (lng_id)
);


CREATE SEQUENCE public.lbr_libelles_referentiel_lbr_id_seq;

CREATE TABLE public.lbr_libelles_referentiel (
                lbr_id BIGINT NOT NULL DEFAULT nextval('public.lbr_libelles_referentiel_lbr_id_seq'),
                lng_id CHAR(2) NOT NULL,
                lbr_nom VARCHAR(60) NOT NULL,
                lbr_texte TEXT NOT NULL,
                CONSTRAINT lbr_libelles_referentiel_pk PRIMARY KEY (lbr_id)
);


ALTER SEQUENCE public.lbr_libelles_referentiel_lbr_id_seq OWNED BY public.lbr_libelles_referentiel.lbr_id;

CREATE UNIQUE INDEX lbr_idx_nom_lng
 ON public.lbr_libelles_referentiel
 ( lng_id, lbr_nom );

CREATE SEQUENCE public.tad_types_action_defaut_tad_id_seq;

CREATE TABLE public.tad_types_action_defaut (
                tad_id BIGINT NOT NULL DEFAULT nextval('public.tad_types_action_defaut_tad_id_seq'),
                tad_nom_code VARCHAR(60) NOT NULL,
                CONSTRAINT tad_types_action_defaut_pk PRIMARY KEY (tad_id)
);


ALTER SEQUENCE public.tad_types_action_defaut_tad_id_seq OWNED BY public.tad_types_action_defaut.tad_id;

CREATE SEQUENCE public.adf_actions_defaut_adf_id_seq;

CREATE TABLE public.adf_actions_defaut (
                adf_id BIGINT NOT NULL DEFAULT nextval('public.adf_actions_defaut_adf_id_seq'),
                tad_id BIGINT NOT NULL,
                adf_debut_date_reelle DATE NOT NULL,
                adf_fin_date_reelle DATE NOT NULL,
                adf_debut_date_previsionnelle DATE NOT NULL,
                adf_fin_date_previsionnelle DATE NOT NULL,
                adf_nom VARCHAR(60) NOT NULL,
                adf_description TEXT NOT NULL,
                CONSTRAINT adf_actions_defaut_pk PRIMARY KEY (adf_id)
);


ALTER SEQUENCE public.adf_actions_defaut_adf_id_seq OWNED BY public.adf_actions_defaut.adf_id;

CREATE SEQUENCE public.tfr_types_fournisseur_tfr_id_seq;

CREATE TABLE public.tfr_types_fournisseur (
                tfr_id BIGINT NOT NULL DEFAULT nextval('public.tfr_types_fournisseur_tfr_id_seq'),
                tfr_nom_code VARCHAR(60) NOT NULL,
                CONSTRAINT tfr_types_fournisseur_pk PRIMARY KEY (tfr_id)
);


ALTER SEQUENCE public.tfr_types_fournisseur_tfr_id_seq OWNED BY public.tfr_types_fournisseur.tfr_id;

CREATE SEQUENCE public.sct_societes_sct_id_seq_1;

CREATE TABLE public.sct_societes (
                sct_id BIGINT NOT NULL DEFAULT nextval('public.sct_societes_sct_id_seq_1'),
                sct_name VARCHAR(100) NOT NULL,
                sct_description TEXT,
                CONSTRAINT sct_societes_pk PRIMARY KEY (sct_id)
);


ALTER SEQUENCE public.sct_societes_sct_id_seq_1 OWNED BY public.sct_societes.sct_id;

CREATE SEQUENCE public.cmp_campagnes_cmp_id_seq_1_1;

CREATE TABLE public.cmp_campagnes (
                cmp_id BIGINT NOT NULL DEFAULT nextval('public.cmp_campagnes_cmp_id_seq_1_1'),
                sct_id BIGINT NOT NULL,
                cmp_date DATE NOT NULL,
                CONSTRAINT cmp_campagnes_pk PRIMARY KEY (cmp_id)
);


ALTER SEQUENCE public.cmp_campagnes_cmp_id_seq_1_1 OWNED BY public.cmp_campagnes.cmp_id;

CREATE SEQUENCE public.pac_plan_actions_act_id_seq;

CREATE TABLE public.pac_plan_actions (
                act_id BIGINT NOT NULL DEFAULT nextval('public.pac_plan_actions_act_id_seq'),
                cmp_id BIGINT NOT NULL,
                pac_action_defaut BOOLEAN DEFAULT false NOT NULL,
                pac_debut_date_previsionnelle DATE NOT NULL,
                pac_fin_date_previsionnelle DATE NOT NULL,
                pac_debut_date_reelle DATE NOT NULL,
                pac_fin_date_reelle DATE NOT NULL,
                pac_nom VARCHAR(60) NOT NULL,
                pac_description TEXT NOT NULL,
                CONSTRAINT pac_plan_actions_pk PRIMARY KEY (act_id)
);


ALTER SEQUENCE public.pac_plan_actions_act_id_seq OWNED BY public.pac_plan_actions.act_id;

CREATE SEQUENCE public.ete_echelle_temps_ete_id_seq_1_1;

CREATE TABLE public.ete_echelle_temps (
                ete_id BIGINT NOT NULL DEFAULT nextval('public.ete_echelle_temps_ete_id_seq_1_1'),
                cmp_id BIGINT NOT NULL,
                ete_poids SMALLINT NOT NULL,
                ete_nom_code VARCHAR(60) NOT NULL,
                CONSTRAINT ete_echelle_temps_pk PRIMARY KEY (ete_id)
);
COMMENT ON COLUMN public.ete_echelle_temps.ete_poids IS 'Plus le poids est grand et plus le temps de reprise est long';


ALTER SEQUENCE public.ete_echelle_temps_ete_id_seq_1_1 OWNED BY public.ete_echelle_temps.ete_id;

CREATE SEQUENCE public.rpp_roles_parties_prenantes_rpp_id_seq_1;

CREATE TABLE public.rpp_roles_parties_prenantes (
                rpp_id BIGINT NOT NULL DEFAULT nextval('public.rpp_roles_parties_prenantes_rpp_id_seq_1'),
                rpp_nom_code VARCHAR(60) NOT NULL,
                CONSTRAINT rpp_roles_parties_prenantes_pk PRIMARY KEY (rpp_id)
);


ALTER SEQUENCE public.rpp_roles_parties_prenantes_rpp_id_seq_1 OWNED BY public.rpp_roles_parties_prenantes.rpp_id;

CREATE SEQUENCE public.ppr_parties_prenantes_ppr_id_seq;

CREATE TABLE public.ppr_parties_prenantes (
                ppr_id BIGINT NOT NULL DEFAULT nextval('public.ppr_parties_prenantes_ppr_id_seq'),
                sct_id BIGINT NOT NULL,
                rpp_id BIGINT NOT NULL,
                ppr_nom VARCHAR(35) NOT NULL,
                ppr_prenom VARCHAR(25) NOT NULL,
                ppr_trigramme CHAR(3) NOT NULL,
                ppr_interne BOOLEAN DEFAULT true NOT NULL,
                ppr_description TEXT,
                CONSTRAINT ppr_parties_prenantes_pk PRIMARY KEY (ppr_id)
);


ALTER SEQUENCE public.ppr_parties_prenantes_ppr_id_seq OWNED BY public.ppr_parties_prenantes.ppr_id;

CREATE SEQUENCE public.tim_types_impact_itp_id_seq;

CREATE TABLE public.tim_types_impact (
                itp_id BIGINT NOT NULL DEFAULT nextval('public.tim_types_impact_itp_id_seq'),
                tim_nom_code VARCHAR(60) NOT NULL,
                CONSTRAINT tim_types_impact_pk PRIMARY KEY (itp_id)
);


ALTER SEQUENCE public.tim_types_impact_itp_id_seq OWNED BY public.tim_types_impact.itp_id;

CREATE TABLE public.ticm_tim_cmg (
                itp_id BIGINT NOT NULL,
                cmp_id BIGINT NOT NULL,
                CONSTRAINT ticm_tim_cmg_pk PRIMARY KEY (itp_id, cmp_id)
);


CREATE SEQUENCE public.nap_niveaux_appreciation_nap_id_seq;

CREATE TABLE public.nap_niveaux_appreciation (
                nap_id BIGINT NOT NULL DEFAULT nextval('public.nap_niveaux_appreciation_nap_id_seq'),
                nap_numero_appreciation SMALLINT NOT NULL,
                nap_poids_appreciation SMALLINT NOT NULL,
                nap_nom_code VARCHAR(60) NOT NULL,
                CONSTRAINT nap_niveaux_appreciation_pk PRIMARY KEY (nap_id)
);


ALTER SEQUENCE public.nap_niveaux_appreciation_nap_id_seq OWNED BY public.nap_niveaux_appreciation.nap_id;

CREATE SEQUENCE public.mim_matrice_impacts_mim_id_seq;

CREATE TABLE public.mim_matrice_impacts (
                mim_id BIGINT NOT NULL DEFAULT nextval('public.mim_matrice_impacts_mim_id_seq'),
                nap_id BIGINT NOT NULL,
                itp_id BIGINT NOT NULL,
                mim_description TEXT NOT NULL,
                CONSTRAINT mim_matrice_impacts_pk PRIMARY KEY (mim_id)
);


ALTER SEQUENCE public.mim_matrice_impacts_mim_id_seq OWNED BY public.mim_matrice_impacts.mim_id;

CREATE TABLE public.micm_mim_cmp (
                mim_id BIGINT NOT NULL,
                cmp_id BIGINT NOT NULL,
                CONSTRAINT micm_mim_cmp_pk PRIMARY KEY (mim_id, cmp_id)
);


CREATE TABLE public.alcm_alv_cmg (
                alv_id BIGINT NOT NULL,
                cmp_id BIGINT NOT NULL,
                CONSTRAINT alcm_alv_cmg_pk PRIMARY KEY (alv_id, cmp_id)
);


CREATE SEQUENCE public.frn_fournisseurs_frn_id_seq;

CREATE TABLE public.frn_fournisseurs (
                frn_id BIGINT NOT NULL DEFAULT nextval('public.frn_fournisseurs_frn_id_seq'),
                cmp_id BIGINT NOT NULL,
                frn_nom VARCHAR(100) NOT NULL,
                frn_description TEXT NOT NULL,
                tfr_id BIGINT NOT NULL,
                CONSTRAINT frn_fournisseurs_pk PRIMARY KEY (frn_id)
);


ALTER SEQUENCE public.frn_fournisseurs_frn_id_seq OWNED BY public.frn_fournisseurs.frn_id;

CREATE SEQUENCE public.app_applications_app_id_seq;

CREATE TABLE public.app_applications (
                app_id BIGINT NOT NULL DEFAULT nextval('public.app_applications_app_id_seq'),
                cmp_id BIGINT NOT NULL,
                app_nom VARCHAR(100) NOT NULL,
                app_hebergement VARCHAR(100),
                app_niveau_service VARCHAR(100),
                app_description TEXT,
                CONSTRAINT app_applications_pk PRIMARY KEY (app_id)
);


ALTER SEQUENCE public.app_applications_app_id_seq OWNED BY public.app_applications.app_id;

CREATE SEQUENCE public.sts_sites_sts_id_seq;

CREATE TABLE public.sts_sites (
                sts_id BIGINT NOT NULL DEFAULT nextval('public.sts_sites_sts_id_seq'),
                sts_nom VARCHAR(50) NOT NULL,
                sts_description TEXT NOT NULL,
                CONSTRAINT sts_sites_pk PRIMARY KEY (sts_id)
);


ALTER SEQUENCE public.sts_sites_sts_id_seq OWNED BY public.sts_sites.sts_id;

CREATE SEQUENCE public.ent_entites_ent_id_seq;

CREATE TABLE public.ent_entites (
                ent_id BIGINT NOT NULL DEFAULT nextval('public.ent_entites_ent_id_seq'),
                ent_nom VARCHAR(100) NOT NULL,
                ent_description TEXT,
                CONSTRAINT ent_entites_pk PRIMARY KEY (ent_id)
);
COMMENT ON TABLE public.ent_entites IS 'Une Entité est une représentation du découpage d''une Entreprise. Elle peut être une Direction, un Département, une Equipe, etc.';


ALTER SEQUENCE public.ent_entites_ent_id_seq OWNED BY public.ent_entites.ent_id;

CREATE SEQUENCE public.eff_effectifs_eff_id_seq;

CREATE TABLE public.eff_effectifs (
                eff_id BIGINT NOT NULL DEFAULT nextval('public.eff_effectifs_eff_id_seq'),
                ent_id BIGINT NOT NULL,
                eff_nominal INTEGER NOT NULL,
                eff_distance INTEGER NOT NULL,
                CONSTRAINT eff_effectifs_pk PRIMARY KEY (eff_id)
);


ALTER SEQUENCE public.eff_effectifs_eff_id_seq OWNED BY public.eff_effectifs.eff_id;

CREATE SEQUENCE public.pcl_personnes_cles_pcl_id_seq;

CREATE TABLE public.pcl_personnes_cles (
                pcl_id BIGINT NOT NULL DEFAULT nextval('public.pcl_personnes_cles_pcl_id_seq'),
                ent_id BIGINT NOT NULL,
                pcl_nom VARCHAR(100) NOT NULL,
                pcl_description_fonction_cle VARCHAR(150) NOT NULL,
                CONSTRAINT pcl_personnes_cles_pk PRIMARY KEY (pcl_id)
);


ALTER SEQUENCE public.pcl_personnes_cles_pcl_id_seq OWNED BY public.pcl_personnes_cles.pcl_id;

CREATE TABLE public.cmen_cmp_ent (
                cmp_id BIGINT NOT NULL,
                ent_id BIGINT NOT NULL,
                CONSTRAINT cmen_cmp_ent_pk PRIMARY KEY (cmp_id, ent_id)
);


CREATE SEQUENCE public.idn_identites_idn_id_seq;

CREATE TABLE public.idn_identites (
                idn_id BIGINT NOT NULL DEFAULT nextval('public.idn_identites_idn_id_seq'),
                ent_id BIGINT NOT NULL,
                cvl_id BIGINT NOT NULL,
                idn_login VARCHAR(20) NOT NULL,
                idn_courriel VARCHAR(100),
                idn_authentifiant VARCHAR(64) NOT NULL,
                idn_grain_sel VARCHAR(32) NOT NULL,
                idn_changer_authentifiant BOOLEAN DEFAULT true NOT NULL,
                idn_tentative SMALLINT NOT NULL,
                idn_desactiver BOOLEAN DEFAULT false NOT NULL,
                idn_super_admin BOOLEAN DEFAULT false NOT NULL,
                idn_auditeur BOOLEAN DEFAULT false NOT NULL,
                idn_derniere_connexion TIMESTAMP,
                idn_date_expiration TIMESTAMP NOT NULL,
                idn_date_modification_authentifiant TIMESTAMP NOT NULL,
                CONSTRAINT idn_identites_pk PRIMARY KEY (idn_id)
);


ALTER SEQUENCE public.idn_identites_idn_id_seq OWNED BY public.idn_identites.idn_id;

CREATE UNIQUE INDEX idn_identities_u1
 ON public.idn_identites
 ( idn_login );

CREATE TABLE public.idsc_idn_sct (
                idn_id BIGINT NOT NULL,
                sct_id BIGINT NOT NULL,
                CONSTRAINT idsc_idn_sct_pk PRIMARY KEY (idn_id, sct_id)
);


CREATE TABLE public.idpr_idn_prf (
                idn_id BIGINT NOT NULL,
                prf_id BIGINT NOT NULL,
                CONSTRAINT idpr_idn_prf_pk PRIMARY KEY (idn_id, prf_id)
);


CREATE TABLE public.hsa_historique_authentifiant (
                idn_id BIGINT NOT NULL,
                hsa_date TIMESTAMP NOT NULL,
                hsa_authentifiant VARCHAR(64) NOT NULL,
                hsa_grain_sel VARCHAR(32) NOT NULL,
                CONSTRAINT hsa_historique_authentifiant_pk PRIMARY KEY (idn_id, hsa_date)
);


CREATE TABLE public.iden_idn_ent (
                idn_id BIGINT NOT NULL,
                ent_id BIGINT NOT NULL,
                iden_admin BOOLEAN NOT NULL,
                CONSTRAINT iden_idn_ent_pk PRIMARY KEY (idn_id, ent_id)
);


CREATE SEQUENCE public.act_activites_act_id_seq;

CREATE TABLE public.act_activites (
                act_id BIGINT NOT NULL DEFAULT nextval('public.act_activites_act_id_seq'),
                ent_id BIGINT NOT NULL,
                idn_id BIGINT NOT NULL,
                act_name VARCHAR(100) NOT NULL,
                act_validation_date DATE NOT NULL,
                act_description TEXT,
                CONSTRAINT act_activites_pk PRIMARY KEY (act_id)
);


ALTER SEQUENCE public.act_activites_act_id_seq OWNED BY public.act_activites.act_id;

CREATE TABLE public.acst_act_sts (
                act_id BIGINT NOT NULL,
                sts_id BIGINT NOT NULL,
                acst_effectif_nominal INTEGER NOT NULL,
                acst_effectif_teletravail INTEGER NOT NULL,
                CONSTRAINT acst_act_sts_pk PRIMARY KEY (act_id, sts_id)
);


CREATE TABLE public.ppac_ppr_act (
                ppr_id BIGINT NOT NULL,
                act_id BIGINT NOT NULL,
                CONSTRAINT ppac_ppr_act_pk PRIMARY KEY (ppr_id, act_id)
);


CREATE SEQUENCE public.tch_taches_tch_id_seq;

CREATE TABLE public.tch_taches (
                tch_id BIGINT NOT NULL DEFAULT nextval('public.tch_taches_tch_id_seq'),
                act_id BIGINT NOT NULL,
                tch_nom VARCHAR(150) NOT NULL,
                tch_description TEXT,
                CONSTRAINT tch_taches_pk PRIMARY KEY (tch_id)
);


ALTER SEQUENCE public.tch_taches_tch_id_seq OWNED BY public.tch_taches.tch_id;

CREATE TABLE public.tcfr_tch_frn (
                frn_id BIGINT NOT NULL,
                tch_id BIGINT NOT NULL,
                CONSTRAINT tcfr_tch_frn_pk PRIMARY KEY (frn_id, tch_id)
);


CREATE TABLE public.tcst_tch_sts (
                sts_id BIGINT NOT NULL,
                tch_id BIGINT NOT NULL,
                ete_id BIGINT NOT NULL,
                tcst_site_backup BOOLEAN DEFAULT false NOT NULL,
                tcst_strategie_montee_charge TEXT NOT NULL,
                CONSTRAINT tcst_tch_sts_pk PRIMARY KEY (sts_id, tch_id)
);


CREATE TABLE public.mitc_mim_tch (
                mim_id BIGINT NOT NULL,
                tch_id BIGINT NOT NULL,
                ete_id BIGINT NOT NULL,
                mitc_justification TEXT NOT NULL,
                asts_teleworkable_task BOOLEAN NOT NULL,
                CONSTRAINT mitc_mim_tch_pk PRIMARY KEY (mim_id, tch_id)
);


CREATE TABLE public.ppta_ppr_tch (
                ppr_id BIGINT NOT NULL,
                tch_id BIGINT NOT NULL,
                CONSTRAINT ppta_ppr_tch_pk PRIMARY KEY (ppr_id, tch_id)
);


CREATE TABLE public.tcap_tch_app (
                tac_id BIGINT NOT NULL,
                app_id BIGINT NOT NULL,
                ete_id_dima BIGINT NOT NULL,
                ete_id_pdma BIGINT NOT NULL,
                tcap_palliatif TEXT NOT NULL,
                CONSTRAINT tcap_tac_app_pk PRIMARY KEY (tac_id, app_id)
);
COMMENT ON COLUMN public.tcap_tch_app.ete_id_dima IS 'DIMA = Durée d''Interruption Maximale Admise';
COMMENT ON COLUMN public.tcap_tch_app.ete_id_pdma IS 'PDMA = Perte de Données Maximale Admise';


CREATE SEQUENCE public.din_dependances_internes_din_id_seq;

CREATE TABLE public.din_dependances_internes (
                din_id BIGINT NOT NULL DEFAULT nextval('public.din_dependances_internes_din_id_seq'),
                tac_id BIGINT NOT NULL,
                din_sens SMALLINT NOT NULL,
                din_description TEXT NOT NULL,
                din_commentaire TEXT NOT NULL,
                CONSTRAINT din_dependances_internes_pk PRIMARY KEY (din_id)
);
COMMENT ON COLUMN public.din_dependances_internes.din_sens IS '0 - Amont
1 - Aval';


ALTER SEQUENCE public.din_dependances_internes_din_id_seq OWNED BY public.din_dependances_internes.din_id;

CREATE SEQUENCE public.hcm_historiques_campagne_hcm_id_seq;

CREATE TABLE public.hcm_historiques_campagne (
                hcm_id BIGINT NOT NULL DEFAULT nextval('public.hcm_historiques_campagne_hcm_id_seq'),
                cmp_id BIGINT NOT NULL,
                hcm_version VARCHAR(10) NOT NULL,
                hcm_date DATE NOT NULL,
                hcm_auteur VARCHAR(60) NOT NULL,
                hcm_description TEXT NOT NULL,
                CONSTRAINT hcm_historiques_campagne_pk PRIMARY KEY (hcm_id)
);


ALTER SEQUENCE public.hcm_historiques_campagne_hcm_id_seq OWNED BY public.hcm_historiques_campagne.hcm_id;

ALTER TABLE public.idn_identites ADD CONSTRAINT cvl_idn_fk
FOREIGN KEY (cvl_id)
REFERENCES public.cvl_civilites (cvl_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.hac_historiques_activites ADD CONSTRAINT tpo_hac_fk
FOREIGN KEY (tpo_id)
REFERENCES public.tpo_types_objet (tpo_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.hac_historiques_activites ADD CONSTRAINT tpa_hac_fk
FOREIGN KEY (tpa_id)
REFERENCES public.tpa_types_action (tpa_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.ain_applications_internes ADD CONSTRAINT tap_ain_fk
FOREIGN KEY (tap_id)
REFERENCES public.tap_types_application (tap_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.caa_controle_acces_application_interne ADD CONSTRAINT ain_caa_fk
FOREIGN KEY (ain_id)
REFERENCES public.ain_applications_internes (ain_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.caa_controle_acces_application_interne ADD CONSTRAINT prf_cta_fk
FOREIGN KEY (prf_id)
REFERENCES public.prf_profils (prf_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.idpr_idn_prf ADD CONSTRAINT prf_idpr_fk
FOREIGN KEY (prf_id)
REFERENCES public.prf_profils (prf_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.caa_controle_acces_application_interne ADD CONSTRAINT drt_cta_fk
FOREIGN KEY (drt_id)
REFERENCES public.drt_droits (drt_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.lbr_libelles_referentiel ADD CONSTRAINT lng_lbr_fk
FOREIGN KEY (lng_id)
REFERENCES public.lng_langages (lng_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.adf_actions_defaut ADD CONSTRAINT tad_adf_fk
FOREIGN KEY (tad_id)
REFERENCES public.tad_types_action_defaut (tad_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.frn_fournisseurs ADD CONSTRAINT tfr_frn_fk
FOREIGN KEY (tfr_id)
REFERENCES public.tfr_types_fournisseur (tfr_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.cmp_campagnes ADD CONSTRAINT sct_cmp_fk
FOREIGN KEY (sct_id)
REFERENCES public.sct_societes (sct_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.idsc_idn_sct ADD CONSTRAINT sct_idsc_fk
FOREIGN KEY (sct_id)
REFERENCES public.sct_societes (sct_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.ppr_parties_prenantes ADD CONSTRAINT sct_societes_ppr_parties_prenantes_fk
FOREIGN KEY (sct_id)
REFERENCES public.sct_societes (sct_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.cmen_cmp_ent ADD CONSTRAINT cmp_cmen_fk
FOREIGN KEY (cmp_id)
REFERENCES public.cmp_campagnes (cmp_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.hcm_historiques_campagne ADD CONSTRAINT cmp_hcm_fk
FOREIGN KEY (cmp_id)
REFERENCES public.cmp_campagnes (cmp_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.ete_echelle_temps ADD CONSTRAINT cmp_ete_fk
FOREIGN KEY (cmp_id)
REFERENCES public.cmp_campagnes (cmp_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.frn_fournisseurs ADD CONSTRAINT cmp_frn_fk
FOREIGN KEY (cmp_id)
REFERENCES public.cmp_campagnes (cmp_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.micm_mim_cmp ADD CONSTRAINT cmp_micm_fk
FOREIGN KEY (cmp_id)
REFERENCES public.cmp_campagnes (cmp_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.ticm_tim_cmg ADD CONSTRAINT cmp_ticm_fk
FOREIGN KEY (cmp_id)
REFERENCES public.cmp_campagnes (cmp_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.alcm_alv_cmg ADD CONSTRAINT cmp_alcm_fk
FOREIGN KEY (cmp_id)
REFERENCES public.cmp_campagnes (cmp_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.pac_plan_actions ADD CONSTRAINT cmp_pac_fk
FOREIGN KEY (cmp_id)
REFERENCES public.cmp_campagnes (cmp_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.app_applications ADD CONSTRAINT cmp_app_fk
FOREIGN KEY (cmp_id)
REFERENCES public.cmp_campagnes (cmp_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.tcap_tch_app ADD CONSTRAINT ete_tcap_fk
FOREIGN KEY (ete_id_dima)
REFERENCES public.ete_echelle_temps (ete_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.tcap_tch_app ADD CONSTRAINT ete_tcap_fk1
FOREIGN KEY (ete_id_pdma)
REFERENCES public.ete_echelle_temps (ete_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.tcst_tch_sts ADD CONSTRAINT ete_echelle_temps_tcst_tch_sts_fk
FOREIGN KEY (ete_id)
REFERENCES public.ete_echelle_temps (ete_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.mitc_mim_tch ADD CONSTRAINT ete_mitc_fk
FOREIGN KEY (ete_id)
REFERENCES public.ete_echelle_temps (ete_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.ppr_parties_prenantes ADD CONSTRAINT rpp_ppr_fk
FOREIGN KEY (rpp_id)
REFERENCES public.rpp_roles_parties_prenantes (rpp_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.ppta_ppr_tch ADD CONSTRAINT ppr_ppta_fk
FOREIGN KEY (ppr_id)
REFERENCES public.ppr_parties_prenantes (ppr_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.ppac_ppr_act ADD CONSTRAINT ppr_ppac_fk
FOREIGN KEY (ppr_id)
REFERENCES public.ppr_parties_prenantes (ppr_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.ticm_tim_cmg ADD CONSTRAINT tim_ticm_fk
FOREIGN KEY (itp_id)
REFERENCES public.tim_types_impact (itp_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.mim_matrice_impacts ADD CONSTRAINT tim_mim_fk
FOREIGN KEY (itp_id)
REFERENCES public.tim_types_impact (itp_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.alcm_alv_cmg ADD CONSTRAINT nap_alcm_fk
FOREIGN KEY (alv_id)
REFERENCES public.nap_niveaux_appreciation (nap_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.mim_matrice_impacts ADD CONSTRAINT nap_mim_fk
FOREIGN KEY (nap_id)
REFERENCES public.nap_niveaux_appreciation (nap_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.mitc_mim_tch ADD CONSTRAINT mim_mitc_fk
FOREIGN KEY (mim_id)
REFERENCES public.mim_matrice_impacts (mim_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.micm_mim_cmp ADD CONSTRAINT mim_micm_fk
FOREIGN KEY (mim_id)
REFERENCES public.mim_matrice_impacts (mim_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.tcfr_tch_frn ADD CONSTRAINT frn_tcfr_fk
FOREIGN KEY (frn_id)
REFERENCES public.frn_fournisseurs (frn_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.tcap_tch_app ADD CONSTRAINT app_tcap_fk
FOREIGN KEY (app_id)
REFERENCES public.app_applications (app_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.tcst_tch_sts ADD CONSTRAINT sts_tcst_fk
FOREIGN KEY (sts_id)
REFERENCES public.sts_sites (sts_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.acst_act_sts ADD CONSTRAINT sts_acst_fk
FOREIGN KEY (sts_id)
REFERENCES public.sts_sites (sts_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.act_activites ADD CONSTRAINT ent_act_fk
FOREIGN KEY (ent_id)
REFERENCES public.ent_entites (ent_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.iden_idn_ent ADD CONSTRAINT ent_iden_fk
FOREIGN KEY (ent_id)
REFERENCES public.ent_entites (ent_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.idn_identites ADD CONSTRAINT ent_idn_fk
FOREIGN KEY (ent_id)
REFERENCES public.ent_entites (ent_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.cmen_cmp_ent ADD CONSTRAINT ent_cmen_fk
FOREIGN KEY (ent_id)
REFERENCES public.ent_entites (ent_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.pcl_personnes_cles ADD CONSTRAINT ent_entites_pcl_personnes_cles_fk
FOREIGN KEY (ent_id)
REFERENCES public.ent_entites (ent_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.eff_effectifs ADD CONSTRAINT ent_entites_eff_effectifs_fk
FOREIGN KEY (ent_id)
REFERENCES public.ent_entites (ent_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.iden_idn_ent ADD CONSTRAINT idn_iden_fk
FOREIGN KEY (idn_id)
REFERENCES public.idn_identites (idn_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.hsa_historique_authentifiant ADD CONSTRAINT idn_hsa_fk
FOREIGN KEY (idn_id)
REFERENCES public.idn_identites (idn_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.idpr_idn_prf ADD CONSTRAINT idn_idpr_fk
FOREIGN KEY (idn_id)
REFERENCES public.idn_identites (idn_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.act_activites ADD CONSTRAINT idn_act_fk
FOREIGN KEY (idn_id)
REFERENCES public.idn_identites (idn_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.idsc_idn_sct ADD CONSTRAINT idn_idsc_fk
FOREIGN KEY (idn_id)
REFERENCES public.idn_identites (idn_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.tch_taches ADD CONSTRAINT act_tch_fk
FOREIGN KEY (act_id)
REFERENCES public.act_activites (act_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.ppac_ppr_act ADD CONSTRAINT act_ppac_fk
FOREIGN KEY (act_id)
REFERENCES public.act_activites (act_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.acst_act_sts ADD CONSTRAINT act_acst_fk
FOREIGN KEY (act_id)
REFERENCES public.act_activites (act_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.din_dependances_internes ADD CONSTRAINT tch_din_fk
FOREIGN KEY (tac_id)
REFERENCES public.tch_taches (tch_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.tcap_tch_app ADD CONSTRAINT tch_tcap_fk
FOREIGN KEY (tac_id)
REFERENCES public.tch_taches (tch_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.ppta_ppr_tch ADD CONSTRAINT tch_ppta_fk
FOREIGN KEY (tch_id)
REFERENCES public.tch_taches (tch_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.mitc_mim_tch ADD CONSTRAINT tch_mitc_fk
FOREIGN KEY (tch_id)
REFERENCES public.tch_taches (tch_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.tcst_tch_sts ADD CONSTRAINT tch_tcst_fk
FOREIGN KEY (tch_id)
REFERENCES public.tch_taches (tch_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.tcfr_tch_frn ADD CONSTRAINT tch_tcfr_fk
FOREIGN KEY (tch_id)
REFERENCES public.tch_taches (tch_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;