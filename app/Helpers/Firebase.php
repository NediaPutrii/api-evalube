<?php

namespace App\Helpers;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;


class firebase
{
    public static function updateDeviceId(string $fcm_token, string $device_id)
    {
        try{
            $database = app('firebase.database');
            $updates = [
                'Users/'.$fcm_token.'/device_id' => $device_id
            ];
            $result = $database->getReference()->update($updates);
            return $result->getChild('Users/'.$fcm_token.'/device_id')->getValue();
        }
        catch (\Exception $e) {
            return null; 
        }
    }

    public static function subscribeTopic(string $topic, string $deviceId)
    {
        try{
            $messaging = app('firebase.messaging');
            if(!$topic){
                $topic = "Presensi JSI";
            }

            if($deviceId){
                $messaging->subscribeToTopic($topic, $deviceId);
                return true;
            }
            return false;
        }
        catch (\Exception $e) {
            return false; 
        }
    }

    public static function unSubscribeTopic(string $topic, string $fcm_token)
    {
        try{
            $messaging = app('firebase.messaging');
            $database = app('firebase.database');
            $deviceId = $database->getReference('Users/'.$fcm_token.'/device_id')->getValue();
            if($deviceId){
                $messaging->unsubscribeFromTopic($topic, $deviceId);
                return true;
            }
            return false;
        }
        catch (\Exception $e) {
            return false; 
        }
    }

    public static function unSubscribeAllTopicByDeviceId($deviceId)
    {
        $messaging = app('firebase.messaging');
        $appInstance = $messaging->getAppInstance($deviceId);
        $subscriptions = $appInstance->topicSubscriptions();
        foreach ($subscriptions as $subscription) {
            if($subscription->registrationToken()==$deviceId){
                $messaging->unsubscribeFromTopic($subscription->topic(), $deviceId);
            }
        }
    }

    public static function unSubscribeAllTopic(string $fcm_token)
    {
        try{
            $messaging = app('firebase.messaging');
            $database = app('firebase.database');
            $deviceId = $database->getReference('Users/'.$fcm_token.'/device_id')->getValue();
            if($deviceId){
                //unsubscribe 
                $appInstance = $messaging->getAppInstance($deviceId);
                $subscriptions = $appInstance->topicSubscriptions();
                foreach ($subscriptions as $subscription) {
                    if($subscription->registrationToken()==$deviceId){
                        $messaging->unsubscribeFromTopic($subscription->topic(), $deviceId);
                    }
                }
                return true;
            }
            return false;
        }
        catch (\Exception $e) {
            return false; 
        }
    }

   
}