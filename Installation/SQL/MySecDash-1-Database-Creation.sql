--
-- Auteur  : Pierre-Luc MARY
-- Date    : 2023-12-04
-- Package : MySecDash
--
-- Commentaire :
-- Ce script crée la base de données "mysecdash" avec l'utilisateur "mysecdash_u" comme propriétaire

--
-- Création de la base de données "mysecdash".
--
CREATE DATABASE mysecdash
  WITH OWNER = mysecdash_u
       ENCODING = UTF8
       TABLESPACE = pg_default
       LOCALE = 'fr_FR.UTF-8'
	   TEMPLATE = 'template0'
       CONNECTION LIMIT = -1;