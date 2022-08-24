<?php

function DateDiff($endtime, $starttime, $unit='')
{
	switch($unit)
	{
		case 's':
			$rettime = 1;
			break;
		case 'i':
			$rettime = 60;
			break;
		case 'h':
			$rettime = 3600;
			break;
		case 'd':
			$rettime = 86400;
			break;
		default:
			$rettime = 1;
	}
	if($endtime && $starttime)
	{
		return (float)($endtime-$starttime) / $rettime;
	}
	else
	{
		return false;
	}
}
?>