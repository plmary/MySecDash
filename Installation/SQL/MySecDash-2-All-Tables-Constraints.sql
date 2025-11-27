--
-- Auteur  : Pierre-Luc MARY
-- Date    : 2025-10-25
-- Package : MySecDash
--
-- Commentaire :
-- Ce script crée toutes les tables et toutes les contraintes de la base de données "mysecdash".
-- Modèle SQL : MySecDash v1.5


CREATE SEQUENCE public.tgs_tags_tgs_id_seq;

CREATE TABLE public.tgs_tags (
                tgs_id BIGINT NOT NULL DEFAULT nextval('public.tgs_tags_tgs_id_seq'),
                tgs_code VARCHAR(10) NOT NULL,
                tgs_libelle VARCHAR(60) NOT NULL,
                tgs_description TEXT NOT NULL,
                CONSTRAINT tgs_tags_pk PRIMARY KEY (tgs_id)
);


ALTER SEQUENCE public.tgs_tags_tgs_id_seq OWNED BY public.tgs_tags.tgs_id;

CREATE UNIQUE INDEX tgs_code_u
 ON public.tgs_tags
 ( tgs_code );

CREATE UNIQUE INDEX tgs_libelle_u
 ON public.tgs_tags
 ( tgs_libelle );

CREATE SEQUENCE public.tpo_types_objet_tpo_id_seq;

CREATE TABLE public.tpo_types_objet (
                tpo_id BIGINT NOT NULL DEFAULT nextval('public.tpo_types_objet_tpo_id_seq'),
                tpo_code_libelle VARCHAR(45) NOT NULL,
                CONSTRAINT tpo_types_objet_pk PRIMARY KEY (tpo_id)
);


ALTER SEQUENCE public.tpo_types_objet_tpo_id_seq OWNED BY public.tpo_types_objet.tpo_id;

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

CREATE SEQUENCE public.prf_profils_prf_id_seq;

CREATE TABLE public.prf_profils (
                prf_id BIGINT NOT NULL DEFAULT nextval('public.prf_profils_prf_id_seq'),
                prf_libelle VARCHAR(40) NOT NULL,
                prf_description TEXT,
                CONSTRAINT prf_profils_pk PRIMARY KEY (prf_id)
);


ALTER SEQUENCE public.prf_profils_prf_id_seq OWNED BY public.prf_profils.prf_id;

CREATE UNIQUE INDEX prf_profils_u1
 ON public.prf_profils
 ( prf_libelle );

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

CREATE UNIQUE INDEX tap_types_application_u1
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
COMMENT ON COLUMN public.ain_applications_internes.ain_date_expiration IS 'une application.';
COMMENT ON COLUMN public.ain_applications_internes.ain_maintenance IS 'Pour signaler si une application est en maintenance.';


ALTER SEQUENCE public.ain_applications_internes_ain_id_seq OWNED BY public.ain_applications_internes.ain_id;

CREATE UNIQUE INDEX app_applications_u1
 ON public.ain_applications_internes
 ( ain_localisation );

CREATE SEQUENCE public.tfr_types_fournisseur_tfr_id_seq;

CREATE TABLE public.tfr_types_fournisseur (
                tfr_id BIGINT NOT NULL DEFAULT nextval('public.tfr_types_fournisseur_tfr_id_seq'),
                tfr_nom_code VARCHAR(60) NOT NULL,
                CONSTRAINT tfr_types_fournisseur_pk PRIMARY KEY (tfr_id)
);


ALTER SEQUENCE public.tfr_types_fournisseur_tfr_id_seq OWNED BY public.tfr_types_fournisseur.tfr_id;

CREATE UNIQUE INDEX tfr_u_nom_idx
 ON public.tfr_types_fournisseur
 ( tfr_nom_code );

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

CREATE SEQUENCE public.frn_fournisseurs_frn_id_seq;

CREATE TABLE public.frn_fournisseurs (
                frn_id BIGINT NOT NULL DEFAULT nextval('public.frn_fournisseurs_frn_id_seq'),
                tfr_id BIGINT,
                frn_nom VARCHAR(100) NOT NULL,
                frn_description TEXT NOT NULL,
                CONSTRAINT frn_fournisseurs_pk PRIMARY KEY (frn_id)
);


ALTER SEQUENCE public.frn_fournisseurs_frn_id_seq OWNED BY public.frn_fournisseurs.frn_id;

CREATE UNIQUE INDEX frn_u_nom_idx
 ON public.frn_fournisseurs
 ( frn_nom );

CREATE TABLE public.frtg_frn_tgs (
                tgs_id BIGINT NOT NULL,
                frn_id BIGINT NOT NULL,
                CONSTRAINT frtg_frn_tgs_pk PRIMARY KEY (tgs_id, frn_id)
);


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

CREATE SEQUENCE public.rpp_roles_parties_prenantes_rpp_id_seq;

