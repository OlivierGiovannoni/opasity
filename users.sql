CREATE TABLE webcontrat_utilisateurs ( id INT AUTO_INCREMENT PRIMARY KEY,
       	      	    			      username VARCHAR(128) NOT NULL,
					      passwordhash VARCHAR(1024) NOT NULL,
					      email VARCHAR(128) NOT NULL,
					      fname VARCHAR(64) NOT NULL,
					      lname VARCHAR(128) NOT NULL,
					      created DATETIME NOT NULL,
					      lastLogin DATETIME NOT NULL,
					      superuser TINYINT );
