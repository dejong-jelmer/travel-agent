<?php

return [
    'author_name' => env('COMPANY_OWNER', '') ?: config('app.name'),
    // The short bio in the author box below every story (Components/Molecules/AuthorBox.vue).
    'author_bio' => 'Ik ben de persoon achter Omdat We Reizen. Ik stel treinreizen door Europa samen zoals ik een '
        .'puzzel leg: eerst de bestemming en de route, dan de details die een reis bijzonder maken. Hier schrijf ik '
        .'over de plekken die me raakten en wat ik onderweg ontdekte.',
];
