<?php

return [
    'importers' => [
        'persons' => [
            'title' => '담당자',

            'validation' => [
                'errors' => [
                    'duplicate-email' => '이메일 : \'%s\'가 가져오기 파일에 두 번 이상 존재합니다.',
                    'duplicate-phone' => '전화번호 : \'%s\'가 가져오기 파일에 두 번 이상 존재합니다.',
                    'email-not-found' => '이메일 : \'%s\'를 시스템에서 찾을 수 없습니다.',
                ],
            ],
        ],

        'products' => [
            'title' => '상품',

            'validation' => [
                'errors' => [
                    'sku-not-found' => '지정한 SKU를 가진 상품을 찾을 수 없습니다',
                ],
            ],
        ],

        'leads' => [
            'title' => '리드',

            'validation' => [
                'errors' => [
                    'id-not-found' => 'ID : \'%s\'를 시스템에서 찾을 수 없습니다.',
                ],
            ],
        ],
    ],

    'validation' => [
        'errors' => [
            'column-empty-headers' => '열 번호 "%s"에 빈 헤더가 있습니다.',
            'column-name-invalid' => '잘못된 열 이름입니다: "%s".',
            'column-not-found' => '필수 열을 찾을 수 없습니다: %s.',
            'column-numbers' => '열의 개수가 헤더의 행 개수와 일치하지 않습니다.',
            'invalid-attribute' => '헤더에 잘못된 속성이 포함되어 있습니다: "%s".',
            'system' => '예기치 않은 시스템 오류가 발생했습니다.',
            'wrong-quotes' => '직선 따옴표 대신 곡선 따옴표가 사용되었습니다.',
            'already-exists' => ':attribute이(가) 이미 존재합니다.',
        ],
    ],
];
