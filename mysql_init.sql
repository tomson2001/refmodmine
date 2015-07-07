CREATE DATABASE `rmmaas` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;

create table site_stats (
	_date date NOT NULL,
	ip varchar(15) NOT NULL,
	email varchar(155),
	site varchar(55) NOT NULL,
	clicks int NOT NULL,
	PRIMARY KEY (_date, ip, site)
);

create table action_stats (
	_date date NOT NULL,
	pot datetime NOT NULL,
	ip varchar(15) NOT NULL,
	email varchar(155),
	action varchar(55) NOT NULL,
	session_id varchar(55) NOT NULL,
	PRIMARY KEY (pot, ip)
);

create view view_stats_users_per_day (day, users, avg_clicks) as (
	SELECT _date, count(ip), round(avg(clicks)) FROM site_stats GROUP BY _date
);

create view view_stats_actions_per_day (day, users, different_action_calls, action_calls) as (
	SELECT _date, count(distinct ip), count(distinct action), count(action) FROM action_stats GROUP BY _date
);