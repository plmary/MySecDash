--
-- Auteur  : Pierre-Luc MARY
-- Date    : 2017-03-23
-- Package : Loxense
--
-- Commentaire :
-- Ce script insère le référentiel ISO-27002 dans Loxense
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

SET search_path = public, pg_catalog;


-- =======================================================
-- Création des mesures génériques et des libellés associés.

INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (1, 'MGR_5.1.1');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (2, 'MGR_5.1.2');

INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (3, 'MGR_6.1.1');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (4, 'MGR_6.1.2');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (5, 'MGR_6.1.3');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (6, 'MGR_6.1.4');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (7, 'MGR_6.1.5');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (8, 'MGR_6.2.1');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (9, 'MGR_6.2.2');

INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (10, 'MGR_7.1.1');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (11, 'MGR_7.1.2');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (12, 'MGR_7.2.1');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (13, 'MGR_7.2.2');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (14, 'MGR_7.2.3');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (15, 'MGR_7.3.1');

INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (16, 'MGR_8.1.1');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (17, 'MGR_8.1.2');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (18, 'MGR_8.1.3');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (19, 'MGR_8.1.4');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (20, 'MGR_8.2.1');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (21, 'MGR_8.2.2');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (22, 'MGR_8.2.3');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (23, 'MGR_8.3.1');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (24, 'MGR_8.3.2');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (25, 'MGR_8.3.3');

INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (26, 'MGR_9.1.1');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (27, 'MGR_9.1.2');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (28, 'MGR_9.2.1');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (29, 'MGR_9.2.2');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (30, 'MGR_9.2.3');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (31, 'MGR_9.2.4');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (32, 'MGR_9.2.5');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (33, 'MGR_9.2.6');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (34, 'MGR_9.3.1');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (35, 'MGR_9.4.1');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (36, 'MGR_9.4.2');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (37, 'MGR_9.4.3');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (38, 'MGR_9.4.4');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (39, 'MGR_9.4.5');

INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (40, 'MGR_10.1.1');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (41, 'MGR_10.1.2');

INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (42, 'MGR_11.1.1');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (43, 'MGR_11.1.2');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (44, 'MGR_11.1.3');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (45, 'MGR_11.1.4');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (46, 'MGR_11.1.5');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (47, 'MGR_11.1.6');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (48, 'MGR_11.2.1');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (49, 'MGR_11.2.2');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (50, 'MGR_11.2.3');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (51, 'MGR_11.2.4');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (52, 'MGR_11.2.5');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (53, 'MGR_11.2.6');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (54, 'MGR_11.2.7');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (55, 'MGR_11.2.8');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (56, 'MGR_11.2.9');

INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (57, 'MGR_12.1.1');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (58, 'MGR_12.1.2');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (59, 'MGR_12.1.3');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (60, 'MGR_12.1.4');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (61, 'MGR_12.2.1');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (62, 'MGR_12.3.1');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (63, 'MGR_12.4.1');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (64, 'MGR_12.4.2');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (65, 'MGR_12.4.3');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (66, 'MGR_12.4.4');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (67, 'MGR_12.5.1');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (68, 'MGR_12.6.1');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (69, 'MGR_12.6.2');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (70, 'MGR_12.7.1');

INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (71, 'MGR_13.1.1');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (72, 'MGR_13.1.2');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (73, 'MGR_13.1.3');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (74, 'MGR_13.2.1');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (75, 'MGR_13.2.2');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (76, 'MGR_13.2.3');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (77, 'MGR_13.2.4');

INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (78, 'MGR_14.1.1');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (79, 'MGR_14.1.2');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (80, 'MGR_14.1.3');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (81, 'MGR_14.2.1');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (82, 'MGR_14.2.2');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (83, 'MGR_14.2.3');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (84, 'MGR_14.2.4');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (85, 'MGR_14.2.5');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (86, 'MGR_14.2.6');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (87, 'MGR_14.2.7');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (88, 'MGR_14.2.8');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (89, 'MGR_14.2.9');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (90, 'MGR_14.3.1');

INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (91, 'MGR_15.1.1');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (92, 'MGR_15.1.2');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (93, 'MGR_15.1.3');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (94, 'MGR_15.2.1');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (95, 'MGR_15.2.2');

INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (96, 'MGR_16.1.1');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (97, 'MGR_16.1.2');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (98, 'MGR_16.1.3');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (99, 'MGR_16.1.4');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (100, 'MGR_16.1.5');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (101, 'MGR_16.1.6');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (102, 'MGR_16.1.7');

INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (103, 'MGR_17.1.1');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (104, 'MGR_17.1.2');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (105, 'MGR_17.1.3');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (106, 'MGR_17.2.1');

INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (107, 'MGR_18.1.1');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (108, 'MGR_18.1.2');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (109, 'MGR_18.1.3');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (110, 'MGR_18.1.4');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (111, 'MGR_18.1.5');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (112, 'MGR_18.2.1');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (113, 'MGR_18.2.2');
INSERT INTO mgr_mesures_generiques (mgr_id, mgr_code) VALUES (114, 'MGR_18.2.3');

SELECT pg_catalog.setval('mgr_mesures_generiques_mgr_id_seq', 114, true);


INSERT INTO lbr_libelles_referentiel (lbr_code, lng_id, lbr_libelle) VALUES
('MGR_5.1.1', 'fr', 'Politiques de sécurité de l''information - Il convient de définir un ensemble de politiques en matière de sécurité de l''information qui soit approuvé par la Direction, diffusé et communiqué aux salariés et aux tiers concernés.'),
('MGR_5.1.2', 'fr', 'Revue des politiques de sécurité de l''information - Pour garantir la constance de la pertinence, de l''adéquation et de l''efficacité des politiques liées à la sécurité de l''information, il convient de revoir ces politiques à intervalles programmés ou en cas de changements majeurs.'),

