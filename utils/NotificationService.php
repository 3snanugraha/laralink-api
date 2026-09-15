<?php
/**
 * Notification Service
 *
 * Mengirim push notification ke user via Expo Push API.
 * Token disimpan di kolom fcm_token pada tabel users.
 */
class NotificationService
{
    private $db;
    private $logger;

    public function __construct($db = null)
    {
        if ($db === null) {
            require_once __DIR__ . '/../config/database.php';
            $database = new Database();
            $this->db = $database->getConnection();
        } else {
            $this->db = $db;
        }

        require_once __DIR__ . '/Logger.php';
        $this->logger = new Logger();
    }

    /**
     * Kirim notifikasi ke semua user yang memiliki push token
     *
     * @param string $title Judul notifikasi
     * @param string $body  Isi notifikasi
     * @param array  $data  Data tambahan (opsional)
     * @return array Hasil pengiriman
     */
    public function sendToAllUsers($title, $body, $data = [])
    {
        $tokens = $this->getAllUserTokens();

        if (empty($tokens)) {
            $this->logger->log("NotificationService: Tidak ada user dengan push token");
            return ['success' => 0, 'failed' => 0, 'total' => 0];
        }

        return $this->sendBatch($tokens, $title, $body, $data);
    }

    /**
     * Kirim notifikasi ke user tertentu
     *
     * @param int    $userId User ID
     * @param string $title  Judul notifikasi
     * @param string $body   Isi notifikasi
     * @param array  $data   Data tambahan (opsional)
     * @return bool Berhasil atau tidak
     */
    public function sendToUser($userId, $title, $body, $data = [])
    {
        $token = $this->getUserToken($userId);

        if (empty($token)) {
            $this->logger->log("NotificationService: User $userId tidak memiliki push token");
            return false;
        }

        $result = $this->sendBatch([$token], $title, $body, $data);
        return $result['success'] > 0;
    }

    /**
     * Kirim notifikasi ke beberapa user berdasarkan ID
     *
     * @param array  $userIds Array of user IDs
     * @param string $title   Judul notifikasi
     * @param string $body    Isi notifikasi
     * @param array  $data    Data tambahan (opsional)
     * @return array Hasil pengiriman
     */
    public function sendToUsers($userIds, $title, $body, $data = [])
    {
        $tokens = [];

        foreach ($userIds as $userId) {
            $token = $this->getUserToken($userId);
            if (!empty($token)) {
                $tokens[] = $token;
            }
        }

        if (empty($tokens)) {
            return ['success' => 0, 'failed' => 0, 'total' => 0];
        }

        return $this->sendBatch($tokens, $title, $body, $data);
    }

    /**
     * Ambil semua push token dari user aktif
     *
     * @return array Array of push tokens
     */
    private function getAllUserTokens()
    {
        $query = "SELECT fcm_token FROM users 
                  WHERE fcm_token IS NOT NULL 
                  AND fcm_token != '' 
                  AND status = 'active'
                  AND role = 'user'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        $tokens = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tokens[] = $row['fcm_token'];
        }

        return $tokens;
    }

    /**
     * Ambil push token milik user tertentu
     *
     * @param int $userId User ID
     * @return string|null Push token atau null
     */
    private function getUserToken($userId)
    {
        $query = "SELECT fcm_token FROM users WHERE user_id = ? AND status = 'active'";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$userId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($row && !empty($row['fcm_token'])) ? $row['fcm_token'] : null;
    }

    /**
     * Kirim batch notifikasi via Expo Push API
     *
     * @param array  $tokens Array push token
     * @param string $title  Judul notifikasi
     * @param string $body   Isi notifikasi
     * @param array  $data   Data tambahan
     * @return array Hasil pengiriman
     */
    private function sendBatch($tokens, $title, $body, $data = [])
    {
        $messages = [];
        foreach ($tokens as $token) {
            $message = [
                'to' => $token,
                'sound' => 'default',
                'title' => $title,
                'body' => $body,
                'channelId' => 'laralink-notifications',
            ];

            if (!empty($data)) {
                $message['data'] = $data;
            }

            $messages[] = $message;
        }

        // Expo Push API menerima max 100 pesan per request
        $chunks = array_chunk($messages, 100);
        $successCount = 0;
        $failedCount = 0;

        foreach ($chunks as $chunk) {
            $result = $this->sendToExpoAPI($chunk);

            if ($result !== false) {
                // Hitung success dan failed dari response
                if (isset($result['data'])) {
                    foreach ($result['data'] as $item) {
                        if (isset($item['status']) && $item['status'] === 'ok') {
                            $successCount++;
                        } else {
                            $failedCount++;
                            // Log token yang gagal untuk debug
                            $errorMsg = isset($item['message']) ? $item['message'] : 'Unknown error';
                            $this->logger->log("NotificationService: Gagal kirim - " . $errorMsg);
                        }
                    }
                } else {
                    $successCount += count($chunk);
                }
            } else {
                $failedCount += count($chunk);
            }
        }

        $this->logger->log("NotificationService: Terkirim $successCount, Gagal $failedCount dari " . count($tokens) . " token");

        return [
            'success' => $successCount,
            'failed' => $failedCount,
            'total' => count($tokens)
        ];
    }

    /**
     * Kirim request ke Expo Push API
     *
     * @param array $messages Array of message objects
     * @return array|false Response atau false jika gagal
     */
    private function sendToExpoAPI($messages)
    {
        $url = 'https://exp.host/--/api/v2/push/send';

        $headers = [
            'Accept: application/json',
            'Accept-encoding: gzip, deflate',
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($messages),
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            $this->logger->log("NotificationService: cURL error - $error");
            return false;
        }

        if ($httpCode !== 200) {
            $this->logger->log("NotificationService: HTTP $httpCode - $response");
            return false;
        }

        $decoded = json_decode($response, true);
        return $decoded ?: false;
    }
}
