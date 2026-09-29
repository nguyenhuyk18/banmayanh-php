<?php
class ArticleController {
    function list() {
        $articles = db_execute('SELECT id,title,slug,is_published,created_at FROM article ORDER BY created_at DESC,id DESC')->get_result()->fetch_all(MYSQLI_ASSOC);
        $page_title = 'Bài viết';
        include 'view/article/list.php';
    }
    function add() {
        $article = null;
        $page_title = 'Thêm bài viết';
        include 'view/article/form.php';
    }
    function edit() {
        $article = db_execute('SELECT * FROM article WHERE id = ?', [positive_id($_GET['id'] ?? 0)])->get_result()->fetch_assoc();
        require_record($article);
        $page_title = 'Sửa bài viết';
        include 'view/article/form.php';
    }
    private function data() {
        require_fields(['title','summary','content']);
        $title = input_text('title');
        if (mb_strlen($title) > 200 || mb_strlen(input_text('summary')) > 500) throw new InvalidArgumentException('Tiêu đề hoặc phần tóm tắt quá dài.');
        $slug = (new \Cocur\Slugify\Slugify())->slugify($title);
        if ($slug === '') throw new InvalidArgumentException('Tiêu đề không hợp lệ.');
        return [$title, $slug, input_text('summary'), input_text('content'), !empty($_POST['is_published']) ? 1 : 0];
    }
    function save() {
        $data = $this->data();
        db_execute('INSERT INTO article (title,slug,summary,content,is_published) VALUES (?,?,?,?,?)', $data);
        header('Location: index.php?c=article&a=list');
        exit;
    }
    function update() {
        $id = positive_id($_POST['id'] ?? 0);
        require_record(db_execute('SELECT id FROM article WHERE id = ?', [$id])->get_result()->fetch_assoc());
        $data = $this->data();
        $data[] = $id;
        db_execute('UPDATE article SET title=?,slug=?,summary=?,content=?,is_published=? WHERE id=?', $data);
        header('Location: index.php?c=article&a=list');
        exit;
    }
    function delete() {
        db_execute('DELETE FROM article WHERE id = ?', [positive_id($_GET['id'] ?? 0)]);
        header('Location: index.php?c=article&a=list');
        exit;
    }
}
