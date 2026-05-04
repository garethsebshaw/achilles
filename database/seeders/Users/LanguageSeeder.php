<?php

namespace Database\Seeders\Users;

use App\Models\SystemModule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SystemCategory;
use App\Models\Language;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class LanguageSeeder extends Seeder
{
    private const USER_CHUNK_SIZE = 2000;
    private const INSERT_CHUNK_SIZE = 1000;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command?->getOutput()->setVerbosity(\Symfony\Component\Console\Output\OutputInterface::VERBOSITY_QUIET);

        $languages = [
            'Spoken Languages' => [
                'English', 'Mandarin Chinese', 'Hindi', 'Spanish', 'Arabic', 'Bengali', 'Portuguese', 'Russian',
                'Japanese', 'Punjabi', 'German', 'Javanese', 'Wu Chinese', 'Korean', 'French', 'Telugu',
                'Vietnamese', 'Marathi', 'Tamil', 'Turkish', 'Italian', 'Dutch', 'Norwegian', 'Swedish',
                'Danish', 'Finnish', 'Ukrainian', 'Greek', 'Hungarian', 'Polish', 'Hebrew', 'Farsi (Persian)',
                'Thai', 'Indonesian (Bahasa Indonesia)', 'Tagalog (Filipino)', 'Swahili', 'Zulu', 'Afrikaans',
                'Hausa', 'Malay (Bahasa Melayu)', 'Burmese', 'Mongolian', 'Quechua', 'Guarani', 'Catalan',
                'Basque', 'Kurdish', 'Cantonese', 'Haitian Creole', 'Pashto', 'Sinhala', 'Urdu', 'Malayalam',
                'Kannada', 'Oromo', 'Amharic', 'Twi', 'Serbian', 'Croatian', 'Romanian', 'Georgian', 'Czech',
                'Slovak'
            ],

            'Written Languages' => [
                // Commonly used modern scripts
                'Latin Script', 'Simplified Chinese Characters', 'Traditional Chinese Characters',
                'Arabic Script', 'Devanagari Script', 'Cyrillic Script', 'Japanese Kanji',
                'Japanese Hiragana', 'Japanese Katakana', 'Korean Hangul', 'Greek Alphabet',
                'Hebrew Script', 'Thai Script', 'Bengali Script',

                // Regional and indigenous scripts
                'Tamil Script', 'Telugu Script', 'Kannada Script', 'Malayalam Script', 'Gujarati Script',
                'Gurmukhi Script (Punjabi)', 'Oriya Script', 'Sinhala Script', 'Tibetan Script',
                'Burmese Script', 'Khmer Script', 'Lao Script', 'Javanese Script', 'Balinese Script',
                'Batak Script', 'Buginese Script', 'Sundanese Script', 'Tagalog Baybayin Script',
                'Hanunó’o Script', 'Buhid Script', 'Makasar Script', 'Mongolian Script',
                'Cherokee Syllabary', 'Inuktitut Syllabics', 'N’Ko Script (West Africa)',
                'Vai Script (Liberia)', 'Adlam Script (Fulani)', 'Tifinagh Script (Berber)',

                // Historic scripts
                'Cuneiform (Ancient Mesopotamian Script)', 'Egyptian Hieroglyphs',
                'Runic Script (Old Norse, Anglo-Saxon)', 'Ogham Script (Old Irish)',
                'Phoenician Script', 'Old Turkic Script', 'Glagolitic Script (Old Slavic)',
                'Meroitic Script (Ancient Sudanese)', 'Elamite Linear Script',
                'Ugaritic Cuneiform', 'Mayan Hieroglyphs',

                // Additional notable scripts
                'Thaana Script (Maldivian)', 'Shavian Alphabet', 'Deseret Alphabet',
                'Manchu Script', 'Yi Script', 'Tengwar (Tolkien’s Elvish Script)',
                'Klingon Script (Fictional)', 'SignWriting (for sign languages)'
            ],

            'Sign Languages' => [
                // **Major International Sign Languages**
                'American Sign Language (ASL)', 'British Sign Language (BSL)', 'Chinese Sign Language (CSL)',
                'French Sign Language (LSF)', 'Australian Sign Language (Auslan)', 'Brazilian Sign Language (Libras)',
                'Japanese Sign Language (JSL)', 'Russian Sign Language (RSL)', 'Indian Sign Language (ISL)',
                'International Sign',

                // **Regional Sign Languages**
                'German Sign Language (DGS)', 'Spanish Sign Language (LSE)', 'Italian Sign Language (LIS)',
                'Portuguese Sign Language (LGP)', 'Mexican Sign Language (LSM)', 'Argentine Sign Language (LSA)',
                'Chilean Sign Language (LSCh)', 'Colombian Sign Language (LSC)', 'Peruvian Sign Language (LSP)',
                'Venezuelan Sign Language (LSV)', 'Cuban Sign Language (LSC)', 'Ecuadorian Sign Language (LSEC)',
                'Bolivian Sign Language (LSB)', 'Paraguayan Sign Language (LSPy)', 'Uruguayan Sign Language (LSU)',

                // **Asian Sign Languages**
                'Korean Sign Language (KSL)', 'Thai Sign Language (TSL)', 'Vietnamese Sign Language (VSL)',
                'Indonesian Sign Language (Bisindo)', 'Filipino Sign Language (FSL)', 'Mongolian Sign Language (MSL)',
                'Taiwanese Sign Language (TSL)', 'Hong Kong Sign Language (HKSL)', 'Malay Sign Language (MySL)',
                'Indian Sign Language (ISL)', 'Pakistani Sign Language (PSL)', 'Nepali Sign Language (NSL)',
                'Bangladeshi Sign Language (BdSL)', 'Burmese Sign Language (MSL)', 'Sri Lankan Sign Language (SLSL)',

                // **European Sign Languages**
                'Irish Sign Language (ISL)', 'Dutch Sign Language (NGT)', 'Belgian French Sign Language (LSFB)',
                'Swedish Sign Language (SSL)', 'Danish Sign Language (DSL)', 'Finnish Sign Language (FSL)',
                'Norwegian Sign Language (NSL)', 'Icelandic Sign Language (ÍTM)', 'Czech Sign Language (CSE)',
                'Slovak Sign Language (SVJ)', 'Polish Sign Language (PJM)', 'Hungarian Sign Language (HSL)',
                'Romanian Sign Language (LSR)', 'Bulgarian Sign Language (BGSL)', 'Serbian Sign Language (SZJ)',
                'Croatian Sign Language (HZJ)', 'Slovenian Sign Language (SZJ)', 'Greek Sign Language (GSL)',
                'Turkish Sign Language (TID)', 'Maltese Sign Language (LSM)', 'Ukrainian Sign Language (USL)',

                // **African Sign Languages**
                'South African Sign Language (SASL)', 'Kenyan Sign Language (KSL)', 'Ugandan Sign Language (USL)',
                'Tanzanian Sign Language (TSL)', 'Ghanaian Sign Language (GSL)', 'Nigerian Sign Language (NSL)',
                'Ethiopian Sign Language (EthSL)', 'Zimbabwean Sign Language (ZSL)', 'Zambian Sign Language (Zamsl)',
                'Malagasy Sign Language (LSM)', 'Sudanese Sign Language (SSL)', 'Libyan Sign Language (LSL)',
                'Moroccan Sign Language (MSL)', 'Egyptian Sign Language (ESL)', 'Algerian Sign Language (ASL)',
                'Tunisian Sign Language (TSL)', 'Chadian Sign Language (CSL)',

                // **Middle Eastern Sign Languages**
                'Israeli Sign Language (ISL)', 'Lebanese Sign Language (LSL)', 'Palestinian Sign Language (PSL)',
                'Jordanian Sign Language (LIU)', 'Iraqi Sign Language (ISL)', 'Saudi Sign Language (SSL)',
                'Emirati Sign Language (ESL)', 'Omani Sign Language (OSL)', 'Qatari Sign Language (QSL)',
                'Bahraini Sign Language (BSL)', 'Kuwaiti Sign Language (KSL)', 'Syrian Sign Language (SSL)',
                'Persian Sign Language (PSL)',

                // **Indigenous and Unique Sign Languages**
                'Yucatec Maya Sign Language (YMSL)', 'Nicaraguan Sign Language (NSL)', 'Martha’s Vineyard Sign Language (MVSL)',
                'Al-Sayyid Bedouin Sign Language (ABSL)', 'Hawaiian Sign Language (HSL)', 'Ban Khor Sign Language (BKSL)',
                'Plains Indian Sign Language (PISL)', 'Australian Aboriginal Sign Languages', 'Inuit Sign Language (ISL)',
                'Ghanaian Adamorobe Sign Language', 'Nigerian Adamorobe Sign Language',
            ],


            'Tactile Communication' => [
                // **Major Tactile Sign Languages**
                'Tactile ASL (TASL)', 'Tactile French Sign Language (LSFT)', 'Tactile British Sign Language (TBSL)',
                'Tactile Russian Sign Language (TRSL)', 'Tactile Japanese Sign Language (TJSL)',
                'Tactile Australian Sign Language (TAuslan)', 'Tactile Brazilian Sign Language (TLibras)',
                'Tactile Finnish Sign Language (TFinSL)', 'Tactile Swedish Sign Language (TSSL)',

                // **Pro-Tactile and Haptic Communication**
                'Pro-Tactile Sign Language', 'Haptic Communication', 'Haptics for DeafBlind Individuals',
                'Social Haptics', 'Haptic Signals in Sports', 'Tactile Symbols for Communication',

                // **DeafBlind-Specific Communication Methods**
                'Tadoma Method', 'Lorm Alphabet (Germany and Austria)', 'Malossi Alphabet (Italy)',
                'Two-Hand Manual Alphabet (Scandinavia)', 'One-Hand Manual Alphabet (Spain, Latin America)',
                'Braille Fingerspelling', 'DeafBlind Manual Alphabet (UK)', 'Block Letter Spelling (Palm Writing)',

                // **Written Tactile Communication**
                'Raised Letter Writing', 'Braille', 'Moon Type', 'Finger Braille (Japan)',

                // **Tactile Cueing and Environmental Interpretation**
                'Tactile Object Symbols', 'Textured Symbols for Literacy', 'Tactile Pictograms for Navigation',
                'Tactile Maps and 3D Navigation Aids', 'Vibration-Based Communication Devices',

                // **Alternative Tactile Communication Systems**
                'Blissymbolics with Tactile Adaptation', 'Morse Code via Touch', 'Tactile Morse Code',
                'Electronic Tactile Feedback Systems', 'Vibro-Tactile Communication for Speech Impairments'
            ],

            'Nonverbal Communication' => [
                // **Facial Expressions and Body Language**
                'Facial Expressions', 'Body Language', 'Posture and Gait Interpretation',

                // **Proxemics and Spatial Awareness**
                'Proxemics (Use of Space)', 'Tactile Proxemics for DeafBlind Individuals',

                // **Paralinguistics (Nonverbal Aspects of Speech)**
                'Tone and Pitch Recognition', 'Rhythm and Speech Cadence', 'Breath-Based Communication',

                // **Chronemics (Time Perception in Communication)**
                'Pacing in Nonverbal Interaction', 'Turn-Taking in Communication',

                // **Kinesics (Body Movements and Gestures)**
                'Head Nods and Shakes', 'Hand and Arm Gestures', 'Adaptive Gesture-Based Communication',
                'Touch-Based Cues for Athletes with Visual Impairments',

                // **Haptics (Touch-Based Communication)**
                'Guided Touch Communication', 'Tactile Cues for Directional Guidance',
                'Haptic Feedback in Sports and Navigation', 'Adaptive Touch-Based Signals',

                // **Environmental and Sensory Communication**
                'Vibrotactile Communication', 'Light and Shadow-Based Cues for Deaf Athletes',
                'Color-Coded or Shape-Based Communication Aids',

                // **Alternative Nonverbal Communication**
                'Eye Gaze Communication', 'Blink-Pattern Communication', 'Lip-Reading',
                'Whistle-Based Communication (Used in Sports and Mobility Training)',
                'Signaling Through Clothing (Reflective Symbols, Wearable Tech for Communication)'
            ],
        ];

        $languageModule = SystemModule::firstOrCreate([
            'name' => 'Languages',
            'model_type' => Language::class
        ]);

        foreach ($languages as $categoryName => $languageList) {
//            $this->command->info(class_basename(static::class) . $categoryName . ' found');

            $category = SystemCategory::firstOrCreate([
                'system_module_id' => $languageModule->id,
                'name' => $categoryName
            ]);
            /*
                        $category = SystemCategory::whereHas('systemModule', function ($query) {
                            $query->where('model_type', \App\Models\Language::class);
                        })->where('name', $categoryName)->first();*/

            if ($category) {
//                $this->command->info(class_basename(static::class) . $category . ' added');
                foreach ($languageList as $languageName) {
//                    $this->command->info(class_basename(static::class) . $languageName . ' added');
                    Language::firstOrCreate([
                        'name' => $languageName,
                        'system_category_id' => $category->id
                    ]);
                }
            } else {
                Log::warning("Category not found: {$categoryName}");
            }
        }

        $languagesNeeded = [
            'English' => 0,
            'French' => 0,
            'Spanish' => 0,
            'Mandarin Chinese' => 0,
            'Italian' => 0,
            'Braille' => 0,
            'Norwegian' => 0,
            'American Sign Language (ASL)' => 0,
            'Norwegian Sign Language (NSL)' => 0
        ];
        $proficienciesNeeded = [
            '1st / Native' => 0,
            'Advanced' => 0,
            'Intermediate' => 0,
            'Basic' => 0,
            'Limited' => 0
        ];


//        Get the languages we need for the seeder
        foreach ($languagesNeeded as $langName => $langId) {
            $languagesNeeded[$langName] = \DB::table('languages')
                ->where('name', $langName)
                ->value('id');
        }
//        get the language proficiency status ID
//        First get the language proficiency module ID
        $systemModuleId = \DB::table('system_modules')
            ->where('model_type', \App\Models\LanguageProficiency::class)
            ->value('id');

        foreach ($proficienciesNeeded as $profName => $profId) {
            $proficienciesNeeded[$profName] = \DB::table('system_statuses')
                ->where('name', $profName)
                ->where('system_module_id', $systemModuleId)
                ->value('id');
        }

        $data = [
            // User 1: Speaks English fluently, French limited, Spanish limited
            ['user_id' => 1, 'language_id' => $languagesNeeded['English'], 'proficiency_status_id' => $proficienciesNeeded['1st / Native']], // English (Fluent)
            ['user_id' => 1, 'language_id' => $languagesNeeded['French'], 'proficiency_status_id' => $proficienciesNeeded['Limited']], // French (Limited)
            ['user_id' => 1, 'language_id' => $languagesNeeded['Spanish'], 'proficiency_status_id' => $proficienciesNeeded['Limited']], // Spanish (Limited)

            // User 2: Speaks Spanish and English fluently
            ['user_id' => 2, 'language_id' => $languagesNeeded['English'], 'proficiency_status_id' => $proficienciesNeeded['1st / Native']], // English (Fluent)
            ['user_id' => 2, 'language_id' => $languagesNeeded['Mandarin Chinese'], 'proficiency_status_id' => $proficienciesNeeded['1st / Native']], // Mandarin (Fluent)

        ];
//        Add them all to the database
        DB::table('language_proficiencies')->insert($data);

        $primaryLanguageNames = ['English', 'French', 'Spanish', 'Mandarin Chinese', 'Italian', 'Norwegian'];
        $secondaryProficiencyIds = array_values(array_diff_key($proficienciesNeeded, ['1st / Native' => 0]));
        $processedUsers = 2;

        DB::table('users')
            ->select('id')
            ->where('id', '>', 2)
            ->orderBy('id')
            ->chunkById(self::USER_CHUNK_SIZE, function ($users) use (
                $languagesNeeded,
                $proficienciesNeeded,
                $secondaryProficiencyIds,
                $primaryLanguageNames,
                &$processedUsers
            ) {
                $rows = [];
                $now = now();

                foreach ($users as $user) {
                    $assignedLanguageIds = [];
                    $numLanguages = rand(1, 3);
                    $primaryLanguagePool = $primaryLanguageNames;

                    shuffle($primaryLanguagePool);
                    $selectedPrimaryNames = array_slice(
                        $primaryLanguagePool,
                        0,
                        mt_rand(1, 1000) <= 25 ? 2 : 1
                    );

                    foreach ($selectedPrimaryNames as $primaryLanguageName) {
                        $languageId = $languagesNeeded[$primaryLanguageName] ?? null;

                        if (! $languageId || in_array($languageId, $assignedLanguageIds, true)) {
                            continue;
                        }

                        $rows[] = [
                            'user_id' => $user->id,
                            'language_id' => $languageId,
                            'proficiency_status_id' => $proficienciesNeeded['1st / Native'],
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                        $assignedLanguageIds[] = $languageId;

                        if ($primaryLanguageName === 'English' && mt_rand(1, 100) <= 4) {
                            $aslId = $languagesNeeded['American Sign Language (ASL)'] ?? null;

                            if ($aslId && ! in_array($aslId, $assignedLanguageIds, true)) {
                                $rows[] = [
                                    'user_id' => $user->id,
                                    'language_id' => $aslId,
                                    'proficiency_status_id' => $secondaryProficiencyIds[array_rand($secondaryProficiencyIds)],
                                    'created_at' => $now,
                                    'updated_at' => $now,
                                ];
                                $assignedLanguageIds[] = $aslId;
                            }
                        }

                        if ($primaryLanguageName === 'Norwegian' && mt_rand(1, 100) <= 2) {
                            $nslId = $languagesNeeded['Norwegian Sign Language (NSL)'] ?? null;

                            if ($nslId && ! in_array($nslId, $assignedLanguageIds, true)) {
                                $rows[] = [
                                    'user_id' => $user->id,
                                    'language_id' => $nslId,
                                    'proficiency_status_id' => $secondaryProficiencyIds[array_rand($secondaryProficiencyIds)],
                                    'created_at' => $now,
                                    'updated_at' => $now,
                                ];
                                $assignedLanguageIds[] = $nslId;
                            }
                        }

                        if (mt_rand(1, 100) <= 1) {
                            $brailleId = $languagesNeeded['Braille'] ?? null;

                            if ($brailleId && ! in_array($brailleId, $assignedLanguageIds, true)) {
                                $rows[] = [
                                    'user_id' => $user->id,
                                    'language_id' => $brailleId,
                                    'proficiency_status_id' => $secondaryProficiencyIds[array_rand($secondaryProficiencyIds)],
                                    'created_at' => $now,
                                    'updated_at' => $now,
                                ];
                                $assignedLanguageIds[] = $brailleId;
                            }
                        }
                    }

                    if ($numLanguages > count($assignedLanguageIds)) {
                        $remainingLanguageIds = array_values(array_diff(array_values($languagesNeeded), $assignedLanguageIds));

                        if ($remainingLanguageIds !== []) {
                            shuffle($remainingLanguageIds);
                            $additionalLanguageIds = array_slice(
                                $remainingLanguageIds,
                                0,
                                min(count($remainingLanguageIds), $numLanguages - count($assignedLanguageIds))
                            );

                            foreach ($additionalLanguageIds as $languageId) {
                                $rows[] = [
                                    'user_id' => $user->id,
                                    'language_id' => $languageId,
                                    'proficiency_status_id' => $secondaryProficiencyIds[array_rand($secondaryProficiencyIds)],
                                    'created_at' => $now,
                                    'updated_at' => $now,
                                ];
                            }
                        }
                    }

                    $processedUsers++;
                }

                foreach (array_chunk($rows, self::INSERT_CHUNK_SIZE) as $chunk) {
                    DB::table('language_proficiencies')->insert($chunk);
                }

            }, 'id');
    }
}
