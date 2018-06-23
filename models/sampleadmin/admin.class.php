<?php
namespace MODELS\ADMIN;
use CORE\MVC\Model;
use CORE\MVC\IModel;
use CORE\Session;
class Admin extends Model implements IModel
{
    protected static $table="admin";
    private static $keyName = "admin_id_ds89";
    protected static $model = [
        'Id'=>['Key'=>true,'Type'=>'Int','Required'=>true, 'ColumnName'=>'id'],
        'Name'=>['Max'=>100,'Min'=>3,'Type'=>'Name','Required'=>true,'ColumnName'=>'name','Display'=>'Name'],
        'Email'=>['Type'=>'Email','Required'=>true, 'ColumnName'=>'email','Display'=>'Email Name'],
        'Date'=>['Type'=>'DateTime','Required'=>false, 'AutoGenerated'=>true,'ColumnName'=>'date'],
        'Password'=>['Type'=>'String','Required'=>true,'Min'=>3,'Max'=>40,'ColumnName'=>'password'],
        'Salt'=>['Type'=>'String','Required'=>false, 'ColumnName'=>'salt'],
        'Status'=>['Type'=>'Enum','Required'=>false, 'ColumnName'=>'status', 'Enum' => '\MODELS\ADMIN\Status', 'Default' => Status::Active],
        'Role'=>['Type'=>'Model','Required'=>true,'ColumnName'=>'role_id','ForeignKey' => '\MODELS\ADMIN\Role:Id'],
    ];
    public static function isLogin() : bool
    {
        return \CORE\Session::exists(self::$keyName);
    }
    public static function loginNow(Admin $admin) : void
    {
        \CORE\Session::set(self::$keyName, $admin->getId());
    }

    // public static function loginNow(self $admin) : void
    // {
        // \CORE\Session::set(self::$keyName, $admin->getId());
    // }
    public static function getCurrent() : Admin
    {
        return Admin::init(\CORE\Session::get(self::$keyName));
    }
    public static function logout() : void
    {
        \CORE\Session::remove(self::$keyName);
    }
    public static function createPassword(self $adminInstance, string $password) : void
    {
        $salt = \CORE\Hash::getSalt();
        $password = \CORE\Hash::generatePasswordHash($password, $salt);
        $adminInstance->setPassword($password);
        $adminInstance->setSalt($salt);
    }
    public static function validatePassword(self &$adminInstance, string $password) : bool
    {
        return \CORE\Hash::verifyPassword($password, $adminInstance->getSalt(), $adminInstance->getPassword());
    }
    public function hasRights(string $controller, string $action)
    {
        if(in_array($controller."/".$action, $this->getRole()->getPermissions()))
            return true;
        else
            return false;
    }
    public static function createTable()
    {
        \CORE\DB\DB::query("CREATE TABLE `admin` (
          `name` varchar(100) DEFAULT NULL,
          `email` varchar(70) DEFAULT NULL UNIQUE KEY,
          `role_id` tinyint(4) DEFAULT NULL,
          `password` varchar(70) DEFAULT NULL,
          `salt` varchar(64) NOT NULL,
          `date` datetime DEFAULT CURRENT_TIMESTAMP,
          `status` tinyint(1) DEFAULT NULL,
          `id` int(4) NOT NULL auto_increment Key
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1;
        ");
        \CORE\DB\DB::query("INSERT INTO `admin` (`name`, `email`, `role_id`, `password`, `salt`, `date`, `status`, `id`) VALUES
        ('Adam', 'admin@admin.com', 1, '$2y$14\$gKti7jM6sacLncnkamQpz.D6C2ufln6uJatzGDpLTJ1Omv.BhVgDi', 'd1fd2c898665ab3e39ec0c6bdba85a0447c3f55f91371863fe01f9ffe7e25227', '2017-09-27 01:27:49', 1, 1);
        ");
    }
}
/*

Model Options

Key => @bool | true, false #primary key
ForeignKey => \MODELS\USER\User::Id #foreign key #classname::columnName

Required => @bool true | false
Max => @int #Max Length if type string #Max Number if type Number|Int
Min => @int #Min Length if type string #Min Number if type Number|Int
Regexp => @string #regural expression

Type => @string | Int, Number, String, Array, Name, Email, Model, Enum, DateTime @data type @default String
ColumnName => @string #Database Column Name
AutoGenerated => @bool | true, false #database auto generated, like current_timestamp
Display => @string #to display column name on frontend to users
*/