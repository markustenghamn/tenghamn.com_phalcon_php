<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Request;
use Phalcon\Filter;

class ContactController extends Controller
{

    private $_transport;

    public function indexAction()
    {
        $request = new Request();
        if ($this->request->isPost()) {
            $name = $request->getPost("name");
            $email = $request->getPost("email");
            $text = $request->getPost("text");
            $to      = 'markus@tenghamn.com';
            $subject = 'New contact form submission from Tenghamn.com';
            $message = "From: ".$name." (".$email.")<br/>".$text;

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
    <section class="contact-me">
    <h2>Contact me</h2>
    <div id="form-main">
  <div id="form-div">
    <form class="form" id="form1" method="post" action="/contact/">
      
      <p class="name">
        <input name="name" type="text" class="feedback-input" placeholder="Name" id="name" />
      </p>
      
      <p class="email">
        <input name="email" type="text" class="feedback-input" id="email" placeholder="Email" />
      </p>
      
      <p class="text">
        <textarea name="text" class="feedback-input" id="comment" placeholder="Your message"></textarea>
      </p>
      
      
      <div class="submit">
        <input type="submit" value="SEND" id="button-blue"/>
        <div class="ease"></div>
      </div>
    </form>
  </div>';
        $output .= "</section>";
        $output .= DOMHelper::footer();
        return DOMHelper::sanitize_output($output);
    }

}