<?php

namespace app\models\query;

use yii\db\ActiveQuery;

/**
* This is the query class for class  "Option".
*
*/
class OptionQuery extends ActiveQuery
{
    public function primary_key($primary_key = null)
    {
        if (is_array($primary_key)) {
            $this->andWhere($primary_key);
        } else {
            if ($primary_key) {
                $this->andWhere(['id' => $primary_key]);
            }
        }
        return $this;
    }

    public function poll_searchOptions($poll_searchOptions = [])
    {
        if (is_array($poll_searchOptions) && sizeof($poll_searchOptions)) {
            $this->andWhere($poll_searchOptions);
        }
        return $this;
    }

    public function poll_id($poll_id = null)
    {
        if ($poll_id) {
            $this->andWhere(['poll_id' => $poll_id]);
        }
        return $this;
    }
}
