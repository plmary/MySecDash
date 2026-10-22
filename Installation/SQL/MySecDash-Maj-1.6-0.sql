--
-- Auteur  : Pierre-Luc MARY
-- Date    : 2025-12-13
-- Package : MySecDash
--
-- Commentaire :
-- Ce script fait la migration de la base v1.5 vers la base v1.6.
-- Modèle SQL : MySecDash v1.6-0




CREATE SEQUENCE public.ipp_indisponibilite_personnel_prestataire_ipp_id_seq;

CREATE TABLE public.ipp_indisponibilite_personnel_prestataire (
                ipp_id BIGINT NOT NULL DEFAULT nextval('public.ipp_indisponibilite_personnel_prestataire_ipp_id_seq'),
                act_id BIGINT NOT NULL,
                frn_id BIGINT NOT NULL,
                ete_id BIGINT NOT NULL,
                ipp_contact VARCHAR(100),
                ipp_commentaires TEXT,
                CONSTRAINT ipp_indisponibilite_personnel_prestataire_pk PRIMARY KEY (ipp_id)
);

ALTER SEQUENCE public.ipp_indisponibilite_personnel_prestataire_ipp_id_seq OWNED BY public.ipp_indisponibilite_personnel_prestataire.ipp_id;


CREATE SEQUENCE public.ipi_indisponibilite_personnel_interne_ipi_id_seq;

CREATE TABLE public.ipi_indisponibilite_personnel_interne (
                ipi_id BIGINT NOT NULL DEFAULT nextval('public.ipi_indisponibilite_personnel_interne_ipi_id_seq'),
                act_id BIGINT NOT NULL,
                ein_id BIGINT NOT NULL,
                ete_id BIGINT NOT NULL,
                ppr_id BIGINT,
                ipi_commentaires TEXT,
                CONSTRAINT ipi_indisponibilite_personnel_interne_pk PRIMARY KEY (ipi_id)
);

ALTER SEQUENCE public.ipi_indisponibilite_personnel_interne_ipi_id_seq OWNED BY public.ipi_indisponibilite_personnel_interne.ipi_id;


CREATE SEQUENCE public.pei_prestataires_vers_equipes_internes_pei_id_seq;

CREATE TABLE public.pei_prestataires_vers_equipes_internes (
                pei_id BIGINT NOT NULL DEFAULT nextval('public.pei_prestataires_vers_equipes_internes_pei_id_seq'),
                frn_id BIGINT NOT NULL,
                act_id BIGINT NOT NULL,
                ete_id BIGINT NOT NULL,
                ppr_id BIGINT NOT NULL,
                ein_id BIGINT NOT NULL,
                pei_commentaires TEXT,
                CONSTRAINT pei_prestataires_vers_equipes_internes_pk PRIMARY KEY (pei_id)
);

ALTER SEQUENCE public.pei_prestataires_vers_equipes_internes_pei_id_seq OWNED BY public.pei_prestataires_vers_equipes_internes.pei_id;


ALTER TABLE ONLY public.tgs_tags ALTER COLUMN tgs_description TYPE TEXT, ALTER COLUMN tgs_description DROP NOT NULL;


CREATE SEQUENCE public.pap_prestataires_autres_prestataires_pap_id_seq;

CREATE TABLE public.pap_prestataires_autres_prestataires (
                pap_id BIGINT NOT NULL DEFAULT nextval('public.pap_prestataires_autres_prestataires_pap_id_seq'),
                frn_id BIGINT NOT NULL,
                act_id BIGINT NOT NULL,
                ete_id BIGINT NOT NULL,
                frn_id_nouveau BIGINT NOT NULL,
                pap_contact VARCHAR(100),
                pap_commentaires TEXT,
                CONSTRAINT pap_prestataires_autres_prestataires_pk PRIMARY KEY (pap_id)
);

ALTER SEQUENCE public.pap_prestataires_autres_prestataires_pap_id_seq OWNED BY public.pap_prestataires_autres_prestataires.pap_id;


CREATE SEQUENCE public.ein_equipes_internes_ein_id_seq;

CREATE TABLE public.ein_equipes_internes (
                ein_id BIGINT NOT NULL DEFAULT nextval('public.ein_equipes_internes_ein_id_seq'),
                sct_id BIGINT NOT NULL,
                ein_nom VARCHAR(100) NOT NULL,
                ein_description TEXT,
                CONSTRAINT ein_equipes_internes_pk PRIMARY KEY (ein_id)
);

