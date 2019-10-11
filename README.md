# Les routes disponibles

## `/api/menu/{menu-id}` \

Méthode : GET 

Retourne un menu accompagné de plusieurs liens vers les recettes qui le composent, ainsi que d'un lien vers son utilisateur. 

> le menu n'est accessible qu'à l'utilisateur qui l'a créé  
> pas besoin d'envoyer cette information en front, la vérification se fait côté back, en récupérant l'user connecté

Ex de réponse :

```json
{
  "idMenu": 245,
  "createdAt": "2019-10-11",
  "updatedAt": null,
  "userQuantity": 6,
  "user": {
    "id": 65,
    "url": "http://vmlocal:8001/api/user/65"
  },
  "recipes": [
    {
      "id": 171,
      "title": "Pancakes",
      "url": "http://vmlocal:8001/api/recipe/171",
      "type": "petit déjeuner",
      "price": 5.03,
      "image": "https://assets.afcdn.com/recipe/20190423/91265_w420h344c1cx1936cy3442cxt0cyt0cxb19200cyb28800.jpg"
    },
    {
      "id": 174,
      "title": "Egg Mcmuffin",
      "url": "http://vmlocal:8001/api/recipe/174",
      "type": "petit déjeuner",
      "price": 11.4,
      "image": "https://assets.afcdn.com/recipe/20160322/61548_w420h344c1cx1632cy2464.jpg"
    },
    {
      "id": 175,
      "title": "Petits pain au lait maison",
      "url": "http://vmlocal:8001/api/recipe/175",
      "type": "petit déjeuner",
      "price": 4.68,
      "image": "https://assets.afcdn.com/recipe/20131207/51120_w500h500.jpg"
    },
    {
       "etc": "etc"        
    }
  ]
}
```

## `/api/recipe/{recipe_id}`  

Méthode : GET 

Retourne une seule recette, accompagnée de ses ingrédients et de ses étapes.

Ex : 

```json
{
  "metaData": {
    "userQuantity": 1
  },
  "id": 189,
  "title": "Cordon bleu",
  "slug": "cordon-bleu",
  "type": "déjeuner",
  "createdAt": {
    "date": "2019-10-08 17:00:56.000000",
    "timezone_type": 3,
    "timezone": "Europe/Berlin"
  },
  "recipeSteps": [
    {
      "stepNumber": 1,
      "content": "Dans chaque tranche de poulet mettre deux tranches de bacon l'une à côté de l'autre et poser dessus une tranche de fromage."
    },
    {
      "stepNumber": 2,
      "content": "Refermer le tout en pliant la tranche en deux."
    },
    {
      "stepNumber": 3,
      "content": "Prévoir 3 assiettes et mettre dedans la farine, l'oeuf battu et la chapelure.\r\nTremper chaque escalope d'abord dans la farine, puis dans l'oeuf et pour finir dans la chapelure."
    },
    {
      "stepNumber": 4,
      "content": "Les faire cuire quelques minutes de chaque côté dans une poêle avec un peu de beurre."
    }
  ],
  "ingredients": [
    {
      "quantity": 1,
      "aliment": [
        {
          "name": "tranche blanc de poulet",
          "unit": "unité"
        }
      ]
    },
    {
      "quantity": 2,
      "aliment": [
        {
          "name": "tranche de bacon",
          "unit": "unité"
        }
      ]
    }
}
```

## `/api/recipe/{recipe_id}/ingredients`  

Méthode : GET 

Retourne les ingrédients de la recette associées

Ex de réponse :

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
  },
  {
    "etc": "etc"
    }
]
```

## `/api/recipe/{recipe_id}/steps`  

Méthode : GET 

Retourne les étapes de préparation de la recette associée

Ex de réponse :

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

## `/api/user/{user_id}`  

Méthode : GET 

Retourne les informations d'un utilisateur déjà enregistré en bdd

Ex de réponse :

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
        "etc": "etc"
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
        "etc": "etc"
      ]
    }
  ]
}

```

> Attention : Ce résultat va changer !


## `/api/menu/user/last`  

Méthode : GET 

Retourne le dernier menu appartenant à l'utilisateur connecté

> pas besoin de mettre l'id de l'user dans l'url !  \
> la récupération de l'user connecté se fait côté back  \

Ex de réponse 200 :

```json
{
  "idMenu": 246,
  "createdAt": "2019-10-11",
  "updatedAt": null,
  "userQuantity": 3,
  "user": {
    "id": 65,
    "url": "http://vmlocal:8001/api/user/65"
  },
  "recipes": [
    {
      "id": 174,
      "title": "Egg Mcmuffin",
      "url": "http://vmlocal:8001/api/recipe/174",
      "type": "petit déjeuner",
      "price": 5.7,
      "image": "https://assets.afcdn.com/recipe/20160322/61548_w420h344c1cx1632cy2464.jpg"
    },
    {
      "id": 176,
      "title": "Milkshake et petit sandwich",
      "url": "http://vmlocal:8001/api/recipe/176",
      "type": "petit déjeuner",
      "price": 6.07,
      "image": "https://www.enfant.com/uploads/1000/petit-dej-fraicheur.jpg"
    },
    {
       "etc": "etc"        
    }
  ]
}
```

