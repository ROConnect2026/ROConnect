<?php

test('homepage shows translation engine button linking to translator page', function () {
    $response = $this->get(route('home'));

    $response
        ->assertOk()
    ->assertSee('Translation Engine')
        ->assertSee(route('translator.index'), false);
});
