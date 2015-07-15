<?php

namespace app\models;

use \app\models\query\CodeQuery;

class Code extends \app\models\base\CodeBase
{
    const CODE_STATUS_INVALID_USED = -2;
    const CODE_STATUS_INVALID_UNUSED = -1;
    const CODE_STATUS_UNUSED = 1;
    const CODE_STATUS_USED = 2;


    /**
     * @return returns representingColumn default null
     */
    public static function representingColumn()
    {
        return ['token'];
    }

    /**
     * @inheritdoc
     * @return CodeQuery
     */
    public static function find()
    {
        return new CodeQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
        ]);
    }

    public static function generateCode($poll_id, $member_id) {
        $code = new Code();
        $code->member_id = $member_id;
        $code->poll_id = $poll_id;
        $length = 10;
        $code->token = substr(str_shuffle(MD5(microtime())), 0, $length);//Yii::$app->getSecurity()->generateRandomString($length);
        // Better safe than sorry, avoid collisions.
        while(!$code->validate(['token'])) {
            $code->token = substr(str_shuffle(MD5(microtime())), 0, $length);
        }
        return $code;
    }

    public function isValid() {
        return in_array($this->code_status, [self::CODE_STATUS_UNUSED, self::CODE_STATUS_USED]);
    }

    public function isUsed() {
        return in_array($this->code_status, [self::CODE_STATUS_USED, self::CODE_STATUS_INVALID_USED]);
    }

    public static function findCodeByToken($token, $type = null)
    {
        return static::findOne(['token' => $token]);
    }

    public function getHTMLOptions() {
        if (!$this->isValid()) {
            return ['class' => 'token-invalid', 'title' => 'This voting code has been invalidated'];
        }
        if (!$this->isUsed()) {
            return ['class' => 'token-valid', 'title' => 'This voting code has not been used'];
        }
        if ($this->isUsed()) {
            return ['class' => 'token-used', 'title' => 'A vote has been submitted using this voting code'];
        }
    }

}
