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
	public $user;
	function __construct()
	{
		parent::__construct();
		$this->user = $this->user_info();
	}
	
	/**
	 * [index 登录首页]
	 * @Date   2016-05-27
	 * @return [type]     [登录页面]
	 */
	public function index()
	{
		$this->load->view('public/main.html',$this->user);
	}

	/**
	 * [start 起始页]
	 * @Date   2016-09-07
	 * @return [type]     [description]
	 */
	public function start()
	{
		$this->load->view('public/start.html',$this->user);
	}

	/**
	 * [user_info 获取用户信息]
	 * @return [type] [description]
	 */
	public function user_info()
	{
		$data = [];
		if(isset($_SESSION['user']) && $_SESSION['user']){
			$data = $_SESSION['user'];
		}else
		{
			$this->load->view('public/login.html',[]);
		}

		return $data;
	}

	/**
     * [password 修改密码]
     * @return [type] [description]
     */
    public function password()
    {
        $data['pageTitle'] = '修改密码';
        $data['user'] = $this->user;

        $this->display('public/user.html',$data);
    }


    /**
     * [edit_user 修改用户及密码]
     * @return [type] [description]
     */
    public function edit_user()
    {
        $data = format_ajax_data($this->input->post('data'));
     
        foreach ($data as $key => $value) {  
            if(!$value) $this->ajaxReturn($value,300,'请填写数据完整');
           
        }

        $user_info_ = $this->user_model->getConditionData('*','username="'.$data['username'].'"');
        $user_info = $user_info_[0];

        if(md5($data['password'])!==$user_info['password']){
        	dump(md5($data['password']));
        	dump($user_info['password']);
            $this->ajaxReturn($user_info['username'],300,'请输入正确的原密码');
        }

        //密码要6位-32位
        if(strlen($data['password1'])<6 || strlen($data['password1'])>20)
        {
           $this->ajaxReturn($user_info['username'],300,'密码位数不对，请重新输入');
        }

        if(trim($data['password1'])!==trim($data['password2']))
        {
            $this->ajaxReturn($user_info['username'],300,'两次密码不一致，请重新输入');
        }

        $data['password'] = md5($data['password1']);
        unset($data['password1']);
        unset($data['password2']);

         $res = $this->user_model->saveData($data,' uid='.(int)$user_info['uid']);
        if (!$res) {
            $this->ajaxReturn($res,300,'保存错误');
        }else
        {
            $this->ajaxReturn($res,200,'修改成功，请退出重新登录');
        }
    }

	
}