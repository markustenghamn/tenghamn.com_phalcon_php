<?php

use Phalcon\Mvc\Controller;

class IndexController extends Controller
{

	public function indexAction()
	{
		$query = $this->modelsManager->createQuery("SELECT * FROM Post LIMIT 3");
		$posts  = $query->execute();
		$this->view->posts = $posts;
        $output = DOMHelper::header();
		$output .= '
<body>
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
    <section class="intro">
    	<h2>About me</h2>
        <p>I consider myself a backend PHP developer, self taught with over 10 years of experience.</p>
        <p>My skillset is not limited to PHP, I have held many roles that allowed me to learn and master other areas such
            as server management, cloud hosting, node.js and golang. A lot of my most recent
            work involves real time analysis and processing of data for some of the largest companies in Scandinavia.</p>
        <p>I love the work that I do and here on my blog you will find posts about things that I find interesting and
            hopefully others will find them helpful. My blog is a bit of an experiment where I try to show off my work related to optimization and web development. Read more about <a href="/blog/">what interests me</a> and if you have a question or
            have something that you think might interest me then feel free to <a href="/contact/">contact me</a>.</p>
    </section>
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

	public function sitemapAction() {
		$response = new \Phalcon\Http\Response();
		$expireDate = new DateTime('now', new DateTimeZone('UTC'));
		$expireDate->modify('+1 day');
		$sitemap = new DOMDocument("1.0", "UTF-8");
		$urlset = $sitemap->createElement('urlset');
		$urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
		$urlset->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
		$baseUrl = "http://tenghamn.com/";
		$url = $sitemap->createElement('url');
		$url->appendChild($sitemap->createElement('loc', $baseUrl));
		$url->appendChild($sitemap->createElement('changefreq', 'daily'));
		$url->appendChild($sitemap->createElement('priority', '1.0'));

		$url2 = $sitemap->createElement('url');
		$url2->appendChild($sitemap->createElement('loc', $baseUrl.'blog/'));
		$url2->appendChild($sitemap->createElement('changefreq', 'daily'));
		$url2->appendChild($sitemap->createElement('priority', '1.0'));

		$url3 = $sitemap->createElement('url');
		$url3->appendChild($sitemap->createElement('loc', $baseUrl.'contact/'));
		$url3->appendChild($sitemap->createElement('changefreq', 'monthly'));
		$url3->appendChild($sitemap->createElement('priority', '5.0'));

		$url4 = $sitemap->createElement('url');
		$url4->appendChild($sitemap->createElement('loc', $baseUrl.'services/'));
		$url4->appendChild($sitemap->createElement('changefreq', 'monthly'));
		$url4->appendChild($sitemap->createElement('priority', '10.0'));
		$urlset->appendChild($url);
		$urlset->appendChild($url2);
		$urlset->appendChild($url3);
		$urlset->appendChild($url4);
		$parametersPosts = [
			'columns'    => "id, slug, date",
			'order'      => 'date DESC'
		];
		$posts = Post::find($parametersPosts);
		$modifiedAt = new DateTime('now', new DateTimeZone('UTC'));
		$postKarma = 1;
		foreach ($posts as $post) {
			$modifiedAt->setTimestamp(time($post->date));
			$url = $sitemap->createElement('url');
			$href = trim($baseUrl, '/') . '/' . $post->slug;
			$url->appendChild(
				$sitemap->createElement('loc', $href)
			);
			$valuePriority = $postKarma > 0.7 ? sprintf("%0.1f", $postKarma) : sprintf("%0.1f", $postKarma + 0.25);
			$url->appendChild(
				$sitemap->createElement('priority', $valuePriority)
			);
			$url->appendChild($sitemap->createElement('lastmod', $modifiedAt->format('Y-m-d\TH:i:s\Z')));
			$urlset->appendChild($url);
			$postKarma += 10;
		}
		$sitemap->appendChild($urlset);
		$response
			->setExpires($expireDate)
			->setContent(DOMHelper::sanitize_output($sitemap->saveXML()))
			->setContentType('application/xml');
		return $response;
	}
}
