<?php
/************************************************************
** @Description: 任务调度
** @Author: haodaquan
** @Date:   2016-12-20 15:26:59
** @Last Modified by:   cuixiaona
** @Last Modified time: 2017-04-27 13:27:23
*************************************************************/

class Cron extends MY_Controller
{
    static public $status = ['删除','运行中','已停止'];
    static public $type   = ['未知','定时任务','常驻任务'];
    // static public $style  = ['未知','小红书','寺库','天猫','小红书海外店'];
    static public $buttonColor = ['label-default','label-success','label-danger'];
    public $data = [];
    public $style;#任务分类
    public $resource;#服务器资源
    //列表字段，必须设置
    public $showFields = array(
    							// 'id'        => 'ID',
                                'resource_id'=> '资源类型',
                                'style'     => '任务分类',
                                'title'     => '任务名称',
                                // 'timed'     => "时间",
                                // 'command'   => '命令内容',
                                'type'      => '运行类型',
                                'detail'    => '任务说明',
                                'status'    => '状态',
                                'edit_time' => '修改时间',
                                'action'    => '操作'
                            );
    public $columnsWidth = array(
                                'title'     => 150,
                                'add_time'  => 150,                     
                            );
    #设置标题，非必设置,不设置需要查询权限节点名称
    public $pageTitle = '任务调度列表';
    #设置模型名称 ,用于 $this->model 必须设置，并且在本文件中加载该模型 $this->load->model('xiaohongshu/product_model');
    public $modelName   = 'cron_model';
    public $searchFile  = 'task/cron_search.html';#搜索文件
    public $pageTips    = '注意：修改或新增后必须更新任务才能生效';
    public $checkCol    = 0;
    public $user_info   = [];

	public function __construct()
	{
		parent::__construct();
        $this->load->model('task/cron_model');
        $this->user_info = $_SESSION['user'];


        $this->load->model('task/category_model');
        $this->style = $this->category_model->get_category();
        $this->style[0] = '未知分类';

        $this->load->model('task/resource_model');
        $this->resource  = $this->resource_model->get_resource();

    }

    public function index()
    {
        $this->data['style']        = $this->style;
        $this->data['resource']     = $this->resource;   
        parent::index();
    }

    /**
     * [query 查询配置 这里继承父类方法，也可以这里配置查询条件]
     * @Author haodaquan
     * @Date   2016-08-07
     * @return [type]     [description]
     */
    public function query()
    {
        $_POST['status|<>']=0;
        if(!isset($_POST['sort'])) $_POST['sort'] = ' style.asc';
        parent::query();
    }

    /**
     * [listDataFormat 对数据进行格式化]
     * @param  [type] $listData [description]
     * @return [type]           [description]
     */
    public function listDataFormat($listData)
    {
        $buttons = array(
                'delete' => '删除',
                'edit'   => '编辑',
            );
        $data['totalCount'] = $listData['totalCount'];
        foreach ($listData['items'] as $key => $value) {
            $value['edit_time'] = date('Y-m-d H:i:s',$value['edit_time']);
            $_buttons = $buttons;
            if($value['status']==2)  $_buttons['changeStatus'] = '启动';
            if($value['status']==1)  $_buttons['disable'] = '停止';
            $value['action'] = getButton($value['id'],$_buttons);
            $value['status'] = '<span class=" label '.self::$buttonColor[$value['status']].'">'.self::$status[$value['status']].'</span>';
            // self::$status[$value['status']];
            $value['type']   = self::$type[$value['type']];
            $value['style']  = $this->style[$value['style']];
            // $value['resource_id']  = $this->resource[$value['resource_id']]['resource_name'];
            $data['items'][$key] = $value;
        }
        return $data;
    }

    /**
     * [delete 逻辑删除]
     * @return [type] [ajax]
     */
    public function delete()
    {
        $id = $this->input->post('id');
        if(!$id) $this->ajaxReturn($id,300,'数据错误');
        $data['id'] = $id;
        $data['status'] = 0;
        $res = $this->cron_model->editData($data,'id='.(int)$id);
        ($res!=-1 && $res!=false) ? $this->ajaxReturn($res) : $this->ajaxReturn($res,300,'操作失败');
    }

