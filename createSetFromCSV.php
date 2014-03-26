<?php
global $setID;

$link = mysql_connect("localhost", "dcsp04", "Fl!p2014");

mysql_select_db("dcsp04", $link);




$file = (isset($argv[1]) ? $argv[1] : "data.csv");
$setName = (isset($argv[2]) ? $argv[2] : "Set" . rand());

$row = 1;
$headers = array();
$values_1d = array();
$values = array();
$values_count = 0;
$values_offset = 0;
if (($handle = fopen($file, "r")) !== FALSE)
{
	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
	{
		$num = count($data);
		
		if ($row == 1)
		{	
			for ($i = 0; $i < $num; $i++)
			{
					$headers[] = $data[$i];
			}
		} else
		{
			for ($c = 0; $c < $num; $c++)
			{
				$values_1d[] = $data[$c];
			}	
		}
		$row++;
	}
	fclose($handle);
}



mysql_query(setSQL($setName), $link);
mysql_query(flashcardsSQL($values_1d, $setID, $num), $link);







function setSQL($name)
{
	global $setID;
	$setID = rand();
	$date = date("Y-m-d H:i:s");
	return "INSERT INTO `set` (`id`, `name`, `creator`, `visibility`, `dateCreated`) VALUES ('{$setID}', '{$name}', '00000', 'pri', '{$date}');";
}


function flashcardsSQL($array, $setID, $numCards)
{
	$return = "INSERT INTO `dcsp04`.`flashcards` (`set`, `id`, `side1`, `side2`) VALUES ";
	
	for ($i = 0; $i < count($array); $i+=$numCards)
	{
		$s1 = $array[$i];
		$s2 = $array[$i+1];
		$id = $i/2;
		$return .= "('{$setID}', '{$id}', '{$s1}', '{$s2}'), ";
	}
	$return = substr($return, 0, -2);
	$return .= ";";
	return $return;
}

?>