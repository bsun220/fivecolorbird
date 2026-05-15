<?php
namespace app\adminapi\validate\performance;

use app\common\validate\BaseValidate;

class PerformanceValidate extends BaseValidate
{
    protected $rule = [
        'id' => 'require',
        'user_id' => 'require',
        'statistical_month' => 'require',
        'merit_pay' => 'float|egt:0',
        'merit_pay_note' => 'string',
        'issue_date' => 'date',
        'cumulative_merit_pay' => 'float|egt:0',
        'issued' => 'float|egt:0',
        'remaining_overtime_hours' => 'float|egt:0',
        'reward_amount' => 'float|egt:0',
        'reward_amount_note' => 'string',
        'penalty_amount' => 'float|egt:0',
        'penalty_amount_note' => 'string',
        'work_score' => 'number|egt:0',
        'work_comment' => 'string',
    ];

    protected $field = [
        'id' => 'id',
        'user_id' => '用户id',
        'statistical_month' => '统计月份',
        'merit_pay' => '绩效工资',
        'merit_pay_note' => '绩效工资说明',
        'issue_date' => '预计发放日期',
        'cumulative_merit_pay' => '累计绩效工资',
        'issued' => '已发绩效奖金',
        'remaining_overtime_hours' => '剩余加班工时',
        'reward_amount' => '奖励金额',
        'reward_amount_note' => '奖励金额说明',
        'penalty_amount' => '处罚金额',
        'penalty_amount_note' => '处罚金额说明',
        'work_score' => '工作评分',
        'work_comment' => '工作点评',
    ];

    public function sceneAdd()
    {
        return $this->remove('id', true);
    }

    public function sceneEdit()
    {
        return $this->only(['id', 'user_id', 'statistical_month', 'merit_pay', 'merit_pay_note', 'issue_date', 'cumulative_merit_pay', 'issued', 'remaining_overtime_hours', 'reward_amount', 'reward_amount_note', 'penalty_amount', 'penalty_amount_note', 'work_score', 'work_comment']);
    }

    public function sceneDelete()
    {
        return $this->only(['id']);
    }

    public function sceneDetail()
    {
        return $this->only(['id']);
    }

    public function sceneEvaluate()
    {
        return $this->only(['id', 'work_score', 'work_comment', 'merit_pay', 'merit_pay_note', 'reward_amount', 'reward_amount_note', 'penalty_amount', 'penalty_amount_note', 'remaining_overtime_hours']);
    }

    public function sceneWeeklyReportList()
    {
        return $this->only(['user_id', 'month', 'statistical_month']);
    }
}
