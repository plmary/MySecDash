--
-- Auteur  : Pierre-Luc MARY
-- Date    : 2024-04-07
-- Package : MySecDash
--
-- Commentaire :
-- Ce script insère les données de base utile au démarrage de l'application "loxense".
--


INSERT INTO prs_parametres_systeme (prs_nom, prs_type, prs_valeur, prs_commentaire, prs_groupe, prs_super_admin) VALUES
('authentification_type', 2, 'D', 'Type authentification', 'connexion', FALSE),
('expiration_time', 1, '10', 'Nombre de minutes avant expiration', 'connexion', FALSE),
('default_password', 2, '1CouRouCouCou!', 'Mot de passe par défaut', 'connexion', FALSE),
('account_lifetime', 1, '12', 'Nombre de mois avant désactivation de l identité', 'connexion', FALSE),
('max_attempt', 1, '10', 'Nombre maximum de tentative de connexion', 'connexion', FALSE),
('min_password_size', 1, '12', 'Taille minimum d''un mot de passe', 'connexion', FALSE),
('password_complexity', 1, '3', 'Complexité demandée à la création d un mot de passe', 'connexion', FALSE),
('root_alternative_boot', 0, 'TRUE', 'authentification alternative au "root"', 'connexion', TRUE),
('ldap_ip_address', 2, 'localhost', 'Adresse IP du serveur LDAP', 'connexion', TRUE),
('ldap_ip_port', 1, '10389', 'Port IP du serveur LDAP', 'connexion', TRUE),
('ldap_protocol_version', 1, '3', '', 'connexion', TRUE),
('ldap_organization', 2, 'dc=loxense,dc=fr', 'Précise l''organisation du RDN du LDAP', 'connexion', TRUE),
('ldap_rdn_prefix', 2, 'uid', 'Précise le préfixe du RDN du LDAP', 'connexion', TRUE),
('ldap_ssl', 0, 'TRUE', 'Appel LDAP en SSL', 'connexion', TRUE),

('language_alert', 2, 'fr', 'Langue dans laquelle seront affichées les alertes', 'alerte', FALSE),
('syslog_alert', 0, 'TRUE', 'Alerter par syslog', 'alerte', TRUE),
('syslog_host', 2, 'localhost', 'Serveur sur lequel on réplique le flux SYSLOG', 'alerte', TRUE),
('syslog_port', 1, '514', 'Serveur sur lequel on réplique le flux SYSLOG', 'alerte', TRUE),
('syslog_template', 2, 'template_syslog.tmpl', 'Fichier squelette d''un message SYSLOG', 'alerte', TRUE),
('mail_alert', 0, 'FALSE', 'Alerter par courriel', 'alerte', TRUE),
('mail_title', 2, 'MySecDash : événement', 'Standardisation du sujet des courriels d''alert', 'alerte', TRUE),
('mail_body_type', 2, 'HTML', 'Format des courriels d''alert (HTML ou TEXT)', 'alerte', TRUE),
('mail_template', 2, 'template_mail.tmpl', 'Fichier squelette du corps du courriel d''alert', 'alerte', TRUE),
('mail_sender', 2, 'loxense@societe.com', 'Emetteur courriel', 'alerte', TRUE),
('mail_receiver', 2, 'supervision@societe.com', 'Destinataire courriel', 'alerte', TRUE),

('limitation_entites', 1, '0', 'Nombre maximum d''Entités dans MySecDash', 'limitations', TRUE),
('limitation_civilites', 1, '0', 'Nombre maximum de Civilités dans MySecDash', 'limitations', TRUE),
('limitation_profils', 1, '9', 'Nombre maximum de Profils dans MySecDash', 'limitations', TRUE),
('limitation_utilisateurs', 1, '0', 'Nombre maximum d''Utilisateurs dans MySecDash', 'limitations', TRUE),

('limitation_cartographies', 1, '0', 'Nombre maximum de Cartographies dans MySecDash', 'limitations', TRUE),
('limitation_actifs_primordiaux', 1, '0', 'Nombre maximum d''Actifs Primodiaux dans MySecDash', 'limitations', TRUE),
('limitation_actifs_supports', 1, '0', 'Nombre maximum d''Actifs Supports dans MySecDash', 'limitations', TRUE),
('limitation_evenements_redoutes', 1, '0', 'Nombre maximum d''Evénements Redoutés dans MySecDash', 'limitations', TRUE);


--
-- Contenu de la Civilité de base.
--

