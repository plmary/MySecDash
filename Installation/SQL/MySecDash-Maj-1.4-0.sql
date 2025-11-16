--
-- Auteur  : Pierre-Luc MARY
-- Date    : 2025-10-02
-- Package : MySecDash
--
-- Commentaire :
-- Ce script réalise les mises à jour pour passer à la version 2.5-0.
--


CREATE TABLE public.scap_sct_app (
                app_id BIGINT NOT NULL,
                sct_id BIGINT NOT NULL,
                ete_id_dima_dsi BIGINT,
                ete_id_pdma_dsi BIGINT,
                cmap_description_dima TEXT,
                cmap_description_pdma TEXT,
                CONSTRAINT scap_sct_app_pk PRIMARY KEY (app_id, sct_id)
);

ALTER TABLE public.scap_sct_app ADD CONSTRAINT sct_scap_fk
FOREIGN KEY (sct_id)
REFERENCES public.sct_societes (sct_id)
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

ALTER TABLE public.scap_sct_app ADD CONSTRAINT app_scap_fk
FOREIGN KEY (app_id)
REFERENCES public.app_applications (app_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.ete_echelle_temps ADD COLUMN sct_id BIGINT;

ALTER TABLE public.ete_echelle_temps ADD CONSTRAINT sct_societes_ete_echelle_temps_fk
FOREIGN KEY (sct_id)
REFERENCES public.sct_societes (sct_id)
ON DELETE NO ACTION
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.acap_act_app ADD CONSTRAINT ete_tcap_dima_fk
FOREIGN KEY (ete_id_dima)
REFERENCES public.ete_echelle_temps (ete_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.acap_act_app ADD CONSTRAINT ete_tcap_pdma_fk
FOREIGN KEY (ete_id_pdma)
REFERENCES public.ete_echelle_temps (ete_id)
ON DELETE CASCADE
ON UPDATE NO ACTION
NOT DEFERRABLE;

ALTER TABLE public.ete_echelle_temps DROP COLUMN cmp_id;

ALTER TABLE public.app_applications ADD COLUMN app_nom_alias VARCHAR(100);

ALTER TABLE scap_sct_app RENAME COLUMN cmap_description_dima TO scap_description_dima;
ALTER TABLE scap_sct_app RENAME COLUMN cmap_description_pdma TO scap_description_pdma;

