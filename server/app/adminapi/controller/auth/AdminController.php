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

namespace app\adminapi\controller\auth;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\auth\AdminLists;
use app\adminapi\logic\log\LogLogic;
use app\adminapi\logic\system_msg\SystemMsgLogic;
use app\adminapi\validate\auth\AdminValidate;
use app\adminapi\logic\auth\AdminLogic;
use app\adminapi\validate\auth\editSelfValidate;
use app\common\model\auth\Admin;

/**
 * 管理员控制器
 * Class AdminController
 * @package app\adminapi\controller\auth
 */
class AdminController extends BaseAdminController
{

    /**
     * @notes 查看用户列表
     * @return \think\response\Json
     * @author 段誉
     * @date 2021/12/29 9:55
     */
    public function lists()
    {
        return $this->dataLists(new AdminLists());
    }


    /**
     * @notes 添加用户
     * @return \think\response\Json
     * @author 段誉
     * @date 2021/12/29 10:21
     */
    public function add()
    {
        $params = (new AdminValidate())->post()->goCheck('add');
        $result = AdminLogic::add($params);

        if (false !== $result) {
            LogLogic::add([
                'admin_id'=>$this->adminId,
                'admin_name'=>$this->adminInfo['name']??'',
                'action'=>'添加',
                'title'=>"用户",
                'name'=>"{$params['name']}",
            ]);
            SystemMsgLogic::add(['content'=>"账户添加", 'user_id'=>$result['id']]);
            return $this->success('操作成功', [], 1, 1);
        }
        return $this->fail(AdminLogic::getError());
    }


    /**
     * @notes 编辑用户
     * @return \think\response\Json
     * @author 段誉
     * @date 2021/12/29 11:03
     */
    public function edit()
    {
        $params = (new AdminValidate())->post()->goCheck('edit');
        $result = AdminLogic::edit($params);
        if (false !== $result) {
            LogLogic::add([
                'admin_id'=>$this->adminId,
                'admin_name'=>$this->adminInfo['name']??'',
                'action'=>'编辑',
                'title'=>"用户",
                'name'=>"{$params['name']}",
            ]);
            SystemMsgLogic::add(['content'=>"账户被编辑", 'user_id'=>$result['id']]);
            return $this->success('操作成功', [], 1, 1);
        }
        return $this->fail(AdminLogic::getError());
    }


    /**
     * @notes 删除用户
     * @return \think\response\Json
     * @author 段誉
     * @date 2021/12/29 11:03
     */
    public function delete()
    {
        $params = (new AdminValidate())->post()->goCheck('delete');

        if(is_array($params['id'])){
            foreach ($params['id'] as $id){
                $names = implode(',', Admin::whereIn('id', $id)->column("name"));
                $result = AdminLogic::delete(['id'=>$id]);
                if (true === $result) {
                    LogLogic::add([
                        'admin_id'=>$this->adminId,
                        'admin_name'=>$this->adminInfo['name']??'',
                        'action'=>'删除',
                        'title'=>"用户",
                        'name'=>$names
                    ]);
                }
            }
            return $this->success('操作成功', [], 1, 1);
        }else{
            $names = implode(',', Admin::whereIn('id', $params['id'])->column("name"));
            $result = AdminLogic::delete($params);
            if (true === $result) {
                LogLogic::add([
                    'admin_id'=>$this->adminId,
                    'admin_name'=>$this->adminInfo['name']??'',
                    'action'=>'删除',
                    'title'=>"用户",
                    'name'=>$names
                ]);
                return $this->success('操作成功', [], 1, 1);
            }
        }

        return $this->fail(AdminLogic::getError());
    }

    /**
     * @notes 设置用户离职
     * @return \think\response\Json
     * @author 段誉
     * @date 2021/12/29 11:03
     */
    public function disable()
    {
        $params = (new AdminValidate())->post()->goCheck('disable');
        $result = AdminLogic::disable($params);
        if (true === $result) {
            LogLogic::add([
                'admin_id'=>$this->adminId,
                'admin_name'=>$this->adminInfo['name']??'',
                'action'=>'设置离职',
                'title'=>"用户",
                'name'=>implode(',', Admin::whereIn('id', $params['id'])->column("name"))
            ]);
            return $this->success('操作成功', [], 1, 1);
        }
        return $this->fail(AdminLogic::getError());
    }



    /**
     * @notes 查看用户详情
     * @return \think\response\Json
     * @author 段誉
     * @date 2021/12/29 11:07
     */
    public function detail()
    {
        $params = (new AdminValidate())->goCheck('detail');
        $result = AdminLogic::detail($params);
        return $this->data($result);
    }


    public function adminCount()
    {
        $result = AdminLogic::count();
        return $this->data($result);
    }

    /**
     * @notes 获取当前用户信息
     * @return \think\response\Json
     * @author 段誉
     * @date 2021/12/31 10:53
     */
    public function mySelf()
    {
        $result = AdminLogic::detail(['id' => $this->adminId], 'auth');
        return $this->data($result);
    }


    /**
     * @notes 编辑超级管理员信息
     * @return \think\response\Json
     * @author 段誉
     * @date 2022/4/8 17:54
     */
    public function editSelf()
    {
        $params = (new editSelfValidate())->post()->goCheck('', ['admin_id' => $this->adminId]);
        $result = AdminLogic::editSelf($params);
        return $this->success('操作成功', [], 1, 1);
    }


    /**
     * @notes 查看用户列表全部
     * @return \think\response\Json
     * @author 段誉
     * @date 2021/12/29 9:55
     */
    public function all()
    {
        $params = $this->request->get();
        $list = AdminLogic::all($params);
        return $this->data($list);
    }

}
