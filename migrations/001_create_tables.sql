create database weather;
use weather;

create table cities (
    id int primary key,
    name varchar(255) not null,
    region enum("Dunántúl", "Közép-Magyarország", "Észak és Alföld") not null
);

create table maximum_temperatures (
    city_id int not null, 
    date bigint not null,
    max_temp float not null comment "Maximum hőmérséklet, Celsius fokban"
);

create table minimum_temperatures (
    city_id int not null, 
    date bigint not null,
    min_temp float not null comment "Minimum hőmérséklet, Celsius fokban"
);

create table precipitation (
    city_id int not null, 
    date bigint not null,
    precipitation float not null comment "Csapadék, milliméterben"
);
