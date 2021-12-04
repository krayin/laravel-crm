<?php

test('check main admin login page', function () {
    $response = $this->get(route('admin.session.create'));

    $response->assertStatus(200);
});
