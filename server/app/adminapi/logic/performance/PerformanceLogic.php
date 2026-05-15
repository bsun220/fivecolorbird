<?php
namespace app\adminapi\logic\performance;

use app\adminapi\logic\system_msg\SystemMsgLogic;
use app\common\model\dept\Dept;
use app\common\model\dept\Jobs;
use app\common\model\performance\Performance;
use app\common\model\weekly_report\WeeklyReport;
use app\common\logic\BaseLogic;
use think\Exception;
use think\facade\Db;

class PerformanceLogic extends BaseLogic
{
    public static function add(array $params): bool
    {
        Db::startTrans();
        try {
            $data = [
                'user_id' => $params['user_id'],
                'statistical_month' => $params['statistical_month'] ?? '',
                'merit_pay' => $params['merit_pay'] ?? 0,
                'merit_pay_note' => $params['merit_pay_note'] ?? '',
                'issue_date' => $params['issue_date'] ?? null,
                'cumulative_merit_pay' => $params['cumulative_merit_pay'] ?? 0,
                'issued' => $params['issued'] ?? 0,
                'remaining_overtime_hours' => $params['remaining_overtime_hours'] ?? 0,
                'reward_amount' => $params['reward_amount'] ?? 0,
                'reward_amount_note' => $params['reward_amount_note'] ?? '',
                'penalty_amount' => $params['penalty_amount'] ?? 0,
                'penalty_amount_note' => $params['penalty_amount_note'] ?? '',
                'work_score' => $params['work_score'] ?? null,
                'work_comment' => $params['work_comment'] ?? '',
            ];

            Performance::create($data);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    public static function edit(array $params): bool
    {
        Db::startTrans();
        try {
            $Performance = Performance::findOrEmpty($params['id']);
            if ($Performance->isEmpty()) {
                throw new Exception("绩效记录不存在");
            }

            $data = [
                'statistical_month' => $params['statistical_month'] ?? '',
                'merit_pay' => $params['merit_pay'] ?? 0,
                'merit_pay_note' => $params['merit_pay_note'] ?? '',
                'issue_date' => $params['issue_date'] ?? null,
                'cumulative_merit_pay' => $params['cumulative_merit_pay'] ?? 0,
                'issued' => $params['issued'] ?? 0,
                'remaining_overtime_hours' => $params['remaining_overtime_hours'] ?? $Performance->remaining_overtime_hours ?? 0,
                'reward_amount' => $params['reward_amount'] ?? 0,
                'reward_amount_note' => $params['reward_amount_note'] ?? '',
                'penalty_amount' => $params['penalty_amount'] ?? 0,
                'penalty_amount_note' => $params['penalty_amount_note'] ?? '',
                'work_score' => $params['work_score'] ?? null,
                'work_comment' => $params['work_comment'] ?? '',
            ];

            Performance::where('id', $params['id'])->update($data);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    public static function delete(array $params): bool
    {
        return Performance::destroy($params['id']);
    }

    public static function detail($params): array
    {
        $Performance = Performance::where(['id' => $params['id']])
            ->with(['userInfo' => function ($query) {
                $query->withTrashed()
                    ->withoutField(['password', 'password_mw'])
                    ->append(['role_id', 'dept_id', 'jobs_id', 'disable_desc']);
            }])
            ->append(['work_score_desc'])
            ->find();

        if (empty($Performance)) {
            return [];
        }

        $Performance = $Performance->toArray();

        $deptLists = Dept::column('name', 'id');
        $jobsLists = Jobs::column('name', 'id');

        if (!empty($Performance['userInfo']['dept_id'])) {
            $deptName = '';
            foreach ((array)$Performance['userInfo']['dept_id'] as $deptId) {
                $deptName .= $deptLists[$deptId] ?? '';
                $deptName .= '/';
            }
            $Performance['userInfo']['dept_name'] = trim($deptName, '/');
        }

        if (!empty($Performance['userInfo']['jobs_id'])) {
            $jobsName = '';
            foreach ((array)$Performance['userInfo']['jobs_id'] as $jobsId) {
                $jobsName .= $jobsLists[$jobsId] ?? '';
                $jobsName .= '/';
            }
            $Performance['userInfo']['jobs_name'] = trim($jobsName, '/');
        }

        $Performance['remaining_overtime_hours'] = (float)($Performance['remaining_overtime_hours'] ?? 0);

        return $Performance;
    }

    public static function evaluate(array $params): bool
    {
        Db::startTrans();
        try {
            $Performance = Performance::findOrEmpty($params['id']);
            if ($Performance->isEmpty()) {
                throw new Exception("绩效记录不存在");
            }

            $data = [
                'work_score' => $params['work_score'] ?? null,
                'work_comment' => $params['work_comment'] ?? '',
                'merit_pay' => $params['merit_pay'] ?? 0,
                'merit_pay_note' => $params['merit_pay_note'] ?? '',
                'reward_amount' => $params['reward_amount'] ?? 0,
                'reward_amount_note' => $params['reward_amount_note'] ?? '',
                'penalty_amount' => $params['penalty_amount'] ?? 0,
                'penalty_amount_note' => $params['penalty_amount_note'] ?? '',
                'remaining_overtime_hours' => $params['remaining_overtime_hours'] ?? $Performance->remaining_overtime_hours ?? 0,
                'admin_id' => $params['admin_id'] ?? 0,
            ];

            Performance::where('id', $params['id'])->update($data);

            SystemMsgLogic::add([
                'content' => "您的绩效已评分",
                'user_id' => $Performance->user_id,
            ]);

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    public static function weeklyReportList($params): array
    {
        $where = [];
        if (!empty($params['user_id'])) {
            $where[] = ['user_id', '=', $params['user_id']];
        }

        $month = $params['statistical_month'] ?? ($params['month'] ?? '');
        $monthRange = self::monthDateRange($month);
        if (!empty($monthRange)) {
            $where[] = ['start_date', '<=', $monthRange['end']];
            $where[] = ['end_date', '>=', $monthRange['start']];
        }

        $list = WeeklyReport::where($where)
            ->field(['id', 'start_date', 'end_date', 'total_hours', 'overtime_hours', 'reply', 'status', 'create_time'])
            ->order(['id' => 'desc'])
            ->select()
            ->toArray();

        return $list;
    }

    private static function monthDateRange($month): array
    {
        if (empty($month)) {
            return [];
        }
        $timestamp = strtotime($month . '-01');
        if (empty($timestamp)) {
            return [];
        }
        return [
            'start' => date('Y-m-01', $timestamp),
            'end' => date('Y-m-t', $timestamp),
        ];
    }

}
