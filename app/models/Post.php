<?php

use Phalcon\Mvc\Model;

class Post extends Model
{
    public $id;
    public $date;
    public $content;
    public $title;
    public $slug;
    public $excerpt;
    public $status;
    public $comment_count;

    public function getSource()
    {
        return 'posts';
    }
}