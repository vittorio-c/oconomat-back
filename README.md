# Les routes disponibles

`/api/menu/{menu-id}` \
Retourne un menu accompagné de plusieurs liens vers les recettes qui le composent, ainsi que d'un lien vers son utilisateur. 

Ex: 

```json
{
  "id": 5,
  "createdAt": "2019-09-28T18:19:04+02:00",
  "updatedAt": null,
  "user": "http://vmlocal:8001/api/user/9",
  "recipes": [
    "http://vmlocal:8001/api/recipe/83",
    "http://vmlocal:8001/api/recipe/95",
    ]
}
```

**`/api/recipe/{recipe_id}`**  
Retourne une seule recette, accompagnée de ses ingrédients et de ses étapes.

Ex : 

```json
{
  "id": 14,
  "title": "aut",
  "slug": null,
  "type": "voluptates",
  "createdAt": "2019-10-01T20:33:42+00:00",
  "updatedAt": null,
  "recipeSteps": [],
  "ingredients": [
    {
      "quantity": 166,
      "aliment": {
        "name": "quaerat",
        "unit": "kg"
      }
    },
    {
      "quantity": 31,
      "aliment": {
        "name": "autem",
        "unit": "kg"
      }
    }
}
```

**`/api/recipe/{recipe_id}/ingredients`**  
Retourne les ingrédients de la recette associées

Ex :

```json
[
  {
    "quantity": 166,
    "aliment": {
      "name": "quaerat",
      "unit": "kg",
      "type": "sunt"
    }
  },
  {
    "quantity": 31,
    "aliment": {
      "name": "autem",
      "unit": "kg",
      "type": "omnis"
    }
  }
]
```

**`/api/recipe/{recipe_id}/steps`**  
Retourne les étapes de la recette associée

Ex : 

```json
[
  {
    "stepNumber": 5,
    "content": "Quibusdam placeat aut porro aperiam ea delectus ipsam. Itaque est et reiciendis illum dicta sed et. Beatae laboriosam sit cupiditate esse inventore. Consequuntur eaque placeat quo at."
  },
  {
    "stepNumber": 6,
    "content": "Accusantium repudiandae qui qui qui. Rerum et quia doloribus perspiciatis sit qui iure qui. Sunt est saepe voluptates eveniet et sit. Ex sint rerum exercitationem officia."
  },
  {
    "stepNumber": 7,
    "content": "Illum dolor voluptatibus rerum doloremque. Eos provident ipsam velit eos eaque odit iusto veniam. Deserunt est sed dolores voluptatem."
  }
]
```

**`/api/user/{user_id}`**  
Retourne les informations d'un utilisateur déjà enregistré en bdd

Ex :

```json

```

**`/api/menu/{userId}/last`**  
Retourne le dernier menu appartenant à l'utilisateur

Ex: TODO



