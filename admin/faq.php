<?php
require_once __DIR__ . '/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrf($_POST['csrf_token'] ?? '');
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        executeQuery('DELETE FROM faq WHERE id = :id', [':id' => $id]);
        setFlash('success', 'Soru silindi.');
        header('Location: faq.php');
        exit;
    }

    $question = trim($_POST['question'] ?? '');
    $answer = trim($_POST['answer'] ?? '');
    $sortOrder = (int)($_POST['sort_order'] ?? 0);
    $id = (int)($_POST['id'] ?? 0);

    $params = [
        ':question' => $question,
        ':answer' => $answer,
        ':sort_order' => $sortOrder,
    ];

    if ($id) {
        $params[':id'] = $id;
        executeQuery('UPDATE faq SET question = :question, answer = :answer, sort_order = :sort_order WHERE id = :id', $params);
    } else {
        executeQuery('INSERT INTO faq (question, answer, sort_order) VALUES (:question, :answer, :sort_order)', $params);
    }

    setFlash('success', 'Soru kaydedildi.');
    header('Location: faq.php');
    exit;
}

$questions = fetchAll('SELECT * FROM faq ORDER BY sort_order, id');
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$editQuestion = $editId ? fetchOne('SELECT * FROM faq WHERE id = :id', [':id' => $editId]) : null;
$success = getFlash('success');
?>
<header>
    <h1>Sık Sorulan Sorular</h1>
</header>
<?php if ($success): ?><div class="flash"><?= htmlspecialchars($success); ?></div><?php endif; ?>
<div class="card">
    <h3><?= $editQuestion ? 'Soruyu Düzenle' : 'Yeni Soru'; ?></h3>
    <form method="post">
        <label for="question">Soru</label>
        <input type="text" name="question" id="question" value="<?= htmlspecialchars($editQuestion['question'] ?? ''); ?>" required>

        <label for="answer">Cevap</label>
        <textarea name="answer" id="answer" rows="4" required><?= htmlspecialchars($editQuestion['answer'] ?? ''); ?></textarea>

        <label for="sort_order">Sıra</label>
        <input type="number" name="sort_order" id="sort_order" value="<?= htmlspecialchars($editQuestion['sort_order'] ?? 0); ?>">

        <input type="hidden" name="id" value="<?= (int)($editQuestion['id'] ?? 0); ?>">
        <input type="hidden" name="csrf_token" value="<?= csrfToken(); ?>">
        <button type="submit" class="btn primary">Kaydet</button>
        <?php if ($editQuestion): ?>
            <a class="btn secondary" href="faq.php">İptal</a>
        <?php endif; ?>
    </form>
</div>

<div class="card">
    <h3>Mevcut Sorular</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Soru</th>
                <th>Sıra</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($questions as $question): ?>
                <tr>
                    <td><?= htmlspecialchars($question['question']); ?></td>
                    <td><?= (int)$question['sort_order']; ?></td>
                    <td>
                        <div class="table-actions">
                            <a class="btn secondary" href="faq.php?edit=<?= (int)$question['id']; ?>">Düzenle</a>
                            <form method="post" onsubmit="return confirm('Soru silinsin mi?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int)$question['id']; ?>">
                                <input type="hidden" name="csrf_token" value="<?= csrfToken(); ?>">
                                <button type="submit" class="btn danger">Sil</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php
require_once __DIR__ . '/layout-footer.php';
