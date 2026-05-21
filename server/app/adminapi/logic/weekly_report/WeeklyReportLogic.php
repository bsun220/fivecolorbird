<?php
// +----------------------------------------------------------------------
// | likeadmin快速开发前后端分离管理后台（PHP版）
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | gitee下载：https://gitee.com/likeshop_gitee/likeadmin
// | github下载：https://github.com/likeshop-github/likeadmin
// | 访问官网：https://www.likeadmin.cn
// | likeadmin团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeadminTeam
// +----------------------------------------------------------------------

namespace app\adminapi\logic\weekly_report;


use app\adminapi\logic\system_msg\SystemMsgLogic;
use app\common\model\dept\Dept;
use app\common\model\dept\Jobs;
use app\common\model\weekly_report\WeeklyReport;
use app\common\logic\BaseLogic;
use think\Exception;
use think\facade\Db;


/**
 * WeeklyReport逻辑
 * Class WeeklyReportLogic
 * @package app\adminapi\logic\weekly_report
 */
class WeeklyReportLogic extends BaseLogic
{
    private static function normalizeHour($value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }
        return round(max((float)$value, 0), 1);
    }

    private static function normalizeDailyDetails($value): array
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $value = is_array($decoded) ? $decoded : [];
        }

        if (!is_array($value)) {
            return [];
        }

        foreach ($value as &$day) {
            if (!is_array($day)) {
                $day = [];
            }

            $tasks = $day['tasks'] ?? [];
            $tasks = is_array($tasks) && !empty($tasks) ? $tasks : [];
            $totalHours = 0.0;

            foreach ($tasks as &$task) {
                if (!is_array($task)) {
                    $task = [];
                }
                $task['hours'] = self::normalizeHour($task['hours'] ?? 0);
                $totalHours += $task['hours'];
            }
            unset($task);

            $day['tasks'] = $tasks;
            $day['hours'] = self::normalizeHour($totalHours);
        }
        unset($day);

        return $value;
    }

    private static function sumDailyHours(array $dailyDetails): float
    {
        $total = 0.0;
        foreach ($dailyDetails as $day) {
            $total += self::normalizeHour($day['hours'] ?? 0);
        }
        return self::normalizeHour($total);
    }


    /**
     * @notes 添加
     * @param array $params
     * @return array|false
     * @author likeadmin
     * @date 2025/05/28 09:52
     */
    public static function add(array $params)
    {
        Db::startTrans();
        try {
            // 检查同一用户同一周期是否已存在
            $exist = WeeklyReport::where([
                'user_id' => $params['user_id'],
                'start_date' => $params['start_date'],
                'end_date' => $params['end_date'],
            ])->findOrEmpty();

            if (!$exist->isEmpty()) {
                throw new Exception("该周期周报已存在");
            }

            $dailyDetails = self::normalizeDailyDetails($params['daily_details'] ?? []);

            $data = [
                'user_id' => $params['user_id'],
                'start_date' => $params['start_date'],
                'end_date' => $params['end_date'],
                'daily_details' => $dailyDetails,
                'total_hours' => self::sumDailyHours($dailyDetails),
                'overtime_hours' => self::normalizeHour($params['overtime_hours'] ?? 0),
                'todo_items' => $params['todo_items'] ?? '',
                'status' => 0,
                'submit_time' => null,
                'reply' => '',
            ];

            $weeklyReport = WeeklyReport::create($data);

            Db::commit();
            return ['id' => $weeklyReport->id];
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
     * @date 2025/05/28 09:52
     */
    public static function edit(array $params): bool
    {
        Db::startTrans();
        try {
            $WeeklyReport = WeeklyReport::where([
                'id' => $params['id'],
                'user_id' => $params['user_id'],
            ])->findOrEmpty();
            if ($WeeklyReport->isEmpty()) {
                throw new Exception("周报不存在");
            }

            // 已审批(status=1)的周报不能修改
            if ($WeeklyReport->status == 1) {
                throw new Exception("已审批的周报不能修改");
            }

            $exist = WeeklyReport::where([
                'user_id' => $params['user_id'],
                'start_date' => $params['start_date'],
                'end_date' => $params['end_date'],
            ])->where('id', '<>', $params['id'])->findOrEmpty();
            if (!$exist->isEmpty()) {
                throw new Exception("该周期周报已存在");
            }

            $dailyDetails = self::normalizeDailyDetails($params['daily_details'] ?? []);

            $data = [
                'start_date' => $params['start_date'],
                'end_date' => $params['end_date'],
                'daily_details' => $dailyDetails,
                'total_hours' => self::sumDailyHours($dailyDetails),
                'overtime_hours' => self::normalizeHour($params['overtime_hours'] ?? 0),
                'todo_items' => $params['todo_items'] ?? '',
            ];

            if ($WeeklyReport->status == 2) {
                $data['status'] = 0;
                $data['submit_time'] = null;
                $data['reply'] = '';
                $data['admin_id'] = 0;
                $data['examine_time'] = null;
            }

            WeeklyReport::where('id', $params['id'])->update($data);

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
     * @date 2025/05/28 09:52
     */
    public static function delete(array $params): bool
    {
        $weeklyReport = WeeklyReport::where([
            'id' => $params['id'],
            'user_id' => $params['user_id'],
        ])->findOrEmpty();

        if ($weeklyReport->isEmpty()) {
            self::setError('周报不存在或无权删除');
            return false;
        }

        return (bool)$weeklyReport->delete();
    }


    /**
     * @notes 提交
     * @param array $params
     * @return bool
     * @author likeadmin
     * @date 2025/05/28 09:52
     */
    public static function submit(array $params): bool
    {
        Db::startTrans();
        try {
            $WeeklyReport = WeeklyReport::where([
                'id' => $params['id'],
                'user_id' => $params['user_id'],
            ])->findOrEmpty();
            if ($WeeklyReport->isEmpty()) {
                throw new Exception("周报不存在");
            }

            // 检查状态为草稿(0)
            if ($WeeklyReport->status != 0) {
                throw new Exception("只能提交草稿状态的周报");
            }

            // 标记为已提交待审批（status仍为0，但记录提交时间）
            WeeklyReport::where('id', $params['id'])->update([
                'submit_time' => time(),
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
     * @notes 审核
     * @param array $params
     * @return bool
     * @author likeadmin
     * @date 2025/05/28 09:52
     */
    public static function examine(array $params): bool
    {
        Db::startTrans();
        try {
            $WeeklyReport = WeeklyReport::findOrEmpty($params['id']);
            if ($WeeklyReport->isEmpty()) {
                throw new Exception("周报不存在");
            }

            if (empty($WeeklyReport->submit_time)) {
                throw new Exception("周报未提交，不能审核");
            }

            $data = [
                'status' => $params['status'], // 1=已审批 2=驳回
                'reply' => $params['reply'] ?? '',
                'admin_id' => $params['admin_id'] ?? 0,
                'examine_time' => time(),
            ];

            WeeklyReport::where('id', $params['id'])->update($data);

            // 发送系统消息
            SystemMsgLogic::add([
                'content' => "周报被审核，审核结果：" . ($params['status'] == 1 ? "已审批" : "驳回"),
                'user_id' => $WeeklyReport->user_id,
                'admin_id' => $params['admin_id'] ?? 0,
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
     * @notes 获取详情
     * @param $params
     * @return array
     * @author likeadmin
     * @date 2025/05/28 09:52
     */
    public static function detail($params): array
    {
        $WeeklyReport = WeeklyReport::where(['id' => $params['id']])
            ->field([
                'id', 'start_date', 'end_date', 'daily_details',
                'total_hours', 'overtime_hours', 'todo_items',
                'status', 'reply', 'user_id', 'admin_id',
                'submit_time', 'examine_time', 'create_time', 'update_time'
            ])
            ->with(['userInfo' => function ($query) {
                $query->withTrashed()
                    ->withoutField(['password', 'password_mw'])
                    ->append(['role_id', 'dept_id', 'jobs_id', 'disable_desc']);
            }, 'adminInfo' => function ($query) {
                $query->withTrashed()
                    ->withoutField(['password', 'password_mw']);
            }])
            ->find();

        if (empty($WeeklyReport)) {
            return [];
        }

        $WeeklyReport = $WeeklyReport->toArray();

        // 部门列表
        $deptLists = Dept::column('name', 'id');
        // 岗位列表
        $jobsLists = Jobs::column('name', 'id');

        // 处理部门名称
        $deptName = '';
        if (!empty($WeeklyReport['userInfo']['dept_id'])) {
            foreach ((array)$WeeklyReport['userInfo']['dept_id'] as $deptId) {
                $deptName .= $deptLists[$deptId] ?? '';
                $deptName .= '/';
            }
        }
        $WeeklyReport['userInfo']['dept_name'] = trim($deptName, '/');

        // 处理岗位名称
        $jobsName = '';
        if (!empty($WeeklyReport['userInfo']['jobs_id'])) {
            foreach ((array)$WeeklyReport['userInfo']['jobs_id'] as $jobsId) {
                $jobsName .= $jobsLists[$jobsId] ?? '';
                $jobsName .= '/';
            }
        }
        $WeeklyReport['userInfo']['jobs_name'] = trim($jobsName, '/');

        $WeeklyReport['submit_time'] = !empty($WeeklyReport['submit_time']) ? date('Y-m-d H:i:s', (int)$WeeklyReport['submit_time']) : '';
        $WeeklyReport['examine_time'] = !empty($WeeklyReport['examine_time']) ? date('Y-m-d H:i:s', (int)$WeeklyReport['examine_time']) : '';

        return $WeeklyReport;
    }

}
