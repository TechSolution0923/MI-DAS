--Please run the sql below on the database prior to deploying the code changes for KDS-2741

alter table users  add column seeprojectedsales bool;
alter table users  add column seeprojectedsalesyear bool;
alter table users  add column seeorderfulfillment bool;

update users set seeprojectedsales = 1;
update users set seeprojectedsalesyear = 1;
update users set seeorderfulfillment = 1;

alter table salesorders add column quotefailurereason varchar(30) after quotereason;