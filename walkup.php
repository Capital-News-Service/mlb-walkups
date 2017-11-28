<?php

//Jake Gluck - Capital News Sevice

require('simple_html_dom.php');


$publicKey = "abd5326060a84f419516a058d0cf5be6";

$privateKey = "b007dd4bddbb411eb6d4c95219dfb093";

$accessToken = "BQC5jNcIzgPTsIa3DgeZqAgiHcXECbKTDEQVMhv04dANGtLtbaLSvgFky5zdQdeKM1lnx0bpxt_IHI7oHkqx1tKWFzSu2_APk9hzI67z1ACmcxsfLqihrBnHwRxVUiZopf4CYTiydG_GMJAkrpJMfQ";


//sends a curl get request
function performRequest($headers,$url) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	return $json = json_decode(curl_exec($ch), true);
}

//search by song name
function getSongID($song,$artist) {
	$song = str_replace(" ", "+", $song);
	global $accessToken;

	$url = "https://api.spotify.com/v1/search?q=$song&type=track";
	$headers = array();
	$headers[] = 'Accept: application/json';
	$headers[] = "Authorization: Bearer $accessToken";

	$data = performRequest($headers, $url);
	$tracks = $data["tracks"];
	if ($tracks != null){
		$items = $tracks["items"];

		if (sizeof($items) > 0){
			for ($i = 0; $i < sizeof($items);$i++){
				$obj = $items[$i];
				$artists = $obj["artists"];
				for ($x = 0; $x < sizeof($artists); $x++){
					$val = $artists[(string)$x];
					

					if ($artist == $val["name"]){

						return $obj["id"];

					}
				}

			}
		}
	}
	return "No Song";
}

//get album id given song id
function getAlbumOfSong($id) {
	global $accessToken;

	$url = "https://api.spotify.com/v1/tracks/$id";
	$headers = array();
	$headers[] = 'Accept: application/json';
	$headers[] = "Authorization: Bearer $accessToken";

	$data = performRequest($headers, $url);
	$album = $data["album"];
	$id = $album["id"];
	return $id;
}

//get release date of an album given the albums id
function getYearOfAlbum($id) {
	global $accessToken;

	$url = "https://api.spotify.com/v1/albums/$id";
	$headers = array();
	$headers[] = 'Accept: application/json';
	$headers[] = "Authorization: Bearer $accessToken";

	$data = performRequest($headers, $url);
	$date = $data["release_date"];
	if ($data['release_date_precision'] == "year"){
		return $date;
	}
	return substr($date, 0, 4);
}

//get song information, need song id
function getSong($id) {
	global $accessToken;

	$url = "https://api.spotify.com/v1/audio-features/$id";
	$headers = array();
	$headers[] = 'Accept: application/json';
	$headers[] = "Authorization: Bearer $accessToken";

	$data = performRequest($headers, $url);
	return $data;
}

//get song information, need song id
function getAllSongIds() {
	$artists = array();
	$songs = array();
	$genre = array();
	$ids = array();

	$count = 0;
	if (($handle = fopen("spotify_data.csv", "r")) !== FALSE) {
	  	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
	  		$artists[$count] = $data[0];
    		$songs[$count] = $data[1];
        	$genre[$count] = $data[2];
        	$ids[$count] = getSongID($songs[$count], $artists[$count]);
        	echo "$count\n";
        	echo "$songs[$count]\n";
        	echo "$artists[$count]\n";
        	echo "$genre[$count]\n";
        	echo "$ids[$count]\n";
        	$count++;
	  }

	  buildIdCSV($artists, $songs, $genre, $ids, "data_with_ids.csv");
	  fclose($handle);
	}


	//$id = getSongID($songs[20], $artists[20]);
	//echo "$id";
}

//get song information, need song id
function getAllSongInfo() {
	$artists = array();
	$songs = array();
	$genre = array();
	$ids = array();
	$danceability = array();
	$energy = array();
	$loudness = array();
	$tempo = array();
	$speechiness = array();
	$instrumentalness = array();
	$valence = array();
	$key = array();

	$count = 0;
	if (($handle = fopen("data_with_ids.csv", "r")) !== FALSE) {
	  	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

	  		$artists[$count] = $data[0];
    		$songs[$count] = $data[1];
        	$genre[$count] = $data[2];
        	$ids[$count] = $data[3];

        	 echo "$count\n";
        	echo "$songs[$count]\n";
        	echo "$artists[$count]\n";
        	echo "$ids[$count]\n";
        	if ($ids[$count] == "No Song"){
	        	$danceability[$count] = 0;
				$energy[$count] = 0;
				$loudness[$count] = 0;
				$tempo[$count] = 0;
				$speechiness[$count] = 0;
				$instrumentalness[$count] = 0;
				$valence[$count] = 0;
				$key[$count] = 0;
        	}else{
	        	$data = getSong($ids[$count]);
	        	while (sizeof($data) == 1){
	        		echo "BAD REQUEST\n";
	        		sleep(1);
	        		$data = getSong($ids[$count]);
	        	}
	        	$danceability[$count] = $data["danceability"];
				$energy[$count] = $data["energy"];
				$loudness[$count] = $data["loudness"];
				$tempo[$count] = $data["tempo"];
				$speechiness[$count] = $data["speechiness"];
				$instrumentalness[$count] = $data["instrumentalness"];
				$valence[$count] = $data["valence"];
				$key[$count] = $data["key"];
			}


        	// echo "$count\n";
        	// echo "$songs[$count]\n";
        	// echo "$artists[$count]\n";
        	// echo "$genre[$count]\n";
        	// echo "$ids[$count]\n";
        	// echo "$danceability[$count]\n";
        	// echo "$energy[$count]\n";
        	// echo "$loudness[$count]\n";
        	// echo "$tempo[$count]\n";
        	// echo "$speechiness[$count]\n";
        	// echo "$instrumentalness[$count]\n";
        	// echo "$valence[$count]\n";
        	// echo "$key[$count]\n";
        	$count++;
	  }

	  buildFullCSV($artists, $songs, $genre, $ids, $danceability, $energy, $loudness, $tempo, $speechiness, $instrumentalness, $valence, $key, "song_info.csv");
	  fclose($handle);
	}


	//$id = getSongID($songs[20], $artists[20]);
	//echo "$id";
}

