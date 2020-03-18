<?php

namespace Drupal\questline_engage\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\questline_engage;

class ArticleController extends ControllerBase {

    public function preview() {
        $article_id = \Drupal::request()->query->get('id');
        $article_type = \Drupal::request()->query->get('type');
        $api = new questline_engage\EngageApi();
        $article = $api->getArticlePreview($article_id, $article_type);
        $title = ($article != null) ? t('Engage Article Preview - ' . $article->Title)
                                    : t('Engage Article Not Found Or Is Not Supported');

        $response = [
          '#theme' => 'article_preview_template',
          '#title' => $title,
          '#article' => $article
        ];
        return $response;
    }
}