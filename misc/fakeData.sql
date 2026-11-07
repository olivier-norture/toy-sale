-- Fake data generator: 120 participants and 4000 toys, to simulate big prints
-- (restitution_all.php, restitution_envelope.php, ...).
--
-- Usage (docker):
--   docker compose exec -T db mysql -uroot -prootpassword bourseauxjouets < misc/fakeData.sql
-- Clean up afterwards with misc/fakeDataCleanup.sql
--
-- Everything created here can be found again with:
--   participants : email like '%@fake.test'
--   toys / bills : letter = 'T'
-- Re-running the script adds another batch (clean up first if you don't want that).

USE bourseauxjouets;

SET @nb_participants = 120;
SET @nb_toys = 4000;
SET @sold_ratio = 0.4;
SET @letter = 'T';
SET @base_ref = (SELECT COALESCE(MAX(ref), 0) FROM participant);
SET @redactor = (SELECT participant_PK FROM user WHERE participant_PK IS NOT NULL ORDER BY PK LIMIT 1);

-- numbers 1..10000
DROP TEMPORARY TABLE IF EXISTS numbers;
CREATE TEMPORARY TABLE numbers (n INT PRIMARY KEY);
INSERT INTO numbers (n)
SELECT a.d + 10 * b.d + 100 * c.d + 1000 * e.d + 1
FROM (SELECT 0 d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) a,
     (SELECT 0 d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) b,
     (SELECT 0 d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) c,
     (SELECT 0 d UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) e;

-- One buyer who "buys" the sold toys
INSERT INTO participant (nom, prenom, adresse, cp, ville, email, tel, type, ref)
VALUES ('TESTACHETEUR', 'FAKE', '1 RUE DU TEST', '60230', 'CHAMBLY', 'acheteur@fake.test', '0600000000', 'ACHETEUR', NULL);
SET @buyer = LAST_INSERT_ID();

-- Participants (sellers)
INSERT INTO participant (nom, prenom, adresse, cp, ville, email, tel, type, ref)
SELECT CONCAT('TEST', LPAD(n, 3, '0')),
       CONCAT('FAKE', n),
       CONCAT(n, ' RUE DU TEST'),
       '60230',
       'CHAMBLY',
       CONCAT('participant', n, '@fake.test'),
       CONCAT('06', LPAD(n, 8, '0')),
       'VENDEUR',
       @base_ref + n
FROM numbers
WHERE n <= @nb_participants;

-- Owner of each toy: the first toys give one toy to each participant (so everyone has
-- at least one), the others are spread randomly (some participants have many more toys)
DROP TEMPORARY TABLE IF EXISTS toy_owner;
CREATE TEMPORARY TABLE toy_owner (k INT PRIMARY KEY, owner INT NOT NULL);
INSERT INTO toy_owner (k, owner)
SELECT n,
       CASE WHEN n <= @nb_participants THEN n
            ELSE 1 + FLOOR(@nb_participants * POW(RAND(), 1.5)) END
FROM numbers
WHERE n <= @nb_toys;

-- Toy number inside each participant (1, 2, 3, ...), price and sold flag.
-- (MySQL can't read a temporary table twice in one query, hence the copy.)
DROP TEMPORARY TABLE IF EXISTS toy_owner2;
CREATE TEMPORARY TABLE toy_owner2 (k INT PRIMARY KEY, owner INT NOT NULL, KEY (owner));
INSERT INTO toy_owner2 SELECT k, owner FROM toy_owner;

DROP TEMPORARY TABLE IF EXISTS toy_tmp;
CREATE TEMPORARY TABLE toy_tmp (
    k INT PRIMARY KEY,
    owner INT NOT NULL,
    toy_id INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    sold TINYINT NOT NULL
);
INSERT INTO toy_tmp (k, owner, toy_id, price, sold)
SELECT t.k,
       t.owner,
       (SELECT COUNT(*) FROM toy_owner2 t2 WHERE t2.owner = t.owner AND t2.k <= t.k),
       ROUND((1 + RAND() * 49) * 2) / 2,
       RAND() < @sold_ratio
FROM toy_owner t;

INSERT INTO objet (designation, prix, date_baj, date_depot, date_vente, vendeur_PK, acheteur_PK,
                   redacteurDepot_PK, id, letter, ref)
SELECT CONCAT('JOUET TEST ', LPAD(t.owner, 3, '0'), '-', LPAD(t.toy_id, 3, '0')),
       t.price,
       CURDATE(),
       NOW(),
       IF(t.sold, NOW(), NULL),
       p.PK,
       IF(t.sold, @buyer, NULL),
       @redactor,
       t.toy_id,
       @letter,
       CONCAT(@letter, LPAD(p.ref, 3, '0'), '-', LPAD(t.toy_id, 3, '0'))
FROM toy_tmp t
JOIN participant p ON p.ref = @base_ref + t.owner AND p.email LIKE '%@fake.test';

-- One deposit bill per participant, with its toys attached
INSERT INTO bill (type, cash, `check`, creditCard, total, active, date, customer_pk, redactor_pk, tax, letter)
SELECT 'DEPOT', 0, 0, 0, COALESCE(SUM(o.prix), 0), TRUE, CURDATE(), p.PK, COALESCE(@redactor, p.PK), 5, @letter
FROM participant p
JOIN objet o ON o.vendeur_PK = p.PK AND o.letter = @letter
WHERE p.email LIKE 'participant%@fake.test' AND p.ref > @base_ref
GROUP BY p.PK;

INSERT INTO bill_objects (bill_id, object_id)
SELECT b.id, o.PK
FROM bill b
JOIN objet o ON o.vendeur_PK = b.customer_pk AND o.letter = @letter
WHERE b.letter = @letter AND b.type = 'DEPOT'
  AND NOT EXISTS (SELECT 1 FROM bill_objects bo WHERE bo.bill_id = b.id);

-- Keep the PC counter after the generated refs
UPDATE pc SET counter = GREATEST(counter, @base_ref + @nb_participants);

-- Summary
SELECT COUNT(DISTINCT vendeur_PK) AS participants, COUNT(*) AS toys,
       SUM(acheteur_PK IS NOT NULL) AS sold, SUM(acheteur_PK IS NULL) AS unsold
FROM objet WHERE letter = @letter;
