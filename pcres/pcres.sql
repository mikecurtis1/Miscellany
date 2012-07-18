drop table if exists pcres;
create table pcres (
	pcres_id integer unsigned not null unique auto_increment,
	workstation varchar(16) not null default 'unknown',
	username varchar(16) not null default 'unknown',
	password varchar(16) not null default 'unknown',
	barcode varchar(14) not null default '',
	start datetime,
	stop datetime,
	last_checkin datetime, 
	status varchar(12) not null default 'unknown',
	client varchar(12) not null default 'unknown', 
	samba varchar(12) not null default 'unknown', 

	index (pcres_id),
	index (workstation),
	index (username),
	index (password),
	index (barcode),
	index (start),
	index (last_checkin), 
	index (stop),
	index (status),
	index (client),  
	index (samba)
);

