<?php
$file = (isset($argv[1]) ? $argv[1] : "data.csv");

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

$a = 0;
for ($i = 0; $i < count($values_1d); $i+=$num)
{
	for ($e = 0; $e < $num; $e++)
	{
		$values[$a][$e] = $values_1d[$i+$e];
	}
	$a++;
}

print_r($values);

?>