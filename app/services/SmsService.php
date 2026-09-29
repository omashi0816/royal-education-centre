<?php
/**
 * SMS delivery service for OTP messages.
 */
class SmsService {
    public static function sendOtp($phone, $otp) {
        if (TWILIO_ACCOUNT_SID === '' || TWILIO_AUTH_TOKEN === '' || TWILIO_FROM_NUMBER === '') {
            if (OTP_DEMO_MODE) {
                return [
                    'success' => true,
                    'demo' => true,
                    'message' => 'Development demo mode: no SMS was sent.'
                ];
            }

            return ['success' => false, 'error' => 'SMS service is not configured. Add the Twilio credentials in app/config/config.php.'];
        }

        if (!function_exists('curl_init')) {
            return ['success' => false, 'error' => 'The PHP cURL extension is required to send OTP SMS messages.'];
        }

        $to = self::normalizePhone($phone);
        $endpoint = 'https://api.twilio.com/2010-04-01/Accounts/' . rawurlencode(TWILIO_ACCOUNT_SID) . '/Messages.json';
        $postFields = http_build_query([
            'To' => $to,
            'From' => TWILIO_FROM_NUMBER,
            'Body' => APP_NAME . ' payment verification code: ' . $otp . '. This code expires in 5 minutes.'
        ]);

        $curl = curl_init($endpoint);
        curl_setopt_array($curl, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_USERPWD => TWILIO_ACCOUNT_SID . ':' . TWILIO_AUTH_TOKEN,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded']
        ]);
        $response = curl_exec($curl);
        $httpCode = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlError = curl_error($curl);
        curl_close($curl);

        if ($response === false || $httpCode < 200 || $httpCode >= 300) {
            $twilioError = '';
            if ($response) {
                $decodedResponse = json_decode($response, true);
                $twilioError = $decodedResponse['message'] ?? '';
            }
            return ['success' => false, 'error' => $curlError ?: $twilioError ?: 'Unable to send OTP SMS. Please try again.'];
        }

        return ['success' => true];
    }

    private static function normalizePhone($phone) {
        $phone = preg_replace('/\s+/', '', $phone);
        if (strpos($phone, '0') === 0) {
            return '+94' . substr($phone, 1);
        }
        if (strpos($phone, '94') === 0) {
            return '+' . $phone;
        }
        return $phone;
    }
}