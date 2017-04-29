<?php
/************************************************************
** @Description: 任务调度
** @Author: haodaquan
** @Date:   2016-12-20 15:26:59
** @Last Modified by:   haodaquan
** @Last Modified time: 2016-12-20 15:26:59
*************************************************************/

class Command extends MY_Controller
{
    
    #设置标题，非必设置,不设置需要查询权限节点名称
    public $pageTitle   = '任务命令';
    public $pageTips    = '注意：非技术人员请勿修改';
    public $user_info;
	public function __construct()
	{
		parent::__construct();
		$this->user_info = $_SESSION['user'];
	}

	public function index()
	{
		$data['pageTitle'] = $this->pageTitle;
        $data['pageTips']  = $this->pageTips;

        $stock_file = [];
        $tree = tree(UPLOAD_PATH.'task/command/');
        $data['command'] = [];

        if($tree!==false)
        {
            krsort($tree);
            $count = count($tree);
            // if($count>10) $tree = array_slice($tree,0,10);
            foreach ($tree as $key => $value) {
				if(strpos($value,'.sh')!==false) $data['command'][] = $value;            
            }
        }
        $this->display('task/command.html',$data);
	}

	/**
	 * [save_command 保存命令]
	 * @return [type] [description]
	 */
	public function save_command()
	{
		$title   = $this->input->post('title');
		$command = $this->input->post('command');

		#生成shell文件
		$file_path = UPLOAD_PATH.'task/command';
		make_dir($file_path);
		$file_name = $file_path.'/'.$title;
		$res = file_put_contents($file_name,$command);
		chmod($file_name,0777);
		$res ? $this->ajaxReturn($res) : $this->ajaxReturn($res,300,'操作失败');
	}

	/**
	 * [delete 删除文件]
	 * @return [type] [description]
	 */
	public function delete()
	{
		$file_name  = $this->input->post('filename');
		$file_path = UPLOAD_PATH.'task/command/';
		if(!is_file($file_path.$file_name)) $this->ajaxReturn([],300,'文件不存在');
		
		$res = unlink($file_path.$file_name);
		$res ? $this->ajaxReturn($res) : $this->ajaxReturn($res,300,'操作失败');
	}

    
}