('MGR_6.1.1', 'fr', 'Fonctions et responsabilités liées à la sécurité de l''information - Il convient de définir et d''attribuer toutes les responsabilités en matière de sécurité de l''information.'),
('MGR_6.1.2', 'fr', 'Séparation des tâches - Il convient de séparer les tâches et les domaines de responsabilité incompatibles pour limiter les possibilités de modification ou de mauvais usage, non autorisé(e) ou involontaire, des actifs de l''organisation.'),
('MGR_6.1.3', 'fr', 'Relations avec les autorités - Il convient d''entretenir des relations appropriées avec les autorités compétentes.'),
('MGR_6.1.4', 'fr', 'Relations avec des groupes de travail spécialisés - Il convient d''entretenir des relations appropriées avec des groupes d''intérêt, des forums spécialisés dans la sécurité et des associations professionnelles.'),
('MGR_6.1.5', 'fr', 'La sécurité de l''information dans la gestion de projet - Il convient de traiter la sécurité de l''information dans la gestion de projet, quel que soit le type de projet concerné.'),
('MGR_6.2.1', 'fr', 'Politique en matière d''appareils mobiles - Il convient d''adopter une politique et des mesures de sécurité complémentaires pour gérer les risques.'),
('MGR_6.2.2', 'fr', 'Télétravail - Il convient de mettre en œuvre une politique et des mesures de sécurité complémentaires pour protéger.'),

('MGR_7.1.1', 'fr', 'Sélection des candidats - Il convient que des vérifications des informations concernant tous les candidats à l''embauche soient réalisées conformément ,ux lois, aux règlements et à l''éthique, et il convient qu''elles soient proportionnelles aux exigences métier, à la classification des informations accessibles et aux risques identifiés.'),
('MGR_7.1.2', 'fr', 'Termes et conditions d''embauche - Il convient que les accords contractuels conclus avec les salariés et les contractants déterminent leurs responsabilités et celles de l''organisation en matière de sécurité de l''information.'),
('MGR_7.2.1', 'fr', 'Responsabilités de la direction - Il convient que la direction demande à tous les salariés et contractants d''appliquer les règles de sécurité conformément aux politiques et aux procédures en vigueur dans l''organisation.'),
('MGR_7.2.2', 'fr', 'Sensibilisation, apprentissage et formation à la sécurité de l''information - Il convient que l''ensemble des salariés de l''organisation et, le cas échéant, les contractants suivent un apprentissage et des formations de sensibilisation adaptés et qu''ils reçoivent régulièrement les mises à jour des politiques et procédures de l''organisation s''appliquant à leurs fonctions.'),
('MGR_7.2.3', 'fr', 'Processus disciplinaire - Il convient qu''il existe un processus disciplinaire formel et connu de tous pour prendre des mesures à l''encontre des salariés ayant enfreint les règles liées à la sécurité de l''information.'),
('MGR_7.3.1', 'fr', 'Achèvement ou modification des responsabilités associées au contrat de travail - Il convient de définir les responsabilités et les missions liées à la sécurité de l''information qui restent valables à l''issue de la rupture, du terme ou de la modification du contrat de travail, d''en informer le salarié ou le contractant et de veiller à leur application.'),

('MGR_8.1.1', 'fr', 'Inventaire des actifs - Il convient d''identifier les actifs associés à l''information et aux moyens de traitement de l''information et de dresser et tenir à jour un inventaire de ces actifs.'),
('MGR_8.1.2', 'fr', 'Propriété des actifs - Il convient que les actifs figurant à l''inventaire aient un propriétaire.'),
('MGR_8.1.3', 'fr', 'Utilisation correcte des actifs - Il convient d''identifier, de documenter et de mettre en œuvre des règles d''utilisation correcte de l''information, des actifs associés à l''information et des moyens de traitement de l''information.'),
('MGR_8.1.4', 'fr', 'Restitution des actifs - Il convient que tous les salariés et utilisateurs tiers restituent la totalité des actifs de l''organisation qu''ils ont en leur possession au terme de la période d''emploi, du contrat ou de l''accord.'),
('MGR_8.2.1', 'fr', 'Classification des informations - Il convient de classer les informations en termes de valeur, d''exigences légales, de sensibilité ou de leur caractère critique pour l''entreprise.'),
('MGR_8.2.2', 'fr', 'Marquage des informations - Il convient d''élaborer et de mettre en œuvre un ensemble approprié de procédures pour le marquage de l''information, conformément au plan de classification de l''information adopté par l''organisation.'),
('MGR_8.2.3', 'fr', 'Manipulation des actifs - Il convient d''élaborer et de mettre en œuvre des procédures de traitement des actifs, conformément au plan de classification de l''information adopté par l''organisation.'),
('MGR_8.3.1', 'fr', 'Gestion des supports amovibles - Il convient de mettre en œuvre des procédures de gestion des supports amovibles conformément au plan de classification adopté par l''organisation.'),
('MGR_8.3.2', 'fr', 'Mise au rebut des supports - Il convient de procéder à une mise au rebut sécurisée des supports qui ne servent plus, en suivant des procédures formelles.'),
('MGR_8.3.3', 'fr', 'Transfert physique des supports - Il convient de protéger les supports contenant de l''information contre les accès non autorisés, l''utilisation frauduleuse ou l''altération lors du transport.'),

('MGR_9.1.1', 'fr', 'Politique de contrôle d''accès - Il convient d''établir, de documenter et de revoir une politique du contrôle d''accès sur la base des exigences métier et de sécurité de l''information.'),
('MGR_9.1.2', 'fr', 'Accès aux réseaux et aux services en réseau - Il convient que les utilisateurs aient uniquement accès au réseau et aux services en réseau pour lesquels ils ont spécifiquement reçu une autorisation.'),
('MGR_9.2.1', 'fr', 'Enregistrement et désinscription des utilisateurs - Il convient de mettre en œuvre une procédure formelle d''enregistrement et de désinscription des utilisateurs destinée à permettre l''attribution de droits d''accès.'),
('MGR_9.2.2', 'fr', 'Maîtrise de la gestion des accès utilisateur - Il convient de mettre en œuvre un processus formel de maîtrise de la gestion des accès utilisateur pour attribuer ou révoquer des droits d''accès à tous les types d''utilisateurs de tous les systèmes et de tous les services d''information.'),
('MGR_9.2.3', 'fr', 'Gestion des privilèges d''accès - Il convient de restreindre et de contrôler l''attribution et l''utilisation des privilèges d''accès.'),
('MGR_9.2.4', 'fr', 'Gestion des informations secrètes d''authentification des utilisateurs - Il convient que l''attribution des informations secrètes d''authentification soit réalisée dans le cadre d''un processus de gestion formel.'),
('MGR_9.2.5', 'fr', 'Revue des droits d''accès utilisateur - Il convient que les propriétaires d''actifs revoient les droits d''accès des utilisateurs à intervalles réguliers.'),
('MGR_9.2.6', 'fr', 'Suppression ou adaptation des droits d''accès - Il convient que les droits d''accès de l''ensemble des salariés et utilisateurs tiers à l''information et aux moyens de traitement de l''information soient supprimés à la fin de leur période d''emploi, ou adaptés en cas de modification du contrat ou de l''accord.'),
('MGR_9.3.1', 'fr', 'Utilisation d''informations secrètes d''authentification - Il convient d''exiger des utilisateurs des informations secrètes d''authentification qu''ils appliquent les pratiques de l''organisation en la matière.'),
('MGR_9.4.1', 'fr', 'Restriction d''accès à l''information - Il convient de restreindre l''accès à l''information et aux fonctions d''application système conformément à la politique de contrôle d''accès.'),
('MGR_9.4.2', 'fr', 'Sécuriser les procédures de connexion - Lorsque la politique de contrôle d''accès l''exige, il convient que l''accès aux systèmes et aux applications soit contrôlé par une procédure de connexion sécurisée.'),
('MGR_9.4.3', 'fr', 'Système de gestion des mots de passe - Il convient que les systèmes qui gèrent les mots de passe soient interactifs et fournissent des mots de passe de qualité.'),
('MGR_9.4.4', 'fr', 'Utilisation de programmes utilitaires à privilèges - Il convient de limiter et de contrôler étroitement l''utilisation des programmes utilitaires permettant de contourner les mesures de sécurité d''un système ou d''une application.'),
('MGR_9.4.5', 'fr', 'Contrôle d''accès au code source des programmes - Il convient de restreindre l''accès au code source des programmes.'),

