<?php
/************************************************************
** @Description: 后台首页
** @Author: haodaquan
** @Date:   2016-05-27 13:52:48
** @Last Modified by:   haodaquan
** @Last Modified time: 2016-11-15 13:53:58
*************************************************************/

class Home extends MY_Controller 
{

	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * [index 登录首页]
	 * @Date   2016-05-27
	 * @return [type]     [登录页面]
	 */
	public function index()
	{
        if (isset($_SESSION['user']) && $_SESSION['user']) {
            $url = "http://".$_SERVER['HTTP_HOST']."/public/home/index";
            header("Location:".$url);
        }
        
		$this->load->view('public/login.html',[]);
	}
	
}
