-- Removes everything created by misc/fakeData.sql
--   docker compose exec -T db mysql -uroot -prootpassword bourseauxjouets < misc/fakeDataCleanup.sql
USE bourseauxjouets;

DELETE FROM bill_objects WHERE bill_id IN (SELECT id FROM bill WHERE letter = 'T' AND type = 'DEPOT'
                                            AND customer_pk IN (SELECT PK FROM participant WHERE email LIKE '%@fake.test'));
DELETE FROM bill WHERE letter = 'T' AND type = 'DEPOT'
                   AND customer_pk IN (SELECT PK FROM participant WHERE email LIKE '%@fake.test');
DELETE FROM objet WHERE vendeur_PK IN (SELECT PK FROM participant WHERE email LIKE '%@fake.test');
DELETE FROM participant WHERE email LIKE '%@fake.test';
