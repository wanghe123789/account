<?php
namespace Home\Controller;
use Think\Controller;
class FindController extends BaseController {
    public function index(){
        $uid = session('uid');
        $ShowFind = 1;
        if(IS_POST){
            $ClassValue = I('post.find_class');
            if($ClassValue === 'all'){
                //$data['acclassid'] = null;
                //$data['zhifu']     = null;
            }else if($ClassValue === 'outClass'){
                //$data['acclassid'] = null;
                $data['zhifu']     = '2';
            }else if($ClassValue === 'inClass'){
                //$data['acclassid'] = null;
                $data['zhifu']     = '1';
            }else{
                $data['acclassid'] = $ClassValue;
                //$data['zhifu']     = null;
            }
            $data['starttime'] = I('post.find_start_time');
            $data['endtime']   = I('post.find_end_time');
            $data['acremark']  = I('post.find_mark');
            $data['fid']       = I('post.find_funds');
            $data['jiid']      = $uid;
            
            //更新缓存
            ClearFindCache(); //清除查询缓存
            S('find_data_'.$uid,$data);
            S('find_data_class_'.$uid,$ClassValue);

        }else{
            //读取查询缓存
            $data = S('find_data_'.$uid);
            $ClassValue = S('find_data_class_'.$uid);
        }
        
        if($data) {
            //不显示搜索
            $ShowFind = 0;
            
            //设置返回页
            SetRefURL(__ACTION__);
            
            //输出查询信息
            $this -> assign('FindData',$data);
            $this -> assign('FindDataClass',$ClassValue);
            
            //获取指定页数据
            $DbAccount = FindAccountData($data);
            $this -> assign('SumInMoney', $DbAccount['SumInMoney']);
            $this -> assign('SumOutMoney', $DbAccount['SumOutMoney']);
            
            //获取资金账户数据
            $DbFunds = array();
            $FundsData = GetFundsData($uid);
            foreach ($FundsData as $key => $data) {
                $DbFunds[$data[id]] = $data[name];
            }

            //获取分类列表
            $DbClass = GetClassData($uid);
            
            //整合List表格数组
            $ListData = OutListData($DbAccount,$DbClass,$DbFunds);
            $this -> assign('Page', $ListData[0]);
            $this -> assign('PageMax', $ListData[1]);
            $this -> assign('ArrPage', $ListData[2]);
            $this -> assign('ShowData', $ListData[3]);
        }
        
        $this -> assign('inClassData',GetClassData($uid,1));
        $this -> assign('outClassData',GetClassData($uid,2));
        $this -> assign('FundsData',GetFundsData($uid));
        $this -> assign('ShowFind',$ShowFind);
        $this -> display();
    }
    
    public function reboot() {
        ClearFindCache(); //清除查询缓存
        $this -> redirect('Home/Find/index');
    }
}