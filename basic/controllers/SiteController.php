<?php

namespace app\controllers;

use app\models\Chat;
use app\models\User;
use app\models\Users;
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
    public function actionGetusers(){
        $user = Yii::$app->user->identity;
        $messages = Chat::find()->andWhere(['user_to'=>$user->id])->orWhere(['user_from'=>$user->id])->all();
        $usersId = array();
        for($i=0;$i<count($messages);$i++){
            if(!in_array($messages[$i]->user_from, $usersId) && !in_array($messages[$i]->user_to, $usersId)){
                if($messages[$i]->user_from == $user->id){
                    $usersId[] = $messages[$i]->user_to;
                }
                else{
                    $usersId[] = $messages[$i]->user_from;
                }
            }
        }
        $users = array();
        for($i=0;$i<count($usersId);$i++){
            if($usersId[$i]!=$user->id) {
                $userData = new Users();
                $userData->id = $usersId[$i];
                $selectedUser = User::find()->andWhere(['id' => $usersId[$i]])->one();
                $userData->name = $selectedUser->name;
                $userData->last_name = $selectedUser->last_name;
                $users[] = $userData;
            }
        }
        if(count($users)>0){
            return [
                'error' => false,
                'message'=>null,
                'users'=>$users
            ];
        }
        else {
            return [
                'error' => true,
                'message'=> 'there is no message for you',
                'users'=>null
            ];
        }
    }
    public function actionGetallusers(){
        $user = Yii::$app->user->identity;
        $users = User::find()->all();
        $newUsers = array();
        $messages = Chat::find()->andWhere(['user_to'=>$user->id])->orWhere(['user_from'=>$user->id])->all();
        $usersId = array();
        for($i=0;$i<count($messages);$i++){
            if(!in_array($messages[$i]->user_from, $usersId)){
                $usersId[] = $messages[$i]->user_from;
            }
            else if(!in_array($messages[$i]->user_to, $usersId)){
                $usersId[] = $messages[$i]->user_to;
            }
        }
        for($i=0;$i<count($users);$i++){
            if($users[$i]->login!=$user->login && !in_array($users[$i]->id, $usersId)) {
                $newUsers[] = [
                    'id' => $users[$i]->id,
                    'name' => $users[$i]->name,
                    'last_name' => $users[$i]->last_name,
                ];
            }
        }
        if($newUsers!=null) {
            return [
                'error' => false,
                'message' => null,
                'users' => $newUsers
            ];
        }
        else {
            return[
                'error'=>true,
                'message'=>'There is not users in database',
            ];
        }
    }
    public function actionRecivemessage($user_id){
        $user = Yii::$app->user->identity;
        $users = User::find()->andWhere(['id'=>$user_id])->one();
        $messages = Chat::find()->all();
        $conversatioMessages = array();
        for($i=0;$i<count($messages);$i++){
            if($messages[$i]->user_from == $user->id && $messages[$i]->user_to == $user_id){
                $conversatioMessages[] = [
                    'id'=>$messages[$i]->id,
                    'message'=>$messages[$i]->message,
                    'from'=>$user->name
                ];
            }
            else if($messages[$i]->user_from == $user_id && $messages[$i]->user_to == $user->id){
                $conversatioMessages[] = [
                    'id'=>$messages[$i]->id,
                    'message'=>$messages[$i]->message,
                    'from'=>User::find()->andWhere(['id'=>$user_id])->one()->name
                ];
            }
        }
        if($conversatioMessages!=null){
            return[
              'error'=>false,
              'message'=>null,
                'messages' => $conversatioMessages
            ];
        }
        else{
            return[
                'error'=>true,
                'message'=>'no messages in this conversation',
                'messages' => null
            ];
        }
    }
}