INSERT INTO cvl_civilites (cvl_id, cvl_nom, cvl_prenom) VALUES
(1, 'MySecDash', 'Administrateur');


SELECT pg_catalog.setval('cvl_civilites_cvl_id_seq', 1, true);


--
-- Création de la Société de Base.
--

INSERT INTO sct_societes (sct_id, sct_nom, sct_description) VALUES
(1, 'Loxense', 'Société par défaut');


SELECT pg_catalog.setval('sct_societes_sct_id_seq', 1, true);


--
-- Contenu de l'Entité de Base.
--

INSERT INTO ent_entites (ent_id, sct_id, ent_nom) VALUES
(1, 1, 'PLM');


SELECT pg_catalog.setval('ent_entites_ent_id_seq', 1, true);


--
-- Contenu de l'Identité de base. Mot de passe par défaut : "Welcome !"
--

INSERT INTO idn_identites (
idn_id, sct_id, cvl_id, idn_login, idn_authentifiant, idn_grain_sel, idn_changer_authentifiant, idn_super_admin, idn_tentative, idn_desactiver, idn_derniere_connexion, idn_date_expiration, idn_date_modification_authentifiant) VALUES(
1, 1, 1, 'root', 'f6f3b63150785a187a6f425db0b07d34d46642b34a892d493b758bdefd2ceafa', 'Azdser23ddAAXx', false, true, 0, false, CURRENT_TIMESTAMP, '2030-01-01 00:00:00', CURRENT_TIMESTAMP);


SELECT pg_catalog.setval('idn_identites_idn_id_seq', 1, true);


--
-- Création du Profil de base.
--

INSERT INTO prf_profils (prf_id, prf_libelle) VALUES
(1, 'Admin. Système' ),
(2, 'Gest. BIA');


SELECT pg_catalog.setval('prf_profils_prf_id_seq', 2, true);


--
-- Création du lien entre le Profil de base et l'Identité de base.
--

INSERT INTO idpr_idn_prf (idn_id, prf_id) VALUES
(1, 1);


--
-- Création des Droits.
--

INSERT INTO drt_droits (drt_id, drt_code_libelle) VALUES
(1, 'RGH_1'), --'Lecture'
(2, 'RGH_2'), --'Ecriture'
(3, 'RGH_3'), --'Modification'
(4, 'RGH_4'); --'Suppression'


SELECT pg_catalog.setval('drt_droits_drt_id_seq', 4, true);


--
-- Création des Types d'applications
--

INSERT INTO tap_types_application (tap_id, tap_code_libelle) VALUES
(1, 'TAP_1'), -- Interactive
(2, 'TAP_2'); -- Batch


SELECT pg_catalog.setval('tap_types_application_tap_id_seq', 2, true);


--
-- Création des applications.
--

INSERT INTO ain_applications_internes (ain_id, tap_id, ain_libelle, ain_localisation) VALUES
(1, 1, 'Ecran Principal MySecDash', 'MySecDash-Principal.php'),
(2, 1, 'Consultation de l''historique', 'MySecDash-Historiques.php'),

-- *** Administration globales
(3, 1, 'Gestion des Sociétés', 'MySecDash-Societes.php'),
(4, 1, 'Gestion des Entités', 'MySecDash-Entites.php'),
(5, 1, 'Gestion des Civilités', 'MySecDash-Civilites.php'),
(6, 1, 'Gestion des Utilisateurs', 'MySecDash-Utilisateurs.php'),
(7, 1, 'Gestion des Profils', 'MySecDash-Profils.php'),
(8, 1, 'Gestion des Applications Internes', 'MySecDash-ApplicationsInternes.php'),
(9, 1, 'Gestion des Privilèges', 'MySecDash-Privileges.php'),
(10, 1, 'Paramétrage interne de MySecDash', 'MySecDash-Parametres.php'),

