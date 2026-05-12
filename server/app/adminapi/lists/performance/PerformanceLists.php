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
use app\common\model\auth\SystemRole;
use app\common\model\dept\Dept;
use app\common\model\dept\Jobs;
use app\common\model\performance\Performance;
use app\common\lists\ListsSearchInterface;


/**
 * 绩效列表
 * Class PerformanceLists
 * @package app\adminapi\listsperformance
 */
class PerformanceLists extends BaseAdminDataLists implements ListsSearchInterface
{


    /**
     * @notes 设置搜索条件
     * @return \string[][]
     * @author likeadmin
     * @date 2025/06/01 23:31
     */
    public function setSearch(): array
    {
        return [
            '=' => ['user_id', 'statistical_month', 'merit_pay', 'merit_pay_note', 'cumulative_merit_pay', 'issued', 'reward_amount', 'reward_amount_note', 'penalty_amount', 'penalty_amount_note', 'work_score', 'work_comment'],
            'between_time' => 'issue_date',
        ];
    }


    /**
     * @notes 获取列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author likeadmin
     * @date 2025/06/01 23:31
     */
    public function lists(): array
    {
        $type = $this->request->get('type');
        if(!empty($type)){
            $this->searchWhere[] = ['user_id', '=', $this->adminId];
        }

        $list = Performance::where($this->searchWhere)
            ->field(['id', 'user_id', 'statistical_month', 'merit_pay', 'merit_pay_note', 'issue_date', 'cumulative_merit_pay', 'issued', 'reward_amount', 'reward_amount_note', 'penalty_amount', 'penalty_amount_note', 'work_score', 'work_comment'])
            ->with(['userInfo' => function($query) {
                $query->withTrashed(); // 包含已删除的用户信息
            }])
            ->limit($this->limitOffset, $this->limitLength)
            ->order(['id' => 'desc'])
            ->select()
            ->toArray();

        // 角色数组（'角色id'=>'角色名称')
        $roleLists = SystemRole::column('name', 'id');
        // 部门列表
        $deptLists = Dept::column('name', 'id');
        // 岗位列表
        $jobsLists = Jobs::column('name', 'id');

        //管理员列表增加角色名称
        foreach ($list as $k => $v) {
            $roleName = '';
            if ($v['userInfo']['root'] == 1) {
                $roleName = '系统管理员';
            } else {
                foreach ($v['userInfo']['role_id'] as $roleId) {
                    $roleName .= $roleLists[$roleId] ?? '';
                    $roleName .= '/';
                }
            }

            $deptName = '';
            foreach ($v['userInfo']['dept_id'] as $deptId) {
                $deptName .= $deptLists[$deptId] ?? '';
                $deptName .= '/';
            }

            $jobsName = '';
            foreach ($v['userInfo']['jobs_id'] as $jobsId) {
                $jobsName .= $jobsLists[$jobsId] ?? '';
                $jobsName .= '/';
            }

            $list[$k]['userInfo']['role_name'] = trim($roleName, '/');
            $list[$k]['userInfo']['dept_name'] = trim($deptName, '/');
            $list[$k]['userInfo']['jobs_name'] = trim($jobsName, '/');
        }

        return $list;
    }


    /**
     * @notes 获取数量
     * @return int
     * @author likeadmin
     * @date 2025/06/01 23:31
     */
    public function count(): int
    {
        return Performance::where($this->searchWhere)->count();
    }

}
