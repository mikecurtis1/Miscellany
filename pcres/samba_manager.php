#!/usr/bin/php
<?php 

// config 

date_default_timezone_set('EST');

// functions

function get_current_usernames (){
	$now = time();
	$link = mysql_connect('host', 'username', 'passwd');
	mysql_select_db('pcres',$link);
	$sql = "
	SELECT username
	FROM `pcres` 
	WHERE start <= NOW() and stop > NOW()
	";
	if(($result = mysql_query($sql)) !== FALSE){
		$current_usernames = array();
		while($row = mysql_fetch_assoc($result)){
			$current_usernames[] = $row['username'];
		}
		mysql_close($link);
		return $current_usernames;
	} else {
		return (boolean) FALSE;
	}
}

function get_inactive_usernames ($threshold=300){
	$link = mysql_connect('host', 'username', 'passwd');
	mysql_select_db('pcres',$link);
	#WHERE (start <= NOW() and stop > NOW())
	#WHERE DATE(stop) = DATE(NOW()) AND samba = 'active'
	$sql = "
	SELECT pcres_id, username 
	FROM `pcres` 
	WHERE (start <= NOW()) 
	AND samba = 'active' 
	AND (TIMESTAMPDIFF(SECOND,last_checkin,NOW()) >= {$threshold}) 
	";
	if(($result = mysql_query($sql)) !== FALSE){
		$inactive_usernames = array();
		while($row = mysql_fetch_assoc($result)){
			$inactive_usernames[$row['pcres_id']] = $row['username'];
		}
		mysql_close($link);
		#print_r($inactive_usernames);
		return $inactive_usernames;
	} else {
		return (boolean) FALSE;
	}
}

function update_inactive_usernames ($rows=array()){
	$link = mysql_connect('host', 'username', 'passwd');
	mysql_select_db('pcres',$link);
	$where_clause = 'pcres_id = \''.implode('\' OR pcres_id = \'',array_keys($rows)).'\'';
	$sql = "
		UPDATE pcres 
		SET `samba` = 'inactive'
		WHERE {$where_clause} 
	";
	if(mysql_query($sql) === TRUE){
		mysql_close($link);
		return (boolean) TRUE;
	} else {
		mysql_close($link);
		return (boolean) FALSE;
	}
}

/*function update_last_checkin ($rows=array()){
	#$date = date("Y-m-d H:i:s");
	$link = mysql_connect('host', 'username', 'passwd');
	mysql_select_db('pcres',$link);
	$where_clause = 'username = \''.implode('\' OR username = \'',$rows).'\'';
	$sql = "
		UPDATE pcres 
		SET `last_checkin` = NOW() 
		WHERE {$where_clause} 
	";
	#echo $sql."\n";
	if(mysql_query($sql) === TRUE){
		mysql_close($link);
		return (boolean) TRUE;
	} else {
		mysql_close($link);
		return (boolean) FALSE;
	}
}*/

// check which Linux accounts we will accept to pass to userdel, don't delete important accounts!
function filter_linux_accounts_for_userdel (&$accounts=array()){
	foreach($accounts as $key => $account){
		if(!preg_match('/^\w\w\w\w\d\d$/',$account,$matches)){ // filter on the username/password pattern we use in schedule manager
			unset($accounts[$key]);
		}
		$cmd = "grep '^".$account."\:' /etc/passwd | cut -d: -f3"; // this will get the account UID
		echo date('c') . " - SAMBA Manager: CMD:{$cmd}:\n";
		exec($cmd,$uid);
		if(!(isset($uid[0]) && $uid[0] > 499)){ // must match a linux user account with uid 500+
			unset($accounts[$key]);
		}
		unset($uid);
	}
	return $accounts;
}

// begin Samba procedure

echo date('c') . " - SAMBA Manager: Starting\n";

// create a string of linux machine group names based on regex
$cmd = "grep '^LIB-[0-9]\{3\}-.\{7\}\\$' /etc/group | cut -d: -f1";
echo date('c') . " - SAMBA Manager: CMD:{$cmd}:\n";
exec($cmd,$machine_groups);
$linux_group_string = implode(",",$machine_groups);
echo date('c') . " - SAMBA Manager: MACHINE GROUPS: " . implode('|',$machine_groups) . "\n";

