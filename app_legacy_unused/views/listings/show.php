<?php
$pageTitle = htmlspecialchars($listing['title']);
ob_start();
$isEnded = strtotime($listing['ends_at']) <= time();
$canBid = Security::isLoggedIn() && !$isEnded && $listing['user_id'] != ($_SESSION['user_id'] ?? 0);
?>

<div class="container mt-7">
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
                        <div class="main-image-container">
                            <img src="<?= htmlspecialchars($images[0]['path']) ?>" 
                                 alt="<?= htmlspecialchars($listing['title']) ?>"
                                 class="main-image"
                                 loading="eager">
                        </div>
                        
                        <?php if (count($images) > 1): ?>
                        <div class="image-thumbnails">
                            <?php foreach ($images as $image): ?>
                                <div class="thumbnail">
                                    <img src="<?= htmlspecialchars($image['path']) ?>" 
                                         alt="<?= htmlspecialchars($listing['title']) ?>"
                                         loading="lazy">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="main-image-container">
                        <img src="/assets/img/placeholder.jpg" alt="Ei kuvaa" class="main-image">
                    </div>
                <?php endif; ?>
                
                <!-- Description -->
                <div class="listing-description">
                    <h3>Kuvaus</h3>
                    <p><?= htmlspecialchars($listing['description']) ?></p>
                </div>
                
                <!-- Details -->
                <div class="listing-details-box">
                    <h3>Tiedot</h3>
                    <table>
                        <tr>
                            <td>Kunto:</td>
                            <td><?= htmlspecialchars($listing['condition'] ?? 'Ei ilmoitettu') ?></td>
                        </tr>
                        <tr>
                            <td>Sijainti:</td>
                            <td><?= htmlspecialchars($listing['region'] ?? 'Ei ilmoitettu') ?></td>
                        </tr>
                        <tr>
                            <td>Myyjä:</td>
                            <td><?= htmlspecialchars($listing['seller_name']) ?></td>
                        </tr>
                        <tr>
                            <td>Aloitushinta:</td>
                            <td><?= Security::formatPrice($listing['start_price']) ?></td>
                        </tr>
                        <?php if ($listing['buy_now_price']): ?>
                        <tr>
                            <td>Osta heti:</td>
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
                        <div class="badge badge-danger badge-lg mt-4">
                            Huutokauppa päättynyt
                        </div>
                        <?php if ($listing['highest_bidder_id']): ?>
                            <p class="mt-4">
                                Voittaja: <strong><?= htmlspecialchars($listing['highest_bidder_name'] ?? 'Tuntematon') ?></strong>
                            </p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="mt-3">Päättyy: <strong data-countdown="<?= $listing['ends_at'] ?>" class="countdown">
                            <?= Security::timeRemaining($listing['ends_at']) ?>
                        </strong></p>
                        
                        <?php if ($canBid): ?>
                            <form method="POST" action="/huuda/<?= $listing['id'] ?>" id="bid-form" class="bid-form">
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
                                
                                <button type="submit" class="btn btn-success w-full">Huuda</button>
                            </form>
                            
                            <?php if ($listing['buy_now_price'] && $listing['buy_now_price'] > $listing['current_price']): ?>
                                <form method="POST" action="/osta-heti/<?= $listing['id'] ?>" class="mt-3">
                                    <input type="hidden" name="csrf_token" value="<?= Security::generateToken() ?>">
                                    <button type="submit" class="btn btn-primary w-full">
                                        Osta heti <?= Security::formatPrice($listing['buy_now_price']) ?>
                                    </button>
                                </form>
                            <?php endif; ?>
                        <?php elseif (!Security::isLoggedIn()): ?>
                            <p class="mt-4">
                                <a href="/kirjaudu" class="btn btn-primary w-full text-center d-block">
                                    Kirjaudu huutaaksesi
                                </a>
                            </p>
                        <?php elseif ($listing['user_id'] == ($_SESSION['user_id'] ?? 0)): ?>
                            <p class="mt-4 text-secondary text-sm">
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
                                <span><?= htmlspecialchars(mb_substr($bid['bidder_name'], 0, 1, 'UTF-8')) ?>***</span>
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
