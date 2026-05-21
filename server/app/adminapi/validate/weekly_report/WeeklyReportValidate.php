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

namespace app\adminapi\validate\weekly_report;


use app\common\validate\BaseValidate;


/**
 * WeeklyReport验证器
 * Class WeeklyReportValidate
 * @package app\adminapi\validate\weekly_report
 */
class WeeklyReportValidate extends BaseValidate
{

    /**
     * 设置校验规则
     * @var string[]
     */
    protected $rule = [
        'id' => 'require',
        'start_date' => 'require|date',
        'end_date' => 'require|date',
        'daily_details' => 'require|array',
        'total_hours' => 'require|float|egt:0',
        'overtime_hours' => 'float|egt:0',
        'todo_items' => 'string',
        'status' => 'require|in:0,1,2',
        'reply' => 'string',
    ];


    /**
     * 参数描述
     * @var string[]
     */
    protected $field = [
        'id' => 'id',
        'start_date' => '开始日期',
        'end_date' => '结束日期',
        'daily_details' => '每日详情',
        'total_hours' => '总工时',
        'overtime_hours' => '加班工时',
        'todo_items' => '待办事项',
        'status' => '状态',
        'reply' => '回复',
    ];


    /**
     * @notes 添加场景
     * @return WeeklyReportValidate
     * @author likeadmin
     * @date 2025/05/28 09:52
     */
    public function sceneAdd()
    {
        return $this->remove('id', true)
            ->remove('status', true);
    }


    /**
     * @notes 编辑场景
     * @return WeeklyReportValidate
     * @author likeadmin
     * @date 2025/05/28 09:52
     */
    public function sceneEdit()
    {
        return $this->only(['id', 'start_date', 'end_date', 'daily_details', 'total_hours', 'overtime_hours', 'todo_items']);
    }


    /**
     * @notes 提交场景
     * @return WeeklyReportValidate
     * @author likeadmin
     * @date 2025/05/28 09:52
     */
    public function sceneSubmit()
    {
        return $this->only(['id']);
    }


    /**
     * @notes 删除场景
     * @return WeeklyReportValidate
     * @author likeadmin
     * @date 2025/05/28 09:52
     */
    public function sceneDelete()
    {
        return $this->only(['id']);
    }


    /**
     * @notes 详情场景
     * @return WeeklyReportValidate
     * @author likeadmin
     * @date 2025/05/28 09:52
     */
    public function sceneDetail()
    {
        return $this->only(['id']);
    }


    /**
     * @notes 审核场景
     * @return WeeklyReportValidate
     * @author likeadmin
     * @date 2025/05/28 09:52
     */
    public function sceneExamine()
    {
        return $this->only(['id', 'status', 'reply']);
    }


}