-- *** Gestion des BIA
(11, 1, 'Gestion de la Matrice des Impacts', 'MyContinuity-MatriceImpacts.php'),
(12, 1, 'Gestion des Echelles de Temps', 'MyContinuity-EchellesTemps.php'),
(13, 1, 'Gestion des Sites', 'MyContinuity-Sites.php'),
(14, 1, 'Gestion des Types de Fournisseur', 'MyContinuity-TypesFournisseur.php'),
(15, 1, 'Gestion des Fournisseurs', 'MyContinuity-Fournisseurs.php'),
(16, 1, 'Gestion des Rôles des Parties Prenantes', 'MyContinuity-RolesPartiesPrenantes.php'),
(17, 1, 'Gestion des Parties Prenantes', 'MyContinuity-PartiesPrenantes.php'),
(18, 1, 'Gestion des Applications', 'MyContinuity-Applications.php'),
(19, 1, 'Gestion des Effectifs', 'MyContinuity-Effectifs.php'),
(20, 1, 'Gestion des Campagnes', 'MyContinuity-Campagnes.php'),
(21, 1, 'Gestion des Activités', 'MyContinuity-Activites.php'),
(22, 1, 'Visualiser les BIA', 'MyContinuity-VisualiserBIA.php'),
(23, 1, 'Editer les BIA', 'MyContinuity-EditionsBIA.php'),
(24, 1, 'Validation des BIAs des Entités', 'MyContinuity-ValiderEntites.php'),
(25, 1, 'Ecran Principal MyContinuity', 'MyContinuity-Principal.php'),

-- *** Gestion transverse des TAGS, pas encore opérationnel
(26, 1, 'Gestion des étiquettes (tags)', 'MySecDash-Etiquettes.php'),

(27, 1, 'Comparateur de DMIA sur Activités', 'MyContinuity-ComparateurDMIAActivites.php'),

(28, 1, 'Gestion des Libellés du Référentiel', 'MySecDash-LibellesReferentiel.php');


SELECT pg_catalog.setval('ain_applications_internes_ain_id_seq', 28, true);

--(9,  1, 'Consultation des Matrices des Risques', 'MySecDash-MatricesRisques.php'),
--(10, 1, 'Gestion de l''Appréciation des Risques', 'MySecDash-AppreciationRisques.php'),
--(11, 1, 'Gestion des Actifs Primordiaux', 'MySecDash-ActifsPrimordiaux.php'),
--(12, 1, 'Gestion des Actifs Supports', 'MySecDash-ActifsSupports.php'),
--(13, 1, 'Gestion des Cartographies', 'MySecDash-CartographiesRisques.php'),
--(14, 1, 'Gestion des Critères de Valorisation des Actifs', 'MySecDash-CriteresValorisationActifs.php'),
--(15, 1, 'Gestion des Critères d''Appréciation et d''Acceptation des Risques', 'MySecDash-CriteresAppreciationAcceptationRisques.php'),
--(16, 1, 'Gestion des Evénements Redoutés', 'MySecDash-EvenementsRedoutes.php'),
--(17, 1, 'Gestion des Impacts Generiques', 'MySecDash-ImpactsGeneriques.php'),
--(18, 1, 'Gestion des Menaces Génériques', 'MySecDash-MenacesGeneriques.php'),
--(19, 1, 'Gestion des Mesures de MySecDash', 'MySecDash-MesuresGeneriques.php'),
--(20, 1, 'Gestion des Risques Generiques', 'MySecDash-RisquesGeneriques.php'),
--(21, 1, 'Gestion des Référentiels de Conformité', 'MySecDash-ReferentielsConformite.php'),
--(22, 1, 'Gestion des Sources de Menaces', 'MySecDash-SourcesMenaces.php'),
--(23, 1, 'Gestion des Type d''Actif Support', 'MySecDash-TypesActifSupport.php'),
--(24, 1, 'Gestion des Types de Menace Générique', 'MySecDash-TypesMenaceGenerique.php'),
--(25, 1, 'Gestion des Types de Traitement des Risques', 'MySecDash-TypesTraitementRisques.php'),
--(26, 1, 'Gestion des éditions des Risques', 'MySecDash-EditionsRisques.php'),
--(27, 1, 'Gestion du Traitement des Risques', 'MySecDash-TraitementRisques.php'),
--(28, 1, 'Gestion des Equipes de Gestionnaires', 'MySecDash-Gestionnaires.php'),
--(29, 1, 'Gestion des Vulnérabilités Génériques', 'MySecDash-VulnerabilitesGeneriques.php'),
--(30, 1, 'Gestion des Actions', 'MySecDash-Actions.php'),
--(31, 1, 'Gestion des Editions des Actions', 'MySecDash-EditionsActions.php'),

