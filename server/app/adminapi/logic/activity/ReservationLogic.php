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


use app\common\enum\activity\ReservationEnum;
use app\common\model\activity\Reservation;
use app\common\logic\BaseLogic;
use think\facade\Db;


/**
 * Reservation逻辑
 * Class ReservationLogic
 * @package app\adminapi\logic\activity
 */
class ReservationLogic extends BaseLogic
{


    /**
     * @notes 添加
     * @param array $params
     * @return bool
     * @author likeadmin
     * @date 2025/05/12 10:35
     */
    public static function add(array $params): bool
    {
        Db::startTrans();
        try {
            Reservation::create([
                'user_id' => $params['user_id'],
                'date' => $params['date'],
                'time_slot' => $params['time_slot'],
                'name' => $params['name'],
                'mobile' => $params['mobile'],
                'num' => $params['num'],
                'note' => $params['note'],
                'company' => $params['company']
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
     * @date 2025/05/12 10:35
     */
    public static function edit(array $params): bool
    {
        Db::startTrans();
        try {
            Reservation::where('id', $params['id'])->update([
                'user_id' => $params['user_id'],
                'date' => $params['date'],
                'time_slot' => $params['time_slot'],
                'name' => $params['name'],
                'mobile' => $params['mobile'],
                'num' => $params['num'],
                'note' => $params['note'],
                'company' => $params['company']
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
     * @date 2025/05/12 10:35
     */
    public static function delete(array $params): bool
    {
        return Reservation::destroy($params['id']);
    }

    /**
     * @notes 核验
     * @param array $params
     * @return bool
     * @author likeadmin
     * @date 2025/05/12 10:35
     */
    public static function verification(array $params): bool
    {
        return Reservation::where(['id'=>$params['id']])->update([
            'status'=>ReservationEnum::YES_VERIFICATION
        ]);
    }


    /**
     * @notes 获取详情
     * @param $params
     * @return array
     * @author likeadmin
     * @date 2025/05/12 10:35
     */
    public static function detail($params): array
    {
        return Reservation::findOrEmpty($params['id'])->toArray();
    }
}