CREATE TABLE public.rpp_roles_parties_prenantes (
                rpp_id BIGINT NOT NULL DEFAULT nextval('public.rpp_roles_parties_prenantes_rpp_id_seq'),
                rpp_nom_code VARCHAR(60) NOT NULL,
                CONSTRAINT rpp_roles_parties_prenantes_pk PRIMARY KEY (rpp_id)
);


ALTER SEQUENCE public.rpp_roles_parties_prenantes_rpp_id_seq OWNED BY public.rpp_roles_parties_prenantes.rpp_id;

CREATE UNIQUE INDEX rpp_u_nom_idx
 ON public.rpp_roles_parties_prenantes
 ( rpp_nom_code );

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
                lbr_code VARCHAR(60) NOT NULL,
                lbr_libelle TEXT NOT NULL,
                CONSTRAINT lbr_libelles_referentiel_pk PRIMARY KEY (lbr_id)
);


ALTER SEQUENCE public.lbr_libelles_referentiel_lbr_id_seq OWNED BY public.lbr_libelles_referentiel.lbr_id;

CREATE UNIQUE INDEX lbr_idx_nom_lng
 ON public.lbr_libelles_referentiel
 ( lng_id, lbr_code );

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


CREATE SEQUENCE public.sct_societes_sct_id_seq;

CREATE TABLE public.sct_societes (
                sct_id BIGINT NOT NULL DEFAULT nextval('public.sct_societes_sct_id_seq'),
                sct_nom VARCHAR(100) NOT NULL,
                sct_description TEXT,
                CONSTRAINT sct_societes_pk PRIMARY KEY (sct_id)
);


ALTER SEQUENCE public.sct_societes_sct_id_seq OWNED BY public.sct_societes.sct_id;

CREATE UNIQUE INDEX sct_u_nom_idx
 ON public.sct_societes
 ( sct_nom );

CREATE SEQUENCE public.ete_echelle_temps_ete_id_seq;

CREATE TABLE public.ete_echelle_temps (
                ete_id BIGINT NOT NULL DEFAULT nextval('public.ete_echelle_temps_ete_id_seq'),
                sct_id BIGINT NOT NULL,
                ete_poids SMALLINT NOT NULL,
                ete_nom_code VARCHAR(60) NOT NULL,
                CONSTRAINT ete_echelle_temps_pk PRIMARY KEY (ete_id)
);
COMMENT ON COLUMN public.ete_echelle_temps.ete_poids IS 'Plus le poids est grand et plus le temps de reprise est long';


ALTER SEQUENCE public.ete_echelle_temps_ete_id_seq OWNED BY public.ete_echelle_temps.ete_id;

CREATE INDEX ete_u_nom_idx
 ON public.ete_echelle_temps
 ( ete_nom_code );

CREATE UNIQUE INDEX ete_u_poids_idx
 ON public.ete_echelle_temps
 ( ete_poids );

CREATE TABLE public.sctg_sct_tgs (
                tgs_id BIGINT NOT NULL,
                sct_id BIGINT NOT NULL,
                CONSTRAINT sctg_sct_tgs_pk PRIMARY KEY (tgs_id, sct_id)
);


CREATE SEQUENCE public.idn_identites_idn_id_seq;

