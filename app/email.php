<?php
/**
 * Email System
 * Handles email sending with dev mode support
 */

class Email {
    private $devMode;
    private $logFile;
    
    public function __construct() {
        $this->devMode = MAIL_DEV_MODE || !MAIL_ENABLED;
        $this->logFile = MAIL_LOG_FILE;
        
        // Ensure log directory exists
        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }
    
    /**
     * Send email (or log in dev mode)
     */
    public function send($to, $subject, $body, $isHtml = true) {
        if ($this->devMode) {
            return $this->logEmail($to, $subject, $body);
        }
        
        return $this->sendEmail($to, $subject, $body, $isHtml);
    }
    
    /**
     * Log email to file (dev mode)
     */
    private function logEmail($to, $subject, $body) {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = str_repeat('=', 80) . "\n";
        $logEntry .= "DEV MODE EMAIL LOG - {$timestamp}\n";
        $logEntry .= str_repeat('=', 80) . "\n";
        $logEntry .= "To: {$to}\n";
        $logEntry .= "Subject: {$subject}\n";
        $logEntry .= str_repeat('-', 80) . "\n";
        $logEntry .= $body . "\n";
        $logEntry .= str_repeat('=', 80) . "\n\n";
        
        file_put_contents($this->logFile, $logEntry, FILE_APPEND);
        
        error_log("DEV MODE: Email logged instead of sent. To: {$to}, Subject: {$subject}");
        
        return true;
    }
    
    /**
     * Actually send email via SMTP or mail()
     */
    private function sendEmail($to, $subject, $body, $isHtml) {
        $headers = [];
        $headers[] = 'From: ' . MAIL_FROM_NAME . ' <' . MAIL_FROM . '>';
        $headers[] = 'Reply-To: ' . MAIL_FROM;
        $headers[] = 'X-Mailer: PHP/' . phpversion();
        
        if ($isHtml) {
            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-Type: text/html; charset=UTF-8';
        } else {
            $headers[] = 'Content-Type: text/plain; charset=UTF-8';
        }
        
        // Only use SMTP if host, username, and password are all configured
        if (!empty(SMTP_HOST) && !empty(SMTP_USERNAME) && !empty(SMTP_PASSWORD)) {
            return $this->sendViaSMTP($to, $subject, $body, $headers);
        }
        
        // Otherwise use PHP's mail() function
        return mail($to, $subject, $body, implode("\r\n", $headers));
    }
    
    /**
     * Send via SMTP (basic implementation)
     * Currently falls back to PHP's mail() until proper SMTP support is added.
     * For production, consider using PHPMailer library.
     */
    private function sendViaSMTP($to, $subject, $body, $headers) {
        // SMTP is not implemented yet - log a warning and fall back to mail()
        error_log('SMTP sending not implemented. Falling back to PHP mail(). Install PHPMailer for SMTP support.');
        
        // Ensure headers are in string form for mail()
        $headersString = is_array($headers) ? implode("\r\n", $headers) : $headers;
        
        return mail($to, $subject, $body, $headersString);
    }
    
    /**
     * Send verification email
     */
    public function sendVerification($email, $token) {
        $url = BASE_URL . '/auth/verify.php?token=' . urlencode($token);
        
        $subject = 'Vahvista sähköpostiosoitteesi - ' . SITE_NAME;
        
        $body = $this->getEmailTemplate(
            'Vahvista sähköpostiosoitteesi',
            '<p>Kiitos rekisteröitymisestä! Vahvista sähköpostiosoitteesi klikkaamalla alla olevaa linkkiä:</p>
            <p style="margin: 30px 0;">
                <a href="' . e($url) . '" style="background: #3b82f6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;">
                    Vahvista sähköposti
                </a>
            </p>
            <p>Tai kopioi tämä linkki selaimeesi:</p>
            <p style="word-break: break-all; color: #666;">' . e($url) . '</p>
            <p>Linkki on voimassa 24 tuntia.</p>'
        );
        
        return $this->send($email, $subject, $body);
    }
    
    /**
     * Send magic login code
     */
    public function sendMagicCode($email, $code) {
        $subject = 'Kirjautumiskoodi - ' . SITE_NAME;
        
        $body = $this->getEmailTemplate(
            'Kirjautumiskoodi',
            '<p>Kirjautumiskoodisi on:</p>
            <p style="margin: 30px 0;">
                <span style="font-size: 32px; font-weight: bold; letter-spacing: 8px; color: #3b82f6;">' . e($code) . '</span>
            </p>
            <p>Syötä tämä koodi kirjautumissivulla. Koodi on voimassa 10 minuuttia.</p>
            <p><strong>Älä jaa tätä koodia kenellekään!</strong></p>'
        );
        
        return $this->send($email, $subject, $body);
    }
    
