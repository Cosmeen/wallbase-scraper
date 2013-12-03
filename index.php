<!DOCTYPE html>
<pre>
<?php

ini_set('max_execution_time', 0);

include "Curl.class.php";

/*
preg_match("/(\d+)/", $url, $output);
$url = "http://wallbase.cc/wallpaper/". $output[0];
*/

$url = "http://wallbase.cc/wallpaper/1";

$i = 1;
$max = 5000;
// how many wallpapers to scrape

while ($i <= $max) {

	preg_match("/(.*?)(\d+)$/",$url,$matches);
	$url = $matches[1].($matches[2]+1);
	$i++;

	sleep(1);
	$curl = new Curl();
	$curl->setUserAgent('Mozilla/5.0 (Windows NT 6.2; WOW64; rv:24.0) Gecko/20100101 Firefox/24.0');
	$curl->get($url);



	if ($curl->error) {

		echo  $i .". 00" .$curl->error . ": ". $url . "\n";

		$file = fopen("403.txt","a");
		fwrite($file, "\n" . $url);
		fclose($file);
	}

	else {
		$input_lines = strip_tags($curl->response, "<img>");

		if ( preg_match_all("/(<img[^>]*>)/", $input_lines, $output_array) )  {
			
			$output = implode("\n", $output_array[0] );
			// echo  $output;

			if (preg_match_all("/(wallpapers.wallbase.cc\/)(\w+)(-\w+)?(\/)(wallpaper-(\d+))(.)(jpg?|png?|gif)/", $output, $output_array)) {
				$wallp = "http://" . $output_array[0][0];

				$wall = array( 'id' => NULL, 'url' => $wallp );
				$db->query("INSERT INTO `urls` SET ?u", $wall);

				$file = fopen("files.txt","a");
				fwrite($file, "\n" . $wallp);
				fclose($file);


				echo $i . ". 200: " . $wallp . "\n";
			} // regex ok

			else {
				echo  "bad?: " . $output . "\n";
			} // regex fails?


		} // if regex image

		else {
			echo $curl->response;	
		}

	}// if no error

	

	if ($i == $max) {
		$sc = '<iframe width="100%" height="166" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/118554093&amp;color=897364&amp;auto_play=true&amp;show_artwork=false"></iframe>';
		echo $sc . "\n";
		echo "job finished ;)";
	}

} // while loop

?>