<?php
use Phinx\Migration\AbstractMigration;

class CreateUserTable extends AbstractMigration
{
    public function up()
    {
        $this->execute("
            CREATE TABLE `fly_user` (
                `u_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `u_screen_name` varchar(32) DEFAULT '',
                `u_full_name` varchar(100) DEFAULT '',
                `u_email` varchar(100) DEFAULT '',
                `u_address` varchar(255) DEFAULT NULL,
                `u_password` varchar(100) NOT NULL,
                `u_groupid` varchar(100) DEFAULT '0',
                `u_avatar` varchar(255) DEFAULT '',
                `u_gender` varchar(5) DEFAULT NULL,
                `u_status` int(2) DEFAULT '0',
                `u_oauth_provider` varchar(20) DEFAULT '',
                `u_oauth_uid` varchar(50) DEFAULT '',
                `u_oauth_access_token` varchar(255) DEFAULT '',
                `u_onesignal_id` varchar(255) DEFAULT '',
                `u_state` int(2) DEFAULT '0',
                `u_dob` int(10) DEFAULT '0',
                `u_date_created` int(10) DEFAULT '0',
                `u_date_last_change_password` int(10) DEFAULT '0',
                `u_date_modified` int(10) DEFAULT '0',
                `u_mobile_number` varchar(20) DEFAULT NULL,
                `u_is_verified` tinyint(1) DEFAULT '0',
                `u_verify_type` tinyint(1) DEFAULT '0',
                PRIMARY KEY (`u_id`),
                KEY `u_email` (`u_email`),
                KEY `u_password` (`u_password`),
                KEY `u_status` (`u_status`),
                KEY `u_group` (`u_groupid`),
                KEY `u_mobile_number` (`u_mobile_number`),
                KEY `u_is_verified` (`u_is_verified`),
                KEY `u_gender` (`u_gender`)
            ) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
        ");

        $this->execute("
            INSERT INTO `fly_user` (`u_id`, `u_screen_name`, `u_full_name`, `u_email`, `u_address`, `u_password`, `u_groupid`, `u_avatar`, `u_gender`, `u_status`, `u_oauth_provider`, `u_oauth_uid`, `u_oauth_access_token`, `u_onesignal_id`, `u_state`, `u_dob`, `u_date_created`, `u_date_last_change_password`, `u_date_modified`, `u_mobile_number`, `u_is_verified`, `u_verify_type`)
            VALUES
            (1, 'Administrator', 'Duy Nguyễn', 'admin@localhost.local', '', '\$2y\$10\$YXlpU3JCZENrSER5OCsyVO8C3pKbA3Dr.2NwjhHD/EL/lfwDMzGv6', 'administrator', '', 'male', 1, '', '', '', '', 0, NULL, 1494560696, 1522141389, 1522141389, '', 1, 1);
        ");
    }
}
