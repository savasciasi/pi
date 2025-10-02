<?php
require_once __DIR__ . '/header.php';

$days = [
    1 => 'Pazartesi',
    2 => 'Salı',
    3 => 'Çarşamba',
    4 => 'Perşembe',
    5 => 'Cuma',
    6 => 'Cumartesi',
    7 => 'Pazar',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrf($_POST['csrf_token'] ?? '');
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        executeQuery('DELETE FROM schedule_entries WHERE id = :id', [':id' => $id]);
        setFlash('success', 'Ders programı satırı silindi.');
        header('Location: schedule.php');
        exit;
    }

    $dayOrder = (int)($_POST['day_order'] ?? 1);
    $startTime = $_POST['start_time'] ?? '09:00';
    $classType = trim($_POST['class_type'] ?? '');
    $level = trim($_POST['level'] ?? '');
    $id = (int)($_POST['id'] ?? 0);

    $params = [
        ':day_order' => $dayOrder,
        ':start_time' => $startTime,
        ':class_type' => $classType,
        ':level' => $level,
    ];

    if ($id) {
        $params[':id'] = $id;
        executeQuery('UPDATE schedule_entries SET day_order = :day_order, start_time = :start_time, class_type = :class_type, level = :level WHERE id = :id', $params);
    } else {
        executeQuery('INSERT INTO schedule_entries (day_order, start_time, class_type, level) VALUES (:day_order, :start_time, :class_type, :level)', $params);
    }

    setFlash('success', 'Ders programı kaydedildi.');
    header('Location: schedule.php');
    exit;
}

$schedule = fetchAll('SELECT * FROM schedule_entries ORDER BY day_order, start_time');
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$editEntry = $editId ? fetchOne('SELECT * FROM schedule_entries WHERE id = :id', [':id' => $editId]) : null;
$success = getFlash('success');
?>
<header>
    <h1>Ders Programı</h1>
</header>
<?php if ($success): ?><div class="flash"><?= htmlspecialchars($success); ?></div><?php endif; ?>
<div class="card">
    <h3><?= $editEntry ? 'Program Satırı Düzenle' : 'Yeni Program Satırı'; ?></h3>
    <form method="post">
        <label for="day_order">Gün</label>
        <select name="day_order" id="day_order">
            <?php foreach ($days as $value => $label): ?>
                <option value="<?= $value; ?>" <?= (int)($editEntry['day_order'] ?? 1) === $value ? 'selected' : ''; ?>><?= htmlspecialchars($label); ?></option>
            <?php endforeach; ?>
        </select>

        <label for="start_time">Saat</label>
        <input type="time" name="start_time" id="start_time" value="<?= htmlspecialchars($editEntry['start_time'] ?? '09:00'); ?>">

        <label for="class_type">Ders</label>
        <input type="text" name="class_type" id="class_type" value="<?= htmlspecialchars($editEntry['class_type'] ?? ''); ?>" required>

        <label for="level">Seviye</label>
        <input type="text" name="level" id="level" value="<?= htmlspecialchars($editEntry['level'] ?? ''); ?>">

        <input type="hidden" name="id" value="<?= (int)($editEntry['id'] ?? 0); ?>">
        <input type="hidden" name="csrf_token" value="<?= csrfToken(); ?>">
        <button type="submit" class="btn primary">Kaydet</button>
        <?php if ($editEntry): ?>
            <a class="btn secondary" href="schedule.php">İptal</a>
        <?php endif; ?>
    </form>
</div>

<div class="card">
    <h3>Mevcut Program</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Gün</th>
                <th>Saat</th>
                <th>Ders</th>
                <th>Seviye</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($schedule as $entry): ?>
                <tr>
                    <td><?= htmlspecialchars($days[(int)$entry['day_order']]); ?></td>
                    <td><?= htmlspecialchars(substr($entry['start_time'], 0, 5)); ?></td>
                    <td><?= htmlspecialchars($entry['class_type']); ?></td>
                    <td><?= htmlspecialchars($entry['level']); ?></td>
                    <td>
                        <div class="table-actions">
                            <a class="btn secondary" href="schedule.php?edit=<?= (int)$entry['id']; ?>">Düzenle</a>
                            <form method="post" onsubmit="return confirm('Satır silinsin mi?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int)$entry['id']; ?>">
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
