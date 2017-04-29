<?php
/************************************************************
** @Description: 服务器资源分配
** @Author: haodaquan
** @Date:   2016-12-19 09:26:47
** @Last Modified by:   haodaquan
** @Last Modified time: 2016-12-19 17:04:17
*************************************************************/
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Resource extends MY_Controller
{
    public $data = [];
    //列表字段，必须设置
    public $showFields = array(
                                'id'     			 => '资源ID',
                                'resource_name'      => '资源名称',
                                'resource_tag'       => '资源标志',
                                'ip'      			 => 'ip地址',
                                'action'             => '操作'
                            );
    public $columnsWidth = array(
                                'action'        => 150,
                            );
    public $pageTitle   = '资源配置';
    public $modelName   = 'resource_model';
    public $searchFile  = 'task/resource_search.html';#搜索文件
    public $pageTips    = '添加服务器资源，注意部署执行器';
    public $checkCol    = 0;

    // private $queue_stock_name;#库存更新
    public function __construct()
    {
        parent::__construct();
        $this->load->model('task/resource_model');
    }

	/**
     * [index 上架商品管理]
     * @Date   2016-10-09
     * @return [type]     [description]
     */
    public function index()
    {
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
    	$_POST['status|=']=0;
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
                'edit'	 => '编辑'
            );
        $data['totalCount'] = $listData['totalCount'];
        foreach ($listData['items'] as $key => $value) {
        	$value['action'] = getButton($value['id'],$buttons);
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
    	$data['status'] = 1;
    	$res = $this->resource_model->editData($data,'id='.(int)$id);
    	($res!=-1 && $res!=false) ? $this->ajaxReturn($res) : $this->ajaxReturn($res,300,'操作失败');
    }

  
    /**
     * [save_resource 保存任务分类]
     * @return [type] [description]
     */
    public  function save_resource()
    {
        $form_data = format_ajax_data($this->input->post('form_data'));
        if(!$form_data['resource_tag']) $this->ajaxReturn([],300,'数据不完整');

        $res_data = $this->resource_model->getConditionData('id','resource_tag="'.$form_data['resource_tag'].'" and status=0');
        if($res_data) $this->ajaxReturn($res_data[0]['id'],300,'资源标志已经存在！');
        $id = $form_data['resource_tag'];
        if (isset($form_data['id'])) {
        	$id = $form_data['id'];
        	$res = $this->resource_model->editData($form_data,'id="'.(int)$id.'"');
        }else
        {
        	$res = $this->resource_model->addData($form_data);
        }
        $res ? $this->ajaxReturn($res) : $this->ajaxReturn($res,300,'保存失败');
    }


     /**
     * [get_detail 获取单个分类]
     * @return [type] [description]
     */
    public function get_detail()
    {
    	$id = $this->input->post('id');
    	if (!$id) $this->ajaxReturn([],300,'参数错误');
    	
    	$data = $this->resource_model->getConditionData('*','id="'.(int)$id.'"');
    	$data ? $this->ajaxReturn($data) : $this->ajaxReturn($data,300,'数据不存在');
    }

}