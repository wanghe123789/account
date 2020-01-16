<?php
namespace Home\Controller;
use Think\Controller;
class FundsController extends BaseController {
    public function index(){
        $uid = session('uid');

        if (IS_POST) {
            $strFundsName = I('post.funds_name');
            $Updata = AddNewFunds($strFundsName, $uid);
            ClearDataCache(); //清除缓存
            ShowAlert($Updata[1]);
        }
        $FundsData = GetFundsData($uid);
        // dump($FundsData);
        $this -> assign('ShowData', $FundsData);
        $this -> display();
    }

    public function edit(){
        $uid = session('uid');
        $fundsid = I('get.id','',int);
        if ($fundsid > 0) {
            $DbFunds = GetFundsIdData($fundsid, $uid);
            if (IS_POST) {
                $fundsSubmit = I('post.funds_submit','');
                if ($fundsSubmit === '编辑') {
                    $fundsName = I('post.funds_name','');
                    $ret = EditFundsName($fundsid, $fundsName, $uid);
                    ShowAlert($ret[1],U('Home/Funds/index'));
                    $this -> display('Public/base');
                } elseif ($fundsSubmit === '删除') {
                    $fundsChange = I('post.funds_change','',int);
                    $ret = DeleteFunds($fundsid, $uid, $fundsChange);
                    ShowAlert($ret[1],U('Home/Funds/index'));
                    $this -> display('Public/base');
                } else {
                    $this -> error('非法操作...');
                }
            } else {
                if ($DbFunds[0]) {
                    $this -> assign('FundsId', $fundsid);
                    $this -> assign('FundsName', $DbFunds[1]['fundsname']);
                    $this -> assign('FundsData', GetFundsData($uid));
                    $this -> display();  
                } else {
                    $this -> error($DbData[1]);
                }
            }
        } else {
            $this -> error('非法操作...');
        }
    }
}