('MGR_10.1.1', 'fr', 'Politique d''utilisation des mesures cryptographiques - Il convient d''élaborer et de mettre en œuvre une politique d''utilisation de mesures cryptographiques en vue de protéger l''information.'),
('MGR_10.1.2', 'fr', 'Gestion des clés - Il convient d''élaborer et de mettre en œuvre tout au long de leur cycle de vie une politique sur l''utilisation, la protection et la durée de vie des clés cryptographiques.'),

('MGR_11.1.1', 'fr', 'Périmètre de sécurité physique - Il convient de définir des périmètres de sécurité servant à protéger les zones contenant l''information sensible ou critique et les moyens de traitement de l''information.'),
('MGR_11.1.2', 'fr', 'Contrôles physiques des accès - Il convient de protéger les zones sécurisées par des contrôles adéquats à l''entrée pour s''assurer que seul le personnel autorisé est admis.'),
('MGR_11.1.3', 'fr', 'Sécurisation des bureaux, des salles et des équipements - Il convient de concevoir et d''appliquer des mesures de sécurité physique aux bureaux, aux salles et aux équipements.'),
('MGR_11.1.4', 'fr', 'Protection contre les menaces extérieures et environnementales - Il convient de concevoir et d''appliquer des mesures de protection physique contre les désastres naturels, les attaques malveillantes ou les accidents.'),
('MGR_11.1.5', 'fr', 'Travail dans les zones sécurisées - Il convient de concevoir et d''appliquer des procédures pour le travail en zone sécurisée.'),
('MGR_11.1.6', 'fr', 'Zones de livraison et de chargement - Il convient de contrôler les points d''accès tels que les zones de livraison et de chargement et les autres points par lesquels des personnes non autorisées peuvent pénétrer dans les locaux et, si possible, de les isoler des moyens de traitement de l''information, de façon à éviter les accès non autorisés.'),
('MGR_11.2.1', 'fr', 'Emplacement et protection du matériel - Il convient de déterminer l''emplacement du matériel et de le protéger de manière à réduire les risques liés à des menaces et dangers environnementaux et les possibilités d''accès non autorisé.'),
('MGR_11.2.2', 'fr', 'Services généraux - Il convient de protéger le matériel des coupures de courant et autres perturbations dues à une défaillance des services généraux.'),
('MGR_11.2.3', 'fr', 'Sécurité du câblage - Il convient de protéger les câbles électriques ou de télécommunication transportant des données ou supportant les services d''information contre toute interception, interférence ou dommage.'),
('MGR_11.2.4', 'fr', 'Maintenance du matériel - Il convient d''entretenir le matériel correctement pour garantir sa disponibilité permanente et son intégrité.'),
('MGR_11.2.5', 'fr', 'Sortie des actifs - Il convient de ne pas sortir un matériel, des informations ou des logiciels des locaux de l''organisation sans autorisation préalable.'),
('MGR_11.2.6', 'fr', 'Sécurité du matériel et des actifs hors des locaux - Il convient d''appliquer des mesures de sécurité au matériel utilisé hors des locaux de l''organisation en tenant compte des différents risques associés au travail hors site.'),
('MGR_11.2.7', 'fr', 'Mise au rebut ou recyclage sécurisé(e) du matériel - Il convient de vérifier chacun des éléments du matériel contenant des supports de stockage pour s''assurer que toute donnée sensible a bien été supprimée et que tout logiciel sous licence a bien été désinstallé ou écrasé de façon sécurisée, avant sa mise au rebut ou sa réutilisation.'),
('MGR_11.2.8', 'fr', 'Matériel utilisateur laissé sans surveillance - Il convient que les utilisateurs s''assurent que le matériel non surveillé est doté d''une protection appropriée.'),
('MGR_11.2.9', 'fr', 'Politique du bureau propre et de l''écran vide - Il convient d''adopter une politique du bureau propre pour les documents papier et les supports de stockage amovibles, et une politique de l''écran vide pour les moyens de traitement de l''information.'),

