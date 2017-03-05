<?php

use Phalcon\Mvc\Controller;

class ServicesController extends Controller
{

    public function indexAction()
    {


            $output = DOMHelper::header();
            $output .= '
<body>
<main class="page-wrapper">
    <nav>
        <ul>
            <li>
                <header>
                    <h1>
                        <a href="/">Markus Tenghamn <span class="divider">-</span> <span class="typer"><span class="caption" id="caption">Backend PHP Developer</span></span>
                    </h1>
                </header>
            </li>
            <li><a href="/services/">Services</a></li>
            <li><a href="/blog/">Blog</a></li>
            <li><a href="/contact/">Contact</a></li>
        </ul>
        <div class="handle">Menu</div>
    </nav>';
            $output .= '<section class="intro">
    	<h2>Services</h2>
    	<div class="cf"> 
    	<div class="col-md-8">
    	<div class="services">
        <p>I am not looking for any freelance work at the moment as I am busy 
        with a full-time job. However, I am always available if you would like 
        to send a nice greeting, discuss an article, ideas, or any oppurtunities 
        that you may have. You can get in touch via my <a href="/contact/">contact page</a>.</p>
        </div>
        </div>
    	<div class="col-md-4">
    	<img width="400" src="/assets/images/clear.jpg" data-src="/assets/images/services.jpg">
        </div>
        </div>
    </section>';
            $output .= '</section>';
            $output .= DOMHelper::footer();
            return DOMHelper::sanitize_output($output);

    }

}