create table christmas_participants (
	id int NOT NULL AUTO_INCREMENT,
	_year int NOT NULL,
	first_name varchar(155) NOT NULL,
	last_name varchar(155) NOT NULL,
	email varchar(155) NOT NULL,
	blocked BOOLEAN NOT NULL DEFAULT FALSE,
	register_pot datetime NOT NULL,
	ip varchar(15) NOT NULL,
	PRIMARY KEY (id)
);

create table christmas_days (
	_year int NOT NULL,
	_day int NOT NULL,
	first_name varchar(155) DEFAULT NULL,
	last_name varchar(155) DEFAULT NULL,
	email varchar(155) DEFAULT NULL,
	PRIMARY KEY (_year, _day)
);

INSERT INTO christmas_days (_year, _day) VALUES (2015, 1);
INSERT INTO christmas_days (_year, _day) VALUES (2015, 2);
INSERT INTO christmas_days (_year, _day) VALUES (2015, 3);
INSERT INTO christmas_days (_year, _day) VALUES (2015, 4);
INSERT INTO christmas_days (_year, _day) VALUES (2015, 5);
INSERT INTO christmas_days (_year, _day) VALUES (2015, 6);
INSERT INTO christmas_days (_year, _day) VALUES (2015, 7);
INSERT INTO christmas_days (_year, _day) VALUES (2015, 8);
INSERT INTO christmas_days (_year, _day) VALUES (2015, 9);
INSERT INTO christmas_days (_year, _day) VALUES (2015, 10);
INSERT INTO christmas_days (_year, _day) VALUES (2015, 11);
INSERT INTO christmas_days (_year, _day) VALUES (2015, 12);
INSERT INTO christmas_days (_year, _day) VALUES (2015, 13);
INSERT INTO christmas_days (_year, _day) VALUES (2015, 14);
INSERT INTO christmas_days (_year, _day) VALUES (2015, 15);
INSERT INTO christmas_days (_year, _day) VALUES (2015, 16);
INSERT INTO christmas_days (_year, _day) VALUES (2015, 17);
INSERT INTO christmas_days (_year, _day) VALUES (2015, 18);
INSERT INTO christmas_days (_year, _day) VALUES (2015, 19);
INSERT INTO christmas_days (_year, _day) VALUES (2015, 20);
INSERT INTO christmas_days (_year, _day) VALUES (2015, 21);
INSERT INTO christmas_days (_year, _day) VALUES (2015, 22);
INSERT INTO christmas_days (_year, _day) VALUES (2015, 23);
INSERT INTO christmas_days (_year, _day) VALUES (2015, 24);