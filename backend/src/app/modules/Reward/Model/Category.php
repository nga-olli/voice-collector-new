<?php
namespace Reward\Model;

use Core\Model\AbstractModel;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Uniqueness;
use Shirou\Behavior\Model\Fileable;
use Core\Helper\Utils as Helper;

/**
 * @Source('fly_reward_category');
 * @Behavior('\Shirou\Behavior\Model\Timestampable');
 */
class Category extends AbstractModel
{
    /**
    * @Primary
    * @Identity
    * @Column(type="integer", nullable=false, column="rc_id")
    */
    public $id;

    /**
    * @Column(type="string", nullable=true, column="rc_name")
    */
    public $name;

    /**
    * @Column(type="string", nullable=true, column="rc_description")
    */
    public $description;

    /**
    * @Column(type="string", nullable=true, column="rc_display_order")
    */
    public $displayorder;

    /**
    * @Column(type="integer", nullable=true, column="rc_status")
    */
    public $status;

    /**
    * @Column(type="integer", nullable=true, column="rc_parent_id")
    */
    public $parentid;

    /**
    * @Column(type="string", nullable=true, column="rc_cover")
    */
    public $cover;

    /**
    * @Column(type="integer", nullable=true, column="rc_date_created")
    */
    public $datecreated;

    /**
    * @Column(type="integer", nullable=true, column="rc_date_modified")
    */
    public $datemodified;

    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 3;

    public function initialize()
    {
        $config = $this->getDI()->get('config');

        if (!$this->getDI()->get('app')->isConsole()) {
            $configBehavior = [
                'field' => 'cover',
                'uploadPath' => $config->default->rewards->directory,
                'allowedFormats' => $config->default->rewards->mimes->toArray(),
                'allowedMaximumSize' => $config->default->rewards->maxsize,
                'allowedMinimumSize' => $config->default->rewards->minsize,
                'isOverwrite' => $config->default->rewards->isoverwrite
            ];

            $this->addBehavior(new Fileable([
                'beforeCreate' => $configBehavior,
                'beforeDelete' => $configBehavior,
                'beforeUpdate' => $configBehavior
            ]));
        }
    }

    public function getStatusName(): string
    {
        $name = '';
        $lang = self::getStaticDi()->get('lang');

        switch ($this->status) {
            case self::STATUS_ENABLE:
                $name = 'Enabled';
                break;
            case self::STATUS_DISABLE:
                $name = 'Disabled';
                break;
        }

        return $name;
    }

    public static function getStatusList()
    {
        $lang = self::getStaticDi()->get('lang');

        return $data = [
            [
                'label' => 'Enabled',
                'value' => (string) self::STATUS_ENABLE
            ],
            [
                'label' => 'Disabled',
                'value' => (string) self::STATUS_DISABLE
            ],
        ];
    }

    public function getStatusStyle(): string
    {
        $class = '';
        switch ($this->status) {
            case self::STATUS_ENABLE:
                $class = 'primary';
                break;
            case self::STATUS_DISABLE:
                $class = 'danger';
                break;
        }

        return $class;
    }

    public static function getFullParentProductCategorys($parentId = 0)
    {
        $array = Category::find()->toArray();

        $array = array_combine(array_column ($array, 'id'), array_values($array));

        foreach ($array as $k => &$v) {
            if (isset($array[$v['parentid']])) {
                $array[$v['parentid']]['children'][$k] = &$v;
            }
            unset($v);
        }
        

        return array_filter($array, function($v) use ($parentId) {
            return $v['parentid'] == $parentId;
        });
    }

    public function getCoverPath(): string
    {
        $config = $this->getDI()->get('config');
        $url = $this->getDI()->get('url');

        if ($this->cover != '') {
            return Helper::getFileUrl(
                $url->getBaseUri(),
                $config->default->rewards->directory,
                $this->cover
            );
        } else {
            return '';
        }
    }
}
