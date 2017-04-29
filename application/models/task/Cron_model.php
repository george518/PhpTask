<?php
/************************************************************
** @Description: 小红书更新库存黑名单
** @Author: haodaquan
** @Date:   2016-12-22 16:03:57
** @Last Modified by:   haodaquan
** @Last Modified time: 2016-12-22 16:05:24
*************************************************************/
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cron_model extends MY_Model
{
	protected $_table;
	/**
	 * [__construct 初始化方法]
	 */
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'pub_task';
	}
}