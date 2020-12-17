<?php
/*
 * Copyright (c) 2017-2018 THL A29 Limited, a Tencent company. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace TencentCloud\Ses\V20201002\Models;
use TencentCloud\Common\AbstractModel;

/**
 * SendEmail请求参数结构体
 *
 * @method string getFromEmailAddress() 获取发信邮件地址。比如：noreply@mail.qcloud.com。
 * @method void setFromEmailAddress(string $FromEmailAddress) 设置发信邮件地址。比如：noreply@mail.qcloud.com。
 * @method array getDestination() 获取收信人邮箱地址
 * @method void setDestination(array $Destination) 设置收信人邮箱地址
 * @method string getSubject() 获取邮件主题
 * @method void setSubject(string $Subject) 设置邮件主题
 * @method string getReplyToAddresses() 获取邮件的“回复”电子邮件地址。可以填写您能收到邮件的邮箱地址，可以是个人邮箱。如果不填，收件人将会回复到腾讯云。
 * @method void setReplyToAddresses(string $ReplyToAddresses) 设置邮件的“回复”电子邮件地址。可以填写您能收到邮件的邮箱地址，可以是个人邮箱。如果不填，收件人将会回复到腾讯云。
 * @method Template getTemplate() 获取使用模板发送时，填写的模板相关参数
 * @method void setTemplate(Template $Template) 设置使用模板发送时，填写的模板相关参数
 * @method Simple getSimple() 获取使用API直接发送内容时，填写的邮件内容
 * @method void setSimple(Simple $Simple) 设置使用API直接发送内容时，填写的邮件内容
 */
class SendEmailRequest extends AbstractModel
{
    /**
     * @var string 发信邮件地址。比如：noreply@mail.qcloud.com。
     */
    public $FromEmailAddress;

    /**
     * @var array 收信人邮箱地址
     */
    public $Destination;

    /**
     * @var string 邮件主题
     */
    public $Subject;

    /**
     * @var string 邮件的“回复”电子邮件地址。可以填写您能收到邮件的邮箱地址，可以是个人邮箱。如果不填，收件人将会回复到腾讯云。
     */
    public $ReplyToAddresses;

    /**
     * @var Template 使用模板发送时，填写的模板相关参数
     */
    public $Template;

    /**
     * @var Simple 使用API直接发送内容时，填写的邮件内容
     */
    public $Simple;

    /**
     * @param string $FromEmailAddress 发信邮件地址。比如：noreply@mail.qcloud.com。
     * @param array $Destination 收信人邮箱地址
     * @param string $Subject 邮件主题
     * @param string $ReplyToAddresses 邮件的“回复”电子邮件地址。可以填写您能收到邮件的邮箱地址，可以是个人邮箱。如果不填，收件人将会回复到腾讯云。
     * @param Template $Template 使用模板发送时，填写的模板相关参数
     * @param Simple $Simple 使用API直接发送内容时，填写的邮件内容
     */
    function __construct()
    {

    }

    /**
     * For internal only. DO NOT USE IT.
     */
    public function deserialize($param)
    {
        if ($param === null) {
            return;
        }
        if (array_key_exists("FromEmailAddress",$param) and $param["FromEmailAddress"] !== null) {
            $this->FromEmailAddress = $param["FromEmailAddress"];
        }

        if (array_key_exists("Destination",$param) and $param["Destination"] !== null) {
            $this->Destination = $param["Destination"];
        }

        if (array_key_exists("Subject",$param) and $param["Subject"] !== null) {
            $this->Subject = $param["Subject"];
        }

        if (array_key_exists("ReplyToAddresses",$param) and $param["ReplyToAddresses"] !== null) {
            $this->ReplyToAddresses = $param["ReplyToAddresses"];
        }

        if (array_key_exists("Template",$param) and $param["Template"] !== null) {
            $this->Template = new Template();
            $this->Template->deserialize($param["Template"]);
        }

        if (array_key_exists("Simple",$param) and $param["Simple"] !== null) {
            $this->Simple = new Simple();
            $this->Simple->deserialize($param["Simple"]);
        }
    }
}
