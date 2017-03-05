<?php

class DOMHelper
{

    static public function sanitize_output($buffer) {
        
        // This code basically makes all HTML one or two lines of code, removes line breaks and whitespace

        $search = array(
            '/\>[^\S ]+/s',  // strip whitespaces after tags, except space
            '/[^\S ]+\</s',  // strip whitespaces before tags, except space
            '/(\s)+/s'       // shorten multiple whitespace sequences
        );

        $replace = array(
            '>',
            '<',
            '\\1'
        );

        $buffer = preg_replace($search, $replace, $buffer);

        return $buffer;
    }

    static public function randomDate($datestring) {
        // I thought it would be clever with a random date format, not so cool :)
        
        return date(DATE_ATOM,strtotime($datestring));
    }

    static function method404() {
        $output = DOMHelper::header("404 not found");
        $output .= '
<body>
<main class="page-wrapper">
    <nav>
        <ul>
            <li>
                <header>
                    <h1>
                        <a href="/">Markus Tenghamn <span class="divider">-</span> 404 not found</a>
                    </h1>
                </header>
            </li>
            <li><a href="/services/">Services</a></li>
            <li><a href="/blog/">Blog</a></li>
            <li><a href="/contact/">Contact</a></li>
        </ul>
        <div class="handle">Menu</div>
    </nav>
    <section class="404">
        <h2>404 not found</h2>
        <p>The page you are looking for can not be found</p>
        <p>Go to <a href="/">Home</a></p>
    </section>';
        $output .= DOMHelper::footer();
        return DOMHelper::sanitize_output($output);
    }
    
