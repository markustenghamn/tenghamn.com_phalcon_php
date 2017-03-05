<?php

use Phalcon\Mvc\Controller;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Http\Request;
use Phalcon\Filter;

class ApiController extends Controller
{

    public function indexAction()
    {

    }

    public function getpostAction($date) {
        die($date);
    }

    public function fetchpageAction() {
        $request = new Request();
        if ($this->request->isPost()) {
            $id = $request->getPost("article");
            if ($id > 0) {
                $query = $this->modelsManager->createQuery("SELECT * FROM Post ORDER BY id ASC LIMIT 1 OFFSET " . $id);
                $post = $query->execute();
                if (isset($post)) {
                    foreach ($post as $p) {
                        $output = '<article class="post">
            <div class=wrapper>
                <h3 class=post-title><a href="' . "/" . $p->slug . '">' . $p->title . '</a></h3>
                <p class=post-meta>Posted: <span class="date">' . DOMHelper::randomDate($p->date) . '</span></p>
                <div class=post-body>' . $p->excerpt . ' <a href="'."/".$p->slug.'">[Read more]</a></div>
                <hr>
            </div>
        </article>';
                        return $output;
                    }
                }
            } else {
                return "Not authorized";
            }
        } else {
            return "Woops something went wrong!";
        }
        return "";
    }


    public function compressimagesAction() {
        $dir = str_replace("/app/controllers", "", __DIR__."/public/assets/images/*.jpg");
        $images = glob( $dir );
        foreach( $images as $image ) {
            if (!(strpos($image, "-min") !== false)) {
                echo $image . "</br>";
                $this->compress($image, str_replace(".jpg", "-min.jpg", $image), 50);
            }
        }
        $dir = str_replace("/app/controllers", "", __DIR__."/public/assets/images/*.png");
        $images = glob( $dir );
        foreach( $images as $image ) {
            if (!(strpos($image, "-min") !== false)) {
                echo $image . "</br>";
                $this->compress($image, str_replace(".png", "-min.jpg", $image), 50);
            }
        }
        $dir = str_replace("/app/controllers", "", __DIR__."/public/assets/images/*.gif");
        $images = glob( $dir );
        foreach( $images as $image ) {
            if (!(strpos($image, "-min") !== false)) {
                echo $image . "</br>";
                $this->compress($image, str_replace(".gif", "-min.jpg", $image), 50);
            }
        }
        return "Images compressed";
    }

    public function emptydatabaseAction() {
        $query = $this->modelsManager->createQuery("DELETE FROM Post WHERE 1");
        $query->execute();
        $query2 = $this->modelsManager->createQuery("DELETE FROM Comment WHERE 1");
        $query2->execute();
        return "Database emptied";
    }

