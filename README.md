<p align="center">
  <a href="https://anira-web.ru/" target="_blank">
    <img src="test" width="300px">
  </a>
  <h1 align="center">Yii 2 Rest Project + Catalog with recursion</h1>
  <br>
</p>

<div class="site-about">
  <ul>
    <li><b>Запустить в терминале встроенный http сервер (если сайт не открывается)</b>: nohup php /var/www/html/yii serve 127.0.0.1 -p 80 -t /var/www/html/web &
    </li>
    <li><b>Остановить встроенный http сервер</b>: kill -9 `ps -ef | grep php | grep -v grep | awk '{print $2}'`</li>
    <li><b>Авторизация реализована через Bearer токен</b>: 101-token</li>
  </ul>

  <hr>

  <h3>Получить все категории (GET)</h3>
  <p style="color:green;font-weight:bold;">curl -X GET -H "Content-Type: application/json" -H "Authorization: Bearer
    101-token"
  http://127.0.0.1:8080/rest/getcats</p>

  <h4>Полезная нагрузка</h4>
  <ul>
    <li>отсутствует</li>
  </ul>

  <hr>

  <h3>Добавить категорию (POST)</h3>
  <p style="color:green;font-weight:bold;">curl -X POST -H "Content-Type: application/json" -d '{"category_name":"My
    Cat 1"}' -H "Authorization: Bearer
  101-token" http://127.0.0.1:8080/rest/addcat</p>
  <h4>Полезная нагрузка</h4>
  <ul>
    <li><b>category_name</b>: string, required, min = 1</li>
  </ul>

  <hr>

  <h3>Обновить категорию (PUT)</h3>
  <p style="color:green;font-weight:bold;">curl -X PUT -H "Content-Type: application/json" -H "Authorization: Bearer
    101-token" -d '{"category_id": "9", "category_name": "My subCat 17_1 updated", "category_parent":"17"}'
  http://127.0.0.1:8080/rest/updatecat</p>
  <h4>Полезная нагрузка</h4>
  <ul>
    <li><b>category_id</b>: integer, required, min = 1</li>
    <li><b>category_name</b>: string, min = 1</li>
    <li><b>category_parent</b>: integer, min = 1</li>
  </ul>

  <hr>

  <h3>Удалить категорию (DELETE)</h3>
  <p style="color:green;font-weight:bold;">curl -X DELETE -H "Content-Type: application/json" -H "Authorization:
  Bearer 101-token" -d '{"category_id": "1"}' http://127.0.0.1:8080/rest/deletecat</p>
  <h4>Полезная нагрузка</h4>
  <ul>
    <li><b>category_id</b>: integer, required, min = 1</li>
  </ul>

  <hr>

  <h3>Получить все продукты (GET)</h3>
  <p style="color:green;font-weight:bold;">curl -X GET -H "Content-Type: application/json" -H "Authorization: Bearer
    101-token"
  http://127.0.0.1:8080/rest/getproducts</p>

  <h4>Полезная нагрузка</h4>
  <ul>
    <li>отсутствует</li>
  </ul>

  <hr>

  <h3>Добавить продукт (POST)</h3>
  <p style="color:green;font-weight:bold;">curl -X POST -H "Content-Type: application/json" -d '{"product_name":"My
    product 1", "product_content":"My product 1 content", "product_category_id":"1,5,1100"}' -H "Authorization:
  Bearer 101-token" http://127.0.0.1:8080/rest/addproduct</p>
  <h4>Полезная нагрузка</h4>
  <ul>
    <li><b>product_category_id</b>: string (split by ',' for multy cats), required, min = 1</li>
    <li><b>product_name</b>: string, min = 1</li>
    <li><b>product_content</b>: string, min = 1</li>
  </ul>

  <hr>

  <h3>Обновить продукт (PUT)</h3>
  <p style="color:green;font-weight:bold;">curl -X PUT -H "Content-Type: application/json" -H "Authorization: Bearer
    101-token" -d '{"product_id": "8", "product_name": "New product Title 101", "product_content": "New product Content 101", "product_category_id":"5,6"}'
  http://127.0.0.1:8080/rest/updateproduct</p>
  <h4>Полезная нагрузка</h4>
  <ul>
    <li><b>product_id</b>: integer, min = 1, required</li>
    <li><b>product_category_id</b>: string (split by ',' for multy cats), min = 1</li>
    <li><b>product_name</b>: string, min = 1</li>
    <li><b>product_content</b>: string, min = 1</li>
  </ul>

  <hr>

  <h3>Удалить продукт (DELETE)</h3>
  <p style="color:green;font-weight:bold;">curl -X DELETE -H "Content-Type: application/json" -H "Authorization: Bearer 101-token" -d '{"product_id": "7"}' http://127.0.0.1:8080/rest/deleteproduct</p>
  <h4>Полезная нагрузка</h4>
  <ul>
    <li><b>product_id</b>: integer, required, min = 1</li>
  </ul>

  <hr>

</div>
