<?php
namespace Reward\Model;

use Core\Model\AbstractModel;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Uniqueness;
use Core\Helper\Utils as Helper;

/**
 * @Source('fly_gift_attribute');
 * @Behavior('\Shirou\Behavior\Model\Timestampable');
 * @HasOne('gtid', '\Reward\Model\GiftType', 'id', {'alias': 'type'})
 */
class GiftAttribute extends AbstractModel
{
    /**
    * @Column(type="integer", nullable=true, column="gt_id")
    */
    public $gtid;

    /**
    * @Primary
    * @Identity
    * @Column(type="integer", nullable=false, column="ga_id")
    */
    public $id;

    /**
    * @Column(type="string", nullable=true, column="ga_name")
    */
    public $name;

    /**
    * @Column(type="integer", nullable=true, column="ga_display_order")
    */
    public $displayorder;

    /**
    * @Column(type="integer", nullable=true, column="ga_display_type")
    */
    public $displaytype;

    /**
    * @Column(type="string", nullable=true, column="ga_unit")
    */
    public $unit;

    /**
    * @Column(type="integer", nullable=true, column="ga_type")
    */
    public $type;

    /**
    * @Column(type="integer", nullable=true, column="ga_is_critical")
    */
    public $iscritical;

    /**
    * @Column(type="integer", nullable=true, column="ga_date_created")
    */
    public $datecreated;

    /**
    * @Column(type="integer", nullable=true, column="ga_date_modified")
    */
    public $datemodified;

    const TYPE_TEXT = 1;
    const TYPE_QRCODE = 3;
    const IS_CRITICAL = 1;
    const IS_NOT_CRITICAL = 3;
}
