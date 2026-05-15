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
            '>=' => ['start_date'],
            '<=' => ['end_date'],
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

        if (isset($this->params['status']) && $this->params['status'] !== '') {
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

        $query = WeeklyReport::where($this->searchWhere)
            ->where($this->queryWhere())
            ->field(['id', 'start_date', 'end_date', 'total_hours', 'overtime_hours', 'status', 'reply', 'submit_time', 'examine_time', 'create_time', 'user_id']);

        if (empty($this->params['type']) || $this->params['type'] != 1) {
            $query->whereNotNull('submit_time');
        }

        $list = $query
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

            $item['submit_time'] = !empty($item['submit_time']) ? date('Y-m-d H:i:s', (int)$item['submit_time']) : '';
            $item['examine_time'] = !empty($item['examine_time']) ? date('Y-m-d H:i:s', (int)$item['examine_time']) : '';
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
        $query = WeeklyReport::where($this->searchWhere)->where($this->queryWhere());
        if (empty($this->params['type']) || $this->params['type'] != 1) {
            $query->whereNotNull('submit_time');
        }
        return $query->count();
    }

}
