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

namespace app\common\enum\activity;

/**
 * 管理后台登录终端
 * Class terminalEnum
 * @package app\common\enum
 */
class ReservationEnum
{

    const CANCEL = 0;//取消预约
    const NO_VERIFICATION = 1;//未审核
    const YES_VERIFICATION = 2;//已审核


    /**
     * @notes 状态描述
     * @param bool $from
     * @return string|string[]
     * @author 段誉
     * @date 2022/9/7 15:05
     */
    public static function getStatusDesc($from = true)
    {
        $desc = [
            self::CANCEL => '取消',
            self::NO_VERIFICATION => '未审核',
            self::YES_VERIFICATION => '已审核',
        ];
        if (true === $from) {
            return $desc;
        }
        return $desc[$from] ?? '';
    }
}
