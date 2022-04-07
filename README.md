### About / Описание
Obtaining official VK API token through unofficial (unlisted) method. For official method refer to [this article.](https://dev.vk.com/api/access-token/getting-started) 

Получение официального токена VK API через неофициальный (скрытый) метод. Для использования официального метода обратитесь к [этой статье.](https://dev.vk.com/api/access-token/getting-started)

### Usage / Применение

```php
$generator = new VKTokenGenerator();

$params = [
    'username' => '89001112233',
    'password' => 'a1b2c3d4e5',
];

$data = $generator->getToken($params); // optional JSON encoding, add 'true' as a 2nd argument
print_r($data);
```
__Output / Вывод__

```
[ok] => 1
[access_token] => 58adc18305c04d036dred59e431c8aff40a5ge8393b99a7s7d0bbaae819e8371f5de6220084db3ae47046
[user_id] => 123456
```

### Errors / Ошибки
In some cases `getToken()` method can return `ok = false` and additional error info.

В некоторых случаях метод `getToken()` может вернуть `ok = false` и дополнительную информацию об ошибке.

__Invalid username or password / Неправильные логин или пароль__
```
[ok] => 
[error_code] => 1
[error_message] => Username or password is incorrect
```

__Captcha needed / Нужно решить капчу__
```
[ok] => 
[error_code] => 2
[error_message] => Captcha needed, visit 'captcha_img' and retry with ['username' => '89001112233', 'password' => 'a1b2c3d4e5', 'captcha_sid' => '283753294399', 'captcha_key' => 'SOLVED_CAPTCHA']
[captcha_img] => https://api.vk.com/captcha.php?sid=283753294399
[captcha_sid] => 283753294399
```

In this case you should go to `captcha_img` link, solve the captcha and retry as specified in `error_message`.

В этом случае нужно зайти по ссылке, указанной в `captcha_img`, решить капчу и попробовать заново, как указано в `error_message`.

```php
$generator = new VKTokenGenerator();

$params = [
    'username' => '89001112233',
    'password' => 'a1b2c3d4e5',
    'captcha_sid' => '283753294399',
    'captcha_key' => 'ke337k'
];

$data = $generator->getToken($params);
print_r($data);
```

__2FA validation needed / Нужно ввести код 2FA__
```
[ok] => 
[error_code] => 3
[error_message] => '2FA needed, currently unsupported'
```

__Banned account / Аккаунт заблокирован__
```
[ok] => 
[error_code] => 4
[error_message] => 'Your account is banned'
```
