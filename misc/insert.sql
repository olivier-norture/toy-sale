insert into participant(nom, prenom, tel) values("admin", "admin", "admin");

insert into user(login, password, isAdmin, isDepot, isVente, isRestitution, participant_PK)
values("admin", "admin", 1, 1, 1, 1, 1);

insert into server_vars(`key`, value)
values
    ("SERVER_IP", "127.0.0.1"),
    ("DATE_DEBUT", "01/01/1970"),
    ("DATE_FIN", "03/01/1970")
;