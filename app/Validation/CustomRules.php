<?php

namespace App\Validation;

use App\Libraries\ContentFilter;

class CustomRules
{
    /**
     * Memeriksa apakah teks bersih dari kata kasar, SARA, dan pornografi
     */
    public function clean_words(?string $str, ?string &$error = null): bool
    {
        if ($str === null || trim($str) === '') {
            return true;
        }

        if (ContentFilter::hasProfanity($str)) {
            $error = 'Nama tidak boleh mengandung kata kasar, tidak pantas, atau melanggar norma SARA.';
            return false;
        }

        return true;
    }

    /**
     * Memeriksa format nama (hanya huruf, spasi, titik, koma, petik tunggal, tanda hubung)
     */
    public function valid_name_format(?string $str, ?string &$error = null): bool
    {
        if ($str === null || trim($str) === '') {
            return true;
        }

        $specificError = null;
        if (! ContentFilter::isValidName($str, $specificError)) {
            $error = $specificError ?: 'Format nama tidak valid. Hanya diperbolehkan huruf alfabet dengan batas tanda baca (. , \' -) yang wajar.';
            return false;
        }

        return true;
    }
}