    /**
     * Send password reset email
     */
    public function sendPasswordReset($email, $token) {
        $url = BASE_URL . '/auth/reset-password.php?token=' . urlencode($token);
        
        $subject = 'Salasanan nollaus - ' . SITE_NAME;
        
        $body = $this->getEmailTemplate(
            'Salasanan nollaus',
            '<p>Olet pyytänyt salasanan nollausta. Nollaa salasanasi klikkaamalla alla olevaa linkkiä:</p>
            <p style="margin: 30px 0;">
                <a href="' . e($url) . '" style="background: #3b82f6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;">
                    Nollaa salasana
                </a>
            </p>
            <p>Tai kopioi tämä linkki selaimeesi:</p>
            <p style="word-break: break-all; color: #666;">' . e($url) . '</p>
            <p>Linkki on voimassa 1 tunti.</p>
            <p>Jos et pyytänyt salasanan nollausta, voit ohittaa tämän viestin.</p>'
        );
        
        return $this->send($email, $subject, $body);
    }
    
    /**
     * Send notification about outbid
     */
    public function sendOutbidNotification($email, $auctionTitle, $auctionId) {
        $url = BASE_URL . '/auction.php?id=' . $auctionId;
        
        $subject = 'Sinut on ylitetty - ' . $auctionTitle;
        
        $body = $this->getEmailTemplate(
            'Sinut on ylitetty',
            '<p>Huutokaupassa "<strong>' . e($auctionTitle) . '</strong>" on tehty uusi tarjous ja sinut on ylitetty.</p>
            <p style="margin: 30px 0;">
                <a href="' . e($url) . '" style="background: #3b82f6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;">
                    Näytä huutokauppa
                </a>
            </p>
            <p>Voit tehdä uuden tarjouksen pysyäksesi mukana kilpailussa.</p>'
        );
        
        return $this->send($email, $subject, $body);
    }
    
    /**
     * Send notification about auction ending soon
     */
    public function sendEndingSoonNotification($email, $auctionTitle, $auctionId) {
        $url = BASE_URL . '/auction.php?id=' . $auctionId;
        
        $subject = 'Huutokauppa päättyy pian - ' . $auctionTitle;
        
        $body = $this->getEmailTemplate(
            'Huutokauppa päättyy pian',
            '<p>Seuraamasi huutokauppa "<strong>' . e($auctionTitle) . '</strong>" päättyy pian!</p>
            <p style="margin: 30px 0;">
                <a href="' . e($url) . '" style="background: #3b82f6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;">
                    Näytä huutokauppa
                </a>
            </p>
            <p>Tee tarjouksesi ennen kuin on liian myöhäistä!</p>'
        );
        
        return $this->send($email, $subject, $body);
    }
    
    /**
     * Get email HTML template
     */
    private function getEmailTemplate($title, $content) {
        return '<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . e($title) . '</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif; background-color: #f3f4f6;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f3f4f6; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="padding: 40px 30px; text-align: center; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: 8px 8px 0 0;">
                            <h1 style="margin: 0; color: white; font-size: 24px; font-weight: 600;">' . e(SITE_NAME) . '</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 40px 30px;">
                            <h2 style="margin: 0 0 20px 0; color: #111827; font-size: 20px; font-weight: 600;">' . e($title) . '</h2>
                            <div style="color: #374151; font-size: 16px; line-height: 1.6;">
                                ' . $content . '
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 30px; text-align: center; border-top: 1px solid #e5e7eb; color: #6b7280; font-size: 14px;">
                            <p style="margin: 0 0 10px 0;">© ' . date('Y') . ' ' . e(SITE_NAME) . '. Kaikki oikeudet pidätetään.</p>
                            <p style="margin: 0;"><a href="' . e(BASE_URL) . '" style="color: #3b82f6; text-decoration: none;">Siirry sivustolle</a></p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
    }
}

/**
 * Global helper to get Email instance
 */
function email() {
    static $instance = null;
    if ($instance === null) {
        $instance = new Email();
    }
    return $instance;
}
