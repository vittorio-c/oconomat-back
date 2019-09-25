 # V1

## CRUD 

| Endpoint  | Méthode HTTP | Contrôleur->method() | Description |
| --        | --           | --                   | --    |
| /api/user/create | POST | UserController->create() | Création d'un utilisateur |
| /api/user/{id}| GET | UserController->find($id) | récupération d'un utilisateur |
| /api/recipe/{$id}/ingredient | GET | recipeController->find($id) | liste des ingrédients d'une recette |
| /api/recipe/{id}/steps | GET |  recipeController->find($id) | liste des étapes d'une recette  |
| /api/recipe/{$id} | GET | récupération d'une recette d'un menu |
| /api/menu/{id} | GET | MenuController->find() | récupération d'un menu (=de la liste des recettes associées) |
| /api/menu/create | POST | MenuController->create() | soumission du formulaire avec mes objectifs |

## Autres ? a valider

| Endpoint  | Méthode HTTP | Contrôleur->method() | Description |
| --        | --           | --                   | --    |
| /api/contact | POST | UserController->contact() | soumission & traitement formulaire de contact |
| /api/user/login | POST | UserController->login() | soumission & traitement formulaire de login |
| /api/menu/{id}/recipies | GET | MenuController->getRecipies() | accès aux recettes d'un menu |
| /api/menu/{id}/shopping-list | GET | MenuController->getShopiingList() | accès à la liste de course d'un menu |
| /api/menu/renew | GET (POST?) | MenuController->renew() | renouvellement d'un menu avec critères non modifiés |
| /api/user/logout | GET | UserController->logout() | logout |


todo : 
- ajouter une rubrique type dans la table recette
- ajouter une rubrique type done pour recette



## de côté

| /api/user/check-password | POST | UserController->checkPassword() | soumission & traitement formulaire de récupération mot de passe |
