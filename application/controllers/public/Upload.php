<?php
/************************************************************
** @Description: 文件图片上传基础类
** @Author: haodaquan
** @Date:   2016-11-28 13:52:48
** @Last Modified by:   haodaquan
** @Last Modified time: 2016-11-28 13:39:26
*************************************************************/

class Upload extends MY_Controller
{
    public $file_path    = UPLOAD_PATH;
    public $allow_type   = ["jpg", "png", "gif",'xlsx'];//允许上传文件格式 
    public $allow_size   = 2097152;//上传文件大小 2M2*1024*1024

    /**
     * [file 文件上传]
     * @return [type] [description]
     */
    public function file()
    {
        $file = $this->input->get('file');
        $size = $this->input->get('size');

        if(!$file) $this->return_error('上传文件配置有误');
        if(!isset($_POST)) $this->return_error('上传有误！');

        $name     = $_FILES['file']['name']; 
        $size     = $_FILES['file']['size']; 
        $name_tmp = $_FILES['file']['tmp_name']; 
        if (empty($name)) $this->return_error('您还未选择文件');
        #判断文件类型
        $type = strtolower(substr(strrchr($name, '.'), 1)); //获取文件类型 
        if (!in_array($type, $this->allow_type)) $this->return_error('请上传正确类型的文件！');
        if ($size > $this->allow_type)  $this->return_error('文件大小已超过2M限制！');
        
        #上传地址 

        $path = $this->file_path.$file.'/'.date('Y-m-d',time()).'/';
        if(!is_dir($path)) make_dir($path);
        $file_name = time() . rand(10000, 99999) . "." . $type;//名称 
        $file_path = $path .  $file_name; //上传后路径+名称
        $return_file_path = $file.'/'.date('Y-m-d',time()).'/'. $file_name;//返回后的地址

        if (move_uploaded_file($name_tmp, $file_path)) 
        { 
            //临时文件转移到目标文件夹 
            echo json_encode(array("error"=>"0","path"=>$return_file_path,"name"=>$name)); 
            exit();
        } 
        $this->return_error('上传有误，请检查服务器配置！');
        
    }

    /**
     * [return_error 返回错误]
     * @param  [type] $msg [错误信息]
     * @return [type]      [description]
     */
    public function return_error($msg)
    {
    	echo json_encode(["error"=>$msg]);
    	exit();
    }

    
}
