<?php

/**
 *------------------------------------------------------------------------------.
 *
 * @copyright     Copyright (C) 2004-2020 JoomlArt.com. All Rights Reserved.
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 * @authors       JoomlArt
 * @forum:        https://www.joomlart.com/forums/t/t4-builder
 * @Link:         https://demo.t4-builder.joomlart.com/
 *------------------------------------------------------------------------------
 */

namespace JPB\Editor\Action;

defined('_JEXEC') or die;
use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Language\Text as JText;

class Contact extends Base
{
    public function run()
    {
        $data = JFactory::getApplication()->input;
        $task = $data->get('task');
        switch ($task) {
            case 'contact':
                $return = self::contact($data);
                break;

            case 'subscribe':
                if ($data->get('platform') == 'acym') {
                    $return = self::subscribe($data);
                } elseif ($data->get('platform') == 'mailchimp') {
                    $return = self::subscribeMailChimp($data);
                }
                break;
            default:
                $return = ['error' => JText::_('COM_T4PAGEBUILDER_CONTACT_NO_ACTION')];
        }

        return $return;
    }

    public static function subscribe($data)
    {
        $db = JFactory::getDbo();
        //get data
        $name = $data->getVar('name');
        $email = $data->getVar('email');
        $field_list = explode('@', $data->getVar('hiddenlists'));

        if (empty($name)) {
            $matching = explode('@', $email);
            $name = $matching[0];
        }
        if (self::vali_acymEmail($email)) {
            return ['message' => JText::_('COM_T4PAGEBUILDER_EMAIL_SUBCRIBED')];
        }
        //create user subscribe and subscribe list
        $user = new \stdClass();
        $user->name = $name;
        $user->email = $email;
        $user->key = self::acym_generateKey(14);
        $user->source = 'T4 Page Builder';
        $user->creation_date = date('Y-m-d h:m:s');
        $db->insertObject('#__acym_user', $user, 'id');
        $userId = $db->insertId();
        foreach ($field_list as $list) {
            $userList = new \stdClass();
            $userList->user_id = $userId;
            $userList->list_id = $list;
            $userList->status = 1;
            $userList->subscription_date = date('Y-m-d h:m:s');
            $db->insertObject('#__acym_user_has_list', $userList);
        }

        return ['message' => JText::_('COM_T4PAGEBUILDER_EMAIL_SUBCRIBE_SUCCESS')];
    }

    public static function contact($data)
    {
        if (empty($data)) {
            return '';
        }
        $dataForm = $data->getVar('data');
        foreach ($dataForm as $item) {
            switch ($item['name']) {
                case 'recipient':
                   $recipient = $item['value'] ? $item['value'] : "";
                    break;
                case 'subject':
                   $subject = $item['value'] ?: "";
                    break;
                case 'emailtpl':
                   $emailContent = $item['value']?: "";
                    break;

                default:
                    // code...
                    break;
            }
        }
        if(!isset($recipient)){
            return $return = ['error' => JText::_('COM_T4PAGEBUILDER_CONTACT_SEND_EMAIL_ERROR')];
        }
        $config = JFactory::getConfig();
        $result = false;
        if($emailContent) $emailContent = self::getEmailBody($dataForm, $emailContent);
        $fromName = $config->get('fromname');
        $fromEmail = $config->get('mailfrom');
        $result = self::sendEmail($recipient, $subject, $emailContent, $fromName, $fromEmail);
        if ($result) {
            $return = ['message' => JText::_('COM_T4PAGEBUILDER_CONTACT_SEND_EMAIL_MESSAGE')];
        } else {
            $return = ['error' => JText::_('COM_T4PAGEBUILDER_CONTACT_SEND_EMAIL_ERROR')];
        }

        return $return;
    }

    public static function sendEmail($recipient, $subject, $emailBody, $fromname, $fromemail)
    {
        $res = JFactory::getMailer()->sendMail(
            $fromemail,
            $fromname,
            $recipient,
            $subject,
            $emailBody,
            true
        );

        return $res;
    }

    public static function getEmailBody($data, $emailContent)
    {
        foreach ($data as $item) {
            $emailContent = str_replace('{'.$item['name'].'}', $item['value'], $emailContent);
        }

        return $emailContent;
    }

    public static function getUsers()
    {
        $db = JFactory::getDbo();

        return $db->setQuery('select name,email from #__users WHERE sendEmail = "1"')->loadObjectList();
    }

    public static function acym_generateKey($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randstring = '';
        $max = strlen($characters) - 1;
        for ($i = 0; $i < $length; ++$i) {
            $randstring .= $characters[mt_rand(0, $max)];
        }

        return $randstring;
    }

    public static function vali_acymEmail($email)
    {
        $db = JFactory::getDbo();
        $q = 'SELECT EXISTS(SELECT * FROM #__acym_user WHERE email = '.$db->quote($email).')';

        return $db->setQuery($q)->loadResult();
    }

    public static function subscribeMailChimp($data)
    {
        $dataForm = $data->getVar('data');
         //API Details
        $apiKey = '05540c5ec38f8c15dad8871f5f563859-us5';
        $listId = '78869976d3';
        $firstname = '';
        $lastname = '';
        foreach ($dataForm as $field) {
            if($field['value']){
                if($field['name'] == 'email'){
                    $email = $field['value'];
                }elseif ($field['name'] == 'api') {
                    $apiKey = $field['value'];
                }elseif($field['name'] == 'list_id'){
                    $listId = $field['value'];
                }
            }
        }
        if ($email) {
            //Create mailchimp API url
            $memberId = md5(strtolower($email));
            $dataCenter = substr($apiKey, strpos($apiKey, '-') + 1);
            $url = 'https://'.$dataCenter.'.api.mailchimp.com/3.0/lists/'.$listId.'/members/'.$memberId;

            //Member info
            $dataMember = [
            'email_address' => $email,
            'status' => 'subscribed',
            'merge_fields' => [
                'FNAME' => $firstname,
                'LNAME' => $lastname,
            ],
            ];
            $jsonString = json_encode($dataMember);

            // send a HTTP POST request with curl
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_USERPWD, 'user:'.$apiKey);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonString);
            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            //Collecting the status
            switch ($httpCode) {
                case 200:
                    $msg = ['message'=>'Success, newsletter subcribed using mailchimp API'];
                    break;
                case 214:
                    $msg = ['message'=>'Already Subscribed'];
                    break;
                default:
                    $msg = ['error' => 'Oop, Email can not subscribe. Please contact the admin'];
                    break;
            }
        }else{
            $msg = ['error'=> 'Oops, please try again. Please add email'];
        }

        return $msg;
    }
}
