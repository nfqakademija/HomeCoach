<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('Check if workout activation works');
$I->amOnPage('/');
$I->click('PRISIJUNGTI');
$I->fillField('_username','test');
$I->fillField('_password','test');
$I->click('_submit');
$I->amOnPage('/workouts/1');
$I->click('activateForm[activate]');
$I->amOnPage('/');
$I->see('Aktyvi programa');
