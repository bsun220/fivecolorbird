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


namespace app\adminapi\controller\work_file;


use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\work_file\WorkFileLists;
use app\adminapi\logic\log\LogLogic;
use app\adminapi\logic\work_file\WorkFileLogic;
use app\adminapi\validate\work_file\WorkFileValidate;
use think\App;


/**
 * WorkFile控制器
 * Class WorkFileController
 * @package app\adminapi\controller\work_file
 */
class WorkFileController extends BaseAdminController
{

    /**
     * @notes 获取表单文件列表
     * @return \think\response\Json
     * @author likeadmin
     * @date 2025/06/02 17:10
     */
    public function lists()
    {
        return $this->dataLists(new WorkFileLists());
    }


    /**
     * @notes 添加表单文件
     * @return \think\response\Json
     * @author likeadmin
     * @date 2025/06/02 17:10
     */
    public function add()
    {
        $params = (new WorkFileValidate())->post()->goCheck('add');
        $params['admin_id'] = $this->adminId;
        $result = WorkFileLogic::add($params);
        if (true === $result) {
            LogLogic::add([
                'admin_id'=>$this->adminId,
                'admin_name'=>$this->adminInfo['name']??'',
                'action'=>'添加',
                'title'=>'表单文件',
                'name'=>"文件[". $params['name'] ."]",
            ]);
            return $this->success('添加成功', [], 1, 1);
        }
        return $this->fail(WorkFileLogic::getError());
    }


    /**
     * @notes 编辑表单文件
     * @return \think\response\Json
     * @author likeadmin
     * @date 2025/06/02 17:10
     */
    public function edit()
    {
        $params = (new WorkFileValidate())->post()->goCheck('edit');
        $result = WorkFileLogic::edit($params);
        if (true === $result) {
            return $this->success('编辑成功', [], 1, 1);
        }

        return $this->fail(WorkFileLogic::getError());
    }


    /**
     * @notes 删除表单文件
     * @return \think\response\Json
     * @author likeadmin
     * @date 2025/06/02 17:10
     */
    public function delete()
    {
        $params = (new WorkFileValidate())->post()->goCheck('delete');
        WorkFileLogic::delete($params);
        LogLogic::add([
            'admin_id'=>$this->adminId,
            'admin_name'=>$this->adminInfo['name']??'',
            'action'=>'删除',
            'title'=>'表单文件',
            'name'=>"文件id[". $params['id'] ."]",
        ]);
        return $this->success('删除成功', [], 1, 1);
    }


    /**
     * @notes 获取表单文件详情
     * @return \think\response\Json
     * @author likeadmin
     * @date 2025/06/02 17:10
     */
    public function detail()
    {
        $params = (new WorkFileValidate())->goCheck('detail');
        $result = WorkFileLogic::detail($params);
        return $this->data($result);
    }


}
