## Fixtures
To load fixtures run:  

```bin/console doctrine:fixtures:load```

this will load 1k dummy products.

Simply run:  

```app:create-user <email> <password> [<role>]```  

for example: 
```bin/console app:create-user enzode@enzode.pl "enzode" ```  

Access to all endpoints except ```api/login``` is restricted to authenticated users.
To authenticate, you need to send a ```POST``` request to ```/api/login``` with the following body:
```json
{
    "email": "enzode@enzode.pl",
    "password": "enzode"
}
```
If the credentials are correct, you will receive a response with a token that you need to include in the headers of all subsequent requests:
```json
{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJp..."
}
```
Then include token in headers:

``` Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJp...```

## API Endpoints

### ```GET``` /api/products
### ```GET``` /api/products/report
### ```POST``` /api/products
example payload:
```json
{
    "name": "Ziemniak",
    "price": 181.99,
    "description": "Smaczna rzecz",
    "createdAt": "2025-02-20T16:54:55+00:00",
    "currency": {
        "code": "PLN"
    },
    "category": {
        "name": "Warzywa"
    },
    "productAttributes": [
        {
            "attribute": {
                "code": "ORG"
            },
            "value": "Pakistan"
        },
        {
            "attribute": {
                "code": "DAN"
            },
            "value": "Jadalne"
        }
    ]
}
```
Succesful response with status code ```201```:
```json
{
    "message": "Product created successfully",
    "data": {
        "id": "1056f083-f3fa-4f3e-9747-d477f546acc4"
    }
}
```
