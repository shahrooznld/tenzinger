# tenzinger

**Simple Export Symfony Api**

Installation process

1.Download or Clone Project </br>
2.Run composer install  </br>
3.Add database configuration .env file  </br>
4.Run php bin/console doctrine:database:create to create the database  </br>
5.Run php bin/console doctrine:migrations:migrate to execute the migration  </br>
6.Execute this sql query to fill database  </br>

`
INSERT INTO `employee_data` (`id`, `name`, `transport`, `distance`, `work_days`) VALUES
(1, 'Paul', 'Car', 60, 5),
(2, 'MArtin', 'Bus', 8, 4),
(3, 'Jeroen', 'Bike', 9, 5),
(4, 'Tineke', 'Bike', 4, 3),
(5, 'Arnout', 'Train', 23, 5),
(6, 'Matthijs', 'Bike', 11, 5),
(7, 'Rens', 'Car', 12, 5);
`
 
7.Run symfony server:start to run server  </br>
Project has no authentication, user and ... </br>


https://127.0.0.1:8000/export
