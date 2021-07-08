<?php

use Illuminate\Support\Str;
use IsaEken\PasswordGenerator\Converters\PasswordToRememberable;use IsaEken\PasswordGenerator\PasswordGenerator;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * @param string $name
 * @param string $type
 * @param int|bool|null $default
 * @return int|bool|null
 */
function get_option(string $name, string $type, int|bool|null $default = null): int|bool|null {
    if (isset($_GET[$name])) {
        if ($type == 'integer') {
            return intval($_GET[$name]);
        }

        if ($type == 'boolean') {
            return $_GET[$name] == 'on';
        }
    }

    return $default;
}

$options = [
    'length' => [
        'name' => 'Length',
        'type' => 'integer',
        'value' => get_option('length', 'integer', 16),
    ],
    'symbols' => [
        'name' => 'Symbols',
        'type' => 'boolean',
        'value' => get_option('symbols', 'boolean', true),
    ],
    'numbers' => [
        'name' => 'Numbers',
        'type' => 'boolean',
        'value' => get_option('numbers', 'boolean', true),
    ],
    'lowercase' => [
        'name' => 'Lowercase',
        'type' => 'boolean',
        'value' => get_option('lowercase', 'boolean', true),
    ],
    'uppercase' => [
        'name' => 'Uppercase',
        'type' => 'boolean',
        'value' => get_option('uppercase', 'boolean', true),
    ],
    'similar' => [
        'name' => 'Similar Characters',
        'type' => 'boolean',
        'value' => get_option('similar', 'boolean', false),
    ],
    'ambiguous' => [
        'name' => 'Ambiguous Characters',
        'type' => 'boolean',
        'value' => get_option('ambiguous', 'boolean', false),
    ],
];

$generator = new PasswordGenerator;
foreach ($options as $option => $opt) {
    $generator->{$option} = $opt['value'];
}

$password = $generator->generate();
$rememberable_texts = [];

$languages = ['tr', 'en'];
foreach ($languages as $language) {
    $passwordToRememberable = new PasswordToRememberable;
    $passwordToRememberable->setLanguage($language);
    $passwordToRememberable->password = $password;
    $rememberable_texts[$language] = $passwordToRememberable->convert();
}

?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/app.css">
    <title>Password Generator</title>
</head>
<body class="bg-gray-900 text-gray-100">
    <div class="max-w-3xl mx-auto my-12">
        <div class="text-2xl font-semibold">
            Password Generator
        </div>
        <div class="my-4">
            <div class="py-3 px-2 bg-gray-800 w-full text-xl rounded text-center select-all cursor-text font-mono tracking-wider"><?= $password ?></div>
            <div class="mt-4 space-y-2">
                <?php foreach ($rememberable_texts as $language => $rememberable_text): ?>
                    <div>
                        <div class="text-gray-600 mb-1 text-xs">You can remember this password using this words (<?= mb_strtoupper($language) ?>):</div>
                        <div class="py-3 px-2 bg-gray-800 w-full text-sm rounded text-center select-all cursor-text font-mono"><?= $rememberable_text ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div>
            <form action="?" method="get">
                <?php $loop = 0; foreach ($options as $option => $opt): ?>
                    <div class="mb-4">
                        <?php if ($opt['type'] == 'integer'): ?>
                            <div>
                                <label for="option<?= $loop ?>"><?= $opt['name'] ?></label>
                            </div>
                            <div>
                                <input name="<?= $option ?>" class="text-gray-900" id="option<?= $loop ?>" type="text" value="<?= $opt['value'] ?>">
                            </div>
                        <?php elseif ($opt['type'] == 'boolean'): ?>
                            <div class="select-none">
                                <label for="option<?= $loop ?>"><?= $opt['name'] ?></label>
                                <input type="hidden" name="<?= $option ?>" id="option<?= $loop ?>cb" value="<?= $opt['value'] ? 'on' : 'off' ?>">
                                <input class="ml-2" id="option<?= $loop ?>" type="checkbox" onchange="document.getElementById('option<?= $loop ?>cb').value = this.checked ? 'on' : 'off'" <?= $opt['value'] ? 'checked' : '' ?>>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php $loop++; endforeach; ?>
                <div>
                    <button type="submit" class="py-1.5 px-3 bg-indigo-300 text-gray-900 font-semibold transition hover:bg-indigo-400">
                        Regenerate
                    </button>
                </div>
            </form>
        </div>
        <div class="my-12 text-sm">
            <a href="https://github.com/isaeken/password-generator-website" target="_blank" class="text-indigo-300 font-semibold">Password Generator Website</a>
            is developed by
            <a href="https://isaeken.com.tr" target="_blank" class="text-red-300 font-semibold">İsa Eken</a>
        </div>
    </div>
</body>
</html>
