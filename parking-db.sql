  CREATE DATABASE IF NOT EXISTS parking;

USE parking;

CREATE TABLE IF NOT EXISTS brands( 
	id int unsigned not null primary key auto_increment,
	brandName VARCHAR(55) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS colors( 
	id int unsigned not null primary key auto_increment,
	color VARCHAR(55) NOT NULL UNIQUE 
);

CREATE TABLE IF NOT EXISTS models( 
	id int unsigned not null primary key auto_increment,
	modelName VARCHAR(55) NOT null UNIQUE,
	brandId	int unsigned not null,
 	foreign key (brandId) references brands(id) on delete cascade on update cascade
);

CREATE TABLE IF NOT EXISTS spaces( 
	id int unsigned not null primary key auto_increment,
	isFree boolean default 0 NOT NULL,
	floor int NOT NULL
);


INSERT INTO spaces (isFree, floor)
SELECT 1 AS isFree, (ROW_NUMBER() OVER (ORDER BY (SELECT NULL)) - 1) DIV 10 + 1 AS floor
FROM INFORMATION_SCHEMA.COLUMNS
LIMIT 50;

CREATE TABLE IF NOT EXISTS vehicles( 
	id int not null primary key,
	licencePlate VARCHAR(55) not null,
	brandId INT UNSIGNED not null,
	colorId INT UNSIGNED not null,
	modelId INT UNSIGNED not null,
	spaceId INT UNSIGNED not null,
 	foreign key (modelId) references models(id),
 	foreign key (brandId) references brands(id),
 	foreign key (colorId) references colors(id),
 	foreign key (spaceId) references spaces(id)
);