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

namespace app\adminapi\lists\notices;


use app\adminapi\lists\BaseAdminDataLists;
use app\common\model\auth\Admin;
use app\common\model\auth\SystemRole;
use app\common\model\dept\Dept;
use app\common\model\notices\Notices;
use app\common\lists\ListsSearchInterface;


/**
 * Notices列表
 * Class NoticesLists
 * @package app\adminapi\listsnotices
 */
class NoticesLists extends BaseAdminDataLists implements ListsSearchInterface
{


    /**
     * @notes 设置搜索条件
     * @return \string[][]
     * @author likeadmin
     * @date 2025/06/15 13:30
     */
    public function setSearch(): array
    {
        return [
            '=' => ['pid'],
            '%like%' => ['content'],
            'between_time' => 'create_time',
        ];
    }

    /**
     * @notes 自定查询条件
     * @return array
     * @author fsn
     * @date 2025/7/7
     */
    public function queryWhere()
    {
        $admin = Admin::where(['id'=>$this->adminId])->append(['role_id', 'dept_id'])->find()->toArray();
        //普通员工
        if(isset($admin['role_id'][0]) &&  $admin['role_id'][0] == 2){
            $admin['dept_id'] = array_merge($admin['dept_id'],[0]);
            $where[] = ['pid', 'in', $admin['dept_id']];
        }
        return $where ?? [];
    }


    /**
     * @notes 获取列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author likeadmin
     * @date 2025/06/15 13:30
     */
    public function lists(): array
    {

        $list = Notices::where($this->searchWhere)
            ->where($this->queryWhere())
            ->field(['id', 'pid', 'content', 'create_time'])
            ->limit($this->limitOffset, $this->limitLength)
            ->order(['id' => 'desc'])
            ->select()
            ->toArray();

        // 部门列表
        $deptLists = Dept::column('name', 'id');
        foreach ($list as $key => $value){
            if($value['pid'] == 0){
                $list[$key]['dept_name'] = '全部';
            }else{
                $list[$key]['dept_name'] = $deptLists[$value['pid']];
            }
        }

        return $list;
    }


    /**
     * @notes 获取数量
     * @return int
     * @author likeadmin
     * @date 2025/06/15 13:30
     */
    public function count(): int
    {
        return Notices::where($this->searchWhere)->where($this->queryWhere())->count();
    }

}
