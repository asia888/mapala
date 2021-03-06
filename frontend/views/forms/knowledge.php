
<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use common\models\Countries;
//use vova07\imperavi\Widget;
use yii\helpers\StringHelper;
use kartik\select2\Select2;
use kartik\widgets\ActiveForm;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;
use common\models\OurCategory;
use yii\helpers\Html;
use common\models\BlockChain;
use common\components\editorwidget\EditorWidget;

$this->registerJsFile('\js/form_save.js',  ['position' => yii\web\View::POS_END]); 

$this->title = Yii::t('frontend','Knowledge');

$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend','Update database'), 'url'=> ['/site/add']];
$this->params['breadcrumbs'][] = $this->title;

?>
<h1><?php echo Html::encode($this->title) ?></h1>


<div class="form-index">
     <?php if ($author&&$permlink){
              echo Html::a(Yii::t('frontend', 'Сменить модель данных'), 
                  ['/site/add', 'author' => $author, 'permlink' => $permlink], 
                  ['class'=>'btn btn-warning change_category_btn']);
          }
          ?>
         
    <div class="row">
        <div class="col-lg-12">
              <div class ='col-lg-7'>
            <?php //------ Start active form and show title
            $form = ActiveForm::begin(['id' => 'add-form']); ?>
            <?php echo $form->field($model, 'title') ?>
         
            
            <?php //-------SHOW Categories--------------------------------------------.   
            echo $form->field($model, 'tags')->widget(Select2::classname(),[
                 'options' => ['placeholder' => 'Select a category ...'],
                 "data" => ArrayHelper::map(OurCategory::find()
                        ->Where(['model' => StringHelper::basename(get_class($model))])
                        ->all(), BlockChain::get_blockchain_from_locale(), BlockChain::get_blockchain_from_locale()),
                //---------------------------------------------------------------------
            ]);?> 
            
             <?php //Show Countries-------------------------------------------------------------
                echo $form->field($model, 'country')->widget(Select2::classname(),[
                     'options' => ['placeholder' => 'Select a state ...'],

                    "data" => ArrayHelper::map(Countries::find()->all(),'id','name'),
                    'pluginEvents' => [
                     ],//---------------------------------------------------------------------
                ]); 
                ?>

                 <?php //Show Location-------------------------------------------------------------
                echo $form->field($model, 'location')->input(['text'], ['placeholder' => 'Select a location ...', 'id'=>'location']); 
                ?>
                 <?php //Show Location-------------------------------------------------------------
                echo $form->field($model, 'city')->hiddenInput(['text'], ['placeholder' => 'Select a location ...', 'id'=>'city'])->label(false); 
                ?>

             
           <?php //---------------- EDITOR------------------------------
                 echo $form->field($model, 'body')->widget(EditorWidget::className(), [
                    'settings' => [
                        'minHeight' => 400,
                        'toolbarFixedTopOffset' => 50,
                        'imageResizable' => true,
                        'imagePosition' => true,
                        'imageUpload' => yii\helpers\Url::to(['/site/image-upload']),
                        'plugins' => [
                            'fullscreen',
                            'imagemanager'
                        ]
                    ]//-------------------------------------------------
                ]);?>   
                
          
        
                   <div class="form-group">
                        <?php echo Html::submitButton(Yii::t('frontend', 'Submit'), ['class' => 'btn btn-primary', 'name' => 'add-button']) ?>
                       <div class ="loader_head"  style="display: none;">Transaction...
                       <div id = 'steem_load' class = 'loader' ></div>
                       </div>
                       <div class="account_name"></div>
                       
                   </div>
                   <?= $this->render('map',['model'=>$model, 'form' => $form]) ?>
                   <?php ActiveForm::end(); ?>
                                 
        </div>
            
            
            <div class ='col-lg-5'>
            <div class="panel panel-success">
                    <div class="panel-heading">   
                        <?= Yii::t('frontend', 'Title') ?>
                    </div>
                    <div class="panel-body"><?= Yii::t('frontend', 'Постарайся уложиться в 100 символов, кратко и емко рассказав о сути знания') ?></div>
                </div>
            
            
            </div>
    <div class ='col-lg-5'>
                <div class="panel panel-success">
                    <div class="panel-heading">   
                        <?= Yii::t('frontend', 'Category') ?>
                    </div>
                    <div class="panel-body"><?= Yii::t('frontend', 
                            '- <b>Лайфхак</b> - расскажи секреты жизни в своем городе и стране; <br>'
                            . '- <b>Погода</b> - расскажи о климате и временах года, к чему путешественнику быть готовым?; <br>'
                            . '- <b>География</b> - конечно, не все знают где находится твой город и какой пейзжах вокруг. Расскажи об этом, а лучше - покажи;<br>'
                            . '- <b>Традиции</b> - расскажи о традициях, обрядах и церемониях; <br>'
                            . '- <b>Язык</b> - расскажи на каких языках говорят в твоем городе, расскажи о нем; <br>'
                            . '- <b>Видео-презентация</b> - покажи короткую видеопрезентацию города. Позволь путешественникам взглянуть на него твоими глазами;') ?></div>
                </div>

                
            </div>
         <div class ='col-lg-5'>
                <div class="panel panel-success">
                    <div class="panel-heading">   
                        <?= Yii::t('frontend', 'Country') ?>
                    </div>
                    <div class="panel-body"><?= Yii::t('frontend', 'Страна будет размещена в "голове" дерева тегов') ?></div>
                </div>
                
            </div>
                 <div class='col-lg-5'>
                 <div class="panel panel-success">
                    <div class="panel-heading">   
                        <?= Yii::t('frontend', 'City') ?>
                    </div>
                    <div class="panel-body"><?= Yii::t('frontend', 'Город будет являться вложенной папкой в дереве тегов') ?></div>
                </div>

                
            </div>
             <div class ='col-lg-5'>
                <div class="panel panel-success">
                    <div class="panel-heading">   
                        <?= Yii::t('frontend', 'Инструкция к редактору:') ?>
                    </div>
                    <div class="panel-body"><?= Yii::t('frontend', 'Для установки картинки, вставьте в редактор прямую ссылку на нее.<br>'
                         . 'Используется базовый синтаксис html, без поддержки MarkDown. <br>'
                        );?>
                    </div>
                </div>
            </div>
            
            
            <div class ='col-lg-5'>
                 <div class="panel panel-success">
                    <div class="panel-heading">   
                        <?= Yii::t('frontend', 'Coordinates') ?>
                    </div>
                    <div class="panel-body"><?= Yii::t('frontend', 'Отметь место на карте, если это уместно, или ничего не отмечай, если нет.') ?></div>
                </div>
                
            </div>
            

            
            
    </div>

        
</div> 
    
</div>    
<?php
        yii\bootstrap\Modal::begin([
            'headerOptions' => ['id' => 'modalHead','class'=>'text-center'],
            'header' => '<h2>' . Yii::t('frontend', 'Ключ Golos') . '</h2>',
            'id' => 'modalKey',
            'size' => 'modal-lg',
            'clientOptions' => ['backdrop' => 'static', 'keyboard' => FALSE],
            'options'=>['style'=>'min-width:400px']
        ]);


        echo $this->context->renderPartial('@frontend/modules/user/views/keys/keysForm');
        yii\bootstrap\Modal::end();

        ?>

<script>
       
    $('#add-form').on('beforeSubmit', function () {
          $('.loader_head').css('display', 'inline');
    });
     var acc = '<?= BlockChain::get_blockchain_from_locale()?>' + 'ac';
       acc = getCookie(acc);
       if (acc){
          $('.account_name').text(acc);
       } else {
         $('<a id="key_modal_ask"><?php echo Yii::t('frontend', 'install STEEM posting private key') ?></a>').appendTo('.account_name');
         $(":submit").attr("disabled", true);
       } 
       
       $(".account_name").click(function() {
          $('#modalKey').modal('show');
       });
     
</script>