    /**
     * [changeStatus 启动]
     * @return [type] [ajax]
     */
    public function changeStatus()
    {
        $id = $this->input->post('id');
        if(!$id) $this->ajaxReturn($id,300,'数据错误');
        $data['id'] = $id;
        $data['status'] = 1;
        $res = $this->cron_model->editData($data,'id='.(int)$id);
        ($res!=-1 && $res!=false) ? $this->ajaxReturn($res) : $this->ajaxReturn($res,300,'操作失败');
    }

    /**
     * [disable 停止]
     * @return [type] [description]
     */
    public function disable()
    {
        $id = $this->input->post('id');
        if(!$id) $this->ajaxReturn($id,300,'数据错误');
        $data['id'] = $id;
        $data['status'] = 2;
        $res = $this->cron_model->editData($data,'id='.(int)$id);

        #如果是常驻进程，当场就玩真的！
        $process = $this->cron_model->getConditionData('*','id='.(int)$id);

        if($process[0]['type']==2)
        {
            $resource_ids = explode(',',$process[0]['resource_id']);

            $process_tag = $process[0]['process_tag'].PHP_EOL;
            $file_path = UPLOAD_PATH.'task';
            foreach ($resource_ids as $key => $value) {
                $file_name = '/stop_'.$value;
                $file_result = file_put_contents($file_path.$file_name,$process_tag,FILE_APPEND);
            }
           
        }
        
        ($res!=-1 && $res!=false) ? $this->ajaxReturn($res) : $this->ajaxReturn($res,300,'操作失败');
    }


    /**
     * [save_cron 保存定时任务]
     * @return [type] [description]
     */
	public function save_cron()
	{
		$form_data = format_ajax_data($this->input->post('data'));

        foreach ($form_data as $key => $value) {
            if(!$value) $this->ajaxReturn($key.'='.$value,300,'请填写完整');
            if(strpos($key,'resource')!==false){
                $form_data['resource_id'][] = $value;
                unset($form_data[$key]);
            }
        }
        if(!isset($form_data['resource_id']) || empty($form_data['resource_id'])) $this->ajaxReturn([],300,'请选择资源类型');
        $form_data['resource_id'] = implode(',',$form_data['resource_id']);

        #TODO 检测 这里先不检测了，自己用
        if(isset($form_data['id']))
        {
            $result = $this->cron_model->editData($form_data,'id='.(int)$form_data['id']);
        }else
        {
            $result = $this->cron_model->addData($form_data);
        }
        
        if ($result) {
            $this->ajaxReturn($result);
        }else
        {
            $this->ajaxReturn($result,300,'保存失败');
        }
	}

    /**
     * [refresh 更新任务]
     * @return [type] [description]
     */
    public function refresh()
    {
        #查询所有任务
        $task = $this->cron_model->getConditionData('*','status=1',' style ASC');
        #生成cron文件
        $file_path = UPLOAD_PATH.'task';
        make_dir($file_path);

        foreach ($this->resource as $k => $r) {
            $timed_task[$k] = '';
        }

        foreach ($task as $key => $value) {
            $resource_ids = explode(',',$value['resource_id']);
            foreach ($resource_ids as $r_id) {
                $timed_task[$r_id] .= '#'.$this->style[$value['style']].' '.$value['title'].PHP_EOL;
                $command = $file_path.'/command/'.$value['command'];
                $timed_task[$r_id] .= $value['timed'] . ' ' .$command.PHP_EOL;
            }
        }

        foreach ($this->resource as $k => $r) {
            $file_name = '/cron_'.$k;
            $task  = PHP_EOL."##<TASK START> FOR PIPI TASK ##".PHP_EOL;
            $task .= "*/1 * * * *  ". $file_path."/cron.sh".PHP_EOL;
            $task .= $timed_task[$k];
            $task .= "##<TASK END> FOR PIPI TASK ##".PHP_EOL;
            $res[] = file_put_contents($file_path.$file_name,$task);
        }
        
        if(!$res) $this->ajaxReturn($res,300,'更新错误');#判断不严谨
        $this->ajaxReturn($res);
    }


    /**
     * [get_detail 根据ID获取配置信息]
     * @return [type] [description]
     */
    public  function get_detail()
    {
        $id = $this->input->post('id');
        $data = $this->cron_model->getConditionData('*','id='.(int)$id);
        $this->ajaxReturn($data);
    }
}