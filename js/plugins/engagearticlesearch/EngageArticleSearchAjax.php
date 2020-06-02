<?php

if (isset($_POST)) {
	$results = null;
	
	if (isset($_POST)) {		
		$search_keyword = isset($_POST['search_keyword']) ? $_POST['search_keyword'] : '';
		$page_index = isset($_POST['page_index']) ? $_POST['page_index'] : '0';
		$page_size = isset($_POST['page_size']) ? $_POST['page_size'] : '10';

		$api = new \Drupal\questline_engage\EngageApi();
		$results = $api->searchArticles($search_keyword, $page_index, $page_size);
	}
	
	echo json_encode($results);
	die();
}
