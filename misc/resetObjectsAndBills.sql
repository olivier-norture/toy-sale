use bourseauxjouets

--truncate table pc;
--truncate table bilan_detail;
delete from bill_objects;
delete from bill;
delete from objet;
update pc set counter = 0;
update participant set REF = NULL;
