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

namespace app\adminapi\logic\log;


use app\common\model\log\Log;
use app\common\logic\BaseLogic;
use think\facade\Db;


/**
 * Log逻辑
 * Class LogLogic
 * @package app\adminapi\logic\log
 */
class LogLogic extends BaseLogic
{


    /**
     * @notes 添加
     * @param array $params
     * @return bool
     * @author likeadmin
     * @date 2025/06/02 18:03
     */
    public static function add(array $params): bool
    {
        Db::startTrans();
        try {
            Log::create([
                'admin_id' => $params['admin_id'],
                'admin_name' => $params['admin_name'],
                'action' => $params['action'],
                'title' => $params['title'],
                'name' => $params['name'],
                'ip' => request()->ip()
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
     * @date 2025/06/02 18:03
     */
    public static function edit(array $params): bool
    {
        Db::startTrans();
        try {
            Log::where('id', $params['id'])->update([

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
     * @date 2025/06/02 18:03
     */
    public static function delete(array $params): bool
    {
        return Log::destroy($params['id']);
    }


    /**
     * @notes 获取详情
     * @param $params
     * @return array
     * @author likeadmin
     * @date 2025/06/02 18:03
     */
    public static function detail($params): array
    {
        return Log::findOrEmpty($params['id'])->toArray();
    }
}
