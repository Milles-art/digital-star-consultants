<?php

/*
|--------------------------------------------------------------------------
| Kiswahili validation messages
|--------------------------------------------------------------------------
|
| Only the rules this app actually uses are listed here. Anything missing
| falls back to the framework's English messages automatically (the app
| fallback locale is sw, and untranslated keys resolve per key), so this
| file can grow rule by rule as new ones are introduced.
|
*/

return [

    // Field presence / basic types
    'accepted' => ':attribute lazima likubaliwe.',
    'required' => ':attribute inahitajika.',
    'required_if' => ':attribute inahitajika wakati :other ni :value.',
    'required_unless' => ':attribute inahitajika isipokuwa :other ni :values.',
    'required_with' => ':attribute inahitajika wakati :values vipo.',
    'required_without' => ':attribute inahitajika wakati :values havipo.',
    'nullable' => ':attribute kinaruhusiwa kuachwa wazi.',
    'present' => ':attribute lazima lipatikane.',
    'string' => ':attribute lazima liwe maandishi.',
    'numeric' => ':attribute lazima liwe namba.',
    'integer' => ':attribute lazima liwe namba nzima.',
    'boolean' => ':attribute lazima liwe "kweli" au "si kweli".',
    'array' => ':attribute lazima liwe orodha.',
    'file' => ':attribute lazima liwe faili.',
    'image' => ':attribute lazima liwe picha.',
    'date' => ':attribute lazima liwe tarehe sahihi.',
    'date_format' => ':attribute lazima ilingane na muundo :format.',
    'email' => ':attribute lazima liwe barua pepe sahihi.',
    'url' => ':attribute lazima liwe kiungo sahihi.',
    'in' => ':attribute ni si sahihi.',
    'not_in' => ':attribute ni si sahihi.',
    'exists' => ':attribute ni si sahihi.',
    'unique' => ':attribute tayari imetumika.',
    'confirmed' => ':attribute hakibiani na uthibitisho wake.',
    'regex' => ':attribute lazima ulingane na muundo uliotolewa.',
    'same' => ':attribute lazima lilingane na :other.',
    'different' => ':attribute lazima lingane na :other.',
    'before_or_equal' => ':attribute lazima liwe :date au kabla yake.',
    'after_or_equal' => ':attribute lazima liwe :date au baada yake.',

    // Size limits
    'min.numeric' => ':attribute lazima liwe angalau :min.',
    'min.string' => ':attribute lazima liwe angalau herufi :min.',
    'min.file' => ':attribute lazima liwe angalau :min kilobaiti.',
    'min.array' => ':attribute lazima liwe angalau :min vitu.',
    'max.numeric' => ':attribute lazima lisizidi :max.',
    'max.string' => ':attribute lazima lisizidi herufi :max.',
    'max.file' => ':attribute lazima lisizidi :max kilobaiti.',
    'max.array' => ':attribute lazima lisizidi :max vitu.',
    'between.numeric' => ':attribute lazima liwe kati ya :min na :max.',
    'between.string' => ':attribute lazima liwe herufi kati ya :min na :max.',
    'between.file' => ':attribute lazima liwe kati ya :min na :max kilobaiti.',
    'between.array' => ':attribute lazima liwe vitu kati ya :min na :max.',

    // Uploads
    'mimes' => ':attribute lazima liwe faili la aina zifuatazo: :values.',
    'mimetypes' => ':attribute lazima liwe faili la aina zifuatazo: :values.',
    'dimensions' => ':attribute lina vipimo visivyo sahihi vya picha.',
    'extensions' => ':attribute lazima liwe na kiendelezi: :values.',

    // Dates
    'after' => ':attribute lazima liwe tarehe baada ya :date.',
    'before' => ':attribute lazima liwe tarehe kabla ya :date.',
];
