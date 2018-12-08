<?php
namespace Voice\Model;

use Core\Model\AbstractModel;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Uniqueness;

/**
 * @Source('fly_voice_script_category');
 * @Behavior('\Shirou\Behavior\Model\Timestampable');
 * @HasMany('id', '\Voice\Model\Script', 'vscid', {'alias': 'scripts'})
 */
class ScriptCategory extends AbstractModel
{
    /**
    * @Primary
    * @Identity
    * @Column(type="integer", nullable=false, column="vsc_id")
    */
    public $id;

    /**
    * @Column(type="string", nullable=true, column="vsc_name")
    */
    public $name;

    /**
    * @Column(type="string", nullable=true, column="vsc_display_order")
    */
    public $displayorder;

    /**
    * @Column(type="integer", nullable=true, column="vsc_parent_id")
    */
    public $parentid;

    /**
    * @Column(type="integer", nullable=true, column="vsc_status")
    */
    public $status;

    /**
    * @Column(type="integer", nullable=true, column="vsc_date_created")
    */
    public $datecreated;

    /**
    * @Column(type="integer", nullable=true, column="vsc_date_modified")
    */
    public $datemodified;

    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 3;

    public function getStatusName(): string
    {
        $name = '';
        $lang = self::getStaticDi()->get('lang');

        switch ($this->status) {
            case self::STATUS_ENABLE:
                $name = $lang->_('label-status-enable');
                break;
            case self::STATUS_DISABLE:
                $name = $lang->_('label-status-disable');
                break;
        }

        return $name;
    }

    public static function getStatusList()
    {
        $lang = self::getStaticDi()->get('lang');

        return $data = [
            [
                'label' => $lang->_('label-status-enable'),
                'value' => (string) self::STATUS_ENABLE
            ],
            [
                'label' => $lang->_('label-status-disable'),
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
}
