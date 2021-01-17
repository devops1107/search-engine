<?php

namespace spark\drivers\I18n;

use spark\drivers\I18n\I18n;

/**
* Locale Driver
*
* @package spark
*/
class Locale
{
    /**
     * Default textdomain
     */
    const DEFAULT_TEXTDOMAIN = '_spark';

    /**
     * Cookie name for preferred language
     */
    const COOKIE_NAME = '__based_locale';

    const DEFAULT_LOCALE = 'en_US';

    const THEME_LOCALE_DIR = 'locale';

    /**
     * Registered locale
     *
     * @var array
     */
    protected $locales = [];

    /**
     * Theme languages
     *
     * @var array
     */
    protected $themeLanguages;

    /**
     * Dashboard Languages
     *
     * @var array
     */
    protected $dashboardLanguages;

    /**
     * List of locale names
     *
     * @var [type]
     */
    protected static $localeNames = [
     "af_NA" => "Afrikaans (NA)",
     "af_ZA" => "Afrikaans (ZA)",
     "af" => "Afrikaans",
     "ak_GH" => "Akan (GH)",
     "ak" => "Akan",
     "sq_AL" => "Albanian (AL)",
     "sq" => "Albanian",
     "am_ET" => "Amharic (ET)",
     "am" => "Amharic",
     "ar_DZ" => "Arabic (DZ)",
     "ar_BH" => "Arabic (BH)",
     "ar_EG" => "Arabic (EG)",
     "ar_IQ" => "Arabic (IQ)",
     "ar_JO" => "Arabic (JO)",
     "ar_KW" => "Arabic (KW)",
     "ar_LB" => "Arabic (LB)",
     "ar_LY" => "Arabic (LY)",
     "ar_MA" => "Arabic (MA)",
     "ar_OM" => "Arabic (OM)",
     "ar_QA" => "Arabic (QA)",
     "ar_SA" => "Arabic (SA)",
     "ar_SD" => "Arabic (SD)",
     "ar_SY" => "Arabic (SY)",
     "ar_TN" => "Arabic (TN)",
     "ar_AE" => "Arabic (AE)",
     "ar_YE" => "Arabic (YE)",
     "ar" => "Arabic",
     "hy_AM" => "Armenian (AM)",
     "hy" => "Armenian",
     "as_IN" => "Assamese (IN)",
     "as" => "Assamese",
     "asa_TZ" => "Asu (TZ)",
     "asa" => "Asu",
     "az_Cyrl" => "Azerbaijani (Cyrl)",
     "az_Cyrl_AZ" => "Azerbaijani (AZ)",
     "az_Latn" => "Azerbaijani (Latn)",
     "az_Latn_AZ" => "Azerbaijani (AZ)",
     "az" => "Azerbaijani",
     "bm_ML" => "Bambara (ML)",
     "bm" => "Bambara",
     "eu_ES" => "Basque (ES)",
     "eu" => "Basque",
     "be_BY" => "Belarusian (BY)",
     "be" => "Belarusian",
     "bem_ZM" => "Bemba (ZM)",
     "bem" => "Bemba",
     "bez_TZ" => "Bena (TZ)",
     "bez" => "Bena",
     "bn_BD" => "Bengali (BD)",
     "bn_IN" => "Bengali (IN)",
     "bn" => "Bengali",
     "bs_BA" => "Bosnian (BA)",
     "bs" => "Bosnian",
     "bg_BG" => "Bulgarian (BG)",
     "bg" => "Bulgarian",
     "my_MM" => "Burmese (MM)",
     "my" => "Burmese",
     "yue_Hant_HK" => "Cantonese (HK)",
     "ca_ES" => "Catalan (ES)",
     "ca" => "Catalan",
     "tzm_Latn" => "Central (Latn)",
     "tzm_Latn_MA" => "Central (MA)",
     "tzm" => "Central Morocco Tamazight",
     "chr_US" => "Cherokee (US)",
     "chr" => "Cherokee",
     "cgg_UG" => "Chiga (UG)",
     "cgg" => "Chiga",
     "zh_Hans" => "Chinese (Hans)",
     "zh_Hans_CN" => "Chinese (CN)",
     "zh_Hans_HK" => "Chinese (HK)",
     "zh_Hans_MO" => "Chinese (MO)",
     "zh_Hans_SG" => "Chinese (SG)",
     "zh_Hant" => "Chinese (Hant)",
     "zh_Hant_HK" => "Chinese (HK)",
     "zh_Hant_MO" => "Chinese (MO)",
     "zh_Hant_TW" => "Chinese (TW)",
     "zh" => "Chinese",
     "kw_GB" => "Cornish (GB)",
     "kw" => "Cornish",
     "hr_HR" => "Croatian (HR)",
     "hr" => "Croatian",
     "cs_CZ" => "Czech (CZ)",
     "cs" => "Czech",
     "da_DK" => "Danish (DK)",
     "da" => "Danish",
     "nl_BE" => "Dutch (BE)",
     "nl_NL" => "Dutch (NL)",
     "nl" => "Dutch",
     "ebu_KE" => "Embu (KE)",
     "ebu" => "Embu",
     "en_AS" => "English (AS)",
     "en_AU" => "English (AU)",
     "en_BE" => "English (BE)",
     "en_BZ" => "English (BZ)",
     "en_BW" => "English (BW)",
     "en_CA" => "English (CA)",
     "en_GU" => "English (GU)",
     "en_HK" => "English (HK)",
     "en_IN" => "English (IN)",
     "en_IE" => "English (IE)",
     "en_IL" => "English (IL)",
     "en_JM" => "English (JM)",
     "en_MT" => "English (MT)",
     "en_MH" => "English (MH)",
     "en_MU" => "English (MU)",
     "en_NA" => "English (NA)",
     "en_NZ" => "English (NZ)",
     "en_MP" => "English (MP)",
     "en_PK" => "English (PK)",
     "en_PH" => "English (PH)",
     "en_SG" => "English (SG)",
     "en_ZA" => "English (ZA)",
     "en_TT" => "English (TT)",
     "en_UM" => "English (UM)",
     "en_VI" => "English (VI)",
     "en_GB" => "English (GB)",
     "en_US" => "English (US)",
     "en_ZW" => "English (ZW)",
     "en" => "English",
     "eo" => "Esperanto",
     "et_EE" => "Estonian (EE)",
     "et" => "Estonian",
     "ee_GH" => "Ewe (GH)",
     "ee_TG" => "Ewe (TG)",
     "ee" => "Ewe",
     "fo_FO" => "Faroese (FO)",
     "fo" => "Faroese",
     "fil_PH" => "Filipino (PH)",
     "fil" => "Filipino",
     "fi_FI" => "Finnish (FI)",
     "fi" => "Finnish",
     "fr_BE" => "French (BE)",
     "fr_BJ" => "French (BJ)",
     "fr_BF" => "French (BF)",
     "fr_BI" => "French (BI)",
     "fr_CM" => "French (CM)",
     "fr_CA" => "French (CA)",
     "fr_CF" => "French (CF)",
     "fr_TD" => "French (TD)",
     "fr_KM" => "French (KM)",
     "fr_CG" => "French (CG)",
     "fr_CD" => "French (CD)",
     "fr_CI" => "French (CI)",
     "fr_DJ" => "French (DJ)",
     "fr_GQ" => "French (GQ)",
     "fr_FR" => "French (FR)",
     "fr_GA" => "French (GA)",
     "fr_GP" => "French (GP)",
     "fr_GN" => "French (GN)",
     "fr_LU" => "French (LU)",
     "fr_MG" => "French (MG)",
     "fr_ML" => "French (ML)",
     "fr_MQ" => "French (MQ)",
     "fr_MC" => "French (MC)",
     "fr_NE" => "French (NE)",
     "fr_RW" => "French (RW)",
     "fr_RE" => "French (RE)",
     "fr_BL" => "French (BL)",
     "fr_MF" => "French (MF)",
     "fr_SN" => "French (SN)",
     "fr_CH" => "French (CH)",
     "fr_TG" => "French (TG)",
     "fr" => "French",
     "ff_SN" => "Fulah (SN)",
     "ff" => "Fulah",
     "gl_ES" => "Galician (ES)",
     "gl" => "Galician",
     "lg_UG" => "Ganda (UG)",
     "lg" => "Ganda",
     "ka_GE" => "Georgian (GE)",
     "ka" => "Georgian",
     "de_AT" => "German (AT)",
     "de_BE" => "German (BE)",
     "de_DE" => "German (DE)",
     "de_LI" => "German (LI)",
     "de_LU" => "German (LU)",
     "de_CH" => "German (CH)",
     "de" => "German",
     "dv" => "Dhivehi",
     "dv_MV" => "Dhivehi",
     "el_CY" => "Greek (CY)",
     "el_GR" => "Greek (GR)",
     "el" => "Greek",
     "gu_IN" => "Gujarati (IN)",
     "gu" => "Gujarati",
     "guz_KE" => "Gusii (KE)",
     "guz" => "Gusii",
     "ha_Latn" => "Hausa (Latn)",
     "ha_Latn_GH" => "Hausa (GH)",
     "ha_Latn_NE" => "Hausa (NE)",
     "ha_Latn_NG" => "Hausa (NG)",
     "ha" => "Hausa",
     "haw_US" => "Hawaiian (US)",
     "haw" => "Hawaiian",
     "he_IL" => "Hebrew (IL)",
     "he" => "Hebrew",
     "hi_IN" => "Hindi (IN)",
     "hi" => "Hindi",
     "hu_HU" => "Hungarian (HU)",
     "hu" => "Hungarian",
     "is_IS" => "Icelandic (IS)",
     "is" => "Icelandic",
     "ig_NG" => "Igbo (NG)",
     "ig" => "Igbo",
     "id_ID" => "Indonesian (ID)",
     "id" => "Indonesian",
     "ga_IE" => "Irish (IE)",
     "ga" => "Irish",
     "it_IT" => "Italian (IT)",
     "it_CH" => "Italian (CH)",
     "it" => "Italian",
     "ja_JP" => "Japanese (JP)",
     "ja" => "Japanese",
     "kea_CV" => "Kabuverdianu (CV)",
     "kea" => "Kabuverdianu",
     "kab_DZ" => "Kabyle (DZ)",
     "kab" => "Kabyle",
     "kl_GL" => "Kalaallisut (GL)",
     "kl" => "Kalaallisut",
     "kln_KE" => "Kalenjin (KE)",
     "kln" => "Kalenjin",
     "kam_KE" => "Kamba (KE)",
     "kam" => "Kamba",
     "kn_IN" => "Kannada (IN)",
     "kn" => "Kannada",
     "kk_Cyrl" => "Kazakh (Cyrl)",
     "kk_Cyrl_KZ" => "Kazakh (KZ)",
     "kk" => "Kazakh",
     "km_KH" => "Khmer (KH)",
     "km" => "Khmer",
     "ki_KE" => "Kikuyu (KE)",
     "ki" => "Kikuyu",
     "ckb" => "Kurdish (Sorani)",
     "rw_RW" => "Kinyarwanda (RW)",
     "rw" => "Kinyarwanda",
     "kok_IN" => "Konkani (IN)",
     "kok" => "Konkani",
     "ko_KR" => "Korean (KR)",
     "ko" => "Korean",
     "khq_ML" => "Koyra (ML)",
     "khq" => "Koyra Chiini",
     "ses_ML" => "Koyraboro (ML)",
     "ses" => "Koyraboro Senni",
     "lag_TZ" => "Langi (TZ)",
     "lag" => "Langi",
     "lv_LV" => "Latvian (LV)",
     "lv" => "Latvian",
     "lt_LT" => "Lithuanian (LT)",
     "lt" => "Lithuanian",
     "luo_KE" => "Luo (KE)",
     "luo" => "Luo",
     "luy_KE" => "Luyia (KE)",
     "luy" => "Luyia",
     "mk_MK" => "Macedonian (MK)",
     "mk" => "Macedonian",
     "jmc_TZ" => "Machame (TZ)",
     "jmc" => "Machame",
     "kde_TZ" => "Makonde (TZ)",
     "kde" => "Makonde",
     "mg_MG" => "Malagasy (MG)",
     "mg" => "Malagasy",
     "ms_BN" => "Malay (BN)",
     "ms_MY" => "Malay (MY)",
     "ms" => "Malay",
     "ml_IN" => "Malayalam (IN)",
     "ml" => "Malayalam",
     "mt_MT" => "Maltese (MT)",
     "mt" => "Maltese",
     "gv_GB" => "Manx (GB)",
     "gv" => "Manx",
     "mr_IN" => "Marathi (IN)",
     "mr" => "Marathi",
     "mas_KE" => "Masai (KE)",
     "mas_TZ" => "Masai (TZ)",
     "mas" => "Masai",
     "mer_KE" => "Meru (KE)",
     "mer" => "Meru",
     "mfe_MU" => "Morisyen (MU)",
     "mfe" => "Morisyen",
     "naq_NA" => "Nama (NA)",
     "naq" => "Nama",
     "ne_IN" => "Nepali (IN)",
     "ne_NP" => "Nepali (NP)",
     "ne" => "Nepali",
     "nd_ZW" => "North (ZW)",
     "nd" => "North Ndebele",
     "nb_NO" => "Norwegian (NO)",
     "nb" => "Norwegian Bokmål",
     "nn_NO" => "Norwegian (NO)",
     "nn" => "Norwegian Nynorsk",
     "nyn_UG" => "Nyankole (UG)",
     "nyn" => "Nyankole",
     "or_IN" => "Oriya (IN)",
     "or" => "Oriya",
     "om_ET" => "Oromo (ET)",
     "om_KE" => "Oromo (KE)",
     "om" => "Oromo",
     "ps_AF" => "Pashto (AF)",
     "ps" => "Pashto",
     "fa_AF" => "Persian (AF)",
     "fa_IR" => "Persian (IR)",
     "fa" => "Persian",
     "pl_PL" => "Polish (PL)",
     "pl" => "Polish",
     "pt_BR" => "Portuguese (BR)",
     "pt_GW" => "Portuguese (GW)",
     "pt_MZ" => "Portuguese (MZ)",
     "pt_PT" => "Portuguese (PT)",
     "pt" => "Portuguese",
     "pa_Arab" => "Punjabi (Arab)",
     "pa_Arab_PK" => "Punjabi (PK)",
     "pa_Guru" => "Punjabi (Guru)",
     "pa_Guru_IN" => "Punjabi (IN)",
     "pa" => "Punjabi",
     "ro_MD" => "Romanian (MD)",
     "ro_RO" => "Romanian (RO)",
     "ro" => "Romanian",
     "rm_CH" => "Romansh (CH)",
     "rm" => "Romansh",
     "rof_TZ" => "Rombo (TZ)",
     "rof" => "Rombo",
     "ru_MD" => "Russian (MD)",
     "ru_RU" => "Russian (RU)",
     "ru_UA" => "Russian (UA)",
     "ru" => "Russian",
     "rwk_TZ" => "Rwa (TZ)",
     "rwk" => "Rwa",
     "saq_KE" => "Samburu (KE)",
     "saq" => "Samburu",
     "sg_CF" => "Sango (CF)",
     "sg" => "Sango",
     "seh_MZ" => "Sena (MZ)",
     "seh" => "Sena",
     "sr_Cyrl" => "Serbian (Cyrl)",
     "sr_Cyrl_BA" => "Serbian (BA)",
     "sr_Cyrl_ME" => "Serbian (ME)",
     "sr_Cyrl_RS" => "Serbian (RS)",
     "sr_Latn" => "Serbian (Latn)",
     "sr_Latn_BA" => "Serbian (BA)",
     "sr_Latn_ME" => "Serbian (ME)",
     "sr_Latn_RS" => "Serbian (RS)",
     "sr" => "Serbian",
     "sn_ZW" => "Shona (ZW)",
     "sn" => "Shona",
     "ii_CN" => "Sichuan (CN)",
     "ii" => "Sichuan Yi",
     "si_LK" => "Sinhala (LK)",
     "si" => "Sinhala",
     "sk_SK" => "Slovak (SK)",
     "sk" => "Slovak",
     "sl_SI" => "Slovenian (SI)",
     "sl" => "Slovenian",
     "xog_UG" => "Soga (UG)",
     "xog" => "Soga",
     "so_DJ" => "Somali (DJ)",
     "so_ET" => "Somali (ET)",
     "so_KE" => "Somali (KE)",
     "so_SO" => "Somali (SO)",
     "so" => "Somali",
     "es_AR" => "Spanish (AR)",
     "es_BO" => "Spanish (BO)",
     "es_CL" => "Spanish (CL)",
     "es_CO" => "Spanish (CO)",
     "es_CR" => "Spanish (CR)",
     "es_DO" => "Spanish (DO)",
     "es_EC" => "Spanish (EC)",
     "es_SV" => "Spanish (SV)",
     "es_GQ" => "Spanish (GQ)",
     "es_GT" => "Spanish (GT)",
     "es_HN" => "Spanish (HN)",
     "es_419" => "Spanish (419)",
     "es_MX" => "Spanish (MX)",
     "es_NI" => "Spanish (NI)",
     "es_PA" => "Spanish (PA)",
     "es_PY" => "Spanish (PY)",
     "es_PE" => "Spanish (PE)",
     "es_PR" => "Spanish (PR)",
     "es_ES" => "Spanish (ES)",
     "es_US" => "Spanish (US)",
     "es_UY" => "Spanish (UY)",
     "es_VE" => "Spanish (VE)",
     "es" => "Spanish",
     "sw_KE" => "Swahili (KE)",
     "sw_TZ" => "Swahili (TZ)",
     "sw" => "Swahili",
     "sv_FI" => "Swedish (FI)",
     "sv_SE" => "Swedish (SE)",
     "sv" => "Swedish",
     "gsw_CH" => "Swiss (CH)",
     "gsw" => "Swiss German",
     "shi_Latn" => "Tachelhit (Latn)",
     "shi_Latn_MA" => "Tachelhit (MA)",
     "shi_Tfng" => "Tachelhit (Tfng)",
     "shi_Tfng_MA" => "Tachelhit (MA)",
     "shi" => "Tachelhit",
     "dav_KE" => "Taita (KE)",
     "dav" => "Taita",
     "ta_IN" => "Tamil (IN)",
     "ta_LK" => "Tamil (LK)",
     "ta" => "Tamil",
     "te_IN" => "Telugu (IN)",
     "te" => "Telugu",
     "teo_KE" => "Teso (KE)",
     "teo_UG" => "Teso (UG)",
     "teo" => "Teso",
     "th_TH" => "Thai (TH)",
     "th" => "Thai",
     "bo_CN" => "Tibetan (CN)",
     "bo_IN" => "Tibetan (IN)",
     "bo" => "Tibetan",
     "ti_ER" => "Tigrinya (ER)",
     "ti_ET" => "Tigrinya (ET)",
     "ti" => "Tigrinya",
     "to_TO" => "Tonga (TO)",
     "to" => "Tonga",
     "tr_TR" => "Turkish (TR)",
     "tr" => "Turkish",
     "uk_UA" => "Ukrainian (UA)",
     "uk" => "Ukrainian",
     "ur_IN" => "Urdu (IN)",
     "ur_PK" => "Urdu (PK)",
     "ur" => "Urdu",
     "uz_Arab" => "Uzbek (Arab)",
     "uz_Arab_AF" => "Uzbek (AF)",
     "uz_Cyrl" => "Uzbek (Cyrl)",
     "uz_Cyrl_UZ" => "Uzbek (UZ)",
     "uz_Latn" => "Uzbek (Latn)",
     "uz_Latn_UZ" => "Uzbek (UZ)",
     "uz" => "Uzbek",
     "vi_VN" => "Vietnamese (VN)",
     "vi" => "Vietnamese",
     "vun_TZ" => "Vunjo (TZ)",
     "vun" => "Vunjo",
     "cy_GB" => "Welsh (GB)",
     "cy" => "Welsh",
     "yo_NG" => "Yoruba (NG)",
     "yo" => "Yoruba",
     "zu_ZA" => "Zulu (ZA)",
     "zu" => "Zulu"
    ];

