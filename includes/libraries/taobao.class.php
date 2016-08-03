<?php
class CSV
{
	var $title=array();
	var $result=array();
	function CSV($file)
	{
		setlocale(LC_ALL,array('zh_CN.gbk','zh_CN.gb2312','zh_CN.gb18030'));
		$result=array();
		if(($handle = fopen($file, "r")) !==FALSE)
		{	
			$i=0;
			while(($data = fgetcsv($handle, 0,",")) !==FALSE)
			{
				foreach($data as $k=>$v)
				{
					$data[$k]=iconv('gbk','utf-8',trim($v));
				}				
				$result[]=$data;
			}
			fclose($handle);			
		}
		$this->result=$result;
	}
	private function _fields()
	{
		//key 为返回数组的key，value为csv文件中的头	
		return array(
            'goods_name'  => '宝贝名称',
            'cid'         => '宝贝类目',
            'price'       => '宝贝价格',
            'stock'       => '宝贝数量',
            'if_show'     => '放入仓库',
            'recommended' => '橱窗推荐',
            'description' => '宝贝描述',
            'goods_image' => '新图片',
            'sale_attr'   => '销售属性组合',
            'sale_attr_alias' => '销售属性别名'
        );
		
	}
	function filter($fields=array())
	{
		$array=array();
		if(empty($fields))	$fields=$this->_fields();
		foreach($this->result as $key=>$row)
		{
			if(count($row)<count($fields))	continue;
			/*if(array_key_exists($row[0],$fields) && array_key_exists($row[1],$fields))
			{
				unset($this->result[$key]);
				continue;
			}*/
			//前两个列的内容和列名一样的为表头
			if(array_search($row[0],$fields)!=false && array_search($row[1],$fields)!=false)
			{				
				$this->title=$row;
				$array=array();
				continue;
			}
			$array[]=$row;
		}
		$this->result=$array;

		$cols=$this->_fields_cols($this->title,$fields);

		$array=array();
		$index=0;
		foreach($this->result as $row)
		{
			foreach($cols as $key=>$pos)
			{
				$array[$index][$key]=$row[$pos];				
			}
			$index++;
		}
		$this->result=$array;
		return $this->result;
	}
	
	/* 每个字段所在CSV中的列序号，从0开始算 */
    private function _fields_cols($title_arr, $import_fields)
    {
        $fields_cols = array();
        foreach ($import_fields as $k => $field)
        {
            $pos = array_search($field, $title_arr);
            if ($pos !== false)
            {
                $fields_cols[$k] = $pos;
            }
        }
        return $fields_cols;
    }
}

/*$csv=new CSV('ss.csv');
$csv->filter();
print_r($csv->result);*/