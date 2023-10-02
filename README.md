<h1 style="background:#8A2061; text-align:center; font-size:40px; font-weight:500; border-radius: 20px">LAB TAT <br> Praava Health </h1>
<h3 style="font-size:30px;">Requirements</h3>
<ul>
<li>PHP 8.0.6</li>
<li>Composer 2.3.5</li>
<li>psql (PostgreSQL) 14.5</li>
</ul><br><br>
<h3 style="font-size:30px;">Getting Started</h3>
<p>Clone the repo: </p>
<a style="border:2px solid #810054; border-radius:10px; background:#000; padding: 10px;" href="https://github.com/raihanislampolock/tat_time_lab.git">https://github.com/raihanislampolock/tat_time_lab.git</a><br><br><br>
<p>After finisheing the clone update the composer in the project</p>
<p style="font-size:14px; border:2px solid #810054; border-radius:10px; background:#000; padding: 10px;" >composer update</p><br><br>
<p>Generate .env File</p>
<p style="font-size:14px; border:2px solid #810054; border-radius:10px; background:#000; padding: 10px;">copy .env.example .env</p><br><br>
<p>Generate Key</p>
<p style="font-size:14px; border:2px solid #810054; border-radius:10px; background:#000; padding: 10px;">php artisan key:generate</p><br><br>
<h3 style="font-size:30px;">Database</h3>
<p>Create Database</p>
<p style="font-size:14px; border:2px solid #810054; border-radius:10px; background:#000; padding: 10px;">create database lab_tat;</p><br><br>
<p>Create a Schema named tat_times</p>
<h3 style="font-size:20px;">Setup Database</h3>
<p>Go to .env file and Edit values to match your database for setup local database</p>
<p style="font-size:14px; border:2px solid #810054; border-radius:10px; background:#000; padding: 10px;">DB_CONNECTION=pgsql<br>
DB_HOST=localhost<br>
DB_PORT=5432<br>
DB_DATABASE=tat_times<br>
DB_USERNAME=postgres<br>
DB_PASSWORD=</p><br><br>
<h3>Running migrations</h3>
<p style="font-size:14px; border:2px solid #810054; border-radius:10px; background:#000; padding: 10px;">php artisan migrate <br> php artisan admin:install</p><br><br>
<p>Start the server for development:</p>
<p style="font-size:14px; border:2px solid #810054; border-radius:10px; background:#000; padding: 10px;">php artisan serve</p><br><br>