CREATE TABLE public.idn_identites (
                idn_id BIGINT NOT NULL DEFAULT nextval('public.idn_identites_idn_id_seq'),
                sct_id BIGINT NOT NULL,
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

CREATE TABLE public.hsa_historique_authentifiant (
                idn_id BIGINT NOT NULL,
                hsa_date TIMESTAMP NOT NULL,
                hsa_authentifiant VARCHAR(64) NOT NULL,
                hsa_grain_sel VARCHAR(32) NOT NULL,
                CONSTRAINT hsa_historique_authentifiant_pk PRIMARY KEY (idn_id, hsa_date)
);


CREATE TABLE public.idpr_idn_prf (
                idn_id BIGINT NOT NULL,
                prf_id BIGINT NOT NULL,
                CONSTRAINT idpr_idn_prf_pk PRIMARY KEY (idn_id, prf_id)
);


CREATE TABLE public.idtg_idn_tgs (
                tgs_id BIGINT NOT NULL,
                idn_id BIGINT NOT NULL,
                CONSTRAINT idtg_idn_tgs_pk PRIMARY KEY (tgs_id, idn_id)
);


CREATE TABLE public.idsc_idn_sct (
                idn_id BIGINT NOT NULL,
                sct_id BIGINT NOT NULL,
                idsc_admin BOOLEAN DEFAULT FALSE NOT NULL,
                CONSTRAINT idsc_idn_sct_pk PRIMARY KEY (idn_id, sct_id)
);
COMMENT ON COLUMN public.idsc_idn_sct.idsc_admin IS 'Flag pour autoriser l''utilisateur à administrer cette Société ultérieurement';


CREATE SEQUENCE public.ent_entites_ent_id_seq;

CREATE TABLE public.ent_entites (
                ent_id BIGINT NOT NULL DEFAULT nextval('public.ent_entites_ent_id_seq'),
                sct_id BIGINT NOT NULL,
                ent_nom VARCHAR(100) NOT NULL,
                ent_description TEXT,
                CONSTRAINT ent_entites_pk PRIMARY KEY (ent_id)
);
COMMENT ON TABLE public.ent_entites IS 'Une Entité est une représentation du découpage d''une Entreprise. Elle peut être une Direction, un Département, une Equipe, etc.';


ALTER SEQUENCE public.ent_entites_ent_id_seq OWNED BY public.ent_entites.ent_id;

CREATE UNIQUE INDEX ent_u_nom_idx
 ON public.ent_entites
 ( ent_nom, sct_id );

CREATE TABLE public.entg_ent_tgs (
                ent_id BIGINT NOT NULL,
                tgs_id BIGINT NOT NULL,
                CONSTRAINT entg_ent_tgs_pk PRIMARY KEY (ent_id, tgs_id)
);


CREATE TABLE public.iden_idn_ent (
                idn_id BIGINT NOT NULL,
                ent_id BIGINT NOT NULL,
                iden_admin BOOLEAN DEFAULT FALSE NOT NULL,
                CONSTRAINT iden_idn_ent_pk PRIMARY KEY (idn_id, ent_id)
);
COMMENT ON COLUMN public.iden_idn_ent.iden_admin IS 'Flag pour autoriser l''utilisateur à administrer cette Entité ultérieurement';


CREATE SEQUENCE public.sts_sites_sts_id_seq;

CREATE TABLE public.sts_sites (
                sts_id BIGINT NOT NULL DEFAULT nextval('public.sts_sites_sts_id_seq'),
                sct_id BIGINT NOT NULL,
                sts_nom VARCHAR(50) NOT NULL,
                sts_description TEXT NOT NULL,
                CONSTRAINT sts_sites_pk PRIMARY KEY (sts_id)
);


ALTER SEQUENCE public.sts_sites_sts_id_seq OWNED BY public.sts_sites.sts_id;

CREATE UNIQUE INDEX sts_u_nom_idx
 ON public.sts_sites
 ( sct_id, sts_nom );

CREATE TABLE public.sttg_sts_tgs (
                tgs_id BIGINT NOT NULL,
                sts_id BIGINT NOT NULL,
                CONSTRAINT sttg_sts_tgs_pk PRIMARY KEY (tgs_id, sts_id)
);


CREATE SEQUENCE public.ppr_parties_prenantes_ppr_id_seq;

CREATE TABLE public.ppr_parties_prenantes (
                ppr_id BIGINT NOT NULL DEFAULT nextval('public.ppr_parties_prenantes_ppr_id_seq'),
                sct_id BIGINT NOT NULL,
                ppr_nom VARCHAR(35) NOT NULL,
                ppr_prenom VARCHAR(25) NOT NULL,
                ppr_interne BOOLEAN DEFAULT true NOT NULL,
                ppr_description TEXT,
                CONSTRAINT ppr_parties_prenantes_pk PRIMARY KEY (ppr_id)
);
COMMENT ON COLUMN public.ppr_parties_prenantes.ppr_interne IS 'true = interne
false = externe';


ALTER SEQUENCE public.ppr_parties_prenantes_ppr_id_seq OWNED BY public.ppr_parties_prenantes.ppr_id;

CREATE UNIQUE INDEX ppr_u_nom_idx
 ON public.ppr_parties_prenantes
 ( sct_id, ppr_nom, ppr_prenom );

CREATE TABLE public.pptg_ppr_tgs (
                ppr_id BIGINT NOT NULL,
                tgs_id BIGINT NOT NULL,
                CONSTRAINT pptg_ppr_tgs_pk PRIMARY KEY (ppr_id, tgs_id)
);


CREATE SEQUENCE public.cmp_campagnes_cmp_id_seq;

CREATE TABLE public.cmp_campagnes (
                cmp_id BIGINT NOT NULL DEFAULT nextval('public.cmp_campagnes_cmp_id_seq'),
                sct_id BIGINT NOT NULL,
                idn_id BIGINT,
                cmp_date DATE NOT NULL,
                cmp_flag_validation BOOLEAN DEFAULT FALSE NOT NULL,
                cmp_date_validation TIMESTAMP,
                cmp_niveau_impact_accepte SMALLINT DEFAULT 2 NOT NULL,
                CONSTRAINT cmp_campagnes_pk PRIMARY KEY (cmp_id)
);
COMMENT ON COLUMN public.cmp_campagnes.cmp_flag_validation IS 'FALSE = La Campagne n''est pas validé
TRUE = La Campagne est validé';


ALTER SEQUENCE public.cmp_campagnes_cmp_id_seq OWNED BY public.cmp_campagnes.cmp_id;

CREATE UNIQUE INDEX cmp_u_nom_idx
 ON public.cmp_campagnes
 ( sct_id, cmp_date );

CREATE SEQUENCE public.act_activites_act_id_seq;

CREATE TABLE public.act_activites (
                act_id BIGINT NOT NULL DEFAULT nextval('public.act_activites_act_id_seq'),
                ent_id BIGINT NOT NULL,
                cmp_id BIGINT NOT NULL,
                ppr_id_responsable BIGINT,
                ppr_id_suppleant BIGINT,
                act_nom VARCHAR(100) NOT NULL,
                act_teletravail BOOLEAN DEFAULT TRUE,
                act_effectifs_en_nominal INTEGER NOT NULL,
                act_taux_occupation SMALLINT NOT NULL,
                act_effectifs_a_distance INTEGER NOT NULL,
                act_justification_dmia TEXT,
                act_description TEXT,
                act_dependances_internes_amont TEXT,
                act_dependances_internes_aval TEXT,
                act_description_entraides TEXT,
                act_strategie_montee_en_charge TEXT,
                CONSTRAINT act_activites_pk PRIMARY KEY (act_id)
);
COMMENT ON COLUMN public.act_activites.act_teletravail IS 'FALSE = Activité pas télétravaillable
TRUE = Activité télétravaillable';


ALTER SEQUENCE public.act_activites_act_id_seq OWNED BY public.act_activites.act_id;

CREATE UNIQUE INDEX act_u_nom_idx
 ON public.act_activites
 ( act_nom, ent_id, cmp_id );

CREATE TABLE public.rut_redemarrage_utilisateurs (
                ete_id BIGINT NOT NULL,
                act_id BIGINT NOT NULL,
                rut_nbr_utilisateurs_a_redemarrer INTEGER NOT NULL,
                CONSTRAINT rut_redemarrage_utilisateurs_pk PRIMARY KEY (ete_id, act_id)
);


CREATE TABLE public.acfr_act_frn (
                act_id BIGINT NOT NULL,
                frn_id BIGINT NOT NULL,
                ete_id BIGINT,
                acfr_consequence_indisponibilite TEXT,
                acfr_palliatif_tiers TEXT,
                CONSTRAINT acfr_act_frn_pk PRIMARY KEY (act_id, frn_id)
);


CREATE TABLE public.actg_act_tgs (
                tgs_id BIGINT NOT NULL,
                act_id BIGINT NOT NULL,
                CONSTRAINT actg_act_tgs_pk PRIMARY KEY (tgs_id, act_id)
);


CREATE TABLE public.cmst_cmp_sts (
                cmp_id BIGINT NOT NULL,
                sts_id BIGINT NOT NULL,
                CONSTRAINT cmst_cmp_sts_pk PRIMARY KEY (cmp_id, sts_id)
);


CREATE TABLE public.acst_act_sts (
                act_id BIGINT NOT NULL,
                cmp_id BIGINT NOT NULL,
                sts_id BIGINT NOT NULL,
                acst_type_site SMALLINT DEFAULT 0 NOT NULL,
                CONSTRAINT acst_act_sts_pk PRIMARY KEY (act_id, cmp_id, sts_id)
);
COMMENT ON COLUMN public.acst_act_sts.acst_type_site IS '0 = Site Nominal pour l''Activité
1 = Site de Secours pour l''Activité';


CREATE SEQUENCE public.nim_niveaux_impact_nim_id_seq;

CREATE TABLE public.nim_niveaux_impact (
                nim_id BIGINT NOT NULL DEFAULT nextval('public.nim_niveaux_impact_nim_id_seq'),
                cmp_id BIGINT NOT NULL,
                nim_numero SMALLINT NOT NULL,
                nim_poids SMALLINT NOT NULL,
                nim_nom_code VARCHAR(60) NOT NULL,
                nim_couleur VARCHAR(6) NOT NULL,
                CONSTRAINT nim_niveaux_impact_pk PRIMARY KEY (nim_id)
);


ALTER SEQUENCE public.nim_niveaux_impact_nim_id_seq OWNED BY public.nim_niveaux_impact.nim_id;

CREATE UNIQUE INDEX nim_u_nom_idx
 ON public.nim_niveaux_impact
 ( nim_nom_code, cmp_id );

CREATE UNIQUE INDEX nim_u_numero_idx
 ON public.nim_niveaux_impact
 ( cmp_id, nim_numero );

CREATE UNIQUE INDEX nim_u_poids_idx
 ON public.nim_niveaux_impact
 ( cmp_id, nim_poids );

CREATE SEQUENCE public.tim_types_impact_tim_id_seq;

CREATE TABLE public.tim_types_impact (
                tim_id BIGINT NOT NULL DEFAULT nextval('public.tim_types_impact_tim_id_seq'),
                cmp_id BIGINT NOT NULL,
                tim_poids SMALLINT NOT NULL,
                tim_nom_code VARCHAR(60) NOT NULL,
                CONSTRAINT tim_types_impact_pk PRIMARY KEY (tim_id)
);


ALTER SEQUENCE public.tim_types_impact_tim_id_seq OWNED BY public.tim_types_impact.tim_id;

CREATE UNIQUE INDEX tim_u_nom_idx
 ON public.tim_types_impact
 ( tim_nom_code, cmp_id );

CREATE UNIQUE INDEX tim_u_poids_idx
 ON public.tim_types_impact
 ( cmp_id, tim_poids );

CREATE SEQUENCE public.mim_matrice_impacts_mim_id_seq;

CREATE TABLE public.mim_matrice_impacts (
                mim_id BIGINT NOT NULL DEFAULT nextval('public.mim_matrice_impacts_mim_id_seq'),
                cmp_id BIGINT NOT NULL,
                nim_id BIGINT NOT NULL,
                tim_id BIGINT NOT NULL,
                mim_description TEXT NOT NULL,
                CONSTRAINT mim_matrice_impacts_pk PRIMARY KEY (mim_id)
);


ALTER SEQUENCE public.mim_matrice_impacts_mim_id_seq OWNED BY public.mim_matrice_impacts.mim_id;

CREATE UNIQUE INDEX mim_u1_idx
 ON public.mim_matrice_impacts
 ( nim_id, tim_id, cmp_id );

CREATE TABLE public.ppac_ppr_act (
                ppr_id BIGINT NOT NULL,
                act_id BIGINT NOT NULL,
                cmp_id BIGINT NOT NULL,
                ppac_description TEXT,
                CONSTRAINT ppac_ppr_act_pk PRIMARY KEY (ppr_id, act_id, cmp_id)
);


CREATE SEQUENCE public.dma_dmia_activite_dma_id_seq;

CREATE TABLE public.dma_dmia_activite (
                dma_id BIGINT NOT NULL DEFAULT nextval('public.dma_dmia_activite_dma_id_seq'),
                act_id BIGINT NOT NULL,
                ete_id BIGINT NOT NULL,
                mim_id BIGINT NOT NULL,
                cmp_id BIGINT NOT NULL,
                CONSTRAINT dma_dmia_activite_pk PRIMARY KEY (dma_id)
);


ALTER SEQUENCE public.dma_dmia_activite_dma_id_seq OWNED BY public.dma_dmia_activite.dma_id;

CREATE TABLE public.cmen_cmp_ent (
                ent_id BIGINT NOT NULL,
                cmp_id BIGINT NOT NULL,
                ppr_id_cpca BIGINT,
                cmen_date_entretien_cpca DATE,
                ppr_id_validation BIGINT,
                cmen_date_validation DATE,
                cmen_effectif_total INTEGER,
                CONSTRAINT cmen_cmp_ent_pk PRIMARY KEY (ent_id, cmp_id)
);


CREATE SEQUENCE public.pac_plan_actions_pac_id_seq;

CREATE TABLE public.pac_plan_actions (
                pac_id BIGINT NOT NULL DEFAULT nextval('public.pac_plan_actions_pac_id_seq'),
                cmp_id BIGINT NOT NULL,
                pac_action_defaut BOOLEAN DEFAULT false NOT NULL,
                pac_debut_date_previsionnelle DATE NOT NULL,
                pac_fin_date_previsionnelle DATE NOT NULL,
                pac_debut_date_reelle DATE NOT NULL,
                pac_fin_date_reelle DATE NOT NULL,
                pac_nom VARCHAR(60) NOT NULL,
                pac_description TEXT NOT NULL,
                CONSTRAINT pac_plan_actions_pk PRIMARY KEY (pac_id)
);


ALTER SEQUENCE public.pac_plan_actions_pac_id_seq OWNED BY public.pac_plan_actions.pac_id;

CREATE UNIQUE INDEX pac_u_nom_idx
 ON public.pac_plan_actions
 ( cmp_id, pac_nom );

CREATE SEQUENCE public.app_applications_app_id_seq;

CREATE TABLE public.app_applications (
                app_id BIGINT NOT NULL DEFAULT nextval('public.app_applications_app_id_seq'),
                frn_id BIGINT,
                sct_id BIGINT,
                app_nom VARCHAR(100) NOT NULL,
                app_nom_alias VARCHAR(100),
                app_hebergement VARCHAR(100),
                app_niveau_service VARCHAR(100),
                app_description TEXT,
                CONSTRAINT app_applications_pk PRIMARY KEY (app_id)
);


ALTER SEQUENCE public.app_applications_app_id_seq OWNED BY public.app_applications.app_id;

CREATE UNIQUE INDEX app_u_nom_idx
 ON public.app_applications
 ( app_nom );

CREATE TABLE public.scap_sct_app (
                app_id BIGINT NOT NULL,
                sct_id BIGINT NOT NULL,
                ete_id_dima_dsi BIGINT,
                ete_id_pdma_dsi BIGINT,
                scap_description_dima TEXT,
                scap_description_pdma TEXT,
                CONSTRAINT scap_sct_app_pk PRIMARY KEY (app_id, sct_id)
);


CREATE TABLE public.acap_act_app (
                act_id BIGINT NOT NULL,
                app_id BIGINT NOT NULL,
                ete_id_dima BIGINT,
                ete_id_pdma BIGINT,
                acap_palliatif TEXT,
                acap_donnees TEXT,
                acap_hebergement TEXT,
                acap_niveau_service TEXT,
                CONSTRAINT acap_act_app_pk PRIMARY KEY (act_id, app_id)
);
COMMENT ON COLUMN public.acap_act_app.ete_id_dima IS 'DIMA = Durée d''Interruption Maximale Admise';
COMMENT ON COLUMN public.acap_act_app.ete_id_pdma IS 'PDMA = Perte de Données Maximale Admise';
COMMENT ON COLUMN public.acap_act_app.acap_donnees IS 'Nom données 1=ICT;Nom données 2=ICT...';


CREATE TABLE public.aptg_app_tgs (
                app_id BIGINT NOT NULL,
                tgs_id BIGINT NOT NULL,
                CONSTRAINT aptg_app_tgs_pk PRIMARY KEY (app_id, tgs_id)
);


ALTER TABLE public.idtg_idn_tgs ADD CONSTRAINT tgs_idtg_fk
FOREIGN KEY (tgs_id)
REFERENCES public.tgs_tags (tgs_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.pptg_ppr_tgs ADD CONSTRAINT tgs_pptg_fk
FOREIGN KEY (tgs_id)
REFERENCES public.tgs_tags (tgs_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.actg_act_tgs ADD CONSTRAINT tgs_actg_fk
FOREIGN KEY (tgs_id)
REFERENCES public.tgs_tags (tgs_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.entg_ent_tgs ADD CONSTRAINT tgs_entg_fk
FOREIGN KEY (tgs_id)
REFERENCES public.tgs_tags (tgs_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.sctg_sct_tgs ADD CONSTRAINT tgs_sctg_fk
FOREIGN KEY (tgs_id)
REFERENCES public.tgs_tags (tgs_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.sttg_sts_tgs ADD CONSTRAINT tgs_sttg_fk
FOREIGN KEY (tgs_id)
REFERENCES public.tgs_tags (tgs_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.frtg_frn_tgs ADD CONSTRAINT tgs_frtg_fk
FOREIGN KEY (tgs_id)
REFERENCES public.tgs_tags (tgs_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.aptg_app_tgs ADD CONSTRAINT tgs_aptg_fk
FOREIGN KEY (tgs_id)
REFERENCES public.tgs_tags (tgs_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.hac_historiques_activites ADD CONSTRAINT tpo_hac_fk
FOREIGN KEY (tpo_id)
REFERENCES public.tpo_types_objet (tpo_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.idn_identites ADD CONSTRAINT cvl_idn_fk
FOREIGN KEY (cvl_id)
REFERENCES public.cvl_civilites (cvl_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.idpr_idn_prf ADD CONSTRAINT prf_idpr_fk
FOREIGN KEY (prf_id)
REFERENCES public.prf_profils (prf_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.caa_controle_acces_application_interne ADD CONSTRAINT prf_caa_fk
FOREIGN KEY (prf_id)
REFERENCES public.prf_profils (prf_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.hac_historiques_activites ADD CONSTRAINT tpa_hac_fk
FOREIGN KEY (tpa_id)
REFERENCES public.tpa_types_action (tpa_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.ain_applications_internes ADD CONSTRAINT tap_ain_fk
FOREIGN KEY (tap_id)
REFERENCES public.tap_types_application (tap_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.caa_controle_acces_application_interne ADD CONSTRAINT ain_caa_fk
FOREIGN KEY (ain_id)
REFERENCES public.ain_applications_internes (ain_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.frn_fournisseurs ADD CONSTRAINT tfr_frn_fk
FOREIGN KEY (tfr_id)
REFERENCES public.tfr_types_fournisseur (tfr_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.frtg_frn_tgs ADD CONSTRAINT frn_frtg_fk
FOREIGN KEY (frn_id)
REFERENCES public.frn_fournisseurs (frn_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.app_applications ADD CONSTRAINT frn_app_fk
FOREIGN KEY (frn_id)
REFERENCES public.frn_fournisseurs (frn_id)
ON DELETE SET NULL
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.acfr_act_frn ADD CONSTRAINT frn_acfr_fk
FOREIGN KEY (frn_id)
REFERENCES public.frn_fournisseurs (frn_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.adf_actions_defaut ADD CONSTRAINT tad_adf_fk
FOREIGN KEY (tad_id)
REFERENCES public.tad_types_action_defaut (tad_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.lbr_libelles_referentiel ADD CONSTRAINT lng_lbr_fk
FOREIGN KEY (lng_id)
REFERENCES public.lng_langages (lng_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.caa_controle_acces_application_interne ADD CONSTRAINT drt_cta_fk
FOREIGN KEY (drt_id)
REFERENCES public.drt_droits (drt_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.cmp_campagnes ADD CONSTRAINT sct_cmp_fk
FOREIGN KEY (sct_id)
REFERENCES public.sct_societes (sct_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.ppr_parties_prenantes ADD CONSTRAINT sct_ppr_fk
FOREIGN KEY (sct_id)
REFERENCES public.sct_societes (sct_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.sts_sites ADD CONSTRAINT sct_sts_fk
FOREIGN KEY (sct_id)
REFERENCES public.sct_societes (sct_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.ent_entites ADD CONSTRAINT sct_ent_fk
FOREIGN KEY (sct_id)
REFERENCES public.sct_societes (sct_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.idn_identites ADD CONSTRAINT sct_idn_fk
FOREIGN KEY (sct_id)
REFERENCES public.sct_societes (sct_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.idsc_idn_sct ADD CONSTRAINT sct_idsc_fk
FOREIGN KEY (sct_id)
REFERENCES public.sct_societes (sct_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.sctg_sct_tgs ADD CONSTRAINT sct_sctg_fk
FOREIGN KEY (sct_id)
REFERENCES public.sct_societes (sct_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.app_applications ADD CONSTRAINT sct_app_fk
FOREIGN KEY (sct_id)
REFERENCES public.sct_societes (sct_id)
ON DELETE SET NULL
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.scap_sct_app ADD CONSTRAINT sct_scap_fk
FOREIGN KEY (sct_id)
REFERENCES public.sct_societes (sct_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.ete_echelle_temps ADD CONSTRAINT sct_societes_ete_echelle_temps_fk
FOREIGN KEY (sct_id)
REFERENCES public.sct_societes (sct_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.acap_act_app ADD CONSTRAINT ete_acap_dima_fk
FOREIGN KEY (ete_id_dima)
REFERENCES public.ete_echelle_temps (ete_id)
ON DELETE SET NULL
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.acap_act_app ADD CONSTRAINT ete_acap_pdma_fk
FOREIGN KEY (ete_id_pdma)
REFERENCES public.ete_echelle_temps (ete_id)
ON DELETE SET NULL
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.dma_dmia_activite ADD CONSTRAINT ete_dma_fk
FOREIGN KEY (ete_id)
REFERENCES public.ete_echelle_temps (ete_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.acfr_act_frn ADD CONSTRAINT ete_acfr_fk
FOREIGN KEY (ete_id)
REFERENCES public.ete_echelle_temps (ete_id)
ON DELETE SET NULL
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.rut_redemarrage_utilisateurs ADD CONSTRAINT ete_rut_fk
FOREIGN KEY (ete_id)
REFERENCES public.ete_echelle_temps (ete_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.scap_sct_app ADD CONSTRAINT ete_scap_dima_fk
FOREIGN KEY (ete_id_dima_dsi)
REFERENCES public.ete_echelle_temps (ete_id)
ON DELETE SET NULL
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.scap_sct_app ADD CONSTRAINT ete_scap_pdma_fk
FOREIGN KEY (ete_id_pdma_dsi)
REFERENCES public.ete_echelle_temps (ete_id)
ON DELETE SET NULL
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.idsc_idn_sct ADD CONSTRAINT idn_idsc_fk
FOREIGN KEY (idn_id)
REFERENCES public.idn_identites (idn_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.iden_idn_ent ADD CONSTRAINT idn_iden_fk
FOREIGN KEY (idn_id)
REFERENCES public.idn_identites (idn_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.idtg_idn_tgs ADD CONSTRAINT idn_idtg_fk
FOREIGN KEY (idn_id)
REFERENCES public.idn_identites (idn_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.idpr_idn_prf ADD CONSTRAINT idn_idpr_fk
FOREIGN KEY (idn_id)
REFERENCES public.idn_identites (idn_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.hsa_historique_authentifiant ADD CONSTRAINT idn_hsa_fk
FOREIGN KEY (idn_id)
REFERENCES public.idn_identites (idn_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.cmp_campagnes ADD CONSTRAINT idn_cmp_fk
FOREIGN KEY (idn_id)
REFERENCES public.idn_identites (idn_id)
ON DELETE SET NULL
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.iden_idn_ent ADD CONSTRAINT ent_iden_fk
FOREIGN KEY (ent_id)
REFERENCES public.ent_entites (ent_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.entg_ent_tgs ADD CONSTRAINT ent_entg_fk
FOREIGN KEY (ent_id)
REFERENCES public.ent_entites (ent_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.cmen_cmp_ent ADD CONSTRAINT ent_cmen_fk
FOREIGN KEY (ent_id)
REFERENCES public.ent_entites (ent_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.act_activites ADD CONSTRAINT ent_act_fk
FOREIGN KEY (ent_id)
REFERENCES public.ent_entites (ent_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.sttg_sts_tgs ADD CONSTRAINT sts_sttg_fk
FOREIGN KEY (sts_id)
REFERENCES public.sts_sites (sts_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.cmst_cmp_sts ADD CONSTRAINT sts_cmst_fk
FOREIGN KEY (sts_id)
REFERENCES public.sts_sites (sts_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.ppac_ppr_act ADD CONSTRAINT ppr_ppac_fk
FOREIGN KEY (ppr_id)
REFERENCES public.ppr_parties_prenantes (ppr_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.pptg_ppr_tgs ADD CONSTRAINT ppr_pptg_fk
FOREIGN KEY (ppr_id)
REFERENCES public.ppr_parties_prenantes (ppr_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.act_activites ADD CONSTRAINT ppr_act_resp_fk
FOREIGN KEY (ppr_id_responsable)
REFERENCES public.ppr_parties_prenantes (ppr_id)
ON DELETE SET NULL
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.act_activites ADD CONSTRAINT ppr_act_supp_fk
FOREIGN KEY (ppr_id_suppleant)
REFERENCES public.ppr_parties_prenantes (ppr_id)
ON DELETE SET NULL
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.pac_plan_actions ADD CONSTRAINT cmp_pac_fk
FOREIGN KEY (cmp_id)
REFERENCES public.cmp_campagnes (cmp_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.cmen_cmp_ent ADD CONSTRAINT cmp_cmen_fk
FOREIGN KEY (cmp_id)
REFERENCES public.cmp_campagnes (cmp_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.dma_dmia_activite ADD CONSTRAINT cmp_dma_fk
FOREIGN KEY (cmp_id)
REFERENCES public.cmp_campagnes (cmp_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.ppac_ppr_act ADD CONSTRAINT cmp_ppac_fk
FOREIGN KEY (cmp_id)
REFERENCES public.cmp_campagnes (cmp_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.tim_types_impact ADD CONSTRAINT cmp_tim_fk
FOREIGN KEY (cmp_id)
REFERENCES public.cmp_campagnes (cmp_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.mim_matrice_impacts ADD CONSTRAINT cmp_mim_fk
FOREIGN KEY (cmp_id)
REFERENCES public.cmp_campagnes (cmp_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.nim_niveaux_impact ADD CONSTRAINT cmp_nim_fk
FOREIGN KEY (cmp_id)
REFERENCES public.cmp_campagnes (cmp_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.cmst_cmp_sts ADD CONSTRAINT cmp_cmst_fk
FOREIGN KEY (cmp_id)
REFERENCES public.cmp_campagnes (cmp_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.act_activites ADD CONSTRAINT cmp_act_fk
FOREIGN KEY (cmp_id)
REFERENCES public.cmp_campagnes (cmp_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.acst_act_sts ADD CONSTRAINT act_acst_fk
FOREIGN KEY (act_id)
REFERENCES public.act_activites (act_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.ppac_ppr_act ADD CONSTRAINT act_ppac_fk
FOREIGN KEY (act_id)
REFERENCES public.act_activites (act_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.dma_dmia_activite ADD CONSTRAINT act_dma_fk
FOREIGN KEY (act_id)
REFERENCES public.act_activites (act_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.actg_act_tgs ADD CONSTRAINT act_actg_fk
FOREIGN KEY (act_id)
REFERENCES public.act_activites (act_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.acap_act_app ADD CONSTRAINT act_acap_fk
FOREIGN KEY (act_id)
REFERENCES public.act_activites (act_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.acfr_act_frn ADD CONSTRAINT act_fret_fk
FOREIGN KEY (act_id)
REFERENCES public.act_activites (act_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.rut_redemarrage_utilisateurs ADD CONSTRAINT act_rut_fk
FOREIGN KEY (act_id)
REFERENCES public.act_activites (act_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.acst_act_sts ADD CONSTRAINT cmst_acst_fk
FOREIGN KEY (cmp_id, sts_id)
REFERENCES public.cmst_cmp_sts (cmp_id, sts_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.mim_matrice_impacts ADD CONSTRAINT nim_mim_fk
FOREIGN KEY (nim_id)
REFERENCES public.nim_niveaux_impact (nim_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.mim_matrice_impacts ADD CONSTRAINT tim_mim_fk
FOREIGN KEY (tim_id)
REFERENCES public.tim_types_impact (tim_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.dma_dmia_activite ADD CONSTRAINT mim_dma_fk
FOREIGN KEY (mim_id)
REFERENCES public.mim_matrice_impacts (mim_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.aptg_app_tgs ADD CONSTRAINT app_aptg_fk
FOREIGN KEY (app_id)
REFERENCES public.app_applications (app_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.acap_act_app ADD CONSTRAINT app_acap_fk
FOREIGN KEY (app_id)
REFERENCES public.app_applications (app_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.scap_sct_app ADD CONSTRAINT app_scap_fk
FOREIGN KEY (app_id)
REFERENCES public.app_applications (app_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;