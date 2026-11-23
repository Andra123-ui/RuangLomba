<?php

namespace App\Libraries;

class ContentFilter
{
    /**
     * Daftar kata terlarang (SARA, ujaran kebencian, makian, pornografi, dan variasi evasion/slang)
     */
    private const BAD_WORDS = [
        // Kata kotor / makian / hewan penghinaan
        'anjing', 'anying', 'anjir', 'anjrit', 'asw', 'asu', 'bajingan', 'bangsat', 'babi',
        'monyet', 'kunyuk', 'kampang', 'pantek', 'puki', 'kimak', 'pukimak', 'bodat',
        'brengsek', 'tai', 'taek', 'tahi', 'bego', 'goblok', 'tolol', 'idiot', 'geblek',
        'dungu', 'kampret', 'setan', 'iblis', 'dajal', 'dajjal',

        // Organ intim & seksualitas vulgar + variasi evasion & typo
        'kontol', 'kontdol', 'kondol', 'kntl', 'kntol', 'kntd', 'kontl', 'palkon',
        'memek', 'memk', 'mmk', 'itil', 'jembut', 'pepek', 'pepk', 'ppk', 'peler', 'titit',
        'ngentot', 'ngentod', 'entot', 'crot', 'colmek', 'coli', 'onani', 'masturbasi',
        'toket', 'tetek', 'nenen', 'lonte', 'perek', 'pelacur', 'sundal', 'jablay',
        'bokep', 'porno', 'porn', 'bokepindo', 'seks', 'vagina', 'penis',
        'ngewe', 'ewe', 'sange', 'sagne', 'peju',

        // Singkatan makian populer
        'anjg', 'ajg', 'anj', 'bngst', 'bgst', 'bjngn', 'bjg', 'pntk', 'gblk', 'tll',

        // SARA / Rasisme / Ujaran Kebencian / Ekstremisme
        'kafir', 'infidel', 'cebong', 'kadrun', 'antek', 'komunis', 'pki',
        'teroris', 'nazi', 'hitler', 'zionis', 'yahudi', 'khilafah', 'radikal',

        // Makian Bahasa Inggris umum
        'fuck', 'fucker', 'fucking', 'bitch', 'bastard', 'asshole', 'dick', 'pussy',
        'cunt', 'whore', 'slut', 'nigger', 'nigga', 'fag', 'faggot', 'shit', 'bullshit',
    ];

    /**
     * Normalisasi teks leetspeak dan penghapusan simbol pengabur
     */
    public static function normalize(string $text): array
    {
        $text = mb_strtolower(trim($text));

        // Peta penggantian karakter angka/simbol ke huruf alfabet
        $leetspeak = [
            '4' => 'a', '@' => 'a', '^' => 'a',
            '8' => 'b',
            '3' => 'e',
            '1' => 'i', '!' => 'i', '|' => 'i',
            '0' => 'o',
            '5' => 's', '$' => 's',
            '7' => 't', '+' => 't',
            'v' => 'u',
        ];

        $normalized = strtr($text, $leetspeak);
        $cleaned    = preg_replace('/[.\-_,+=~`\'"\/\s]/', '', $normalized);

        // Versi 1: reduksi huruf berulang >= 3 kali (contoh: anjjjiiing -> anjing)
        $deduped3 = preg_replace('/(.)\1{2,}/', '$1', $cleaned);

        // Versi 2: reduksi penuh semua huruf berulang (contoh: kkoontdol -> kontdol)
        $dedupedAll = preg_replace('/(.)\1+/', '$1', $cleaned);

        return [
            'raw'         => $text,
            'cleaned'     => $cleaned,
            'deduped3'    => $deduped3,
            'deduped_all' => $dedupedAll,
        ];
    }

