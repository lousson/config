Lousson: Config
===============

[![Build Status](https://travis-ci.org/lousson/config.png?branch=master,0.4)]
(https://travis-ci.org/lousson/config)

The `lousson/Lousson_Config` package provides flexible facilities for
application configuration and setup - in various formats.


Dependencies
------------

The lousson/config package itself only depends on PHP and a(ny) PSR-0
compatible autoload implementation:

- **PHP 5.3.0+**:                           http://www.php.net/
- **PSR-0 Autoloader**:                     http://pear.phix-project.org/
- **Lousson_Record**:                       http://pear.lousson.org/

However, there is also a bunch of tools the development and build
processes rely on, e.g.:

- **Git 1.7+**:                             http://www.git-scm.com/
- **Phing 2.4+**:                           http://www.phing.info/
- **Phix 0.15.0+**:                         http://www.phix-project.org/
- **PHPUnit 3.6+**:                         http://www.phpunit.de/
- **Pirum 1.1.4+**:                         http://pirum.sensiolabs.org/

Please note that The Lousson Project does NOT provide support for any of
the aforementioned software!


Resources
---------

The Lousson packages are available through the PEAR channel at
http://pear.lousson.org - thus, one can use the "pear" script to
install any of them, e.g.:

	pear channel-discover pear.lousson.org
	pear install lousson/Lousson_Config

The complete sourcecode and version history is avialabe at GitHub.
One may either visit http://github.com/lousson/config or clone
the source tree directly:

	git clone https://github.com/lousson/config.git

GitHub is also used to track issues like bugs and feature-requests:

	http://github.com/lousson/config/issues

Pull requests and other contributions are welcome!

Example
-------

The CallbackSQL* classes rely on a SQL compatible data storage with
existing data tables, which comply with a certain pattern. The following
SQL statements serve the purpose of providing CREATE TABLE statements
for exemplary use.

    CREATE  TABLE config.member_config (
        id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
        option_name enum('sedo.marketplace.bcp.rows_per_page') NOT NULL,
        entity_id mediumint(10) UNSIGNED NULL COMMENT 'Refers to MEMBER.Id',
        option_value VARCHAR(255) NOT NULL ,
        created DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
        changed TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
        PRIMARY KEY (id) ,
        UNIQUE KEY `member_config__uk_option_name_entity_id` (`option_name`,`entity_id`),
        INDEX member_config__k_entity_id (entity_id)
    )   ENGINE = InnoDB;

    # Table to store the history of deleted and updated records
    CREATE TABLE member_config_history (
    	    id int(10) unsigned NOT NULL AUTO_INCREMENT,
	    member_config_id int(10) unsigned NOT NULL,
	    option_name enum('sedo.marketplace.bcp.rows_per_page') NOT NULL,
	    entity_id mediumint(10) unsigned DEFAULT NULL COMMENT 'Refers to MEMBER.Id',
	    option_value varchar(255) NOT NULL,
	    created datetime NOT NULL,
	    changed timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	    PRIMARY KEY (id),
	    KEY member_config_history__k_member_config_id (member_config_id)
    )  ENGINE=InnoDB DEFAULT CHARSET=latin1;

    # Trigger to fill the 'created' field
    DELIMITER $$
    CREATE TRIGGER trg_member_config_before_insert BEFORE INSERT ON member_config
          FOR EACH ROW
              BEGIN
          IF NEW.created = '0000-00-00 00:00:00' THEN SET NEW.created = NOW();
      END IF;
          END$$


    DELIMITER ;

    # Trigger to insert the record into history table 'member_config_history' for deleted records
    DELIMITER $$
    CREATE TRIGGER trg_member_config_after_delete AFTER DELETE ON member_config
         FOR EACH ROW
         BEGIN
         INSERT INTO member_config_history ( member_config_id, option_name,entity_id,option_value,created ) VALUES (OLD.id, OLD.option_name,OLD.entity_id,OLD.option_value,OLD.created);
         END $$
    DELIMITER ;

    # Trigger to insert the record into history table 'member_config_history' for updated records
    DELIMITER $$
    CREATE TRIGGER trg_member_config_after_update AFTER UPDATE ON member_config
         FOR EACH ROW
         BEGIN
         INSERT INTO member_config_history ( member_config_id, option_name,entity_id,option_value,created ) VALUES (OLD.id, NEW.option_name,NEW.entity_id,NEW.option_value,NEW.created);
         END $$
    DELIMITER ;


Copyright & License
-------------------

Unless denoted otherwise, the following terms apply to all software
provided within the `lousson/Lousson_Config` package:

	Copyright (c) 2012 - 2013, The Lousson Project

	All rights reserved.

	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions
	are met:

	1) Redistributions of source code must retain the above copyright
	   notice, this list of conditions and the following disclaimer.
	2) Redistributions in binary form must reproduce the above copyright
	   notice, this list of conditions and the following disclaimer in
	   the documentation and/or other materials provided with the
	   distribution.

	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
	"AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
	LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
	FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
	COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,
	INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
	(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
	SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
	HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
	STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
	ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
	OF THE POSSIBILITY OF SUCH DAMAGE.

Please note that the creators of the software mentioned in the
"Dependencies" section define their own licensing terms & conditions!