('MGR_12.1.1', 'fr', 'Procédures d''exploitation documentées - Il convient de documenter les procédures d''exploitation et de les mettre à disposition de tous les utilisateurs concernés.'),
('MGR_12.1.2', 'fr', 'Gestion des changements - Il convient de contrôler les changements apportés à l''organisation, aux processus métier, aux systèmes et moyens de traitement de l''information qui influent sur la sécurité de l''information.'),
('MGR_12.1.3', 'fr', 'Dimensionnement - Il convient de surveiller et d''ajuster au plus près l''utilisation des ressources et il convient de faire des projections sur les dimensionnements futurs pour garantir les performances exigées du système.'),
('MGR_12.1.4', 'fr', 'Séparation des environnements de développement, de test et d''exploitation - Il convient de séparer les environnements de développement, de test et d''exploitation pour réduire les risques d''accès ou de changements non autorisés dans l''environnement en exploitation.'),
('MGR_12.2.1', 'fr', 'Mesures contre les logiciels malveillants - Il convient de mettre en œuvre des mesures de détection, de prévention et de récupération, conjuguées à une sensibilisation des utilisateurs adaptée, pour se protéger contre les logiciels malveillants.'),
('MGR_12.3.1', 'fr', 'Sauvegarde des informations - Il convient de réaliser des copies de sauvegarde de l''information, des logiciels et des images systèmes, et de les tester régulièrement conformément à une politique de sauvegarde convenue.'),
('MGR_12.4.1', 'fr', 'Journalisation des événements - Il convient de créer, de tenir à jour et de revoir régulièrement les journaux d''événements enregistrant les activités de l''utilisateur, les exceptions, les défaillances et les événements liés à la sécurité de l''information.'),
('MGR_12.4.2', 'fr', 'Protection de l''information journalisée - Il convient de protéger les moyens de journalisation et l''information journalisée contre les risques de falsification ou d''accès non autorisé.'),
('MGR_12.4.3', 'fr', 'Journaux administrateur et opérateur - Il convient de journaliser les activités de l''administrateur système et de l''opérateur système, ainsi que de protéger et de revoir régulièrement les journaux.'),
('MGR_12.4.4', 'fr', 'Synchronisation des horloges - Il convient de synchroniser les horloges de l''ensemble des systèmes de traitement de l''information concernés d''une organisation ou d''un domaine de sécurité sur une source de référence temporelle unique.'),
('MGR_12.5.1', 'fr', 'Installation de logiciels sur des systèmes en exploitation - Il convient de mettre en œuvre des procédures pour contrôler l''installation de logiciels sur des systèmes en exploitation.'),
('MGR_12.6.1', 'fr', 'Gestion des vulnérabilités techniques - Il convient d''être informé en temps voulu des vulnérabilités techniques des systèmes d''information en exploitation, d''évaluer l''exposition de l''organisation à ces vulnérabilités et de prendre les mesures appropriées pour traiter le risque associé'),
('MGR_12.6.2', 'fr', 'Restrictions liées à l''installation de logiciels - Il convient d''établir et de mettre en œuvre des règles régissant l''installation de logiciels par les utilisateurs.'),
('MGR_12.7.1', 'fr', 'Mesures relatives à l''audit des systèmes d''information - Pour réduire au minimum les perturbations subies par les processus métier, il convient de planifier avec soin et d''arrêter avec les personnes intéressées les exigences d''audit et les activités impliquant des contrôles des systèmes en exploitation.'),

('MGR_13.1.1', 'fr', 'Contrôle des réseaux - Il convient de gérer et de contrôler les réseaux pour protéger l''information contenue dans les systèmes et les applications.'),
('MGR_13.1.2', 'fr', 'Sécurité des services de réseau - Pour tous les services de réseau, il convient d''identifier les mécanismes de sécurité, les niveaux de service et les exigences de gestion, et de les intégrer dans les accords de services de réseau, que ces services soient fournis en interne ou externalisés.'),
('MGR_13.1.3', 'fr', 'Cloisonnement des réseaux - Il convient que les groupes de services d''information, d''utilisateurs et de systèmes d''information soient cloisonnés sur les réseaux.'),
('MGR_13.2.1', 'fr', 'Politiques et procédures de transfert de l''information - Il convient de mettre en place des politiques, des procédures et des mesures de transfert formelles pour protéger les transferts d''information transitant par tous types d''équipements de communication.'),
('MGR_13.2.2', 'fr', 'Accords en matière de transfert d''information - Il convient que les accords traitent du transfert sécurisé de l''information liée à l''activité entre l''organisation et les tiers.'),
('MGR_13.2.3', 'fr', 'Messagerie électronique - Il convient de protéger de manière appropriée l''information transitant par la messagerie électronique.'),
('MGR_13.2.4', 'fr', 'Engagements de confidentialité ou de non-divulgation - Il convient d''identifier, de revoir régulièrement et de documenter les exigences en matière d''engagements de confidentialité ou de non-divulgation, conformément aux besoins de l''organisation en matière de protection de l''information.'),

('MGR_14.1.1', 'fr', 'Analyse et spécification des exigences de sécurité de l''information - Il convient que les exigences liées à la sécurité de l''information figurent dans les exigences des nouveaux systèmes d''information ou des changements apportés aux systèmes existants.'),
('MGR_14.1.2', 'fr', 'Sécurisation des services d''application sur les réseaux publics - Il convient de protéger l''information liée aux services d''application transmise sur les réseaux publics contre les activités frauduleuses, les différends contractuels, ainsi que la divulgation et la modification non autorisées.'),
('MGR_14.1.3', 'fr', 'Protection des transactions liées aux services d''application - Il convient de protéger l''information impliquée dans les transactions liées aux services d''application pour empêcher une transmission incomplète, des erreurs d''acheminement, la modification non autorisée, la divulgation non autorisée, la duplication non autorisée du message ou sa réémission.'),
('MGR_14.2.1', 'fr', 'Politique de développement sécurisé - Il convient d''établir des règles de développement des logiciels et des systèmes, et de les appliquer aux développements de l''organisation.'),
('MGR_14.2.2', 'fr', 'Procédures de contrôle des changements apportés au système - Il convient de contrôler les changements apportés au système dans le cycle de développement en utilisant des procédures formelles de contrôle des changements.'),
('MGR_14.2.3', 'fr', 'Revue technique des applications après changement apporté à la plateforme d''exploitation - Lorsque des changements sont apportés aux plateformes d''exploitation, il convient de revoir et de tester les applications critiques métier afin de vérifier l''absence de tout effet indésirable sur l''activité ou sur la sécurité.'),
('MGR_14.2.4', 'fr', 'Restrictions relatives aux changements apportés aux progiciels - Il convient de ne pas encourager la modification des progiciels et de se limiter aux changements nécessaires. Il convient également d''exercer un contrôle strict sur ces changements.'),
('MGR_14.2.5', 'fr', 'Principes d''ingénérie de la sécurité des systèmes - Il convient d''établir, de documenter, de tenir à jour et d''appliquer des principes d''ingénierie de la sécurité des systèmes à tous les travaux de mise en œuvre de systèmes d''information.'),
('MGR_14.2.6', 'fr', 'Environnement de développement sécurisé - Il convient que les organisations établissent un environnement de développement sécurisé pour les tâches de développement et d''intégration du système, qui englobe l''intégralité du cycle de développement du système, et qu''ils en assurent la protection de manière appropriée.'),
('MGR_14.2.7', 'fr', 'Développement externalisé - Il convient que l''organisation supervise et contrôle l''activité de développement du système externalisé.'),
('MGR_14.2.8', 'fr', 'Phase de test de la sécurité du système - Il convient de réaliser les tests de fonctionnalité de la sécurité pendant le développement.'),
('MGR_14.2.9', 'fr', 'Test de conformité du système - Il convient de déterminer des programmes de test de conformité et des critères associés pour les nouveaux systèmes d''information, les mises à jour et les nouvelles versions.'),
('MGR_14.3.1', 'fr', 'Protection des données de test - Il convient que les données de test soient sélectionnées avec soin, protégées et contrôlées.'),

