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

namespace app\adminapi\logic\activity;


use app\common\model\activity\Activity;
use app\common\logic\BaseLogic;
use app\common\model\activity\ActivityTime;
use think\facade\Db;


/**
 * Activity逻辑
 * Class ActivityLogic
 * @package app\adminapi\logic\activity
 */
class ActivityLogic extends BaseLogic
{


    /**
     * @notes 添加
     * @param array $params
     * @return bool
     * @author likeadmin
     * @date 2025/05/10 11:38
     */
    public static function add(array $params): bool
    {
        Db::startTrans();
        try {
            $ret = Activity::create([
                'name' => $params['name'],
                'image' => $params['image'],
                'start_date' => $params['start_date'],
                'end_date' => $params['end_date']
            ]);

            foreach ($params['activityTime'] as $value){
                ActivityTime::create([
                    "activity_id" => $ret->id,
                    "start_time" => $value['start_time'],
                    "end_time" => $value['end_time'],
                ]);
            }

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
     * @date 2025/05/10 11:38
     */
    public static function edit(array $params): bool
    {
        Db::startTrans();
        try {
            Activity::where('id', $params['id'])->update([
                'name' => $params['name'],
                'image' => $params['image'],
                'start_date' => $params['start_date'],
                'end_date' => $params['end_date']
            ]);

            ActivityTime::destroy(['activity_id'=>$params['id']]);

            foreach ($params['activityTime'] as $value){
                ActivityTime::create([
                    "activity_id" => $params['id'],
                    "start_time" => $value['start_time'],
                    "end_time" => $value['end_time'],
                ]);
            }

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
     * @date 2025/05/10 11:38
     */
    public static function delete(array $params): bool
    {
        return Activity::destroy($params['id']);
    }


    /**
     * @notes 获取详情
     * @param $params
     * @return array
     * @author likeadmin
     * @date 2025/05/10 11:38
     */
    public static function detail($params): array
    {
        return Activity::findOrEmpty($params['id'])->toArray();
    }
}
