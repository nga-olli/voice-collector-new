<?php
namespace Job\Model;

use Core\Model\AbstractModel;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Uniqueness;
use Shirou\Behavior\Model\Fileable;
use Core\Helper\Utils as Helper;

/**
 * @Source('fly_job');
 * @Behavior('\Shirou\Behavior\Model\Timestampable');
 */
class Job extends AbstractModel
{
    /**
    * @Primary
    * @Identity
    * @Column(type="integer", nullable=false, column="j_id")
    */
    public $id;

    /**
    * @Column(type="string", nullable=true, column="j_name")
    */
    public $name;

    /**
    * @Column(type="string", nullable=true, column="j_description")
    */
    public $description;

    /**
    * @Column(type="integer", nullable=true, column="j_status")
    */
    public $status;

    /**
    * @Column(type="integer", nullable=true, column="j_type")
    */
    public $type;

    /**
    * @Column(type="integer", nullable=true, column="j_max_coin_reward")
    */
    public $maxcoinreward;

    /**
    * @Column(type="integer", nullable=true, column="j_number_of_scripts")
    */
    public $numberofscripts;

    /**
    * @Column(type="integer", nullable=true, column="j_posted_by")
    */
    public $postedby;

    /**
    * @Column(type="integer", nullable=true, column="j_required_id")
    */
    public $requiredid;

    /**
    * @Column(type="string", nullable=true, column="j_cover")
    */
    public $cover;

    /**
    * @Column(type="integer", nullable=true, column="j_date_created")
    */
    public $datecreated;

    /**
    * @Column(type="integer", nullable=true, column="j_date_modified")
    */
    public $datemodified;

    /**
    * @Column(type="integer", nullable=true, column="j_date_expired")
    */
    public $dateexpired;

    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 3;

    /**
     * Initialize model
     */
    public function initialize()
    {
        $config = $this->getDI()->get('config');

        if (!$this->getDI()->get('app')->isConsole()) {
            $configBehavior = [
                'field' => 'cover',
                'uploadPath' => $config->default->jobs->directory,
                'allowedFormats' => $config->default->jobs->mimes->toArray(),
                'allowedMaximumSize' => $config->default->jobs->maxsize,
                'allowedMinimumSize' => $config->default->jobs->minsize,
                'isOverwrite' => $config->default->jobs->isoverwrite
            ];

            $this->addBehavior(new Fileable([
                'beforeDelete' => $configBehavior
            ]));
        }
    }

    // /**
    //  * Form field validation
    //  */
    // public function validation()
    // {
    //     $validator = new Validation();

    //     $validator->add('groupid', new PresenceOf([
    //         'message' => 'message-groupid-notempty'
    //     ]));

    //     $validator->add('status', new PresenceOf([
    //         'message' => 'message-status-notempty'
    //     ]));

    //     $validator->add('email', new Uniqueness([
    //         'message' => 'message-email-unique'
    //     ]));

    //     return $this->validate($validator);
    // }

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

    // return avatar response support api
    public function getCoverJson(): string
    {
        $config = $this->getDI()->get('config');
        $url = $this->getDI()->get('url');

        if ($this->cover != '') {
            return Helper::getFileUrl(
                $url->getBaseUri(),
                $config->default->jobs->directory,
                $this->cover
            );
        } else {
            return '';
        }
    }
}
