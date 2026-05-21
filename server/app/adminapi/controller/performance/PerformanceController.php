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

namespace app\adminapi\controller\performance;


use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\performance\PerformanceLists;
use app\adminapi\logic\log\LogLogic;
use app\adminapi\logic\performance\PerformanceLogic;
use app\adminapi\logic\system_msg\SystemMsgLogic;
use app\adminapi\validate\performance\PerformanceValidate;
use app\common\model\auth\Admin;
use app\common\model\Performance;


/**
 * Performance控制器
 * Class PerformanceController
 * @package app\adminapi\controller\performance
 */
class PerformanceController extends BaseAdminController
{


    /**
     * @notes 获取绩效列表
     * @return \think\response\Json
     * @author likeadmin
     * @date 2025/06/01 23:31
     */
    public function lists()
    {
        return $this->dataLists(new PerformanceLists());
    }


    /**
     * @notes 添加绩效
     * @return \think\response\Json
     * @author likeadmin
     * @date 2025/06/01 23:31
     */
    public function add()
    {
        $params = (new PerformanceValidate())->post()->goCheck('add');
        $result = PerformanceLogic::add($params);
        if (false !== $result) {
            LogLogic::add([
                'admin_id'=>$this->adminId,
                'admin_name'=>$this->adminInfo['name']??'',
                'action'=>'添加',
                'title'=>"绩效",
                'name'=>Admin::where(['id'=>$params['user_id']])->value('name'),
            ]);

            SystemMsgLogic::add(['content'=>"添加绩效", 'user_id'=>$params['user_id'], 'admin_id'=>$this->adminId]);
            return $this->success('添加成功', [], 1, 1);
        }
        return $this->fail(PerformanceLogic::getError());
    }


    /**
     * @notes 编辑绩效
     * @return \think\response\Json
     * @author likeadmin
     * @date 2025/06/01 23:31
     */
    public function edit()
    {
        $params = (new PerformanceValidate())->post()->goCheck('edit');
        $result = PerformanceLogic::edit($params);
        if (true === $result) {
            LogLogic::add([
                'admin_id'=>$this->adminId,
                'admin_name'=>$this->adminInfo['name']??'',
                'action'=>'编辑',
                'title'=>"绩效",
                'name'=>Admin::where(['id'=>$params['user_id']])->value('name'),
            ]);

            SystemMsgLogic::add(['content'=>"绩效被编辑", 'user_id'=>$params['user_id'], 'admin_id'=>$this->adminId]);
            return $this->success('编辑成功', [], 1, 1);
        }
        return $this->fail(PerformanceLogic::getError());
    }


    /**
     * @notes 删除绩效
     * @return \think\response\Json
     * @author likeadmin
     * @date 2025/06/01 23:31
     */
    public function delete()
    {
        $params = (new PerformanceValidate())->post()->goCheck('delete');
        $user_id = Performance::where(['id'=>$params['id']])->value('user_id');
        PerformanceLogic::delete($params);
        LogLogic::add([
            'admin_id'=>$this->adminId,
            'admin_name'=>$this->adminInfo['name']??'',
            'action'=>'删除',
            'title'=>"绩效",
            'name'=>Admin::where(['id'=>$user_id])->value('name') ?? $params['id'],
        ]);
        return $this->success('删除成功', [], 1, 1);
    }


    /**
     * @notes 获取绩效详情
     * @return \think\response\Json
     * @author likeadmin
     * @date 2025/06/01 23:31
     */
    public function detail()
    {
        $params = (new PerformanceValidate())->goCheck('detail');
        $result = PerformanceLogic::detail($params);
        return $this->data($result);
    }

    /**
     * @notes 绩效中获取周报详情
     * @return \think\response\Json
     * @author likeadmin
     * @date 2025/06/01 23:31
     */
    public function weeklyReportList()
    {
        $params = (new PerformanceValidate())->goCheck('weeklyReportList');
        $result = PerformanceLogic::weeklyReportList($params);
        return $this->data($result);
    }


    /**
     * @notes 绩效评分
     * @return \think\response\Json
     * @author likeadmin
     * @date 2025/06/01 23:31
     */
    public function evaluate()
    {
        $params = (new PerformanceValidate())->post()->goCheck('evaluate');
        $params['admin_id'] = $this->adminId;
        $result = PerformanceLogic::evaluate($params);
        if (true === $result) {
            LogLogic::add([
                'admin_id'=>$this->adminId,
                'admin_name'=>$this->adminInfo['name']??'',
                'action'=>'评分',
                'title'=>"绩效",
                'name'=>Admin::where(['id'=>$params['user_id']])->value('name'),
            ]);
            return $this->success('评分成功', [], 1, 1);
        }
        return $this->fail(PerformanceLogic::getError());
    }


}