//get release years of all songs
function getAllYears() {
	$name = array();
	$team = array();
	$songs = array();
	$artists = array();
	$genre = array();
	$ids = array();
	$danceability = array();
	$energy = array();
	$loudness = array();
	$tempo = array();
	$speechiness = array();
	$instrumentalness = array();
	$valence = array();
	$key = array();
	$position = array();
	$winperc = array();
	$count = 0;
	if (($handle = fopen("mlb-info.csv", "r")) !== FALSE) {
	  	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
	  		if ($data[0] != 'Name' && $data[0] != ''){
	        	$name[$count] = $data[0];
				$team[$count] = $data[1];
				$songs[$count] = $data[2];
				$artists[$count] = $data[3];
				$genre[$count] = $data[4];
				$ids[$count] = $data[5];
				$danceability[$count] = $data[6];
				$energy[$count] = $data[7];
				$loudness[$count] = $data[8];
				$tempo[$count] = $data[9];
				$speechiness[$count] = $data[10];
				$instrumentalness[$count] = $data[11];
				$valence[$count] = $data[12];
				$key[$count] = $data[13];
				$position[$count] = $data[14];
				$winperc[$count] =$data[17];

				if ($ids[$count] == "No Song"){
					$year[$count] = "No Song";
				}else{
					$albid = getAlbumOfSong($ids[$count]);
					while (sizeof($albid) == 0){
		        		echo "BAD REQUEST ALBUM\n";
		        		echo "$ids[$count]\n";
		        		sleep(1);
		        		$albid = getAlbumOfSong($ids[$count]);
		        	}
		        	$tempyear = getYearOfAlbum($albid);
		        	while (sizeof($tempyear) == 0){
		        		echo "BAD REQUEST YEAR\n";
		        		sleep(1);
		        		$tempyear = getAlbumOfSong($ids[$count]);
		        	}
		        	$year[$count] = $tempyear;
				}
				print("$count\n");
				echo "$name[$count]\n";
	        	$count++;
        	}
	  }

	  buildFullCSVDate($name, $team, $songs, $artists, $genre, $ids, $danceability, $energy, $loudness, $tempo, $speechiness, $instrumentalness, $valence, $key, $year, $position, $winperc, "mlb-info-with-date.csv");
	  fclose($handle);
	}
}

//builds csv from four arrays that are the same size
function buildIdCSV($songs, $artists, $genres, $ids, $fileName){
	$row = array();
	$file = fopen($fileName,"w");
	for ($entry = 0; $entry < sizeof($artists); $entry++){
		$row = [$songs[$entry], $artists[$entry], $genres[$entry], $ids[$entry]];
		fputcsv($file, $row);
	}
}

//builds Full CSV of songs
function buildFullCSV($artists, $songs, $genres, $ids, $danceability, $energy, $loudness, $tempo, $speechiness, $instrumentalness, $valence, $key, $year, $fileName){
	$row = array();
	$file = fopen($fileName,"w");
	for ($entry = 0; $entry < sizeof($artists); $entry++){
		$row = [$songs[$entry], $artists[$entry], $genres[$entry], $ids[$entry], $danceability[$entry], $energy[$entry], $loudness[$entry], $tempo[$entry], $speechiness[$entry], $instrumentalness[$entry], $valence[$entry], $key[$entry]];
		fputcsv($file, $row);
	}
}

//builds Full CSV of songs with release date
function buildFullCSVDate($name, $team, $songs, $artists, $genre, $ids, $danceability, $energy, $loudness, $tempo, $speechiness, $instrumentalness, $valence, $key, $year, $position, $winperc, 
	$fileName){
	$row = array();
	$file = fopen($fileName,"w");
	for ($entry = 0; $entry < sizeof($artists); $entry++){
		$row = [$name[$entry], $team[$entry], $songs[$entry], $artists[$entry], $genre[$entry], $ids[$entry], $danceability[$entry], $energy[$entry], $loudness[$entry], $tempo[$entry], $speechiness[$entry], $instrumentalness[$entry], $valence[$entry], $key[$entry], $year[$entry], $position[$entry], $winperc[$entry]];
		fputcsv($file, $row);
	}
}



//("0GuLonchqLiGK1zzcrp2ye");

//getAllSongIds();

//getAllSongInfo();

getAllYears();
?>
