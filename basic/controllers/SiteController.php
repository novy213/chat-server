<?php

namespace app\controllers;

use app\models\Chat;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use app\components\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }
    public function actionRegister(){

        $post = $this->getJsonInput();
        $user = new User();
        if (isset($post->login)) {
            $user->login = $post->login;
        }
        if (isset($post->password)) {
            $user->password = $post->password;
        }
        if (isset($post->name)) {
            $user->name = $post->name;
        }
        if (isset($post->last_name)) {
            $user->last_name = $post->last_name;
        }
        if ($user->validate()) {
            $user->save();
            return [
                'error' => FALSE,
                'message' => NULL,
            ];
        } else {
            return [
                'error' => true,
                'message' => $user->getErrorSummary(false),
            ];
        }
    }
    public function actionSendmessage($user_to){
        $post = $this->getJsonInput();
        $user = Yii::$app->user->identity;
        $chat = new Chat();
        if(isset($post->message)){
            $chat->message = $post->message;
        }
        if(isset($user_to)){
            $chat->user_to = $user_to;
        }
        if(isset($user)){
            $chat->user_from = $user->id;
        }
        if($chat->validate()){
            $chat->save();
            return[
                'error' => false,
                'message'=>null
            ];
        }
        else{
            return [
                'error' => true,
                'message' => $chat->getErrorSummary(false),
            ];
        }
    }
    public function actionRecivemessage($user_from){
        $user = Yii::$app->user->identity;
        $chat = Chat::find()->andWhere(['user_to'=>$user->id,'user_from'=>$user_from])->all();
        if($chat!=null){
            return[
                'error' => false,
                'message'=>null,
                'chat'=>$chat
            ];
        }
        else{
            return [
                'error' => true,
                'message' => 'There is no any message for you'
            ];
        }
    }
}
