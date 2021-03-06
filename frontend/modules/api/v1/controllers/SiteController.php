<?php
namespace frontend\modules\api\v1\controllers;

use Yii;
use frontend\models\AddForm;
use yii\web\Controller;
use common\models\ArtSearch;
use common\models\Art;
use common\models\BlockChain;
use Coinbase\Wallet\Resource\Account;
use Coinbase\Wallet\Resource\Address;
use Coinbase\Wallet\Enum\CurrencyCode;
use Coinbase\Wallet\Resource\Transaction;
use Coinbase\Wallet\Value\Money;
use common\models\Ico;
use common\models\User;
use DateTime;
use common\models\Calendar;
use common\models\BitCoin;
use yii\data\ArrayDataProvider;
use Coinbase\Wallet\Enum\Param;



use Coinbase\Wallet\Client;
use Coinbase\Wallet\Configuration;
/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction'
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null
            ],
            'set-locale'=>[
                'class'=>'common\actions\SetLocaleAction',
                'locales'=>array_keys(Yii::$app->params['availableLocales'])
            ],
            'image-upload' => [
            'class' => 'vova07\imperavi\actions\UploadAction',
                'validatorOptions' => [
                'maxSize' => 4000000
              ],
            'url' => 'https://mapala.net/storage/web/img/', // Directory URL address, where files are stored.
            'path' => '@storage/web/img/' // Or absolute path to directory where files are stored.
            ],
        ];
    }
    
    
/*
Начальная страница проекта возвращается действием Index контроллера Site. В качестве параметров передаются переменные: $state = 'new'/'trending'/'discuss, что меняет порядок отображения контента в ленте. Если в параметрах указаны автор и прямая ссылка - контроллер возвращает статью. Если только автор - блог автора. Если массив категорий - возвращаются данные по одной или нескольким категориям. 
*/
    
    
    public function actionIndex($state = 'new', $author = null, $permlink = null, $categories = null)
    {
        $categories_tree = Art::create_array_categories();
        
        if ($categories != null){ //Отображение по категориям
            $dataProvider = Art::get_data_by_categories($categories);
            $categories_tree = Art::create_array_categories();
        
            return $this->render('index', ['dataProvider'=>$dataProvider,
            'data' => $categories_tree,
             ]); 
        }
        
        if (($permlink == null)&&($author != null)) { //Отображение персонального блога
            $dataProvider = Art::get_single_blog($author);
            return $this->render('single_blog', ['dataProvider' => $dataProvider,
                'data' => $categories_tree,
                'author'=>$author,
            ]);
            
        } elseif (($permlink != null)&&($author != null)) { //Отображение статьи в полный экран
            $model = Art::get_single_art($author, $permlink);
            if ($model == null){
                return $this->render('empty_blog');
            } else{ 
                return $this->render('single_art', ['model'=>$model,
              ]);
            }
            
        }
        
        $dataProvider = Art::get_data_by_categories($categories=null, $state); //Стандартное отображение с порядком вывода, определяемым переменной $state
        return $this->render('index', ['dataProvider'=>$dataProvider,
            'data' => $categories_tree,
         ]);
        
}



    /*
    Действие ADD используется для обновление базы. В качестве параметров возможно передать автора и прямую ссылку, что используется для смены категорий в процессе редактирования опубликованной статьи. 
    */
    
    
    public function actionAdd($author = null, $permlink = null)  //for change data model
    {
        $model = new AddForm();
        return $this->render('add', [
            'author' => $author,
            'permlink' => $permlink
        ]);
    }
    
    
    
    /*
    Поскольку мы не храним ключей пользователя и не передаем их на сервер ни в каком виде, то единственный способ перейти в блог пользователя - это перенаправить его на страницу, где выполнится javascript скрипт, получающий имя аккаунта пользователя и переадресующий с ним на действие Site/Index с параметром author.
    */
    
       
    public function actionShow_single_blog(){
      
        return $this->render('_pre_single_blog');
    }
    
    

    /*
    Используется для построения дерева комментарием к определенной статье. 
    */
    
    public function actionComments($permlink) {
      $model = new Art();
      $model = Art::find()->where(['permlink' => $permlink])->one();
    
      return $this->renderAjax('comments', [
         'model' => $model
     ]);
     }
     
     
    public function actionInvestors(){
        $investors = Bitcoin::get_all_investors();
        $total_btc = Bitcoin::get_all_btc();
        $total_tokens = Bitcoin::get_all_tokens();
        
    $data_provider = new ArrayDataProvider([
        'allModels' => $investors,
        'sort' => [
            'attributes' => ['name', 'created_at', 'amount', 'bonuse', 'tokens', 'bonuse', 'symbol'],
        ],
        'pagination' => [
            'pageSize' => 50,
        ],
    ]);
        
        return $this->render('investors',[
            'total_btc' => $total_btc,
            'total_tokens' => $total_tokens,
            'data_provider' => $data_provider,
        ]);   
    } 
     
     
     
    public function actionIco($user){
$btc_wallet = BitCoin::get_user_wallet($user);
$total_invest_by_user = BitCoin::get_user_btc_investments($user);
$total_amount = BitCoin::get_total_amount($total_invest_by_user);
$interval = Bitcoin::get_interval();
$data = BitCoin::get_data($interval);
$weekly_btc = BitCoin::get_weekly_btc($data);
$weekly_gbg = BitCoin::get_weekly_gbg($data);

$players = BitCoin::get_data($interval);
$personal_tokens = BitCoin::get_tokens($user);
$total_btc = Bitcoin::get_all_btc();
$personal_btc = Bitcoin::get_personal_btc($user);
$personal_gbg = Bitcoin::get_personal_gbg($user);
$total_tokens = Bitcoin::get_all_tokens();
$bonuse_today = BitCoin::get_bonuse_today();

        $ico =  array( 
            'amount' => $total_amount,
            'btc_wallet'=>$btc_wallet['btc_wallet'],
            'players' => $players,
            'tokens' => $personal_tokens,
            'interval' => $interval,
            'total_btc' => $total_btc,
            'total_tokens' => $total_tokens,
            'personal_btc' => $personal_btc, 
            'personal_gbg' => $personal_gbg,
            'weekly_btc' => $weekly_btc, 
            'weekly_gbg' => $weekly_gbg,
            'bonuse_today' => $bonuse_today,
        );
        return  $json = json_encode($ico);    
        
    }
    
     
    
    
    
    public function actionPersonal_history(){
        $btc_wallet = BitCoin::get_user_wallet();
        $total_btc = Bitcoin::get_all_btc();
        $personal_btc = Bitcoin::get_personal_btc();
        $personal_gbg = Bitcoin::get_personal_gbg();
        $personal_tokens = BitCoin::get_tokens();
        $total_invest_by_user = BitCoin::get_user_btc_investments();
        $total_tokens = Bitcoin::get_all_tokens();
        
    $data_provider_for_periods = new ArrayDataProvider([
        'allModels' => $total_invest_by_user,
        'sort' => [
            'attributes' => ['name', 'created_at', 'amount', 'bonuse', 'currency','tokens', 'symbol'],
        ],
        'pagination' => [
            'pageSize' => 50,
        ],
    ]);

        
        return $this->render('personal_info',[
            'total_btc' => $total_btc,
            'total_tokens' => $total_tokens,
            'data_provider_for_periods' => $data_provider_for_periods,
            'btc_wallet'=>$btc_wallet['btc_wallet'],
            'personal_btc' => $personal_btc, 
            'personal_gbg' => $personal_gbg,
               'tokens' => $personal_tokens,
         
       
            
        ]);   
    } 
    
    
    
    
    
    
}
