<?php
/************************************************************
** @Description: 登录处理
** @Author: haodaquan
** @Date:   2016-05-27 13:52:48
** @Last Modified by:   haodaquan
** @Last Modified time: 2016-11-15 13:39:26
*************************************************************/

class Login extends MY_Controller 
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

        $user_info = $_SESSION['user'];
        
        if ($user_info) {
            $url = "http://".$_SERVER['HTTP_HOST']."/home/main";
        }else
        {
            $url = "http://".$_SERVER['HTTP_HOST'];
        }

        header("Location:".$url);
	}

	/**
	 * [do_login 登录处理]
	 * @Date   2016-06-03
	 * @return [type]     [description]
	 */
	public function do_login()
	{
        $username = $this->input->post("username");
        $password = $this->input->post("password");
        $code = $this->input->post("code");

        //用户名只能是字母+数字，4位-20位
        if(!preg_match("/^[a-zA-Z0-9][a-zA-Z0-9]{3,19}$/", $username))
        {
            echo -1;
            return;
        }

        //密码要6位-32位
        if(strlen($password)<6 || strlen($password)>32)
        {
            echo -1;
            return;
        }

        $username = htmlentities($username);
        $user = $this->user_model->getConditionData('uid,password,username,realname','username="'.$username.'"');        
        if(!$user[0] || !$user[0]["password"])
        {
            echo -1;
            return;
        }
        
        if($user[0]["password"] != md5($password))
        {
            echo -1;
            return;
        }

        unset($user[0]['password']);
        $_SESSION['user'] = $user[0];
        echo 1;
    }

    /**
     * [do_login_out 退出登录]
     * @Date   2016-06-03
     * @return [type]     [description]
     */
    public function do_login_out()
    {
        $_SESSION['user'] = NULL;
        $url = "http://".$_SERVER['HTTP_HOST']."/";
        header("Location:".$url);
        exit(); 
    }



}