--(32, 1, 'Gestion des Actifs Primordiaux par Tags', 'MySecDash-ActifsPrimordiauxTags.php'),
--(33, 1, 'Gestion des Actifs Supports par Tags', 'MySecDash-ActifsSupportsTags.php'),
--(34, 1, 'Gestion de l''Appréciation des Risques par Tags', 'MySecDash-AppreciationRisquesTags.php'),
--(35, 1, 'Gestion du Traitement des Risques par Tags', 'MySecDash-TraitementRisquesTags.php'),
--(36, 1, 'Gestion des Etiquettes', 'MySecDash-Etiquettes.php'),
--(37, 1, 'Gestion des Imports et Exports de Base', 'MySecDash-ExportBase.php'),
--(38, 1, 'Gestion des Types de Critère de Valorisation des Risques', 'MySecDash-TypesCritereValorisationRisques.php'),

--(39, 1, 'Gestion des Grilles d''Impact', 'MySecDash-GrillesImpacts.php'),
--(40, 1, 'Gestion des Grilles de Vraisemblances', 'MySecDash-GrillesVraisemblances.php'),
--(41, 1, 'Gestion de la Conformité', 'MySecDash-Conformite.php'),
--(42, 1, 'Gestion des Editions de Conformité', 'MySecDash-EditionConformite.php'),
--(43, 1, 'Gestion des Objectifs Visés', 'MySecDash-ObjectifsVises.php'),
--(44, 1, 'Gestion des Sources de Risque', 'MySecDash-SourcesRisques.php'),

--(45, 1, 'Gestion du Référentiel des Actifs Supports', 'MySecDash-ReferentielActifsSupports.php'),
--(46, 1, 'Gestion du Référentiel des Actifs Primordiaux', 'MySecDash-ReferentielActifsPrimordiaux.php'),



--
-- Création du lien entre les Ecrans et les Profil d'utilisateurs et leurs droits
--

INSERT INTO caa_controle_acces_application_interne (prf_id, ain_id, drt_id) VALUES
-- Profil 1 = Admin. Système
(1, 1, 1),
(1, 2, 1),
(2, 1, 1),
(2, 2, 1),

(1, 3, 1),
(1, 3, 2),
(1, 3, 3),
(1, 3, 4),
(1, 4, 1),
(1, 4, 2),
(1, 4, 3),
(1, 4, 4),
(1, 5, 1),
(1, 5, 2),
(1, 5, 3),
(1, 5, 4),
(1, 6, 1),
(1, 6, 2),
(1, 6, 3),
(1, 6, 4),
(1, 7, 1),
(1, 7, 2),
(1, 7, 3),
(1, 7, 4),
(1, 8, 1),
(1, 8, 2),
(1, 8, 3),
(1, 8, 4),
(1, 9, 1),
(1, 9, 2),
(1, 9, 3),
(1, 9, 4),
(1, 10, 1),
(1, 10, 2),
(1, 10, 3),
(1, 10, 4),
(1, 25, 1),
(1, 25, 2),
(1, 25, 3),
(1, 25, 4),

-- Profil 2 = Gest. BIA
(2, 4, 1),
(2, 4, 2),
(2, 4, 3),
(2, 4, 4),
(2, 11, 1),
(2, 11, 2),
(2, 11, 3),
(2, 11, 4),
(2, 12, 1),
(2, 12, 2),
(2, 12, 3),
(2, 12, 4),
(2, 13, 1),
(2, 13, 2),
(2, 13, 3),
(2, 13, 4),
(2, 14, 1),
(2, 14, 2),
(2, 14, 3),
(2, 14, 4),
(2, 15, 1),
(2, 15, 2),
(2, 15, 3),
(2, 15, 4),
(2, 16, 1),
(2, 16, 2),
(2, 16, 3),
(2, 16, 4),
(2, 17, 1),
(2, 17, 2),
(2, 17, 3),
(2, 17, 4),
(2, 18, 1),
(2, 18, 2),
(2, 18, 3),
(2, 18, 4),
(2, 19, 1),
(2, 19, 2),
(2, 19, 3),
(2, 19, 4),
(2, 20, 1),
(2, 20, 2),
(2, 20, 3),
(2, 20, 4),
(2, 21, 1),
(2, 21, 2),
(2, 21, 3),
(2, 21, 4),
(2, 22, 1),
(2, 22, 2),
(2, 22, 3),
(2, 22, 4),
(2, 23, 1),
(2, 23, 2),
(2, 23, 3),
(2, 23, 4),
(2, 24, 1),
(2, 24, 3),
(2, 27, 1);


--
-- Création des Types d'Action traçables dans l'outil.
--

