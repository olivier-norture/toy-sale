alter table pc add `counter` bigint(20) NOT NULL DEFAULT 1;
alter table participant add `REF` bigint(20);
alter table objet add `ref` varchar(20);

-- Update the REF for existing Participant
update participant set `REF` = `PK`;

-- Update all PC's counters to the max
update pc set counter = (select max(`PK`)+1 from participant);

-- Update all Object's ref
update objet set ref = concat(letter, LPAD(vendeur_pk, 3, "0"), "-", LPAD(id, 3, "0"));