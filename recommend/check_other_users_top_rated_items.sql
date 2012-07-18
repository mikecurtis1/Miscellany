SET @user := 266226;
SELECT @umax := MAX(`Book-Rating`) FROM `BX-Book-Ratings` WHERE `User-ID` = @user;

SET @otheruser := 270713;
SELECT @omax := MAX(`Book-Rating`) FROM `BX-Book-Ratings` WHERE `User-ID` = @otheruser;

SELECT * FROM `BX-Book-Ratings` WHERE `ISBN` IN (
  SELECT `ISBN` FROM `BX-Book-Ratings` WHERE `Book-Rating` >= @umax AND `User-ID` = @user
) 
AND `Book-Rating` >= @omax AND `User-ID` = @otheruser;
/* try creating a VIEW */
