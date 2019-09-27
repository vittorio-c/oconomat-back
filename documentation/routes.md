# V1

## CRUD 

### User

> NB : la création d'un utilisateur sera gérée par le SecurityController au moment du signup

| Endpoint  | Méthode HTTP | Contrôleur->method() | Description |
| --        | --           | --                   | --    |
| /api/user/{id}| GET | UserController->find($id) | récupération d'un utilisateur |
| /api/user/{id}| PUT | UserController->update($id) | modification d'un utilisateur |
| /api/user/{id}| DELETE | UserController->delete($id) | suppression d'un utilisateur |
| /signup | POST | SecurityController->signup() | création d'un utilisateur |

### Recipe

> NB : le create/delete/update d'une recette sera utile uniquement pour la partie admin
> et, éventuellement, si on laisse la possibilité à l'user d'ajouter ses propres recettes dans une V2/V3

| Endpoint  | Méthode HTTP | Contrôleur->method() | Description |
| --        | --           | --                   | --    |
| /api/recipe/{id} | GET | récupération d'une recette d'un menu |
| /api/recipe/{id}/ingredient | GET | recipeController->find($id) | liste des ingrédients d'une recette |
| /api/recipe/{id}/steps | GET |  recipeController->find($id) | liste des étapes d'une recette  |
| /api/recipe/create | POST | création d'une recette |
| /api/recipe/{id} | DELETE | suppression d'une recette |
| /api/recipe/{id} | UPDATE | modification d'une recette |

### Menu

| Endpoint  | Méthode HTTP | Contrôleur->method() | Description |
| --        | --           | --                   | --    |
| /api/menu/{id} | GET | MenuController->find() | récupération d'un menu (=de la liste des recettes associées) |
| /api/menu/create | POST | MenuController->create() | création d'un menu (=soumission du formulaire avec objectifs) |
| /api/menu/{id} | UPDATE | MenuController->update() | modification d'un menu existant |
| /api/menu/{id} | DELETE | MenuController->delete() | suppression d'un menu existant |

## Autres routes

### User

| Endpoint  | Méthode HTTP | Contrôleur->method() | Description |
| --        | --           | --                   | --    |
| /api/contact | POST | UserController->contact() | soumission & traitement formulaire de contact |
| /login | POST | SecurityController->login() | soumission & traitement formulaire de login |
| /logout | GET | SecurityController->logout() | logout |

### Divers

| Endpoint  | Méthode HTTP | Contrôleur->method() | Description |
| --        | --           | --                   | --    |
| /api/menu/{id}/shopping-list | GET | MenuController->getShopiingList() | accès à la liste de course d'un menu |
| /api/menu/renew | POST | MenuController->renew() | renouvellement d'un menu avec critères non modifiés |


## mises de côté

| /api/user/check-password | POST | UserController->checkPassword() | soumission & traitement formulaire de récupération mot de passe |
