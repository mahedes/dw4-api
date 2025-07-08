Voir : https://symfony.com/doc/current/serializer.html 

Projet API Bibliothèque (EEDN_DW4)
========================



Interface front
------------

Voir le site Postman : https://www.postman.com/

-> Crée un compte

-> Ouvre un espace Workspace

-> Télécharge Postman Desktop



Installation
------------

Récupérer le code source
```bash
$ git clone https://github.com/mahedes/dw4-api.git

```

Installer les dépendances

```bash
$ composer install

```


Completer DATABASE_URL dans .env et lancer les migrations 

```bash
$ php bin/console doctrine:database:create

$ php bin/console doctrine:migrations:migrate

```

Charger les fixtures

```bash
$ php bin/console doctrine:fixtures:load

```


Run the application
------------

```bash
$ symfony serve
```


Exemple
------------

Ici, on teste l'url http://localhost:8000/books

En requête GET et on clique sur le bouton Send

On obtient le résultat suivant (une réponse en JSON)

<img width="1596" alt="Capture d’écran 2025-07-07 à 13 57 15" src="https://github.com/user-attachments/assets/708efae0-d69f-45b9-8d2f-2520abaa583b" />