    static public function header($title = "Backend PHP Developer, Optimization Expert and Innovator") {
        $output = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge, chrome=1" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Markus Tenghamn - '.$title.'</title>		
    <meta name="description" content="'.$title.'"/>
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="http://tenghamn.com/ping" />
	
	<meta name="google-site-verification" content="WqT3o2ebEg2_QSbhfVVMx6E_c0E0UmBEUksQ1n90D7Q" />
    <meta name="robots" content="noodp"/>
    <link rel="canonical" href="http://tenghamn.com/" />
    <link rel="publisher" href="https://plus.google.com/u/0/+MarkusTenghamn"/>
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="Markus Tenghamn - '.$title.'" />
    <meta property="og:description" content="'.$title.'" />
    <meta property="og:url" content="http://tenghamn.com/" />
    <meta property="og:site_name" content="Markus Tenghamn" />
    <meta property="fb:admins" content="576675459" />
    <meta property="og:image" content="http://tenghamn.com/assets/images/markustenghamn.jpg" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:description" content="'.$title.'" />
    <meta name="twitter:title" content="Markus Tenghamn - '.$title.'" />
    <meta name="twitter:site" content="@markustenghamn" />
    <meta name="twitter:image" content="http://tenghamn.com/assets/images/markustenghamn.jpg" />
    <script type=\'application/ld+json\'>{"@context":"http:\/\/schema.org","@type":"WebSite","@id":"#website","url":"http:\/\/tenghamn.com\/","name":"Markus Tenghamn","potentialAction":{"@type":"SearchAction","target":"http:\/\/tenghamn.com\/?s={search_term_string}","query-input":"required name=search_term_string"}}</script>
    <meta name="msvalidate.01" content="74683B1EE050977CAF48C88C8DEF3E51" />
    <meta name="google-site-verification" content="BdPVuMizxYUS61gbqgOugqFfua0e_lEonvkuhYF3-2A" />
    <meta name="p:domain_verify" content="e9cb431cd29a25709cf2cf93cff2c7f3" />
    <meta name="yandex-verification" content="58f5a36a92b08c9d" />
    <link rel="alternate" type="application/rss+xml" title="Markus Tenghamn &raquo; Feed" href="http://tenghamn.com/feed" />
    <link rel="alternate" type="application/rss+xml" title="Markus Tenghamn &raquo; Comments Feed" href="http://tenghamn.com/comments/feed" />
    <meta name="generator" content="Anveto" />
    <style>
    body{background-color:#fff;padding:0;margin:0;color:#000;font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;font-size:1em}a{color:#000;text-decoration:none}h1{font-size:1.2em}img{background:url(\'/assets/images/loading.gif\') 50% no-repeat}.post h3{width:100%;background-color:#000;color:#fff;padding:20px}.post h3 a{text-decoration:none;color:#fff}.post h3 a:visited,.post h3 a:active{text-decoration:none;color:#fff}.post h3 a:hover{color:#fff}code{white-space:pre}.aligncenter,div.aligncenter{display:block;margin:5px auto 5px auto}.console{font-family:"Lucida Sans Typewriter","Lucida Console",Monaco,"Bitstream Vera Sans Mono",monospace}.caption{font-weight:normal}input[type="button"]{cursor:pointer}.loader{width:100%;position:relative;height:300px;display:none;text-align:center}.circle{width:80px;height:80px;border-radius:50%;border:5px solid #333;-webkit-animation:spin 4s linear infinite;-moz-animation:spin 4s linear infinite;animation:spin 4s linear infinite;position:absolute;top:0;bottom:0;left:0;right:0;margin:0 auto;margin-top:11%}.circle:after{content:\' \';width:18px;height:18px;background:#333;display:block;border-radius:50%}header{width:100%;font-size:1em;color:#fff}footer{height:100px}nav{background-color:#000;width:100%}nav ul{overflow:hidden;color:white;padding:0;margin:0;text-align:center;width:100%}nav ul li:first-child{min-width:50em;text-align:left}nav ul li{display:inline-block;padding:15px}nav ul li:hover{background-color:#1f1f1f;color:white}nav a,nav a:visited,nav a:active{color:white}.alert{line-height:1.5em;padding:20px;width:75%;font-size:1.3em;margin:0 auto;background-color:#eee}.col-md-4{width:33.3%;float:left;display:block}.services{padding-right:20px}.col-md-8{width:66.6%;float:left;display:block}.cf:before,.cf:after{content:" ";display:table}.cf:after{clear:both}.cf{*zoom:1}section{line-height:1.5em;padding:20px;width:75%;font-size:1.3em;margin:0 auto}section a{text-decoration:underline}.handle{width:100%;background:#000;text-align:left;box-sizing:border-box;padding:15px 10px;cursor:pointer;color:white;display:none}@media screen and (max-width:600px){nav ul li:first-child{min-width:100%;text-align:left}nav ul{max-height:0}nav ul li{width:100%;box-sizing:border-box;padding:15px;text-align:left}header{width:90%}section{padding:10px;width:90%}.handle{display:block}.divider{display:none}.showing{max-height:20em}.typer{display:block}}@-moz-keyframes spin{100%{-moz-transform:rotate(360deg)}}@-webkit-keyframes spin{100%{-webkit-transform:rotate(360deg)}}@keyframes spin{100%{-webkit-transform:rotate(360deg);transform:rotate(360deg)}}.comment{background-color:#f5f5f5;padding:20px;margin-bottom:20px}.typed-cursor{opacity:1;-webkit-animation:blink .7s infinite;-moz-animation:blink .7s infinite;animation:blink .7s infinite}@keyframes blink{0{opacity:1}50%{opacity:0}100%{opacity:1}}@-webkit-keyframes blink{0{opacity:1}50%{opacity:0}100%{opacity:1}}@-moz-keyframes blink{0{opacity:1}50%{opacity:0}100%{opacity:1}}@import url(http://fonts.googleapis.com/css?family=Montserrat:400,700);html{background:#3498db}
    </style>

</head>';
        return $output;
    }

    static public function footer($loadtime=0) {
        global $start;
        if (isset($start) && $start > 0) {
            $loadtime=microtime(true)-$start;
        }
        $output = '<footer>
        <!-- Footer is empty -->
    </footer>
</main>
<script async src="/assets/js/script.min.js"></script>';
        $output .= '<noscript id="deferred-styles">    
    <link rel="stylesheet" href="/assets/css/style.min.css"> 
    </noscript>
    <script>
      var loadDeferredStyles = function() {
        var addStylesNode = document.getElementById("deferred-styles");
        var replacement = document.createElement("div");
        replacement.innerHTML = addStylesNode.textContent;
        document.body.appendChild(replacement);
        addStylesNode.parentElement.removeChild(addStylesNode);
      };
      var raf = requestAnimationFrame || mozRequestAnimationFrame ||
          webkitRequestAnimationFrame || msRequestAnimationFrame;
      if (raf) { raf(function() { window.setTimeout(loadDeferredStyles, 0); }); }
      else { window.addEventListener("load", loadDeferredStyles); }
    </script>
    <script>
    window.ga=window.ga||function(){(ga.q=ga.q||[]).push(arguments)};ga.l=+new Date;
    ga(\'create\', \'UA-28059087-1\', \'auto\');
    ga(\'send\', \'pageview\');
</script>';
        if ($loadtime > 0) {
            $output .= '<script>
    ga(\'send\', \'timing\', \'PHP\', \'load\', ' . round($loadtime*1000) . ');
  
    </script>';
        }
        $output .= '
        <script async src="/assets/js/a.js"></script>
        </body></html>';
        return $output;
    }
    
}