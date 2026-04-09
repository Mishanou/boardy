![Вывод systemctl status nginx (active)](screenshots/01-nginx-status.png)
![«Welcome to nginx!» в браузере](screenshots/02-browser-ip.png)
![Вывод curl -v http://84.201.136.165](screenshots/03-curl.png)
![Вывод ls -la /var/www/ после chown](screenshots/04-permissions.png)
listen 80 default_server;
Директива listen указывает, на каком порту и интерфейсе сервер должен принимать входящие соединения; в данном случае Nginx слушает порт 80 на всех IPv4-интерфейсах и помечен как сервер по умолчанию.

listen [::]:80 default_server;
Директива listen указывает, что сервер также принимает соединения на порт 80 по протоколу IPv6 на всех интерфейсах и тоже является сервером по умолчанию для IPv6.

root /var/www/html;
Директива root задаёт корневую директорию, из которой Nginx будет брать файлы для обслуживания запросов (по умолчанию — /var/www/html).

server_name _;
Директива server_name определяет, для каких имён хостов (доменов) будет работать этот серверный блок; символ _ означает «любой домен / запрос без подходящего имени» (ловушка для всех остальных запросов).

index index.html index.htm index.nginx-debian.html;
Директива index задаёт список имён файлов, которые Nginx будет автоматически отдавать, если в запросе указана только директория (без имени файла), пробуя их по порядку.

![Создал DNS-зону в DuckDNS (альтернатива VK Cloud, т.к. использую Yandex Cloud и не добавлен в ai-info.ru)](screenshots/05-dns-zone.png)
В DuckDNS A-запись создаётся автоматически при обновлении IP

![Вывод ping (домен резолвится в IP VPS)](screenshots/07-ping.png)
![Вывод dig с подписями](screenshots/08-dig.png)
![Вывод dig +trace](screenshots/09-dig-trace.png)
![Страница Nginx в браузере](screenshots/10-browser-domain.png)