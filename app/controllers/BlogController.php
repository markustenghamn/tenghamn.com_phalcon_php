<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Request;
use Phalcon\Filter;

class BlogController extends Controller
{

    private $_transport;

    public function indexAction()
    {
        $query = $this->modelsManager->createQuery("SELECT * FROM Post LIMIT 3");
        $posts  = $query->execute();
        $this->view->posts = $posts;
        $output = DOMHelper::header();
        $output .= '<body>
<main class="page-wrapper">
    <nav>
        <ul>
            <li>
                <header>
                    <h1>
                        <a href="/">Markus Tenghamn <span class="divider">-</span> <span class="typer"><span class="caption" id="caption">Backend PHP Developer</span></span></a>
                    </h1>
                </header>
            </li>
            <li><a href="/services/">Services</a></li>
            <li><a href="/blog/">Blog</a></li>
            <li><a href="/contact/">Contact</a></li>
        </ul>
        <div class="handle">Menu</div>
    </nav>
    <section class="post-list">
    <h2>Articles</h2>';

		foreach ($posts as $p) {
			$output .= '<article class="post">
            <div class=wrapper>
                <h3 class=post-title><a href="'."/".$p->slug.'">'.$p->title.'</a></h3>
                <p class=post-meta>Posted: <span class="date">'.DOMHelper::randomDate($p->date).'</span></p>
                <div class=post-body>'.$p->excerpt.' <a href="'."/".$p->slug.'">[Read more]</a></div>
            </div>
        </article>';

		}
		$output .= '</section>
    <div class="loader">
        <div class="circle"></div>
    </div>';
        $output .= DOMHelper::footer();
		return DOMHelper::sanitize_output($output);
    }

