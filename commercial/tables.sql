-- CREATE TABLE webcommercial_permissions_revue ( id INT NOT NULL AUTO_INCREMENT,
--    	     				       Revue_id INT NOT NULL,
-- 					       User_id INT NOT NULL,
-- 					       DateAcces DATE NOT NULL,
-- 					       Autorisation TINYINT,
-- 					       PRIMARY KEY (id) );

-- CREATE TABLE webcommercial_permissions_client ( id INT NOT NULL AUTO_INCREMENT,
--    	     				       	Client_id INT NOT NULL,
-- 					       	User_id INT NOT NULL,
-- 					       	DateAcces DATE NOT NULL,
-- 					       	Autorisation TINYINT,
-- 					       	PRIMARY KEY (id) );

-- CREATE TABLE webcommercial_commentaire ( Commentaire_id INT NOT NULL AUTO_INCREMENT,
--        	     			         Commentaire VARCHAR(512) NOT NULL,
-- 					 Auteur VARCHAR(32) NOT NULL,
-- 					 Date DATE NOT NULL,
-- 					 Client_id INT NOT NULL,
-- 					 Revue_id INT NOT NULL,
-- 					 Prochaine_relance DATE,
-- 					 Contact_id VARCHAR(4),
-- 					 Fichier VARCHAR(128),
-- 					 DernierCom TINYINT,
-- 					 Acceptee TINYINT,
-- 					 PRIMARY KEY (Commentaire_id) );

CREATE TABLE webcommercial_multiacces ( id INT NOT NULL AUTO_INCREMENT,
       	     			      	User_id INT NOT NULL,
					Acces_id INT NOT NULL,
					DateAcces DATE NOT NULL,
					PRIMARY KEY (id) );

CREATE TABLE webcommercial_client_revue ( id INT NOT NULL AUTO_INCREMENT,
        	     			  Revue_id INT NOT NULL,
					  Client_id INT NOT NULL,
					  Gerant_id INT,
				    	  PRIMARY KEY (id) );

-- CREATE TABLE webcommercial_client ( id INT NOT NULL AUTO_INCREMENT,
--        	     			    DateCreation DATE NOT NULL,
-- 				    -- TypeClient VARCHAR(2),
-- 				    NomSociete VARCHAR(64) NOT NULL,
--        	     			    Addr1 VARCHAR(128),
-- 				    Addr2 VARCHAR(128),
-- 				    CP VARCHAR(8) NOT NULL,
-- 				    Ville VARCHAR(64),
-- 				    Pays VARCHAR(32),
-- 				    TelSociete VARCHAR(64),
-- 				    -- RaisonSocial VARCHAR(64),
--     				    SIRET VARCHAR(16),
-- 				    CodeAPE VARCHAR(8),
-- 				    Createur VARCHAR(32),
-- 				    PRIMARY KEY (id) );

-- CREATE TABLE webcommercial_contact ( id INT NOT NULL AUTO_INCREMENT,
-- 				     Client_id INT NOT NULL,
-- 			   	     Nom VARCHAR(32) NOT NULL,
-- 				     Prenom VARCHAR(32),
-- 				     NumTelephone1 VARCHAR(64),
-- 				     AdresseMail1 VARCHAR(128),
-- 				     NumTelephone2 VARCHAR(64),
-- 				     AdresseMail2 VARCHAR(128),
-- 				     Fonction VARCHAR(128),
-- 				     PRIMARY KEY (id) );
