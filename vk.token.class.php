<?php 

class VKTokenGenerator {
    private $session;
    private $userAgent = 'Mozilla/5.0 (Linux; Android 11; sdk_gphone_x86_arm Build/RSR1.200819.001.A1; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/83.0.4103.106 Mobile Safari/537.36';

    private $app = [
        'client_id' => '2274003',
        'client_secret' => 'hHbZxrka2uZ6jB1inYsH'
    ];

    const BASE_URL = 'https://oauth.vk.com/token';
    const API_VERSION = '5.131';

    const E_UNKNOWN = [
        'ok' => false,
        'error_code' => -1,
        'error_message' => 'Unknown error'
    ];

    public function __construct() {
        $this->session = curl_init();
        curl_setopt($this->session, CURLOPT_RETURNTRANSFER, true);
        $headers = [
            'Upgrade-Insecure-Requests: 1',
            'User-Agent: '.$this->userAgent,
            'Accept: */*',
            'Origin: https://static.vk.com',
            'X-Requested-With: com.vkontakte.android',
            'Sec-Fetch-Site: same-site',
            'Sec-Fetch-Mode: cors',
            'Sec-Fetch-Dest: empty',
            'Referer: https://static.vk.com/',
            'Accept-Language: en-US,en;q=0.9'
        ];
        curl_setopt($this->session, CURLOPT_HTTPHEADER, $headers);
    }

    public function getToken(array $data, bool $json = false) {
        if (!isset($data['username'])) throw new Exception('No username set');
        if (!isset($data['password'])) throw new Exception('No password set');
        
        $params = [
            'v' => self::API_VERSION,
            'grant_type' => 'password'
        ];
        $params = array_merge($params, $this->app, $data);

        $url = self::BASE_URL.'?'.http_build_query($params);
        curl_setopt($this->session, CURLOPT_URL, $url);
        curl_setopt($this->session, CURLOPT_FRESH_CONNECT, true);

        $response = json_decode(curl_exec($this->session), true);

        $error = $response['error'] ?? false;
        if ($error) {
            switch ($error) {
                case 'invalid_client':
                    $data = [
                        'ok' => false, 
                        'error_code' => 1,
                        'error_message' => 'Username or password is incorrect'
                    ];
                    return $json ? json_encode($data) : $data;
                    break;

                case 'need_captcha':
                    $data = [
                        'ok' => false,
                        'error_code' => 2,
                        'error_message' => 'Captcha needed, visit link in \'captcha_img\' and retry with [\'username\' => \''.$data['username'].'\', \'password\' => \''.$data['password'].'\', \'captcha_sid\' => \''.$response['captcha_sid'].'\', \'captcha_key\' => \'SOLVED_CAPTCHA\']',
                        'captcha_img' => $response['captcha_img'],
                        'captcha_sid' => $response['captcha_sid']
                    ];
                    return $json ? json_encode($data) : $data;
                    break;
                
                case 'need_validation':
                    if (isset($response['validation_sid'])) {
                        $data = [
                            'ok' => false,
                            'error_code' => 3,
                            'error_message' => '2FA needed, currently unsupported'
                        ];
                    } elseif (isset($response['ban_info'])) {
                        $data = [
                            'ok' => false,
                            'error_code' => 4,
                            'error_message' => 'Your account is banned'
                        ];
                    } else {
                        $data = self::E_UNKNOWN;
                    }
                    return $json ? json_encode($data) : $data;
                    break;

                default:
                    $data = self::E_UNKNOWN;
                    return $json ? json_encode($data) : $data;
                    break;
            }
        }

        $data = [
            'ok' => true,
            'access_token' => $response['access_token'],
            'user_id' => $response['user_id']
        ];
        return $json ? json_encode($data) : $data;
    }

    public function __destruct() {
        curl_close($this->session);
    }
}