    protected static $rtlLocales = [
     "ar_DZ", "ar_BH", "ar_EG", "ar_IQ", "ar_JO", "ar_KW", "ar_LB", "ar_LY",
     "ar_MA", "ar_OM", "ar_QA", "ar_SA", "ar_SD", "ar_SY", "ar_TN", "ar_AE", "ar_YE", "ar",
     "az_Cyrl", "az_Cyrl_AZ", "az_Latn", "az_Latn_AZ", "az", "dv", "dv_MV", "he_IL",
     "he", "ckb", "fa_AF", "fa_IR", "fa", "ur", "ur_PK", "ur_IN",
    ];


    /**
     * Register a textdomain and locale file
     *
     * @param  string $localeFile
     * @param  string $textdomain
     * @return boolean
     */
    public function register($localeFile, $textdomain)
    {
        // Can't override existing textdomains
        if (isset($this->locales[$textdomain])) {
            return false;
        }

        $this->locales[$textdomain] = [
            'localeFile' => $localeFile
        ];

        return true;
    }

    /**
     * Get translator instance for a textdomain
     *
     * @param  string $textdomain
     * @return boolean|Translator
     */
    public function getTranslator($textdomain)
    {
        // Easy peasy lemon squeezey
        if (!isset($this->locales[$textdomain])) {
            return false;
        }

        if (isset($this->locales[$textdomain]['instance']) && $this->locales[$textdomain]['instance'] instanceof I18n) {
            return $this->locales[$textdomain]['instance'];
        } else {
            $this->locales[$textdomain]['instance'] = new I18n($this->locales[$textdomain]['localeFile']);
            return $this->locales[$textdomain]['instance'];
        }

        return false;
    }

