<?php

namespace Drupal\questline_engage\Controller;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

class SearchController extends ControllerBase {

    public function dialog() {
        $response = [
          '#theme' => 'search_dialog_template'
        ];
        return $response;
    }

    public function search(){
        if (isset($_POST)) {
            $results = null;
            if (isset($_POST)) {
                $search_keyword = isset($_POST['search_keyword']) ? $_POST['search_keyword'] : '';
                $page_index = isset($_POST['page_index']) ? $_POST['page_index'] : '0';
                $page_size = isset($_POST['page_size']) ? $_POST['page_size'] : '10';

                $api = new \Drupal\questline_engage\Core\EngageApi();
                $results = $api->searchArticles($search_keyword, $page_index, $page_size);
            }
            return new JsonResponse($results);
        }
    }
}