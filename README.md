To start this website on your local machine:

1. Install docker https://docs.docker.com/engine/install/
2. If you have Windows - please install something to be able to run Makefile commands https://stackoverflow.com/questions/2532234/how-to-run-a-makefile-in-windows
3. open terminal inside the project folder
4. run `make init`
5. website - http://localhost:8888
1. all sent mails - http://localhost:8087/

### Production

Before running PROD you have to create a file with name `.env` in source root folder.<br>
The content of .env should be as in this example:
```shell
APP_SECRET=RANDOM_UNIQUE_STRING___CHANGE_PLEASE_hgsfd56&^*&(ADS*
POSTGRES_PASSWORD=RANDOM_UNIQUE_STRING___CHANGE_PLEASE_hgsfd56&^*&(ADS*
```
<br>
After creating `.env` file, you can run the app in Production environment with:

```shell
make prod-init
```

This version of the app will **NOT** be updated when you edit any files locally.<br>
If you want to edit and run it in PROD mode again, run `make prod-init` again.


To start the app in DEV mode - run `make prod-init`, it will stop PROD version and start DEV app.

### Test users for development

You can log in with different roles:

#### Admin account:

Email: `admin@example.com`
<br>Pass: `admin`

#### Student account:

Email: `student@example.com`
<br>Pass: `student`

#### Teacher account:

Email: `teacher@example.com`
<br>Pass: `teacher`