Ex de réponse 404 (l'utilisateur connecté ne possède pas encore de menu) :

```json
{
  "status": 404,
  "message": "L'utilisateur connecté ne possède pas encore de menu."
}
```


##  `/api/objectif/menu/generate`  

Méthode : POST 

Permet de créer un nouveau menu en fonction d'un budget donné 

Ex de requête :

```json
{
    "budget": 100,
    "userQuantity": 3
}
```

> requete json en post

Ex de réponse :

```json
{
  "metadata": {
    "status": 200,
    "message": "Menu généré avec succès.",
    "budget": 100,
    "totalPrice": 94.23,
    "userQuantity": 3
  },
  "idMenu": 246,
  "createdAt": "2019-10-11",
  "updatedAt": null,
  "userQuantity": 3,
  "user": {
    "id": 65,
    "url": "http://vmlocal:8001/api/user/65"
  },
  "recipes": [
    {
      "id": 180,
      "title": "Muesli dans son bain de yahourt nature",
      "url": "http://vmlocal:8001/api/recipe/180",
      "type": "petit déjeuner",
      "price": 2.5,
      "image": "https://static.cuisineaz.com/610x610/i76565-yaourt-muesli.jpg"
    },
    {
      "id": 183,
      "title": "Gaufres faciles et légères",
      "url": "http://vmlocal:8001/api/recipe/183",
      "type": "petit déjeuner",
      "price": 1.42,
      "image": "https://assets.afcdn.com/recipe/20140103/35197_w420h344c1cx1632cy2464.jpg"
    },
    {
       "etc": "etc"        
    }
  ]
}
```

## `/api/objectif/budget/last/{userId}`

Retourne le dernier budget entré par l'utilisateur

Methode : GET

Ex de réponse :

```json
72
```


## `/api/menu/{menu}/shopping-list`  

Méthode : GET 

Retourne une liste de course générée à partir d'un menu

> pour les valeurs float, il faut faire un travail en front pour les arrondir (je n'ai pas réussi en back)

Ex de réponse :

```json
{
  "metadata": {
    "menuId": 245,
    "createdAt": "2019-10-11T11:35:47+02:00",
    "userId": 65,
    "userQuantity": 6,
    "shoppingTotalPrice": 185.6500000000001
  },
  "shoppingList": [
    {
      "foodId": 526,
      "name": "farine",
      "quantity": 1.67,
      "price": 1.9,
      "unit": "kg",
      "totalPrice": 3.1599999999999997
    },
    {
      "foodId": 527,
      "name": "oeuf",
      "quantity": 54,
      "price": 0.3,
      "unit": "unité",
      "totalPrice": 16.200000000000003
    },
    {
        "ext" : "etc"
    }

```


##  `/api/login_check`

Méthode : POST 

Permet de se login sur le site.

Ex de requête :

```
{
	"email": "gilbert23@barre.com",
	"password": "titan"
}
```

> Content-Type: application/json

Ex de réponse :

```json
{
  "userId": 65,
  "payload": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE1NzAxOTczNTMsImV4cCI6MTU3MDIwMDk1Mywicm9sZXMiOlsiUk9MRV9VU0VSIl0sImVtYWlsIjoiZ2lsYmVydDIzQGJhcnJlLmNvbSJ9.nojLdcUfH_KLxBHgEdYRMj7OlCvfPe0rQOna5TNjiesUv9ehGxI6vRP807RCxVRAXAUVkkEaKL_1lTN5xe_yYN8O2vrmuW7TSjzq_twXLzvy-Mdq6CV7SyNtgOzB3B2knsNVakvXfQjbCmkd21IqmN0LcUxtGObztid4yHKof3zQY3SUsg5qxSZ40SGqR5TZyIIM090L2q_R2mZ1DlRZG_OJrWIkBMknGpEwfM-lfsAHX33VYVwNfK3Q8p53gORwTPmxdKnMyoXdJR2UkL9Oo5hgwGhXQmX7cgqU4h0kvcM8AStP89TmFpzlVWfeld_uHHXODNefcdCGQP-frsyx5uCW-Lw71kxPJm1N5JqdmAGAZWqvFp0sYwoLRWRv8J3YcdDMVsZh-aL6fdKTYnjV3cnw66ALGKS7934J4i0vj51d-h0M7eT1E_YWx-m8fRdBxeQzWyHDWX4ZyX04htEY--OoVasG0-J_o6e3Fba5hjxPs6gJIzewPmmpTvjWfwAl6ZLIPtMnet8cySRyhCUL0SThsLbIhn1jToN07fx-ZQRYMy_FryYaagUFBcLJ3yQeSxnNMHCIeyexkGTOtEy4Yb_S8d-zAcgdh_M65CqTR-64PJ5Wfa1FOe87ueSRb7C1l8G_k9LX4nL3p31_5UR9ReAfLMB1L_X9SCsiH0HvB_M"
  }
}
```


##  `/api/register`  

Méthode : POST 

Permet de créer un nouvel utilisateur

Ex de requête :

```
firstname: Mickael
lastname: Jackson
email: mickael.jackson@gmail.com
password: mickey
passwordConfirm: mickery
```

> Content-Type: multipart/form-data (à confirmer)





