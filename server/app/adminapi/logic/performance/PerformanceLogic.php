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

namespace app\adminapi\logic\performance;


use app\common\model\dept\Dept;
use app\common\model\dept\Jobs;
use app\common\model\performance\Performance;
use app\common\logic\BaseLogic;
use app\common\model\WeeklyReport;
use think\facade\Db;


/**
 * Performance逻辑
 * Class PerformanceLogic
 * @package app\adminapi\logic\performance
 */
class PerformanceLogic extends BaseLogic
{


    /**
     * @notes 添加
     * @param array $params
     * @return bool
     * @author likeadmin
     * @date 2025/06/01 23:31
     */
    public static function add(array $params)
    {
        Db::startTrans();
        try {
            $Performance = Performance::create([
                'user_id' => $params['user_id'],
                'statistical_month' => $params['statistical_month'],
                'merit_pay' => $params['merit_pay'],
                'merit_pay_note' => $params['merit_pay_note'],
                'issue_date' => $params['issue_date'],
                'cumulative_merit_pay' => $params['cumulative_merit_pay'],
                'issued' => $params['issued'],
                'reward_amount' => $params['reward_amount'],
                'reward_amount_note' => $params['reward_amount_note'],
                'penalty_amount' => $params['penalty_amount'],
                'penalty_amount_note' => $params['penalty_amount_note'],
                'work_score' => $params['work_score'],
                'work_comment' => $params['work_comment']
            ]);

            Db::commit();
            return $Performance;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * @notes 编辑
     * @param array $params
     * @return bool
     * @author likeadmin
     * @date 2025/06/01 23:31
     */
    public static function edit(array $params): bool
    {
        Db::startTrans();
        try {
            Performance::where('id', $params['id'])->update([
                'user_id' => $params['user_id'],
                'statistical_month' => $params['statistical_month'],
                'merit_pay' => $params['merit_pay'],
                'merit_pay_note' => $params['merit_pay_note'],
                'issue_date' => $params['issue_date'],
                'cumulative_merit_pay' => $params['cumulative_merit_pay'],
                'issued' => $params['issued'],
                'reward_amount' => $params['reward_amount'],
                'reward_amount_note' => $params['reward_amount_note'],
                'penalty_amount' => $params['penalty_amount'],
                'penalty_amount_note' => $params['penalty_amount_note'],
                'work_score' => $params['work_score'],
                'work_comment' => $params['work_comment']
            ]);

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }


    /**
     * @notes 删除
     * @param array $params
     * @return bool
     * @author likeadmin
     * @date 2025/06/01 23:31
     */
    public static function delete(array $params): bool
    {
        return Performance::destroy($params['id']);
    }


    /**
     * @notes 获取详情
     * @param $params
     * @return array
     * @author likeadmin
     * @date 2025/06/01 23:31
     */
    public static function detail($params): array
    {
        $info = Performance::with(['userInfo' => function($query) {
                $query->withTrashed(); // 包含已删除的用户信息
            }])
            ->findOrEmpty($params['id'])->toArray();

        // 部门列表
        $deptLists = Dept::column('name', 'id');
        // 岗位列表
        $jobsLists = Jobs::column('name', 'id');


        $userInfo = $info['userInfo'];

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

        $info['userInfo']['dept_name'] = trim($deptName, '/');
        $info['userInfo']['jobs_name'] = trim($jobsName, '/');


        return $info;
    }


    /**
     * @notes 获取详情
     * @param $params
     * @return array
     * @author likeadmin
     * @date 2025/06/01 23:31
     */
    public static function weeklyReportList($params): array
    {
        $statistical_month = $params['statistical_month'];
        $user_id = $params['user_id'];
        list($date,$month) =  explode('-',$statistical_month);
        $where = [
            'user_id' => $user_id,
            'date' => $date,
            'month' => intval($month),
            'status' => WeeklyReport::YES,
        ];
        $field = [
            'node',
            'actual_hours',
            'overtime_hours',
            'remarks',
        ];
        return WeeklyReport::field($field)->where($where)->select()->toArray();
    }



}
