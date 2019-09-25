
# food

| Champ  | Type  | Spécificités   | Description   |
| - | -   | -   | -   |
| id | int | NOT NULL PRIMARY KEY AUTO_INCREMENT UNSIGNED | identifiant d'un aliment |
| name | TINYTEXT | NOT NULL | nom d'un aliment |
| price | FLOAT | NOT NULL | prix d'un aliment |
| unit | TINYTEXT | NOT NULL | unité de mesure d'un aliment ex: en Kg |
| type | TINYTEXT | NOT NULL | type d'un aliment |
| created_at | TIMESTAMP | NOT NULL | date de création de l'aliment|
| updated_at | TIMESTAMMP | NULL, on update CURRENT_TIMESTAMP | date de modification |


# user

| Champ  | Type  | Spécificités   | Description   |
| - | -   | -   | -   |
| id | INT | PRIMARY KEY, NOT NULL, UNSIGNED, AUTO_INCREMENT | id user |
| firstname | TINYTEXT | NOT NULL | prénom de l'utilisateur |
| lastname | TINYTEXT | NULL | nom de famille de l'utilisateur |
| email | TINYTEXT | NOT NULL | email de l'utilisateur |
| hashed_password | TINYTEXT | NOT NULL | mot de passe hashé de l'utilisateur |
| created_at | TIMESTAMP | NOT NULL, DEFAULT CURRENT_TIMESTAMP | date de création |
| updated_at | TIMESTAMMP | NULL, on update CURRENT_TIMESTAMP | date de modification |

# recipe

| Champ  | Type  | Spécificités   | Description   |
| - | -   | -   | -   |
| id | INT | PRIMARY KEY, NOT NULL, UNSIGNED, AUTO_INCREMENT | id recette |
| title | TINYTEXT | NOT NULL | titre de la recette (pour affichage) |
| slug | TINYTEXT | NOT NULL | titre de la recette (pour bdd/url) |
| type | TINYTEXT | NOT NULL | Type de la recette selon le moment de la journée |
| created_at | TIMESTAMP | NOT NULL, DEFAULT CURRENT_TIMESTAMP | date de création |
| updated_at | TIMESTAMMP | NULL, on update CURRENT_TIMESTAMP | date de modification |


# ingredients

| Champ  | Type  | Spécificités   | Description   |
| - | -   | -   | -   |
| recipe_id | entity | NOT NULL, FOREIGN KEY | id de la recette rattachée |
| food_id | entity | NOT NULL, FOREIGN KEY | id de l'aliment rattaché |
| quantity | INT | NOT NULL | quantité de l'ingrédient (unité de mesure donnée dans Food) |

# recettes_steps

| Champ  | Type  | Spécificités   | Description   |
| - | -   | -   | -   |
| id | int | NOT NULL PRIMARY KEY UNSIGNED AUTO_INCREMENT | id d'une étape de recette |
| recipe_id | entity | NOT NULL, FOREIGN KEY | id de la recette rattachée |
| step_order | SMALLINT | NOT NULL | L'ordre d'affichage de l'étape |
| content | TEXT | NOT NULL |  |
| created_at | TIMESTAMP | NOT NULL, DEFAULT CURRENT_TIMESTAMP | date de création |
| updated_at | TIMESTAMMP | NULL, on update CURRENT_TIMESTAMP | date de modification |

# menus

| Champ  | Type  | Spécificités   | Description   |
| - | -   | -   | -   |
| id | int | NOT NULL PRIMARY KEY UNSIGNED AUTO_INCREMENT | id d'un menu |
| user_id | entity | NOT NULL, FOREIGN KEY | id de l'user |
| created_at | TIMESTAMP | NOT NULL, DEFAULT CURRENT_TIMESTAMP | date de création |
| updated_at | TIMESTAMMP | NULL, on update CURRENT_TIMESTAMP | date de modification |


# menu_recipe

| Champ  | Type  | Spécificités   | Description   |
| - | -   | -   | -   |
| recipe_id | entity | PRIMARY KEY FOREIGN KEY | lien vers la recette |
| menu_id | entity | PRIMARY KEY FOREIGN KEY | lien vers le menu |



# objectives

| Champ  | Type  | Spécificités   | Description   |
| - | -   | -   | -   |
| id | int | NOT NULL PRIMARY KEY UNSIGNED AUTO_INCREMENT | id de l'objectif |
| budget | FLOAT | NOT NULL | object budget |
| user_id | INT | NOT NULL FOREIGN KEY | lien avec le user |
| created_at | TIMESTAMP | NOT NULL, DEFAULT CURRENT_TIMESTAMP | date de création |
| updated_at | TIMESTAMMP | NULL, on update CURRENT_TIMESTAMP | date de modification |


# labels

| Champ  | Type  | Spécificités   | Description   |
| - | -   | -   | -   |
| id | int | NOT NULL PRIMARY KEY UNSIGNED AUTO_INCREMENT | id du label |
| name | TINYTEXT | NOT NULL | nom du label d'un aliment |
| created_at | TIMESTAMP | NOT NULL, DEFAULT CURRENT_TIMESTAMP | date de création |
| updated_at | TIMESTAMMP | NULL, on update CURRENT_TIMESTAMP | date de modification |


# label_food

| Champ  | Type  | Spécificités   | Description   |
| - | -   | -   | -   |
| food_id | entity | PRIMARY KEY FOREIGN KEY | lien vers l'aliment |
| label_id | entity | PRIMARY KEY FOREIGN KEY | lien vers le label |