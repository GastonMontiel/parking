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

INSERT INTO spaces (isFree)
SELECT 1 AS isFree
FROM INFORMATION_SCHEMA.COLUMNS
WHERE NOT EXISTS (SELECT 1 FROM spaces)
LIMIT 50;

CREATE TABLE IF NOT EXISTS vehicles( 
	id int not null primary key,
	licencePlate VARCHAR(55) not null,
	brandId INT UNSIGNED not null,
	colorId INT UNSIGNED not null,
	modelId INT UNSIGNED not null,
 	foreign key (modelId) references models(id),
 	foreign key (brandId) references brands(id),
 	foreign key (colorId) references colors(id)
);

CREATE TABLE IF NOT EXISTS vehiclesSpaces( 
	id int unsigned not null primary key auto_increment,
	vehicleId INT NOT null,
	spaceId INT UNSIGNED NOT null,
	arriveDate datetime NOT null,
	leaveDate datetime NOT null,
 	foreign KEY (spaceId) references spaces(id),
 	foreign key (vehicleId) references vehicles(id)
)


