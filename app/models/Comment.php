<?php

use Phalcon\Mvc\Model;

class Comment extends Model
{
    public $id;
    public $post_id;
    public $parent_id;
    public $author_name;
    public $author_url;
    public $author_email;
    public $author_ip;
    public $date;
    public $content;
    public $approved;
    public $type;
    public $spam;
    public $token;

    public function getSource()
    {
        return 'comments';
    }
}