('MGR_15.1.1', 'fr', 'Politique de sécurité de l''information dans les relations avec les fournisseurs'),
('MGR_15.1.2', 'fr', 'La sécurité dans les accords conclus avec les fournisseurs - Il convient que les exigences applicables liées à la sécurité de l''information soient établies et convenues avec chaque fournisseur pouvant avoir accès, traiter, stocker, communiquer ou fournir des composants de l''infrastructure informatique destinés à l''information de l''organisation.'),
('MGR_15.1.3', 'fr', 'Chaine d''approvisionnement informatique - Il convient que les accords conclus avec les fournisseurs incluent des exigences sur le traitement des risques de sécurité de l''information associés à la chaîne d''approvisionnement des produits et des services informatiques.'),
('MGR_15.2.1', 'fr', 'Surveillance et revue des services des fournisseurs - Il convient que les organisations surveillent, revoient et auditent à intervalles réguliers la prestation des services assurés par les fournisseurs.'),
('MGR_15.2.2', 'fr', 'Gestion des changements apportés dans les services des fournisseurs - Il convient de gérer les changements effectués dans les prestations de service des fournisseurs, y compris le maintien et l''amélioration des politiques, procédures et mesures existant en matière de sécurité de l''information, en tenant compte du caractère critique de l''information, des systèmes et des processus concernés et de la réappréciation du risque.'),

('MGR_16.1.1', 'fr', 'Responsabilités et procédures - Il convient d''établir des responsabilités et des procédures permettant de garantir une réponse rapide, efficace et pertinente en cas d''incident lié à la sécurité de l''information.'),
('MGR_16.1.2', 'fr', 'Signalement des événements liés à la sécurité de l''information - Il convient de signaler, dans les meilleurs délais, les événements liés à la sécurité de l''information, par les voies hiérarchiques appropriées.'),
('MGR_16.1.3', 'fr', 'Signalement des failles liées à la sécurité de l''information - Il convient d''enjoindre tous les salariés et contractants utilisant les systèmes et services d''information de l''organisation à noter et à signaler toute faille de sécurité observée ou soupçonnée dans les systèmes ou services.'),
('MGR_16.1.4', 'fr', 'Appréciation des événements liés à la sécurité de l''information et prise de décision - Il convient d''apprécier les événements liés à la sécurité de l''information et de décider s''ils doivent être classés comme incidents liés à la sécurité de l''information.'),
('MGR_16.1.5', 'fr', 'Réponse aux incidents liés à la sécurité de l''information - Il convient de répondre aux incidents liés à la sécurité de l''information conformément aux procédures documentées.'),
('MGR_16.1.6', 'fr', 'Tirer des enseignements des incidents liés à la sécurité de l''information - Il convient de tirer parti des connaissances recueillies suite à l''analyse et la résolution des incidents liés à la sécurité de l''information pour réduire la probabilité ou les conséquences d''incidents ultérieurs.'),
('MGR_16.1.7', 'fr', 'Recueil de preuves - Il convient que l''organisation définisse et applique des procédures d''identification, de recueil, d''acquisition et de protection de l''information pouvant servir de preuve.'),

('MGR_17.1.1', 'fr', 'Organisation de la continuité de la sécurité de l''information - Il convient que l''organisation détermine ses exigences en matière de sécurité de l''information et de continuité du management de la sécurité de l''information dans des situations défavorables, comme lors d''une crise ou d''un sinistre.'),
('MGR_17.1.2', 'fr', 'Mise en œuvre de la continuité de la sécurité de l''information - Il convient que l''organisation établisse, documente, mette en œuvre et maintienne à jour des processus, des procédures et des mesures permettant de garantir le niveau requis de continuité de la sécurité de l''information au cours d''une situation défavorable.'),
('MGR_17.1.3', 'fr', 'Vérifier,revoiretévaluerlacontinuitédelasécuritédel''information - Il convient que l''organisation vérifie à intervalles réguliers les mesures de continuité de la sécurité de l''information déterminées et mises en œuvre, afin que s''assurer qu''elles restent valables et efficaces dans des situations défavorables.'),
('MGR_17.2.1', 'fr', 'Disponibilité des moyens de traitement de l''information - Il convient de mettre en œuvre des moyens de traitement de l''information avec suffisamment de redondances pour répondre aux exigences de disponibilité.'),

('MGR_18.1.1', 'fr', 'Identification de la législation et des exigences contractuelles applicables - Il convient, pour chaque système d''information et pour l''organisation elle-même, de définir, documenter et mettre à jour explicitement toutes les exigences légales, réglementaires et contractuelles en vigueur, ainsi que l''approche adoptée par l''organisation pour satisfaire à ces exigences.'),
('MGR_18.1.2', 'fr', 'Droits de propriété intellectuelle - Il convient de mettre en œuvre des procédures appropriées visant à garantir la conformité avec les exigences légales, réglementaires et contractuelles relatives aux droits de propriété intellectuelle et à l''utilisation de logiciels propriétaires.'),
('MGR_18.1.3', 'fr', 'Protection des enregistrements - Il convient de protéger les enregistrements de la perte, de la destruction, de la falsification, des accès non autorisés et des diffusions non autorisées conformément aux exigences légales, réglementaires, contractuelles et aux exigences métier.'),
('MGR_18.1.4', 'fr', 'Protection de la vie privée et protection des données à caractère personnel - Il convient de garantir la protection de la vie privée et la protection des données à caractère personnel telles que l''exigent la législation et les réglementations applicables, le cas échéant.'),
('MGR_18.1.5', 'fr', 'Réglementation relative aux mesures cryptographiques - Il convient de prendre des mesures cryptographiques conformément aux accords, lois et réglementations applicables.'),
('MGR_18.2.1', 'fr', 'Revue indépendante de la sécurité de l''information - Il convient de procéder à des revues régulières et indépendantes de l''approche retenue par l''organisation pour gérer et mettre en œuvre la sécurité de l''information (à savoir le suivi des objectifs, les mesures, les politiques, les procédures et les processus relatifs à la sécurité de l''information) à intervalles définis ou lorsque des changements importants sont intervenus.'),
('MGR_18.2.2', 'fr', 'Conformité avec les politiques et les normes de sécurité - Il convient que les responsables revoient régulièrement la conformité du traitement de l''information et des procédures dont ils sont chargés au regard des politiques, des normes de sécurité applicables et autres exigences de sécurité.'),
('MGR_18.2.3', 'fr', 'Examen de la conformité technique - Il convient que les systèmes d''information soient régulièrement revus pour vérifier leur conformité avec les politiques et les normes de sécurité de l''information de l''organisation.'),

