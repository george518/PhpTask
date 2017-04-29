<?php
/************************************************************
** @Description: 任务类型
** @Author: haodaquan
** @Date:   2016-12-22 16:03:57
** @Last Modified by:   haodaquan
** @Last Modified time: 2016-12-22 16:05:24
*************************************************************/
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Resource_model extends MY_Model
{
	protected $_table;
	/**
	 * [__construct 初始化方法]
	 */
	public function __construct()
	{
		parent::__construct();
		$this->_table = 'pub_resource';
	}

	/**
	 * [get_category 获取所有服务器配置，并且格式化输出]
	 * @return [type] [description]
	 */
	public function get_resource()
	{
        $result_data = $this->getConditionData('*','status=0');
        $data = [];
        foreach ($result_data as $key => $value) {
        	$data[$value['id']] = $value;
        }
        return $data;
	}
}