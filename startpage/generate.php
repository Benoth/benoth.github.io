<?php

class Link
{
    public function __construct ($name, $link, $accessKey = null, $icon = null)
    {
        $this->name      = $name;
        $this->link      = $link;
        $this->accessKey = $accessKey;
        $this->icon      = $icon;
    }

    public function renderButtons()
    {
        $html = '<a class="btn btn-link link-icon '.$this->slugifyName().'" href="'.$this->link.'" accesskey="'.$this->accessKey.'">';
        if (!empty($this->icon)) {
            $html .= $this->icon;
        } else {
            $html .= '<div class="icon-placeholder"></div>';
        }
        $html .= '<div class="name">'.$this->name.'</div>';
        $html .= '</a>';

        return $html;
    }

    public function slugifyName()
    {
        $text = preg_replace('~[^\pL\d]+~u', '-', $this->name);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);
        if (empty($text)) {
            return 'none';
        }

        return $text;
    }
}

$mainLine = [
    new Link('GMail',       'https://mail.google.com/',      'm', '<i class="fab fa-google"></i>'),
    new Link('Calendar',    'https://calendar.google.com/',  'c', '<i class="fas fa-calendar-alt"></i>'),
    new Link('Trello',      'https://trello.com/',           't', '<i class="fab fa-trello"></i>'),
    new Link('Facebook',    'https://www.facebook.com/',     'f', '<i class="fab fa-facebook"></i>'),
    new Link('Twitter',     'https://twitter.com/',          'w', '<i class="fab fa-twitter"></i>'),
    new Link('Hubic',       'https://hubic.com/fr/',         'h', '<i class="fas fa-file-alt"></i>'),
    new Link('GistNote',    'https://gistnote.github.io/',   'n', '<i class="fas fa-sticky-note"></i>'),
    new Link('KeeWeb',      'https://app.keeweb.info/',      'k', '<i class="fas fa-key"></i>'),
    new Link('YouTube',     'https://www.youtube.com/',      'y', '<i class="fab fa-youtube"></i>'),
    new Link('Twitch',      'https://www.twitch.tv/',        'v', '<i class="fab fa-twitch"></i>'),
    new Link('Netflix',     'https://www.netflix.com/',      'n', '<i class="fas fa-film-alt"></i>'),
    new Link('Prime Video', 'https://www.primevideo.com/',   'a', '<i class="fab fa-amazon"></i>'),
    new Link('Reddit PHP',  'https://www.reddit.com/r/PHP/', 'r', '<i class="fab fa-reddit-alien"></i>'),
];

$html = file_get_contents(__DIR__.'/index.html.template');

$mainLine = array_map(function($link) {
    return $link->renderButtons();
}, $mainLine);
$html = str_replace('{{ mainLine }}', implode(PHP_EOL, $mainLine), $html);

file_put_contents(__DIR__.'/index.html', $html);

exit($html);
