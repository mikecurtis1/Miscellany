/* select list with item overlap of one or more items */
SELECT person, GROUP_CONCAT(item SEPARATOR '|') AS list, COUNT(item) AS count FROM items WHERE ((item IN (SELECT item FROM items WHERE person = 'a')) AND (person != 'a')) GROUP BY person
/*----*/
SELECT * FROM `BX-Users` WHERE `items` = 3 LIMIT 10
SELECT `User-ID`, COUNT(`ISBN`) AS `count` FROM `BX-Likes` WHERE ((`ISBN` IN (SELECT `ISBN` FROM `BX-Likes` WHERE `User-ID` = '408')) AND (`User-ID` != '408')) GROUP BY `User-ID`

SELECT `User-ID`, GROUP_CONCAT(`ISBN` SEPARATOR '|') AS `list`, COUNT(`ISBN`) AS `count` FROM `BX-Likes` WHERE ((`ISBN` IN (SELECT `ISBN` FROM `BX-Likes` WHERE `User-ID` = '408')) AND (`User-ID` != '408')) GROUP BY `User-ID`

/*----*/
SET @max = (SELECT MAX(`Book-Rating`) FROM `BX-Book-Ratings` WHERE `User-ID` = 228); SELECT `User-ID`, GROUP_CONCAT(`ISBN` SEPARATOR '|') AS `list`, `Book-Rating` FROM `BX-Book-Ratings` WHERE `Book-Rating` >= @max AND `ISBN` IN (SELECT `ISBN` FROM `BX-Book-Ratings` WHERE `Book-Rating` = @max AND `User-ID` = 228) AND `User-ID` != 228 GROUP BY `User-ID` LIMIT 100;
Query OK, 0 rows affected (0.01 sec)

+---------+------------+-------------+
| User-ID | list       | Book-Rating |
+---------+------------+-------------+
|   11718 | 039575514X |           9 |
|   28523 | 039575514X |          10 |
|   98440 | 039575514X |          10 |
|  125892 | 039575514X |           9 |
|  131437 | 039575514X |           9 |
|  132375 | 039575514X |          10 |
|  266226 | 039575514X |           9 |
+---------+------------+-------------+
7 rows in set (9.98 sec)

SELECT `ISBN`,`Book-Rating` FROM `BX-Book-Ratings` WHERE `Book-Rating` = (SELECT MAX(`Book-Rating`) FROM `BX-Book-Ratings` WHERE `User-ID` = 228) AND `User-ID` = 228;
+------------+-------------+
| ISBN       | Book-Rating |
+------------+-------------+
| 039575514X |           9 |
+------------+-------------+
1 row in set (0.01 sec)

SELECT * FROM `BX-Book-Ratings` WHERE `User-ID` = 11718 AND `Book-Rating` >= 9;
+---------+------------+-------------+
| User-ID | ISBN       | Book-Rating |
+---------+------------+-------------+
|   11718 | 0064400204 |           9 |
|   11718 | 0140430725 |          10 |
|   11718 | 0142004235 |           9 |
|   11718 | 0375415351 |          10 |
|   11718 | 037576013X |           9 |
|   11718 | 0380432811 |           9 |
|   11718 | 039575514X |           9 |
|   11718 | 0395927218 |           9 |
|   11718 | 0553212435 |           9 |
|   11718 | 0965018936 |          10 |
+---------+------------+-------------+
10 rows in set (0.02 sec)

