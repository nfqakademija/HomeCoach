<?php 
$I = new AcceptanceTester($scenario);
$I->wantTo('Check if create workout works');
$I->amOnPage('/');
$I->click('PRISIJUNGTI');
$I->fillField('_username','test');
$I->fillField('_password','test');
$I->click('_submit');
$I->amOnPage('/createWorkout');
$I->fillField('workout[title]','Pavadinimas');
$I->fillField('workout[description]','aprasymas aprasymas aprasymas aprasymas aprasymas ap');
$I->click('workout[save]');
$I->see('Pavadinimas');