    /**
     * Cek apakah teks mengandung kata terlarang / SARA / makian
     */
    public static function hasProfanity(string $text): bool
    {
        if (trim($text) === '') {
            return false;
        }

        $norm     = self::normalize($text);
        $rawLower = $norm['raw'];
        // Tokenisasi kata berdasarkan spasi dan simbol
        $words = preg_split('/[\s\-_.,+=~`\'"\/\d]+/', $rawLower, -1, PREG_SPLIT_NO_EMPTY);

        foreach (self::BAD_WORDS as $badWord) {
            $bwLen = strlen($badWord);

            // 1. Cek kecocokan kata utuh pada token teks asli
            if (in_array($badWord, $words, true)) {
                return true;
            }

            // 2. Cek boundary regex pada teks asli
            $pattern = '/\b' . preg_quote($badWord, '/') . '\b/i';
            if (preg_match($pattern, $rawLower)) {
                return true;
            }

            // 3. Untuk kata pendek (<= 3 karakter seperti asu, tai, ewe):
            // Hanya cocok jika identik dengan seluruh kata/token (mencegah false positive seperti 'Sulaiman' atau 'Hasri')
            if ($bwLen <= 3) {
                if ($norm['cleaned'] === $badWord || $norm['deduped_all'] === $badWord) {
                    return true;
                }
                foreach ($words as $w) {
                    $wClean = preg_replace('/(.)\1+/', '$1', $w);
                    if ($wClean === $badWord) {
                        return true;
                    }
                }
                continue;
            }

            // 4. Untuk kata >= 4 huruf (kontol, anjing, memek, dll):
            // Cek substring pada teks cleaned atau deduped
            if (str_contains($norm['cleaned'], $badWord) ||
                str_contains($norm['deduped3'], $badWord) ||
                str_contains($norm['deduped_all'], $badWord)) {
                return true;
            }

            // 5. Fuzzy match dengan jarak Levenshtein <= 1 untuk kata panjang (>= 5 huruf)
            // Mendeteksi sisipan huruf / typo evasion (misal: 'kontdol' vs 'kontol')
            if ($bwLen >= 5) {
                $target    = $norm['deduped_all'];
                $targetLen = strlen($target);
                for ($i = 0; $i <= $targetLen - $bwLen; $i++) {
                    $sub = substr($target, $i, $bwLen);
                    if (levenshtein($sub, $badWord) <= 1) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Validasi format nama dan pembatasan tanda baca (. , ' -)
     * Memberikan alasan spesifik jika tidak valid melalui parameter &$errorMsg.
     */
    public static function isValidName(string $name, ?string &$errorMsg = null): bool
    {
        $name = trim($name);
        $len  = mb_strlen($name);

        if ($len < 3 || $len > 100) {
            $errorMsg = 'Panjang nama harus antara 3 sampai 100 karakter.';
            return false;
        }

        // Hanya alfabet, spasi, titik, koma, petik tunggal, dan tanda hubung
        if (!preg_match('/^[a-zA-Z\s\.,\'\-]+$/u', $name)) {
            $errorMsg = 'Nama hanya boleh berisi huruf alfabet, spasi, dan tanda baca (. , \' -).';
            return false;
        }

        // Harus diawali huruf alfabet kapital
        if (!preg_match('/^[A-Z]/', $name)) {
            $errorMsg = 'Nama harus diawali dengan huruf kapital.';
            return false;
        }

        // Setiap awal kata harus diawali huruf kapital (contoh: Budi Santoso)
        $words = preg_split('/[\s,]+/', $name, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($words as $word) {
            // Huruf pertama dari setiap kata harus huruf kapital
            if (!preg_match('/^[A-Z]/', $word)) {
                $errorMsg = 'Setiap awal kata pada nama harus menggunakan huruf kapital (contoh: Budi Santoso).';
                return false;
            }

            // Jika ada tanda hubung (-), kata setelah tanda hubung juga harus diawali huruf kapital (contoh: Nurhaliza-Sulaiman)
            if (preg_match('/-[a-z]/', $word)) {
                $errorMsg = 'Kata setelah tanda hubung (-) harus diawali dengan huruf kapital (contoh: Nurhaliza-Sulaiman).';
                return false;
            }

            // Jika ada gelar/singkatan dengan titik (contoh: S.Kom. atau M.T.), huruf setelah titik harus huruf kapital
            if (preg_match('/\.[a-z]/', $word)) {
                $errorMsg = 'Singkatan gelar setelah tanda titik harus menggunakan huruf kapital (contoh: S.Kom., M.T.).';
                return false;
            }
        }

        // Harus diakhiri huruf alfabet atau titik (untuk gelar akhir seperti S.Kom.)
        if (!preg_match('/[a-zA-Z.]$/', $name)) {
            $errorMsg = 'Nama tidak boleh diakhiri dengan simbol selain tanda titik gelar.';
            return false;
        }

        // Harus memiliki minimal satu huruf vokal
        if (!preg_match('/[aiueoAIUEO]/', $name)) {
            $errorMsg = 'Nama tidak valid (wajib memiliki huruf vokal).';
            return false;
        }

        // Batas jumlah simbol agar nama tidak disalahgunakan / disensor-sensor:
        $dotCount    = substr_count($name, '.');
        $commaCount  = substr_count($name, ',');
        $quoteCount  = substr_count($name, "'");
        $hyphenCount = substr_count($name, '-');

        // Titik (.) maksimal 5 (untuk inisial nama dan singkatan gelar, misal: Dr. Ir. H. Ahmad Dahlan, M.T.)
        if ($dotCount > 5) {
            $errorMsg = 'Tanda titik (.) maksimal 5 kali (hanya untuk inisial atau singkatan gelar).';
            return false;
        }

        // Koma (,) maksimal 2 (hanya untuk pemisah gelar, misal: Nama, S.Kom., M.Cs.)
        if ($commaCount > 2) {
            $errorMsg = 'Tanda koma (,) maksimal 2 kali (hanya untuk pemisah gelar).';
            return false;
        }

        // Petik tunggal (') maksimal 2 (untuk nama khas seperti Ma\'ruf atau Sa\'adah)
        if ($quoteCount > 2) {
            $errorMsg = 'Tanda petik (\') maksimal 2 kali.';
            return false;
        }

        // Tanda hubung (-) maksimal 2 (untuk nama ganda)
        if ($hyphenCount > 2) {
            $errorMsg = 'Tanda hubung (-) maksimal 2 kali.';
            return false;
        }

        // Simbol tidak boleh berurutan (seperti .. atau ,, atau '' atau --)
        if (preg_match('/\.{2,}|,{2,}|\'{2,}|-{2,}/', $name)) {
            $errorMsg = 'Tanda baca tidak boleh digunakan berurutan (seperti .. atau ,,).';
            return false;
        }

        // Dilarang titik diapit langsung huruf kecil tanpa spasi (trik evasion seperti kk.oo.ntdol, a.n.j.i.n.g)
        if (preg_match('/[a-z]\.[a-z]/', $name)) {
            $errorMsg = 'Tanda titik tidak boleh disisipkan di antara huruf kecil tanpa spasi.';
            return false;
        }

        // Dilarang titik setelah kata (>= 2 huruf) yang langsung menempel ke huruf berikutnya tanpa spasi (misal: Ahmad.Dahlan, KK.OO)
        if (preg_match('/[a-zA-Z]{2,}\.[a-zA-Z]/', $name)) {
            $errorMsg = 'Tanda titik setelah kata harus diikuti spasi atau koma.';
            return false;
        }

        // Dilarang pola petik diapit huruf kecil berulang (misal k\'o\'n\'t\'o\'l)
        if (preg_match('/[a-zA-Z]\'[a-zA-Z]\'[a-zA-Z]/', $name)) {
            $errorMsg = 'Tanda petik tidak wajar pada kata.';
            return false;
        }

        // Rasio huruf alfabet minimal 70% dari seluruh karakter non-spasi
        $noSpaces   = str_replace(' ', '', $name);
        $alphaCount = preg_match_all('/[a-zA-Z]/', $noSpaces);
        if ($alphaCount > 0 && ($alphaCount / strlen($noSpaces)) < 0.7) {
            $errorMsg = 'Nama mengandung terlalu banyak tanda baca non-alfabet.';
            return false;
        }

        return true;
    }

    /**
     * Temukan kata terlarang pertama (untuk keperluan pesan error/debug)
     */
    public static function findBadWord(string $text): ?string
    {
        $norm     = self::normalize($text);
        $rawLower = $norm['raw'];
        $words    = preg_split('/[\s\-_.,+=~`\'"\/\d]+/', $rawLower, -1, PREG_SPLIT_NO_EMPTY);

        foreach (self::BAD_WORDS as $badWord) {
            $bwLen = strlen($badWord);

            if (in_array($badWord, $words, true)) {
                return $badWord;
            }

            if ($bwLen <= 3) {
                if ($norm['cleaned'] === $badWord || $norm['deduped_all'] === $badWord) {
                    return $badWord;
                }
                continue;
            }

            if (str_contains($norm['cleaned'], $badWord) ||
                str_contains($norm['deduped3'], $badWord) ||
                str_contains($norm['deduped_all'], $badWord)) {
                return $badWord;
            }

            if ($bwLen >= 5) {
                $target    = $norm['deduped_all'];
                $targetLen = strlen($target);
                for ($i = 0; $i <= $targetLen - $bwLen; $i++) {
                    $sub = substr($target, $i, $bwLen);
                    if (levenshtein($sub, $badWord) <= 1) {
                        return $badWord;
                    }
                }
            }
        }

        return null;
    }
}