--

('MGR_5.1.1', 'en', 'Policies for information security - A set of policies for information security should be defined, approved by management, published and communicated to employees and relevant external parties.'),
('MGR_5.1.2', 'en', 'Review of the policies for information security - The policies for information security should be reviewed at planned intervals or if significant changes occur to ensure their continuing suitability, adequacy and effectiveness.'),

('MGR_6.1.1', 'en', 'Information security roles and responsilities - All information security responsibilities should be defined and allocated.'),
('MGR_6.1.2', 'en', 'Segregation of duties - Conflicting duties and areas of responsibility should be segregated to reduce opportunities for unauthorized or unintentional modification or misuse of the organization’s assets.'),
('MGR_6.1.3', 'en', 'Contact with authorities - Appropriate contacts with relevant authorities should be maintained.'),
('MGR_6.1.4', 'en', 'Contact with special interest groups - Appropriate contacts with special interest groups or other specialist security forums and professional associations should be maintained.'),
('MGR_6.1.5', 'en', 'Information security in project management - Information security should be addressed in project management, regardless of the type of the project.'),
('MGR_6.2.1', 'en', 'Mobile device policy - A policy and supporting security measures should be adopted to manage the risks introduced by using mobile devices.'),
('MGR_6.2.2', 'en', 'Teleworking - A policy and supporting security measures should be implemented to protect information accessed, processed or stored at teleworking sites.'),

('MGR_7.1.1', 'en', 'Screening - Background verification checks on all candidates for employment should be carried out in accordance with relevant laws, regulations and ethics and should be proportional to the business requirements, the classification of the information to be accessed and the perceived risks.'),
('MGR_7.1.2', 'en', 'Terms and conditions of employment - The contractual agreements with employees and contractors should state their and the organization’s responsibilities for information security.'),
('MGR_7.2.1', 'en', 'Management responsibilities - Management should require all employees and contractors to apply information security in accordance with the established policies and procedures of the organization.'),
('MGR_7.2.2', 'en', 'Information security awareness, education and training - All employees of the organization and, where relevant, contractors should receive appropriate awareness education and training and regular updates in organizational policies and procedures, as relevant for their job function.'),
('MGR_7.2.3', 'en', 'Disciplinary process - There should be a formal and communicated disciplinary process in place to take action against employees who have committed an information security breach.'),
('MGR_7.3.1', 'en', 'Termination or change of employment responsibilities - Information security responsibilities and duties that remain valid after termination or change of employment should be defined, communicated to the employee or contractor and enforced.'),

('MGR_8.1.1', 'en', 'Inventory of assets - Assets associated with information and information processing facilities should be identified and an inventory of these assets should be drawn up and maintained.'),
('MGR_8.1.2', 'en', 'Ownership of assets - Assets maintained in the inventory should be owned.'),
('MGR_8.1.3', 'en', 'Acceptable use of assets - Rules for the acceptable use of information and of assets associated with information and information processing facilities should be identified, documented and implemented.'),
('MGR_8.1.4', 'en', 'Return of assets - All employees and external party users should return all of the organizational assets in their possession upon termination of their employment, contract or agreement.'),
('MGR_8.2.1', 'en', 'Classification of information - Information should be classified in terms of legal requirements, value, criticality and sensitivity to unauthorised disclosure or modification.'),
('MGR_8.2.2', 'en', 'Labelling of information - An appropriate set of procedures for information labelling should be developed and implemented in accordance with the information classification scheme adopted by the organization.'),
('MGR_8.2.3', 'en', 'Handling of assets - Procedures for handling assets should be developed and implemented in accordance with the information classification scheme adopted by the organization.'),
('MGR_8.3.1', 'en', 'Management of removable media - Procedures should be implemented for the management of removable media in accordance with the classification scheme adopted by the organization.'),
('MGR_8.3.2', 'en', 'Disposal of media - Media should be disposed of securely when no longer required, using formal procedures.'),
('MGR_8.3.3', 'en', 'Physical media transfer - Media containing information should be protected against unauthorized access, misuse or corruption during transportation.'),

('MGR_9.1.1', 'en', 'Access control policy - An access control policy should be established, documented and reviewed based on business and information security requirements.'),
('MGR_9.1.2', 'en', 'Access to networks and network services - Users should only be provided with access to the network and network services that they have been specifically authorized to use.'),
('MGR_9.2.1', 'en', 'User registration and de-registration - A formal user registration and de-registration process should be implemented to enable assignment of access rights.'),
('MGR_9.2.2', 'en', 'User access provisioning - A formal user access provisioning process should be implemented to assign or revoke access rights for all user types to all systems and services.'),
('MGR_9.2.3', 'en', 'Management of privileged access right - The allocation and use of privileged access rights should be restricted and controlled.'),
('MGR_9.2.4', 'en', 'Management of secret authentication information of users - The allocation of secret authentication information should be controlled through a formal management process.'),
('MGR_9.2.5', 'en', 'Review of user access rights - Asset owners should review users’ access rights at regular intervals.'),
('MGR_9.2.6', 'en', 'Removal or adjustment of access rights - The access rights of all employees and external party users to information and information processing facilities should be removed upon termination of their employment, contract or agreement, or adjusted upon change.'),
('MGR_9.3.1', 'en', 'Use of secret authentication information - Users should be required to follow the organization’s practices in the use of secret authentication information.'),
('MGR_9.4.1', 'en', 'Information access restriction - Access to information and application system functions should be restricted in accordance with the access control policy.'),
('MGR_9.4.2', 'en', 'Secure log-on procedures - Where required by the access control policy, access to systems and applications should be controlled by a secure log-on procedure.'),
('MGR_9.4.3', 'en', 'Password management system - Password management systems should be interactive and should ensure quality passwords.'),
('MGR_9.4.4', 'en', 'Use of privileged utility programs - The use of utility programs that might be capable of overriding system and application controls should be restricted and tightly controlled.'),
('MGR_9.4.5', 'en', 'Access control to program source code - Access to program source code should be restricted.'),

