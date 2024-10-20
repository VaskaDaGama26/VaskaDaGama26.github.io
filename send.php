<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получаем данные из формы и очищаем
    $fio = htmlspecialchars(trim($_POST['fio']));
    $email = htmlspecialchars(trim($_POST['email']));
    $message = htmlspecialchars(trim($_POST['message']));
    $consent = isset($_POST['consent']); // Проверка согласия

    // Валидация данных
    $errors = [];

    // Проверка ФИО
    if (empty($fio) || !preg_match("/^[А-Яа-яЁё\s]+$/u", $fio)) {
        $errors[] = "Введите корректное ФИО.";
    }

    // Проверка email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Введите корректный адрес электронной почты.";
    }

    // Проверка сообщения
    if (empty($message) || strlen($message) < 10) {
        $errors[] = "Сообщение должно содержать минимум 10 символов.";
    }

    // Проверка согласия
    if (!$consent) {
        $errors[] = "Необходимо согласие на обработку персональных данных.";
    }

    // Если есть ошибки, выводим их
    if (!empty($errors)) {
        echo implode("<br>", $errors);
    } else {
        // Данные для Telegram
        $token = '6026150952:AAEMD-gghRUnDqQPZH_4jRnbks4GYXs6mDs';  // Замените на токен вашего бота
        $chat_id = '386557013';  // Замените на ваш chat_id
        $text = "Новое сообщение с сайта:\n\nФИО: $fio\nEmail: $email\nСообщение: $message";

        // URL для отправки сообщения в Telegram
        $url = "https://api.telegram.org/bot{$token}/sendMessage";

        // Параметры для отправки
        $data = [
            'chat_id' => $chat_id,
            'text' => $text,
            'parse_mode' => 'HTML' // Можно использовать HTML-разметку
        ];

        // Настройки cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Отправка сообщения и получение ответа
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Декодируем ответ
        $response_data = json_decode($response, true);

        // Проверка на успех
        if ($http_code == 200 && isset($response_data['ok']) && $response_data['ok']) {
            echo "Сообщение успешно отправлено в Telegram!";
        } else {
            echo "Ошибка при отправке сообщения в Telegram: " . $response_data['description'];
        }
    }
} else {
    echo "Неверный метод запроса.";
}
