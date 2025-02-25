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

