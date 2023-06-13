<?php

namespace lion9966\Permission\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Db;

class Install extends Command
{
    protected function configure()
    {
        $this->setName('permission:install')->setDescription('install permission table');
    }

    protected function execute(Input $input, Output $output)
    {
        $table = $this->hasTable();
        if ($table) {
            $output->writeln("Table " . $table . ' already exists, please do not create a duplicate.');
        } else {
            $newTable = $this->createTable();
            if ($newTable) {
                $output->writeln("Has been good for you to create tables: " . $newTable);
            } else {
                $output->writeln("System cannot automatically create tables, please try again or created manually");
            }
        }

    }

    public function hasTable()
    {
        $dataType = config('database.default');
        $prefix   = config('database.connections.' . $dataType . '.prefix');
        $database = config('database.connections.' . $dataType . '.database');

        $sql  = "SHOW TABLES";
        $data = Db::query($sql);
        if (!empty($data)) {
            $data = array_column($data, "Tables_in_" . $database);
        }
        $needle  = ['permission', 'role', 'user', 'role_permission_access', 'user_role_access'];
        $hasBase = [];
        foreach ($needle as $value) {
            if (in_array($prefix . $value, $data)) {
                $hasBase[] = $value;
            }
        }
        if (!empty($hasBase)) {
            return implode(', ', $hasBase);
        } else {
            return false;
        }
    }

    /**
     * 权限规则表
     * @return string
     */
    protected function permission(): string
    {
        $str = "`id` int(11) NOT NULL AUTO_INCREMENT," . "\n";
        $str .= "`name` varchar(100) NOT NULL COMMENT '规则唯一标识'," . "\n";
        $str .= "`status` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '状态'," . "\n";
        $str .= "PRIMARY KEY (`id`)," . "\n";
        $str .= "UNIQUE KEY `name` (`name`)" . "\n";
        $str .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='权限规则表';";
        return $str;
    }

    /**
     * 角色表
     * @return string
     */
    protected function role(): string
    {
        $str = "`id` int(11) NOT NULL AUTO_INCREMENT," . "\n";
        $str .= "`name` varchar(100) NOT NULL COMMENT '角色唯一标识'," . "\n";
        $str .= "`status` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '状态'," . "\n";
        $str .= "PRIMARY KEY (`id`)," . "\n";
        $str .= "UNIQUE KEY `name` (`name`)" . "\n";
        $str .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='角色表';";
        return $str;
    }

    /**
     * 用户表
     * @return string
     */
    protected function user(): string
    {
        $str = "`id` int(11) NOT NULL AUTO_INCREMENT," . "\n";
        $str .= "`name` varchar(50) NOT NULL COMMENT '用户唯一标识'," . "\n";
        $str .= "`status` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '状态'," . "\n";
        $str .= "PRIMARY KEY (`id`)," . "\n";
        $str .= "UNIQUE KEY `name` (`name`)" . "\n";
        $str .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户表';";
        return $str;
    }

    /**
     * 权限规则与角色中间表
     * @return string
     */
    protected function rolePermissionAccess(): string
    {
        $str = "`role_id` int(11) NOT NULL COMMENT '角色主键'," . "\n";
        $str .= "`permission_id` int(11) NOT NULL COMMENT '规则主键'," . "\n";
        $str .= "PRIMARY KEY (`role_id`,`permission_id`)" . "\n";
        $str .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='权限规则与角色中间表';";
        return $str;
    }

    /**
     * 用户与角色中间表
     * @return string
     */
    protected function userRoleAccess(): string
    {
        $str = "`user_id` int(11) NOT NULL COMMENT '用户主键'," . "\n";
        $str .= "`role_id` int(11) NOT NULL COMMENT '角色主键'," . "\n";
        $str .= "PRIMARY KEY (`user_id`,`role_id`)" . "\n";
        $str .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户与角色中间表';";
        return $str;
    }

    protected function createString(string $table)
    {
        $dataType = config('database.default');
        $prefix   = config('database.connections.' . $dataType . '.prefix');
        $str      = "CREATE TABLE `" . $prefix . $table . "` (" . "\n";
        return $str;
    }

    public function createTable()
    {
        Db::query($this->createString("permission") . $this->permission());
        Db::query($this->createString("role") . $this->role());
        Db::query($this->createString("user") . $this->user());
        Db::query($this->createString("role_permission_access") . $this->rolePermissionAccess());
        Db::query($this->createString("user_role_access") . $this->userRoleAccess());
        return $this->hasTable();
    }

}
