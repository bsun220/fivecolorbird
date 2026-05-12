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

namespace app\adminapi\lists\weekly_report;


use app\adminapi\lists\BaseAdminDataLists;
use app\common\model\auth\Admin;
use app\common\model\auth\SystemRole;
use app\common\model\dept\Dept;
use app\common\model\dept\Jobs;
use app\common\model\weekly_report\WeeklyReport;
use app\common\lists\ListsSearchInterface;


/**
 * WeeklyReport列表
 * Class WeeklyReportLists
 * @package app\adminapi\listsweekly_report
 */
class WeeklyReportLists extends BaseAdminDataLists implements ListsSearchInterface
{


    /**
     * @notes 设置搜索条件
     * @return \string[][]
     * @author likeadmin
     * @date 2025/05/28 09:52
     */
    public function setSearch(): array
    {
        return [
            '=' => ['node_id'],
            '%like%' => ['node'],
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
        $where = [];
        if (!empty($this->params['name'])) {
            $id = Admin::where(['name'=>$this->params['name']])->column('id');
            $where[] = ['user_id', 'in', $id];
        }

        if (!empty($this->params['status'])) {
            $where[] = ['status', '=', $this->params['status']];
        }

        if (!empty($this->params['type']) && $this->params['type'] == 1) {
            $where[] = ['user_id', '=', $this->adminId];
        }

        return $where;
    }

    /**
     * @notes 获取列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author likeadmin
     * @date 2025/05/28 09:52
     */
    public function lists(): array
    {

        $list = WeeklyReport::where($this->searchWhere)
            ->where($this->queryWhere())
            ->field(['id', 'file_name', 'file_url', 'node_id', 'node', 'user_id', 'working_hours', 'actual_hours', 'unfinished_work_hours', 'overtime_hours', 'remarks', 'status', 'create_time'])
            ->with(['userInfo'=>function($query){
                $query->withTrashed()->withoutField(['password', 'password_mw'])->append(['role_id', 'dept_id', 'jobs_id', 'disable_desc']);
            }])
            ->limit($this->limitOffset, $this->limitLength)
            ->order(['id' => 'desc'])
            ->select()
            ->toArray();

        // 部门列表
        $deptLists = Dept::column('name', 'id');
        // 岗位列表
        $jobsLists = Jobs::column('name', 'id');

        foreach ($list as $key => &$item){
            $userInfo = $item['userInfo'];
            if($userInfo){
                $deptName = '';
                foreach ($userInfo['dept_id'] as $deptId) {
                    $deptName .= $deptLists[$deptId] ?? '';
                    $deptName .= '/';
                }

                $jobsName = '';
                foreach ($userInfo['jobs_id'] as $jobsId) {
                    $jobsName .= $jobsLists[$jobsId] ?? '';
                    $jobsName .= '/';
                }

                $item['userInfo']['dept_name'] = trim($deptName, '/');
                $item['userInfo']['jobs_name'] = trim($jobsName, '/');
            }
        }

        return $list;
    }


    /**
     * @notes 获取数量
     * @return int
     * @author likeadmin
     * @date 2025/05/28 09:52
     */
    public function count(): int
    {
        return WeeklyReport::where($this->searchWhere)->count();
    }

}
