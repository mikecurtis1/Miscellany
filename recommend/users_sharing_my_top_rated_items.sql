SET @user := 266226;
SELECT @umax := MAX(`Book-Rating`) FROM `BX-Book-Ratings` WHERE `User-ID` = @user;

SELECT * FROM `BX-Book-Ratings` WHERE `ISBN` IN (
  SELECT `ISBN` FROM `BX-Book-Ratings` WHERE `Book-Rating` >= @umax AND `User-ID` = @user
) 
AND `User-ID` != @user AND `Book-Rating` >= @umax GROUP BY `User-ID` LIMIT 20;

/*
don't base other user rating max on the main user, their rating scales may be different
base other's rating limit on that other person's max rating
SET @user := 266226;
SELECT @umax := MAX(`Book-Rating`) FROM `BX-Book-Ratings` WHERE `User-ID` = @user;

SELECT * FROM `BX-Book-Ratings` WHERE `ISBN` IN (
  SELECT `ISBN` FROM `BX-Book-Ratings` WHERE `Book-Rating` >= @umax AND `User-ID` = @user
) 
AND `User-ID` != @user;
*/


/*
SET @user := 123456;
SELECT @group := `group` FROM user WHERE user = @user;
SELECT * FROM user WHERE `group` = @group;
*/

/*
SELECT `ISBN` FROM `BX-Book-Ratings` WHERE `Book-Rating` >= @umax AND `User-ID` = @user
*/
