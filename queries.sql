queries needed:
add family
update family
delete family
show families
show one family

add user
update user
delete user
show users
show one user

[add church]
update church
[delete church]
[show churches]
show one church

add fund
update fund
delete fund
show funds
show one fund

[add account]
[update account]
[delete account]
[show accounts]
[show one account]

add contribution
update contribution
delete contribution
show contributions
show one contribution

[add transaction]
[update transaction]
[delete transaction]
show transactions
show one transaction



*****************************************************************************
*****************************************************************************
*****************************************************************************
*****************************************************************************
*****************************************************************************
*****************************************************************************
*****************************************************************************
*****************************************************************************
*****************************************************************************
*****************************************************************************
*****************************************************************************

add family
	INSERT INTO `families`(fid, cid, displayname, photo, envstatus) VALUES (, 1, "", blob, 0/1)
update family
	UPDATE `families` SET displayname="", photo=blob, envstatus=0/1 WHERE fid=1
delete family
	DELETE FROM `families` WHERE fid=1
show families
	SELECT * FROM `families` WHERE cid=1
show one family
	SELECT * FROM `families` WHERE fid=1 LIMIT 1

add user
	INSERT INTO `users`(uid, fid, age, gender, prefix, firstname, surname, suffix, dob, dod, countryoforigin, memberstatus, relationship, photo, getsemailreceipts, daysnoticeemail, userrole, password)
				VALUES(, 1, adult/minor, m/f, Mr/Mrs/Ms, "John", "Smith", "Jr.", 1982-01-18, NULL, "United States", active/inactive/deceased, head/spouse/child/grandchild/other, blob, 0/1, 7, admin/staff/volunteer/member, passwordhash)
update user
	UPDATE `users` SET col=val WHERE uid=1
delete user
	DELETE FROM `users` WHERE uid=1
show users from a church
	SELECT `users`.* FROM `users` LEFT JOIN `families` ON (`users`.fid=`families`.fid) WHERE `families`.cid=1
	(select everything from users table when the user belongs to a family that belongs to the church)
	- joins users to families based on fid - and only selects those records with the right cid - and only selects columns from users table
show one user
	SELECT * FROM `users` WHERE uid=1 LIMIT 1
	
[add church]
	INSERT INTO `churches`(cid, name, address1, address2, city, state, zip, country, contactemail, receiptemail, foundingdate, usesenvelopes, achfee, ccfee, ccpct)
					VALUES()
update church
	UPDATE `churches` SET col=val WHERE cid=1
[delete church]
	DELETE FROM `churches` WHERE cid=1
[show churches]
	SELECT * FROM `churches`
show one church
	SELECT * FROM `churches` WHERE cid=1 LIMIT 1

add fund
	INSERT INTO `funds`(fdid, cid, aid, fundname, funddesc, startdate, enddate, enabled)
				VALUES(NULL, 1, 1, "Debt Relief", "This is for relieving our debt", NULL, NULL, "1")
update fund
	UPDATE `funds` SET col=val WHERE fdid=1
delete fund
	DELETE FROM `funds` WHERE fdid=1
show funds
	SELECT * FROM `funds` WHERE cid=1
show one fund
	SELECT * FROM `funds` WHERE fdid=1

[add account]
	INSERT INTO `accounts`(aid,cid) VALUES(1,1)
[update account]
	UPDATE `accounts` SET col=val WHERE aid=1 (necessary?)
[delete account]
	DELETE FROM `accounts` WHERE aid=1
[show accounts]
	SELECT * FROM `accounts` WHERE cid=1
[show one account]
	SELECT * FROM `accounts` WHERE aid=1

add contribution
	INSERT INTO `contributions`(cbid, uid, fdid, frequency, startdate, enddate, numberprocessed, totalnumber, type, lastfour, dateentered, amount)
					VALUES(NULL, 1, 1, 'weekly', '2014-01-31', '2015-01-31', 0, 52, 'visa', '1234', '2014-01-21', 14.99)
update contribution
	UPDATE `contributions` SET col=val WHERE cbid=1
delete contribution
	DELETE FROM `contributions` WHERE cbid=1
show contributions from a church
	SELECT `contributions`.* FROM `contributions` LEFT JOIN `users` ON (`contributions`.uid=`users`.uid) LEFT JOIN `families` ON (`users`.fid=`families`.fid) WHERE (`families`.cid=1)
	- joins users to contributions on uid, then joins families on fid, selects from contributions where families.cid = church
show contributions from a family
	SELECT `contributions`.*, `users`.* FROM `contributions` LEFT JOIN `users` ON (`contributions`.uid=`users`.uid) LEFT JOIN `families` ON (`users`.fid=`families`.fid) WHERE (`families`.fid=1)
show one contribution
	SELECT * FROM `contributions` WHERE cbid=1

[add transaction]
	INSERT INTO `transactions`(tid, cbid, processeddate, paidoutdate, paidoutamount, transactionamount, status)
				VALUES(NULL, 1, '2014-01-22', NULL, NULL, 10.00, 'pending')
	- (probably need a join to select the amount from contributions)
	- need to update contributions.cbid numberprocessed and last/next (need to add next)
[update transaction]
	UPDATE `transactions` SET col=val WHERE tid=1
[delete transaction]
	DELETE FROM `transactions` WHERE tid=1
show transactions from a church
	SELECT `transactions`.* FROM `transactions` LEFT JOIN `contributions` ON (`transactions`.cbid=`contributions`.cbid) LEFT JOIN `users` ON (`contributions`.uid=`users`.uid) LEFT JOIN `families` ON (`users`.fid=`families`.fid) WHERE (`families`.cid=1)

show transactions from a family
	SELECT `transactions`.*, `users`.*, `families`.* FROM `transactions` LEFT JOIN `contributions` ON (`transactions`.cbid=`contributions`.cbid) LEFT JOIN `users` ON (`contributions`.uid=`users`.uid) LEFT JOIN `families` ON (`users`.fid=`families`.fid) WHERE (`families`.fid=1)
	
show transactions from a user
	SELECT `transactions`.*, `users`.* FROM `transactions` LEFT JOIN `contributions` ON (`transactions`.cbid=`contributions`.cbid) LEFT JOIN `users` ON (`contributinos`.uid=`users`.uid) WHERE (`users`.uid=1)
	
show one transaction
	SELECT * FROM `transactions` WHERE (tid=1)
	
	
add address
	INSERT INTO `addresses`(adid, fid, isprimary, begindate, enddate, address1, address2, city, state, zip)
				VALUES(NULL, 1, "1", NULL, NULL, "1133 Maple Street", NULL, "Salem", "OH", "44460")
update address
	UPDATE `addresses` SET col=val WHERE adid=1
delete address
	DELETE FROM `addresses` WHERE adid=1
show addresses from a family
	SELECT `addresses`.*, `families`.* FROM `addresses` LEFT JOIN `families` ON (`addresses`.fid=`families`.fid) WHERE (`families`.fid=1)
show addresses from a church
	SELECT `addresses`.*, `families`.* FROM `addresses` LEFT JOIN `families` ON (`addresses`.fid=`families`.fid) WHERE (`families`.cid=1)
show one address
	SELECT * FROM `addresses` WHERE adid=1


