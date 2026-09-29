<?php
class ArticleController {
    function about() {
        require ABSPATH_SITE . 'view/article/about.php';
    }
    function index() {
        $articles = db_execute('SELECT id,title,slug,summary,created_at FROM article WHERE is_published = 1 ORDER BY created_at DESC, id DESC')->get_result()->fetch_all(MYSQLI_ASSOC);
        require ABSPATH_SITE . 'view/article/enhanced-index.php';
    }
    function detail($slug = null, $id = null) {
        $id = positive_id($id ?? $_GET['id'] ?? 0);
        $article = db_execute('SELECT * FROM article WHERE id = ? AND is_published = 1', [$id])->get_result()->fetch_assoc();
        require_record($article);
        if ($slug !== null && $slug !== $article['slug']) {
            header('Location: ' . app_url('bai-viet/' . $article['slug'] . '-' . $id . '.html'), true, 301);
            exit;
        }
        require ABSPATH_SITE . 'view/article/enhanced-detail.php';
    }
}