ALTER SEQUENCE public.ein_equipes_internes_ein_id_seq OWNED BY public.ein_equipes_internes.ein_id;

CREATE UNIQUE INDEX idx_u_ein_nom
 ON public.ein_equipes_internes
 ( ein_nom );


CREATE INDEX idx_pap_frn_id
 ON public.pap_prestataires_autres_prestataires
 ( frn_id_nouveau );

CREATE INDEX idx_ipi_ein_id
 ON public.ipi_indisponibilite_personnel_interne
 ( ein_id );

CREATE INDEX idx_pap_composite
 ON public.pap_prestataires_autres_prestataires
 ( frn_id, act_id );

CREATE INDEX idx_ipi_act_id
 ON public.ipi_indisponibilite_personnel_interne
 ( act_id );

CREATE INDEX idx_pap_ete_id
 ON public.pap_prestataires_autres_prestataires
 ( ete_id );

CREATE INDEX idx_ipi_ete_id
 ON public.ipi_indisponibilite_personnel_interne
 ( ete_id );

CREATE INDEX idx_pei_composite
 ON public.pei_prestataires_vers_equipes_internes
 ( frn_id, act_id );

CREATE INDEX idx_ein_sct_id
 ON public.ein_equipes_internes
 ( sct_id );

CREATE INDEX idx_pei_ete_id
 ON public.pei_prestataires_vers_equipes_internes
 ( ete_id );

CREATE INDEX idx_pei_ppr_id
 ON public.pei_prestataires_vers_equipes_internes
 ( ppr_id );

CREATE INDEX idx_pei_ein_id
 ON public.pei_prestataires_vers_equipes_internes
 ( ein_id );

CREATE INDEX idx_ipp_act_id
 ON public.ipp_indisponibilite_personnel_prestataire
 ( act_id );

CREATE INDEX idx_ipp_ete_id
 ON public.ipp_indisponibilite_personnel_prestataire
 ( ete_id );

CREATE INDEX idx_ipp_frn_id
 ON public.ipp_indisponibilite_personnel_prestataire
 ( frn_id );


