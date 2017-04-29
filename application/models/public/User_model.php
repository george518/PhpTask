<?php
/************************************************************
** @Description: 渠道用户管理
** @Author: haodaquan
** @Date:   2016-12-22 16:03:57
** @Last Modified by:   haodaquan
** @Last Modified time: 2016-12-22 16:05:24
*************************************************************/
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends MY_Model
{
	protected $_table;
	/**
	 * [__construct 初始化方法]
	 */
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'pub_member';
	}

	/**
     * [check_user 检查登录]
     * @Date   2016-06-03
     * @return [type]     [description]
     */
    function check_user()
    {
        if (isset($_SESSION['user']) && $_SESSION['user']) {
            $url = "http://".$_SERVER['HTTP_HOST']."/public/login";
            header("Location:".$url);
        }
        return $_SESSION['user'];
    }
}