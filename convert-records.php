<?php

//DB infos
$host = '127.0.0.1';
$dbname = 'teeworlds';
$user = 'root';
$password = '';
$prefix = 'record'; //prefix can't be empty
$maxNameLength = 16;

//records infos
$dirRecords = 'records'; //path to records files

//verbose
$nbMap = 0;
$nbScore = 0;
$nbScoreByMap = 0;


echo "Start... \n";
try {
	$dsn = 'mysql:dbname='.$dbname.';host='.$host;
    $dbh = new PDO($dsn, $user, $password);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	
	$dir = opendir($dirRecords);
	while($file = readdir($dir)){
		if(pathinfo($file, PATHINFO_EXTENSION) == 'dtb'){
			$nbScoreByMap = 0;
			$map = substr($file, 0, -11);
			$map = str_replace(' ', '_', $map);
			echo $map."\n";
			$query = 'CREATE TABLE IF NOT EXISTS '.$prefix.'_'.$map.'_race (Name VARCHAR('.$maxNameLength.') NOT NULL, Timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , Time FLOAT DEFAULT 0, cp1 FLOAT DEFAULT 0, cp2 FLOAT DEFAULT 0, cp3 FLOAT DEFAULT 0, cp4 FLOAT DEFAULT 0, cp5 FLOAT DEFAULT 0, cp6 FLOAT DEFAULT 0, cp7 FLOAT DEFAULT 0, cp8 FLOAT DEFAULT 0, cp9 FLOAT DEFAULT 0, cp10 FLOAT DEFAULT 0, cp11 FLOAT DEFAULT 0, cp12 FLOAT DEFAULT 0, cp13 FLOAT DEFAULT 0, cp14 FLOAT DEFAULT 0, cp15 FLOAT DEFAULT 0, cp16 FLOAT DEFAULT 0, cp17 FLOAT DEFAULT 0, cp18 FLOAT DEFAULT 0, cp19 FLOAT DEFAULT 0, cp20 FLOAT DEFAULT 0, cp21 FLOAT DEFAULT 0, cp22 FLOAT DEFAULT 0, cp23 FLOAT DEFAULT 0, cp24 FLOAT DEFAULT 0, cp25 FLOAT DEFAULT 0, KEY Name (Name)) CHARACTER SET utf8 ;';
			$dbh->exec($query);
			$handle = fopen($dirRecords.'/'.$file, "r");
			if ($handle) {
				while (($name = fgets($handle, 4096)) !== false && ($time = fgets($handle, 4096)) !== false && ($checkpoints = fgets($handle, 4096)) !== false){
					$name = trim($name);
					$time = trim($time);
					
					if(isset($argv[1]) && $argv[1] == '-v')
						echo $name.' '.$time.' '.$checkpoints."\n";
					
					$checkpoints = explode(' ',$checkpoints);
					unset($checkpoints[25]);
					$values = array_merge(array($name, date('Y-m-d h:i:s' ,time()), $time), $checkpoints);
					$query = 'INSERT INTO '.$prefix.'_'.$map.'_race VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
					$sth = $dbh->prepare($query);
					$sth->execute($values);
					$nbScoreByMap++;
				}
				fclose($handle);
			}
			echo "End \n";
			echo $nbScoreByMap. "score(s) added \n";
			$nbScore += $nbScoreByMap;
			$nbMap++;
		}
	}
	echo $nbMap. "map(s) added \n";
	echo $nbScore. "score(s) added \n";
	
} catch (PDOException $e) {
    die ('Error : ' . $e->getMessage());
}