    public function showpostAction($slug) {
        $themsg = "";
        $post= Post::findFirst(
            [
                "conditions" => "slug LIKE '".$slug."'",
                "limit" => 1,
            ]
        );
        if ($post) {
            $this->post = $post;

            $request = new Request();
            if ($this->request->isPost()) {
                $name = $request->getPost("name");
                $email = $request->getPost("email");
                $text = $request->getPost("text");
                $honey = $request->getPost("honey");
                $url = $request->getPost("url");
                if (isset($honey) && strlen($honey) == 0) {
                    $comment = new Comment();
                    $comment->post_id = $this->post->id;
                    $comment->parent_id = 0;
                    $comment->author_name = htmlentities(strip_tags($name));
                    $comment->author_url = "";
                    $comment->author_email = htmlentities(strip_tags($email));
                    $comment->author_ip = htmlentities(strip_tags($_SERVER['REMOTE_ADDR']));
                    $comment->date = date('Y-m-d H:i:s', time());
                    $comment->content = htmlentities(strip_tags($text));
                    $comment->approved = 0;
                    $comment->type = "";
                    $token = bin2hex(openssl_random_pseudo_bytes(32));
                    $comment->token = $token;
                    $akismet = new Akismet("tenghamn.com", "64dde15d76f8");
                    $akismet->setUserIP($_SERVER['HTTP_REFERER']);
                    $akismet->setCommentAuthor($name);
                    $akismet->setCommentAuthorEmail($email);
                    $akismet->setCommentContent($text);
                    $akismet->setCommentUserAgent($_SERVER['HTTP_USER_AGENT']);
                    $akismet->setReferrer($_SERVER["HTTP_REFERER"]);
                    $akismet->setCommentType("comment");
                    $akismet->setCommentAuthorURL($url);
                    $spam = $akismet->isCommentSpam();
                    if (strpos(strtolower($text . $name . $email), "car insurance") !== false) {
                        $spam = true;
                    } else if (strpos(strtolower($text . $name . $email), "cialis") !== false) {
                        $spam = true;
                    } else if (strpos(strtolower($text . $name . $email), "tadalafil") !== false) {
                        $spam = true;
                    }
                    $comment->spam = $spam;
                    if ($comment->create() === false) {
                        $themsg .= "Umh, We can't store comments right now: \n" . "<br/>";

                        $messages = $comment->getMessages();

                        foreach ($messages as $message) {
                            $themsg = $message .= "\n" . "<br/>";
                        }
                    } else {
                        $themsg .= "Great, a new comment was created successfully!" . "<br/>";
                        if ($comment->spam == 0) {
                            $to = 'markus@tenghamn.com';
                            $subject = 'New comment on Tenghamn.com';
                            $message = "Comment on: " . $post->title . "<br/>";
                            $message .= "From: " . $name . " (" . $email . ")<br/>" . $text . '<br/>';
                            $message .= 'View it: <a href="http://tenghamn.com/' . $slug . '#comment-' . $comment->id . '">http://tenghamn.com/' . $slug . '#comment-' . $comment->id . '</a><br/>';
                            $message .= 'Spam it: <a href="http://tenghamn.com/' . $slug . '#comment-' . $comment->id . '?token=' . $comment->token . '&action=spam' . '">http://tenghamn.com/' . $slug . '#comment-' . $comment->id . '?token=' . $comment->token . '&action=spam' . '</a><br/>';
                            $message .= 'Trash it: <a href="http://tenghamn.com/' . $slug . '#comment-' . $comment->id . '?token=' . $comment->token . '&action=trash' . '">http://tenghamn.com/' . $slug . '#comment-' . $comment->id . '?token=' . $comment->token . '&action=trash' . '</a><br/>';
                            $message .= 'Approve it: <a href="http://tenghamn.com/' . $slug . '#comment-' . $comment->id . '?token=' . $comment->token . '&action=approve' . '">http://tenghamn.com/' . $slug . '#comment-' . $comment->id . '?token=' . $comment->token . '&action=approve' . '</a><br/>';

                            // Create the message
                            $message = Swift_Message::newInstance()
                                ->setSubject($subject)
                                ->setTo($to)
                                ->setFrom(array(
                                    $email => $name
                                ))
                                ->setBody($message, 'text/html');
                            if (!$this->_transport) {
                                $this->_transport = Swift_SmtpTransport::newInstance(
                                    "smtp.gmail.com",
                                    "587",
                                    "TLS"
                                )
                                    ->setUsername("markus@tenghamn.com")
                                    ->setPassword("Y~2!mn623p5qN9i");
                            }
                            // Create the Mailer using your created Transport
                            $mailer = Swift_Mailer::newInstance($this->_transport);
                            $mailer->send($message);
                        }
                    }
                }
            }


            $output = DOMHelper::header($this->post->title);
            $output .= '
<body>
<main class="page-wrapper">
    <nav>
        <ul>
            <li>
                <header>
                    <h1>
                        <a href="/">Markus Tenghamn <span class="divider">-</span> '.$this->post->title.'</a>
                    </h1>
                </header>
            </li>
            <li><a href="/services/">Services</a></li>
            <li><a href="/blog/">Blog</a></li>
            <li><a href="/contact/">Contact</a></li>
        </ul>
        <div class="handle">Menu</div>
    </nav>';
            if (strlen($themsg) > 0) {
                $output .= "<div class=alert><div class=wrapper>".$themsg."</div></div>";
            }
    $output .= '<section class="post">';
                $output .= '<article class="post">
            <div class=wrapper>
                <h2 class=post-title>' . $this->post->title . '</h2>
                <p class=post-meta>Posted: <span class="date">' . DOMHelper::randomDate($this->post->date) . '</span></p>
                <div class=post-body>' . nl2br($this->post->content) . '</div>
            </div>
        </article>';
            $output .= '<div class="comments">';
            $output .= '<h2>Comments</h2>';
            $comments = Comment::find(
                [
                    "conditions" => "post_id = '".$post->id."'",
                ]
            );
            $count = 0;
            if ($comments && count($comments) > 0) {
                foreach ($comments as $c) {
                    $dt = $c->date;
                    $date = new DateTime($dt);
                    $now = new DateTime();
                    $diff = $now->diff($date);
                    if ($c->approved == 1 || ($c->author_ip == $_SERVER['REMOTE_ADDR'] && !$c->spam && $c->date && $diff->days <= 1)) {
                        $output .= '<div class="comment" id="comment-'.$c->id.'">
                    <div class="comment-author"><b>' . htmlentities(strip_tags($c->author_name)) . '</b></div>
                    <div class="comment-date">Posted: <span class="date">' . DOMHelper::randomDate($c->date) . '</span></div>
                    <div class="comment-link"><a href="/'.$slug.'#comment-'.$c->id.'">Link</a></div>
                    <div class="comment-content"><p>' . utf8_encode(htmlentities(strip_tags($c->content))) . '</p></div>
                </div>';
                        $count++;
                    }
                }
            }
            if ($count == 0) {
                $output .= '<p>No comments here :)</p>';
            }
            $output .= '</div>';
            $output .= '<h2>Leave a comment</h2>
    <div id="form-main">
  <div id="form-div">
    <form class="form" id="form1" method="post" action="/'.$this->post->slug.'">
      
      <p class="name">
        <input name="name" type="text" class="feedback-input" placeholder="Name" id="name" />
      </p>
      
      <p class="name">
        <input name="url" type="text" class="feedback-input" placeholder="Website" id="website" />
      </p>
      
      <p class="email">
        <input name="email" type="text" class="feedback-input" id="email" placeholder="Email" />
      </p>
      
      <p class="text">
        <textarea name="text" class="feedback-input" id="comment" placeholder="Comment"></textarea>
      </p>
      
      <input type="hidden" name="honey" value="">
      
      
      <div class="submit">
        <input type="submit" value="LEAVE A COMMENT" id="button-blue"/>
        <div class="ease"></div>
      </div>
    </form>
  </div>';
            $output .= '</section>';
            $output .= DOMHelper::footer();
            return DOMHelper::sanitize_output($output);
        } else {
            $this->response->setStatusCode(404, 'Not Found');
            return DOMHelper::method404();
        }
    }

}