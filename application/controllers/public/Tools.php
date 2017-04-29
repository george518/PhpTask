<?php
/************************************************************
** @Description: 公共工具
** @Author: haodaquan
** @Date:   2016-08-26 15:23:56
** @Last Modified by:   haodaquan
** @Last Modified time: 2016-08-26 15:59:34
*************************************************************/

class Tools extends MY_Controller 
{

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * [yintai 银泰工具查询]
	 * @return [type] [description]
	 */
	public function yintai()
	{
		$data['pageTitle'] = '商品信息查询';
    	$this->display('public/yintai_tools.html',$data);
	}

	public function yintai_search()
	{
		$sku_code = $this->input->post('sku_code');
		$type = $this->input->post('type');#1-库存2-价格
        $this->load->model('public/stock_model');
        $info = '';
        if($type==1)
        {
        	$res = $this->stock_model->get_yintai_stock($sku_code);
        	$info .= '库存数量:'.$res[0]['AvailableQuantity'];
        }elseif ($type==2) {
        	$res = $this->stock_model->get_yintai_price($sku_code);
        	$info .= '最低价：'.$res[0]['CeilPrice'].PHP_EOL.'渠道价：'.$res[0]['Price'];
        }else
        {
        	$info = '暂无信息'; 
        }
        $this->ajaxReturn($info,200);
	}


	/**
	 * [test description]
	 * @return [type] [description]
	 */
	public function test()
	{
		$this->load->model('public/product_model');
		$param = ['ids'=>'113,114,115','channel'=>'red'];
		$data = $this->product_model->get_cloud_product($param);
		if($data['status']!==200) return false;
		$this->load->model('red/product_storage_model');
		$this->load->model('red/product_storage_item_model');
		foreach ($data['data'] as $key => $value) {
			$item = $value['items'];
			unset($value['items']);
			#spu
			$spu = $value;
			$spu['spu_cloud_id'] = $spu['id'];
			unset($spu['id']);
			$spu_id = $this->product_storage_model->addData($spu);
			if(!$spu_id) continue; 
			#sku
			foreach ($item as $k => $v) {
				$sku = [];
				$sku = $v;
				$sku['spu_cloud_id'] = $spu_id;
				$sku['sku_cloud_id'] = $v['id'];
				unset($sku['id']);
				$sku['pics'] = implode(',',$sku['pics']);
				$sku_id[] = $this->product_storage_item_model->addData($sku);
			}
		}
	}

	/**
	 * [tax_freight 计算税费]
	 * @return [type] [description]
	 */
	public function tax_freight()
	{
		$data['pageTitle'] = '计算税额运费';
    	$this->display('public/tax_freight.html',$data);
	}

	/**
	 * [ajax_tax_freight 处理运费和税额查询]
	 * @return [type] [description]
	 */
	public function  ajax_tax_freight()
	{
		$form_data = format_ajax_data($this->input->post('data'));

		foreach ($form_data as $key => $value) {
			if(!$value) $this->ajaxReturn($form_data,300,'请填写完整数据');
		}

    	$this->load->model('red/tax_freight_model');
    	$config = $this->tax_freight_model->get_config();

    	if((int)((float)$form_data['order_pay']*100) > (int)((float)($config['min_declare_price']*100)))
    	{

    		$type = '高值订单';
    		#高值订单
    		$tf = $this->tax_freight_model->height_tf($form_data['sku_id'],$form_data['qty']);
    		
    	}else
    	{
    		$type = '低值订单';
    		#低值订单
    		$tf = $this->tax_freight_model->low_tf($form_data['sku_id'],$form_data['qty'],$form_data['pay_price']);
    	}

    	$tf_detail = json_decode($tf['tf_detail'],true);

    	if(!$tf_detail) $this->ajaxReturn($tf_detail,300,'请检查sku_id是否存在或者其他数据是否完整');
    	#组成字符串
    	$str  = '类型：'.$type.'<br />';
    	$str .= '运费：'.$tf['freight'].'<br />';
    	$str .= '税额：'.$tf['tax'].'<br />';
    	$str .= '首重：'.$tf_detail['first_weight'].'<br />';
    	$str .= '首重价格：'.$tf_detail['first_price'].'<br />';
    	$str .= '续重：'.$tf_detail['add_weight'].'<br />';
    	$str .= '续重价格：'.$tf_detail['add_price'].'<br />';
    	$str .= '总重：'.$tf_detail['total_weight'].'<br />';
    	$str .= isset($tf_detail['tax_rate']) ? '税率：'.$tf_detail['tax_rate'].'<br />' :'';
    	$str .= isset($tf_detail['cost_price']) ? '成本价：'.$tf_detail['cost_price'].'<br />' :'';
    	$str .= isset($tf_detail['sale_price']) ? '购买价：'.$tf_detail['sale_price'].'<br />' :'';
    	$this->ajaxReturn($str);
	}




}