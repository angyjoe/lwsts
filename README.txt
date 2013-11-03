To set up the database for the system, execute the following SQL in your DBMS. The system has been tested on CentOS with Apache/2.2.15, MySQL/5.1.61 and PHP/5.3.3. The default admin login is admin@boolean.in // changeme.

CREATE DATABASE sticketsystem;
USE sticketsystem;

/* Create user table. */
CREATE TABLE user (
uid INT(10) PRIMARY KEY NOT NULL AUTO_INCREMENT,
email VARCHAR(64) UNIQUE NOT NULL,
password VARCHAR(64) NOT NULL,
salt VARCHAR(8) NOT NULL,
userlevel INT(1) NOT NULL
) ENGINE=InnoDB;

/* Create userdata table, with foreign key (user, cascade). */
CREATE TABLE userdata (
uid INT(10) PRIMARY KEY NOT NULL AUTO_INCREMENT,
name VARCHAR(64),
phone VARCHAR(16),
country VARCHAR(32),
state VARCHAR(32),
address VARCHAR(128),
postal INT(8),
company VARCHAR(64),
website VARCHAR(64),
city VARCHAR(64),
INDEX(uid),
FOREIGN KEY (uid) 
REFERENCES user(uid)
ON UPDATE CASCADE
ON DELETE CASCADE
) ENGINE=InnoDB;

/* Create table for password reset tokens, with foreign key (user, cascade). */
CREATE TABLE passwordreset (
email VARCHAR(64) PRIMARY KEY NOT NULL,
utoken INT(64) NOT NULL,
INDEX(email),
FOREIGN KEY (email) 
REFERENCES user(email)
ON UPDATE CASCADE
ON DELETE CASCADE
) ENGINE=InnoDB;

/* Create ticket table, with foreign key (user, cascade). */
CREATE TABLE ticket (
tid INT(10) PRIMARY KEY NOT NULL AUTO_INCREMENT,
uid INT(10) NOT NULL,
message TEXT(10240) NOT NULL,
tstamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
edittstamp TIMESTAMP,
subject VARCHAR(32) NOT NULL,
product VARCHAR(32) NOT NULL,
department VARCHAR(32) NOT NULL,
status VARCHAR(16) NOT NULL default 'Open',
INDEX(uid),
FOREIGN KEY (uid) 
REFERENCES user(uid)
ON UPDATE CASCADE
ON DELETE CASCADE
) ENGINE=InnoDB;

/* Create ticket table, with foreign keys (user, cascade) (ticket, cascade). */
CREATE TABLE reply (
rid INT(10) PRIMARY KEY NOT NULL AUTO_INCREMENT,
tid INT(10) NOT NULL,
uid INT(10) NOT NULL,
message TEXT(1024) NOT NULL,
tstamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
INDEX(tid),
INDEX(uid),
FOREIGN KEY (tid)
REFERENCES ticket(tid)
ON UPDATE CASCADE
ON DELETE CASCADE,
FOREIGN KEY (uid)
REFERENCES user(uid)
ON UPDATE CASCADE
ON DELETE CASCADE
) ENGINE=InnoDB;

/* Create product table. */
CREATE TABLE product (
product VARCHAR(32) PRIMARY KEY NOT NULL
) ENGINE=InnoDB;

/* Create department table. */
CREATE TABLE department (
department VARCHAR(32) PRIMARY KEY NOT NULL
) ENGINE=InnoDB;

/* Create an admin user with the temporary password "changeme". */
INSERT INTO user (email, password, salt, userlevel) 
VALUES ('admin@boolean.in', 'e2086916f63851cad840c344e3cae0dd8f1058285b25a3db558fd8f999ff92c4', '2447cd19', 3);

INSERT INTO userdata (uid, name)
VALUES (1, 'admin');