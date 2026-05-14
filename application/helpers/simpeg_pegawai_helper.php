<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('simpeg_pangkat_terakhir_options')) {
    function simpeg_pangkat_terakhir_options()
    {
        return array(
            'I/a: Juru Muda' => 'I/a: Juru Muda',
            'I/b: Juru Muda Tingkat I' => 'I/b: Juru Muda Tingkat I',
            'I/c: Juru' => 'I/c: Juru',
            'I/d: Juru Tingkat I' => 'I/d: Juru Tingkat I',
            'II/a: Pengatur Muda' => 'II/a: Pengatur Muda',
            'II/b: Pengatur Muda Tingkat I' => 'II/b: Pengatur Muda Tingkat I',
            'II/c: Pengatur' => 'II/c: Pengatur',
            'II/d: Pengatur Tingkat I' => 'II/d: Pengatur Tingkat I',
            'III/a: Penata Muda' => 'III/a: Penata Muda',
            'III/b: Penata Muda Tingkat I' => 'III/b: Penata Muda Tingkat I',
            'III/c: Penata' => 'III/c: Penata',
            'III/d: Penata Tingkat I' => 'III/d: Penata Tingkat I',
            'IV/a: Pembina' => 'IV/a: Pembina',
            'IV/b: Pembina Tingkat I' => 'IV/b: Pembina Tingkat I',
            'IV/c: Pembina Utama Muda' => 'IV/c: Pembina Utama Muda',
            'IV/d: Pembina Utama Madya' => 'IV/d: Pembina Utama Madya',
            'IV/e: Pembina Utama' => 'IV/e: Pembina Utama',
        );
    }
}

if (!function_exists('simpeg_prepare_pangkat_terakhir_options')) {
    function simpeg_prepare_pangkat_terakhir_options($selected_value = '')
    {
        $options = simpeg_pangkat_terakhir_options();
        $selected_value = trim((string) $selected_value);

        if ($selected_value !== '' && !isset($options[$selected_value])) {
            $options = array($selected_value => $selected_value) + $options;
        }

        return $options;
    }
}

if (!function_exists('simpeg_is_valid_pangkat_terakhir')) {
    function simpeg_is_valid_pangkat_terakhir($value, $additional_allowed = array())
    {
        $value = trim((string) $value);

        if ($value === '') {
            return TRUE;
        }

        $allowed_values = array_keys(simpeg_pangkat_terakhir_options());

        foreach ((array) $additional_allowed as $allowed) {
            $allowed = trim((string) $allowed);

            if ($allowed !== '') {
                $allowed_values[] = $allowed;
            }
        }

        return in_array($value, array_unique($allowed_values), TRUE);
    }
}
