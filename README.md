# Bourse aux jouets

## PC
Chaque PC a une lettre unique A, B, C, E (le D est sauté car il ressemble trop au B).

Lorsqu'un utilisateur se présente à un PC il est créer et un identifiant lui est attribué.

Cet identifiant est une séquence propre à chaque PC qui commence à 1 (A1, A2, B1, B2, etc.).

Si un utilisateur revient déposer il garde son numéro MAIS il doit retourner au même PC car la lettre n'est pas stoquée ...

## Bug
- BUG-001
Cathy a reprit une saisie et le compteur s'est incrémenté.
Je n'ai pas réussi à reproduire le bug
J'ai essayer d'entrer 2 jouets; de quitter le dépot et d'y revenir et j'ai garder la référence.
J'ai même cliqué sur imprimer pour revenir ensuite et c'était toujours la même référence ...

## Play a migration script
```shell
$ mysql -u root bourseauxjouets < misc/update/update04_04_10_2024.sql
```

## Get the bilan
```shell
$ echo "select o.`ref`, p.prenom, p.nom, CONCAT('"', o.designation, '"') as description, o.prix, CASE WHEN o.acheteur_PK is null THEN 0 ELSE 1 END as vendu from objet o join participant p on (o.vendeur_PK = p.PK)" | mysql -B -u root bourseauxjouets | sed 's/\t/;/g' > /var/www/html/bourseauxjoeuts/web/bilan.csv 
```

##
```shell
$ sudo yum remove influxdb telegraf
```

## SeLinux
```shell
$ sudo /var/www/html/bourseauxjoeuts/misc/selinux.sh
```

## update ref to participant and object
```
update participant set ref = 666 where pk = 1120;
update objet set ref = concat("E040-", LPAD(id, 3, 0)) where vendeur_PK = 1856;
```

## TODO
- [] Enregister la lettre du PC avec la référence de l'utilisateur ou changer la référence pour qu'elle soit unique
- [x] Vider les refs lors de la purge du système

- [x] Pas de lettre
- [x] Doublon
- [x] Il y en a sans identifiant

- [ ] bouton reset DB
  Demander une validation avant de lancer la purge !
- [x] CB ajouter 0 par default
Je pense que c'est la valeur qui est en DB; par default c'est null sur l'existant et pas 0.
A reverifier avec une nouvelle entrée

- [ ] Eviter les pages vides lors de l'impression (ou avec seulement la signature, etc.)

- essayer de faire lettre par lettre plutot que tout d'un coup

- NOTICE a supprimer



## Docker

Rebuild and refresh running container:
```sh
docker compose up -d --build
```

## Development environement
This project use Nix for an easy setup of dev dependencies like PHP.
Run it using
```sh
nix-develop
```

## How to run tests

This project uses PHPUnit for testing. To run the tests, you need to be in the Nix development environment.

1.  **Enter the Nix development environment:**
    ```sh
    nix-shell
    ```
2.  **Install Composer dependencies (if not already installed):**
    ```sh
    composer install
    ```
3.  **Run PHPUnit tests:**
    ```sh
    vendor/bin/phpunit
    ```
