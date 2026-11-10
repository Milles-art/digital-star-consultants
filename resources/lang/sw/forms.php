<?php

/**
 * Fomu zinazoweza kuchapishwa (printable forms).
 *
 * Kiwahili ni chanzo cha ukweli. Kiingereza kiko kwenye resources/lang/en/forms.php.
 * Kila 'type' ya block inachakatwa na resources/views/forms/partials/block.blade.php:
 * lead | lines | checks | choices | declaration | terms | signatures | office |
 * pricing | note
 */

return [

    'common' => [
        'back' => 'Rudi kwenye fomu',
        'print' => 'Chapisha fomu',
        'download_hint' => 'Gusa "Chapisha" kisha chagua "Hifadhi PDF" kwenye kidirisha cha print.',
        'sections' => 'Sehemu',
        'fields' => 'Taarifa',
        'optional' => 'si lazima',
        'stamp' => 'MUHURI WA OFISI',
        'signature' => 'Saini',
        'date' => 'Tarehe',
        'line' => '________________________',
        'check_here' => 'Weka alama ya ✓',
        'footer_tagline' => 'Live & Enjoy your Digital Life',
    ],

    'index' => [
        'eyebrow' => 'Fomu za msaada wa maombi',
        'title' => 'Fomu za maombi',
        'lead' => 'Chagua fomu, chapisha na uijaze kwa mkono, kisha ilete ofisini kwetu Mbagala Rangi 3. Unaweza pia kujaza mtandaoni kupitia huduma husika.',
        'open' => 'Fungua fomu',
        'services_link' => 'Angalia huduma zinazohusiana',
        'empty' => 'Fomu hazijapatikana.',
    ],

    // ---------------------------------------------------------------------
    // 1. Kufuatilia simu iliyopotea / kuibiwa
    // ---------------------------------------------------------------------
    'tracking-simu' => [
        'card' => [
            'title' => 'Fomu ya Ombi la Huduma ya Kufuatilia (Tracking) Simu Iliyopotea au Kuibiwa',
            'summary' => 'Tuma taarifa ulizonazo. Tunasaidia kuandaa taarifa na kufuatilia hatua kupitia Polisi/mtoa huduma; tutakuelekeza kuhusu nyaraka zinazobaki. Hakuna ahadi ya kubaini eneo au kuirejesha simu.',
            'meta' => ['Kuanzia TSh 100,000/=', 'Mbagala Rangi 3', 'Msaada wa taarifa na ufuatiliaji'],
        ],
        'title' => 'FOMU YA OMBI LA HUDUMA YA KUFUATILIA (TRACKING) SIMU ILIYOPOTEA AU KUIBIWA',
        'office' => [
            ['icon' => 'map-pin', 'text' => 'Digital Star Consultants – Mbagala Rangi 3, Dar es Salaam'],
            ['icon' => 'phone', 'text' => 'Simu: 0783257716 / 0757257716 / +255 760 637 716'],
        ],
        'sections' => [
            [
                'heading' => '1. HATUA YA TAARIFA',
                'blocks' => [
                    ['type' => 'note', 'text' => 'Ukikosa taarifa/nyaraka endelea; tutawasiliana nawe. Usitoe password au OTP. Hii ni fomu ya msaada wa Digital Star, si ripoti ya Polisi.'],
                    ['type' => 'lines', 'columns' => 1, 'items' => ['Umeshatoa taarifa Polisi? Ikiwa bado tutakuelekeza', 'RB / namba ya ripoti na kituo, kama ipo', 'Mpelelezi na mawasiliano, kama umepewa', 'Mtoa huduma na hali ya kufungia kifaa/laini']],
                ],
            ],
            [
                'heading' => '2. TAARIFA ZA MWOMBAJI',
                'blocks' => [
                    ['type' => 'lines', 'columns' => 1, 'items' => [
                        'Jina Kamili',
                        'Namba ya NIDA',
                        'Namba ya Simu ya Mawasiliano',
                        'Barua Pepe (Email)',
                        'Mkoa',
                        'Wilaya',
                        'Kata / Mtaa / Kijiji',
                        'Anwani Kamili',
                    ]],
                ],
            ],
            [
                'heading' => '3. TAARIFA ZA SIMU ILIYOPOTEA / KUIBIWA',
                'blocks' => [
                    ['type' => 'lines', 'columns' => 1, 'items' => [
                        'Aina ya Simu (Make / Model)',
                        'Rangi ya Simu',
                        'IMEI Na. 1',
                        'IMEI Na. 2',
                        'Namba ya Simu Iliyokuwa Inatumika',
                        'Tarehe ya Kupotea / Kuibiwa',
                        'Mahali Ilikuwa Ipopotea / Kuibiwa',
                        'Maelezo ya Tukio',
                    ]],
                ],
            ],
            [
                'heading' => '4. VIELELEZO VILIVYOWASILISHWA',
                'blocks' => [
                    ['type' => 'note', 'text' => '(Weka alama ya ✓ kwenye kila kielelezo ulichokuleta)'],
                    ['type' => 'checks', 'items' => [
                        'Nakala ya Kitambulisho cha Taifa (NIDA)',
                        'Box la Simu lenye IMEI',
                        'Risiti ya Ununuzi wa Simu',
                        'Akaunti ya Google (Android)',
                        'Akaunti ya iCloud (iPhone)',
                        'Email Iliyounganishwa na Simu',
                        'Namba ya Simu Iliyokuwa Inatumika',
                    ], 'other' => 'Vinginevyo (Taja)'],
                ],
            ],
            [
                'heading' => '5. KIAPO CHA MWOMBAJI',
                'blocks' => [
                    ['type' => 'declaration', 'paragraphs' => [
                        'Mimi, ................................................................, mwenye Namba ya NIDA ........................................, naapa na kuthibitisha kuwa simu ninayoomba ifuatiliwe ni mali halali na nina mamlaka ya kisheria ya kuiwasilisha.',
                        'Ninaelewa kuwa taarifa zote nilizowasilisha katika fomu hii pamoja na vielelezo vilivyowasilishwa ni za kweli na sahihi kwa kadri ya ufahamu wangu.',
                        'Ninaelewa kuwa kutoa taarifa za uongo, kughushi nyaraka au kuwasilisha ombi la kufuatilia simu ambayo si mali yangu kunaweza kusababisha hatua za kisheria kuchukuliwa dhidi yangu kwa mujibu wa sheria za Jamhuri ya Muungano wa Tanzania.',
                        'Ninakubali kuwajibika kikamilifu kwa usahihi wa taarifa nilizowasilisha, na kuondoa Digital Star Consultants dhidi ya madai yoyote yatayayotokana na taarifa zisizo sahihi nilizotoa.',
                    ]],
                ],
            ],
            [
                'heading' => '6. TAMKO MUHIMU NA KANUSHO LA WAJIBU',
                'blocks' => [
                    ['type' => 'lead', 'text' => 'Ninaelewa na kukubali kuwa:'],
                    ['type' => 'terms', 'items' => [
                        'Digital Star Consultants husaidia maandalizi ya taarifa na ufuatiliaji kupitia Polisi na mtoa huduma. Hakuna ahadi ya kupata taarifa za eneo au kuirejesha simu.',
                        'Digital Star Consultants haina mamlaka ya kisheria ya kukamata, kumhoji au kuchukua hatua zozote za utekelezaji wa sheria dhidi ya mtu yeyote anayeuzisha simu inayofuatiliwa.',
                        'Endapo simu itapatikana au eneo lake kubainika, hatua zote za kurejesha simu, kufanya uchunguzi, kukamata watuhumiwa au kufungulia mashtaka zitafanywa na mamlaka husika kwa mujibu wa sheria za nchi.',
                        'Digital Star Consultants haitawajibika kwa madhara yoyote yatayayotokana na mteja au mtu mwingine kuchukua sheria mikononi mwake bila kibali cha mamlaka husika, au kwa kufuatilia simu zisizo halili.',
                        'Digital Star Consultants hatoi dhamana ya kupatikana kwa simu husika, kwani matokeo ya huduma hutegemea upatikanaji wa taarifa na mazingira ya kiufundi yanayohusika.',
                        'Taarifa zote zitakazopewa zitatumiwa kwa madhumuni halali na kwa kuzingatia sheria za Jamhuri ya Muungano wa Tanzania.',
                    ]],
                ],
            ],
            [
                'heading' => '7. TAMKO LA MWOMBAJI',
                'blocks' => [
                    ['type' => 'declaration', 'paragraphs' => [
                        'Mimi, ................................................................, Nathibitisha kuwa nimesoma, naelewa na nakubaliana na taarifa zote zilizomo katika fomu hii.',
                    ]],
                    ['type' => 'signatures', 'items' => ['Saini ya Mwombaji', 'Tarehe']],
                ],
            ],
            [
                'heading' => '8. KWA MATUMIZI YA OFISI PEKEE',
                'blocks' => [
                    ['type' => 'office', 'fields' => ['Jina la Afisa Aliyepokea Ombi', 'Saini', 'Tarehe', 'Maoni ya Afisa']],
                ],
            ],
            [
                'heading' => '9. GHARAMA ZA HUDUMA YA KUFUATILIA (TRACKING)',
                'blocks' => [
                    [
                        'type' => 'pricing',
                        'columns' => ['Na.', 'MFURUSHI WA HUDUMA', 'MAELEZO', 'GHARAMA (TZS)'],
                        'rows' => [
                            ['no' => '1.', 'package' => 'BASIC TRACK', 'price' => '100,000/=', 'details' => [
                                'Msaada wa kuandaa taarifa za tukio',
                                'Muhtasari wa hatua zilizochukuliwa',
                            ]],
                            ['no' => '2.', 'package' => 'STANDARD TRACK', 'price' => '200,000/=', 'details' => [
                                'Msaada wa kufuatilia taarifa kwa mamlaka husika',
                                'Muhtasari wa hatua zilizochukuliwa',
                                'Ushauri wa hatua za kufuata',
                            ]],
                            ['no' => '3.', 'package' => 'PREMIUM TRACK', 'price' => '300,000/=', 'details' => [
                                'Usaidizi wa maalum wa kufuatilia',
                                'Muhtasari wa hatua zilizochukuliwa',
                                'Ushauri wa hatua za kurejesha',
                                'Ufuatiliaji wa mara kwa mara (Follow-up Support)',
                            ]],
                        ],
                    ],
                    ['type' => 'note', 'text' => 'KUMBUKA: Gharama hazijumuwi ada za kisheria au gharama zozote zitakazohusiana na mamlaka za Serikali (kama Polisi).'],
                ],
            ],
        ],
    ],

    // ---------------------------------------------------------------------
    // 2. Pasipoti ya kusafiria
    // ---------------------------------------------------------------------
    'pasipoti' => [
        'card' => [
            'title' => 'Fomu ya Maombi ya Pasipoti ya Kusafiria',
            'summary' => 'Hii ni fomu ya msaada wa maombi. Jaza unachojua na leta nyaraka ulizonazo; tutawasiliana nawe kukuelekeza kuhusu vilivyobaki. Uhamiaji ndiyo huamua na kutoa pasipoti.',
            'meta' => ['TSh 200,000/=', 'Mbagala Rangi 3', 'Msaada wa kukamilisha nyaraka'],
        ],
        'title' => 'FOMU YA MAOMBI YA PASIPOTI YA KUSAFIRIA',
        'office' => [
            ['icon' => 'map-pin', 'text' => 'Digital Star Consultants – Mbagala Rangi 3, Dar es Salaam'],
            ['icon' => 'phone', 'text' => 'Simu: 0783257716 / 0757257716 / +255 760 637 716'],
        ],
        'sections' => [
            [
                'heading' => 'SEHEMU A: TAARIFA BINAFSI ZA MWOMBAJI',
                'blocks' => [
                    ['type' => 'lines', 'columns' => 2, 'items' => [
                        'Majina Kamili (Jina, Jina la Kati, Jina la Ukoo)',
                        'Jina Maarufu (Kama linatumika)',
                        'Tarehe ya Kuzaliwa (dd/mm/yyyy)',
                        'Mahali pa Kuzaliwa (Mkoa / Wilaya)',
                        'Jinsia (Mwanaume / Mwanamke)',
                        'Uraia',
                        'Namba ya NIDA',
                        'Namba ya Simu',
                        'Anwani ya Makazi (Kimwili)',
                        'Anwani ya Posta (Kama ni tofauti)',
                        'Kazi / Cheo',
                        'Mwajiri / Jina la Kampuni (Kama ipo)',
                        'Hali ya Ndoa (Sijioa / Nimeoa / Nimetengana / Mjane)',
                    ]],
                ],
            ],
            [
                'heading' => 'SEHEMU B: TAARIFA ZA PASIPOTI YA AWALI (KAMA ULIWAIKUWA NA PASIPOTI)',
                'blocks' => [
                    ['type' => 'lines', 'columns' => 2, 'items' => [
                        'Je, umewahi kuwa na Pasipoti? (Ndiyo / Hapana)',
                        'Namba ya Pasipoti ya Awali',
                        'Tarehe ya Kutolewa (dd/mm/yyyy)',
                        'Tarehe ya Kumalizika (dd/mm/yyyy)',
                        'Mahali ilipotolewa',
                        'Sababu ya Kuomba Tena',
                    ]],
                ],
            ],
            [
                'heading' => 'SEHEMU C: TAARIFA ZA WAZAZI',
                'blocks' => [
                    ['type' => 'groups', 'items' => [
                        ['label' => 'BABA', 'lines' => [
                            'Majina Kamili',
                            'Tarehe ya Kuzaliwa',
                            'Mahali pa Kuzaliwa (Mkoa / Wilaya)',
                            'Uraia',
                            'Kazi / Cheo',
                            'Namba ya Simu',
                        ]],
                        ['label' => 'MAMA', 'lines' => [
                            'Majina Kamili',
                            'Tarehe ya Kuzaliwa',
                            'Mahali pa Kuzaliwa (Mkoa / Wilaya)',
                            'Uraia',
                            'Kazi / Cheo',
                            'Namba ya Simu',
                        ]],
                    ]],
                ],
            ],
            [
                'heading' => 'SEHEMU D: TAARIFA NYINGINE',
                'blocks' => [
                    ['type' => 'lines', 'columns' => 2, 'items' => [
                        'Nchi utakayosafiria',
                        'Muda utakaokaa nje ya nchi',
                        'Madhumuni ya Safari',
                        'Je, una visa nyinginezo? (Ndiyo / Hapana)',
                        'Kama Ndiyo, taja nchi',
                    ]],
                ],
            ],
            [
                'heading' => 'SEHEMU E: WADHAMINI NA VIELELEZO',
                'blocks' => [
                    ['type' => 'note', 'text' => 'Taarifa na viambatisho ni vya kuandaa ombi; ukikosa kitu endelea. Kila mdhamini atasaini mwenyewe sehemu zinazotakiwa na Uhamiaji. Timu yetu itakuelekeza kuhusu sifa na hatua za saini.'],
                    ['type' => 'lines', 'columns' => 1, 'items' => [
                        'Mdhamini 1: majina, anwani, simu na uhusiano',
                        'Mdhamini 2: majina, anwani, simu na uhusiano',
                        'Mzazi/mlezi wa kisheria wa mwombaji chini ya miaka 16: jina na mawasiliano',
                        'Taarifa / nyaraka unazohitaji msaada kuzipata',
                    ]],
                    ['type' => 'checks', 'items' => [
                        'Ushahidi wa kuzaliwa / uraia wa mwombaji: cheti, kiapo au cheti cha uraia kinachokubalika',
                        'Ushahidi wa kuzaliwa / uraia wa mzazi unaokubalika',
                        'Picha ya mwombaji',
                        'Ridhaa iliyoandikwa ya mzazi/mlezi wa kisheria kwa mwombaji chini ya miaka 16',
                    ]],
                ],
            ],
            [
                'heading' => 'TAMKO LA MWOMBAJI',
                'blocks' => [
                    ['type' => 'declaration', 'paragraphs' => [
                        'Naeleza kwamba taarifa zote nilizotoa hapo juu ni za kweli na sahihi.',
                    ]],
                    ['type' => 'signatures', 'items' => ['Sahihi na Mwombaji', 'Tarehe']],
                ],
            ],
        ],
        'footer_box' => [
            'title' => 'Digital Star husaidia maandalizi na ufuatiliaji. Uhamiaji ndiyo huamua maombi na muda wa kutoa pasipoti. Ukikosa taarifa au nyaraka, timu yetu itakuelekeza.',
            'fee_label' => 'GHARAMA ZA HUDUMA:',
            'fee' => 'Pasipoti ya Miaka 10 — Tsh 200,000/=',
            'contact_label' => 'Kwa maelezo zaidi wasiliana na:',
            'phones' => ['0783257716', '0757257716', '+255 760 637 716'],
            'banner' => 'KARIBU DIGITAL STAR CONSULTANTS – TUNAKUSAIDIA KWA HARAKA NA UHAKIKA!',
        ],
    ],
];