INSERT INTO tpa_types_action (tpa_code_libelle) VALUES
('ATP_CNX'), --'connexion'
('ATP_DCNX'), --'Déconnexion'
('ATP_CHG_MDP'), --'Changer de mot de passe'
('ATP_LECTURE'), --'Lecture'
('ATP_ECRITURE'), --'Ecriture'
('ATP_MODIFICATION'), --'Modification'
('ATP_SUPPRESSION'), --'Suppression'
('ATP_DUPLICATION'), --'Duplication'
('ATP_GENERATION'), -- Génération
('ATP_ALERTE'), -- Alert
('ATP_VALIDATION'); -- Validation

SELECT pg_catalog.setval('tpa_types_action_tpa_id_seq', 11, true);


--
-- Création des Types d'Objet traçables dans l'outil.
--

INSERT INTO tpo_types_objet (tpo_code_libelle) VALUES
('OTP_PARAMETRE'), --'Parametre'
('OTP_LIBELLE_INTERNE'), -- 'Libellé interne'
('OTP_REF_INTERNE'), -- 'Référentiel interne'
('OTP_ENTITE'), -- 'Entité'
('OTP_CIVILITE'), -- 'Civilité'
('OTP_IDENTITE'), -- 'Identité'
('OTP_PROFIL'), -- 'Profil'
('OTP_CTRL_ACCES'), -- 'Contrôle d'accès'
('OTP_APPLICATION'), -- 'Application'
('OTP_DROIT'), -- Droit interne (Profil sur Application)
('OTP_TYPE_ACTIF_SUPPORT'), -- Type d'actif support
('OTP_TYPE_MENACE_GENERIQUE'), -- Type de Menace Générique
('OTP_MENACE_GENERIQUE'), -- Menace Générique
('OTP_IMPACT_GENERIQUE'), -- Impact Générique
('OTP_VULNERABILITE_GENERIQUE'), -- Vulnérabilité Générique
('OTP_SOURCE_MENACE'), -- Source de Menace
('OTP_RISQUE_GENERIQUE'), -- Risque Générique
('OTP_MESURE_GENERIQUE'), -- Mesure de MySecDash
('OTP_CARTOGRAPHIE_RISQUES'), -- Cartographie des risques de MySecDash
('OTP_CRITERE_VALORISATION'), -- Critere de valorisation des actifs de MySecDash
('OTP_CRITERE_APPRECIATION'), -- Critere d'appréciation et d'acceptaion des risques de MySecDash
('OTP_ACTIF_PRIMORDIAL'), -- Actif primordial de MySecDash
('OTP_ACTIF_SUPPORT'), -- Actif support de MySecDash
('OTP_EVENEMENT_REDOUTE'), -- Evénement redouté de MySecDash
('OTP_APPRECIATION_RISQUE'), -- Appréciation des risques de MySecDash
('OTP_TYPE_TRAITEMENT_RISQUE'), -- Type de traitement des risques de MySecDash
('OTP_TRAITEMENT_RISQUE'), -- Traitement des risques de MySecDash
('OTP_GESTIONNAIRE'), -- Equipe des Gestionnaires
('OTP_ACTION'), -- Action de sécurité de MySecDash
('OTP_TAG'), -- Etiquette ou Tag
('OTP_INFORMATION_COMPLEMENTAIRE'),
('OTP_REFERENTIEL'),
('OTP_OBJECTIF_REFERENTIEL'),
('OTP_THEME_REFERENTIEL'),
('OTP_MESURE_REFERENTIEL'),
('OTP_MESURE_CONFORMITE'),
('OTP_SOURCE_RISQUE'),
('OTP_OBJECTIF_VISE'),
('OTP_PARTIE_PRENANTE'),
('OTP_SOCIETE'),
('OTP_CAMPAGNE'),
('OTP_NIVEAU_IMPACT'),
('OTP_TYPE_IMPACT'),
('OTP_MATRICE_IMPACT'),
('OTP_DESCRIPTION_IMPACT'),
('OTP_ACTIVITE'),
('OTP_TACHE'),
('OTP_FOURNISSEUR'),
('OTP_ECHELLE_TEMPS'),
('OTP_SITE'),
('OTP_EFFECTIF'),
('OTP_SECURITE');

SELECT pg_catalog.setval('tpo_types_objet_tpo_id_seq', 52, true);


--
-- Création d'une campagne par défaut
--

INSERT INTO cmp_campagnes (cmp_id, sct_id, idn_id, cmp_date) VALUES
(1, 1, 1, '2024-02-15');