ALTER TABLE public.ipp_indisponibilite_personnel_prestataire ADD CONSTRAINT act_ipp_fk
FOREIGN KEY (act_id)
REFERENCES public.act_activites (act_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.ipi_indisponibilite_personnel_interne ADD CONSTRAINT act_ipe_fk
FOREIGN KEY (act_id)
REFERENCES public.act_activites (act_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.pei_prestataires_vers_equipes_internes ADD CONSTRAINT act_pei_fk
FOREIGN KEY (act_id)
REFERENCES public.act_activites (act_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.pap_prestataires_autres_prestataires ADD CONSTRAINT act_pap_fk
FOREIGN KEY (act_id)
REFERENCES public.act_activites (act_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.ipp_indisponibilite_personnel_prestataire ADD CONSTRAINT frn_ipp_fk
FOREIGN KEY (frn_id)
REFERENCES public.frn_fournisseurs (frn_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.pei_prestataires_vers_equipes_internes ADD CONSTRAINT frn_pei_fk
FOREIGN KEY (frn_id)
REFERENCES public.frn_fournisseurs (frn_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.pap_prestataires_autres_prestataires ADD CONSTRAINT frn_pap_nouveau_fk
FOREIGN KEY (frn_id_nouveau)
REFERENCES public.frn_fournisseurs (frn_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.pap_prestataires_autres_prestataires ADD CONSTRAINT frn_pap_origine_fk
FOREIGN KEY (frn_id)
REFERENCES public.frn_fournisseurs (frn_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.ein_equipes_internes ADD CONSTRAINT sct_ein_fk
FOREIGN KEY (sct_id)
REFERENCES public.sct_societes (sct_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.ipp_indisponibilite_personnel_prestataire ADD CONSTRAINT ete_ipp_fk
FOREIGN KEY (ete_id)
REFERENCES public.ete_echelle_temps (ete_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.ipi_indisponibilite_personnel_interne ADD CONSTRAINT ete_ipi_fk
FOREIGN KEY (ete_id)
REFERENCES public.ete_echelle_temps (ete_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.pei_prestataires_vers_equipes_internes ADD CONSTRAINT ete_pei_fk
FOREIGN KEY (ete_id)
REFERENCES public.ete_echelle_temps (ete_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.pap_prestataires_autres_prestataires ADD CONSTRAINT ete_pap_fk
FOREIGN KEY (ete_id)
REFERENCES public.ete_echelle_temps (ete_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.ipi_indisponibilite_personnel_interne ADD CONSTRAINT ppr_ipi_fk
FOREIGN KEY (ppr_id)
REFERENCES public.ppr_parties_prenantes (ppr_id)
ON DELETE SET NULL
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.pei_prestataires_vers_equipes_internes ADD CONSTRAINT ppr_pei_fk
FOREIGN KEY (ppr_id)
REFERENCES public.ppr_parties_prenantes (ppr_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.ipi_indisponibilite_personnel_interne ADD CONSTRAINT ein_ipi_fk
FOREIGN KEY (ein_id)
REFERENCES public.ein_equipes_internes (ein_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.pei_prestataires_vers_equipes_internes ADD CONSTRAINT ein_pei_fk
FOREIGN KEY (ein_id)
REFERENCES public.ein_equipes_internes (ein_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;



--
-- Ajouts des nouveaux libellés
--

INSERT INTO lbr_libelles_referentiel (lng_id, lbr_code, lbr_libelle) VALUES
('fr', '__LRI_REPRISE_ACTIVITE_AUTRE_EQUIPE', 'Reprise de l''activité par une autre équipe'),
('en', '__LRI_REPRISE_ACTIVITE_AUTRE_EQUIPE', 'Resumption of activity by another team'),

('fr', '__LRI_REPRISE_ACTIVITE_PRESTATAIRE', 'Reprise de l''activité par une prestataire'),
('en', '__LRI_REPRISE_ACTIVITE_PRESTATAIRE', 'Resumption of activity by a provider'),

('fr', '__LRI_INDISPONIBILITE_PERSONNEL', 'Indisponibilité du Personnel'),
('en', '__LRI_INDISPONIBILITE_PERSONNEL', 'Resumption of activity by a provider'),

('fr', '__LRI_EQUIPE', 'Equipe'),
('en', '__LRI_EQUIPE', 'Team'),

('fr', '__LRI_EQUIPE_INTERNE', 'Equipe interne'),
('en', '__LRI_EQUIPE_INTERNE', 'Internal team'),

('fr', '__LRI_EQUIPES_INTERNES', 'Equipes internes'),
('en', '__LRI_EQUIPES_INTERNES', 'Internal teams'),

('fr', '__LRI_RESPONSABLE', 'Responsable'),
('en', '__LRI_RESPONSABLE', 'Manager'),

('fr', '__LRI_FOURNISSEUR', 'Fournisseur'),
('en', '__LRI_FOURNISSEUR', 'Supplier'),

('fr', '__LRI_CONTACT', 'Contact'),
('en', '__LRI_CONTACT', 'Contact'),

('fr', '__LRI_PRESTATAIRE', 'Prestataire'),
('en', '__LRI_PRESTATAIRE', 'Provider'),

('fr', '__LRI_MODIFICATION_REALISEE', 'Modification réalisée'),
('en', '__LRI_MODIFICATION_REALISEE', 'Modification completed'),

('fr', '__LRI_EQUIPE_INTERNE_AJOUTEE', 'Equipe Interne ajoutée'),
('en', '__LRI_EQUIPE_INTERNE_AJOUTEE', 'Internal Team added'),
('fr', '__LRI_EQUIPE_INTERNE_MODIFIEE', 'Equipe interne modifiée'),
('en', '__LRI_EQUIPE_INTERNE_MODIFIEE', 'Internal team modified'),
('fr', '__LRI_EQUIPE_INTERNE_SUPPRIMEE', 'Equipe interne supprimée'),
('en', '__LRI_EQUIPE_INTERNE_SUPPRIMEE', 'Internal team removed'),

('fr', '__LRI_PRESTATAIRE_AJOUTEE', 'Prestataire ajouté'),
('en', '__LRI_PRESTATAIRE_AJOUTEE', 'Provider added'),

('fr', '__LRI_GESTION_INDISPO_PERSONNEL_ACTIVITE', 'Gestion de l''indisponibilité du personnel pour une activité'),
('en', '__LRI_GESTION_INDISPO_PERSONNEL_ACTIVITE', 'Managing staff unavailability for an activity'),

('fr', '__LRI_GESTION_EQUIPES_INTERNES', 'Gestion des équipes internes'),
('en', '__LRI_GESTION_EQUIPES_INTERNES', 'Internal team management')

('fr', '__LRI_CONFIRMER_SUPPRESSION_EQUIPE_INTERNE', 'Confirmez-vous la suppression de l''équipe interne "%s" ?'),
('en', '__LRI_CONFIRMER_SUPPRESSION_EQUIPE_INTERNE', 'Confirm deletion of internal team "%s"?')
;