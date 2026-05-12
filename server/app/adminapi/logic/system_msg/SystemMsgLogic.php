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

namespace app\adminapi\logic\system_msg;


use app\common\model\system_msg\SystemMsg;
use app\common\logic\BaseLogic;
use think\facade\Db;


/**
 * SystemMsg逻辑
 * Class SystemMsgLogic
 * @package app\adminapi\logic\system_msg
 */
class SystemMsgLogic extends BaseLogic
{


    /**
     * @notes 添加
     * @param array $params
     * @return bool
     * @author likeadmin
     * @date 2025/06/15 15:11
     */
    public static function add(array $params): bool
    {
        Db::startTrans();
        try {
            //1、周报，包括普通员工上传，管理员审核
            //2、绩效，管理员录入（只推送到对应的人员）
            //3、员工资料，管理员编辑（只推送到对应的人员）
            SystemMsg::create([
                "content" => $params['content'],
                "user_id" => $params['user_id'],
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
     * @date 2025/06/15 15:11
     */
    public static function edit(array $params): bool
    {
        Db::startTrans();
        try {
            SystemMsg::where('id', $params['id'])->update([

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
     * @date 2025/06/15 15:11
     */
    public static function delete(array $params): bool
    {
        return SystemMsg::destroy($params['id']);
    }


    /**
     * @notes 获取详情
     * @param $params
     * @return array
     * @author likeadmin
     * @date 2025/06/15 15:11
     */
    public static function detail($params): array
    {
        return SystemMsg::findOrEmpty($params['id'])->toArray();
    }
}
