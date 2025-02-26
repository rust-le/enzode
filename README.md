# Enzode
___
## Docker
To run the project, you need to have Docker installed.

To build and start containers run:
```docker-compose up -d```  
This will create 4 containers: ```nginx```, ```php-fpm```, ```db``` and ```redis```.



## Fixtures
To load fixtures, you need to enter the ```php-fpm``` container:
```docker exec -it php-fpm /bin/bash```
and run ```bin/console doctrine:fixtures:load```

This will load 1k of dummy products and will create a user with email ```enzode@enzode.com``` and password ```enzode```.

By the way, you can create your own user: ```app:create-user <email> <password> [<role>]```,   
for example: 
```bin/console app:create-user enzode@enzode.com "enzode" ```  

## Authentication

Access to all endpoints except ```api/login``` is restricted to authenticated users.
To authenticate, you need to send a ```POST``` request to ```/api/login``` with the following body:
```json
{
    "email": "enzode@enzode.com",
    "password": "enzode"
}
```
If the credentials are correct, you will receive a response with a token that you need to include in the headers of all subsequent requests:
```json
{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJp..."
}
```
Then include token in headers: ``` Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJp...```

## API Endpoints

### ```GET``` /api/products

example request:
```
localhost/api/products?page=1&limit=1
```
response:
```json
{
    "meta": {
        "current_page": 1,
        "limit": 1,
        "total_items": 1000,
        "total_pages": 1000
    },
    "data": [
        {
            "id": "4bab3ea5-394f-4870-9be2-4cdcf2531b53",
            "name": "Qui sequi nihil. 323",
            "price": 142.93,
            "createdAt": "2025-02-26T10:12:21+00:00",
            "currency": {
                "code": "KGS"
            },
            "category": {
                "name": "maiores"
            },
            "productAttributes": [
                {
                    "attribute": {
                        "code": "WEI"
                    },
                    "value": "quam"
                }
            ]
        }
    ]
}
```

### ```GET``` /api/products/report

example request:
```
localhost/api/products/report?page=1&limit=2&category=maiores&name=u&price_min=100&price_max=200
```
and response:
```csv
4bab3ea5-394f-4870-9be2-4cdcf2531b53,"Qui sequi nihil. 323",142.93,KGS,maiores
ecc9ef44-cf96-45a7-bb98-0616806d15a4,"Quo et fugit. 275",192.14,VES,maiores
```
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
## Rate limiting
Rate limiting is implemented on Nginx level. This solution is less flexible than ```symfony/rate-limiter``` but more powerful.  
User will receive a ```429``` status code if the limit is exceeded:
* After 30 request in 1 minute from the same IP (with burst of 10 extra requests).
* After 10 requests in 1 minute per token (with small burst of 5 extra requests).
```json
{
    "code": 429,
    "message": "Rate limit exceeded"
}
```

## Redis based Cache
There are two types of cache entries: ```PRODUCT``` and ```QUERY```.  
For the purposes of this task, it was assumed that entries with product data type ```PRODUCT``` do not expire (one of many simplifications).   
Each time you create a new product, a new ```PRODUCT``` entry is created in Redis and all ```QUERY``` type entries are deleted.   
During each request, if it does not already exist, an entry ```QUERY``` corresponding for the request parameters is created.   
Each subsequent request with the same parameters is returned from the cached data.


