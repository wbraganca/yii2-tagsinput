# yii2-tagsinput

[![Latest Version](https://img.shields.io/github/release/wbraganca/yii2-tagsinput.svg?style=flat-square)](https://github.com/wbraganca/yii2-tagsinput/releases)
[![Software License](http://img.shields.io/badge/license-BSD3-brightgreen.svg?style=flat-square)](LICENSE.md)
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

<?php echo $form->field($model, 'places')->widget(TagsinputWidget::classname(), [
    'clientOptions' => [
        "itemValue" => 'name',
        "itemText" => 'name',
    ],
   'dataset' => [
        [
            'remote' => [
                'url' => Url::to(['get-countries']). '?q=%QUERY',
                'wildcard' => '%QUERY'
            ],
            'datumTokenizer' => "Bloodhound.tokenizers.obj.whitespace('name')",
            'displayKey' => 'name',
            'limit' => 10,
            'templates' => [
                'header' => '<h3 class="name">Country</h3>'
            ]
        ],
        [
            'remote' => [
                'url' => Url::to(['get-cities']). '?q=%QUERY',
                'wildcard' => '%QUERY'
            ],
            'datumTokenizer' => "Bloodhound.tokenizers.obj.whitespace('name')",
            'displayKey' => 'name',
            'limit' => 10,
            'templates' => [
                'header' => '<h3 class="name">City</h3>'
            ]
        ],
        [
            'remote' => [
                'url' => Url::to(['get-states']). '?q=%QUERY',
                'wildcard' => '%QUERY'
            ],
            'datumTokenizer' => "Bloodhound.tokenizers.obj.whitespace('name')",
            'displayKey' => 'name',
            'limit' => 10,
            'templates' => [
                'header' => '<h3 class="name">State</h3>'
            ]
        ]
    ]
]) ?>

```

For more options, visit: http://bootstrap-tagsinput.github.io/bootstrap-tagsinput/examples/
