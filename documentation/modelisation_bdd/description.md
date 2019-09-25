# liste des tables
​
**aliments**  
​
- nom
- prix en euro
- unité de mesure (kilo, litre, unité, etc)
- type (fruit, légume, féculents, viandes, etc.)
- quantité
​
Ex : 
​
| nom     | prix (€) | unité    | type              |
| -       | -        | -        | -                 |
| banane  | 2        | kilo     | fruit             |
| farine  | 1,50     | gramme   | céréales          |
| pain    | 1        | unitaire | céréales          |
| poisson | 8        | kilo     | poissons          |
| lait    | 1,50     | litre    | produits-laitiers |
| pain    | 1        | unitaire | céréales          |
​
​
**utilisateur (enregistré)**
​
- nom
- prénom
- email
- mot de passe (hashé)
​
> contraintes alimentaires + budgétaires + nb personnes : ce sont des données qui sont amenées à bouger d'une semaine à l'autre, si on part sur un formulaire soumis de façon hebdomadaire. Du coup, il conviendrait de ne pas les rattacher à la personne, mais plutôt au  niveau du "menu hebdomadaire" généré. 
​
**recettes**

- id
- titre
- aliments (relation)

**ingredients**

- recette id : 45
- aliment id : 6
- quantité : 3
​
**recettes_steps**

- recette id
- step_number
- content​

**menus**

> toutes les recettes d'un utilisateur pour la semaine​

- user id
- recette id
- numéro de menu 
​
> possibilité 1 : clé composite sur person_id/recette_id/numéro_menu, pour garder des enregistrements uniques
> possibilité 2 : clé primaire "classique". A voir.
​
Ex : 
​
| user_id | recette_id | numero_menu |
| -       | -          | -           |
| 8       | 12         | 1           |
| 8       | 5          | 1           |
| 8       | 47         | 1           |
| 8       | 12         | 2           |
| 8       | 5          | 2           |
| 8       | 6          | 2           |
​

**objectifs**

- user id
- budget
- nb de personnes
- nb de repas​

**labels** 

> table qui servira à sélectionner des "contraintes" pour l'utilisateur
> mais également à décrire plus précisemment les aliments ou les recettes

- nom


**label_aliment** 

- aliment id
- label id
