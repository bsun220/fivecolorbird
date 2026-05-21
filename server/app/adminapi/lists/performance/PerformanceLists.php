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

namespace app\adminapi\lists\performance;


use app\adminapi\lists\BaseAdminDataLists;
use app\common\model\auth\Admin;
use app\common\model\dept\Dept;
use app\common\model\dept\Jobs;
use app\common\model\performance\Performance;
use app\common\lists\ListsSearchInterface;


/**
 * Performance列表
 * Class PerformanceLists
 * @package app\adminapi\lists\performance
 */
class PerformanceLists extends BaseAdminDataLists implements ListsSearchInterface
{

    /**
     * @notes 设置搜索条件
     * @return array
     * @author likeadmin
     * @date 2025/05/28 09:52
     */
    public function setSearch(): array
    {
        return [
            '=' => ['user_id', 'work_score'],
        ];
    }

    private function normalizeMonthValues($value): array
    {
        $value = trim((string)$value);
        if ($value === '') {
            return [];
        }

        if (!preg_match('/^(\d{4})-(\d{1,2})/', $value, $matches)) {
            return [$value];
        }

        $year = $matches[1];
        $month = (int)$matches[2];
        if ($month < 1 || $month > 12) {
            return [$value];
        }

        return array_values(array_unique([
            $year . '-' . str_pad((string)$month, 2, '0', STR_PAD_LEFT),
            $year . '-' . $month,
        ]));
    }

    /**
     * @notes 自定义查询条件
     * @return array
     * @author likeadmin
     * @date 2025/05/28 09:52
     */
    public function queryWhere()
    {
        $where = [];

        if (!empty($this->params['name'])) {
            $id = Admin::where(['name' => $this->params['name']])->column('id');
            $where[] = ['user_id', 'in', $id];
        }

        if (!empty($this->params['work_score'])) {
            $where[] = ['work_score', '=', $this->params['work_score']];
        }

        $statisticalMonth = $this->params['statistical_month'] ?? ($this->params['month'] ?? '');
        $monthValues = $this->normalizeMonthValues($statisticalMonth);
        if (!empty($monthValues)) {
            $where[] = count($monthValues) === 1
                ? ['statistical_month', '=', $monthValues[0]]
                : ['statistical_month', 'in', $monthValues];
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
        $list = Performance::where($this->searchWhere)
            ->where($this->queryWhere())
            ->field([
                'id', 'user_id', 'statistical_month', 'merit_pay', 'merit_pay_note',
                'issue_date', 'cumulative_merit_pay', 'issued', 'reward_amount',
                'remaining_overtime_hours', 'reward_amount_note', 'penalty_amount', 'penalty_amount_note',
                'work_score', 'work_comment', 'admin_id', 'create_time'
            ])
            ->append(['work_score_desc'])
            ->with(['userInfo' => function ($query) {
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

        foreach ($list as $key => &$item) {
            $userInfo = $item['userInfo'];
            if ($userInfo) {
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
        return Performance::where($this->searchWhere)->where($this->queryWhere())->count();
    }

}
