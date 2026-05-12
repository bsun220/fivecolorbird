<?php
// +----------------------------------------------------------------------
// | likeadmin快速开发前后端分离管理后台（PHP版）
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | gitee下载：https://gitee.com/likeshop_gitee/likeadmin
// | github下载：https://github.com/likeshop-github/likeadmin
// | 访问官网：https://www.likeadmin.cn
// | likeadmin团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeadminTeam
// +----------------------------------------------------------------------


namespace app\adminapi\controller\weekly_report;


use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\weekly_report\WeeklyReportLists;
use app\adminapi\logic\log\LogLogic;
use app\adminapi\logic\weekly_report\WeeklyReportLogic;
use app\adminapi\validate\weekly_report\WeeklyReportValidate;


/**
 * WeeklyReport控制器
 * Class WeeklyReportController
 * @package app\adminapi\controller\weekly_report
 */
class WeeklyReportController extends BaseAdminController
{

    /**
     * @notes 获取周报列表
     * @return \think\response\Json
     * @author likeadmin
     * @date 2025/05/28 09:52
     */
    public function lists()
    {
        return $this->dataLists(new WeeklyReportLists());
    }


    /**
     * @notes 添加周报
     * @return \think\response\Json
     * @author likeadmin
     * @date 2025/05/28 09:52
     */
    public function add()
    {
        $params = (new WeeklyReportValidate())->post()->goCheck('add');
        $params['user_id'] = $this->adminId;
        $result = WeeklyReportLogic::add($params);
        if (true === $result) {
            return $this->success('添加成功', [], 1, 1);
        }
        return $this->fail(WeeklyReportLogic::getError());
    }


    /**
     * @notes 编辑周报
     * @return \think\response\Json
     * @author likeadmin
     * @date 2025/05/28 09:52
     */
    public function edit()
    {
        $params = (new WeeklyReportValidate())->post()->goCheck('edit');
        $params['user_id'] = $this->adminId;
        $result = WeeklyReportLogic::edit($params);
        if (true === $result) {
            return $this->success('编辑成功', [], 1, 1);
        }
        return $this->fail(WeeklyReportLogic::getError());
    }


    /**
     * @notes 删除周报
     * @return \think\response\Json
     * @author likeadmin
     * @date 2025/05/28 09:52
     */
    public function delete()
    {
        $params = (new WeeklyReportValidate())->post()->goCheck('delete');
        $params['user_id'] = $this->adminId;
        WeeklyReportLogic::delete($params);
        return $this->success('删除成功', [], 1, 1);
    }


    /**
     * @notes 获取周报详情
     * @return \think\response\Json
     * @author likeadmin
     * @date 2025/05/28 09:52
     */
    public function detail()
    {
        $params = (new WeeklyReportValidate())->goCheck('detail');
        $result = WeeklyReportLogic::detail($params);
        return $this->data($result);
    }


    /**
     * @notes 周报审核
     * @return \think\response\Json
     * @author likeadmin
     * @date 2025/05/28 09:52
     */
    public function examine()
    {
        $params = (new WeeklyReportValidate())->post()->goCheck('examine');
        $params['admin_id'] = $this->adminId;
        $result = WeeklyReportLogic::examine($params);
        if (true === $result) {
            LogLogic::add([
                'admin_id'=>$this->adminId,
                'admin_name'=>$this->adminInfo['name']??'',
                'action'=>'审核',
                'title'=>"周报",
                'name'=>"{$params['userInfo']['name']}",
            ]);
            return $this->success('审核成功', [], 1, 1);
        }
        return $this->fail(WeeklyReportLogic::getError());
    }



}
