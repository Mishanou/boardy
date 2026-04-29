<?php
// functions.php - вспомогательные функции

/**
 * Форматирует дату в человеко-читаемый вид
 * 
 * @param string $datetime Дата и время в формате YYYY-MM-DD HH:MM:SS
 * @return string Отформатированная дата
 */
function time_ago($datetime) {
    $timestamp = strtotime($datetime);
    $now = time();
    $diff = $now - $timestamp;
    
    if ($diff < 60) {
        return 'только что';
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return $minutes . ' ' . get_minute_text($minutes) . ' назад';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' ' . get_hour_text($hours) . ' назад';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        if ($days == 1) {
            return 'вчера';
        }
        return $days . ' ' . get_day_text($days) . ' назад';
    } else {
        return date('d.m.Y', $timestamp);
    }
}

/**
 * Возвращает правильное склонение для слова "минута"
 */
function get_minute_text($minutes) {
    if ($minutes % 10 == 1 && $minutes % 100 != 11) return 'минуту';
    if ($minutes % 10 >= 2 && $minutes % 10 <= 4 && ($minutes % 100 < 10 || $minutes % 100 >= 20)) return 'минуты';
    return 'минут';
}

/**
 * Возвращает правильное склонение для слова "час"
 */
function get_hour_text($hours) {
    if ($hours % 10 == 1 && $hours % 100 != 11) return 'час';
    if ($hours % 10 >= 2 && $hours % 10 <= 4 && ($hours % 100 < 10 || $hours % 100 >= 20)) return 'часа';
    return 'часов';
}

/**
 * Возвращает правильное склонение для слова "день"
 */
function get_day_text($days) {
    if ($days % 10 == 1 && $days % 100 != 11) return 'день';
    if ($days % 10 >= 2 && $days % 10 <= 4 && ($days % 100 < 10 || $days % 100 >= 20)) return 'дня';
    return 'дней';
}

/**
 * Безопасное экранирование HTML
 * 
 * @param string $data Входные данные
 * @return string Экранированная строка
 */
function escape($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Обрезка текста до определенной длины
 * 
 * @param string $text Текст для обрезки
 * @param int $length Максимальная длина
 * @param string $suffix Суффикс для обрезанного текста
 * @return string Обрезанный текст
 */
function truncate($text, $length = 200, $suffix = '...') {
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    return mb_substr($text, 0, $length) . $suffix;
}