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
    * @Column(type="integer", nullable=true, column="ruj_date_created")
    */
    public $datecreated;

    /**
    * @Column(type="integer", nullable=true, column="ruj_date_modified")
    */
    public $datemodified;
}
