<?php
return [
    'class'=>'yii\web\UrlManager',
    'enablePrettyUrl'=>true,
    'showScriptName'=>false,
    'rules'=> [
       '' => 'site/index',
       '<action>'=>'site/<action>',
    ]
];