SELECT pg_catalog.setval('cmp_campagnes_cmp_id_seq', 1, true);


--
-- Création des Types d'Impact pour la Campagne par défaut
--

INSERT INTO tim_types_impact (tim_id, cmp_id, tim_poids, tim_nom_code) VALUES
(1, 1, 1, 'Financier'),
(2, 1, 2, 'Organisationnel'),
(3, 1, 3, 'Juridique / Règlementaire'),
(4, 1, 4, 'Image');

SELECT pg_catalog.setval('tim_types_impact_tim_id_seq', 4, true);


--
-- Création des Niveaux d'Impact pour la Campagne par défaut
--

INSERT INTO nim_niveaux_impact (nim_id, cmp_id, nim_numero, nim_poids, nim_nom_code, nim_couleur) VALUES
(1, 1, 1, 1, 'Faible', '27AE60'),
(2, 1, 2, 2, 'Notable', 'F1C40F'),
(3, 1, 3, 3, 'Grave', 'E67E22'),
(4, 1, 4, 4, 'Vital', 'C0392B');

SELECT pg_catalog.setval('nim_niveaux_impact_nim_id_seq', 4, true);


--
-- Création de la Matrice d'Impact pour la Campagne par défaut
--

INSERT INTO mim_matrice_impacts (cmp_id, nim_id, tim_id, mim_description) VALUES
(1, 1, 1, '<ul><li>Perte inférieure ou égale à 5% de CA</li></ul>
<p>Les impacts financiers sont faibles et pourront être facilement rattrapés/compensés ? Oui</p>'),
(1, 1, 2, '<ul><li>Faibles nuisances à l''activité sans impact sur tiers (clients, financeurs, prestataires, fournisseurs…) ni sur les autres service de l''Entreprise.</li></ul>
<p>L''incident peut-il provoquer l''arrêt du service ? Non.</p>'),
(1, 1, 3, '<ul><li>Absence d''éligibilité à une action civile ou pénale ou à une action réglementaire, mais recours amiable (Mise en demeure, acte extrajudiciaire etc…) de la part d''un cocontractant ou d''un tiers.</li></ul>
<p>L''incident entraîne-t-il des conséquences juridiques ? Non</p>'),
(1, 1, 4, '<ul><li>Pas de médiatisation mais mécontentement possible de parties prenantes internes.</li>
<li>Faible atteinte (moins de 5%) à la satisfaction des tiers (clients, financeurs, prestataires, fournisseurs,  relations publiques…).</li></ul>
<p>L''incident va-t-il circuler en interne ? Nécessite-t-il une communication interne spécifique ? Oui</p>'),

(1, 2, 1, '<ul></li>Perte 5% et 10% de CA.</li></ul>
<p>Les impacts financier sont significatifs mais n''entraîneront pas de conséquences sur le long terme ? Oui</p>'),
(1, 2, 2, '<ul><li>Nuisances organisationnelles internes à l''activité entraînant la perturbation de service  pour une ou plusieurs catégories de tiers (clients, financeurs, prestataires, fournisseurs…) mais n''impactant pas d''autres services de l''Entreprise.</li></ul>
<p>L''incident peut-il provoquer l''arrêt du service ? Oui</p>
<p>Peut-il impacter d''autres services ? Non</p>'),
(1, 2, 3, '<ul><li>Contravention ou Exposition à des poursuites civiles limitées.</li>
<li>Recommandations des autorités de tutelle.</li></ul>
<p>L''incident peut impliquer des conséquences juridiques limitées ? Oui</p>'),
(1, 2, 4, '<ul><li>Exposition à un risque de mention négative ponctuelle sur un média ou réseau social à faible audience, ayant peu d’impact ou éloigné de notre cœur de métier.</li>
<li>Atteinte (entre 6% et 20%) à la satisfaction des tiers (clients, financeurs, prestataires, fournisseurs, relations publiques…) avec une rémédiation rapide et satisfaisante.</li></ul>
<p>L''incident dépasse-t-il la sphère interne ? Peut-il être relayé sur les réseaux sociaux ? Par la presse locale (faible audience) ? Oui</p>
<p>A-t-il un impact au niveau de l''image vis-à-vis de ses partenaires, clients ? Non</p>'),

(1, 3, 1, '<ul><li>Perte 10% et 20% de CA.</li></ul>
<p>Les impacts financier sont importants et des conséquences sur le long terme sont à anticiper ? Oui</p>'),
(1, 3, 2, '<ul><li>Nuisances organisationnelles internes à l''activité entraînant la perturbation de service pour une ou plusieurs catégories de tiers  (clients, financeurs, prestataires, fournisseurs…) - ces nuisances peuvent avoir des impacts et entraîner l''arrêt d''autres services de l''Entreprise.</li></ul>
<p>L''incident peut-il provoquer l''arrêt du service ? Oui</p>
<p>Peut-il impacter d''autres services ? Oui</p>'),
(1, 3, 3, '<ul><li>Exposition à une condamnation civile significative, résiliation de contrat non stratégique.</li>
<li>Délit ou manquement à une norme juridique faisant encourir des sanctions financière significative.</li>
<li>Avertissement ou blâme des autorités de tutelle.</li></ul>
<p>L''incident nécessite-t-il la convocation du représentant légal ? Oui</p>
<p>L''impact juridique peut--il être important ? cf ci-dessous Oui</p>
<p>La conséquence juridique de l''incident peut-elle amener à la fermeture de l''entité ou à la condamnation de ses dirigeants ? Non</p>'),
(1, 3, 4, '<ul><li>Constatation de montée de réclamations via les réseaux légérement supérieure à la normale et exposition à un risque de mentions fréquentes ou récurrentes et dont le contenu ne valorise pas l''image de l''Entreprise, ou la dégrade peu,</li>
<li>Exposition à un risque de mentions négatives dans la presse spécialisée dans notre coeur d''activité,</li>
<li>Volume significatif de réclamations (plus de 20%) de la part de tiers  (clients, financeurs, prestataires, fournisseurs, …) et/ou réclamations récurrentes que l''on ne parvient pas à remédier.</li></ul>
<p>L''incident peut êre relayé par la presse nationale ? Oui</p>
<p>Entraîne-t-il des pertes clients importantes / réclamations importantes de clients ? Oui</p>
<p>Nécessite-t-il la mise en place d''une cellule de communication ? Non</p>
<p>Faut-il prévoir des sorties médiatiques des dirigeants ? La publication de communiqués de presse ? Non</p>'),

(1, 4, 1, '<ul><li>Perte supérieure à 30% de CA</li></ul>
<p>Les impacts financiers peuvent remettre en cause la pérennité de l''entité ? Oui</p>'),
(1, 4, 2, '<ul><li>Arrêt total de l''activité.</li></ul>
<p>L''incident peut-il provoquer l''arrêt total des activités de l''Entreprise ? Oui</p>'),
(1, 4, 3, '<ul><li>Condamnation pénale de l''entreprise : exclusion des marchés publics, amendes pénales > 1M€</li>
<li>Mise en jeu de la responsabilité pénale du Dirigeant avec interdiction d''exercer certaines activités ou peine d''emprisonnement</li>
<li>Retrait d''agrément ou habilitation</li></ul>
<p>La conséquence juridique de l''incident peut-elle amener à la fermeture de l''entité ou à la condamnation de ses dirigeants ? Oui</p>'),
(1, 4, 4, '<ul><li>Crise médiatique relayée par l''ensemble des canaux de communication (réseaux sociaux compris) portant durablement atteinte à l''image ou la réputation de l''Entreprise.<li>
<li>Défiance des tiers  (clients, financeurs, prestataires, fournisseurs…), impact sur un large public, au-delà des parties prenantes habituelles.</li></ul>
<p>L''incident peut être relayé par la presse nationale ? Oui</p>
<p>Entraîne-t-il des pertes lecteurs/clients importantes ? Oui</p>
<p>Nécessite-t-il la mise en place d''une cellule de communication ? Oui</p>
<p>Faut-il prévoir des sorties médiatiques des dirigeants ? La publication de communiqués de presse ? Oui</p>');


--
-- Chargement d'une échelle de temps type.
--

INSERT INTO ete_echelle_temps (cmp_id, ete_poids, ete_nom_code) VALUES
(1, 1, '4 heures'),
(1, 2, '1 jour'),
(1, 3, '2 jours'),
(1, 4, '3 jours'),
(1, 5, '1 semaine'),
(1, 6, '2 semaines'),
(1, 7, '1 mois');

SELECT pg_catalog.setval('ete_echelle_temps_ete_id_seq', 7, true);


--
-- Types de Fournisseur.
--

INSERT INTO tfr_types_fournisseur (tfr_nom_code) VALUES
('Prestataire'),
('Editeur de Solutions'),
('On Premise'),
('Infrastructure as a Service (IaaS)'),
('Platform as a Service (PaaS)'),
('Software as a Service (SaaS)');

SELECT pg_catalog.setval('tfr_types_fournisseur_tfr_id_seq', 6, true);


--
-- Chargement de quelques Type de Fournisseurs génériques
--

INSERT INTO frn_fournisseurs (frn_id, tfr_id, frn_nom, frn_description) VALUES
('1','2','Apple',''),
('2','2','Microsoft',''),
('3','2','Salesforce',''),
('4','2','CSSF',''),
('5','2','JASE Reporting Solutions',''),
('6','2','AMF',''),
('7','2','LSEG',''),
('8','2','Fircosoft',''),
('9','2','FIS',''),
('10','2','Bloomberg',''),
('11','2','OneWealthPlace',''),
('12','2','Softfluent',''),
('13','2','WeeFin',''),
('14','2','TightVNC',''),
('15','2','FileZilla',''),
('16','2','Tradeweb Market Inc.',''),
('17','2','Trade Smart',''),
('18','2','CDC',''),
('19','2','Bank ok New York',''),
('20','2','BGL',''),
('21','2','Fundinfo',''),
('22','2','YooZ',''),
('23','2','BNPP',''),
('24','2','IZNES',''),
('25','2','Walters Kluwer',''),
('26','2','Docusign',''),
('27','2','4TPM',''),
('28','2','Atlassian',''),
('29','2','StoneBranch',''),
('30','2','TeamViewer',''),
('31','2','Ninite',''),
('32','2','Sage Group','')
;

SELECT pg_catalog.setval('frn_fournisseurs_frn_id_seq', 32, true);


--
-- Chargement de quelques Applications génériques
--

INSERT INTO app_applications (app_id, frn_id, app_nom, app_hebergement, app_niveau_service, app_description, sct_id) VALUES
('1','2','Azure','Cloud','','Solution de Cloud de Microsoft',NULL),
('2','2','Office 365','SaaS','','Suite bureautique (messagerie, tableur, traitement de texte...)',NULL),
('3','3','Salesforce','SaaS','','Outil de CRM',NULL),
('4','4','LUXE TRUST','SaaS','','',NULL),
('5','5','JASE','SaaS','','',NULL),
('6','6','ROSA','SaaS','','',NULL),
('7','7','Worldcheck refinitiv','SaaS','','',NULL),
('8','8','Fircosoft','SaaS','','',NULL),
('9','9','Fis Protegent','SaaS','','',NULL),
('10','10','Bloomberg Vault','SaaS','','',NULL),
('11','11','Air PM','PaaS','','','2'),
('12','12','Phoenix','PaaS','','',NULL),
('13','10','Bloomberg VNC','SaaS','','',NULL),
('14','13','WeeFin','SaaS','','',NULL),
('15','14','TightVNC','On Premise','','',NULL),
('16','15','FileZilla','On Premise','','',NULL),
('17','16','Tradeweb','SaaS','','',NULL),
('18','17','Trading Screen','SaaS','','',NULL),
('19','18','CDCNet','SaaS','','',NULL),
('20','19','Nexen','SaaS','','',NULL),
('21','20','ePortfolio','SaaS','','',NULL),
('22','21','Adjuto','SaaS','','',NULL),
('23','22','YooZ','SaaS','','',NULL),
('24','11','Horizon Distrib','SaaS','','',NULL),
('25','23','Neolink / Europagode','SaaS','','',NULL),
('26','24','Iznes','SaaS','','',NULL),
('27','25','Legisway','SaaS','','',NULL),
('28','26','Docusign','SaaS','','',NULL),
('29','27','Patio','SaaS','','',NULL),
('30','2','SQL Server Management Studio','On Premise','','',NULL),
('31','2','Visual Studio','On premise','','',NULL),
('32','28','BitBucket','SaaS','','',NULL),
('33','28','Jira / Confluence','SaaS','','',NULL),
('34','29','StoneBranch','On Premise','','',NULL),
('35','30','TeamViewer','On Premise','','',NULL),
('36','31','Ninite Pro','On Premise','','',NULL),
('37','2','MDT','On Premise','','',NULL),
('38','2','Intune','On Premise','','',NULL),
('39','2','Power BI / Report Server','On Premise','','',NULL),
('40', '32','Sage 100 Cloud','','','',NULL)
;

SELECT pg_catalog.setval('app_applications_app_id_seq', 40, true);
