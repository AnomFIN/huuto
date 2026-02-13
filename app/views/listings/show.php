<?php
$pageTitle = htmlspecialchars($listing['title']);
ob_start();
$isEnded = strtotime($listing['ends_at']) <= time();
$canBid = Security::isLoggedIn() && !$isEnded && $listing['user_id'] != ($_SESSION['user_id'] ?? 0);
?>

<div class="container" style="margin-top: 2rem;">
    <div class="breadcrumb">
        <a href="/">Etusivu</a> / 
        <a href="/kategoriat">Kategoriat</a> / 
        <a href="/kategoria/<?= $listing['category_slug'] ?>"><?= htmlspecialchars($listing['category_name']) ?></a> / 
        <?= htmlspecialchars($listing['title']) ?>
    </div>
    
    <div class="listing-detail">
        <h1><?= htmlspecialchars($listing['title']) ?></h1>
        
        <div class="listing-info">
            <div>
                <!-- Images -->
                <?php if (!empty($images)): ?>
                    <div class="listing-images">
                        <?php foreach ($images as $image): ?>
                            <img src="<?= htmlspecialchars($image['path']) ?>" 
                                 alt="<?= htmlspecialchars($listing['title']) ?>"
                                 loading="lazy">
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <img src="/assets/img/placeholder.jpg" alt="Ei kuvaa" style="width: 100%; border-radius: 8px;">
                <?php endif; ?>
                
                <!-- Description -->
                <div style="margin-top: 2rem;">
                    <h3>Kuvaus</h3>
                    <p style="white-space: pre-line; line-height: 1.6;"><?= htmlspecialchars($listing['description']) ?></p>
                </div>
                
                <!-- Details -->
                <div style="margin-top: 2rem; background: var(--gray-50); padding: 1.5rem; border-radius: 8px;">
                    <h3 style="margin-bottom: 1rem;">Tiedot</h3>
                    <table style="width: 100%;">
                        <tr>
                            <td style="padding: 0.5rem 0; font-weight: 600;">Kunto:</td>
                            <td><?= htmlspecialchars($listing['condition'] ?? 'Ei ilmoitettu') ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 0.5rem 0; font-weight: 600;">Sijainti:</td>
                            <td><?= htmlspecialchars($listing['region'] ?? 'Ei ilmoitettu') ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 0.5rem 0; font-weight: 600;">Myyjä:</td>
                            <td><?= htmlspecialchars($listing['seller_name']) ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 0.5rem 0; font-weight: 600;">Aloitushinta:</td>
                            <td><?= Security::formatPrice($listing['start_price']) ?></td>
                        </tr>
                        <?php if ($listing['buy_now_price']): ?>
                        <tr>
                            <td style="padding: 0.5rem 0; font-weight: 600;">Osta heti:</td>
                            <td><?= Security::formatPrice($listing['buy_now_price']) ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
            
            <!-- Bidding Section -->
            <div>
                <div class="bid-section">
                    <h3>Nykyinen huuto</h3>
                    <div class="current-bid"><?= Security::formatPrice($listing['current_price']) ?></div>
                    
                    <?php if ($isEnded): ?>
                        <div class="badge badge-danger" style="font-size: 1rem; padding: 0.5rem 1rem;">
                            Huutokauppa päättynyt
                        </div>
                        <?php if ($listing['highest_bidder_id']): ?>
                            <p style="margin-top: 1rem;">
                                Voittaja: <strong><?= htmlspecialchars($listing['highest_bidder_name'] ?? 'Tuntematon') ?></strong>
                            </p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p>Päättyy: <strong data-countdown="<?= $listing['ends_at'] ?>" class="countdown">
                            <?= Security::timeRemaining($listing['ends_at']) ?>
                        </strong></p>
                        
                        <?php if ($canBid): ?>
                            <form method="POST" action="/huuda/<?= $listing['id'] ?>" id="bid-form" style="margin-top: 1.5rem;">
                                <input type="hidden" name="csrf_token" value="<?= Security::generateToken() ?>">
                                
                                <div class="form-group">
                                    <label class="form-label">Huutosi (min. <?= Security::formatPrice($listing['current_price'] + $listing['min_increment']) ?>)</label>
                                    <input type="number" 
                                           name="amount" 
                                           id="bid-amount"
                                           class="form-input" 
                                           step="0.01" 
                                           min="<?= $listing['current_price'] + $listing['min_increment'] ?>"
                                           data-min-bid="<?= $listing['current_price'] + $listing['min_increment'] ?>"
                                           value="<?= $listing['current_price'] + $listing['min_increment'] ?>"
                                           required>
                                </div>
                                
                                <button type="submit" class="btn btn-success" style="width: 100%;">Huuda</button>
                            </form>
                            
                            <?php if ($listing['buy_now_price'] && $listing['buy_now_price'] > $listing['current_price']): ?>
                                <button class="btn btn-primary" style="width: 100%; margin-top: 0.5rem;">
                                    Osta heti <?= Security::formatPrice($listing['buy_now_price']) ?>
                                </button>
                            <?php endif; ?>
                        <?php elseif (!Security::isLoggedIn()): ?>
                            <p style="margin-top: 1rem;">
                                <a href="/kirjaudu" class="btn btn-primary" style="width: 100%; text-align: center;">
                                    Kirjaudu huutaaksesi
                                </a>
                            </p>
                        <?php elseif ($listing['user_id'] == ($_SESSION['user_id'] ?? 0)): ?>
                            <p style="margin-top: 1rem; color: var(--gray-600);">
                                Et voi huutaa omaan ilmoitukseesi
                            </p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                
                <!-- Bid History -->
                <?php if (!empty($bids)): ?>
                    <div class="bid-history">
                        <h3>Huutohistoria</h3>
                        <?php foreach ($bids as $bid): ?>
                            <div class="bid-item">
                                <span><?= htmlspecialchars(substr($bid['bidder_name'], 0, 1)) ?>***</span>
                                <strong><?= Security::formatPrice($bid['amount']) ?></strong>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>