    /**
    * Translates a string
    *
    * @param string $msgid String to be translated
    * @param string $textdomain Textdomain, if left empty default will be used
    *
    * @return string translated string (or original, if not found)
    */
    public function gettext($msgid, $textdomain = null, array $variables = [])
    {
        if (!$textdomain) {
            $textdomain = static::DEFAULT_TEXTDOMAIN;
        }

        $translator = $this->getTranslator($textdomain);

        if (!$translator) {
            return $msgid;
        }

        return $translator->getTranslation($msgid, $variables);
    }

    /**
     * List available languages of a theme, defaults to current theme
     *
     * @param  string  $theme
     * @param  boolean $forceScan
     * @return array
     */
    public function getThemeLanguages($theme = null, $forceScan = false)
    {
        if (!$theme) {
            $theme = get_option('active_theme');
        }


        if (isset($this->themeLanguages[$theme]) && !$forceScan) {
            return $this->themeLanguages[$theme];
        }

        $locales = [];

        $pattern = trailingslashit(current_theme_path(static::THEME_LOCALE_DIR)) . '*';
        $localeDirs = glob($pattern, GLOB_ONLYDIR);

        foreach ($localeDirs as $dir) {
            $langName = basename($dir);
            $langPath = trailingslashit($dir) . "{$langName}.php";
            $locales[$langName] = [
                'name'   => static::getLocaleName($langName, $langName),
                'localeFile' => $langPath,
                'code'       => $langName,
                'active' => false,
                'icon'  => false,
            ];

            if (is_file(trailingslashit($dir) . "{$langName}.png")) {
                $locales[$langName]['icon'] = current_theme_uri(static::THEME_LOCALE_DIR . "/{$langName}/{$langName}.png");
            }

            if (is_theme_active_locale($langName)) {
                $locales[$langName]['active'] = true;
            }
        }

        $this->themeLanguages[$theme] = $locales;

        return $locales;
    }

