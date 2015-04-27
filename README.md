# yii2-tagsinput

[![Latest Version](https://img.shields.io/github/release/wbraganca/yii2-tagsinput.svg?style=flat-square)](https://github.com/wbraganca/yii2-tagsinput/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/wbraganca/yii2-tagsinput/master.svg?style=flat-square)](https://travis-ci.org/wbraganca/yii2-tagsinput)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/wbraganca/yii2-tagsinput.svg?style=flat-square)](https://scrutinizer-ci.com/g/wbraganca/yii2-tagsinput/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/wbraganca/yii2-tagsinput.svg?style=flat-square)](https://scrutinizer-ci.com/g/wbraganca/yii2-tagsinput)
[![Total Downloads](https://img.shields.io/packagist/dt/wbraganca/yii2-tagsinput.svg?style=flat-square)](https://packagist.org/packages/wbraganca/yii2-tagsinput)


## Install

Via Composer

```bash
$ composer require wbraganca/yii2-tagsinput
```

or add

```
"wbraganca/yii2-tagsinput": "*"
```

to the require section of your `composer.json` file.


## Usage

On your view file.

```php

<?php
use wbraganca\tagsinput\TagsinputWidget;
?>

<?= $form->field($model, 'tags')->widget(TagsinputWidget::classname(), [
    'clientOptions' => [
        'trimValue' => true,
        'allowDuplicates' => false
    ]
]) ?>

```

For more options, visit: http://timschlechter.github.io/bootstrap-tagsinput/examples/#options
