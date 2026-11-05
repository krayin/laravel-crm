<?php

namespace Tests\Fuzz;

use Random\Engine\Xoshiro256StarStar;
use Random\Randomizer;

class FuzzInput
{
    public const DEFAULT_SEED = 20261105;

    public const CORPUS = [
        '',
        ' ',
        '0',
        '-1',
        '1.5',
        '99999999999999999999',
        '1e309',
        'NaN',
        'null',
        'true',
        "' OR '1'='1",
        '1; DROP TABLE users--',
        '"\\',
        '<script>alert(1)</script>',
        '"><img src=x onerror=alert(1)>',
        '{{7*7}}',
        '${7*7}',
        "{{constructor.constructor('alert(1)')()}}",
        '../../../../etc/passwd',
        '..%2f..%2f..%2fetc%2fpasswd',
        '%s%s%s%n',
        "line\r\nX-Injected: 1",
        "\u{202E}gnp.exe",
        '😀👍🏽',
        '[]',
        '{"a":1}',
    ];

    public const NOT_JSON_SAFE = [
        "\0",
        "\xC3\x28",
    ];

    protected const ALPHABET = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789 !"#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~';

    public static function seed(): int
    {
        return (int) (getenv('FUZZ_SEED') ?: self::DEFAULT_SEED);
    }

    public static function iterations(): int
    {
        $iterations = getenv('FUZZ_ITERATIONS');

        return $iterations === false ? 5 : max(0, (int) $iterations);
    }

    /**
     * The corpus, a long string and seeded random strings. Pass false for values sent in a JSON body.
     */
    public static function strings(bool $includeNonJson = true): array
    {
        $randomizer = new Randomizer(new Xoshiro256StarStar(self::seed()));

        $random = [];

        for ($i = 0; $i < self::iterations(); $i++) {
            $random[] = $randomizer->getBytesFromString(self::ALPHABET, $randomizer->getInt(1, 64));
        }

        return array_values(array_unique(array_merge(
            self::CORPUS,
            $includeNonJson ? self::NOT_JSON_SAFE : [],
            [str_repeat('A', 10000)],
            $random,
        )));
    }

    /**
     * Values of every JSON type, for type confusion in request bodies.
     */
    public static function values(): array
    {
        return array_merge(self::strings(false), [
            null,
            true,
            false,
            0,
            -1,
            PHP_INT_MAX,
            1.5,
            [],
            ['a'],
            ['nested' => ['deeper' => ['deepest' => '<script>alert(1)</script>']]],
            array_fill(0, 500, 'x'),
        ]);
    }

    /**
     * A short set of values of every type, for fuzzing many routes at once.
     */
    public static function typeConfusion(): array
    {
        return [
            null,
            true,
            0,
            -1,
            PHP_INT_MAX,
            1.5,
            '',
            '-1',
            '99999999999999999999',
            "' OR '1'='1",
            '<script>alert(1)</script>',
            '{{7*7}}',
            '../../../../etc/passwd',
            str_repeat('A', 10000),
            [],
            ['a'],
            ['nested' => ['deeper' => ['deepest' => 'x']]],
            array_fill(0, 500, 'x'),
        ];
    }

    /**
     * A readable label for a fuzz value in failure messages.
     */
    public static function describe(mixed $value): string
    {
        $encoded = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);

        return strlen($encoded) > 60 ? substr($encoded, 0, 57).'...' : $encoded;
    }
}
