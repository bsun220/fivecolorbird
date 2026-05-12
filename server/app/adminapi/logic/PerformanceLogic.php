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

namespace app\adminapi\logic;


use app\common\model\Performance;
use app\common\logic\BaseLogic;
use think\facade\Db;


/**
 * Performance逻辑
 * Class PerformanceLogic
 * @package app\adminapi\logic
 */
class PerformanceLogic extends BaseLogic
{


    /**
     * @notes 添加
     * @param array $params
     * @return bool
     * @author likeadmin
     * @date 2025/06/01 23:28
     */
    public static function add(array $params): bool
    {
        Db::startTrans();
        try {
            Performance::create([
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
     * @notes 编辑
     * @param array $params
     * @return bool
     * @author likeadmin
     * @date 2025/06/01 23:28
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
     * @date 2025/06/01 23:28
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
     * @date 2025/06/01 23:28
     */
    public static function detail($params): array
    {
        return Performance::findOrEmpty($params['id'])->toArray();
    }
}
