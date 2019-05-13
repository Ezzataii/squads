# Read Me


## Getting  Project Setup:
1. Place the project in your WAMP's www. directory
2. Make sure the path to the project follows the following: ```278/projects/.git```
3. Create a database named squads and run the ```squads.sql``` commmands



## Getting Mail Setup:
1. Go to your ```php.ini``` file and find the line that reads ```[mail function]``` and modify it as the following:

```ini
[mail function]
sendmail_path ="[your wamp path]\sendmail\sendmail.exe -t -i"
```

2. Create a folder ```sendMail``` in your WAMP folder (usually ```C:\wamp```)
3. Download [This ZIP](http://www.glob.com.au/sendmail/sendmail.zip) and extract it in the folder you just created
4. Open the ```sendMail.ini``` file and configure it as following:
```ini
[sendmail]
smtp_server=smtp.gmail.com
smtp_port=465
smtp_ssl=ssl
default_domain=localhost
error_logfile=error.log
debug_logfile=debug.log
auth_username=[your_gmail_account_username]@gmail.com
auth_password=[your_gmail_account_password]
pop3_server=
pop3_username=
pop3_password=
force_sender=
force_recipient=
hostname=localhost
```
5. Enable ```openssl``` and ```sockets``` extensions for PHP compiler


## Getting Socket Setup:
1. Go to your php folder (mine is ```wamp64\bin\php\php7.2.14```)
2. In your terminal do: ```php.exe -q ../../../www/278/project/util/socket.php```