// get current scheduled users
$current_scheduled_users_array = get_current_usernames();
echo  date('c') . " - SAMBA Manager: CURRENT SCHEDULED USERS: " . implode('|',$current_scheduled_users_array) . "\n";

// get existing Linux users in Samba machine groups
$cmd = "grep '^LIB-[0-9]\{3\}-.\{7\}\\$' /etc/group | cut -d: -f4 | tr , '\n'";
echo date('c') . " - SAMBA Manager: CMD:".str_replace("\n","\\n",$cmd).":\n";
exec($cmd,$users_in_machine_groups);
$users_in_machine_groups = array_filter($users_in_machine_groups); // remove any blank items
$existing_linux_users = array_unique($users_in_machine_groups);
# start test
##$existing_linux_users[] = 'root'; # don't do this if you continue to the USERDEL section
#$existing_linux_users[] = 'rootish';
#$existing_linux_users[] = 'nobody';
# end test
filter_linux_accounts_for_userdel($existing_linux_users);
sort($existing_linux_users);
echo  date('c') . " - SAMBA Manager: EXISTING LINUX USERS: " . implode('|',$existing_linux_users) . "\n";

// determine users to add
$add = array_diff($current_scheduled_users_array,$existing_linux_users);
echo  date('c') . " - SAMBA Manager: ADD USERS: " . implode('|',$add) . "\n";

// determine users to delete
$del = array_diff($existing_linux_users,$current_scheduled_users_array);
echo  date('c') . " - SAMBA Manager: DELETE USERS: " . implode('|',$del) . "\n";

// check for 'inactive' accounts, those that haven't checked it after a certain threshold
$inactive_linux_users = get_inactive_usernames(120);
if(count($inactive_linux_users)>0){
	echo  date('c') . " - SAMBA Manager: INACTIVE LINUX USERS: " . implode('|',$inactive_linux_users) . "\n";
	$update_inactive_usernames = update_inactive_usernames($inactive_linux_users);
	echo date('c') . " - SAMBA Manager: MySQL updated inactive users: boolean=" . $update_inactive_usernames . ", count=" . count($inactive_linux_users) . "\n";
	$del = array_merge($del,$inactive_linux_users);
}

// add Linux and Samba accounts
if(count($add)>0){
	#update_last_checkin($add);
	foreach($add as $user){
		// This is insecure because the password will show up in the process table and can be seen with tools like ps
		$cmd = "useradd -s /bin/false -d /dev/null ".$user." -p `openssl passwd ".$user."`";
		echo date('c') . " - SAMBA Manager: CMD:{$cmd}:\n";
		passthru($cmd);
		$cmd = "usermod -G ".$linux_group_string." ".$user;
		echo date('c') . " - SAMBA Manager: CMD:{$cmd}:\n";
		passthru($cmd);
		$cmd = "(echo ".$user."; echo ".$user.") | smbpasswd -a -s ".$user;
		echo date('c') . " - SAMBA Manager: CMD:{$cmd}:\n";
		passthru($cmd);
	}
	echo  date('c') . " - SAMBA Manager: Added users.\n";
} else {
	echo  date('c') . " - SAMBA Manager: Did NOT add users.\n";
}

// delete Linux and Samba accounts
if(count($del)>0){
	foreach($del as $user){
	// /etc/group and Samba seem to be updated correctly, but I get warnings about shell and path
		if($user != 'root' && $user != 'bin' && $user != 'daemon' && $user != 'adm' && $user != 'mail' && $user != 'ftp' && $user != 'nobody' && $user != 'mysql' && $user != 'apache' && $user != 'library'){
			$cmd = "smbpasswd -x ".$user;
			echo date('c') . " - SAMBA Manager: CMD:{$cmd}:\n";
			passthru($cmd); // deleted smb account before linux account
			$cmd = "userdel -f -r ".$user;
			echo date('c') . " - SAMBA Manager: CMD:{$cmd}:\n";
			passthru($cmd); 
		}
	}
	echo  date('c') . " - SAMBA Manager: Deleted users.\n";
} else {
	echo  date('c') . " - SAMBA Manager: Did NOT delete users.\n";
}

echo  date('c') . " - SAMBA Manager: FINISHED\n";
echo "------------------------------------------\n";

?>

