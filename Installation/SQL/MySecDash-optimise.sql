-- =====================================================
-- MySecDash - Script SQL Optimisé
-- Version: 2.0
-- Base de données: PostgreSQL
-- =====================================================

-- =====================================================
-- TABLES DE RÉFÉRENCE
-- =====================================================

-- Table: tgs_tags
CREATE TABLE public.tgs_tags (
    tgs_id BIGSERIAL NOT NULL,
    tgs_code VARCHAR(10) NOT NULL,
    tgs_libelle VARCHAR(60) NOT NULL,
    tgs_description TEXT NOT NULL,
    CONSTRAINT tgs_tags_pk PRIMARY KEY (tgs_id)
);

CREATE UNIQUE INDEX idx_u_tgs_code ON public.tgs_tags (tgs_code);
CREATE UNIQUE INDEX idx_u_tgs_libelle ON public.tgs_tags (tgs_libelle);

-- Table: tpo_types_objet
CREATE TABLE public.tpo_types_objet (
    tpo_id BIGSERIAL NOT NULL,
    tpo_code_libelle VARCHAR(45) NOT NULL,
    CONSTRAINT tpo_types_objet_pk PRIMARY KEY (tpo_id)
);

-- Table: cvl_civilites
CREATE TABLE public.cvl_civilites (
    cvl_id BIGSERIAL NOT NULL,
    cvl_nom VARCHAR(35) NOT NULL,
    cvl_prenom VARCHAR(25) NOT NULL,
    CONSTRAINT cvl_civilites_pk PRIMARY KEY (cvl_id)
);

CREATE UNIQUE INDEX idx_u_cvl_civilities ON public.cvl_civilites (cvl_nom, cvl_prenom);

-- Table: prf_profils
CREATE TABLE public.prf_profils (
    prf_id BIGSERIAL NOT NULL,
    prf_libelle VARCHAR(40) NOT NULL,
    prf_description TEXT,
    CONSTRAINT prf_profils_pk PRIMARY KEY (prf_id)
);

CREATE UNIQUE INDEX prf_profils_u1 ON public.prf_profils (prf_libelle);

-- Table: tpa_types_action
CREATE TABLE public.tpa_types_action (
    tpa_id BIGSERIAL NOT NULL,
    tpa_code_libelle VARCHAR(45) NOT NULL,
    CONSTRAINT tpa_types_action_pk PRIMARY KEY (tpa_id)
);

-- Table: tap_types_application
CREATE TABLE public.tap_types_application (
    tap_id BIGSERIAL NOT NULL,
    tap_code_libelle VARCHAR(45) NOT NULL,
    CONSTRAINT tap_types_application_pk PRIMARY KEY (tap_id)
);

CREATE UNIQUE INDEX tap_types_application_u1 ON public.tap_types_application (tap_code_libelle);

