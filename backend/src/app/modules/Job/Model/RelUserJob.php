<?php
namespace Job\Model;

use Core\Model\AbstractModel;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Uniqueness;
use Shirou\Behavior\Model\Fileable;
use Core\Helper\Utils as Helper;

/**
 * @Source('fly_rel_user_job');
 * @Behavior('\Shirou\Behavior\Model\Timestampable');
 * @BelongsTo('uid', '\User\Model\User', 'id', {'alias': 'user'})
 * @BelongsTo('jid', '\Job\Model\Job', 'id', {'alias': 'job'})
 */
class RelUserJob extends AbstractModel
{
    /**
    * @Column(type="integer", nullable=true, column="u_id")
    */
    public $uid;

    /**
    * @Column(type="integer", nullable=true, column="j_id")
    */
    public $jid;

    /**
    * @Primary
    * @Identity
    * @Column(type="integer", nullable=false, column="ruj_id")
    */
    public $id;

    /**
    * @Column(type="integer", nullable=true, column="ruj_progress")
    */
    public $progress;

    /**
    * @Column(type="integer", nullable=true, column="ruj_status")
    */
    public $status;

    /**
    * @Column(type="integer", nullable=true, column="ruj_date_created")
    */
    public $datecreated;

    /**
    * @Column(type="integer", nullable=true, column="ruj_date_modified")
    */
    public $datemodified;

    const STATUS_COMPLETED = 1;
    const STATUS_CHECKING = 3;
    const STATUS_DOING = 5;

    public function getStatusName(): string
    {
        $name = '';
        $lang = self::getStaticDi()->get('lang');

        switch ($this->status) {
            case self::STATUS_COMPLETED:
                $name = $lang->_('label-status-completed');
                break;
            case self::STATUS_CHECKING:
                $name = $lang->_('label-status-checking');
                break;
            case self::STATUS_DOING:
                $name = $lang->_('label-status-doing');
                break;
        }

        return $name;
    }

    public static function getStatusList()
    {
        $lang = self::getStaticDi()->get('lang');

        return $data = [
            [
                'label' => $lang->_('label-status-completed'),
                'value' => (string) self::STATUS_COMPLETED
            ],
            [
                'label' => $lang->_('label-status-checking'),
                'value' => (string) self::STATUS_CHECKING
            ],
            [
                'label' => $lang->_('label-status-doing'),
                'value' => (string) self::STATUS_DOING
            ],
        ];
    }

    public function getStatusStyle(): string
    {
        $class = '';
        switch ($this->status) {
            case self::STATUS_COMPLETED:
                $class = 'success';
                break;
            case self::STATUS_CHECKING:
                $class = 'warning';
                break;
            case self::STATUS_DOING:
                $class = 'default';
                break;
        }

        return $class;
    }
}
