<?php

/**
 * Class to generate a news sitemap, handling requests and redirects specifically for news sitemap URLs.
 */
class WP_Sitemap_News {
    private string $post_type = 'post';
    private int $posts_per_page = 1000;

    /**
     * Constructor that initializes the class by setting up rewrite rules and hooks into WordPress actions.
     */
    public function __construct() {
        $this->add_news_sitemap_rewrite_rule();
        add_filter('query_vars', [$this, 'add_query_vars']);
        add_action('template_redirect', [$this, 'render_sitemap']);
        add_filter('redirect_canonical', [$this, 'no_slash_on_news_sitemap'], 10, 2);
    }

    /**
     * Adds a rewrite rule that matches 'wp-sitemap-news.xml' and maps it to a specific query variable.
     */
    public function add_news_sitemap_rewrite_rule(): void {
        add_rewrite_rule('wp-sitemap-news.xml$', 'index.php?news_sitemap=1', 'top');
    }

    /**
     * Prevents WordPress from redirecting the news sitemap URL with a slash to a URL without a slash.
     * 
     * @param string $redirect_url The URL to which WordPress would normally redirect.
     * @param string $requested_url The originally requested URL.
     * @return string|false Either the original redirect URL or false to stop the redirection.
     */
    public function no_slash_on_news_sitemap($redirect_url, $requested_url) {
        if (strpos($requested_url, 'wp-sitemap-news.xml') !== false) {
            return false;
        }
        return $redirect_url;
    }

    /**
     * Redirects from '/wp-sitemap-news.xml/' to '/wp-sitemap-news.xml' if the former was requested.
     */
    public function redirect_news_sitemap_with_slash() {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if ($path === '/wp-sitemap-news.xml/') {
            $redirect_url = site_url('/wp-sitemap-news.xml');
            wp_redirect($redirect_url, 301);
            exit;
        }
    }

    /**
     * Adds custom query variables to the WordPress query system to be recognized in URL requests.
     * 
     * @param array $vars The current array of query variables.
     * @return array The modified array including new custom query variables.
     */
    public function add_query_vars(array $vars): array {
        $vars[] = 'news_sitemap';
        return $vars;
    }

    /**
     * Handles the request and renders the XML sitemap if the correct query variable is detected.
     */
    public function render_sitemap(): void {
        $this->redirect_news_sitemap_with_slash();
        if (get_query_var('news_sitemap')) {
            header('Content-Type: application/xml; charset=utf-8');
            echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">';
            $this->generate_sitemap_content();
            echo '</urlset>';
            exit;
        }
    }

    /**
     * Generates the sitemap content by querying for posts and formatting them as specified by Google's sitemap standards.
     */
    private function generate_sitemap_content(): void {
        $args = [
            'post_type' => $this->post_type,
            'posts_per_page' => $this->posts_per_page,
            'date_query' => [['after' => '2 days ago']],
            'no_found_rows' => true,
            'ignore_sticky_posts' => 1  // Ignores sticky posts to prevent them from appearing at the top of the list
        ];

        $query = new WP_Query($args);
        while ($query->have_posts()) : $query->the_post();
            echo '<url>';
                echo '<loc>' . esc_url(get_permalink()) . '</loc>';
                echo '<news:news>';
                    echo '<news:publication>';
                        echo '<news:name>' . get_bloginfo('name') . '</news:name>';
                        echo '<news:language>' . get_bloginfo('language') . '</news:language>';
                    echo '</news:publication>';
                    echo '<news:publication_date>' . get_the_time(DATE_W3C) . '</news:publication_date>';
                    echo '<news:title>' . get_the_title() . '</news:title>';
                echo '</news:news>';
            echo '</url>';
        endwhile;
        wp_reset_postdata();
    }
}