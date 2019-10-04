# Les routes disponibles

**GET `/api/menu/{menu-id}`** \

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

**GET `/api/recipe/{recipe_id}`**  

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

**GET `/api/recipe/{recipe_id}/ingredients`**  

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

**GET `/api/recipe/{recipe_id}/steps`**  

Retourne les étapes de préparation de la recette associée

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

**GET `/api/user/{user_id}`**  

Retourne les informations d'un utilisateur déjà enregistré en bdd

Ex :

```json
{
  "id": 118,
  "email": "elouis@nicolas.com",
  "roles": [
    "ROLE_USER"
  ],
  "firstname": "Thérèse",
  "lastname": "Hamon",
  "createdAt": "2019-10-04T09:47:00+00:00",
  "updatedAt": null,
  "objectifs": [
    {
      "budget": 83
    }
  ],
  "menus": [
    {
      "id": 133,
      "createdAt": "2019-10-04T09:47:00+00:00",
      "updatedAt": null,
      "recipes": [
        {
          "id": 373
        },
        {
          "id": 384
        },
        {
          "id": 387
        },
        etc.
      ]
    },
    {
      "id": 147,
      "createdAt": "2019-10-04T09:47:00+00:00",
      "updatedAt": null,
      "recipes": [
        {
          "id": 372
        },
        {
          "id": 374
        },
        {
          "id": 384
        },
        etc.
      ]
    }
  ]
}

```

Ce résultat va changer !

**GET `/api/menu/{userId}/last`**  

Retourne le dernier menu appartenant à l'utilisateur

**TODO**



**POST `/api/objectif/menu/generate`**  

Permet de créer un nouveau menu en fonction d'un budget donné 

Ex :

<u>Request POST</u>

```json
{
    "budget": 100
}
```

<u>Réponse</u>

```json
{
  "id": 51,
  "createdAt": "2019-10-04T15:59:28+02:00",
  "updatedAt": null,
  "user": "http://api.oconomat.fr/api/user/65",
  "recipes": [
    "http://api.oconomat.fr/api/recipe/87",
    "http://api.oconomat.fr/api/recipe/93",
    "http://api.oconomat.fr/api/recipe/94",
    "http://api.oconomat.fr/api/recipe/95",
    "http://api.oconomat.fr/api/recipe/101",
    "http://api.oconomat.fr/api/recipe/109",
    "http://api.oconomat.fr/api/recipe/113",
    "http://api.oconomat.fr/api/recipe/139",
    "http://api.oconomat.fr/api/recipe/142",
    "http://api.oconomat.fr/api/recipe/147",
    "http://api.oconomat.fr/api/recipe/150",
    "http://api.oconomat.fr/api/recipe/156",
    "http://api.oconomat.fr/api/recipe/164",
    "http://api.oconomat.fr/api/recipe/167"
  ]
}
```

(le menu créé avec ses recettes; cela sera modifié plus tard, avec notamment l'information du cout total du nouveau menu)



**POST `/api/login_check`**

Permet de se login sur le site.

<u>Request POST</u>

```
{
	"email": "gilbert23@barre.com",
	"password": "titan"
}
```

> Content-Type: application/json

<u>Réponse</u>

```json
{
  "userId": 65,
  "payload": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE1NzAxOTczNTMsImV4cCI6MTU3MDIwMDk1Mywicm9sZXMiOlsiUk9MRV9VU0VSIl0sImVtYWlsIjoiZ2lsYmVydDIzQGJhcnJlLmNvbSJ9.nojLdcUfH_KLxBHgEdYRMj7OlCvfPe0rQOna5TNjiesUv9ehGxI6vRP807RCxVRAXAUVkkEaKL_1lTN5xe_yYN8O2vrmuW7TSjzq_twXLzvy-Mdq6CV7SyNtgOzB3B2knsNVakvXfQjbCmkd21IqmN0LcUxtGObztid4yHKof3zQY3SUsg5qxSZ40SGqR5TZyIIM090L2q_R2mZ1DlRZG_OJrWIkBMknGpEwfM-lfsAHX33VYVwNfK3Q8p53gORwTPmxdKnMyoXdJR2UkL9Oo5hgwGhXQmX7cgqU4h0kvcM8AStP89TmFpzlVWfeld_uHHXODNefcdCGQP-frsyx5uCW-Lw71kxPJm1N5JqdmAGAZWqvFp0sYwoLRWRv8J3YcdDMVsZh-aL6fdKTYnjV3cnw66ALGKS7934J4i0vj51d-h0M7eT1E_YWx-m8fRdBxeQzWyHDWX4ZyX04htEY--OoVasG0-J_o6e3Fba5hjxPs6gJIzewPmmpTvjWfwAl6ZLIPtMnet8cySRyhCUL0SThsLbIhn1jToN07fx-ZQRYMy_FryYaagUFBcLJ3yQeSxnNMHCIeyexkGTOtEy4Yb_S8d-zAcgdh_M65CqTR-64PJ5Wfa1FOe87ueSRb7C1l8G_k9LX4nL3p31_5UR9ReAfLMB1L_X9SCsiH0HvB_M"
  }
}
```


**POST `/api/register`**  

Permet de créer un nouvel utilisateur

```
firstname: Mickael
lastname: Jackson
email: mickael.jackson@gmail.com
password: mickey
passwordConfirm: mickery
```

> Content-Type: multipart/form-data (à confirmer)





