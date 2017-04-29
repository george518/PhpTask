<?php
/************************************************************
** @Description: 文件下载类
** @Author: haodaquan
** @Date:   2016-11-29 13:52:48
** @Last Modified by:   haodaquan
** @Last Modified time: 2016-11-29 13:39:26
*************************************************************/

class Download extends MY_Controller
{
    static public $allow_file = ['log','excel','download','command'];

    /**
     * [index 下载商品]
     * @return [type] [description]
     */
	public function index()
	{
		$file_name  = $this->input->get('name');
		$file_type  = $this->input->get('type');
		$file_model = $this->input->get('model');

		if(!in_array($file_type,self::$allow_file)) exit('类型错误!');
		$this->load->helper('download');
		$upload_path = rtrim(UPLOAD_PATH,'/');
		$file_path = $upload_path.'/'.$file_model.'/'.$file_type.'/'.$file_name;
		force_download($file_path,NULL);
	}

}