    public function syncdatabaseAction() {

        $db = $this->connectRemoteDB();
        $statement = $db->prepare("select ID, post_content, post_date, post_title, post_excerpt, post_name, comment_count from wp_posts where post_type = :type AND post_parent = :parent AND post_status = :status ORDER BY post_date DESC");
        $statement->execute(array(':type' => "post", ":parent" => 0, ':status' => "publish"));
        $rows = $statement->fetchAll();
        // post_name is the slug
        // Fetch comments if comment_count > 0
        $res = array();
        foreach ($rows as $k=>$r) {
            $tmp = array();
            $post = new Post();
            foreach ($r as $m=>$n) {
                if (!is_numeric($m) || $m == "") {
                    $tmp[$m] = utf8_encode($n);
                    if ($m == "post_content") {
                        $text = str_replace("<pre>",'<pre class="language-php">', $n);

                        try {
                            //Download all images
                            $doc = new DOMDocument();
                            @$doc->loadHTML($text);

                            $tags = $doc->getElementsByTagName('img');
                            foreach ($tags as $t) {
                                $m = $t->getAttribute('src');
                                echo $m;
                                ///var/www/tenghamn_com/app/controllers
                                $filepath = $this->filePath($m);
                                $imgname = $filepath['filename']."-min.".$filepath['extension'];
                                $imgpath = str_replace("/app/controllers", "", __DIR__) . '/public/assets/images/' . $imgname;
                                if (!file_exists($imgpath)) {
                                    file_put_contents($imgpath, file_get_contents($m));
                                }
                                $text = str_replace('src="'.$m, 'src="/assets/images/clear.jpg" data-src="'.'/assets/images/' . $imgname, $text);
                            }
                        } catch (Exception $e) {
                            echo $e->getMessage()." Trace: ".$e->getTraceAsString();
                        }

                        $post->content = utf8_encode($text);
                        $post->excerpt = $this->get_words(strip_tags(utf8_encode($n)), 100)."...";


                    } else if ($m == "post_date") {
                        $post->date = utf8_encode($n);
                    } else if ($m == "post_title") {
                        $post->title = utf8_encode($n);
                    } else if ($m == "post_name") {
                        $post->slug = utf8_encode($n);
                    } else if ($m == "comment_count") {
                        $post->comment_count = utf8_encode($n);
                    }
                }
            }
            $post->status = "published";
            if ($post->create() === false) {
                echo "Umh, We can't store posts right now: \n"."<br/>";

                $messages = $post->getMessages();

                foreach ($messages as $message) {
                    echo $message, "\n"."<br/>";
                }
            } else {
                echo "Great, a new post was created successfully!"."<br/>";
                $statement = $db->prepare("select comment_ID, comment_post_ID, comment_author, comment_author_email, comment_author_url, comment_author_IP, comment_date, comment_content, comment_approved, comment_type, comment_parent from wp_comments where comment_post_ID = :postid AND comment_approved = :approved ORDER BY comment_date ASC");
                $statement->execute(array(':postid' => $r['ID'], ":approved" => 1));
                $comments = $statement->fetchAll();
                echo "<b>Comment count for ".$r['ID']."</b>: ".count($comments)." \n"."<br/>";
                foreach ($comments as $c) {
                    $comment = new Comment();
                    $comment->post_id = $post->id;
                    $comment->parent_id = $c['comment_parent'];
                    $comment->author_name = $c['comment_author'];
                    $comment->author_url = $c['comment_author_url'];
                    $comment->author_email = $c['comment_author_email'];
                    $comment->author_ip = $c['comment_author_IP'];
                    $comment->date = $c['comment_date'];
                    $comment->content = $c['comment_content'];
                    $comment->approved = $c['comment_approved'];
                    $comment->type = $c['comment_type'];
                    if ($comment->create() === false) {
                        echo "Umh, We can't store comments right now: \n"."<br/>";

                        $messages = $comment->getMessages();

                        foreach ($messages as $message) {
                            echo $message, "\n"."<br/>";
                        }
                    } else {
                        echo "Great, a new comment was created successfully!" . "<br/>";
                    }
                }
            }
            $res[] = $tmp;
        }
        return "Database synced";
    }

    private function connectRemoteDB() {
        $config = [
            'host'     => 'localhost',
            'dbname'   => '',
            'port'     => 3306,
            'username' => '',
            'password' => ''
        ];

        $connection = new Mysql($config);
        return $connection;
    }

    private function get_words($sentence, $count = 10) {
        preg_match("/(?:\w+(?:\W+|$)){0,$count}/", $sentence, $matches);
        return $matches[0];
    }

    private function compress($source, $destination, $quality) {

        try {
            if ($info = getimagesize($source)) {
                $image = "";

                if ($info['mime'] == 'image/jpeg') {
                    $image = imagecreatefromjpeg($source);
                } elseif ($info['mime'] == 'image/gif') {
                    $image = imagecreatefromgif($source);
                } elseif ($info['mime'] == 'image/png') {
                    $image = imagecreatefrompng($source);
                }

                imagejpeg($image, $destination, $quality);

                return $destination;
            }
        } catch (Exception $e) {

        }
        return $destination;
    }

    private function filePath($filePath)
    {
        $fileParts = pathinfo($filePath);

        if(!isset($fileParts['filename']))
        {$fileParts['filename'] = substr($fileParts['basename'], 0, strrpos($fileParts['basename'], '.'));}

        return $fileParts;
    }

}