    /**
     * Get dashboard/system language files
     *
     * @param  boolean $forceScan
     * @return
     */
    public function getDashboardLanguages($forceScan = false)
    {
        if (is_array($this->dashboardLanguages)) {
            return $this->dashboardLanguages;
        }

        $this->dashboardLanguages = [];

        $pattern = trailingslashit(srcpath('locales')) . '*';
        $localeDirs = glob($pattern, GLOB_ONLYDIR);

        foreach ($localeDirs as $dir) {
            $langName = basename($dir);
            $langPath = trailingslashit($dir) . "{$langName}.php";
            $this->dashboardLanguages[$langName] = [
                'name'       => static::getLocaleName($langName, $langName),
                'code'       => $langName,
                'localeFile' => $langPath,
                'active'     => false,
            ];

            if (get_site_locale() === $langName) {
                $this->dashboardLanguages[$langName]['active'] = true;
            }
        }

        return $this->dashboardLanguages;
    }

    public function getThemeLocaleInfo($locale)
    {

        $theme = get_option('active_theme');

        $languages = $this->getThemeLanguages($theme);

        if (isset($languages[$locale])) {
            return $languages[$locale];
        }

        return [
            'code' => $locale,
            'name' => $locale,
            'icon' => false,
            'active' => null,
            'localeFile' => null,
        ];
    }

    /**
     * Initializes theme locale
     *
     * @return
     */
    public function initThemeLocales()
    {
        static $loaded = false;

        if ($loaded) {
            return false;
        }

        $app = app();

        $locale = get_option('site_locale');

        $cookieLocale = get_cookie(static::COOKIE_NAME);

        if ($cookieLocale) {
            $locale = $cookieLocale;
        }

        registry_store('_spark.theme.locale', $locale, true);

        $loaded = true;
    }

    /**
     * Get a locale name by locale code
     *
     * @param  string $code
     * @param  string $fallback
     * @return mixed
     */
    public static function getLocaleName($code, $fallback = null)
    {
        if (isset(static::$localeNames[$code])) {
            return static::$localeNames[$code];
        }

        return $fallback;
    }

    /**
     * Check if a locale is rtl or not
     *
     * @param  string  $locale
     * @return boolean
     */
    public static function isRtl($locale)
    {
        return in_array($locale, static::$rtlLocales);
    }
}
