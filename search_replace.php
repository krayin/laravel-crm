<?php

return [
    "remove_else_1" => [
        'search' =>
        "if ('<1:in_between>') {
            '<2:var>' = '<3:statement>';
        } else {'<4:in_between>'}

        return '<5:var>';",
        'replace' => "if ('<1>') {
            return '<3>';
        }
        '<4>'

        return '<5>';",
        'predicate' => function($matches) {
            $values = $matches['values'];

            if ($values[1][1] !== $values[4][1]) {
                return false;
            }

            return strlen(str_replace(' ', '', $values[3][1])) > 30;
        },
        'tags' => ['flatten']
    ],
];
