--
-- Auteur  : Pierre-Luc MARY
-- Date    : 2023-12-04
-- Package : MySecDash
--

--
-- Création du rôle de connexion "mysecdash_u".
--
CREATE ROLE mysecdash_u
   WITH LOGIN
   PASSWORD 'SCRAM-SHA-256$4096:Rm5klwzwFgWbC9EcNBo9fA==$b1Skuzct2C5rExNbPbbfBgvAmlNL2GJJSbY93rYnv8Q=:lER3aDy7SFxop0jVxdb2bR30yjeA9qGMgJCAI5I410c='
   INHERIT REPLICATION SUPERUSER CREATEDB CREATEROLE ;