('MGR_10.1.1', 'en', 'Policy on the use of cryptographic controls - A policy on the use of cryptographic controls for protection of information should be developed and implemented.'),
('MGR_10.1.2', 'en', 'Key management - A policy on the use, protection and lifetime of cryptographic keys should be developed and implemented through their whole lifecycle.'),

('MGR_11.1.1', 'en', 'Physical security perimeter - Security perimeters should be defined and used to protect areas that contain either sensitive or critical information and information processing facilities.'),
('MGR_11.1.2', 'en', 'Physical entry controls - Secure areas should be protected by appropriate entry controls to ensure that only authorized personnel are allowed access.'),
('MGR_11.1.3', 'en', 'Securing offices, rooms and facilities - Physical security for offices, rooms and facilities should be designed and applied.'),
('MGR_11.1.4', 'en', 'Protecting against external and environmental threats - Physical protection against natural disasters, malicious attack or accidents should be designed and applied.'),
('MGR_11.1.5', 'en', 'Working in secure areas - Procedures for working in secure areas should be designed and applied.'),
('MGR_11.1.6', 'en', 'Delivery and loading areas - Access points such as delivery and loading areas and other points where unauthorized persons could enter the premises should be controlled and, if possible, isolated from information processing facilities to avoid unauthorized access.'),
('MGR_11.2.1', 'en', 'Equipment siting and protection - Equipment should be sited and protected to reduce the risks from environmental threats and hazards, and opportunities for unauthorized access.'),
('MGR_11.2.2', 'en', 'Supporting utilities - Equipment should be protected from power failures and other disruptions caused by failures in supporting utilities.'),
('MGR_11.2.3', 'en', 'Cabling security - Power and telecommunications cabling carrying data or supporting information services should be protected from interception, interference or damage.'),
('MGR_11.2.4', 'en', 'Equipment maintenance - Equipment should be correctly maintained to ensure its continued availability and integrity.'),
('MGR_11.2.5', 'en', 'Removal of assets - Equipment, information or software should not be taken off-site without prior authorization.'),
('MGR_11.2.6', 'en', 'Security of equipment and assets off-premises - Security should be applied to off-site assets taking into account the different risks of working outside the organization’s premises.'),
('MGR_11.2.7', 'en', 'Secure disposal or re-use of equipment - All items of equipment containing storage media should be verified to ensure that any sensitive data and licensed software has been removed or securely overwritten prior to disposal or re-use.'),
('MGR_11.2.8', 'en', 'Unattended user equipment - Users should ensure that unattended equipment has appropriate protection.'),
('MGR_11.2.9', 'en', 'Clear desk and clear screen policy - A clear desk policy for papers and removable storage media and a clear screen policy for information processing facilities should be adopted.'),

('MGR_12.1.1', 'en', 'Documented operating procedures - Operating procedures should be documented and made available to all users who need them.'),
('MGR_12.1.2', 'en', 'Change management - Changes to the organization, business processes, information processing facilities and systems that affect information security should be controlled.'),
('MGR_12.1.3', 'en', 'Capacity management - The use of resources should be monitored, tuned and projections made of future capacity requirements to ensure the required system performance.'),
('MGR_12.1.4', 'en', 'Separation of development, testing and operational environments - Development, testing, and operational environments should be separated to reduce the risks of unauthorized access or changes to the operational environment.'),
('MGR_12.2.1', 'en', 'Controls against malware - Detection, prevention and recovery controls to protect against malware should be implemented, combined with appropriate user awareness.'),
('MGR_12.3.1', 'en', 'Information backup - Backup copies of information, software and system images should be taken and tested regularly in accordance with an agreed backup policy.'),
('MGR_12.4.1', 'en', 'Event logging - Event logs recording user activities, exceptions, faults and information security events should be produced, kept and regularly reviewed.'),
('MGR_12.4.2', 'en', 'Protection of log information - Logging facilities and log information should be protected against tampering and unauthorized access.'),
('MGR_12.4.3', 'en', 'Administrator and operator logs - System administrator and system operator activities should be logged and the logs protected and regularly reviewed.'),
('MGR_12.4.4', 'en', 'Clock synchronisation - The clocks of all relevant information processing systems within an organization or security domain should be synchronised to a single reference time source.'),
('MGR_12.5.1', 'en', 'Installation of software on operational systems - Procedures should be implemented to control the installation of software on operational systems.'),
('MGR_12.6.1', 'en', 'Management of technical vulnerabilities - Information about technical vulnerabilities of information systems being used should be obtained in a timely fashion, the organization’s exposure to such vulnerabilities evaluated and appropriate measures taken to address the associated risk.'),
('MGR_12.6.2', 'en', 'Restrictions on software installation - Rules governing the installation of software by users should be established and implemented.'),
('MGR_12.7.1', 'en', 'Audit requirements and activities involving verification of operational systems should be carefully planned and agreed to minimize disruptions to business processes.'),

('MGR_13.1.1', 'en', 'Network controls - Networks should be managed and controlled to protect information in systems and applications.'),
('MGR_13.1.2', 'en', 'Security of network services - Security mechanisms, service levels and management requirements of all network services should be identified and included in network services agreements, whether these services are provided in-house or outsourced.'),
('MGR_13.1.3', 'en', 'Segregation in networks - Groups of information services, users and information systems should be segregated on networks.'),
('MGR_13.2.1', 'en', 'Information transfer policies and procedures - Formal transfer policies, procedures and controls should be in place to protect the transfer of information through the use of all types of communication facilities.'),
('MGR_13.2.2', 'en', 'Agreements on information transfer - Agreements should address the secure transfer of business information between the organization and external parties.'),
('MGR_13.2.3', 'en', 'Electronic messaging - Information involved in electronic messaging should be appropriately protected.'),
('MGR_13.2.4', 'en', 'Confidentialityornon-disclosureagreements - Requirements for confidentiality or non-disclosure agreements reflecting the organization’s needs for the protection of information should be identified, regularly reviewed and documented.'),