-- Table: ain_applications_internes
CREATE TABLE public.ain_applications_internes (
    ain_id BIGSERIAL NOT NULL,
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

CREATE UNIQUE INDEX idx_u_app_applications ON public.ain_applications_internes (ain_localisation);
CREATE INDEX idx_ain_tap_id ON public.ain_applications_internes(tap_id);
CREATE INDEX idx_ain_maintenance ON public.ain_applications_internes(ain_maintenance);

-- Table: tfr_types_fournisseur
CREATE TABLE public.tfr_types_fournisseur (
    tfr_id BIGSERIAL NOT NULL,
    tfr_nom_code VARCHAR(60) NOT NULL,
    CONSTRAINT tfr_types_fournisseur_pk PRIMARY KEY (tfr_id)
);

CREATE UNIQUE INDEX idx_u_tfr_types_fournisseur ON public.tfr_types_fournisseur (tfr_nom_code);

-- Table: hac_historiques_activites
CREATE TABLE public.hac_historiques_activites (
    hac_id BIGSERIAL NOT NULL,
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

CREATE INDEX idx_hac_id_application_metier ON public.hac_historiques_activites (hac_id_application_metier);
CREATE INDEX idx_hac_tpa_id ON public.hac_historiques_activites(tpa_id);
CREATE INDEX idx_hac_tpo_id ON public.hac_historiques_activites(tpo_id);
CREATE INDEX idx_hac_crs_id ON public.hac_historiques_activites(crs_id);
CREATE INDEX idx_hac_date ON public.hac_historiques_activites(hac_date DESC);
CREATE INDEX idx_hac_composite ON public.hac_historiques_activites(hac_date DESC, tpa_id, tpo_id);

-- Table: frn_fournisseurs
CREATE TABLE public.frn_fournisseurs (
    frn_id BIGSERIAL NOT NULL,
    tfr_id BIGINT,
    frn_nom VARCHAR(100) NOT NULL,
    frn_description TEXT NOT NULL,
    CONSTRAINT frn_fournisseurs_pk PRIMARY KEY (frn_id)
);

CREATE UNIQUE INDEX idx_u_frn_nom ON public.frn_fournisseurs (frn_nom);
CREATE INDEX idx_frn_tfr_id ON public.frn_fournisseurs(tfr_id);

-- Table: frtg_frn_tgs
CREATE TABLE public.frtg_frn_tgs (
    tgs_id BIGINT NOT NULL,
    frn_id BIGINT NOT NULL,
    CONSTRAINT frtg_frn_tgs_pk PRIMARY KEY (tgs_id, frn_id)
);

CREATE INDEX idx_frtg_frn_id ON public.frtg_frn_tgs(frn_id);

-- Table: tad_types_action_defaut
CREATE TABLE public.tad_types_action_defaut (
    tad_id BIGSERIAL NOT NULL,
    tad_nom_code VARCHAR(60) NOT NULL,
    CONSTRAINT tad_types_action_defaut_pk PRIMARY KEY (tad_id)
);

-- Table: adf_actions_defaut
CREATE TABLE public.adf_actions_defaut (
    adf_id BIGSERIAL NOT NULL,
    tad_id BIGINT NOT NULL,
    adf_debut_date_reelle DATE NOT NULL,
    adf_fin_date_reelle DATE NOT NULL,
    adf_debut_date_previsionnelle DATE NOT NULL,
    adf_fin_date_previsionnelle DATE NOT NULL,
    adf_nom VARCHAR(60) NOT NULL,
    adf_description TEXT NOT NULL,
    CONSTRAINT adf_actions_defaut_pk PRIMARY KEY (adf_id),
    CONSTRAINT chk_adf_dates_reelles CHECK (adf_fin_date_reelle >= adf_debut_date_reelle),
    CONSTRAINT chk_adf_dates_previsionnelles CHECK (adf_fin_date_previsionnelle >= adf_debut_date_previsionnelle)
);

CREATE INDEX idx_adf_tad_id ON public.adf_actions_defaut(tad_id);

-- Table: rpp_roles_parties_prenantes
CREATE TABLE public.rpp_roles_parties_prenantes (
    rpp_id BIGSERIAL NOT NULL,
    rpp_nom_code VARCHAR(60) NOT NULL,
    CONSTRAINT rpp_roles_parties_prenantes_pk PRIMARY KEY (rpp_id)
);

CREATE UNIQUE INDEX idx_u_rpp_nom_code ON public.rpp_roles_parties_prenantes (rpp_nom_code);

-- Table: prs_parametres_systeme
CREATE TABLE public.prs_parametres_systeme (
    prs_id BIGSERIAL NOT NULL,
    prs_groupe VARCHAR(30),
    prs_nom VARCHAR(30) NOT NULL,
    prs_type INTEGER DEFAULT 2 NOT NULL,
    prs_valeur VARCHAR(60) NOT NULL,
    prs_commentaire VARCHAR(100),
    prs_super_admin BOOLEAN DEFAULT false NOT NULL,
    CONSTRAINT prs_parametres_systeme_pk PRIMARY KEY (prs_id),
    CONSTRAINT chk_prs_type CHECK (prs_type IN (0, 1, 2))
);

COMMENT ON COLUMN public.prs_parametres_systeme.prs_type IS '0 = Booléen, 1 = Numérique, 2 = Alphanumérique';
COMMENT ON COLUMN public.prs_parametres_systeme.prs_super_admin IS 'Flag pour autoriser seulement le super admin';

CREATE UNIQUE INDEX idx_u_prs_nom ON public.prs_parametres_systeme (prs_nom);

-- Table: lng_langages
CREATE TABLE public.lng_langages (
    lng_id CHAR(2) NOT NULL,
    lng_libelle VARCHAR(50) NOT NULL,
    lng_langue_geree BOOLEAN DEFAULT false NOT NULL,
    CONSTRAINT lng_langages_pk PRIMARY KEY (lng_id)
);

-- Table: lbr_libelles_referentiel
CREATE TABLE public.lbr_libelles_referentiel (
    lbr_id BIGSERIAL NOT NULL,
    lng_id CHAR(2) NOT NULL,
    lbr_code VARCHAR(60) NOT NULL,
    lbr_libelle TEXT NOT NULL,
    CONSTRAINT lbr_libelles_referentiel_pk PRIMARY KEY (lbr_id)
);

CREATE UNIQUE INDEX idx_u_lbr_composite ON public.lbr_libelles_referentiel (lng_id, lbr_code);
CREATE INDEX idx_lbr_lng_id ON public.lbr_libelles_referentiel(lng_id);

-- Table: drt_droits
CREATE TABLE public.drt_droits (
    drt_id BIGSERIAL NOT NULL,
    drt_code_libelle VARCHAR(45) NOT NULL,
    CONSTRAINT drt_droits_pk PRIMARY KEY (drt_id)
);

COMMENT ON TABLE public.drt_droits IS '1 - Lecture, 2 - Ecriture, 3 - Modification, 4 - Suppression';

CREATE UNIQUE INDEX idx_u_drt_code_libelle ON public.drt_droits (drt_code_libelle);

-- Table: caa_controle_acces_application_interne
CREATE TABLE public.caa_controle_acces_application_interne (
    prf_id BIGINT NOT NULL,
    drt_id BIGINT NOT NULL,
    ain_id BIGINT NOT NULL,
    CONSTRAINT caa_controle_acces_application_interne_pk PRIMARY KEY (prf_id, drt_id, ain_id)
);

CREATE INDEX idx_caa_ain_id ON public.caa_controle_acces_application_interne(ain_id);
CREATE INDEX idx_caa_drt_id ON public.caa_controle_acces_application_interne(drt_id);

-- =====================================================
-- TABLES MÉTIER - SOCIÉTÉS ET STRUCTURES
-- =====================================================

-- Table: sct_societes
CREATE TABLE public.sct_societes (
    sct_id BIGSERIAL NOT NULL,
    sct_nom VARCHAR(100) NOT NULL,
    sct_description TEXT,
    CONSTRAINT sct_societes_pk PRIMARY KEY (sct_id)
);

CREATE UNIQUE INDEX idx_u_sct_nom ON public.sct_societes (sct_nom);

-- Table: ete_echelle_temps
CREATE TABLE public.ete_echelle_temps (
    ete_id BIGSERIAL NOT NULL,
    sct_id BIGINT NOT NULL,
    ete_poids SMALLINT NOT NULL,
    ete_nom_code VARCHAR(60) NOT NULL,
    CONSTRAINT ete_echelle_temps_pk PRIMARY KEY (ete_id),
    CONSTRAINT chk_ete_poids CHECK (ete_poids > 0)
);

COMMENT ON COLUMN public.ete_echelle_temps.ete_poids IS 'Plus le poids est grand et plus le temps de reprise est long';

CREATE INDEX idx_ete_nom_code ON public.ete_echelle_temps (ete_nom_code);
CREATE UNIQUE INDEX ete_u_composite ON public.ete_echelle_temps (sct_id, ete_poids);
CREATE INDEX idx_ete_sct_id ON public.ete_echelle_temps(sct_id);

-- Table: sctg_sct_tgs
CREATE TABLE public.sctg_sct_tgs (
    tgs_id BIGINT NOT NULL,
    sct_id BIGINT NOT NULL,
    CONSTRAINT sctg_sct_tgs_pk PRIMARY KEY (tgs_id, sct_id)
);

CREATE INDEX idx_sctg_sct_id ON public.sctg_sct_tgs(sct_id);

-- Table: idn_identites
CREATE TABLE public.idn_identites (
    idn_id BIGSERIAL NOT NULL,
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
    CONSTRAINT idn_identites_pk PRIMARY KEY (idn_id),
    CONSTRAINT chk_idn_tentative CHECK (idn_tentative >= 0),
    CONSTRAINT chk_idn_courriel CHECK (idn_courriel IS NULL OR idn_courriel ~* '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$')
);

CREATE UNIQUE INDEX idx_u_idn_login ON public.idn_identites (idn_login);
CREATE INDEX idx_idn_sct_id ON public.idn_identites(sct_id);
CREATE INDEX idx_idn_cvl_id ON public.idn_identites(cvl_id);
CREATE INDEX idx_idn_login_lower ON public.idn_identites(LOWER(idn_login));
CREATE INDEX idx_idn_derniere_connexion ON public.idn_identites(idn_derniere_connexion);
CREATE INDEX idx_idn_date_expiration ON public.idn_identites(idn_date_expiration);
CREATE INDEX idx_idn_desactiver ON public.idn_identites(idn_desactiver) WHERE idn_desactiver = false;
CREATE INDEX idx_idn_super_admin ON public.idn_identites(idn_super_admin) WHERE idn_super_admin = true;

-- Table: hsa_historique_authentifiant
CREATE TABLE public.hsa_historique_authentifiant (
    idn_id BIGINT NOT NULL,
    hsa_date TIMESTAMP NOT NULL,
    hsa_authentifiant VARCHAR(64) NOT NULL,
    hsa_grain_sel VARCHAR(32) NOT NULL,
    CONSTRAINT hsa_historique_authentifiant_pk PRIMARY KEY (idn_id, hsa_date)
);

-- Table: idpr_idn_prf
CREATE TABLE public.idpr_idn_prf (
    idn_id BIGINT NOT NULL,
    prf_id BIGINT NOT NULL,
    CONSTRAINT idpr_idn_prf_pk PRIMARY KEY (idn_id, prf_id)
);

CREATE INDEX idx_idpr_prf_id ON public.idpr_idn_prf(prf_id);

-- Table: idtg_idn_tgs
CREATE TABLE public.idtg_idn_tgs (
    tgs_id BIGINT NOT NULL,
    idn_id BIGINT NOT NULL,
    CONSTRAINT idtg_idn_tgs_pk PRIMARY KEY (tgs_id, idn_id)
);

CREATE INDEX idx_idtg_idn_id ON public.idtg_idn_tgs(idn_id);

-- Table: idsc_idn_sct
CREATE TABLE public.idsc_idn_sct (
    idn_id BIGINT NOT NULL,
    sct_id BIGINT NOT NULL,
    idsc_admin BOOLEAN DEFAULT FALSE NOT NULL,
    CONSTRAINT idsc_idn_sct_pk PRIMARY KEY (idn_id, sct_id)
);

COMMENT ON COLUMN public.idsc_idn_sct.idsc_admin IS 'Flag pour autoriser l''utilisateur à administrer cette Société ultérieurement';

CREATE INDEX idx_idsc_sct_id ON public.idsc_idn_sct(sct_id);

-- Table: ent_entites
CREATE TABLE public.ent_entites (
    ent_id BIGSERIAL NOT NULL,
    sct_id BIGINT NOT NULL,
    ent_nom VARCHAR(100) NOT NULL,
    ent_description TEXT,
    CONSTRAINT ent_entites_pk PRIMARY KEY (ent_id)
);

COMMENT ON TABLE public.ent_entites IS 'Une Entité est une représentation du découpage d''une Entreprise. Elle peut être une Direction, un Département, une Equipe, etc.';

CREATE UNIQUE INDEX idx_u_ent_composite ON public.ent_entites (sct_id, ent_nom);
CREATE INDEX idx_ent_sct_id ON public.ent_entites(sct_id);

-- Table: entg_ent_tgs
CREATE TABLE public.entg_ent_tgs (
    tgs_id BIGINT NOT NULL,
    ent_id BIGINT NOT NULL,
    CONSTRAINT entg_ent_tgs_pk PRIMARY KEY (tgs_id, ent_id)
);

CREATE INDEX idx_entg_ent_id ON public.entg_ent_tgs(ent_id);

-- Table: iden_idn_ent
CREATE TABLE public.iden_idn_ent (
    idn_id BIGINT NOT NULL,
    ent_id BIGINT NOT NULL,
    iden_admin BOOLEAN DEFAULT FALSE NOT NULL,
    CONSTRAINT iden_idn_ent_pk PRIMARY KEY (idn_id, ent_id)
);

COMMENT ON COLUMN public.iden_idn_ent.iden_admin IS 'Flag pour autoriser l''utilisateur à administrer cette Entité ultérieurement';

CREATE INDEX idx_iden_ent_id ON public.iden_idn_ent(ent_id);

-- Table: sts_sites
CREATE TABLE public.sts_sites (
    sts_id BIGSERIAL NOT NULL,
    sct_id BIGINT NOT NULL,
    sts_nom VARCHAR(50) NOT NULL,
    sts_description TEXT NOT NULL,
    CONSTRAINT sts_sites_pk PRIMARY KEY (sts_id)
);

CREATE UNIQUE INDEX idx_u_sts_composite ON public.sts_sites (sct_id, sts_nom);
CREATE INDEX idx_sts_sct_id ON public.sts_sites(sct_id);

-- Table: sttg_sts_tgs
CREATE TABLE public.sttg_sts_tgs (
    tgs_id BIGINT NOT NULL,
    sts_id BIGINT NOT NULL,
    CONSTRAINT sttg_sts_tgs_pk PRIMARY KEY (tgs_id, sts_id)
);

CREATE INDEX idx_sttg_sts_id ON public.sttg_sts_tgs(sts_id);

-- Table: ppr_parties_prenantes
CREATE TABLE public.ppr_parties_prenantes (
    ppr_id BIGSERIAL NOT NULL,
    sct_id BIGINT NOT NULL,
    ppr_nom VARCHAR(35) NOT NULL,
    ppr_prenom VARCHAR(25) NOT NULL,
    ppr_interne BOOLEAN DEFAULT true NOT NULL,
    ppr_description TEXT,
    CONSTRAINT ppr_parties_prenantes_pk PRIMARY KEY (ppr_id)
);

COMMENT ON COLUMN public.ppr_parties_prenantes.ppr_interne IS 'true = interne, false = externe';

CREATE UNIQUE INDEX idx_u_ppr_composite ON public.ppr_parties_prenantes (sct_id, ppr_nom, ppr_prenom);
CREATE INDEX idx_ppr_sct_id ON public.ppr_parties_prenantes(sct_id);

-- Table: pptg_ppr_tgs
CREATE TABLE public.pptg_ppr_tgs (
    ppr_id BIGINT NOT NULL,
    tgs_id BIGINT NOT NULL,
    CONSTRAINT pptg_ppr_tgs_pk PRIMARY KEY (ppr_id, tgs_id)
);

CREATE INDEX idx_pptg_ppr_id ON public.pptg_ppr_tgs(ppr_id);

-- =====================================================
-- TABLES MÉTIER - CAMPAGNES ET ACTIVITÉS
-- =====================================================

-- Table: cmp_campagnes
CREATE TABLE public.cmp_campagnes (
    cmp_id BIGSERIAL NOT NULL,
    sct_id BIGINT NOT NULL,
    idn_id BIGINT,
    cmp_date DATE NOT NULL,
    cmp_flag_validation BOOLEAN DEFAULT FALSE NOT NULL,
    cmp_date_validation TIMESTAMP,
    cmp_niveau_impact_accepte SMALLINT DEFAULT 2 NOT NULL,
    CONSTRAINT cmp_campagnes_pk PRIMARY KEY (cmp_id),
    CONSTRAINT chk_cmp_niveau_impact CHECK (cmp_niveau_impact_accepte > 0)
);

COMMENT ON COLUMN public.cmp_campagnes.cmp_flag_validation IS 'FALSE = La Campagne n''est pas validée, TRUE = La Campagne est validée';

CREATE UNIQUE INDEX idx_u_cmp_composite ON public.cmp_campagnes (sct_id, cmp_date);
CREATE INDEX idx_cmp_sct_id ON public.cmp_campagnes(sct_id);
CREATE INDEX idx_cmp_idn_id ON public.cmp_campagnes(idn_id);
CREATE INDEX idx_cmp_date ON public.cmp_campagnes(cmp_date);
CREATE INDEX idx_cmp_flag_validation ON public.cmp_campagnes(cmp_flag_validation);

-- Table: act_activites
CREATE TABLE public.act_activites (
    act_id BIGSERIAL NOT NULL,
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
    CONSTRAINT act_activites_pk PRIMARY KEY (act_id),
    CONSTRAINT chk_act_effectifs CHECK (act_effectifs_en_nominal > 0),
    CONSTRAINT chk_act_taux CHECK (act_taux_occupation BETWEEN 0 AND 100),
    CONSTRAINT chk_act_effectifs_distance CHECK (act_effectifs_a_distance >= 0)
);

COMMENT ON COLUMN public.act_activites.act_teletravail IS 'FALSE = Activité pas télétravaillable, TRUE = Activité télétravaillable';

CREATE UNIQUE INDEX idx_u_act_composite ON public.act_activites (ent_id, cmp_id, act_nom);
CREATE INDEX idx_act_ent_id ON public.act_activites(ent_id);
CREATE INDEX idx_act_cmp_id ON public.act_activites(cmp_id);
CREATE INDEX idx_act_ppr_responsable ON public.act_activites(ppr_id_responsable);
CREATE INDEX idx_act_ppr_suppleant ON public.act_activites(ppr_id_suppleant);

-- Table: rut_redemarrage_utilisateurs
CREATE TABLE public.rut_redemarrage_utilisateurs (
    ete_id BIGINT NOT NULL,
    act_id BIGINT NOT NULL,
    rut_nbr_utilisateurs_a_redemarrer INTEGER NOT NULL,
    CONSTRAINT rut_redemarrage_utilisateurs_pk PRIMARY KEY (ete_id, act_id),
    CONSTRAINT chk_rut_nbr CHECK (rut_nbr_utilisateurs_a_redemarrer >= 0)
);

CREATE INDEX idx_rut_act_id ON public.rut_redemarrage_utilisateurs(act_id);

-- Table: acfr_act_frn
CREATE TABLE public.acfr_act_frn (
    act_id BIGINT NOT NULL,
    frn_id BIGINT NOT NULL,
    ete_id BIGINT,
    acfr_consequence_indisponibilite TEXT,
    acfr_palliatif_tiers TEXT,
    CONSTRAINT acfr_act_frn_pk PRIMARY KEY (act_id, frn_id)
);

CREATE INDEX idx_acfr_frn_id ON public.acfr_act_frn(frn_id);
CREATE INDEX idx_acfr_ete_id ON public.acfr_act_frn(ete_id);

-- Table: actg_act_tgs
CREATE TABLE public.actg_act_tgs (
    tgs_id BIGINT NOT NULL,
    act_id BIGINT NOT NULL,
    CONSTRAINT actg_act_tgs_pk PRIMARY KEY (tgs_id, act_id)
);

CREATE INDEX idx_actg_act_id ON public.actg_act_tgs(act_id);

-- Table: cmst_cmp_sts
CREATE TABLE public.cmst_cmp_sts (
    cmp_id BIGINT NOT NULL,
    sts_id BIGINT NOT NULL,
    CONSTRAINT cmst_cmp_sts_pk PRIMARY KEY (cmp_id, sts_id)
);

CREATE INDEX idx_cmst_sts_id ON public.cmst_cmp_sts(sts_id);

-- Table: acst_act_sts
CREATE TABLE public.acst_act_sts (
    act_id BIGINT NOT NULL,
    cmp_id BIGINT NOT NULL,
    sts_id BIGINT NOT NULL,
    acst_type_site SMALLINT DEFAULT 0 NOT NULL,
    CONSTRAINT acst_act_sts_pk PRIMARY KEY (act_id, cmp_id, sts_id),
    CONSTRAINT chk_acst_type CHECK (acst_type_site IN (0, 1))
);

COMMENT ON COLUMN public.acst_act_sts.acst_type_site IS '0 = Site Nominal pour l''Activité, 1 = Site de Secours pour l''Activité';

CREATE INDEX idx_acst_composite ON public.acst_act_sts(cmp_id, sts_id);

-- Table: nim_niveaux_impact
CREATE TABLE public.nim_niveaux_impact (
    nim_id BIGSERIAL NOT NULL,
    cmp_id BIGINT NOT NULL,
    nim_numero SMALLINT NOT NULL,
    nim_poids SMALLINT NOT NULL,
    nim_nom_code VARCHAR(60) NOT NULL,
    nim_couleur CHAR(6) NOT NULL,
    CONSTRAINT nim_niveaux_impact_pk PRIMARY KEY (nim_id),
    CONSTRAINT chk_nim_couleur CHECK (nim_couleur ~* '^[0-9A-Fa-f]{6}$'),
    CONSTRAINT chk_nim_numero CHECK (nim_numero > 0),
    CONSTRAINT chk_nim_poids CHECK (nim_poids > 0)
);

CREATE UNIQUE INDEX idx_u_nim_nom_code ON public.nim_niveaux_impact (cmp_id, nim_nom_code);
CREATE UNIQUE INDEX idx_u_nim_numero ON public.nim_niveaux_impact (cmp_id, nim_numero);
CREATE UNIQUE INDEX idx_u_nim_poids ON public.nim_niveaux_impact (cmp_id, nim_poids);
CREATE INDEX idx_nim_cmp_id ON public.nim_niveaux_impact(cmp_id);

-- Table: tim_types_impact
CREATE TABLE public.tim_types_impact (
    tim_id BIGSERIAL NOT NULL,
    cmp_id BIGINT NOT NULL,
    tim_poids SMALLINT NOT NULL,
    tim_nom_code VARCHAR(60) NOT NULL,
    CONSTRAINT tim_types_impact_pk PRIMARY KEY (tim_id),
    CONSTRAINT chk_tim_poids CHECK (tim_poids > 0)
);

CREATE UNIQUE INDEX idx_u_tim_nom_code ON public.tim_types_impact (cmp_id, tim_nom_code);
CREATE UNIQUE INDEX idx_u_tim_poids ON public.tim_types_impact (cmp_id, tim_poids);
CREATE INDEX idx_tim_cmp_id ON public.tim_types_impact(cmp_id);

-- Table: mim_matrice_impacts
CREATE TABLE public.mim_matrice_impacts (
    mim_id BIGSERIAL NOT NULL,
    cmp_id BIGINT NOT NULL,
    nim_id BIGINT NOT NULL,
    tim_id BIGINT NOT NULL,
    mim_description TEXT NOT NULL,
    CONSTRAINT mim_matrice_impacts_pk PRIMARY KEY (mim_id)
);

CREATE UNIQUE INDEX idx_u_mim_composite ON public.mim_matrice_impacts (cmp_id, nim_id, tim_id);
CREATE INDEX idx_mim_cmp_id ON public.mim_matrice_impacts(cmp_id);
CREATE INDEX idx_mim_nim_id ON public.mim_matrice_impacts(nim_id);
CREATE INDEX idx_mim_tim_id ON public.mim_matrice_impacts(tim_id);

-- Table: ppac_ppr_act
CREATE TABLE public.ppac_ppr_act (
    ppr_id BIGINT NOT NULL,
    act_id BIGINT NOT NULL,
    cmp_id BIGINT NOT NULL,
    ppac_description TEXT,
    CONSTRAINT ppac_ppr_act_pk PRIMARY KEY (ppr_id, act_id, cmp_id)
);

CREATE INDEX idx_ppac_act_id ON public.ppac_ppr_act(act_id);
CREATE INDEX idx_ppac_cmp_id ON public.ppac_ppr_act(cmp_id);

-- Table: dma_dmia_activite
CREATE TABLE public.dma_dmia_activite (
    dma_id BIGSERIAL NOT NULL,
    act_id BIGINT NOT NULL,
    ete_id BIGINT NOT NULL,
    mim_id BIGINT NOT NULL,
    cmp_id BIGINT NOT NULL,
    CONSTRAINT dma_dmia_activite_pk PRIMARY KEY (dma_id)
);

CREATE UNIQUE INDEX idx_u_dma_composite ON public.dma_dmia_activite (act_id, ete_id, mim_id, cmp_id);
CREATE INDEX idx_dma_act_id ON public.dma_dmia_activite(act_id);
CREATE INDEX idx_dma_ete_id ON public.dma_dmia_activite(ete_id);
CREATE INDEX idx_dma_mim_id ON public.dma_dmia_activite(mim_id);
CREATE INDEX idx_dma_cmp_id ON public.dma_dmia_activite(cmp_id);

-- Table: cmen_cmp_ent
CREATE TABLE public.cmen_cmp_ent (
    ent_id BIGINT NOT NULL,
    cmp_id BIGINT NOT NULL,
    ppr_id_cpca BIGINT,
    cmen_date_entretien_cpca DATE,
    ppr_id_validation BIGINT,
    cmen_date_validation DATE,
    cmen_effectif_total INTEGER,
    CONSTRAINT cmen_cmp_ent_pk PRIMARY KEY (ent_id, cmp_id),
    CONSTRAINT chk_cmen_effectif CHECK (cmen_effectif_total IS NULL OR cmen_effectif_total >= 0)
);

CREATE INDEX idx_cmen_cmp_id ON public.cmen_cmp_ent(cmp_id);
CREATE INDEX idx_cmen_ppr_cpca ON public.cmen_cmp_ent(ppr_id_cpca);
CREATE INDEX idx_cmen_ppr_validation ON public.cmen_cmp_ent(ppr_id_validation);

-- Table: pac_plan_actions
CREATE TABLE public.pac_plan_actions (
    pac_id BIGSERIAL NOT NULL,
    cmp_id BIGINT NOT NULL,
    pac_action_defaut BOOLEAN DEFAULT false NOT NULL,
    pac_debut_date_previsionnelle DATE NOT NULL,
    pac_fin_date_previsionnelle DATE NOT NULL,
    pac_debut_date_reelle DATE NOT NULL,
    pac_fin_date_reelle DATE NOT NULL,
    pac_nom VARCHAR(60) NOT NULL,
    pac_description TEXT NOT NULL,
    CONSTRAINT pac_plan_actions_pk PRIMARY KEY (pac_id),
    CONSTRAINT chk_pac_dates_reelles CHECK (pac_fin_date_reelle >= pac_debut_date_reelle),
    CONSTRAINT chk_pac_dates_previsionnelles CHECK (pac_fin_date_previsionnelle >= pac_debut_date_previsionnelle)
);

CREATE UNIQUE INDEX idx_pac_composite ON public.pac_plan_actions (cmp_id, pac_nom);
CREATE INDEX idx_pac_cmp_id ON public.pac_plan_actions(cmp_id);

-- =====================================================
-- TABLES MÉTIER - APPLICATIONS
-- =====================================================

-- Table: app_applications
CREATE TABLE public.app_applications (
    app_id BIGSERIAL NOT NULL,
    frn_id BIGINT,
    sct_id BIGINT,
    app_nom VARCHAR(100) NOT NULL,
    app_nom_alias VARCHAR(100),
    app_hebergement VARCHAR(100),
    app_niveau_service VARCHAR(100),
    app_description TEXT,
    CONSTRAINT app_applications_pk PRIMARY KEY (app_id)
);

CREATE UNIQUE INDEX idx_u_app_nom ON public.app_applications (app_nom);
CREATE INDEX idx_app_frn_id ON public.app_applications(frn_id);
CREATE INDEX idx_app_sct_id ON public.app_applications(sct_id);

-- Table: scap_sct_app
CREATE TABLE public.scap_sct_app (
    app_id BIGINT NOT NULL,
    sct_id BIGINT NOT NULL,
    ete_id_dima_dsi BIGINT,
    ete_id_pdma_dsi BIGINT,
    scap_description_dima TEXT,
    scap_description_pdma TEXT,
    CONSTRAINT scap_sct_app_pk PRIMARY KEY (app_id, sct_id)
);

CREATE INDEX idx_scap_sct_id ON public.scap_sct_app(sct_id);
CREATE INDEX idx_scap_ete_dima ON public.scap_sct_app(ete_id_dima_dsi);
CREATE INDEX idx_scap_ete_pdma ON public.scap_sct_app(ete_id_pdma_dsi);

-- Table: acap_act_app
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

CREATE INDEX idx_acap_app_id ON public.acap_act_app(app_id);
CREATE INDEX idx_acap_ete_dima ON public.acap_act_app(ete_id_dima);
CREATE INDEX idx_acap_ete_pdma ON public.acap_act_app(ete_id_pdma);
CREATE INDEX idx_acap_composite ON public.acap_act_app(act_id, app_id);

-- Table: aptg_app_tgs
CREATE TABLE public.aptg_app_tgs (
    app_id BIGINT NOT NULL,
    tgs_id BIGINT NOT NULL,
    CONSTRAINT aptg_app_tgs_pk PRIMARY KEY (app_id, tgs_id)
);

CREATE INDEX idx_aptg_app_id ON public.aptg_app_tgs(app_id);

-- =====================================================
-- CONTRAINTES DE CLÉS ÉTRANGÈRES
-- =====================================================

-- Contraintes pour tgs_tags
ALTER TABLE public.idtg_idn_tgs ADD CONSTRAINT tgs_idtg_fk
FOREIGN KEY (tgs_id) REFERENCES public.tgs_tags (tgs_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.pptg_ppr_tgs ADD CONSTRAINT tgs_pptg_fk
FOREIGN KEY (tgs_id) REFERENCES public.tgs_tags (tgs_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.actg_act_tgs ADD CONSTRAINT tgs_actg_fk
FOREIGN KEY (tgs_id) REFERENCES public.tgs_tags (tgs_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.entg_ent_tgs ADD CONSTRAINT tgs_entg_fk
FOREIGN KEY (tgs_id) REFERENCES public.tgs_tags (tgs_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.sctg_sct_tgs ADD CONSTRAINT tgs_sctg_fk
FOREIGN KEY (tgs_id) REFERENCES public.tgs_tags (tgs_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.sttg_sts_tgs ADD CONSTRAINT tgs_sttg_fk
FOREIGN KEY (tgs_id) REFERENCES public.tgs_tags (tgs_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.frtg_frn_tgs ADD CONSTRAINT tgs_frtg_fk
FOREIGN KEY (tgs_id) REFERENCES public.tgs_tags (tgs_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.aptg_app_tgs ADD CONSTRAINT tgs_aptg_fk
FOREIGN KEY (tgs_id) REFERENCES public.tgs_tags (tgs_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

-- Contraintes pour tpo_types_objet
ALTER TABLE public.hac_historiques_activites ADD CONSTRAINT tpo_hac_fk
FOREIGN KEY (tpo_id) REFERENCES public.tpo_types_objet (tpo_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

-- Contraintes pour cvl_civilites
ALTER TABLE public.idn_identites ADD CONSTRAINT cvl_idn_fk
FOREIGN KEY (cvl_id) REFERENCES public.cvl_civilites (cvl_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

-- Contraintes pour prf_profils
ALTER TABLE public.idpr_idn_prf ADD CONSTRAINT prf_idpr_fk
FOREIGN KEY (prf_id) REFERENCES public.prf_profils (prf_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.caa_controle_acces_application_interne ADD CONSTRAINT prf_caa_fk
FOREIGN KEY (prf_id) REFERENCES public.prf_profils (prf_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

-- Contraintes pour tpa_types_action
ALTER TABLE public.hac_historiques_activites ADD CONSTRAINT tpa_hac_fk
FOREIGN KEY (tpa_id) REFERENCES public.tpa_types_action (tpa_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

-- Contraintes pour tap_types_application
ALTER TABLE public.ain_applications_internes ADD CONSTRAINT tap_ain_fk
FOREIGN KEY (tap_id) REFERENCES public.tap_types_application (tap_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

-- Contraintes pour ain_applications_internes
ALTER TABLE public.caa_controle_acces_application_interne ADD CONSTRAINT ain_caa_fk
FOREIGN KEY (ain_id) REFERENCES public.ain_applications_internes (ain_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

-- Contraintes pour tfr_types_fournisseur
ALTER TABLE public.frn_fournisseurs ADD CONSTRAINT tfr_frn_fk
FOREIGN KEY (tfr_id) REFERENCES public.tfr_types_fournisseur (tfr_id)
ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE;

-- Contraintes pour frn_fournisseurs
ALTER TABLE public.frtg_frn_tgs ADD CONSTRAINT frn_frtg_fk
FOREIGN KEY (frn_id) REFERENCES public.frn_fournisseurs (frn_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.app_applications ADD CONSTRAINT frn_app_fk
FOREIGN KEY (frn_id) REFERENCES public.frn_fournisseurs (frn_id)
ON DELETE SET NULL ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.acfr_act_frn ADD CONSTRAINT frn_acfr_fk
FOREIGN KEY (frn_id) REFERENCES public.frn_fournisseurs (frn_id)
ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE;

-- Contraintes pour tad_types_action_defaut
ALTER TABLE public.adf_actions_defaut ADD CONSTRAINT tad_adf_fk
FOREIGN KEY (tad_id) REFERENCES public.tad_types_action_defaut (tad_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

-- Contraintes pour lng_langages
ALTER TABLE public.lbr_libelles_referentiel ADD CONSTRAINT lng_lbr_fk
FOREIGN KEY (lng_id) REFERENCES public.lng_langages (lng_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

-- Contraintes pour drt_droits
ALTER TABLE public.caa_controle_acces_application_interne ADD CONSTRAINT drt_cta_fk
FOREIGN KEY (drt_id) REFERENCES public.drt_droits (drt_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

-- Contraintes pour sct_societes
ALTER TABLE public.cmp_campagnes ADD CONSTRAINT sct_cmp_fk
FOREIGN KEY (sct_id) REFERENCES public.sct_societes (sct_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.ppr_parties_prenantes ADD CONSTRAINT sct_ppr_fk
FOREIGN KEY (sct_id) REFERENCES public.sct_societes (sct_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.sts_sites ADD CONSTRAINT sct_sts_fk
FOREIGN KEY (sct_id) REFERENCES public.sct_societes (sct_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.ent_entites ADD CONSTRAINT sct_ent_fk
FOREIGN KEY (sct_id) REFERENCES public.sct_societes (sct_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.idn_identites ADD CONSTRAINT sct_idn_fk
FOREIGN KEY (sct_id) REFERENCES public.sct_societes (sct_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.idsc_idn_sct ADD CONSTRAINT sct_idsc_fk
FOREIGN KEY (sct_id) REFERENCES public.sct_societes (sct_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.sctg_sct_tgs ADD CONSTRAINT sct_sctg_fk
FOREIGN KEY (sct_id) REFERENCES public.sct_societes (sct_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.app_applications ADD CONSTRAINT sct_app_fk
FOREIGN KEY (sct_id) REFERENCES public.sct_societes (sct_id)
ON DELETE SET NULL ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.scap_sct_app ADD CONSTRAINT sct_scap_fk
FOREIGN KEY (sct_id) REFERENCES public.sct_societes (sct_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.ete_echelle_temps ADD CONSTRAINT sct_ete_fk
FOREIGN KEY (sct_id) REFERENCES public.sct_societes (sct_id)
ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE;

-- Contraintes pour ete_echelle_temps
ALTER TABLE public.acap_act_app ADD CONSTRAINT ete_acap_dima_fk
FOREIGN KEY (ete_id_dima) REFERENCES public.ete_echelle_temps (ete_id)
ON DELETE SET NULL ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.acap_act_app ADD CONSTRAINT ete_acap_pdma_fk
FOREIGN KEY (ete_id_pdma) REFERENCES public.ete_echelle_temps (ete_id)
ON DELETE SET NULL ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.dma_dmia_activite ADD CONSTRAINT ete_dma_fk
FOREIGN KEY (ete_id) REFERENCES public.ete_echelle_temps (ete_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.acfr_act_frn ADD CONSTRAINT ete_acfr_fk
FOREIGN KEY (ete_id) REFERENCES public.ete_echelle_temps (ete_id)
ON DELETE SET NULL ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.rut_redemarrage_utilisateurs ADD CONSTRAINT ete_rut_fk
FOREIGN KEY (ete_id) REFERENCES public.ete_echelle_temps (ete_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.scap_sct_app ADD CONSTRAINT ete_scap_dima_fk
FOREIGN KEY (ete_id_dima_dsi) REFERENCES public.ete_echelle_temps (ete_id)
ON DELETE SET NULL ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.scap_sct_app ADD CONSTRAINT ete_scap_pdma_fk
FOREIGN KEY (ete_id_pdma_dsi) REFERENCES public.ete_echelle_temps (ete_id)
ON DELETE SET NULL ON UPDATE NO ACTION NOT DEFERRABLE;

-- Contraintes pour idn_identites
ALTER TABLE public.idsc_idn_sct ADD CONSTRAINT idn_idsc_fk
FOREIGN KEY (idn_id) REFERENCES public.idn_identites (idn_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.iden_idn_ent ADD CONSTRAINT idn_iden_fk
FOREIGN KEY (idn_id) REFERENCES public.idn_identites (idn_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.idtg_idn_tgs ADD CONSTRAINT idn_idtg_fk
FOREIGN KEY (idn_id) REFERENCES public.idn_identites (idn_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.idpr_idn_prf ADD CONSTRAINT idn_idpr_fk
FOREIGN KEY (idn_id) REFERENCES public.idn_identites (idn_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.hsa_historique_authentifiant ADD CONSTRAINT idn_hsa_fk
FOREIGN KEY (idn_id) REFERENCES public.idn_identites (idn_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.cmp_campagnes ADD CONSTRAINT idn_cmp_fk
FOREIGN KEY (idn_id) REFERENCES public.idn_identites (idn_id)
ON DELETE SET NULL ON UPDATE NO ACTION NOT DEFERRABLE;

-- Contraintes pour ent_entites
ALTER TABLE public.iden_idn_ent ADD CONSTRAINT ent_iden_fk
FOREIGN KEY (ent_id) REFERENCES public.ent_entites (ent_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.entg_ent_tgs ADD CONSTRAINT ent_entg_fk
FOREIGN KEY (ent_id) REFERENCES public.ent_entites (ent_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.cmen_cmp_ent ADD CONSTRAINT ent_cmen_fk
FOREIGN KEY (ent_id) REFERENCES public.ent_entites (ent_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.act_activites ADD CONSTRAINT ent_act_fk
FOREIGN KEY (ent_id) REFERENCES public.ent_entites (ent_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

-- Contraintes pour sts_sites
ALTER TABLE public.sttg_sts_tgs ADD CONSTRAINT sts_sttg_fk
FOREIGN KEY (sts_id) REFERENCES public.sts_sites (sts_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.cmst_cmp_sts ADD CONSTRAINT sts_cmst_fk
FOREIGN KEY (sts_id) REFERENCES public.sts_sites (sts_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

-- Contraintes pour ppr_parties_prenantes
ALTER TABLE public.ppac_ppr_act ADD CONSTRAINT ppr_ppac_fk
FOREIGN KEY (ppr_id) REFERENCES public.ppr_parties_prenantes (ppr_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.pptg_ppr_tgs ADD CONSTRAINT ppr_pptg_fk
FOREIGN KEY (ppr_id) REFERENCES public.ppr_parties_prenantes (ppr_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.act_activites ADD CONSTRAINT ppr_act_resp_fk
FOREIGN KEY (ppr_id_responsable) REFERENCES public.ppr_parties_prenantes (ppr_id)
ON DELETE SET NULL ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.act_activites ADD CONSTRAINT ppr_act_supp_fk
FOREIGN KEY (ppr_id_suppleant) REFERENCES public.ppr_parties_prenantes (ppr_id)
ON DELETE SET NULL ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.cmen_cmp_ent ADD CONSTRAINT ppr_cmen_cpca_fk
FOREIGN KEY (ppr_id_cpca) REFERENCES public.ppr_parties_prenantes (ppr_id)
ON DELETE SET NULL ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.cmen_cmp_ent ADD CONSTRAINT ppr_cmen_validation_fk
FOREIGN KEY (ppr_id_validation) REFERENCES public.ppr_parties_prenantes (ppr_id)
ON DELETE SET NULL ON UPDATE NO ACTION NOT DEFERRABLE;

-- Contraintes pour cmp_campagnes
ALTER TABLE public.pac_plan_actions ADD CONSTRAINT cmp_pac_fk
FOREIGN KEY (cmp_id) REFERENCES public.cmp_campagnes (cmp_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.cmen_cmp_ent ADD CONSTRAINT cmp_cmen_fk
FOREIGN KEY (cmp_id) REFERENCES public.cmp_campagnes (cmp_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.dma_dmia_activite ADD CONSTRAINT cmp_dma_fk
FOREIGN KEY (cmp_id) REFERENCES public.cmp_campagnes (cmp_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.ppac_ppr_act ADD CONSTRAINT cmp_ppac_fk
FOREIGN KEY (cmp_id) REFERENCES public.cmp_campagnes (cmp_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.tim_types_impact ADD CONSTRAINT cmp_tim_fk
FOREIGN KEY (cmp_id) REFERENCES public.cmp_campagnes (cmp_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.mim_matrice_impacts ADD CONSTRAINT cmp_mim_fk
FOREIGN KEY (cmp_id) REFERENCES public.cmp_campagnes (cmp_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.nim_niveaux_impact ADD CONSTRAINT cmp_nim_fk
FOREIGN KEY (cmp_id) REFERENCES public.cmp_campagnes (cmp_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.cmst_cmp_sts ADD CONSTRAINT cmp_cmst_fk
FOREIGN KEY (cmp_id) REFERENCES public.cmp_campagnes (cmp_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.act_activites ADD CONSTRAINT cmp_act_fk
FOREIGN KEY (cmp_id) REFERENCES public.cmp_campagnes (cmp_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

-- Contraintes pour act_activites
ALTER TABLE public.acst_act_sts ADD CONSTRAINT act_acst_fk
FOREIGN KEY (act_id) REFERENCES public.act_activites (act_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.ppac_ppr_act ADD CONSTRAINT act_ppac_fk
FOREIGN KEY (act_id) REFERENCES public.act_activites (act_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.dma_dmia_activite ADD CONSTRAINT act_dma_fk
FOREIGN KEY (act_id) REFERENCES public.act_activites (act_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.actg_act_tgs ADD CONSTRAINT act_actg_fk
FOREIGN KEY (act_id) REFERENCES public.act_activites (act_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.acap_act_app ADD CONSTRAINT act_acap_fk
FOREIGN KEY (act_id) REFERENCES public.act_activites (act_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.acfr_act_frn ADD CONSTRAINT act_acfr_fk
FOREIGN KEY (act_id) REFERENCES public.act_activites (act_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.rut_redemarrage_utilisateurs ADD CONSTRAINT act_rut_fk
FOREIGN KEY (act_id) REFERENCES public.act_activites (act_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

-- Contraintes pour cmst_cmp_sts
ALTER TABLE public.acst_act_sts ADD CONSTRAINT cmst_acst_fk
FOREIGN KEY (cmp_id, sts_id) REFERENCES public.cmst_cmp_sts (cmp_id, sts_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

-- Contraintes pour nim_niveaux_impact
ALTER TABLE public.mim_matrice_impacts ADD CONSTRAINT nim_mim_fk
FOREIGN KEY (nim_id) REFERENCES public.nim_niveaux_impact (nim_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

-- Contraintes pour tim_types_impact
ALTER TABLE public.mim_matrice_impacts ADD CONSTRAINT tim_mim_fk
FOREIGN KEY (tim_id) REFERENCES public.tim_types_impact (tim_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

-- Contraintes pour mim_matrice_impacts
ALTER TABLE public.dma_dmia_activite ADD CONSTRAINT mim_dma_fk
FOREIGN KEY (mim_id) REFERENCES public.mim_matrice_impacts (mim_id)
ON DELETE NO ACTION ON UPDATE NO ACTION NOT DEFERRABLE;

-- Contraintes pour app_applications
ALTER TABLE public.aptg_app_tgs ADD CONSTRAINT app_aptg_fk
FOREIGN KEY (app_id) REFERENCES public.app_applications (app_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.acap_act_app ADD CONSTRAINT app_acap_fk
FOREIGN KEY (app_id) REFERENCES public.app_applications (app_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

ALTER TABLE public.scap_sct_app ADD CONSTRAINT app_scap_fk
FOREIGN KEY (app_id) REFERENCES public.app_applications (app_id)
ON DELETE CASCADE ON UPDATE NO ACTION NOT DEFERRABLE;

-- =====================================================
-- ANALYSE DES TABLES
-- =====================================================

ANALYZE public.tgs_tags;
ANALYZE public.tpo_types_objet;
ANALYZE public.cvl_civilites;
ANALYZE public.prf_profils;
ANALYZE public.tpa_types_action;
ANALYZE public.tap_types_application;
ANALYZE public.ain_applications_internes;
ANALYZE public.tfr_types_fournisseur;
ANALYZE public.hac_historiques_activites;
ANALYZE public.frn_fournisseurs;
ANALYZE public.frtg_frn_tgs;
ANALYZE public.tad_types_action_defaut;
ANALYZE public.adf_actions_defaut;
ANALYZE public.rpp_roles_parties_prenantes;
ANALYZE public.prs_parametres_systeme;
ANALYZE public.lng_langages;
ANALYZE public.lbr_libelles_referentiel;
ANALYZE public.drt_droits;
ANALYZE public.caa_controle_acces_application_interne;
ANALYZE public.sct_societes;
ANALYZE public.ete_echelle_temps;
ANALYZE public.sctg_sct_tgs;
ANALYZE public.idn_identites;
ANALYZE public.hsa_historique_authentifiant;
ANALYZE public.idpr_idn_prf;
ANALYZE public.idtg_idn_tgs;
ANALYZE public.idsc_idn_sct;
ANALYZE public.ent_entites;
ANALYZE public.entg_ent_tgs;
ANALYZE public.iden_idn_ent;
ANALYZE public.sts_sites;
ANALYZE public.sttg_sts_tgs;
ANALYZE public.ppr_parties_prenantes;
ANALYZE public.pptg_ppr_tgs;
ANALYZE public.cmp_campagnes;
ANALYZE public.act_activites;
ANALYZE public.rut_redemarrage_utilisateurs;
ANALYZE public.acfr_act_frn;
ANALYZE public.actg_act_tgs;
ANALYZE public.cmst_cmp_sts;
ANALYZE public.acst_act_sts;
ANALYZE public.nim_niveaux_impact;
ANALYZE public.tim_types_impact;
ANALYZE public.mim_matrice_impacts;
ANALYZE public.ppac_ppr_act;
ANALYZE public.dma_dmia_activite;
ANALYZE public.cmen_cmp_ent;
ANALYZE public.pac_plan_actions;
ANALYZE public.app_applications;
ANALYZE public.scap_sct_app;
ANALYZE public.acap_act_app;
ANALYZE public.aptg_app_tgs;

-- =====================================================
-- FIN DU SCRIPT
-- =====================================================