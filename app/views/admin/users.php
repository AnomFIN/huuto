<?php
$pageTitle = 'Hallinnoi käyttäjiä';
ob_start();
?>

<div class="container" style="margin-top: 2rem;">
    <h1>👥 Käyttäjien hallinta</h1>
    
    <div style="margin: 1.5rem 0;">
        <a href="/admin" class="btn btn-secondary">« Takaisin admin-paneeliin</a>
    </div>
    
    <div style="background: white; padding: 1.5rem; border-radius: 12px; overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid var(--gray-300);">
                    <th style="padding: 0.75rem; text-align: left;">ID</th>
                    <th style="padding: 0.75rem; text-align: left;">Nimi</th>
                    <th style="padding: 0.75rem; text-align: left;">Sähköposti</th>
                    <th style="padding: 0.75rem; text-align: left;">Rooli</th>
                    <th style="padding: 0.75rem; text-align: left;">Status</th>
                    <th style="padding: 0.75rem; text-align: left;">Luotu</th>
                    <th style="padding: 0.75rem; text-align: left;">Toiminnot</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr style="border-bottom: 1px solid var(--gray-200);">
                        <td style="padding: 0.75rem;"><?= $user['id'] ?></td>
                        <td style="padding: 0.75rem;"><?= htmlspecialchars($user['name']) ?></td>
                        <td style="padding: 0.75rem;"><?= htmlspecialchars($user['email']) ?></td>
                        <td style="padding: 0.75rem;">
                            <span class="badge <?= $user['role'] == 'admin' ? 'badge-danger' : 'badge-primary' ?>">
                                <?= htmlspecialchars($user['role']) ?>
                            </span>
                        </td>
                        <td style="padding: 0.75rem;">
                            <span class="badge <?= $user['status'] == 'active' ? 'badge-success' : ($user['status'] == 'banned' ? 'badge-danger' : 'badge-warning') ?>">
                                <?= htmlspecialchars($user['status']) ?>
                            </span>
                        </td>
                        <td style="padding: 0.75rem;"><?= date('d.m.Y', strtotime($user['created_at'])) ?></td>
                        <td style="padding: 0.75rem;">
                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="csrf_token" value="<?= Security::generateToken() ?>">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <?php if ($user['status'] == 'active'): ?>
                                        <button type="submit" name="action" value="ban" class="btn btn-danger" style="padding: 0.25rem 0.75rem; font-size: 0.875rem;">
                                            Estä
                                        </button>
                                    <?php else: ?>
                                        <button type="submit" name="action" value="activate" class="btn btn-success" style="padding: 0.25rem 0.75rem; font-size: 0.875rem;">
                                            Aktivoi
                                        </button>
                                    <?php endif; ?>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
