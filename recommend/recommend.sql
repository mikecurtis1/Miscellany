drop table if exists recommend;
create table items (
	items_id integer unsigned not null unique auto_increment,
	person varchar(6) not null default '??????',
	item varchar(13) not null default '#############',
	rating integer unsigned default null,
	index (items_id),
	index (person),
	index (item),  
	index (rating)
);