('MGR_14.1.1', 'en', 'Information security requirements analysis and specification - The information security related requirements should be included in the requirements for new information systems or enhancements to existing information systems.'),
('MGR_14.1.2', 'en', 'Securing application services on public networks - Information involved in application services passing over public networks should be protected from fraudulent activity, contract dispute and unauthorized disclosure and modification.'),
('MGR_14.1.3', 'en', 'Protecting application services transactions - Information involved in application service transactions should be protected to prevent incomplete transmission, mis-routing, unauthorized message alteration, unauthorized disclosure, unauthorized message duplication or replay.'),
('MGR_14.2.1', 'en', 'Secure development policy - Rules for the development of software and systems should be established and applied to developments within the organization.'),
('MGR_14.2.2', 'en', 'System change control procedures - Changes to systems within the development lifecycle should be controlled by the use of formal change control procedures.'),
('MGR_14.2.3', 'en', 'Technical review of applications after operating platform changes - When operating platforms are changed, business critical applications should be reviewed and tested to ensure there is no adverse impact on organizational operations or security.'),
('MGR_14.2.4', 'en', 'Restrictions on changes to software packages - Modifications to software packages should be discouraged, limited to necessary changes and all changes should be strictly controlled.'),
('MGR_14.2.5', 'en', 'Secure system engineering principles - Principles for engineering secure systems should be established, documented, maintained and applied to any information system implementation efforts.'),
('MGR_14.2.6', 'en', 'Secure development environment - Organizations should establish and appropriately protect secure development environments for system development and integration efforts that cover the entire system development lifecycle.'),
('MGR_14.2.7', 'en', 'Outsourced development - The organization should supervise and monitor the activity of outsourced system development.'),
('MGR_14.2.8', 'en', 'System security testing - Testing of security functionality should be carried out during development.'),
('MGR_14.2.9', 'en', 'System acceptance testing - Acceptance testing programs and related criteria should be established for new information systems, upgrades and new versions.'),
('MGR_14.3.1', 'en', 'Protection of test data - Test data should be selected carefully, protected and controlled.'),

('MGR_15.1.1', 'en', 'Information security policy for supplier relationships - Information security requirements for mitigating the risks associated with supplier’s access to the organization’s assets should be agreed with the supplier and documented.'),
('MGR_15.1.2', 'en', 'Addressing security within supplier agreements - All relevant information security requirements should be established and agreed with each supplier that may access, process, store, communicate, or provide IT infrastructure components for, the organization’s information.'),
('MGR_15.1.3', 'en', 'Information and communication technology supply chain - Agreements with suppliers should include requirements to address the information security risks associated with information and communications technology services and product supply chain.'),
('MGR_15.2.1', 'en', 'Monitoring and review of supplier services - Organizations should regularly monitor, review and audit supplier service delivery.'),
('MGR_15.2.2', 'en', 'Managing changes to supplier services - Changes to the provision of services by suppliers, including maintaining and improving existing information security policies, procedures and controls, should be managed, taking account of the criticality of business information, systems and processes involved and re-assessment of risks.'),

('MGR_16.1.1', 'en', 'Responsibilities and procedures - Management responsibilities and procedures should be established to ensure a quick, effective and orderly response to information security incidents.'),
('MGR_16.1.2', 'en', 'Reporting information security events - Information security events should be reported through appropriate management channels as quickly as possible.'),
('MGR_16.1.3', 'en', 'Reporting information security weaknesses - Employees and contractors using the organization’s information systems and services should be required to note and report any observed or suspected information security weaknesses in systems or services.'),
('MGR_16.1.4', 'en', 'Assessment of and decision on information security events - Information security events should be assessed and it should be decided if they are to be classified as information security incidents.'),
('MGR_16.1.5', 'en', 'Response to information security incidents - Information security incidents should be responded to in accordance with the documented procedures.'),
('MGR_16.1.6', 'en', 'Learning from information security incidents - Knowledge gained from analysing and resolving information security incidents should be used to reduce the likelihood or impact of future incidents.'),
('MGR_16.1.7', 'en', 'Collection of evidence - The organization should define and apply procedures for the identification, collection, acquisition and preservation of information, which can serve as evidence.'),

('MGR_17.1.1', 'en', 'Planning information security continuity - The organization should determine its requirements for information security and the continuity of information security management in adverse situations, e.g. during a crisis or disaster.'),
('MGR_17.1.2', 'en', 'Implementing information security continuity - The organization should establish, document, implement and maintain processes, procedures and controls to ensure the required level of continuity for information security during an adverse situation.'),
('MGR_17.1.3', 'en', 'Verify, review and evaluate information security continuity - The organization should verify the established and implemented information security continuity controls at regular intervals in order to ensure that they are valid and effective during adverse situations.'),
('MGR_17.2.1', 'en', 'Availability of information processing facilities - Information processing facilities should be implemented with redundancy sufficient to meet availability requirements.'),

('MGR_18.1.1', 'en', 'Identification of applicable legislation and contractual requirements - All relevant legislative statutory, regulatory, contractual requirements and the organization’s approach to meet these requirements should be explicitly identified, documented and kept up to date for each information system and the organization.'),
('MGR_18.1.2', 'en', 'Intellectual property rights - Appropriate procedures should be implemented to ensure compliance with legislative, regulatory and contractual requirements related to intellectual property rights and use of proprietary software products.'),
('MGR_18.1.3', 'en', 'Protection of records - Records should be protected from loss, destruction, falsification, unauthorized access and unauthorized release, in accordance with legislatory, regulatory, contractual and business requirements.'),
('MGR_18.1.4', 'en', 'Privacy and protection of personally identifiable information - Privacy and protection of personally identifiable information should be ensured as required in relevant legislation and regulation where applicable.'),
('MGR_18.1.5', 'en', 'Regulation of cryptographic controls - Cryptographic controls should be used in compliance with all relevant agreements, legislation and regulations.'),
('MGR_18.2.1', 'en', 'Independent review of information security - The organization’s approach to managing information security and its implementation (i.e. control objectives, controls, policies, processes and procedures for information security) should be reviewed independently at planned intervals or when significant changes occur.'),
('MGR_18.2.2', 'en', 'Compliance with security policies and standards - Managers should regularly review the compliance of information processing and procedures within their area of responsibility with the appropriate security policies, standards and any other security requirements.'),
('MGR_18.2.3', 'en', 'Technical compliance review - Information systems should be regularly reviewed for compliance with the organization’s information security policies and standards.');
